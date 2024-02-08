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

// instanciation des classes PDO
$contenirPDO = new ContenirPDO($pdo);
$playlistPDO = new PlaylistPDO($pdo);
$imagePDO = new ImagePDO($pdo);
$utilisateurPDO = new UtilisateurPDO($pdo);

// Manage action / controller
$action = $_REQUEST['action'] ?? 'main';
ob_start();
switch ($action) {
    case 'playlist':
        include 'templates/playlist.php';
        break;
    
    case 'logout':
        // supprime la clé "username" de la session
        unset($_SESSION["username"]);
        include 'templates/main.php';
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

    case 'page_connexion_inscription':
        include 'templates/page_connexion_inscription.php';
        break;

    case 'filtre_annee':
        include 'templates/filtre_annee.php';
        break;

    case 'titre_like':
        include 'templates/titre_like.php';
        break;    

    case 'ajouter_playlist':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_SESSION["username"])){
                $id_musique = $_POST['id_musique'];
                $id_playlist = $_POST['id_playlist'];
                $ajoutReussi = $contenirPDO->ajouterContenir($id_musique, $id_playlist);
                if (!$ajoutReussi) {
                    // message d'erreur à afficher -> La musique est déjà dans la playlist
                }
                // redirection de l'utilisateur vers la page de la playlist
                header('Location: ?action=playlist&id_playlist=' . $id_playlist);
                exit;
            }
            else{
                // redirection de l'utilisateur vers la page de connexion
                header('Location: ?action=page_connexion_inscription');
                exit;
            }
        }
        break;
    
    case 'supprimer_musique':
        $id_musique = $_GET['id_musique'] ?? null;
        $id_playlist = $_GET['id_playlist'] ?? null;
        $contenirPDO->supprimerContenir($id_musique, $id_playlist);
        // Redirection de l'utilisateur vers la page de la playlist
        header('Location: ?action=playlist&id_playlist=' . $id_playlist);
        exit;
    
    case 'rechercher_requete':
        // récupération de la valeur saisie dans le champ de recherche
        $intitule_playlist = $_GET['search_query'] ?? ''; // si la valeur n'est pas définie, utilisez une chaîne vide par défaut
        // Redirection de l'utilisateur vers la page de la recherche
        header('Location: ?action=recherche&intitule_recherche=' . $intitule_playlist);
        exit;
    
    case 'creer_playlist':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // récupération des données du formulaire
            $nom_playlist = $_POST['nom_playlist'];

            // gestion de l'image de la playlist
            $image_playlist = $_FILES['image_playlist']['name'];
            $image_temp = $_FILES['image_playlist']['tmp_name'];

            $nombre_aleatoire = rand(1, 1000);
            $imagePDO->ajouterImage($_SESSION["username"] . "-" . $nombre_aleatoire); // nom image -> nom_playlist-nombre_aleatoire
            if ($_FILES["image_playlist"]["error"] > 0){
                $image_temp = "./images/default.jpg";
            }
            move_uploaded_file($image_temp, "./images/" . $_SESSION["username"] . "-" . $nombre_aleatoire);

            // appel de la méthode pour créer la playlist
            $id_new_image = ($imagePDO->getImageByNomImage($_SESSION["username"] . "-" . $nombre_aleatoire))->getIdImage();
            $utilisateur_connecte = $utilisateurPDO->getUtilisateurByNomUtilisateur($_SESSION["username"]);
            $playlistPDO->creerPlaylist($nom_playlist, $id_new_image, $utilisateur_connecte->getIdUtilisateur());

            // redirection de l'utilisateur vers la page principale ou autre
            header('Location: ?action=main');
            exit;
        }
        break;

    default:
        include 'templates/main.php';
        break;
}
$content = ob_get_clean();

// Template
$template = new Template('templates');

if ($action == "logout"){
    $action = "main";
}
$template->setLayout($action);
$template->setContent($content);

echo $template->compile();
