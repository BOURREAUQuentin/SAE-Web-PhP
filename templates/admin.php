<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$albumPDO = new AlbumPDO($pdo);
$musiquePDO = new MusiquePDO($pdo);
$artistePDO = new ArtistePDO($pdo);
$imagePDO = new ImagePDO($pdo);
$utilisateurPDO = new utilisateurPDO($pdo);

// vérification de si l'utilisateur est connecté et s'il est admin
$nom_utilisateur_connecte = "pas connecté";
$est_admin = false;
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $est_admin = ($utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte))->isAdmin();
}
if (!$est_admin) {
    // redirigez l'utilisateur vers la page de connexion (n'est pas admin)
    header('Location: ?action=connexion_inscription');
    exit();
}

// Récupération de la liste des albums, musiques et artistes
$les_albums = $albumPDO->getAlbums();
$les_musiques = $musiquePDO->getMusiques();
$les_artistes = $artistePDO->getArtistes();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music'O</title>
    <style>
        body{
            background-color: #424242;
        }
    </style>
</head>
<body>
<a href="/?action=admin_album">
    <button class="login-button">Gérer les albums</button>
</a>
<a href="/?action=admin_musique">
    <button class="login-button">Gérer les musiques</button>
</a>
<a href="/?action=admin_artiste">
    <button class="login-button">Gérer les artistes</button>
</a>
</body>
</html>