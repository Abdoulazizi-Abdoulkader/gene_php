# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Nature du projet

Générateur de code PHP (2018, auteur Mahaman Sani Nomaou). Il introspecte une base MySQL
et produit une application CRUD MVC complète (une entité par table possédant une clé primaire).
Pas de git, pas de Composer, pas de tests, pas d'outil de lint : uniquement du PHP « nu » servi par Apache.

## Exécution

- Sert sous Apache + mod_rewrite, le dossier étant accessible via `http://localhost/gene/`.
- `index.php` : formulaire (nom d'app, hôte, base, login, mot de passe) qui fait un POST vers `generate.php`.
- `generate.php` s'exécute **à l'inclusion** (appel `generate()` en fin de fichier) :
  il crée `./<nomapp>/`, le copie ensuite dans `../<nomapp>/` (le répertoire frère, donc la racine web),
  puis redirige vers `/<nomapp>/`.
- Vérification de syntaxe : `php -l generate.php` (PHP CLI installé : 8.5).
  Les fichiers `.tpl` ne sont pas du PHP valide avant substitution, il ne faut donc pas les passer à `php -l`.

## Architecture du générateur

- **Introspection** : `SHOW TABLES`, `DESC <table>` (indices de colonne : `[0]`=nom, `[1]`=type, `[3]`=`PRI`)
  et `information_schema.KEY_COLUMN_USAGE` pour les clés étrangères (`getRefTableName`/`getRefColumnName`).
  Les tables sans PK sont ignorées partout (`doesTableContainPK`).
- **Connexion du générateur lui-même** : couche héritée de phpdao.com dans `templates/class/dao/sql/`
  (`QueryExecutor`, `SqlQuery`, `Connection`, mysqli). Elle lit `templates/Config/dev.ini` via
  `templates/Framework/Configuration.php`. `createConfigFile()` **réécrit** `templates/Config/dev.ini`
  avec les identifiants saisis, avant toute requête.
  Une partie de cette couche appelle encore les fonctions `mysql_*`, supprimées depuis PHP 7
  (`executeUpdate`, `SqlQuery::set`, etc.). Seul le chemin `execute()` en mysqli fonctionne.
- **Moteur de gabarits** : `templates/class/Template.php`, qui fait un simple `str_replace` de `${clé}`.
  Une clé non fournie reste telle quelle dans la sortie, sans erreur.
- **Fonctions `getnerate*`** (orthographe d'origine) dans `generate.php` : chacune boucle sur les tables
  et remplit un `.tpl`. Celles qui sont actives sont appelées dans `generate()` : ClassObjects, Managers,
  ManagerFactory, Controleur, et les vues ajout/index/modification/details, plus le menu.
  Les variantes DAO, IDAO, DAOExt et Domain sont du code mort (appels commentés).
  Une grande partie du HTML des vues est construite par concaténation directe dans `generate.php`,
  pas dans les `.tpl`.
- **Conventions de nommage** : `getClazzName` (snake_case vers PascalCase), `getVarName`
  (camelCase, retire le « s » final), `getDTOName`. Les propriétés et getters générés utilisent
  `strtolower(colonne)`.

## Architecture de l'application générée

Mini-framework MVC « Baptiste Pesquet », copié depuis `templates/Framework/` et `templates/others/` :

- `.htaccess` réécrit `xxx/yyy/zzz` en `index.php?controleur=xxx&action=yyy&id=zzz`.
- `Routeur` charge `Controleur/Controleur<Ucfirst>.php`, puis appelle la méthode qui porte le nom de l'action.
  `ControleurSimple::genererVue()` rend `Vue/<Controleur>/<action>.php` à l'intérieur de `Vue/gabarit.php`.
- `Controleur` (classe parente) est censé contrôler l'authentification, mais ce contrôle est commenté.
- Par table : `class/classObjects/<Table>.php` (entité construite à partir d'un tableau),
  `class/managerObjects/<Table>Manager.php` (étend `Modele`, PDO, requêtes préparées pour load/insert/update),
  et `ManagersFactory::get<Table>Manager()`.
- `include_files.php` contient tous les `require_once` (pas d'autoload).
- Configuration : `Config/dev.ini`, puis `prod.ini` en repli (clés `racineWeb`, `dsn`, `login`, `mdp`, `host`, `database`).
- Les ressources front (Bootstrap, jQuery, datetimepicker, font-awesome) sont copiées depuis `templates/Contenu/`.

## Pièges connus

- `generated/` est une **ancienne sortie** produite par une version précédente du générateur
  (structure phpdao `dto/`, `mysql/`, `dao/`, `include_dao.php`). Elle ne reflète pas la sortie actuelle.
- `templates/Config/*.ini` contiennent des identifiants réels ; `dev.ini` est écrasé à chaque génération.
- `generate.php` émet beaucoup d'`echo` de débogage (types de colonnes, chemins de fichiers),
  ce qui peut empêcher la redirection finale (`header()` envoyé après une sortie).
- Les noms de table et de colonne sont injectés sans échappement dans le SQL du générateur
  et dans le code produit.
