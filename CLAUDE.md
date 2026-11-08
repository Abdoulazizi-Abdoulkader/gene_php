# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Nature du projet

gene lit un schéma MySQL/MariaDB et génère une application CRUD PHP autonome. Deux bases de code coexistent :

- `src/` (espace de noms `Gene\`) : **l'outil**. Il tourne en PHP 8.3+ et utilise Symfony Console 7.4 LTS et Twig 3.
- `resources/` : **ce qui est produit**.
  - `resources/skeleton/` est copié tel quel dans chaque application (espace de noms `App\`, aucune dépendance) ;
  - `resources/templates/*.twig` est rendu pour chaque table.

`legacy/` contient le générateur de 2018, archivé et non fonctionnel sous PHP 8. Ne pas le modifier.

## Commandes

```bash
composer check                       # php-cs-fixer (dry-run) + PHPStan niveau 8 + tests hors intégration
vendor/bin/phpunit --filter FormTest # une classe ; --testsuite unit|snapshot|integration
composer cs:fix                      # PER-CS 2.0
UPDATE_SNAPSHOTS=1 vendor/bin/phpunit --testsuite snapshot   # après un changement voulu des gabarits
bin/gene                                                     # assistant interactif (commande par défaut : new)
bin/gene generate -d <base> -u <user> -o output/X            # mot de passe : GENE_DB_PASSWORD ou saisie masquée
bin/gene generate --profile=<nom>                            # profils : ~/.config/gene ou %APPDATA%\gene (GENE_CONFIG_DIR)
bin/gene schema -d <base> -u <user>                          # diagnostic de la lecture du schéma
bin/gene doctor [--connect …]                                # vérification de l'installation
php -S localhost:8000 -t public                              # interface web de l'outil
```

Le test d'intégration (`tests/Integration`) n'est exécuté que si `GENE_TEST_DSN`, `GENE_TEST_USER` et
`GENE_TEST_PASSWORD` sont définis. Il **vide** la base indiquée. Le README donne la commande Docker.

Le guide utilisateur est `docs/GUIDE.md`. Gardez-le à jour quand une commande, une option ou un comportement visible
change. Les extraits de console qu'il cite sont des sorties réelles.

## Points d'entrée

- `bin/gene` (Linux, macOS). Sous Windows, `gene.cmd` (CRLF, ASCII pur) et `gene.ps1` (UTF-8 avec BOM, CRLF) le lancent
  avec PHP. `.gitattributes` fixe ces fins de ligne.
- Les commandes `NewCommand` (assistant), `GenerateCommand`, `SchemaCommand`, `DoctorCommand` et `Profile*Command`, ainsi
  que l'interface web `WebApp`, passent toutes par :
  - `ConnectionSettings` (connexion) ;
  - `Project::generate()` (flux schéma → fichiers → disque) ;
  - `Presenter` (affichage).
  Ne pas réintroduire de logique de génération dans une commande.
- Options de connexion partagées (trait `DatabaseOptions`), dans cet ordre de priorité : option, puis profil, puis
  variable d'environnement, puis défaut. Le mot de passe n'est jamais un argument et n'est jamais écrit dans un profil.
- Les questions oui/non de l'assistant passent par `NewCommand::yesNo()` (o/oui/y/yes), pas par `$io->confirm()`, qui
  n'accepte que l'anglais.

## Chaîne de génération

`MySqlSchemaReader` produit un `Schema`, à partir de 4 requêtes `information_schema` liées par paramètres.
`ModelBuilder` le transforme en `EntityModel` et `FieldModel`. `Generator` rend ensuite les gabarits Twig et
renvoie `chemin => contenu`. Enfin, `FileWriter` écrit les fichiers.

- `ColumnKind` (int, decimal, bool, enum, date…) pilote tout : type PHP, contrôle HTML, validation.
  Le mapping SQL vers `ColumnKind`, ainsi que la normalisation des valeurs par défaut, sont dans
  `MySqlSchemaReader`. MySQL et MariaDB exposent les défauts différemment : voir `normalizeDefault`.
- Twig est réglé avec `autoescape: false` (on produit du PHP) et `strict_variables: true`. Toute valeur issue
  de la base passe par un filtre explicite :
  - `|php` (`PhpExporter`, littéral PHP sûr) dans le code ;
  - `|html` dans le HTML ;
  - `|comment` dans un docblock ;
  - `|env` dans `.env` ;
  - `|md` dans le README généré.
  Ne jamais interpoler un nom de table ou de colonne sans l'un de ces filtres. Seuls les identifiants issus
  d'`Inflector` (`[A-Za-z0-9]`) et les entiers peuvent l'être sans filtre. `testHostileIdentifiersCannotInjectCode`
  en apporte la preuve.
- Dans les gabarits PHP générés, les classes d'un autre espace de noms sont référencées par alias
  (`Entity\X`, `Framework\Form`, `Repository\XRepository`), jamais par `use` d'une classe. Une table nommée
  `form` ou `entity` provoquerait sinon une collision d'import (`testFrameworkClassNamesAsTables`).
- `FileWriter` tient `.gene.json` (empreintes SHA-256). Un fichier modifié à la main n'est jamais écrasé
  sans `--force`.
- Les tables sans PK sont ignorées. Une PK composite ne donne qu'une liste (`readOnly`). Une FK n'est
  exploitée que si sa cible est générée et que la colonne cible est sa PK ; sinon le champ est saisi
  librement, avec un avertissement.

## Application générée (`resources/skeleton`)

- Contrôleur frontal `public/index.php`. Il utilise `vendor/autoload.php` s'il existe, sinon un autoload
  PSR-4 de secours.
- `App` gère la configuration (`.env` puis environnement réel), le routage (`config/routes.php`) et la
  **vérification CSRF centralisée de tout POST**.
- `Repository` (abstrait) contient tout le SQL. Il est piloté par les constantes du dépôt généré :
  `TABLE`, `PRIMARY_KEY`, `COLUMNS`, `WRITABLE`, `KEEP_IF_NULL`, `RELATIONS`.
- `Form` valide et convertit à partir de `Entity::FIELDS`. Clés produites par `FieldModel::spec()` :
  `maxLength`, `maxBytes`, `min`/`max` (entiers), `choices`, `default`, `precision`/`scale`.
  Un champ NOT NULL vidé reprend son `default` littéral. `KEEP_IF_NULL` ne concerne que les défauts
  calculés (CURRENT_TIMESTAMP).
- `Database::userMessage()` traduit les SQLSTATE dus aux données (23000, 22001, 22003…) en message de formulaire.
  Toute autre `PDOException` remonte en 500.
- Le routage se fait sur le chemin **encodé** ; `Router` décode les paramètres (un `%2F` dans un id reste un segment).
- `config/routes.php` et `menu.php` sont régénérés. Les personnalisations vont dans `routes.custom.php` et
  `menu.custom.php`, fichiers du squelette protégés par le manifeste.
- Gabarits PHP : `$this` y est la `View`. Toute sortie passe par `$this->e()` ou `$this->display()`.
- La politique CSP interdit scripts et styles en ligne. Le JS va dans `public/assets/js/app.js` (exemple :
  confirmation par `data-confirm`), le CSS dans `app.css`.

Tout changement du squelette ou des gabarits doit garder vert `tests/Snapshot`. Ce test lance `php -l` sur
chaque fichier généré et passe PHPStan niveau 8 sur le code produit.
