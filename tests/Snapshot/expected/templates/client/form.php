<?php
/**
 * Formulaire « client » (création et modification). Généré par gene.
 *
 * @var App\Framework\View $this
 * @var array<string, string> $values saisie à afficher
 * @var array<string, string> $errors erreurs par champ (« _form » : erreur générale)
 * @var array<string, array<string, string>> $options choix des clés étrangères
 * @var string|null $id null en création
 * @var string $path
 */
$isNew = $id === null;
$this->title(($isNew ? 'Nouveau' : 'Modifier') . ' · ' . 'Client');
$action = $isNew ? $this->url($path) : $this->url($path . '/' . rawurlencode($id));
$cancel = $isNew ? $this->url($path) : $this->url($path . '/' . rawurlencode($id));
?>
<nav aria-label="Fil d'Ariane">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $this->e($this->url($path)) ?>">Client</a></li>
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
            <label class="form-label" for="f-nom">Nom <span class="text-danger" aria-hidden="true">*</span></label>
            <input type="text" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>" id="f-nom" name="nom" value="<?= $this->e($values['nom'] ?? '') ?>" maxlength="100" required>
            <?php if (isset($errors['nom'])): ?><div class="invalid-feedback"><?= $this->e($errors['nom']) ?></div><?php endif ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="f-email">Email</label>
            <input type="text" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" id="f-email" name="email" value="<?= $this->e($values['email'] ?? '') ?>" maxlength="190">
            <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= $this->e($errors['email']) ?></div><?php endif ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="f-date-naissance">Date naissance</label>
            <input type="date" class="form-control<?= isset($errors['date_naissance']) ? ' is-invalid' : '' ?>" id="f-date-naissance" name="date_naissance" value="<?= $this->e($values['date_naissance'] ?? '') ?>">
            <?php if (isset($errors['date_naissance'])): ?><div class="invalid-feedback"><?= $this->e($errors['date_naissance']) ?></div><?php endif ?>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input<?= isset($errors['actif']) ? ' is-invalid' : '' ?>" id="f-actif" name="actif" value="1"<?= ($values['actif'] ?? '') === '1' ? ' checked' : '' ?>>
            <label class="form-check-label" for="f-actif">Actif</label>
            <?php if (isset($errors['actif'])): ?><div class="invalid-feedback"><?= $this->e($errors['actif']) ?></div><?php endif ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="f-solde">Solde</label>
            <input type="text" inputmode="decimal" class="form-control<?= isset($errors['solde']) ? ' is-invalid' : '' ?>" id="f-solde" name="solde" value="<?= $this->e($values['solde'] ?? '') ?>">
            <?php if (isset($errors['solde'])): ?><div class="invalid-feedback"><?= $this->e($errors['solde']) ?></div><?php endif ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="f-statut">Statut</label>
            <select class="form-select<?= isset($errors['statut']) ? ' is-invalid' : '' ?>" id="f-statut" name="statut">
                <option value="">— Choisir —</option>
                <?php foreach (['prospect', 'client', 'ancien'] as $choice): ?>
                    <option value="<?= $this->e($choice) ?>"<?= $choice === ($values['statut'] ?? '') ? ' selected' : '' ?>><?= $this->e($choice) ?></option>
                <?php endforeach ?>
            </select>
            <?php if (isset($errors['statut'])): ?><div class="invalid-feedback"><?= $this->e($errors['statut']) ?></div><?php endif ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="f-pays-code">Pays</label>
            <select class="form-select<?= isset($errors['pays_code']) ? ' is-invalid' : '' ?>" id="f-pays-code" name="pays_code">
                <option value="">— Choisir —</option>
                <?php foreach ($options['pays_code'] ?? [] as $optionValue => $optionLabel): ?>
                    <option value="<?= $this->e($optionValue) ?>"<?= (string) $optionValue === ($values['pays_code'] ?? '') ? ' selected' : '' ?>><?= $this->e($optionLabel) ?></option>
                <?php endforeach ?>
            </select>
            <?php if (isset($errors['pays_code'])): ?><div class="invalid-feedback"><?= $this->e($errors['pays_code']) ?></div><?php endif ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="f-parrain-id">Parrain</label>
            <select class="form-select<?= isset($errors['parrain_id']) ? ' is-invalid' : '' ?>" id="f-parrain-id" name="parrain_id">
                <option value="">— Choisir —</option>
                <?php foreach ($options['parrain_id'] ?? [] as $optionValue => $optionLabel): ?>
                    <option value="<?= $this->e($optionValue) ?>"<?= (string) $optionValue === ($values['parrain_id'] ?? '') ? ' selected' : '' ?>><?= $this->e($optionLabel) ?></option>
                <?php endforeach ?>
            </select>
            <?php if (isset($errors['parrain_id'])): ?><div class="invalid-feedback"><?= $this->e($errors['parrain_id']) ?></div><?php endif ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="f-notes">Notes</label>
            <textarea class="form-control<?= isset($errors['notes']) ? ' is-invalid' : '' ?>" id="f-notes" name="notes" rows="4"><?= $this->e($values['notes'] ?? '') ?></textarea>
            <?php if (isset($errors['notes'])): ?><div class="invalid-feedback"><?= $this->e($errors['notes']) ?></div><?php endif ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="f-cree-le">Cree le</label>
            <input type="datetime-local" step="1" class="form-control<?= isset($errors['cree_le']) ? ' is-invalid' : '' ?>" id="f-cree-le" name="cree_le" value="<?= $this->e($values['cree_le'] ?? '') ?>">
            <?php if (isset($errors['cree_le'])): ?><div class="invalid-feedback"><?= $this->e($errors['cree_le']) ?></div><?php endif ?>
        </div>
    </div>
    <div class="card-footer d-flex gap-2">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a class="btn btn-outline-secondary" href="<?= $this->e($cancel) ?>">Annuler</a>
    </div>
</form>
