<?php

declare(strict_types=1);

namespace Gene\Tests\Unit\Framework;

use App\Framework\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    /** @return iterable<string, array{string, string, string|null, string, string}> */
    public static function deployments(): iterable
    {
        yield 'php -S, racine public/' => ['/client/3', '/index.php', null, '', '/client/3'];
        yield 'accueil' => ['/', '/index.php', null, '', '/'];
        yield 'alias Apache sur public/' => ['/demo/public/client', '/demo/public/index.php', null, '/demo/public', '/client'];
        yield 'Apache sur la racine du projet' => ['/demo/client/', '/demo/public/index.php', null, '/demo', '/client'];
        yield 'accueil du sous-dossier' => ['/demo', '/demo/public/index.php', null, '/demo', '/'];
        yield 'index.php explicite' => ['/demo/public/index.php/client', '/demo/public/index.php', null, '/demo/public', '/client'];
        yield 'préfixe imposé' => ['/app/client', '/index.php', '/app/', '/app', '/client'];
        yield 'identifiant contenant « / » encodé' => ['/client/A%2FB', '/index.php', null, '', '/client/A%2FB'];
        yield 'dossier accentué' => ['/mes%20apps/client', '/mes apps/public/index.php', null, '/mes apps', '/client'];
    }

    #[DataProvider('deployments')]
    public function testSplitPath(string $uri, string $script, ?string $forced, string $base, string $path): void
    {
        self::assertSame([$base, $path], Request::splitPath($uri, $script, $forced));
    }

    public function testInputsIgnoreArrays(): void
    {
        $request = new Request('POST', '/', '', ['page' => '3', 'sort' => ['x']], ['nom' => 'a', 'evil' => ['b']]);

        self::assertSame(3, $request->queryInt('page', 1));
        self::assertSame(1, (new Request('GET', '/', '', ['page' => '-2']))->queryInt('page', 1));
        self::assertNull($request->query('sort'));
        self::assertSame(['nom' => 'a'], $request->postAll());
    }
}
