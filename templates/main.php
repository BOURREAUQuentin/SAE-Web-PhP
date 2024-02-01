<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\ImagePDO;
use PDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$albumPDO = new AlbumPDO($pdo);
$imagePDO = new ImagePDO($pdo);

// Récupération de la liste des albums
$les_albums = $albumPDO->getAlbums();
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

        .album-list {
            display: flex;
            overflow-x: auto;
            white-space: nowrap; /* Empêche le retour à la ligne des éléments */
        }

        .album-container {
            border: 1px solid #ccc;
            margin: 10px;
            padding: 10px;
            background-color: #ffffff;
            width: 300px;
            text-align: center;
        }

        .album-image {
            max-width: 100%;
            height: auto;
        }

        .view-album-button {
            margin-top: 10px;
            background-color: #2196F3;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="album-list">
    <?php foreach ($les_albums as $album):
        $image_album = $imagePDO->getImageByIdImage($album->getIdAlbum());
        $image_path = $image_album->getImage() ? "../images/" . $image_album->getImage() : '../images/default.jpg';
        ?>
        <div class="album-container">
            <p>Titre : <?php echo $album->getTitre(); ?></p>
            <p>Année de sortie : <?php echo $album->getAnneeSortie(); ?></p>
            <img class="album-image" src="<?php echo $image_path ?>" alt="Image de l'album <?php echo $album->getTitre(); ?>"/>
            <a href="/?action=album&id_album=<?php echo $album->getIdAlbum(); ?>">
                <button class="view-album-button">Voir l'album</button>
            </a>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>