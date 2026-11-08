<?php
/**
 * Formulaire « pays » (création et modification). Généré par gene.
 *
 * @var App\Framework\View $this
 * @var array<string, string> $values saisie à afficher
 * @var array<string, string> $errors erreurs par champ (« _form » : erreur générale)
 * @var array<string, array<string, string>> $options choix des clés étrangères
 * @var string|null $id null en création
 * @var string $path
 */
$isNew = $id === null;
$this->title(($isNew ? 'Nouveau' : 'Modifier') . ' · ' . 'Pays');
$action = $isNew ? $this->url($path) : $this->url($path . '/' . rawurlencode($id));
$cancel = $isNew ? $this->url($path) : $this->url($path . '/' . rawurlencode($id));
?>
<nav aria-label="Fil d'Ariane">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $this->e($this->url($path)) ?>">Pays</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $isNew ? 'Nouveau' : 'Modifier' ?></li>
    </ol>
</nav>

<?php if (isset($errors['_form'])): ?>
    <div class="alert alert-danger" role="alert"><?= $this->e($errors['_form']) ?></div>
<?php elseif ($errors !== []): ?>
    <div class="alert alert-danger" role="alert">Le formulaire contient des erreurs : corrigez les champs signalés.</div>
<?php endif ?>

<form method="post" action="<?= $this->e($action) ?>" class="card">
    <div class="card-body">
        <?= $this->csrfField() ?>

        <div class="mb-3">
            <label class="form-label" for="f-code">Code <span class="text-danger" aria-hidden="true">*</span></label>
            <input type="text" class="form-control<?= isset($errors['code']) ? ' is-invalid' : '' ?>" id="f-code" name="code" value="<?= $this->e($values['code'] ?? '') ?>" maxlength="2" required>
            <?php if (isset($errors['code'])): ?><div class="invalid-feedback"><?= $this->e($errors['code']) ?></div><?php endif ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="f-libelle">Libelle <span class="text-danger" aria-hidden="true">*</span></label>
            <input type="text" class="form-control<?= isset($errors['libelle']) ? ' is-invalid' : '' ?>" id="f-libelle" name="libelle" value="<?= $this->e($values['libelle'] ?? '') ?>" maxlength="80" required>
            <?php if (isset($errors['libelle'])): ?><div class="invalid-feedback"><?= $this->e($errors['libelle']) ?></div><?php endif ?>
        </div>
    </div>
    <div class="card-footer d-flex gap-2">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a class="btn btn-outline-secondary" href="<?= $this->e($cancel) ?>">Annuler</a>
    </div>
</form>
