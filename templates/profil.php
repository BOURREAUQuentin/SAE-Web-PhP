<?php
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation de la classe PDO Utilisateur
$utilisateurPDO = new utilisateurPDO($pdo);

// récupération de l'utilisateur connecté et s'il est admin
$nom_utilisateur_connecte = "pas connecté";
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $utilisateur_connecte = $utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte);
}
else{
    // redirigez l'utilisateur vers la page de connexion (n'est pas admin)
    header('Location: ?action=connexion_inscription');
    exit();
}
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

        .genre-list {
            display: flex;
            overflow-x: auto;
            white-space: nowrap; /* Empêche le retour à la ligne des éléments */
        }

        .genre-container {
            border: 1px solid #ccc;
            margin: 10px;
            padding: 10px;
            background-color: #ffffff;
            width: 300px;
            text-align: center;
        }

        .genre-image {
            max-width: 100%;
            height: auto;
        }

        .view-genre-button {
            margin-top: 10px;
            background-color: #2196F3;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        h1{
            color: white;
        }

        .login-button,
        .logout-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .image-playlists{
            max-width: 20%;
            height: auto;
        }
    </style>
</head>
<body>
<div class="genre-container">
    <p><?php echo $utilisateur_connecte->getNomUtilisateur(); ?></p>
    <p><?php echo $utilisateur_connecte->getMailUtilisateur(); ?></p>
    <p><?php echo $utilisateur_connecte->getMdp(); ?></p>
</div>
</body>
</html>