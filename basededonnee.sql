use heryyoh;
SELECT * FROM heryyoh.compte;
/* Suppression de la table */
drop table emprunt;
drop table compte;
drop table annonce;

/* Test Commande */
/*SELECT email,mdp FROM compte WHERE email = 'yohanh2003@live.fr' AND mdp = '';*/

/* Création de la table des comptes */
CREATE table compte(
  id int NOT NULL AUTO_INCREMENT,
  psswd varchar(255) NOT NULL,
  prenom varchar(45) NOT NULL,
  nom varchar(45) NOT NULL,
  birth varchar(10) NOT NULL,
  email varchar(45) NOT NULL,
  rolec varchar(45) NOT NULL default 'user',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/* Création de la table des annonces */
CREATE table annonce(
  id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  titre varchar(255) NOT NULL,
  picture varchar(255) NOT NULL,
  auteur varchar(255) NOT NULL,
  editeur varchar(255) NOT NULL,
  dateparution varchar(45) NOT NULL,
  genre varchar(255) NOT NULL,
  stock int DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Création de la table des emprunts */
CREATE table emprunt(
  idloan int NOT NULL AUTO_INCREMENT,
  idbook int NOT NULL,
  iduser int NOT NULL,
  datedebut varchar(45) NOT NULL,
  datefin varchar(45) NOT NULL,
  nb_prolongations INT DEFAULT 0,
  statut_loan varchar(45) DEFAULT 'Emprunt',
  PRIMARY KEY (idloan),
  FOREIGN KEY(idbook) REFERENCES annonce (id),
  FOREIGN KEY(iduser) REFERENCES compte (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Insertion de livre rapide */

-- Livres
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Le Seigneur des Anneaux : La Communaut� de l\'Anneau', 'jrr_tolkien.jpg', 'J.R.R. Tolkien', 'Christian Bourgois', '1954-07-29', 'Roman de fantasy �pique', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('1984', 'imageanno/1984.jpg', 'George Orwell', 'Secker & Warburg', '1949-06-08', 'Roman dystopique', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Orgueil et Pr�jug�s', 'imageanno/orgeuil_et.jpg', 'Jane Austen', 'Egmont UK Ltd', '1813-01-28', 'Roman classique', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Le Petit Prince', 'imageanno/le_petit_prince.jpg', 'Antoine de Saint-Exup�ry', 'Gallimard', '1943-04-06', 'Conte philosophique', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Les Mis�rables', 'imageanno/les_miserables.jpg', 'Victor Hugo', 'Charles Laffitte', '1862-04-03', 'Roman historique', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Les Fleurs du Mal', 'imageanno/les_fleurs_du_mal.jpg', 'Charles Baudelaire', 'Poulet-Malassis et de Broise', '1857-06-25', 'Recueil de po�sie', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Le Vieil Homme et la Mer', 'imageanno/Le-vieil-homme-et-la-mer.jpg', 'Ernest Hemingway', 'Charles Scribner\'s Sons', '1952-09-08', 'Roman court', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Le Nom de la Rose', 'imageanno/le_nom_de_la_rose.jpg', 'Umberto Eco', 'Bompiani', '1980-01-01', 'Roman historique et philosophique', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Les Enfants du Capitaine Grant', 'imageanno/The_Children_of_Captain_Grant.jpg', 'Jules Verne', 'Pierre-Jules Hetzel', '1867-01-01', 'Roman d\'aventure', 10);
-- Manga
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('One Piece', 'imageanno/one_piecet1.jpg', 'Eiichiro Oda', 'Shueisha', '1997-07-22', 'Manga Shonen', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Naruto', 'imageanno/narutot1.jpg', 'Masashi Kishimoto', 'Shueisha', '1999-09-21', 'Manga Shonen', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Dragon Ball', 'imageanno/dragon_ballt1.jpg', 'Akira Toriyama', 'Shueisha', '1984-11-20', 'Manga Shonen', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Death Note', 'imageanno/death_notet1.jpg', 'Tsugumi Oba', 'Shueisha', '2003-12-01', 'Manga Shonen', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Attack on Titan', 'imageanno/attack_on_titant1.jpg', 'Hajime Isayama', 'Kodansha', '2009-09-09', 'Manga Shonen', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('My Hero Academia', 'my_hero_academia.jpg', 'Kohei Horikoshi', 'Shueisha', '2014-07-07', 'Manga Shonen', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Demon Slayer: Kimetsu no Yaiba', 'demon_slayer.jpg', 'Koyoharu Gotouge', 'Shueisha', '2016-02-15', 'Manga Shonen', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Fullmetal Alchemist', 'fullmetal_alchemist.jpg', 'Hiromu Arakawa', 'Square Enix', '2001-07-12', 'Manga Shonen', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Tokyo Ghoul', 'imageanno/tg_t1.jpg', 'Sui Ishida', 'Shueisha', '2011-09-08', 'Manga Shonen', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('One Punch Man', 'one_punch_man.jpg', 'One', 'Shueisha', '2009-06-14', 'Manga Shonen', 10);
-- Livres du genre "Six of Crows"
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Six of Crows', 'imageanno/six_of_crows.jpg', 'Leigh Bardugo', 'Henry Holt and Company', '2015-09-29', 'Fantasy YA', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Crooked Kingdom', 'imageanno/crooked-kingdom.jpg', 'Leigh Bardugo', 'Henry Holt and Company', '2016-09-27', 'Fantasy YA', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('The Lies of Locke Lamora', 'imageanno/the_lies_of.jpg', 'Scott Lynch', 'Gollancz', '2006-06-27', 'Fantasy', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('The Name of the Wind', 'imageanno/the_name_of_the_wind.jpg', 'Patrick Rothfuss', 'DAW Books', '2007-03-27', 'Fantasy', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Mistborn: The Final Empire', 'imageanno/m_the_final_empire.jpg', 'Brandon Sanderson', 'Tor Books', '2006-07-17', 'Fantasy', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Foundryside', 'foundryside.jpg', 'Robert Jackson Bennett', 'Crown Publishing Group', '2018-08-21', 'Fantasy', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('The Lies of Locke Lamora', 'imageanno/the_lies_of.jpg', 'Scott Lynch', 'Gollancz', '2006-06-27', 'Fantasy', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Red Seas Under Red Skies', 'imageanno/red_seas.jpg', 'Scott Lynch', 'Gollancz', '2007-06-20', 'Fantasy', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('The Republic of Thieves', 'imageanno/the_rep.jpg', 'Scott Lynch', 'Gollancz', '2013-10-08', 'Fantasy', 10);
INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES ('Foundryside', 'foundryside.jpg', 'Robert Jackson Bennett', 'Crown Publishing Group', '2018-08-21', 'Fantasy', 10);


