<?php
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\ImagePDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$artistePDO = new ArtistePDO($pdo);
$imagePDO = new ImagePDO($pdo);

// Récupération de l'id de l'artiste
$id_artiste = intval($_GET['id_artiste']);

$artiste = $artistePDO->getArtisteByIdArtiste($id_artiste);
$image = $imagePDO->getImageByIdImage($artiste->getIdImage());
$image_path = $image->getImage() ? "../static/images/" . $image->getImage() : '../static/images/default.jpg';
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
        <p>Nom artiste : <?php echo $artiste->getNomArtiste(); ?></p>
        <img class="album-image" src="<?php echo $image_path ?>" alt="Image de l'artiste <?php echo $artiste->getNomArtiste(); ?>"/>
    </div>
</div>

</body>
</html>