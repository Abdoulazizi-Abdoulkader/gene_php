-- Schéma d'intégration : même contenu que SchemaFixture, compatible MySQL 8 et MariaDB 10.6+.
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS journal, ligne_commande, commande, client, pays;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE pays (
    code CHAR(2) NOT NULL PRIMARY KEY,
    libelle VARCHAR(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE client (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(190) NULL UNIQUE,
    date_naissance DATE NULL,
    actif TINYINT(1) NOT NULL DEFAULT 1,
    solde DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    statut ENUM('prospect','client','ancien') NOT NULL DEFAULT 'prospect',
    pays_code CHAR(2) NULL,
    parrain_id INT UNSIGNED NULL,
    notes TEXT NULL,
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_client_pays FOREIGN KEY (pays_code) REFERENCES pays (code),
    CONSTRAINT fk_client_parrain FOREIGN KEY (parrain_id) REFERENCES client (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE commande (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    client_id INT UNSIGNED NOT NULL,
    passee_le DATETIME NOT NULL,
    heure_livraison TIME NULL,
    montant_ttc DOUBLE NOT NULL DEFAULT 0,
    `pièce_jointe` BLOB NULL,
    CONSTRAINT fk_commande_client FOREIGN KEY (client_id) REFERENCES client (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ligne_commande (
    commande_id INT NOT NULL,
    numero SMALLINT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    PRIMARY KEY (commande_id, numero),
    CONSTRAINT fk_ligne_commande FOREIGN KEY (commande_id) REFERENCES commande (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE journal (
    message TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO pays (code, libelle) VALUES ('FR', 'France'), ('NE', 'Niger'), ('SN', 'Sénégal');
