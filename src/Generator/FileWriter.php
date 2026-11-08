<?php

declare(strict_types=1);

namespace Gene\Generator;

/**
 * Écrit les fichiers générés sans jamais détruire le travail du développeur.
 *
 * Le manifeste .gene.json garde l'empreinte de chaque fichier tel que gene l'a écrit.
 * À la génération suivante, un fichier dont l'empreinte a changé a été modifié à la main :
 * il est conservé, sauf avec --force.
 */
final class FileWriter
{
    public const MANIFEST = '.gene.json';

    public function __construct(
        private readonly string $outputDir,
        private readonly bool $dryRun = false,
        private readonly bool $force = false,
    ) {}

    /** @param array<string, string> $files chemin relatif => contenu */
    public function write(array $files): WriteReport
    {
        $manifest = $this->readManifest();
        $newManifest = $manifest;
        $report = new WriteReport();

        foreach ($files as $relative => $content) {
            $this->assertSafePath($relative);
            $target = $this->outputDir . '/' . $relative;
            $hash = hash('sha256', $content);

            if (!is_file($target)) {
                $action = WriteAction::Created;
            } else {
                $current = hash_file('sha256', $target);
                if ($current === $hash) {
                    $report->add($relative, WriteAction::Unchanged);
                    $newManifest[$relative] = $hash;
                    continue;
                }
                $untouched = isset($manifest[$relative]) && $manifest[$relative] === $current;
                if (!$untouched && !$this->force) {
                    $report->add($relative, WriteAction::Skipped);
                    continue;
                }
                $action = $untouched ? WriteAction::Updated : WriteAction::Overwritten;
            }

            if (!$this->dryRun) {
                $this->writeFile($target, $content);
            }
            $newManifest[$relative] = $hash;
            $report->add($relative, $action);
        }

        if (!$this->dryRun) {
            ksort($newManifest);
            $this->writeFile(
                $this->outputDir . '/' . self::MANIFEST,
                json_encode(['generator' => 'gene', 'version' => 1, 'files' => $newManifest], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\n",
            );
        }

        return $report;
    }

    /** @return array<string, string> */
    private function readManifest(): array
    {
        $file = $this->outputDir . '/' . self::MANIFEST;
        if (!is_file($file)) {
            return [];
        }
        $data = json_decode((string) file_get_contents($file), true);
        $files = is_array($data) ? ($data['files'] ?? []) : [];

        return is_array($files) ? array_filter($files, 'is_string') : [];
    }

    private function assertSafePath(string $relative): void
    {
        if ($relative === '' || str_starts_with($relative, '/') || preg_match('#(^|/)\.\.(/|$)#', $relative) === 1 || str_contains($relative, "\0")) {
            throw new \InvalidArgumentException(sprintf('Chemin généré refusé : « %s ».', $relative));
        }
    }

    private function writeFile(string $target, string $content): void
    {
        // Un lien symbolique planté dans la sortie ferait écrire ailleurs que dans le dossier choisi.
        $root = rtrim(str_replace('\\', '/', $this->outputDir), '/');
        for ($path = str_replace('\\', '/', $target); strlen($path) > strlen($root); $path = dirname($path)) {
            if ($path === dirname($path)) {
                break; // racine atteinte (« / », « C:/ ») : point fixe de dirname()
            }
            if (is_link($path)) {
                throw new \RuntimeException(sprintf('Écriture refusée à travers un lien symbolique : %s.', $path));
            }
        }
        $dir = dirname($target);
        if (!is_dir($dir) && !mkdir($dir, 0o775, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Impossible de créer le dossier %s.', $dir));
        }
        $temporary = $target . '.tmp-' . bin2hex(random_bytes(4));
        if (file_put_contents($temporary, $content) === false || !rename($temporary, $target)) {
            @unlink($temporary);

            throw new \RuntimeException(sprintf('Impossible d\'écrire %s.', $target));
        }
    }
}
