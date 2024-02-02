<?php
use View\Template;

require_once 'Configuration/config.php';

// SPL autoloader
require 'Classes/autoloader.php'; 
Autoloader::register();

// Manage action / controller
$action = $_REQUEST['action'] ?? 'main';
ob_start();
switch ($action) {
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
$template->setLayout($action);
$template->setContent($content);

echo $template->compile();
