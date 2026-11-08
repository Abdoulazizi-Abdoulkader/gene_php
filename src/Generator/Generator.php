<?php

declare(strict_types=1);

namespace Gene\Generator;

use Gene\Schema\Schema;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

/**
 * Produit l'ensemble des fichiers de l'application (chemin relatif => contenu), sans rien écrire.
 */
final class Generator
{
    private readonly Environment $twig;

    public function __construct(
        private readonly string $resourcesDir = __DIR__ . '/../../resources',
    ) {
        $this->twig = new Environment(new FilesystemLoader($this->resourcesDir . '/templates'), [
            'autoescape' => false,      // on produit du PHP : l'échappement est explicite (filtres php et html)
            'strict_variables' => true, // une variable absente est une erreur, pas un trou dans le code
            'cache' => false,
        ]);
        $this->twig->addFilter(new TwigFilter('php', static fn(mixed $v, int $indent = 0): string => PhpExporter::export($v, $indent)));
        $this->twig->addFilter(new TwigFilter('html', static fn(mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')));
        // Texte placé dans un commentaire PHP : « */ » le refermerait, les retours à la ligne le casseraient.
        $this->twig->addFilter(new TwigFilter('comment', static fn(mixed $v): string => str_replace(['*/', "\r", "\n"], ['* /', ' ', ' '], (string) $v)));
        // Texte Markdown : caractères de mise en forme et de tableau neutralisés.
        $this->twig->addFilter(new TwigFilter('md', static fn(mixed $v): string => (string) preg_replace('/([\\\\`*_{}\[\]<>()#+!|~-])/', '\\\\$1', str_replace(["\r", "\n"], ' ', (string) $v))));
        // Valeur entre guillemets doubles dans un fichier .env.
        $this->twig->addFilter(new TwigFilter('env', static fn(mixed $v): string => str_replace(['\\', '"', "\r", "\n"], ['\\\\', '\\"', '', ''], (string) $v)));
    }

    public function generate(Schema $schema, GenerationOptions $options): GenerationResult
    {
        $builder = new ModelBuilder();
        $entities = $builder->build($schema);

        $context = ['app' => $options, 'entities' => $entities, 'database' => $schema->database];
        $files = $this->skeletonFiles();

        $files['composer.json'] = $this->render('composer.json.twig', $context);
        $files['.env.example'] = $this->render('env.twig', [...$context, 'withUser' => false]);
        $files['.env'] = $this->render('env.twig', [...$context, 'withUser' => true]);
        $files['README.md'] = $this->render('README.md.twig', $context);
        $files['config/routes.php'] = $this->render('routes.php.twig', $context);
        $files['config/menu.php'] = $this->render('menu.php.twig', $context);

        foreach ($entities as $entity) {
            $ctx = [...$context, 't' => $entity];
            $files['src/Entity/' . $entity->className . '.php'] = $this->render('entity.php.twig', $ctx);
            $files['src/Repository/' . $entity->className . 'Repository.php'] = $this->render('repository.php.twig', $ctx);
            $files['src/Controller/' . $entity->className . 'Controller.php'] = $this->render('controller.php.twig', $ctx);
            $files['templates/' . $entity->slug . '/index.php'] = $this->render('views/index.php.twig', $ctx);
            if (!$entity->readOnly()) {
                $files['templates/' . $entity->slug . '/show.php'] = $this->render('views/show.php.twig', $ctx);
                $files['templates/' . $entity->slug . '/form.php'] = $this->render('views/form.php.twig', $ctx);
            }
        }

        ksort($files, SORT_STRING);

        return new GenerationResult($files, $builder->warnings(), count($entities));
    }

    /** @param array<string, mixed> $context */
    private function render(string $template, array $context): string
    {
        return $this->twig->render($template, $context);
    }

    /** @return array<string, string> */
    private function skeletonFiles(): array
    {
        $root = $this->resourcesDir . '/skeleton';
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
        );
        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if ($file->isFile()) {
                $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
                $files[$relative] = (string) file_get_contents($file->getPathname());
            }
        }

        return $files;
    }
}
