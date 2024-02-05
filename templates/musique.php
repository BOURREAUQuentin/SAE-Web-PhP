<?php
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\ImagePDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$musiquePDO = new MusiquePDO($pdo);
$imagePDO = new ImagePDO($pdo);

// Récupération de l'id de la musique
$id_musique = intval($_GET['id_musique']);
$musique = $musiquePDO->getMusiqueByIdMusique($id_musique);
$id_image = $musiquePDO->getIdImageByIdMusique($musique->getIdMusique());
$image = $imagePDO->getImageByIdImage($id_image);
$image_path = $image->getImage() ? "../images/" . $image->getImage() : '../images/default.jpg';
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

        .album-container {
        display: flex;
        flex-direction: column; /* Définir la direction du flux en colonne */
        border: 1px solid #ccc;
        margin: 10px;
        padding: 10px;
        background-color: #ffffff;
        width: 300px;
        text-align: center;
        }

        .musique-container {
            margin-bottom: 10px; /* Espacement entre les conteneurs de musique */
        }

        .album-image {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="album-container">
        <p>Son : <?php echo $musique->getNomMusique(); ?></p>
        <p>Durée : <?php echo $musique->getDureeMusique(); ?></p>
        <p>Nombre d'écoutes : <?php echo $musique->getNbStreams(); ?></p>
        <img class="genre-image" src="<?php echo $image_path ?>" alt="Image de la musique <?php echo $musique->getNomMusique(); ?>"/>
    </div>
</div>

</body>
</html>