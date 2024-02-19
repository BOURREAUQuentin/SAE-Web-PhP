<?php
use View\Template;

require_once 'Configuration/config.php';

// SPL autoloader
require 'Classes/autoloader.php'; 
Autoloader::register();

// lancement de la session
session_start();

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

use Modele\modele_bd\ContenirPDO;
use Modele\modele_bd\PlaylistPDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\FairePartiePDO;
use Modele\modele_bd\RealiserParPDO;
use Modele\modele_bd\AppartenirPDO;
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\LikerPDO;
use Modele\modele_bd\NoterPDO;
use Modele\modele_bd\GenrePDO;

// instanciation des classes PDO
$contenirPDO = new ContenirPDO($pdo);
$playlistPDO = new PlaylistPDO($pdo);
$imagePDO = new ImagePDO($pdo);
$utilisateurPDO = new UtilisateurPDO($pdo);
$artistePDO = new ArtistePDO($pdo);
$albumPDO = new AlbumPDO($pdo);
$fairePartiePDO = new FairePartiePDO($pdo);
$realiserParPDO = new RealiserParPDO($pdo);
$appartenirPDO = new AppartenirPDO($pdo);
$musiquePDO = new MusiquePDO($pdo);
$likerPDO = new LikerPDO($pdo);
$noterPDO = new NoterPDO($pdo);
$genrePDO = new GenrePDO($pdo);

// Manage action / controller
$action = $_REQUEST['action'] ?? 'accueil';
ob_start();
switch ($action) {
    case 'playlist':
        include 'templates/playlist.php';
        break;
    
    case 'logout':
        // supprime la clé "username" de la session
        unset($_SESSION["username"]);
        include 'templates/accueil.php';
        break;

    case 'genre':
        include 'templates/genre.php';
        break;

    case 'album':
        include 'templates/album.php';
        break;
    
    case 'artiste':
        include 'templates/artiste.php';
        break;
    
    case 'recherche':
        include 'templates/recherche.php';
        break;

    case 'connexion_inscription':
        include 'templates/connexion_inscription.php';
        break;

    case 'filtre_annee':
        include 'templates/filtre_annee.php';
        break;

    case 'titres_likes':
        include 'templates/titres_likes.php';
        break;
    
    case 'playlists_utilisateur':
        include 'templates/playlists_utilisateur.php';
        break;

    case 'profil':
        include 'templates/profil.php';
        break;

    case 'admin':
        include 'templates/admin.php';
        break;
    
    case 'admin_album':
        include 'templates/admin_album.php';
        break;
    
    case 'admin_musique':
        include 'templates/admin_musique.php';
        break;
    
    case 'admin_artiste':
        include 'templates/admin_artiste.php';
        break;
    
    case 'admin_utilisateur':
        include 'templates/admin_utilisateur.php';
        break;

    case 'admin_genre':
        include 'templates/admin_genre.php';
        break;

    case 'ajouter_playlist':
        $id_musique = $_GET['id_musique'];
        $id_playlist = $_GET['id_playlist'];
        $ajoutReussi = $contenirPDO->ajouterContenir($id_musique, $id_playlist);

        // redirection de l'utilisateur vers la page de la playlist
        header('Location: ?action=playlist&id_playlist=' . $id_playlist);
        exit;
    
    case 'supprimer_musique_playlist':
        $id_musique = $_GET['id_musique'] ?? null;
        $id_playlist = $_GET['id_playlist'] ?? null;
        $contenirPDO->supprimerContenir($id_musique, $id_playlist);
        // Redirection de l'utilisateur vers la page de la playlist
        header('Location: ?action=playlist&id_playlist=' . $id_playlist);
        exit;
    
    case 'rechercher_requete':
        // récupération des valeurs saisies dans les champs de recherche
        $intitule_recherche = $_GET['search_query'] ?? ''; // si la valeur n'est pas définie, utilisez une chaîne vide par défaut
        $genre_recherche = $_GET['genre'];
        $annee_recherche = $_GET['annee'];
        // Redirection de l'utilisateur vers la page de la recherche
        header('Location: ?action=recherche&intitule_recherche=' . $intitule_recherche . "&genre_recherche=" . $genre_recherche . "&annee_recherche=" . $annee_recherche);
        exit;
    
    case 'creer_playlist':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // récupération des données du formulaire
            $nom_playlist = $_POST['nom_playlist'];

            // gestion de l'image de la playlist
            $url = $_POST["url_image"];
            $image = file_get_contents($url);

            $nombre_aleatoire = rand(1, 10000);
            $nom_playlist_transforme = str_replace(' ', '-', $nom_playlist);
            $imagePDO->ajouterImage($_SESSION["username"] . "-" . $nombre_aleatoire . "-" . $nom_playlist_transforme); // nom image -> nom_utilisateur-nombre_aleatoire-nom_playlist_transforme
            // Écriture du contenu de l'image dans un fichier sur le serveur
            file_put_contents("./images/" . $_SESSION["username"] . "-" . $nombre_aleatoire . "-" . $nom_playlist_transforme, $image);

            // appel de la méthode pour créer la playlist
            $id_new_image = ($imagePDO->getImageByNomImage($_SESSION["username"] . "-" . $nombre_aleatoire . "-" . $nom_playlist_transforme))->getIdImage();
            $utilisateur_connecte = $utilisateurPDO->getUtilisateurByNomUtilisateur($_SESSION["username"]);
            $id_new_playlist = $playlistPDO->creerPlaylist($nom_playlist, $id_new_image, $utilisateur_connecte->getIdUtilisateur());

            // redirection de l'utilisateur vers la page principale ou autre
            header('Location: ?action=playlist&id_playlist=' . $id_new_playlist);
            exit;
        }
        break;

    case 'supprimer_playlist':
        // récupération de l'id de la playlist
        $id_playlist = $_GET['id_playlist'] ?? null;

        // suppression lien playlist et musique
        $contenirPDO->supprimerMusiquesPlaylistsByIdPlaylist($id_playlist);

        // récupération id_image de la playlist pour supprimer après
        $playlist = $playlistPDO->getPlaylistByIdPlaylist($id_playlist);
        $id_image_playlist = $playlist->getIdImage();

        // suppression de la playlist
        $playlistPDO->supprimerPlaylistByIdPlaylist($id_playlist);

        // suppression de l'image associée à la playlist
        $image_playlist = ($imagePDO->getImageByIdImage($id_image_playlist))->getImage();
        if ($image_playlist != "default.jpg"){ // pour ne pas supprimer l'image par défaut de la table IMAGE
            $imagePDO->supprimerImageByIdImage($id_image_playlist);
        }

        // redirection de l'utilisateur vers la page principale ou autre
        header('Location: ?action=playlists_utilisateur');
        exit;

    case 'modifier_playlist':
        // récupération de l'id de la playlist
        $id_playlist = $_GET['id_playlist'] ?? null;

        // récupération du nouveau nom de la playlist (dans formulaire)
        $nouveau_nom_playlist = $_POST['nouveau_nom'];

        // récupération de la playlist à modifier
        $playlist_a_modifier = $playlistPDO->getPlaylistByIdPlaylist($id_playlist);

        // récupère le nom de l'image actuel
        $image_playlist = $imagePDO->getImageByIdImage($playlist_a_modifier->getIdImage());

        // modification du nom de la playlist
        $playlistPDO->mettreAJourNomPlaylist($id_playlist, $nouveau_nom_playlist);

        // modification du nom de l'image de la playlist
        $nom_playlist_transforme = str_replace(' ', '-', $nouveau_nom_playlist);

        // récupération de chaque partie de l'ancien nom de l'image de la playlist
        $parties_nom_image = explode("-", $image_playlist->getImage());
        $nom_utilisateur = $parties_nom_image[0]; // Première partie : nom d'utilisateur
        $nombre_aleatoire = $parties_nom_image[1];// Deuxième partie : nombre aléatoire

        // nom image -> nom_utilisateur-nombre_aleatoire-nom_playlist_transforme
        $nouveau_nom_image_playlist = $nom_utilisateur . "-" . $nombre_aleatoire . "-" . $nom_playlist_transforme;
        $imagePDO->mettreAJourNomImage($image_playlist->getIdImage(), $nouveau_nom_image_playlist);

        // renomme le nom de l'ancienne image dans le dossier images
        rename("./images/" . $image_playlist->getImage(), "./images/" . $nouveau_nom_image_playlist);

        // redirection de l'utilisateur vers la même page
        header('Location: ?action=playlists_utilisateur');
        exit;

    case 'ajouter_artiste':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // récupération des données du formulaire
            $nom_artiste = $_POST['nom_artiste'];

            // gestion de l'image de l'artiste
            $nom_image = $_FILES['image_artiste']['name'];
            $image_temp = $_FILES['image_artiste']['tmp_name'];

            $nombre_aleatoire = rand(1, 10000);
            $imagePDO->ajouterImage($nombre_aleatoire . "-" . $nom_artiste); // nom image -> nombre_aleatoire-nom_artiste
            move_uploaded_file($image_temp, "./images/" . $nombre_aleatoire . "-" . $nom_artiste);

            // appel de la méthode pour créer l'artiste
            $id_new_image = ($imagePDO->getImageByNomImage($nombre_aleatoire . "-" . $nom_artiste))->getIdImage();
            $artistePDO->ajouterArtiste($nom_artiste, $id_new_image);
            // redirection de l'utilisateur vers la même page
            header('Location: ?action=admin_artiste');
            exit;
        }
        break;
    
    case 'ajouter_album':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // récupération des données du formulaire
            $nom_album = $_POST['nom_album'];
            $annee_sortie_album = $_POST['annee_sortie'];
            $id_genres_album = $_POST['genres']; // tableau contenant les ids des genres sélectionnés
            $id_artiste_album = $_POST['artiste']; // la valeur affiché à l'utilisateur est le nom mais on récupère l'id

            // gestion de l'image de l'album
            $image_temp = $_FILES['image_album']['tmp_name'];

            $nombre_aleatoire = rand(1, 10000);
            $nom_image_transforme = str_replace(' ', '-', $nom_album);
            $imagePDO->ajouterImage($nombre_aleatoire . "-" . $nom_image_transforme . "-" . $id_artiste_album); // nom image -> nombre_aleatoire-nom_image_transforme-id_artiste_album
            move_uploaded_file($image_temp, "./images/" . $nombre_aleatoire . "-" . $nom_image_transforme . "-" . $id_artiste_album);

            // appel de la méthode pour créer l'album
            $id_new_image = ($imagePDO->getImageByNomImage($nombre_aleatoire . "-" . $nom_image_transforme . "-" . $id_artiste_album))->getIdImage();
            $id_new_album = $albumPDO->ajouterAlbum($nom_album, $annee_sortie_album, $id_new_image);

            // création du lien entre album et genres (donc artiste aura ces genres aussi)
            foreach ($id_genres_album as $id_genre) {
                $fairePartiePDO->ajouterFairePartie($id_new_album, $id_genre);
                $appartenirPDO->ajouterAppartenir($id_artiste_album, $id_genre);
            }

            // création du lien entre album et artiste
            $realiserParPDO->ajouterRealiser($id_new_album, $id_artiste_album);
            
            // redirection de l'utilisateur vers la même page
            header('Location: ?action=admin_album');
            exit;
        }
        break;
    
    case 'supprimer_album':
        // récupération de l'id de l'album
        $id_album = $_GET['id_album'] ?? null;

        // suppression des musiques associées à l'album
        $likerPDO->supprimerLikesByIdAlbum($id_album); // suppression clés étrangères des musiques de l'album
        $contenirPDO->supprimerMusiquesPlaylistsByIdAlbum($id_album); // suppression clés étrangères des musiques de l'album
        $musiquePDO->supprimerMusiquesByIdAlbum($id_album); // suppression des musiques de l'album

        // suppression du lien entre album et artiste
        $realiserParPDO->supprimerAlbumByIdAlbum($id_album);

        // suppression du lien entre album et utilisateur (notes)
        $noterPDO->supprimerNotesByIdAlbum($id_album);

        // suppression du lien entre album et genre
        $fairePartiePDO->supprimerGenresByIdAlbum($id_album);

        // récupération id_image de l'album pour supprimer après
        $album = $albumPDO->getAlbumByIdAlbum($id_album);
        $id_image_album = $album->getIdImage();

        // suppression de l'album
        $albumPDO->supprimerAlbumByIdAlbum($id_album);

        // suppression de l'image associée à l'album
        $image_album = ($imagePDO->getImageByIdImage($id_image_album))->getImage();
        if ($image_album != "default.jpg"){ // pour ne pas supprimer l'image par défaut de la table IMAGE
            $imagePDO->supprimerImageByIdImage($id_image_album);
        }
        
        // redirection de l'utilisateur vers la même page
        header('Location: ?action=admin_album');
        exit;

    case 'supprimer_artiste':
        // récupération de l'id de l'artiste
        $id_artiste = $_GET['id_artiste'] ?? null;
        print_r($id_artiste);
        $artistePDO -> supprimerArtisteEtSesDependance($id_artiste);
        header('Location: ?action=admin_artiste');
        exit;

    case 'modifier_album':
        // récupération de l'id de l'album
        $id_album = $_GET['id_album'] ?? null;

        $nouveau_titre_album = $_POST["nouveau_titre"];
        $nouvelle_annee_sortie_album = $_POST["nouvelle_annee_sortie"];
        $albumPDO->mettreAJourInfosAlbum($id_album, $nouveau_titre_album, $nouvelle_annee_sortie_album); // modification du nom et de l'année de sortie de l'album

        // redirection de l'utilisateur vers la même page
        header('Location: ?action=admin_album');
        exit;
    
    case 'supprimer_musique':
        // récupération de l'id de la musique
        $id_musique = $_GET['id_musique'] ?? null;

        // suppression des likes de la musique
        $likerPDO->supprimerLikesByIdMusique($id_musique);

        // suppression du lien entre musique et playlist
        $contenirPDO->supprimerMusiquesPlaylistsByIdMusique($id_musique);

        // suppression de la musique
        $musiquePDO->supprimerMusiqueByIdMusique($id_musique);
        
        // redirection de l'utilisateur vers la même page
        header('Location: ?action=admin_musique');
        exit;
    
    case 'modifier_musique':
        // récupération de l'id de la musique
        $id_musique = $_GET['id_musique'] ?? null;

        $nouveau_nom_musique = $_POST["nouveau_nom"];

        $musiquePDO->mettreAJourNomMusique($id_musique, $nouveau_nom_musique); // modification du nom de la musique

        // redirection de l'utilisateur vers la même page
        header('Location: ?action=admin_musique');
        exit;
    
    case 'ajouter_musique':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // récupération des données du formulaire
            $nom_musique = $_POST['nom_musique'];
            $duree_audio = $_POST['duree_audio'] ?? "00:00";
            $id_album_musique = $_POST['album']; // la valeur affiché à l'utilisateur est le nom mais on récupère l'id de l'abum

            // chemin du fichier MP3 téléchargé
            $chemin_fichier_mp3 = $_FILES['fichier_mp3']['tmp_name'];

            var_dump($_FILES["fichier_mp3"]["size"]);
            var_dump($_FILES["fichier_mp3"]["error"]);
            var_dump($chemin_fichier_mp3);

            $nombre_aleatoire = rand(1, 10000);
            $nom_musique_sans_espaces = str_replace(' ', '-', $nom_musique); // remplacer les espaces par des tirets
            $nom_musique_sans_espaces_minuscules = strtolower($nom_musique_sans_espaces); // enlever les minuscules
            $nom_musique_transforme = str_replace("'", "-", $nom_musique_sans_espaces_minuscules); // remplacer les apostrophes par des tirets

            $nom_fichier_son_musique = $id_album_musique . "-" . $nombre_aleatoire . "-" . $nom_musique_transforme . ".mp3";
            // mettre fichier mp3 dans dossier sounds
            move_uploaded_file($chemin_fichier_mp3, "./static/sounds/" . $nom_fichier_son_musique);

            // appel de la méthode pour créer la musique
            $musiquePDO->ajouterMusique($nom_musique, $duree_audio, $nom_fichier_son_musique, $id_album_musique);

            // redirection de l'utilisateur vers la même page
            //header('Location: ?action=admin_musique');
            exit;
        }
        break;

    case 'modifier_artiste':
        // récupération de l'id de l'artiste
        $id_artiste = $_GET['id_artiste'] ?? null;

        $nouveau_nom_artiste = $_POST["nouveau_nom"];
        $artistePDO-> modifierArtiste($id_artiste, $nouveau_nom_artiste); // modification du nom de l'artiste
        // redirection de l'utilisateur vers la même page
        header('Location: ?action=admin_artiste');
        exit;

    case 'supprimer_utilisateur':
        // récupération de l'id de l'utilisateur
        $id_utilisateur = $_GET['id_utilisateur'] ?? null;

        // suppression des likes de l'utilisateur
        $likerPDO->supprimerLikesByIdUtilisateur($id_utilisateur);

        // suppression du lien entre utilisateur et note
        $noterPDO->supprimerNotesByIdUtilisateur($id_utilisateur);

        // suppression du lien des playlists de l'utilisateur
        $playlistPDO->supprimerPlaylistsByIdUtilisateur($id_utilisateur);

        // suppression de l'utilisateur
        $utilisateurPDO->supprimerUtilisateurByIdUtilisateur($id_utilisateur);

        // redirection de l'utilisateur vers la même page
        header('Location: ?action=admin_utilisateur');
        exit;
    
    case 'supprimer_musique_likee':
        // récupération de l'id de la musique
        $id_musique = $_GET['id_musique'] ?? null;
        // récupération de l'id de l'utilisateur
        $id_utilisateur = $_GET['id_utilisateur'] ?? null;

        $likerPDO->supprimerLiker($id_musique, $id_utilisateur);

        // redirection de l'utilisateur vers la même page
        header('Location: ?action=titres_likes');
        exit;

    default:
        include 'templates/accueil.php';
        break;
}
$content = ob_get_clean();

// Template
$template = new Template('templates');

if ($action == "logout"){
    $action = "accueil";
}
$template->setLayout($action);
$template->setContent($content);

echo $template->compile();
