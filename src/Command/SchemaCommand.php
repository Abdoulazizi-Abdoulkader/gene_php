<?php

declare(strict_types=1);

namespace Gene\Command;

use Gene\Config\ProfileStore;
use Gene\Generator\ModelBuilder;
use Gene\Schema\ConnectionError;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'schema', description: 'Affiche le schéma tel que gene le comprend (types, clés, relations)')]
final class SchemaCommand extends Command
{
    use DatabaseOptions;

    public function __construct(private readonly ProfileStore $profiles = new ProfileStore())
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->configureDatabaseOptions($this);
        $this->addOption('tables', 't', InputOption::VALUE_REQUIRED, 'Tables à afficher, séparées par des virgules');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $schema = $this->settings($input, $io, $this->profile($input, $this->profiles))->readSchema()
                ->filter(self::csv($input->getOption('tables')));
        } catch (\PDOException $e) {
            $io->error(ConnectionError::explain($e));

            return Command::FAILURE;
        }

        $io->title(sprintf('Base « %s » : %d table(s)', $schema->database, count($schema->tables)));
        foreach ($schema->tables as $table) {
            $io->section($table->name . ($table->comment !== '' ? ' — ' . $table->comment : ''));
            $rows = [];
            foreach ($table->columns as $column) {
                $fk = $table->foreignKeyFor($column->name);
                $flags = array_filter([
                    $column->primary ? 'PK' : null,
                    $column->autoIncrement ? 'auto' : null,
                    $column->generated ? 'calculée' : null,
                    $column->nullable ? 'NULL' : null,
                    $column->isRequired() ? 'obligatoire' : null,
                ]);
                $rows[] = [
                    $column->name,
                    $column->sqlType,
                    $column->kind->value,
                    implode(', ', $flags),
                    $column->defaultIsExpression ? '(expression)' : ($column->default ?? ''),
                    $fk !== null ? $fk->referencedTable . '.' . $fk->referencedColumn : '',
                ];
            }
            $io->table(['Colonne', 'Type SQL', 'Type gene', 'Attributs', 'Défaut', 'Référence'], $rows);
        }

        $builder = new ModelBuilder();
        $builder->build($schema);
        foreach ($builder->warnings() as $warning) {
            $io->warning($warning);
        }

        return Command::SUCCESS;
    }
}
