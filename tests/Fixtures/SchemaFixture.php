<?php

declare(strict_types=1);

namespace Gene\Tests\Fixtures;

use Gene\Schema\Column;
use Gene\Schema\ColumnKind as K;
use Gene\Schema\ForeignKey;
use Gene\Schema\Schema;
use Gene\Schema\Table;

/**
 * Schéma de référence couvrant les cas délicats, sans base de données.
 * Le même schéma existe en SQL dans tests/Fixtures/schema.sql pour les tests d'intégration.
 */
final class SchemaFixture
{
    public static function schema(): Schema
    {
        return new Schema('gene_test', [
            new Table('pays', [
                new Column('code', K::String, 'char(2)', primary: true, maxLength: 2),
                new Column('libelle', K::String, 'varchar(80)', maxLength: 80),
            ]),
            new Table('client', [
                new Column('id', K::Int, 'int unsigned', autoIncrement: true, primary: true, unsigned: true),
                new Column('nom', K::String, 'varchar(100)', maxLength: 100),
                new Column('email', K::String, 'varchar(190)', nullable: true, maxLength: 190),
                new Column('date_naissance', K::Date, 'date', nullable: true),
                new Column('actif', K::Bool, 'tinyint(1)', default: '1'),
                new Column('solde', K::Decimal, 'decimal(10,2)', default: '0.00', precision: 10, scale: 2),
                new Column('statut', K::Enum, "enum('prospect','client','ancien')", default: 'prospect', enumValues: ['prospect', 'client', 'ancien']),
                new Column('pays_code', K::String, 'char(2)', nullable: true, maxLength: 2),
                new Column('parrain_id', K::Int, 'int unsigned', nullable: true, unsigned: true),
                new Column('notes', K::Text, 'text', nullable: true),
                new Column('cree_le', K::DateTime, 'datetime', defaultIsExpression: true),
            ], [
                new ForeignKey('fk_client_pays', 'pays_code', 'pays', 'code'),
                new ForeignKey('fk_client_parrain', 'parrain_id', 'client', 'id'),
            ]),
            new Table('commande', [
                new Column('id', K::Int, 'int', autoIncrement: true, primary: true),
                new Column('client_id', K::Int, 'int unsigned', unsigned: true),
                new Column('passee_le', K::DateTime, 'datetime'),
                new Column('heure_livraison', K::Time, 'time', nullable: true),
                new Column('montant_ttc', K::Float, 'double', default: '0'),
                new Column('pièce_jointe', K::Binary, 'blob', nullable: true),
            ], [
                new ForeignKey('fk_commande_client', 'client_id', 'client', 'id'),
            ]),
            new Table('ligne_commande', [
                new Column('commande_id', K::Int, 'int', primary: true),
                new Column('numero', K::Int, 'smallint', primary: true),
                new Column('quantite', K::Int, 'int', default: '1'),
            ], [
                new ForeignKey('fk_ligne_commande', 'commande_id', 'commande', 'id'),
            ]),
            new Table('journal', [
                new Column('message', K::Text, 'text'),
            ]),
        ]);
    }
}
