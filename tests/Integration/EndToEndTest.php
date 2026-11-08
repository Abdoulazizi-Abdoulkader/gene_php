<?php

declare(strict_types=1);

namespace Gene\Tests\Integration;

use Gene\Generator\FileWriter;
use Gene\Generator\GenerationOptions;
use Gene\Generator\Generator;
use Gene\Schema\MySqlSchemaReader;
use Gene\Tests\Support\Filesystem;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Bout en bout : schéma réel → génération → serveur PHP intégré → scénario HTTP.
 *
 * Nécessite une base jetable (elle est vidée) :
 *     GENE_TEST_DSN="mysql:host=127.0.0.1;port=3306;dbname=gene_test" GENE_TEST_USER=root GENE_TEST_PASSWORD=… \
 *     composer test -- --group integration
 */
#[Group('integration')]
final class EndToEndTest extends TestCase
{
    private const NULL_DEVICE = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';

    private static string $dir = '';
    /** @var resource|null */
    private static $server = null;
    private static string $baseUrl = '';
    private static \PDO $pdo;

    private \CurlHandle $http;

    public static function setUpBeforeClass(): void
    {
        $dsn = getenv('GENE_TEST_DSN');
        if (!is_string($dsn) || $dsn === '') {
            return;
        }
        $user = (string) getenv('GENE_TEST_USER');
        $password = (string) getenv('GENE_TEST_PASSWORD');

        self::$pdo = MySqlSchemaReader::connect($dsn, $user, $password);
        $sql = (string) file_get_contents(__DIR__ . '/../Fixtures/schema.sql');
        foreach (preg_split('/;\s*\n/', $sql) ?: [] as $statement) {
            $statement = trim((string) preg_replace('/^--.*$/m', '', $statement));
            if ($statement !== '') {
                self::$pdo->exec($statement);
            }
        }

        $schema = (new MySqlSchemaReader(self::$pdo))->read();
        $result = (new Generator())->generate($schema, new GenerationOptions('Integration', $dsn, $user));
        self::$dir = Filesystem::tempDir('gene-e2e');
        (new FileWriter(self::$dir))->write($result->files);
        $env = (string) file_get_contents(self::$dir . '/.env');
        file_put_contents(self::$dir . '/.env', str_replace('DB_PASSWORD=""', 'DB_PASSWORD="' . addcslashes($password, '"\\') . '"', $env));

        $socket = stream_socket_server('tcp://127.0.0.1:0');
        if ($socket === false) {
            throw new \RuntimeException('Aucun port libre.');
        }
        $port = (int) substr((string) stream_socket_get_name($socket, false), strrpos((string) stream_socket_get_name($socket, false), ':') + 1);
        fclose($socket);

        $process = proc_open(
            [PHP_BINARY, '-S', '127.0.0.1:' . $port, '-t', self::$dir . '/public'],
            [0 => ['pipe', 'r'], 1 => ['file', self::NULL_DEVICE, 'w'], 2 => ['file', self::NULL_DEVICE, 'w']],
            $pipes,
        );
        if (!is_resource($process)) {
            throw new \RuntimeException('Impossible de lancer php -S.');
        }
        self::$server = $process;
        self::$baseUrl = 'http://127.0.0.1:' . $port;
        for ($i = 0; $i < 50; ++$i) {
            $probe = @fsockopen('127.0.0.1', $port);
            if ($probe !== false) {
                fclose($probe);
                break;
            }
            usleep(100_000);
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$server !== null) {
            proc_terminate(self::$server);
            proc_close(self::$server);
            self::$server = null;
        }
        Filesystem::remove(self::$dir);
    }

    protected function setUp(): void
    {
        if (self::$baseUrl === '') {
            self::markTestSkipped('GENE_TEST_DSN non défini : test d\'intégration ignoré.');
        }
        $this->http = curl_init();
        curl_setopt_array($this->http, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_COOKIEFILE => '', // cookies en mémoire, propres à ce test
            CURLOPT_TIMEOUT => 10,
        ]);
    }

    public function testCrudScenario(): void
    {
        [$status, , $home] = $this->request('GET', '/');
        self::assertSame(200, $status);
        self::assertStringContainsString('href="/client"', $home);

        // Sans jeton CSRF : refusé avant tout traitement.
        self::assertSame(419, $this->request('POST', '/client', ['nom' => 'x'])[0]);

        $token = $this->token('/client/new');

        // Validation : la saisie est réaffichée avec les erreurs.
        [$status, , $body] = $this->request('POST', '/client', ['_token' => $token, 'nom' => '', 'date_naissance' => '2023-02-30', 'statut' => 'client']);
        self::assertSame(422, $status);
        self::assertStringContainsString('Ce champ est obligatoire.', $body);
        self::assertStringContainsString('Date attendue', $body);

        // Création avec une charge XSS : stockée telle quelle, affichée échappée.
        [$status, $headers] = $this->request('POST', '/client', [
            '_token' => $token, 'nom' => '<script>alert(1)</script>Éloïse', 'email' => 'e@example.org',
            'actif' => '1', 'solde' => '1234,5', 'statut' => 'client', 'pays_code' => 'NE', 'date_naissance' => '1990-05-17',
        ]);
        self::assertSame(303, $status);
        self::assertMatchesRegularExpression('#^/client/(\d+)$#', $headers['location'] ?? '');
        $clientPath = $headers['location'];

        [$status, , $show] = $this->request('GET', $clientPath);
        self::assertSame(200, $status);
        self::assertStringNotContainsString('<script>alert(1)</script>', $show);
        self::assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;Éloïse', $show);
        self::assertStringContainsString('Niger', $show, 'libellé de la clé étrangère');
        self::assertStringContainsString('17/05/1990', $show);
        self::assertSame('1234.50', self::row('SELECT solde FROM client WHERE email = ?', ['e@example.org'])['solde']);

        // Doublon sur une colonne UNIQUE : message clair, pas d'erreur 500.
        [$status, , $body] = $this->request('POST', '/client', ['_token' => $token, 'nom' => 'Bis', 'email' => 'e@example.org', 'statut' => 'client']);
        self::assertSame(422, $status);
        self::assertStringContainsString('doublon', $body);

        // Modification : case décochée = false, champ vidé = NULL.
        [$status] = $this->request('POST', $clientPath, ['_token' => $token, 'nom' => 'Éloïse', 'email' => 'e@example.org', 'statut' => 'ancien', 'solde' => '10']);
        self::assertSame(303, $status);
        $row = self::row('SELECT actif, statut, date_naissance, pays_code FROM client WHERE email = ?', ['e@example.org']);
        self::assertSame(['actif' => 0, 'statut' => 'ancien', 'date_naissance' => null, 'pays_code' => null], $row);

        // Commande liée : la liste affiche le libellé du client.
        $clientId = (int) substr($clientPath, strlen('/client/'));
        [$status, $headers] = $this->request('POST', '/commande', ['_token' => $token, 'client_id' => (string) $clientId, 'passee_le' => '2026-11-08T10:30', 'montant_ttc' => '99,9']);
        self::assertSame(303, $status);
        $commandePath = $headers['location'] ?? '';
        [, , $list] = $this->request('GET', '/commande');
        self::assertStringContainsString('Éloïse', $list);
        self::assertStringContainsString('08/11/2026 10:30', $list);

        // Recherche dans la liste (et conservée par les liens de tri).
        [, , $found] = $this->request('GET', '/client?q=' . rawurlencode('éloï'));
        self::assertStringContainsString('Éloïse', $found);
        self::assertStringContainsString('q=%C3%A9lo%C3%AF', $found);
        self::assertStringContainsString('Aucun résultat', $this->request('GET', '/client?q=zzz')[2]);

        // Valeur hors des bornes de la colonne : erreur sur le champ, pas d'erreur 500.
        [$status, , $body] = $this->request('POST', '/commande', ['_token' => $token, 'client_id' => '99999999999', 'passee_le' => '2026-11-08T10:30']);
        self::assertSame(422, $status);
        self::assertStringContainsString('Nombre entre 0 et 4294967295 attendu.', $body);

        // Les secondes d'un DATETIME survivent à une modification sans changement.
        self::$pdo->exec("UPDATE commande SET passee_le = '2026-11-08 10:30:45' WHERE id = " . (int) substr($commandePath, strlen('/commande/')));
        preg_match('/name="passee_le" value="([^"]+)"/', $this->request('GET', $commandePath . '/edit')[2], $m);
        self::assertSame('2026-11-08T10:30:45', $m[1] ?? null);
        $this->request('POST', $commandePath, ['_token' => $token, 'client_id' => (string) $clientId, 'passee_le' => $m[1], 'montant_ttc' => '99,9']);
        self::assertSame('2026-11-08 10:30:45', self::row('SELECT passee_le FROM commande WHERE id = ?', [substr($commandePath, strlen('/commande/'))])['passee_le']);

        // Suppression bloquée par la clé étrangère, puis suppression effective.
        [$status, $headers] = $this->request('POST', $clientPath . '/delete', ['_token' => $token]);
        self::assertSame([303, $clientPath], [$status, $headers['location'] ?? '']);
        self::assertStringContainsString('Suppression impossible', $this->request('GET', $clientPath)[2]);

        self::assertSame(303, $this->request('POST', $commandePath . '/delete', ['_token' => $token])[0]);
        self::assertSame(404, $this->request('GET', $commandePath)[0]);
        self::assertSame(303, $this->request('POST', $clientPath . '/delete', ['_token' => $token])[0]);
        self::assertSame(404, $this->request('GET', $clientPath)[0]);
    }

    public function testRoutingAndHardening(): void
    {
        self::assertSame(200, $this->request('GET', '/ligne-commande')[0], 'clé composite : liste seule');
        self::assertSame(404, $this->request('GET', '/ligne-commande/new')[0]);
        self::assertSame(404, $this->request('GET', '/journal')[0], 'table sans clé primaire ignorée');
        self::assertSame(405, $this->request('GET', '/client/1/delete')[0]);
        self::assertSame(200, $this->request('GET', '/client?sort=nom;DROP%20TABLE%20client&dir=desc&page=999')[0], 'tri hors liste blanche ignoré');

        [$status, $headers] = $this->request('GET', '/pays');
        self::assertSame(200, $status);
        self::assertStringContainsString("script-src 'self'", $headers['content-security-policy'] ?? '');
        self::assertSame('nosniff', $headers['x-content-type-options'] ?? '');
        self::assertSame(200, $this->request('GET', '/assets/css/bootstrap.min.css')[0]);
    }

    /**
     * @param list<string> $params
     *
     * @return array<string, mixed>
     */
    private static function row(string $sql, array $params): array
    {
        $statement = self::$pdo->prepare($sql);
        $statement->execute($params);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        return is_array($row) ? $row : [];
    }

    private function token(string $path): string
    {
        $body = $this->request('GET', $path)[2];
        self::assertSame(1, preg_match('/name="_token" value="([a-f0-9]+)"/', $body, $m), 'jeton CSRF absent');

        return $m[1] ?? '';
    }

    /**
     * @param array<string, string>|null $form
     *
     * @return array{0: int, 1: array<string, string>, 2: string}
     */
    private function request(string $method, string $path, ?array $form = null): array
    {
        curl_setopt($this->http, CURLOPT_URL, self::$baseUrl . '/' . ltrim($path, '/'));
        if ($form === null) {
            curl_setopt($this->http, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($this->http, CURLOPT_POST, true);
            curl_setopt($this->http, CURLOPT_POSTFIELDS, http_build_query($form));
        }
        curl_setopt($this->http, CURLOPT_CUSTOMREQUEST, $method === 'POST' ? 'POST' : 'GET');
        $raw = (string) curl_exec($this->http);
        $status = (int) curl_getinfo($this->http, CURLINFO_RESPONSE_CODE);
        $headerSize = (int) curl_getinfo($this->http, CURLINFO_HEADER_SIZE);

        $headers = [];
        foreach (explode("\r\n", substr($raw, 0, $headerSize)) as $line) {
            if (str_contains($line, ':')) {
                [$name, $value] = explode(':', $line, 2);
                $headers[strtolower(trim($name))] = trim($value);
            }
        }

        return [$status, $headers, substr($raw, $headerSize)];
    }
}
