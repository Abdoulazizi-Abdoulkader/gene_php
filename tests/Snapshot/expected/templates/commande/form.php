<?php
/**
 * Formulaire « commande » (création et modification). Généré par gene.
 *
 * @var App\Framework\View $this
 * @var array<string, string> $values saisie à afficher
 * @var array<string, string> $errors erreurs par champ (« _form » : erreur générale)
 * @var array<string, array<string, string>> $options choix des clés étrangères
 * @var string|null $id null en création
 * @var string $path
 */
$isNew = $id === null;
$this->title(($isNew ? 'Nouveau' : 'Modifier') . ' · ' . 'Commande');
$action = $isNew ? $this->url($path) : $this->url($path . '/' . rawurlencode($id));
$cancel = $isNew ? $this->url($path) : $this->url($path . '/' . rawurlencode($id));
?>
<nav aria-label="Fil d'Ariane">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $this->e($this->url($path)) ?>">Commande</a></li>
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
            <label class="form-label" for="f-client-id">Client <span class="text-danger" aria-hidden="true">*</span></label>
            <select class="form-select<?= isset($errors['client_id']) ? ' is-invalid' : '' ?>" id="f-client-id" name="client_id" required>
                <option value="">— Choisir —</option>
                <?php foreach ($options['client_id'] ?? [] as $optionValue => $optionLabel): ?>
                    <option value="<?= $this->e($optionValue) ?>"<?= (string) $optionValue === ($values['client_id'] ?? '') ? ' selected' : '' ?>><?= $this->e($optionLabel) ?></option>
                <?php endforeach ?>
            </select>
            <?php if (isset($errors['client_id'])): ?><div class="invalid-feedback"><?= $this->e($errors['client_id']) ?></div><?php endif ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="f-passee-le">Passee le <span class="text-danger" aria-hidden="true">*</span></label>
            <input type="datetime-local" step="1" class="form-control<?= isset($errors['passee_le']) ? ' is-invalid' : '' ?>" id="f-passee-le" name="passee_le" value="<?= $this->e($values['passee_le'] ?? '') ?>" required>
            <?php if (isset($errors['passee_le'])): ?><div class="invalid-feedback"><?= $this->e($errors['passee_le']) ?></div><?php endif ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="f-heure-livraison">Heure livraison</label>
            <input type="time" step="1" class="form-control<?= isset($errors['heure_livraison']) ? ' is-invalid' : '' ?>" id="f-heure-livraison" name="heure_livraison" value="<?= $this->e($values['heure_livraison'] ?? '') ?>">
            <?php if (isset($errors['heure_livraison'])): ?><div class="invalid-feedback"><?= $this->e($errors['heure_livraison']) ?></div><?php endif ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="f-montant-ttc">Montant ttc</label>
            <input type="text" inputmode="decimal" class="form-control<?= isset($errors['montant_ttc']) ? ' is-invalid' : '' ?>" id="f-montant-ttc" name="montant_ttc" value="<?= $this->e($values['montant_ttc'] ?? '') ?>">
            <?php if (isset($errors['montant_ttc'])): ?><div class="invalid-feedback"><?= $this->e($errors['montant_ttc']) ?></div><?php endif ?>
        </div>
    </div>
    <div class="card-footer d-flex gap-2">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a class="btn btn-outline-secondary" href="<?= $this->e($cancel) ?>">Annuler</a>
    </div>
</form>
