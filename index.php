<?php
use View\Template;

require_once 'Configuration/config.php';

// SPL autoloader
require 'Classes/autoloader.php'; 
Autoloader::register();

// lancement de la session
session_start();

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
