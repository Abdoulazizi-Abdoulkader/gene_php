<?php

declare(strict_types=1);

namespace Gene\Tests\Unit\Framework;

use App\Framework\Form;
use PHPUnit\Framework\TestCase;

final class FormTest extends TestCase
{
    private const FIELDS = [
        'nom' => ['label' => 'Nom', 'type' => 'string', 'required' => true, 'nullable' => false, 'maxLength' => 5],
        'age' => ['label' => 'Âge', 'type' => 'int', 'required' => false, 'nullable' => true, 'min' => 0, 'max' => 255],
        'solde' => ['label' => 'Solde', 'type' => 'decimal', 'required' => false, 'nullable' => false, 'precision' => 5, 'scale' => 2, 'default' => '0.00'],
        'taux' => ['label' => 'Taux', 'type' => 'float', 'required' => false, 'nullable' => true],
        'actif' => ['label' => 'Actif', 'type' => 'bool', 'required' => false, 'nullable' => false, 'default' => '1'],
        'statut' => ['label' => 'Statut', 'type' => 'enum', 'required' => true, 'nullable' => false, 'choices' => ['a', 'b']],
        'jour' => ['label' => 'Jour', 'type' => 'date', 'required' => false, 'nullable' => true],
        'quand' => ['label' => 'Quand', 'type' => 'datetime', 'required' => false, 'nullable' => true],
        'heure' => ['label' => 'Heure', 'type' => 'time', 'required' => false, 'nullable' => true],
        'meta' => ['label' => 'Méta', 'type' => 'json', 'required' => false, 'nullable' => true],
        'note' => ['label' => 'Note', 'type' => 'text', 'required' => false, 'nullable' => true, 'maxBytes' => 10],
        'code' => ['label' => 'Code', 'type' => 'string', 'required' => false, 'nullable' => false, 'default' => ''],
    ];

    public function testValidInputIsConverted(): void
    {
        $result = Form::validate([
            'nom' => '  Éloï ', 'age' => '42', 'solde' => '012,50', 'taux' => '1,5', 'actif' => '1', 'statut' => 'b',
            'jour' => '2024-02-29', 'quand' => '2024-02-29T08:05', 'heure' => '09:30', 'meta' => '{"a":1}',
            'note' => 'été', 'code' => 'X1',
        ], self::FIELDS);

        self::assertTrue($result->isValid(), print_r($result->errors, true));
        self::assertSame([
            'nom' => 'Éloï', 'age' => 42, 'solde' => '12.5', 'taux' => 1.5, 'actif' => true, 'statut' => 'b',
            'jour' => '2024-02-29', 'quand' => '2024-02-29 08:05:00', 'heure' => '09:30:00', 'meta' => '{"a":1}',
            'note' => 'été', 'code' => 'X1',
        ], $result->values);
    }

    public function testEmptyFields(): void
    {
        $result = Form::validate(['nom' => 'x', 'statut' => 'a'], self::FIELDS);

        self::assertTrue($result->isValid());
        self::assertNull($result->values['age'], 'colonne NULL vidée : NULL');
        self::assertSame('0', $result->values['solde'], 'colonne NOT NULL vidée : sa valeur par défaut');
        self::assertSame('', $result->values['code'], 'défaut chaîne vide : la colonne peut être vidée');
        self::assertFalse($result->values['actif'], 'case décochée : false');
    }

    public function testErrors(): void
    {
        $result = Form::validate([
            'nom' => 'trop long', 'age' => '-1', 'solde' => '1234.5', 'taux' => 'abc', 'statut' => 'z',
            'jour' => '2023-02-29', 'quand' => 'demain', 'heure' => '25:00', 'meta' => '{', 'note' => 'ééééééé',
        ], self::FIELDS);

        self::assertFalse($result->isValid());
        self::assertSame(['nom', 'age', 'solde', 'taux', 'statut', 'jour', 'quand', 'heure', 'meta', 'note'], array_keys($result->errors));
        self::assertSame('Nombre entre 0 et 255 attendu.', $result->errors['age']);
        self::assertSame(['age'], array_keys(Form::validate(['nom' => 'x', 'statut' => 'a', 'age' => '256'], self::FIELDS)->errors));
        self::assertSame('trop long', $result->raw['nom'], 'la saisie est conservée pour réaffichage');
    }

    public function testRequired(): void
    {
        $result = Form::validate(['nom' => '   '], self::FIELDS);
        self::assertSame('Ce champ est obligatoire.', $result->errors['nom']);
        self::assertSame('Ce champ est obligatoire.', $result->errors['statut']);
    }

    public function testDefaultsAndFromRow(): void
    {
        $defaults = Form::defaults(self::FIELDS);
        self::assertSame('0.00', $defaults['solde']);
        self::assertSame('1', $defaults['actif']);
        self::assertSame('', $defaults['nom']);

        $values = Form::fromRow(['quand' => '2024-01-02 03:04:05', 'actif' => false, 'age' => 7, 'jour' => null], self::FIELDS);
        self::assertSame('2024-01-02T03:04:05', $values['quand'], 'secondes conservées');
        self::assertSame('2024-01-02 03:04:05', Form::validate(['nom' => 'x', 'statut' => 'a', 'quand' => $values['quand']], self::FIELDS)->values['quand']);
        self::assertSame('0', $values['actif']);
        self::assertSame('7', $values['age']);
        self::assertSame('', $values['jour']);
    }
}
