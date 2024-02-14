<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\ImagePDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$albumPDO = new AlbumPDO($pdo);
$imagePDO = new ImagePDO($pdo);

// Récupération du filtre année choisi
$filtre_annee = $_GET['annee'];
$les_albums_filtre_annee = $albumPDO->getAlbumsByFiltreAnnee($filtre_annee);
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

        .album_filtre_annee-list {
            display: flex;
            overflow-x: auto;
            white-space: nowrap; /* Empêche le retour à la ligne des éléments */
        }

        .album_filtre_annee-container {
            border: 1px solid #ccc;
            margin: 10px;
            padding: 10px;
            background-color: #ffffff;
            width: 300px;
            text-align: center;
            margin-right: 10px;
        }

        .album_filtre_annee-image {
            max-width: 100%;
            height: auto;
        }

        h2{
            color: #ffffff;
        }
    </style>
</head>
<body>
<div class="album_filtre_annee-list">
    <?php foreach ($les_albums_filtre_annee as $album_filtre_annee):
        $image_album_filtre_annee = $imagePDO->getImageByIdImage($album_filtre_annee->getIdImage());
        $image_path = $image_album_filtre_annee->getImage() ? "../static/images/" . urlencode($image_album_filtre_annee->getImage()) : '../static/images/default.jpg';
        ?>
        <div class="album_filtre_annee-container">
            <p>Nom : <?php echo $album_filtre_annee->getTitre(); ?></p>
            <img class="album_filtre_annee-image" src="<?php echo $image_path ?>" alt="Image du album_filtre_annee <?php echo $album_filtre_annee->getTitre(); ?>"/>
            <a href="/?action=album&id_album=<?php echo $album_filtre_annee->getIdAlbum(); ?>">
                <button class="view-album_filtre_annee-button">Voir l'album</button>
            </a>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>