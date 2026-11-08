<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity;
use App\Framework;
use App\Repository;

/**
 * CRUD de la table « commande ».
 *
 * Généré par gene à partir de la table « commande ».
 * Vous pouvez modifier ce fichier : une nouvelle génération ne l'écrasera pas sans --force.
 */
final class CommandeController extends Framework\Controller
{
    private const PATH = '/commande';

    private readonly Repository\CommandeRepository $repository;
    private readonly Repository\ClientRepository $clientRepository;

    public function __construct(Framework\App $app)
    {
        parent::__construct($app);
        $this->repository = new Repository\CommandeRepository($app->pdo());
        $this->clientRepository = new Repository\ClientRepository($app->pdo());
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

        return $this->render('commande/index', ['page' => $page, 'path' => self::PATH]);
    }

    public function show(string $id): Framework\Response
    {
        $item = $this->repository->find($id) ?? $this->notFound();

        return $this->render('commande/show', [
            'item' => $item,
            'path' => self::PATH,
            'labels' => [
                'client_id' => $item->clientId === null ? null : $this->clientRepository->labelFor($item->clientId),
            ],
        ]);
    }

    public function create(): Framework\Response
    {
        return $this->form(Framework\Form::defaults(Entity\Commande::FIELDS), [], null);
    }

    public function store(): Framework\Response
    {
        $result = Framework\Form::validate($this->request()->postAll(), Entity\Commande::FIELDS);
        if (!$result->isValid()) {
            return $this->form($result->raw, $result->errors, null, 422);
        }

        try {
            $id = $this->repository->insert(Entity\Commande::fromRow($result->values));
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

        return $this->form(Framework\Form::fromRow($item->toRow(), Entity\Commande::FIELDS), [], $id);
    }

    public function update(string $id): Framework\Response
    {
        $item = $this->repository->find($id) ?? $this->notFound();
        $result = Framework\Form::validate($this->request()->postAll(), Entity\Commande::FIELDS);
        if (!$result->isValid()) {
            return $this->form($result->raw, $result->errors, $id, 422);
        }

        $updated = Entity\Commande::fromRow([...$item->toRow(), ...$result->values]);
        try {
            $this->repository->update($id, $updated);
        } catch (\PDOException $e) {
            return $this->form($result->raw, ['_form' => Framework\Database::userMessage($e) ?? throw $e], $id, 422);
        }

        $this->flash('success', 'Modifications enregistrées.');

        return $this->redirect(self::PATH . '/' . rawurlencode((string) ($updated->id ?? $id)));
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
        return $this->render('commande/form', [
            'values' => $values,
            'errors' => $errors,
            'id' => $id,
            'path' => self::PATH,
            'options' => [
                'client_id' => $this->clientRepository->options($values['client_id'] ?? null),
            ],
        ], $status);
    }
}
