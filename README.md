# gene — générateur d'applications CRUD PHP

gene lit le schéma d'une base **MySQL 8 / MariaDB 10.6+** et génère une application web PHP complète. Pour chaque
table, elle comprend une liste paginée avec recherche et tri, une fiche, et la création, la modification et la
suppression. Les formulaires sont validés selon les types SQL, et la protection contre CSRF, XSS et l'injection SQL
est intégrée.

- **Linux et Windows**, PHP 8.3+ (`pdo_mysql`, `mbstring`).
- **Assistant interactif** (`gene`), **ligne de commande** pour les scripts (`gene generate`), **interface web**.
- **Régénération sans perte** : vos fichiers modifiés sont conservés.
- **Application autonome** : aucun framework externe, Composer facultatif, Bootstrap 5.3 inclus.

📖 **Guide d'utilisation complet : [docs/GUIDE.md](docs/GUIDE.md)** (installation, assistant, options, déploiement,
personnalisation, dépannage).

## Démarrage rapide

```bash
composer install        # une fois
```

| Linux / macOS | Windows (cmd) | Windows (PowerShell) |
| --- | --- | --- |
| `bin/gene` | `gene` | `.\gene.ps1` |

Sans argument, gene lance l'**assistant** :
1. Il demande le serveur et le compte, puis teste la connexion.
2. Vous choisissez la base dans une liste, puis les tables, le nom et le dossier.
3. Il génère l'application, propose d'enregistrer un profil, puis de la lancer.

```bash
bin/gene doctor                                   # vérifier l'installation
bin/gene                                          # assistant
bin/gene generate -d boutique -u dev              # sans questions (mot de passe : saisie masquée ou GENE_DB_PASSWORD)
bin/gene generate --profile=boutique              # rejouer une génération enregistrée
php -S localhost:8080 -t output/Boutique/public   # lancer l'application générée
```

## Développement de gene

```bash
composer check          # style (php-cs-fixer), PHPStan niveau 8, tests unitaires et snapshots
composer test           # toute la suite (l'intégration est ignorée sans base)
composer cs:fix         # applique le style PER-CS 2.0
vendor/bin/phpunit --filter InflectorTest            # une classe de test
vendor/bin/phpunit --filter 'testLabel#1'            # un cas précis d'un fournisseur de données
```

**Snapshots** : `tests/Snapshot/expected/` contient la sortie attendue pour `tests/Fixtures/SchemaFixture.php`. Le
test vérifie aussi que le code produit compile et passe PHPStan niveau 8, y compris avec des noms de tables hostiles ou
homonymes des classes du framework. Après une modification volontaire des gabarits :

```bash
UPDATE_SNAPSHOTS=1 vendor/bin/phpunit --testsuite snapshot && git diff tests/Snapshot/expected
```

**Intégration** : ces tests génèrent une application à partir d'une vraie base, la lancent, rejouent un scénario HTTP
complet et pilotent l'assistant. La base indiquée est **vidée**.

```bash
docker run -d --rm --name gene-mysql -e MYSQL_ROOT_PASSWORD=gene-test -e MYSQL_DATABASE=gene_test \
    -p 127.0.0.1:33084:3306 mysql:8.4
docker exec -i gene-mysql mysql -uroot -pgene-test gene_test < tests/Fixtures/schema.sql
GENE_TEST_DSN="mysql:host=127.0.0.1;port=33084;dbname=gene_test" GENE_TEST_USER=root GENE_TEST_PASSWORD=gene-test \
    vendor/bin/phpunit --group integration
```

Le générateur de 2018 est archivé dans `legacy/`, pour consultation. Il ne fonctionne pas sous PHP 8.
