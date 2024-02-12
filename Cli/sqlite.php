<?php

define('SQLITE_DB', __DIR__.'/../Data/sae_php.db');

$pdo = new PDO('sqlite:' . SQLITE_DB);

switch ($argv[2]) {
    case 'db':
        echo '→ create database "sae_php.db"' . PHP_EOL;
        shell_exec('sqlite3 ' . SQLITE_DB);
        break;

    case 'create':
        echo '→ create tables' . PHP_EOL;
        $query =<<<EOF
            CREATE TABLE IF NOT EXISTS IMAGE (
                id_image INT PRIMARY KEY,
                image VARCHAR(255) NOT NULL UNIQUE
            );
            CREATE TABLE IF NOT EXISTS ARTISTE (
                id_artiste INT PRIMARY KEY,
                nom_artiste VARCHAR(255) NOT NULL,
                id_image INT,
                FOREIGN KEY (id_image) REFERENCES IMAGE(id_image)
            );
            CREATE TABLE IF NOT EXISTS ALBUM (
                id_album INT PRIMARY KEY,
                titre VARCHAR(255) NOT NULL,
                annee_sortie INT,
                id_image INT,
                FOREIGN KEY (id_image) REFERENCES IMAGE(id_image)
            );
            CREATE TABLE IF NOT EXISTS MUSIQUE (
                id_musique INT PRIMARY KEY,
                nom_musique VARCHAR(255) NOT NULL,
                duree_musique TIME NOT NULL,
                son_musique VARCHAR(255) NOT NULL,
                nb_streams INT NOT NULL CHECK (nb_streams >= 0),
                id_album INT,
                FOREIGN KEY (id_album) REFERENCES ALBUM(id_album)
            );
            CREATE TABLE IF NOT EXISTS UTILISATEUR (
                id_utilisateur INT PRIMARY KEY,
                nom_utilisateur VARCHAR(255) UNIQUE NOT NULL,
                mail_utilisateur VARCHAR(255) UNIQUE NOT NULL,
                mdp VARCHAR(255) NOT NULL,
                admin VARCHAR(1) DEFAULT 'N'
            );
            CREATE TABLE IF NOT EXISTS GENRE (
                id_genre INT PRIMARY KEY,
                nom_genre VARCHAR(255) NOT NULL,
                id_image INT,
                FOREIGN KEY (id_image) REFERENCES IMAGE(id_image)
            );
            CREATE TABLE IF NOT EXISTS FAIRE_PARTIE (
                id_album INT,
                id_genre INT,
                PRIMARY KEY (id_album, id_genre),
                FOREIGN KEY (id_album) REFERENCES ALBUM(id_album),
                FOREIGN KEY (id_genre) REFERENCES GENRE(id_genre)
            );
            CREATE TABLE IF NOT EXISTS APPARTENIR (
                id_artiste INT,
                id_genre INT,
                PRIMARY KEY (id_artiste, id_genre),
                FOREIGN KEY (id_artiste) REFERENCES ARTISTE(id_artiste),
                FOREIGN KEY (id_genre) REFERENCES GENRE(id_genre)
            );
            CREATE TABLE IF NOT EXISTS REALISER_PAR (
                id_album INT,
                id_artiste INT,
                PRIMARY KEY (id_album, id_artiste),
                FOREIGN KEY (id_album) REFERENCES ALBUM(id_album),
                FOREIGN KEY (id_artiste) REFERENCES ARTISTE(id_artiste)
            );
            CREATE TABLE IF NOT EXISTS NOTER (
                id_album INT,
                id_utilisateur INT,
                note INT NOT NULL,
                PRIMARY KEY (id_album, id_utilisateur),
                FOREIGN KEY (id_album) REFERENCES ALBUM(id_album),
                FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
            );
            CREATE TABLE IF NOT EXISTS PLAYLIST (
                id_playlist INT PRIMARY KEY,
                nom_playlist VARCHAR(255) NOT NULL,
                id_image INT,
                id_utilisateur INT,
                FOREIGN KEY (id_image) REFERENCES IMAGE(id_image),
                FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
            );
            CREATE TABLE IF NOT EXISTS CONTENIR (
                id_musique INT,
                id_playlist INT,
                PRIMARY KEY (id_musique, id_playlist),
                FOREIGN KEY (id_musique) REFERENCES MUSIQUE(id_musique),
                FOREIGN KEY (id_playlist) REFERENCES PLAYLIST(id_playlist)
            );
            CREATE TABLE IF NOT EXISTS LIKER (
                id_musique INT,
                id_utilisateur INT,
                PRIMARY KEY (id_musique, id_utilisateur),
                FOREIGN KEY (id_musique) REFERENCES MUSIQUE(id_musique),
                FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
            );
        EOF;
        try {
            $pdo->exec($query);
        }
        catch (PDOException $e) {
            var_dump($e->getMessage());
        }
        break;

    case 'delete':
        echo '→ delete table tables' . PHP_EOL;
        $query =<<<EOF
            DROP TABLE IF EXISTS LIKER;
            DROP TABLE IF EXISTS CONTENIR;
            DROP TABLE IF EXISTS PLAYLIST;
            DROP TABLE IF EXISTS NOTER;
            DROP TABLE IF EXISTS REALISER_PAR;
            DROP TABLE IF EXISTS APPARTENIR;
            DROP TABLE IF EXISTS FAIRE_PARTIE;
            DROP TABLE IF EXISTS GENRE;
            DROP TABLE IF EXISTS UTILISATEUR;
            DROP TABLE IF EXISTS MUSIQUE;
            DROP TABLE IF EXISTS ALBUM;
            DROP TABLE IF EXISTS ARTISTE;
            DROP TABLE IF EXISTS IMAGE;
        EOF;
        try {
            $pdo->exec($query);
        }
        catch (PDOException $e) {
            var_dump($e->getMessage());
        }
        break;

    case 'load':
        echo '→ load data tables' . PHP_EOL;

        $query =<<<EOF
            insert into IMAGE (id_image, image) VALUES
            (1, "il-le-fallait.jpg"),
            (2, "adc.jpg"),
            (3, "fave.jpg"),
            (4, "freeze-corleone.jpg"),
            (5, "rap.jpg"),
            (6, "rock.jpg"),
            (7, "jazz.jpg"),
            (8, "quentin-576-Chill-rap.jpg"),
            (9, "quentin-697-Hard-rap.jpeg"),
            (10, "baiser.jpg"),
            (11, "dire-je-taime.jpg"),
            (12, "sincerement.jpg"),
            (13, "f4.jpg"),
            (14, "paradise.jpg"),
            (15, "aujourdhui.jpg"),
            (16, "eternal-youth.jpg"),
            (17, "hiver-a-paris.jpeg"),
            (18, "ipseite.jpg"),
            (19, "kmt.jpg"),
            (20, "lithopedion.jpg"),
            (21, "novae.jpg"),
            (22, "stamina-memento.png"),
            (23, "elle-and-louis.jpg"),
            (24, "what-d-i-say.jpg"),
            (25, "forgotten.jpg"),
            (26, "psychx.jpeg"),
            (27, "bad.jpg"),
            (28, "future-nostalgia.jpg"),
            (29, "thriller.jpg"),
            (30, "x.jpg"),
            (31, "back-in-black.png"),
            (32, "wallace-clever.jpg"),
            (33, "ben-plg.jpg"),
            (34, "hamza.jpg"),
            (35, "4am-liam.jpg"),
            (36, "yvnnis.jpg"),
            (37, "dinos.jpg"),
            (38, "damso.jpg"),
            (39, "gazo.jpg"),
            (40, "louis-armstrong.jpg"),
            (41, "ray-charles.jpg"),
            (42, "lxst-cxntury.jpg"),
            (43, "kordhell.jpg"),
            (44, "michael-jackson.jpg"),
            (45, "dua-lipa.jpg"),
            (46, "ed-sheeran.jpg"),
            (47, "ac-dc.jpg"),
            (48, "phonk.jpg"),
            (49, "pop.jpg"),
            (50, "country.jpg"),
            (51, "classique.jpg");

            insert into ALBUM (id_album, titre, annee_sortie, id_image) VALUES
            (1, "Il le fallait", 2023, 1),
            (2, "L'attaque des clones", 2023, 2),
            (3, "Baiser", 2023, 10),
            (4, "Dire je t’aime", 2024, 11),
            (5, "Sincèrement", 2023, 12),
            (6, "F4", 2023, 13),
            (7, "Paradise", 2019, 14),
            (8, "Aujourd’hui", 2023, 15),
            (9, "Eternal youth", 2022, 16),
            (10, "Hiver à Paris", 2022, 17),
            (11, "Ipséité", 2017, 18),
            (12, "KMT", 2022, 19),
            (13, "Lithopédion", 2018, 20),
            (14, "Novae", 2023, 21),
            (15, "Stamina Memento", 2020, 22),
            (16, "Elle and Louis", 1956, 23),
            (17, "What’d I say", 1959, 24),
            (18, "FORGOTTEN", 2022, 25),
            (19, "PSYCHX", 2022, 26),
            (20, "Bad", 1987, 27),
            (21, "Future Nostalgia", 2020, 28),
            (22, "Thriller", 1982, 29),
            (23, "X", 2014, 30),
            (24, "Back in Black", 1980, 31);

            insert into ARTISTE (id_artiste, nom_artiste, id_image) VALUES
            (1, "Favé", 3),
            (2, "Freeze corleone", 4),
            (3, "Wallace Clever", 32),
            (4, "BEN plg", 33),
            (5, "Hamza", 34),
            (6, "4am-Liam", 35),
            (7, "Yvnnis", 36),
            (8, "Dinos", 37),
            (9, "Damso", 38),
            (10, "Gazo", 39),
            (11, "Louis Armstrong", 40),
            (12, "Ray Charles", 41),
            (13, "LXST CXNTURY", 42),
            (14, "Kordhell", 43),
            (15, "Michael Jackson", 44),
            (16, "Dua Lipa", 45),
            (17, "Ed Sheeran", 46),
            (18, "AC/DC", 47);

            insert into REALISER_PAR (id_album, id_artiste) VALUES
            (1, 1),
            (2, 2),
            (3, 3),
            (4, 4),
            (5, 5),
            (6, 1),
            (7, 5),
            (8, 6),
            (9, 7),
            (10, 8),
            (11, 9),
            (12, 10),
            (13, 9),
            (14, 7),
            (15, 8),
            (16, 11),
            (17, 12),
            (18, 13),
            (19, 14),
            (20, 15),
            (21, 16),
            (22, 15),
            (23, 17),
            (24, 18);

            insert into GENRE (id_genre, nom_genre, id_image) VALUES
            (1, "Rap", 5),
            (2, "Rock", 6),
            (3, "Jazz", 7),
            (4, "Phonk", 48),
            (5, "Pop", 49),
            (6, "Country", 50),
            (7, "Classique", 51);

            insert into MUSIQUE (id_musique, nom_musique, duree_musique, son_musique, nb_streams, id_album) VALUES
            (1, "En vrai", "3:18", "en-vrai.mp3", 1, 1),
            (2, "Gmail", "3:14", "gmail.mp3", 4, 1),
            (3, "Vibes", "2:15", "vibes.mp3", 5, 1),
            (4, "Flashback", "3:03", "flashback.mp3", 8, 1),
            (5, "Favela", "2:51", "favela.mp3", 3, 1),
            (6, "Nuages", "3:13", "nuages.mp3", 2, 1),
            (7, "Ancelotti", "3:42", "ancelotti.mp3", 3, 2),
            (8, "Shavkat", "3:40", "shavkat.mp3", 5, 2),
            (9, "Jour de plus", "3:30", "jour-de-plus.mp3", 3, 2),
            (10, "Calavie", "3:14", "calavie.mp3", 2, 3),
            (11, "Déconnecté", "3:07", "deconnecte.mp3", 3, 3),
            (12, "Dans ma tête", "2:48", "dans-ma-tete.mp3", 3, 3),
            (13, "Benelli828", "3:02", "benelli828.mp3", 3, 3),
            (14, "Xtrois", "2:44", "xtrois.mp3", 3, 3),
            (15, "Murcielago", "2:56", "murcielago.mp3", 3, 3),
            (16, "Merci pour la douleur", "2:19", "merci-pour-la-douleur.mp3", 3, 3),
            (17, "Est-ce que l’aime ?", "2:49", "est-ce-que-l-aime.mp3", 3, 3),
            (18, "Le coeur à papa", "4:03", "le-coeur-a-papa.mp3", 3, 3),
            (19, "Pleurer pour nous", "3:38", "pleurer-pour-nous.mp3", 3, 3),
            (20, "Content", "2:55", "content.mp3", 3, 3),
            (21, "Le vent", "3:27", "le-vent.mp3", 3, 3),
            (22, "De rien pour la douceur", "5:59", "de-rien-pour-la-douceur.mp3", 3, 3),
            (23, "Prochaine fois", "3:16", "prochaine-fois.mp3", 3, 4),
            (24, "Colorier des HLM", "3:13", "colorier-des-hlm.mp3", 3, 4),
            (25, "Étoiles et satellites", "3:05", "etoiles-et-satellites.mp3", 3, 4),
            (26, "Free YSL", "3:07", "free-ysl.mp3", 3, 5),
            (27, "Codéine 19", "2:24", "codeine.mp3", 3, 5),
            (28, "Murder", "2:45", "murder.mp3", 3, 5),
            (29, "Sincèrement", "2:46", "sincerement.mp3", 3, 5),
            (30, "Urus", "2:40", "urus.mp3", 3, 6),
            (31, "Toxic", "3:07", "toxic.mp3", 3, 6),
            (32, "+ de sous", "2:43", "+-de-sous.mp3", 3, 6),
            (33, "00h", "2:49", "00h.mp3", 3, 6),
            (34, "Paradise", "3:29", "paradise.mp3", 3, 7),
            (35, "Validé", "3:21", "valide.mp3", 3, 7),
            (36, "HS", "3:33", "hs.mp3", 3, 7),
            (37, "Meilleur", "3:12", "meilleur.mp3", 3, 7),
            (38, "50x", "3:09", "50x.mp3", 3, 7),
            (39, "Hier", "3:16", "hier.mp3", 3, 8),
            (40, "Équinoxe (Love you)", "4:33", "equinoxe.mp3", 3, 8),
            (41, "Nuit (Aïe Aïe Aïe)", "3:27", "nuit.mp3", 3, 8),
            (42, "Demain", "3:10", "demain.mp3", 3, 8),
            (43, "Certifié", "3:31", "certifie.mp3", 3, 9),
            (44, "Fake", "2:11", "fake.mp3", 3, 9),
            (45, "Vin italien", "3:17", "vin-italien.mp3", 3, 9),
            (46, "Chrome Hearts", "2:49", "chrome-hearts.mp3", 3, 10),
            (47, "Modus Vivendi", "3:00", "modus-vivendi.mp3", 3, 10),
            (48, "Quatre saisons", "3:37", "quatre-saisons.mp3", 3, 10),
            (49, "Simyaci", "3:49", "simyaci.mp3", 3, 10),
            (50, "Rouge Drama", "2:52", "rouge-drama.mp3", 3, 10),
            (51, "Par Amour", "3:37", "par-amour.mp3", 3, 10),
            (52, "Mosaïque solitaire", "5:06", "mosaique-solitaire.mp3", 3, 11),
            (53, "Signaler", "3:22", "signaler.mp3", 3, 11),
            (54, "Macarena", "3:27", "macarena.mp3", 3, 11),
            (55, "Lové", "3:30", "love.mp3", 3, 11),
            (56, "N. J Respect R", "3:37", "n-j-respect-r.mp3", 3, 11),
            (57, "Bodies", "2:46", "bodies.mp3", 3, 12),
            (58, "Rappel", "3:38", "rappel.mp3", 3, 12),
            (59, "Die", "4:00", "die.mp3", 3, 12),
            (60, "Céline 3X", "2:38", "celine-3x.mp3", 3, 12),
            (61, "Molly", "3:30", "molly.mp3", 3, 12),
            (62, "Fleurs", "3:31", "fleurs.mp3", 3, 12),
            (63, "Festival de rêves", "3:26", "festival-de-reves.mp3", 3, 13),
            (64, "Julien", "3:22", "julien.mp3", 3, 13),
            (65, "Silence", "2:08", "silence.mp3", 3, 13),
            (66, "Feu de bois", "3:03", "feu-de-bois.mp3", 3, 13),
            (67, "Même issue", "3:15", "meme-issue.mp3", 3, 13),
            (68, "Aux paradis", "2:49", "aux-paradis.mp3", 3, 13),
            (69, "Washington", "2:03", "washington.mp3", 3, 14),
            (70, "+74", "2:42", "+74.mp3", 3, 14),
            (71, "Héros", "2:28", "heros.mp3", 3, 14),
            (72, "Soleil Pluvieux", "3:02", "soleil-pluvieux.mp3", 3, 14),
            (73, "Du mal à te dire", "3:27", "du-mal-a-te-dire.mp3", 3, 15),
            (74, "El Pichichi", "3:36", "el-pichichi.mp3", 3, 15),
            (75, "Tulum", "2:28", "tulum.mp3", 3, 15),
            (76, "Future Ex", "3:08", "future-ex.mp3", 3, 15),
            (77, "Surcoté", "3:33", "surcote.mp3", 3, 15),
            (78, "Moins un", "3:23", "moins-un.mp3", 3, 15),
            (79, "Moonlight in Vermont", "3:40", "moonlight-in-vermont.mp3", 3, 16),
            (80, "Tenderly", "5:08", "tenderly.mp3", 3, 16),
            (81, "Cheek to Cheek", "5:53", "cheek-to-cheek.mp3", 3, 16),
            (82, "April in Paris", "6:33", "april-in-paris.mp3", 3, 16),
            (83, "You Be My Baby", "2:32", "you-be-my-baby.mp3", 3, 17),
            (84, "Rockhouse", "3:54", "rockhouse.mp3", 3, 17),
            (85, "My Bonnie", "2:49", "my-bonnie.mp3", 3, 17),
            (86, "That’s Enough", "2:47", "that-s-enough.mp3", 3, 17),
            (87, "CATHARSIS", "2:39", "catharsis.mp3", 3, 18),
            (88, "GRIM", "2:56", "grim.mp3", 3, 18),
            (89, "VHS", "2:00", "vhs.mp3", 3, 18),
            (90, "NO HOPE", "1:31", "no-hope.mp3", 3, 18),
            (91, "I FEEL ALIVE", "2:03", "i-feel-alive.mp3", 3, 19),
            (92, "THIS IS MY LIFE", "2:07", "this-is-my-life.mp3", 3, 19),
            (93, "FIND YXURSELF", "2:35", "find-yxurself.mp3", 3, 19),
            (94, "Bad", "4:07", "bad.mp3", 3, 20),
            (95, "The Way You Make Feel", "4:58", "the-way-you-make-feel.mp3", 3, 20),
            (96, "Smooth Criminal", "4:18", "smooth-criminal.mp3", 3, 20),
            (97, "Leave Me Alone", "4:40", "leave-me-alone.mp3", 3, 20),
            (98, "Don’t Start Now", "3:03", "don-t-start-now.mp3", 3, 21),
            (99, "Physical", "3:14", "physical.mp3", 3, 21),
            (100, "Levitating", "3:24", "levitating.mp3", 3, 21),
            (101, "Love Again", "4:18", "love-again.mp3", 3, 21),
            (102, "Fever", "2:37", "fever.mp3", 3, 21),
            (103, "Thriller", "5:58", "thriller.mp3", 3, 22),
            (104, "Beat It", "4:18", "beat-it.mp3", 3, 22),
            (105, "Billie Jean", "4:54", "billie-jean.mp3", 3, 22),
            (106, "The Lady In My Life", "4:58", "the-lady-in-my-life.mp3", 3, 22),
            (107, "Sing", "3:56", "sing.mp3", 3, 23),
            (108, "Don’t", "3:40", "don-t.mp3", 3, 23),
            (109, "Photograph", "4:19", "photograph.mp3", 3, 23),
            (110, "Thinking Out Loud", "4:42", "thinking-out-loud.mp3", 3, 23),
            (111, "Hells Bells", "5:13", "hells-bells.mp3", 3, 24),
            (112, "Shoot to Thrill", "5:18", "shoot-to-thrill.mp3", 3, 24),
            (113, "Back In Black", "4:16", "back-in-black.mp3", 3, 24),
            (114, "Shake a Leg", "4:06", "shake-a-leg.mp3", 3, 24);

            insert into FAIRE_PARTIE (id_album, id_genre) VALUES
            (1, 1),
            (2, 1),
            (3, 1),
            (4, 1),
            (5, 1),
            (6, 1),
            (7, 1),
            (8, 1),
            (9, 1),
            (10, 1),
            (11, 1),
            (12, 1),
            (13, 1),
            (14, 1),
            (15, 1),
            (16, 3),
            (17, 3),
            (18, 4),
            (19, 4),
            (20, 5),
            (21, 5),
            (22, 5),
            (23, 5),
            (24, 2);

            insert into APPARTENIR (id_artiste, id_genre) VALUES
            (1, 1),
            (2, 1),
            (3, 1),
            (4, 1),
            (5, 1),
            (6, 1),
            (7, 1),
            (8, 1),
            (9, 1),
            (10, 1),
            (11, 3),
            (12, 3),
            (13, 4),
            (14, 4),
            (15, 5),
            (16, 5),
            (17, 5),
            (18, 2);

            insert into UTILISATEUR (id_utilisateur, nom_utilisateur, mail_utilisateur, mdp, admin) VALUES
            (1, "quentin", "quentin@gmail.com", "Test123!", "O"),
            (2, "ahmet", "ahmet@gmail.com", "Test123!", "O"),
            (3, "maverick", "maverick@gmail.com", "Test123!", "O"),
            (4, "test", "test@gmail.com", "Test123!", "N");

            insert into PLAYLIST (id_playlist, nom_playlist, id_image, id_utilisateur) VALUES
            (1, "Chill rap", 8, 1),
            (2, "Hard rap", 9, 1);

            insert into CONTENIR (id_musique, id_playlist) VALUES
            (2, 1),
            (3, 1),
            (5, 1),
            (8, 1),
            (7, 2),
            (8, 2),
            (9, 2);
        EOF;
        try {
            $pdo->exec($query);
        }
        catch (PDOException $e) {
            var_dump($e->getMessage());
        }


        // chargement du fichier yml donné dans le sujet

        // Chargement du contenu du fichier YML
        $fileContent = file_get_contents('./Data/extrait.yml');

        if ($fileContent === false) { // vérifie si le chargement a réussi
            die('Erreur de chargement du fichier YML.');
        }

        // converti le fichier YML en tableau associatif
        $lines = explode("\n", $fileContent);
        $les_albums = [];
        $current_album = [];

        foreach ($lines as $line) {
            $line = trim($line);


            if (empty($line)) { // ignore les lignes vides
                continue;
            }

            if (strpos($line, '- by:') === 0) { // identifie le début d'un nouvel album
                if (!empty($current_album)) { // ajoute l'album actuel au tableau
                    $les_albums[] = $current_album;
                    $current_album = [];
                }
            }

            // extraire les clés et les valeurs
            list($key, $value) = explode(':', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key === 'genre') { // gére le cas où la valeur est un tableau
                $current_album[$key] = array_map('trim', explode(',', substr($value, 1, -1)));
            }
            else if ($key === '- by'){
                $current_album[str_replace("- ", "", $key)] = $value;
            }
            else {
                $current_album[$key] = $value;
            }
        }

        // ajoute le dernier album au tableau
        if (!empty($current_album)) {
            $les_albums[] = $current_album;
        }

        $requete_max_id_image = <<<EOF
            select max(id_image) maxIdImage from IMAGE;
        EOF;
        $stmt = $pdo->prepare($requete_max_id_image);
        $stmt->execute();
        $max_id_image = $stmt->fetch(PDO::FETCH_ASSOC)['maxIdImage'];
        if ($max_id_image === null) {
            // aucune entrée dans la table
            $new_id_image = 1;
        }
        else {
            // ajout 1 à la valeur maximale récupérée
            $new_id_image = $max_id_image + 1;
        }

        $requete_max_id_genre = <<<EOF
            select max(id_genre) maxIdGenre from GENRE;
        EOF;
        $stmt = $pdo->prepare($requete_max_id_genre);
        $stmt->execute();
        $max_id_genre = $stmt->fetch(PDO::FETCH_ASSOC)['maxIdGenre'];
        if ($max_id_genre === null) {
            // aucune entrée dans la table
            $new_id_genre = 1;
        }
        else {
            // ajout 1 à la valeur maximale récupérée
            $new_id_genre = $max_id_genre + 1;
        }

        $requete_max_id_artiste = <<<EOF
            select max(id_artiste) maxIdArtiste from ARTISTE;
        EOF;
        $stmt = $pdo->prepare($requete_max_id_artiste);
        $stmt->execute();
        $max_id_artiste = $stmt->fetch(PDO::FETCH_ASSOC)['maxIdArtiste'];
        if ($max_id_artiste === null) {
            // aucune entrée dans la table
            $new_id_artiste = 1;
        }
        else {
            // ajout 1 à la valeur maximale récupérée
            $new_id_artiste = $max_id_artiste + 1;
        }

        // insertion image default pour genre
        $image_default = 'default.jpg';
        $insertion_image_default = <<<EOF
            INSERT INTO IMAGE (id_image, image) VALUES (:id_image, :image);
        EOF;
        $stmt = $pdo->prepare($insertion_image_default);
        $stmt->bindParam(':id_image', $new_id_image, PDO::PARAM_INT);
        $stmt->bindParam(':image', $image_default, PDO::PARAM_STR);
        $stmt->execute();
        $id_image_default = $new_id_image;
        $new_id_image += 1;

        // parcours des albums
        foreach ($les_albums as $album) {

            $image_album = isset($album['img']) ? $album['img'] : 'default.jpg';
            if ($image_album != "null"){
                $insertion_image = <<<EOF
                    INSERT INTO IMAGE (id_image, image) VALUES (:id_image, :image);
                EOF;
                $stmt = $pdo->prepare($insertion_image);
                $stmt->bindParam(':id_image', $new_id_image, PDO::PARAM_INT);
                $stmt->bindParam(':image', $image_album, PDO::PARAM_STR);
                $stmt->execute();
            }

            $insertion_album = <<<EOF
                INSERT INTO ALBUM (id_album, titre, annee_sortie, id_image) VALUES (:id_album, :titre, :annee_sortie, :id_image);
            EOF;
            $stmt = $pdo->prepare($insertion_album);
            $stmt->bindParam(':id_album', $album['entryId'], PDO::PARAM_INT);
            $stmt->bindParam(':titre', $album['title'], PDO::PARAM_STR);
            $stmt->bindParam(':annee_sortie', $album['releaseYear'], PDO::PARAM_STR);
            if ($image_album != "null"){
                $stmt->bindParam(':id_image', $new_id_image, PDO::PARAM_INT);
            }
            else{
                $stmt->bindParam(':id_image', $id_image_default, PDO::PARAM_INT);
            }
            $stmt->execute();

            foreach ($album["genre"] as $genre_album){

                $contient_deja_genre = <<<EOF
                    SELECT id_genre FROM GENRE WHERE nom_genre = :nom_genre;
                EOF;
                $stmt = $pdo->prepare($contient_deja_genre);
                $stmt->bindParam(':nom_genre', $genre_album, PDO::PARAM_STR);
                $stmt->execute();
                $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$resultat) {
                    // Le genre n'existe pas dans la base de données
                    $insertion_genre = <<<EOF
                        INSERT INTO GENRE (id_genre, nom_genre, id_image) VALUES (:id_genre, :nom_genre, :id_image);
                    EOF;
                    $stmt = $pdo->prepare($insertion_genre);
                    $stmt->bindParam(':id_genre', $new_id_genre, PDO::PARAM_INT);
                    $stmt->bindParam(':nom_genre', $genre_album, PDO::PARAM_STR);
                    $stmt->bindParam(':id_image', $id_image_default, PDO::PARAM_INT);
                    $stmt->execute();
                }
                $id_genre_album = isset($resultat['id_genre']) ? $resultat['id_genre'] : $new_id_genre;

                // insertion lien album et genre
                $insertion_faire_partie = <<<EOF
                    INSERT INTO FAIRE_PARTIE (id_album, id_genre) VALUES (:id_album, :id_genre);
                EOF;
                $stmt = $pdo->prepare($insertion_faire_partie);
                $stmt->bindParam(':id_album', $album['entryId'], PDO::PARAM_INT);
                $stmt->bindParam(':id_genre', $id_genre_album, PDO::PARAM_INT);
                $stmt->execute();

                // incrémentation nouvel id pour le prochain genre de l'album
                $new_id_genre += 1;
            }

            $contient_deja_artiste = <<<EOF
                SELECT id_artiste FROM ARTISTE WHERE nom_artiste = :nom_artiste;
            EOF;
            $stmt = $pdo->prepare($contient_deja_artiste);
            $stmt->bindParam(':nom_artiste', $album["by"], PDO::PARAM_STR);
            $stmt->execute();
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$resultat) {
                // L'artiste n'existe pas dans la base de données
                $insertion_artiste = <<<EOF
                    insert into ARTISTE (id_artiste, nom_artiste, id_image) values (:id_artiste, :nom_artiste, :id_image);
                EOF;
                $stmt = $pdo->prepare($insertion_artiste);
                $stmt->bindParam(':id_artiste', $new_id_artiste, PDO::PARAM_INT);
                $stmt->bindParam(':nom_artiste', $album["by"], PDO::PARAM_STR);
                $stmt->bindParam(':id_image', $id_image_default, PDO::PARAM_INT);
                $stmt->execute();

                // pour l'insertion realiser_par
                $id_artiste_actuel = $new_id_artiste;

                // incrémente pour le nouveau prochain artiste à ajouter
                $new_id_artiste += 1;
            }
            else{
                // pour l'insertion realiser_par
                $id_artiste_actuel = $resultat["id_artiste"];
            }

            // insertion lien album et artiste
            $insertion_realiser_par = <<<EOF
                INSERT INTO REALISER_PAR (id_album, id_artiste) VALUES (:id_album, :id_artiste);
            EOF;
            $stmt = $pdo->prepare($insertion_realiser_par);
            $stmt->bindParam(':id_album', $album['entryId'], PDO::PARAM_INT);
            $stmt->bindParam(':id_artiste', $id_artiste_actuel, PDO::PARAM_INT);
            $stmt->execute();

            // incrémentation nouvel id pour le prochain album (nouvelle image future)
            $new_id_image += 1;
        }
        break;
    
    default:
        echo "Pas d'action associée".PHP_EOL;
        break;
}