<?php

declare(strict_types=1);

namespace App\Framework;

abstract class Controller
{
    public function __construct(protected readonly App $app) {}

    /** @param array<string, mixed> $data */
    protected function render(string $template, array $data = [], int $status = 200): Response
    {
        return new Response($this->app->view()->render($template, $data), $status);
    }

    /** @param array<string, scalar> $query */
    protected function redirect(string $path, array $query = []): Response
    {
        return Response::redirect($this->app->view()->url($path, $query));
    }

    protected function flash(string $type, string $message): void
    {
        $this->app->session()->flash($type, $message);
    }

    protected function request(): Request
    {
        return $this->app->request;
    }

    protected function notFound(string $message = 'Enregistrement introuvable.'): never
    {
        throw new HttpException(404, $message);
    }
}
