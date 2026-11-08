<?php

declare(strict_types=1);

namespace Gene\Config;

/** Réglages réutilisables d'une génération. Par conception, aucun champ ne peut contenir de mot de passe. */
final readonly class Profile
{
    /** @param list<string> $tables tables choisies (vide = toutes) */
    public function __construct(
        public string $host = '127.0.0.1',
        public string $port = '3306',
        public string $user = 'root',
        public string $database = '',
        public string $app = '',
        public string $out = '',
        public array $tables = [],
    ) {}

    /** @param array<mixed> $data */
    public static function fromArray(array $data): self
    {
        $string = static fn(string $key, string $default = ''): string => is_scalar($data[$key] ?? null) ? (string) $data[$key] : $default;
        $tables = is_array($data['tables'] ?? null) ? array_values(array_filter($data['tables'], is_string(...))) : [];

        return new self($string('host', '127.0.0.1'), $string('port', '3306'), $string('user', 'root'), $string('database'), $string('app'), $string('out'), $tables);
    }

    /** @return array{host: string, port: string, user: string, database: string, app: string, out: string, tables: list<string>} */
    public function toArray(): array
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'user' => $this->user,
            'database' => $this->database,
            'app' => $this->app,
            'out' => $this->out,
            'tables' => $this->tables,
        ];
    }
}
