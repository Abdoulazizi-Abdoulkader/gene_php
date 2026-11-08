# Guide d'utilisation de gene

gene lit la structure d'une base **MySQL** ou **MariaDB** et écrit pour vous une application web PHP complète.
Elle propose, pour chaque table, une liste paginée avec recherche et tri, une fiche, et les formulaires de création,
de modification et de suppression.

Ce guide s'adresse à toute personne qui veut utiliser gene sous **Linux** ou sous **Windows**, de l'installation au
déploiement. Il ne suppose aucune connaissance préalable de l'outil. Une connaissance de base de PHP et de MySQL aide
pour les sections 9 et 10.

## Sommaire

1. [Ce que fait gene](#1-ce-que-fait-gene)
2. [Installer PHP et Composer](#2-installer-php-et-composer)
3. [Installer gene](#3-installer-gene)
4. [Premier projet avec l'assistant](#4-premier-projet-avec-lassistant)
5. [Générer sans questions (scripts, CI)](#5-générer-sans-questions-scripts-ci)
6. [Les profils](#6-les-profils)
7. [L'interface web](#7-linterface-web)
8. [Lancer et déployer l'application générée](#8-lancer-et-déployer-lapplication-générée)
9. [Comprendre le code généré](#9-comprendre-le-code-généré)
10. [Personnaliser et régénérer](#10-personnaliser-et-régénérer)
11. [Comment gene interprète votre schéma](#11-comment-gene-interprète-votre-schéma)
12. [Sécurité](#12-sécurité)
13. [Dépannage](#13-dépannage)
14. [Référence des commandes](#14-référence-des-commandes)

---

## 1. Ce que fait gene

```text
 Base MySQL « boutique »                 Application « Boutique » (PHP 8.3+)
 ┌─────────────────────┐                 ┌──────────────────────────────────────────────┐
 │ client  (id, nom…)  │                 │ /client          liste, recherche, tri       │
 │ commande (…, client_id → client)  ──► │ /client/new      formulaire de création      │
 │ pays    (code, …)   │    gene         │ /client/12       fiche                       │
 └─────────────────────┘                 │ /client/12/edit  modification ; suppression  │
                                         │ /commande …      liste déroulante des clients│
                                         └──────────────────────────────────────────────┘
```

- **Il lit** les tables, les colonnes et leurs types, les clés primaires, les clés étrangères et les valeurs par
  défaut.
- **Il écrit** un dossier autonome : il fonctionne sans Composer, sans framework externe, avec Bootstrap 5 inclus.
- **Il ne modifie jamais votre base.** Il la lit seulement.
- **Il ne détruit jamais votre travail.** Relancé après une évolution du schéma, il met à jour les fichiers que vous
  n'avez pas touchés et conserve ceux que vous avez modifiés.

Trois façons de s'en servir :

| Mode | Pour qui | Commande |
| --- | --- | --- |
| Assistant | Usage courant, découverte | `gene` |
| Ligne de commande | Scripts, intégration continue | `gene generate …` |
| Interface web | Si vous préférez un formulaire | `php -S localhost:8000 -t public` |

---

## 2. Installer PHP et Composer

gene et les applications qu'il produit demandent **PHP 8.3 ou plus**, avec deux extensions obligatoires :

| Extension | Rôle | Statut |
| --- | --- | --- |
| `pdo_mysql` | Connexion à MySQL/MariaDB | Obligatoire |
| `mbstring` | Textes accentués | Obligatoire |
| `intl` | Noms accentués convertis proprement en noms de classes | Conseillée |
| `zip` | Téléchargement en zip depuis l'interface web | Conseillée |
| `openssl` | Nécessaire à Composer pour télécharger les dépendances | Requise par Composer |

Vérifiez d'abord ce que vous avez déjà :

```bash
php -v          # doit afficher 8.3 ou plus
php -m          # liste des extensions chargées
```

### 2.1 Sous Linux

**Ubuntu 24.04 et plus récent** (PHP 8.3 fourni par défaut) :

```bash
sudo apt update
sudo apt install php-cli php-mysql php-mbstring php-intl php-zip unzip composer
```

**Debian 12, Ubuntu 22.04 ou plus ancien** : les paquets officiels proposent une version de PHP trop ancienne. Ajoutez
le dépôt de Ondřej Surý (`ppa:ondrej/php` sous Ubuntu, `packages.sury.org` sous Debian), puis installez par exemple
`php8.3-cli php8.3-mysql php8.3-mbstring php8.3-intl php8.3-zip`.

**Fedora, RHEL, Rocky, Alma** :

```bash
sudo dnf install php-cli php-mysqlnd php-mbstring php-intl php-zip composer
```

Si `composer` n'est pas proposé par votre distribution, suivez l'installation officielle sur <https://getcomposer.org/download/>.

### 2.2 Sous Windows

1. **Télécharger PHP** : sur <https://windows.php.net/download/>, prenez la dernière version **8.3 ou plus**, en
   variante **x64 Non Thread Safe**, au format zip.
2. **Décompresser** l'archive dans un dossier sans espaces, par exemple `C:\php`.
3. **Créer le fichier de configuration** : dans `C:\php`, copiez `php.ini-development` et nommez la copie `php.ini`.
4. **Activer les extensions** : ouvrez `php.ini` dans un éditeur de texte, puis retirez le `;` en début de ligne pour :
   ```ini
   extension_dir = "ext"
   extension=intl
   extension=mbstring
   extension=openssl
   extension=pdo_mysql
   extension=zip
   ```
5. **Ajouter PHP au PATH** :
   1. Menu Démarrer, puis tapez « variables d'environnement ».
   2. Ouvrez *Modifier les variables d'environnement système*, puis cliquez sur *Variables d'environnement*.
   3. Dans *Path* (utilisateur), cliquez sur *Nouveau* et ajoutez `C:\php`.
   4. Validez, puis **rouvrez** votre terminal.
6. **Installer Composer** avec l'installateur `Composer-Setup.exe` (<https://getcomposer.org/download/>). Il retrouve
   `C:\php\php.exe` tout seul.

> **Vous utilisez déjà XAMPP ou WAMP ?** Ils contiennent un PHP, par exemple dans `C:\xampp\php`. Vérifiez sa version
> avec `C:\xampp\php\php.exe -v`. Si elle est en 8.3 ou plus, ajoutez ce dossier au PATH plutôt que d'installer un
> second PHP.

Vérification, dans un **nouveau** terminal (cmd ou PowerShell) :

```powershell
php -v
composer -V
```

---

## 3. Installer gene

### 3.1 Récupérer le projet et ses dépendances

Placez le dossier `gene` où vous voulez, par exemple `~/outils/gene` ou `C:\outils\gene`, puis installez ses dépendances
**une seule fois** :

```bash
cd ~/outils/gene          # Windows : cd C:\outils\gene
composer install
```

Sous Windows, vous pouvez sauter cette étape : au premier lancement, `gene.cmd` et `gene.ps1` détectent l'absence des
dépendances et proposent de lancer `composer install` pour vous.

### 3.2 Les lanceurs

| Système | Terminal | Commande à taper depuis le dossier de gene |
| --- | --- | --- |
| Linux, macOS | bash, zsh… | `bin/gene` |
| Windows | Invite de commandes (cmd) | `gene` (lance `gene.cmd`) |
| Windows | PowerShell | `.\gene.ps1` |

Sous Windows, les deux lanceurs vérifient que PHP est trouvable et affichent sinon un message clair. Ils transmettent
ensuite tous vos arguments à gene, puis vous rendent son code de sortie.

### 3.3 Rendre `gene` disponible partout

**Linux** : placez un lien dans un dossier du PATH.

```bash
mkdir -p ~/.local/bin
ln -s ~/outils/gene/bin/gene ~/.local/bin/gene
gene --version
```

**Windows** : ajoutez le dossier de gene (par exemple `C:\outils\gene`) au *Path* de vos variables d'environnement,
comme pour PHP au §2.2. Dans un nouveau terminal, `gene` fonctionne alors depuis n'importe quel dossier :
- dans cmd, `gene` lance `gene.cmd` ;
- dans PowerShell, tapez `gene.cmd`, ou `gene.ps1` (voir §13 si PowerShell refuse le script).

> Dans la suite du guide, `gene` désigne la commande adaptée à votre système : `bin/gene`, `gene`, `gene.cmd` ou
> `.\gene.ps1`.

### 3.4 Vérifier l'installation : `gene doctor`

```bash
gene doctor
```

```text
gene doctor — Linux, PHP 8.5.4
==============================

 --- ------------------------------ ----------------------------------------------------
      Élément                        Détail
 --- ------------------------------ ----------------------------------------------------
  ✔   PHP ≥ 8.3                      8.5.4 (/usr/bin/php8.5)
  ✔   php.ini                        /etc/php/8.5/cli/php.ini
  ✔   extension pdo_mysql            connexion à MySQL/MariaDB
  ✔   extension mbstring             textes accentués (validation, libellés)
  ✔   extension intl                 translittération des noms accentués en identifiants
  ✔   extension zip                  téléchargement en zip depuis l'interface web
  ✔   dépendances Composer           /home/vous/outils/gene/vendor
  ✔   dossier de sortie par défaut   /home/vous/outils/gene/output
  !   profils                        /home/vous/.config/gene/profiles.json (créé au premier enregistrement)
 --- ------------------------------ ----------------------------------------------------

 [OK] Tout est prêt. Lancez « gene » pour démarrer l'assistant.
```

- `✔` : l'élément est en ordre.
- `!` : un conseil, qui ne bloque rien.
- `✖` : un problème bloquant. La colonne *Détail* indique alors la correction pour votre système : le paquet à
  installer sous Linux, la ligne à activer dans le `php.ini` affiché sous Windows.

Pour tester aussi l'accès à votre serveur :

```bash
gene doctor --connect --host=127.0.0.1 --user=dev
```

---

## 4. Premier projet avec l'assistant

Lancez simplement :

```bash
gene
```

L'assistant pose les questions dans l'ordre. **La valeur entre crochets est utilisée si vous appuyez sur Entrée.**
Ctrl+C quitte à tout moment, sans rien écrire.

### Étape 1 : connexion

```text
1. Connexion au serveur MySQL/MariaDB
-------------------------------------
 Hôte [127.0.0.1]:
 > 
 Port [3306]:
 > 
 Utilisateur [root]:
 > dev
 Mot de passe (saisie masquée, vide si aucun):
 > 
 ✔ Connecté à 127.0.0.1:3306 (8.4.13).
```

- **Hôte** : `127.0.0.1` pour un serveur sur votre machine. Sinon, le nom ou l'adresse du serveur.
- **Port** : 3306 par défaut. Il vaut souvent 3307 avec WAMP ou MAMP, quand plusieurs serveurs cohabitent.
- **Mot de passe** : rien ne s'affiche quand vous tapez, c'est normal. gene ne l'enregistre jamais.
- La connexion est testée tout de suite. En cas d'échec, l'assistant explique la cause (mot de passe refusé, serveur
  arrêté, extension manquante…) et vous laisse trois essais.

### Étape 2 : choix de la base

```text
2. Base de données
------------------
 Base à utiliser (numéro ou nom):
  [0] boutique
  [1] gestion_courrier
 > boutique
```

Tapez le numéro ou le nom. La touche Tab complète le nom. Si votre compte n'a pas le droit de lister les bases,
l'assistant demande simplement le nom.

### Étape 3 : choix des tables

```text
3. Tables de « boutique »
--------------------------
 ---------------- ---------- ----------------- -------------------------------
  Table            Colonnes   Clés étrangères   Génération
 ---------------- ---------- ----------------- -------------------------------
  client           11         2                 CRUD complet
  commande         6          1                 CRUD complet
  journal          1          0                 ignorée (pas de clé primaire)
  ligne_commande   3          1                 liste seule (clé composite)
  pays             2          0                 CRUD complet
 ---------------- ---------- ----------------- -------------------------------

 Tables à générer (« toutes », ou numéros / noms séparés par des virgules) [toutes]:
  [0] toutes
  [1] client
  [2] commande
  [3] journal
  [4] ligne_commande
  [5] pays
 > client,commande,pays
```

La colonne *Génération* annonce ce que vous obtiendrez pour chaque table. Le §11 explique pourquoi.

### Étape 4 : nom et dossier

```text
4. Application à générer
------------------------
 Nom de l'application [Boutique]:
 > 
 Dossier de sortie [/home/vous/outils/gene/output/Boutique]:
 > 
```

- **Nom** : lettres, chiffres, `_` et `-`, en commençant par une lettre. Il apparaît dans le titre et le menu de
  l'application.
- **Dossier** : par défaut `output/<Nom>` dans le dossier de gene. Un chemin relatif part du dossier courant.

Si le dossier contient déjà une application générée par gene, l'assistant l'annonce. Il demande alors s'il faut
écraser les fichiers que vous avez modifiés à la main ; la réponse par défaut est **non**.

### Étape 5 : récapitulatif et génération

```text
5. Récapitulatif
----------------
  Serveur       dev@127.0.0.1:3306
  Base          boutique
  Tables        client, commande, pays
  Application   Boutique
  Dossier       /home/vous/outils/gene/output/Boutique

 Générer maintenant ? (o/n) [o]:
 > 

 ------ ----
  créé   57
 ------ ----

 [OK] Boutique généré dans /home/vous/outils/gene/output/Boutique (3 table(s)).
```

Les questions oui/non acceptent `o`, `oui`, `y`, `yes`, `n`, `non` et `no`.

### Étape 6 : les suites

```text
 Enregistrer ces réglages comme profil (sans le mot de passe) ? (o/n) [o]:
 > 
 Nom du profil [boutique]:
 > 
 ✔ Profil « boutique » enregistré (/home/vous/.config/gene/profiles.json). Relance : gene generate --profile=boutique
 Écrire le mot de passe dans le .env de l'application ? (pratique en développement local ; jamais sur un serveur partagé) (o/n) [n]:
 > o
 ✔ Mot de passe écrit dans /home/vous/outils/gene/output/Boutique/.env
 Lancer l'application maintenant ? (o/n) [n]:
 > o

 [OK] Application disponible sur http://localhost:8080 — Ctrl+C pour arrêter.
```

1. **Profil** : il retient le serveur, le compte, la base, les tables, le nom et le dossier, mais jamais le mot de
   passe. Il sert à relancer la même génération plus tard (voir §6).
2. **Mot de passe dans `.env`** : l'application générée doit pouvoir se connecter à la base. Répondre `o` l'écrit dans
   son fichier `.env`. C'est pratique sur votre poste, mais à éviter sur une machine partagée. Avec `n`, vous le
   saisirez vous-même plus tard (voir §8).
3. **Lancement** : gene démarre le serveur web intégré de PHP sur le premier port libre à partir de 8080. Ouvrez
   l'adresse affichée dans votre navigateur. Ctrl+C arrête le serveur.

---

## 5. Générer sans questions (scripts, CI)

`gene generate` fait la même chose que l'assistant, mais tout passe par des options. C'est la forme à utiliser dans un
script, une tâche planifiée ou une intégration continue.

```bash
gene generate --database=boutique --user=dev
```

### 5.1 Options

| Option | Rôle | Défaut |
| --- | --- | --- |
| `-d`, `--database` | Base à lire | — (obligatoire, sauf profil ou `--dsn`) |
| `--host`, `--port` | Serveur | `127.0.0.1`, `3306` |
| `-u`, `--user` | Compte | `GENE_DB_USER`, sinon `root` |
| `--dsn` | DSN PDO complet, à la place de host, port et base | `GENE_DSN` |
| `-p`, `--profile` | Reprendre un profil enregistré | — |
| `-a`, `--app` | Nom de l'application | Nom de la base en PascalCase |
| `-o`, `--out` | Dossier de sortie | `output/<app>` |
| `-t`, `--tables` | Tables à inclure, séparées par des virgules | Toutes |
| `-x`, `--exclude` | Tables à exclure | — |
| `--dry-run` | Montrer ce qui serait écrit, sans rien écrire | — |
| `-f`, `--force` | Écraser aussi les fichiers modifiés à la main | — |
| `--password-stdin` | Lire le mot de passe sur l'entrée standard | — |
| `--no-password` | Compte sans mot de passe : ne rien demander | — |
| `-v` | Afficher chaque fichier et ce qui lui arrive | — |
| `-n` | Ne jamais poser de question | — |

Une option donnée explicitement l'emporte toujours sur le profil, qui l'emporte sur les variables d'environnement.

### 5.2 Le mot de passe

gene **refuse de prendre le mot de passe en argument**, car il resterait visible dans l'historique du terminal et dans
la liste des processus. Trois possibilités s'offrent à vous :

1. **La saisie masquée** : c'est le comportement par défaut si rien d'autre n'est fourni et que le terminal est
   interactif.
2. **La variable `GENE_DB_PASSWORD`** :

   ```bash
   # Linux (bash) : la variable ne vaut que pour cette commande
   GENE_DB_PASSWORD='s3cret' gene generate -d boutique -u dev
   ```

   ```powershell
   # Windows PowerShell
   $env:GENE_DB_PASSWORD = 's3cret'
   .\gene.ps1 generate -d boutique -u dev
   Remove-Item Env:GENE_DB_PASSWORD
   ```

   ```bat
   :: Windows cmd
   set GENE_DB_PASSWORD=s3cret
   gene generate -d boutique -u dev
   set GENE_DB_PASSWORD=
   ```

3. **L'entrée standard** : utile quand le mot de passe vient d'un fichier ou d'un gestionnaire de secrets.

   ```bash
   gene generate -d boutique -u dev --password-stdin < ~/.secrets/boutique.txt
   ```

   ```powershell
   Get-Content .\boutique.txt | .\gene.ps1 generate -d boutique -u dev --password-stdin
   ```

### 5.3 Exemples courants

```bash
# Simulation : liste ce qui serait créé ou mis à jour, sans rien écrire
gene generate -d boutique -u dev --dry-run

# Deux tables seulement, dans un dossier précis
gene generate -d boutique -u dev -t client,commande -o ~/sites/boutique

# Toutes les tables sauf les tables techniques
gene generate -d boutique -u dev -x journal,migrations

# Serveur distant, DSN complet
gene generate --dsn="mysql:host=db.interne;port=3306;dbname=boutique" -u lecteur

# Relancer une génération enregistrée
gene generate --profile=boutique
```

### 5.4 Dans un script ou en CI

```bash
#!/usr/bin/env bash
set -euo pipefail
export GENE_DB_PASSWORD="$BOUTIQUE_DB_PASSWORD"   # fourni par le coffre de secrets de la CI
gene generate -n -d boutique -u ci -o build/boutique
```

| Code de sortie | Signification |
| --- | --- |
| `0` | Succès |
| `1` | Échec : connexion impossible, aucune table exploitable, option invalide… |
| `2` | Usage incorrect : par exemple, l'assistant lancé avec `-n` |

---

## 6. Les profils

Un profil mémorise une génération pour la rejouer à l'identique. Il retient le serveur, le port, le compte, la base,
les tables choisies, le nom de l'application et le dossier, **jamais le mot de passe**.

| Action | Commande |
| --- | --- |
| Créer | Répondre « o » à la fin de l'assistant |
| Lister | `gene profile:list` |
| Rejouer sans questions | `gene generate --profile=boutique` |
| Rejouer en partie | `gene generate --profile=boutique -t client` (les options l'emportent) |
| Partir d'un profil dans l'assistant | `gene` propose les profils en première question |
| Supprimer | `gene profile:delete boutique` |

Emplacement du fichier :

| Système | Chemin |
| --- | --- |
| Linux, macOS | `~/.config/gene/profiles.json` (ou `$XDG_CONFIG_HOME/gene`) |
| Windows | `%APPDATA%\gene\profiles.json` |
| Partout | Le dossier indiqué par `GENE_CONFIG_DIR`, s'il est défini |

Sous Linux, le fichier n'est lisible que par vous (droits 0600).

---

## 7. L'interface web

```bash
cd ~/outils/gene                    # Windows : cd C:\outils\gene
php -S localhost:8000 -t public
```

Ouvrez <http://localhost:8000> :
1. Remplissez la connexion, puis éventuellement le nom de l'application et les tables.
2. Cliquez sur **Générer**.
3. L'application est écrite dans `output/<Nom>`. Un bouton propose de la télécharger en zip. Le fichier `.env` en est
   exclu.

C'est un **outil de développement** :
- il n'accepte que les requêtes de la machine locale ;
- il n'enregistre jamais le mot de passe ;
- `GENE_OUTPUT_DIR` change le dossier de sortie ;
- ne le publiez pas sur un serveur, et ne le placez pas derrière un proxy inverse, qui ferait passer toute requête
  pour locale. `GENE_WEB_ALLOW_REMOTE=1` lève la restriction, à vos risques.

---

## 8. Lancer et déployer l'application générée

### 8.1 Configurer la connexion : `.env`

Chaque application contient un fichier `.env` :

```ini
APP_NAME="Boutique"
APP_ENV=dev
APP_DEBUG=1
APP_PER_PAGE=20
# APP_BASE_PATH=/Boutique

DB_DSN="mysql:host=127.0.0.1;port=3306;dbname=boutique;charset=utf8mb4"
DB_USER="dev"
DB_PASSWORD=""
```

Renseignez `DB_PASSWORD` entre les guillemets, sauf si l'assistant l'a déjà fait. Le fichier `.env.example` est un
modèle sans secret, que vous pouvez versionner. **Ne versionnez pas `.env`** : il est d'ailleurs exclu par le
`.gitignore` généré.

Les variables d'environnement réelles du serveur l'emportent sur `.env`. C'est la façon propre de passer le mot de
passe en production.

### 8.2 En développement : le serveur intégré de PHP

```bash
php -S localhost:8080 -t output/Boutique/public
```

La commande est la même sous Windows : `php -S localhost:8080 -t C:\outils\gene\output\Boutique\public`. Ouvrez ensuite
<http://localhost:8080>.

### 8.3 Avec Apache sous Linux

Le plus propre est de faire pointer le site sur le dossier `public/` :

```apache
<VirtualHost *:80>
    ServerName boutique.local
    DocumentRoot /var/www/boutique/public
    <Directory /var/www/boutique/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

```bash
sudo a2enmod rewrite && sudo systemctl reload apache2
```

Vous pouvez aussi déposer l'application dans un sous-dossier, par exemple `/var/www/html/boutique`. Le `.htaccess` à sa
racine renvoie tout vers `public/`, ce qui rend `src/`, `config/` et `.env` inaccessibles. Il faut que `mod_rewrite`
soit actif et que `AllowOverride All` soit autorisé.

### 8.4 Avec XAMPP ou WAMP sous Windows

1. Copiez le dossier généré dans `C:\xampp\htdocs\Boutique` (avec WAMP : `C:\wamp64\www\Boutique`).
2. Avec WAMP, activez `rewrite_module` : icône WAMP, puis *Apache*, puis *Modules Apache*. Il est actif par défaut
   dans XAMPP.
3. Vérifiez que le PHP utilisé par Apache est en 8.3 ou plus et que `pdo_mysql` y est activé.
4. Ouvrez <http://localhost/Boutique/>.

Si les liens de l'application sont faux (proxy, alias particulier), fixez le préfixe dans `.env` :
`APP_BASE_PATH=/Boutique`.

### 8.5 Avant une mise en production

- `APP_ENV=prod` et `APP_DEBUG=0` : avec le débogage actif, une erreur afficherait le détail technique.
- Le mot de passe passe par une variable d'environnement du serveur plutôt que par `.env`.
- Le compte MySQL n'a que les droits nécessaires : `SELECT`, `INSERT`, `UPDATE`, `DELETE` sur la base.
- **Ajoutez une protection d'accès** : gene ne génère pas d'authentification (voir §12).
- Utilisez HTTPS : les cookies de session passent alors automatiquement en mode `Secure`.

---

## 9. Comprendre le code généré

```text
Boutique/
├── public/                 seule partie exposée au web
│   ├── index.php           contrôleur frontal : toutes les requêtes passent par lui
│   └── assets/             Bootstrap 5.3, app.css, app.js
├── config/
│   ├── routes.php          routes générées (ne pas modifier)
│   ├── routes.custom.php   VOS routes
│   ├── menu.php            menu généré (ne pas modifier)
│   ├── menu.custom.php     VOS modifications du menu
│   └── config.php          lecture de l'environnement
├── src/
│   ├── Entity/Client.php           une classe par table
│   ├── Repository/ClientRepository.php
│   ├── Controller/ClientController.php
│   └── Framework/                  mini-framework (routeur, vues, sessions, CSRF, validation)
├── templates/
│   ├── layout.php                  mise en page commune
│   └── client/index.php, show.php, form.php
├── .env, .env.example
└── .gene.json              empreintes des fichiers écrits par gene (ne pas modifier)
```

### L'entité (`src/Entity/Client.php`)

L'entité est une classe à propriétés typées, une par colonne. Elle contient aussi la constante **`FIELDS`**, qui décrit
chaque champ saisissable : libellé, type, caractère obligatoire, longueur maximale, bornes, valeurs d'un `enum`, valeur
par défaut. Cette constante sert à la validation des formulaires.

```php
public const FIELDS = [
    'nom' => ['label' => 'Nom', 'type' => 'string', 'required' => true, 'nullable' => false, 'maxLength' => 100],
    // …
];
public ?string $nom = null;
```

Pour changer un libellé ou rendre un champ obligatoire, modifiez `FIELDS`.

### Le dépôt (`src/Repository/ClientRepository.php`)

Il ne contient que des constantes : la table, la clé primaire, les colonnes lues et écrites, les relations, les
colonnes cherchées. Toutes les requêtes sont dans `src/Framework/Repository.php` : `find`, `paginate`, `insert`,
`update`, `delete`, `options`. Ajoutez vos propres requêtes dans le dépôt de la table :

```php
/** @return list<Entity\Client> clients actifs d'un pays */
public function actifsDuPays(string $code): array
{
    return $this->findBySql(
        'SELECT * FROM `client` WHERE actif = 1 AND pays_code = :code ORDER BY nom',
        ['code' => $code],
    );
}
```

`findBySql()` exécute une requête préparée et renvoie des entités. Passez toujours les valeurs en paramètres
(`:code`), jamais par concaténation. Pour un résultat qui n'est pas une entité (un total, une statistique),
`$this->run($sql, $params)` renvoie le `PDOStatement`.

### Le contrôleur (`src/Controller/ClientController.php`)

Il contient une méthode par action :

| Action | Route |
| --- | --- |
| `index` | `GET /client` |
| `show` | `GET /client/{id}` |
| `create` | `GET /client/new` |
| `store` | `POST /client` |
| `edit` | `GET /client/{id}/edit` |
| `update` | `POST /client/{id}` |
| `destroy` | `POST /client/{id}/delete` |

Après un enregistrement réussi, l'utilisateur est redirigé et un message s'affiche. En cas d'erreur, le formulaire est
réaffiché avec la saisie et les messages.

### Les vues (`templates/client/*.php`)

Ce sont des gabarits PHP ordinaires. Dans un gabarit, `$this` est la vue :

| Méthode | Rôle |
| --- | --- |
| `$this->e($valeur)` | Échappe une valeur. **À utiliser pour toute donnée affichée.** |
| `$this->display($valeur, 'date')` | Formate une valeur (Oui/Non, dates en `jj/mm/aaaa`…) et l'échappe |
| `$this->url('/client')` | Construit un lien qui tient compte du sous-dossier |
| `$this->csrfField()` | Champ caché anti-CSRF, **obligatoire** dans tout formulaire POST |

---

## 10. Personnaliser et régénérer

### 10.1 La règle d'or

gene note dans `.gene.json` l'empreinte de chaque fichier qu'il écrit. À la génération suivante :

| État du fichier | Ce que fait gene |
| --- | --- |
| Absent | Il le crée |
| Identique à ce que gene avait écrit | Il le met à jour |
| Modifié par vous | Il le **conserve** et le signale |
| Avec `--force` | Il écrase tout, y compris vos modifications |

Vous pouvez donc modifier librement un contrôleur, une vue ou une entité : vos changements survivent aux
régénérations. La contrepartie : un fichier que vous avez modifié ne reçoit plus les améliorations des générations
suivantes. Lancez `gene generate --dry-run -v` pour voir lesquels sont concernés.

### 10.2 Ajouter des routes : `config/routes.custom.php`

```php
return static function (Router $router): void {
    $router->get('/rapport', [App\Controller\RapportController::class, 'index']);
};
```

Ces routes sont lues avant les routes générées : elles peuvent donc remplacer une URL générée. Ne modifiez pas
`config/routes.php`. Il est régénéré à chaque fois et doit recevoir les nouvelles tables.

### 10.3 Modifier le menu : `config/menu.custom.php`

```php
return static function (array $menu): array {
    // renommer une entrée
    $menu = array_map(fn ($i) => $i['path'] === '/client' ? ['label' => 'Clients', 'path' => '/client'] : $i, $menu);
    // masquer une entrée et en ajouter une
    $menu = array_values(array_filter($menu, fn ($i) => $i['path'] !== '/pays'));
    $menu[] = ['label' => 'Rapport', 'path' => '/rapport'];

    return $menu;
};
```

### 10.4 Après une évolution du schéma

1. Modifiez votre base : nouvelle table, nouvelle colonne…
2. Relancez la même génération, par exemple `gene generate --profile=boutique`, ou l'assistant en partant du profil.
3. Lisez le résumé : *créé*, *mis à jour*, *inchangé*, *conservé (modifié à la main)*.
4. Pour un fichier conservé, reportez à la main la nouvelle colonne. Vous pouvez aussi le supprimer pour que gene le
   recrée, ou relancer avec `--force` si vos modifications ne comptent pas.

### 10.5 Changer l'apparence

- **Styles** : `public/assets/css/app.css`.
- **Comportements** : `public/assets/js/app.js`. Exemple fourni : la confirmation avant suppression, avec l'attribut
  `data-confirm`.
- **Structure commune** : `templates/layout.php`.

La politique de sécurité du navigateur (CSP) interdit les scripts et les styles écrits directement dans le HTML.
Placez-les toujours dans ces fichiers.

---

## 11. Comment gene interprète votre schéma

| Dans la base | Dans l'application |
| --- | --- |
| Table avec clé primaire simple | CRUD complet |
| Clé primaire composite | Liste seule, sans fiche ni formulaire |
| Table sans clé primaire | Ignorée, avec un avertissement |
| Clé étrangère vers une table générée | Liste déroulante dans le formulaire ; libellé et lien dans la liste et la fiche |
| Clé étrangère vers une table non générée | Champ saisi librement, avec un avertissement |
| `tinyint(1)`, `bit(1)` | Case à cocher |
| `tinyint`, `smallint`, `int`, `bigint` | Champ numérique borné selon le type (300 est refusé dans un `TINYINT`) |
| `decimal`, `float`, `double` | Saisie décimale (la virgule est acceptée), contrôle de la précision |
| `char`, `varchar` | Champ texte avec longueur maximale ; cherché par la recherche de la liste |
| `enum` | Liste déroulante des valeurs ; cherché par la recherche |
| `text`, `json` | Zone de texte (taille contrôlée ; JSON validé) |
| `date`, `datetime`, `timestamp`, `time`, `year` | Champs natifs du navigateur ; affichage en `jj/mm/aaaa` |
| `blob`, géométries | Ignorés dans les formulaires et les listes |
| `AUTO_INCREMENT`, colonne calculée | Non saisissables |
| `NOT NULL` sans valeur par défaut | Champ obligatoire |
| `NOT NULL` avec valeur par défaut, champ vidé | La valeur par défaut est réappliquée |
| Défaut calculé (`CURRENT_TIMESTAMP`) laissé vide | La base le remplit à la création ; la valeur est gardée à la modification |

Le **libellé** affiché pour une clé étrangère est une colonne de la table liée. gene prend en priorité une colonne nommée
`libelle`, `label`, `nom`, `name`, `titre`, `title`, `intitule`, `designation` ou `code`. À défaut, il prend la première
colonne de texte.

Les **noms** sont convertis :
- `gestion_courrier` devient la classe `GestionCourrier` et l'URL `/gestion-courrier` ;
- `date_naissance` devient la propriété `$dateNaissance` et le libellé « Date naissance » ;
- `client_id` devient le libellé « Client ».

Les accents sont translittérés dans les noms de code. Un nom réservé de PHP reçoit un suffixe : `list` devient
`ListItem`.

Pour voir précisément ce que gene comprend de votre base, avant toute génération :

```bash
gene schema -d boutique -u dev
```

---

## 12. Sécurité

**Ce qui est intégré** dans chaque application générée :
- **Injection SQL** : toutes les requêtes sont préparées. Les noms de tables et de colonnes viennent du code généré,
  jamais de la saisie.
- **CSRF** : un jeton est vérifié pour toute requête POST (création, modification, suppression).
- **XSS** : tout est échappé à l'affichage. Une politique CSP stricte interdit les scripts en ligne.
- **Sessions** : les cookies sont `HttpOnly` et `SameSite=Lax` (et `Secure` en HTTPS), en mode strict.
- **Navigation** : en-têtes anti-clickjacking (`X-Frame-Options`, `frame-ancestors`) et `nosniff`.
- **Erreurs** : en production (`APP_DEBUG=0`), elles ne révèlent rien.

**Ce qui reste à votre charge** :
- **L'authentification.** L'application générée est ouverte à quiconque peut l'atteindre. Avant de l'exposer,
  protégez-la : authentification HTTP d'Apache, VPN, ou votre propre système de connexion via `routes.custom.php` et
  un contrôleur.
- Les droits du compte MySQL et HTTPS (voir §8.5).

**gene lui-même** :
- il ne modifie jamais votre base ;
- il n'écrit jamais votre mot de passe ailleurs que dans `.env`, et seulement si vous l'acceptez ;
- il refuse d'écrire à travers un lien symbolique ou en dehors du dossier de sortie.

---

## 13. Dépannage

| Symptôme | Cause et solution |
| --- | --- |
| `could not find driver` | L'extension `pdo_mysql` n'est pas active. Linux : `sudo apt install php-mysql` (ou `php8.3-mysql`). Windows : dans le `php.ini` indiqué par `gene doctor`, retirez le `;` devant `extension=pdo_mysql`, puis rouvrez le terminal. |
| `[1045] Access denied` | Utilisateur ou mot de passe incorrect, ou compte non autorisé depuis cet hôte. Essayez `127.0.0.1` au lieu de `localhost`, ou l'inverse. |
| `[2002]` ou `[2003]`, serveur injoignable | MySQL est arrêté, ou l'hôte ou le port est faux. Linux : `sudo systemctl status mysql`. Windows : services Windows, panneau XAMPP ou WAMP. Vérifiez le port (3306, 3307…). |
| `[1049] Unknown database` | Faute de frappe dans le nom de la base. L'assistant liste les bases disponibles. |
| « 'php' n'est pas reconnu en tant que commande interne » (Windows) | Le dossier de PHP n'est pas dans le PATH (§2.2, étape 5). Rouvrez le terminal après la modification. |
| PowerShell refuse `gene.ps1` (« l'exécution de scripts est désactivée ») | Débloquez le fichier une fois : `Unblock-File .\gene.ps1`. Ou, pour la session : `Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass`. Ou utilisez `gene.cmd`. |
| `Dépendances absentes` | Lancez `composer install` dans le dossier de gene. Sous Windows, les lanceurs le proposent. |
| Une table n'apparaît pas | Elle n'a pas de clé primaire (§11). Ajoutez-en une, par exemple une colonne `id INT AUTO_INCREMENT PRIMARY KEY`. |
| Une clé étrangère est un simple champ texte | La table liée n'est pas générée (exclue ou sans clé primaire), ou la clé ne vise pas sa clé primaire. `gene schema` affiche l'avertissement. |
| Pages ou liens en erreur 404 sous Apache | `mod_rewrite` est inactif ou `AllowOverride` est interdit (§8.3, §8.4). Si les liens restent faux, fixez `APP_BASE_PATH` dans `.env`. |
| « Le formulaire a expiré » (erreur 419) | La session a été perdue (redémarrage, cookies bloqués) : rechargez la page et recommencez. |
| « Une erreur interne est survenue » | Mettez `APP_DEBUG=1` dans `.env` (développement uniquement) pour voir le détail, ou consultez le journal d'erreurs de PHP. Cause fréquente : `DB_PASSWORD` est vide ou faux dans `.env`. |
| « Fichiers modifiés à la main, conservés » | C'est voulu (§10.1). `--force` les écrase ; supprimer un fichier le fait recréer. |
| Accents illisibles dans la console Windows | Utilisez Windows Terminal, ou tapez `chcp 65001` avant de lancer gene. |

Lancez `gene doctor` au moindre doute : il vérifie l'essentiel et donne la correction adaptée à votre système.

---

## 14. Référence des commandes

| Commande | Rôle |
| --- | --- |
| `gene`, `gene new` | Assistant pas à pas |
| `gene generate [options]` | Génération sans questions (§5) |
| `gene schema [options]` | Affiche le schéma tel que gene le comprend : types, champs obligatoires, défauts, relations |
| `gene doctor [--connect …]` | Vérifie l'installation et, en option, la connexion |
| `gene profile:list` | Liste les profils |
| `gene profile:delete <nom>` | Supprime un profil |
| `gene list` | Liste toutes les commandes |
| `gene <commande> --help` | Aide détaillée d'une commande |

Variables d'environnement reconnues :

| Variable | Rôle |
| --- | --- |
| `GENE_DB_PASSWORD` | Mot de passe de la base, pour éviter la saisie |
| `GENE_DB_USER` | Utilisateur par défaut |
| `GENE_DSN` | DSN complet par défaut |
| `GENE_CONFIG_DIR` | Dossier des profils |
| `GENE_OUTPUT_DIR` | Dossier de sortie de l'interface web |
| `GENE_WEB_ALLOW_REMOTE=1` | Ouvre l'interface web aux autres machines (déconseillé) |
