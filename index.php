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
// instanciation des classes PDO
$contenirPDO = new ContenirPDO($pdo);

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

    case 'page_connexion':
        include 'templates/page_connexion.php';
        break;
    case 'page_inscription':
        include 'templates/page_inscription.php';
        break;
    
    case 'ajouter_playlist':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
