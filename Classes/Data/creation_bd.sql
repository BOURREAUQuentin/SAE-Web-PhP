-- Création de la database
CREATE DATABASE IF NOT EXISTS SAE-WEB-PHP;

-- Destruction des tables
DROP TABLE IF EXISTS LIKER;
DROP TABLE IF EXISTS CONTENIR;
DROP TABLE IF EXISTS PLAYLIST;
DROP TABLE IF EXISTS NOTER;
DROP TABLE IF EXISTS REALISER_PAR;
DROP TABLE IF EXISTS APPARTENIR;
DROP TABLE IF EXISTS FAIRE_PARTIE;
DROP TABLE IF EXISTS GENRE;
DROP TABLE IF EXISTS UTILISATEUR;
DROP TABLE IF EXISTS COMPOSER;
DROP TABLE IF EXISTS MUSIQUE;
DROP TABLE IF EXISTS ALBUM;
DROP TABLE IF EXISTS ARTISTE;
DROP TABLE IF EXISTS IMAGE;

-- Création de la table IMAGE
CREATE TABLE IF NOT EXISTS IMAGE (
   id_image INT PRIMARY KEY,
   image VARCHAR(255) NOT NULL
);


-- Création de la table ARTISTE
CREATE TABLE IF NOT EXISTS ARTISTE (
   id_artiste INT PRIMARY KEY,
   nom_artiste VARCHAR(255) NOT NULL,
   id_image INT,
   FOREIGN KEY (id_image) REFERENCES IMAGE(id_image)
);


-- Création de la table ALBUM
CREATE TABLE IF NOT EXISTS ALBUM (
   id_album INT PRIMARY KEY,
   titre VARCHAR(255) NOT NULL,
   annee_sortie INT,
   id_image INT,
   FOREIGN KEY (id_image) REFERENCES IMAGE(id_image)
);


-- Création de la table MUSIQUE
CREATE TABLE IF NOT EXISTS MUSIQUE (
   id_musique INT PRIMARY KEY,
   nom_musique VARCHAR(255) NOT NULL,
   duree_musique VARCHAR(8) NOT NULL, -- champ VARCHAR pour stocker une durée au format HH:MM:SS
   id_album INT UNIQUE,
   FOREIGN KEY (id_album) REFERENCES ALBUM(id_album)
);


-- Création de la table COMPOSER
CREATE TABLE IF NOT EXISTS COMPOSER (
   id_musique INT,
   id_album INT,
   PRIMARY KEY (id_musique, id_album),
   FOREIGN KEY (id_musique) REFERENCES MUSIQUE(id_musique),
   FOREIGN KEY (id_album) REFERENCES ALBUM(id_album)
);


-- Création de la table UTILISATEUR
CREATE TABLE IF NOT EXISTS UTILISATEUR (
   id_utilisateur INT PRIMARY KEY,
   nom_utilisateur VARCHAR(255) UNIQUE NOT NULL,
   mail_utilisateur VARCHAR(255) UNIQUE NOT NULL,
   mdp VARCHAR(255) NOT NULL
);


-- Création de la table GENRE
CREATE TABLE IF NOT EXISTS GENRE (
   id_genre INT PRIMARY KEY,
   nom_genre VARCHAR(255) NOT NULL,
   id_image INT,
   FOREIGN KEY (id_image) REFERENCES IMAGE(id_image)
);


-- Création de la table FAIRE_PARTIE (album vers genre)
CREATE TABLE IF NOT EXISTS FAIRE_PARTIE (
   id_album INT,
   id_genre INT,
   PRIMARY KEY (id_album, id_genre),
   FOREIGN KEY (id_album) REFERENCES ALBUM(id_album),
   FOREIGN KEY (id_genre) REFERENCES GENRE(id_genre)
);

-- Création de la table APPARTENIR (artiste vers genre)
CREATE TABLE IF NOT EXISTS APPARTENIR (
   id_artiste INT,
   id_genre INT,
   PRIMARY KEY (id_artiste, id_genre),
   FOREIGN KEY (id_artiste) REFERENCES ARTISTE(id_artiste),
   FOREIGN KEY (id_genre) REFERENCES GENRE(id_genre)
);

-- Création de la table REALISER_PAR (album vers artiste)
CREATE TABLE IF NOT EXISTS REALISER_PAR (
   id_album INT,
   id_artiste INT,
   PRIMARY KEY (id_album, id_artiste),
   FOREIGN KEY (id_album) REFERENCES ALBUM(id_album),
   FOREIGN KEY (id_artiste) REFERENCES ARTISTE(id_artiste)
);


-- Création de la table NOTER
CREATE TABLE IF NOT EXISTS NOTER (
   id_album INT,
   id_utilisateur INT,
   note INT NOT NULL,
   PRIMARY KEY (id_album, id_utilisateur),
   FOREIGN KEY (id_album) REFERENCES ALBUM(id_album),
   FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
);


-- Création de la table PLAYLIST
CREATE TABLE IF NOT EXISTS PLAYLIST (
   id_playlist INT PRIMARY KEY,
   nom_playlist VARCHAR(255) NOT NULL,
   id_image INT,
   id_utilisateur INT,
   FOREIGN KEY (id_image) REFERENCES IMAGE(id_image),
   FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
);


-- Création de la table CONTENIR
CREATE TABLE IF NOT EXISTS CONTENIR (
   id_musique INT,
   id_playlist INT,
   PRIMARY KEY (id_musique, id_playlist),
   FOREIGN KEY (id_musique) REFERENCES MUSIQUE(id_musique),
   FOREIGN KEY (id_playlist) REFERENCES PLAYLIST(id_playlist)
);


-- Création de la table LIKER
CREATE TABLE IF NOT EXISTS LIKER (
   id_musique INT,
   id_utilisateur INT,
   PRIMARY KEY (id_musique, id_utilisateur),
   FOREIGN KEY (id_musique) REFERENCES MUSIQUE(id_musique),
   FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
);
