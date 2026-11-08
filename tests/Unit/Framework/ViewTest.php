<?php

declare(strict_types=1);

namespace Gene\Tests\Unit\Framework;

use App\Framework\Csrf;
use App\Framework\Page;
use App\Framework\Request;
use App\Framework\Session;
use App\Framework\View;
use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    private View $view;

    protected function setUp(): void
    {
        $_SESSION = [];
        $session = new Session();
        $this->view = new View(__DIR__ . '/../../../resources/skeleton/templates', new Request('GET', '/client', '/demo'), new Csrf($session), $session);
    }

    public function testEscaping(): void
    {
        self::assertSame('&lt;script&gt;&quot;&#039;', $this->view->e('<script>"\''));
        self::assertSame('', $this->view->e(null));
        self::assertSame('&lt;b&gt;', $this->view->display('<b>', 'string'));
    }

    public function testUrls(): void
    {
        self::assertSame('/demo/client/new', $this->view->url('/client/new'));
        self::assertSame('/demo/client?page=2', $this->view->url('client', ['page' => 2, 'sort' => null]));
        self::assertTrue($this->view->isActive('/client'));
        self::assertFalse($this->view->isActive('/'));
    }

    public function testDisplayFormats(): void
    {
        self::assertSame('Oui', $this->view->display(true, 'bool'));
        self::assertSame('31/12/2024', $this->view->display('2024-12-31', 'date'));
        self::assertSame('31/12/2024 23:59', $this->view->display('2024-12-31 23:59:10', 'datetime'));
        self::assertStringContainsString('—', $this->view->display(null, 'string'));
        self::assertSame('abcd…', $this->view->display('abcdefgh', 'string', 5));
    }

    public function testCsrfFieldMatchesToken(): void
    {
        $csrf = new Csrf(new Session());
        if (preg_match('/value="([a-f0-9]{64})"/', $this->view->csrfField(), $m) !== 1) {
            self::fail('jeton absent du champ caché');
        }
        self::assertTrue($csrf->isValid($m[1]));
        self::assertFalse($csrf->isValid('autre'));
        self::assertFalse($csrf->isValid(null));
    }

    public function testRejectsTemplateTraversal(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->view->partial('../../etc/passwd');
    }

    public function testPaginationWindow(): void
    {
        $page = new Page([], 200, 10, 10, 'id', 'asc');
        self::assertSame(20, $page->lastPage());
        self::assertSame([1, 0, 8, 9, 10, 11, 12, 0, 20], $page->window());
        self::assertStringContainsString('aria-current="page">10<', $this->view->partial('_pagination', ['page' => $page, 'path' => '/client']));
    }
}
