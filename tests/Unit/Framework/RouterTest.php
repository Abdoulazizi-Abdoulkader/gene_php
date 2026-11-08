<?php

declare(strict_types=1);

namespace Gene\Tests\Unit\Framework;

use App\Controller\HomeController;
use App\Framework\HttpException;
use App\Framework\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = (new Router())->get('/', [HomeController::class, 'index'])->crud('/bon-commande', HomeController::class);
    }

    public function testCrudRoutes(): void
    {
        self::assertSame([[HomeController::class, 'index'], []], $this->router->match('GET', '/bon-commande'));
        self::assertSame('create', $this->router->match('GET', '/bon-commande/new')[0][1], '« new » passe avant {id}');
        self::assertSame([[HomeController::class, 'show'], ['id' => 'A B']], $this->router->match('GET', '/bon-commande/A B'));
        self::assertSame('edit', $this->router->match('GET', '/bon-commande/5/edit')[0][1]);
        self::assertSame([[HomeController::class, 'edit'], ['id' => 'A/B']], $this->router->match('GET', '/bon-commande/A%2FB/edit'));
        self::assertSame('update', $this->router->match('POST', '/bon-commande/5')[0][1]);
        self::assertSame('destroy', $this->router->match('POST', '/bon-commande/5/delete')[0][1]);
        self::assertSame('index', $this->router->match('HEAD', '/')[0][1]);
    }

    public function testNotFound(): void
    {
        try {
            $this->router->match('GET', '/inconnu');
            self::fail('404 attendu');
        } catch (HttpException $e) {
            self::assertSame(404, $e->status);
        }
    }

    public function testMethodNotAllowed(): void
    {
        try {
            $this->router->match('GET', '/bon-commande/5/delete');
            self::fail('405 attendu');
        } catch (HttpException $e) {
            self::assertSame(405, $e->status);
        }
    }

    public function testReadOnly(): void
    {
        $router = (new Router())->crud('/ligne', HomeController::class, readOnly: true);
        self::assertSame('index', $router->match('GET', '/ligne')[0][1]);
        $this->expectException(HttpException::class);
        $router->match('GET', '/ligne/new');
    }
}
