<?php

declare(strict_types=1);

namespace Gene\Schema;

/** Paramètres de connexion à un serveur MySQL/MariaDB (le mot de passe n'est jamais persisté). */
final readonly class ConnectionSettings
{
    public function __construct(
        public string $host = '127.0.0.1',
        public string $port = '3306',
        public string $database = '',
        public string $user = 'root',
        #[\SensitiveParameter]
        public string $password = '',
        /** DSN complet fourni tel quel (--dsn, GENE_DSN) : prime sur host/port/database. */
        public ?string $rawDsn = null,
    ) {}

    public function dsn(): string
    {
        return $this->rawDsn ?? MySqlSchemaReader::dsn($this->host, $this->port, $this->database);
    }

    public function withDatabase(string $database): self
    {
        return new self($this->host, $this->port, $database, $this->user, $this->password, null);
    }

    public function connect(): \PDO
    {
        return MySqlSchemaReader::connect($this->dsn(), $this->user, $this->password);
    }

    public function readSchema(): Schema
    {
        if ($this->rawDsn === null && $this->database === '') {
            throw new \InvalidArgumentException('Indiquez le nom de la base.');
        }

        return (new MySqlSchemaReader($this->connect()))->read();
    }
}
