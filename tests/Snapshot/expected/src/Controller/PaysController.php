<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity;
use App\Framework;
use App\Repository;

/**
 * CRUD de la table « pays ».
 *
 * Généré par gene à partir de la table « pays ».
 * Vous pouvez modifier ce fichier : une nouvelle génération ne l'écrasera pas sans --force.
 */
final class PaysController extends Framework\Controller
{
    private const PATH = '/pays';

    private readonly Repository\PaysRepository $repository;

    public function __construct(Framework\App $app)
    {
        parent::__construct($app);
        $this->repository = new Repository\PaysRepository($app->pdo());
    }

    public function index(): Framework\Response
    {
        $request = $this->request();
        $page = $this->repository->paginate(
            $request->queryInt('page', 1),
            (int) $this->app->config->get('app.per_page', 20),
            $request->query('sort'),
            $request->query('dir') ?? 'asc',
            $request->query('q') ?? '',
        );

        return $this->render('pays/index', ['page' => $page, 'path' => self::PATH]);
    }

    public function show(string $id): Framework\Response
    {
        $item = $this->repository->find($id) ?? $this->notFound();

        return $this->render('pays/show', [
            'item' => $item,
            'path' => self::PATH,
            'labels' => [
            ],
        ]);
    }

    public function create(): Framework\Response
    {
        return $this->form(Framework\Form::defaults(Entity\Pays::FIELDS), [], null);
    }

    public function store(): Framework\Response
    {
        $result = Framework\Form::validate($this->request()->postAll(), Entity\Pays::FIELDS);
        if (!$result->isValid()) {
            return $this->form($result->raw, $result->errors, null, 422);
        }

        try {
            $id = $this->repository->insert(Entity\Pays::fromRow($result->values));
        } catch (\PDOException $e) {
            // Erreur due aux données (doublon, valeur hors limites…) : message dans le formulaire.
            return $this->form($result->raw, ['_form' => Framework\Database::userMessage($e) ?? throw $e], null, 422);
        }

        $this->flash('success', 'Enregistrement créé.');

        return $this->redirect($id === null ? self::PATH : self::PATH . '/' . rawurlencode($id));
    }

    public function edit(string $id): Framework\Response
    {
        $item = $this->repository->find($id) ?? $this->notFound();

        return $this->form(Framework\Form::fromRow($item->toRow(), Entity\Pays::FIELDS), [], $id);
    }

    public function update(string $id): Framework\Response
    {
        $item = $this->repository->find($id) ?? $this->notFound();
        $result = Framework\Form::validate($this->request()->postAll(), Entity\Pays::FIELDS);
        if (!$result->isValid()) {
            return $this->form($result->raw, $result->errors, $id, 422);
        }

        $updated = Entity\Pays::fromRow([...$item->toRow(), ...$result->values]);
        try {
            $this->repository->update($id, $updated);
        } catch (\PDOException $e) {
            return $this->form($result->raw, ['_form' => Framework\Database::userMessage($e) ?? throw $e], $id, 422);
        }

        $this->flash('success', 'Modifications enregistrées.');

        return $this->redirect(self::PATH . '/' . rawurlencode((string) ($updated->code ?? $id)));
    }

    public function destroy(string $id): Framework\Response
    {
        $this->repository->find($id) ?? $this->notFound();

        try {
            $this->repository->delete($id);
        } catch (\PDOException $e) {
            if (!Framework\Database::isConstraintViolation($e)) {
                throw $e;
            }
            $this->flash('danger', 'Suppression impossible : cet enregistrement est utilisé par d\'autres données.');

            return $this->redirect(self::PATH . '/' . rawurlencode($id));
        }

        $this->flash('success', 'Enregistrement supprimé.');

        return $this->redirect(self::PATH);
    }

    /**
     * @param array<string, string> $values
     * @param array<string, string> $errors
     */
    private function form(array $values, array $errors, ?string $id, int $status = 200): Framework\Response
    {
        return $this->render('pays/form', [
            'values' => $values,
            'errors' => $errors,
            'id' => $id,
            'path' => self::PATH,
            'options' => [
            ],
        ], $status);
    }
}
