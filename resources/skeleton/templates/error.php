<?php
/**
 * @var App\Framework\View $this
 * @var int $status
 * @var string $message
 * @var Throwable|null $error détail affiché uniquement si APP_DEBUG est actif
 */
$this->title('Erreur ' . $status);
?>
<div class="py-5 text-center">
    <p class="display-5 fw-semibold text-body-secondary mb-2"><?= $this->e($status) ?></p>
    <p class="lead"><?= $this->e($message) ?></p>
    <a class="btn btn-outline-primary mt-3" href="<?= $this->e($this->url('/')) ?>">Retour à l'accueil</a>
</div>
<?php if ($error !== null): ?>
    <div class="alert alert-warning">
        <strong><?= $this->e($error::class) ?></strong> : <?= $this->e($error->getMessage()) ?>
        <div class="small mt-1"><?= $this->e($error->getFile()) ?>:<?= $this->e($error->getLine()) ?></div>
    </div>
    <pre class="small bg-body-tertiary p-3 border rounded"><?= $this->e($error->getTraceAsString()) ?></pre>
<?php endif ?>
