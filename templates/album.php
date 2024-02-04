<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\RealiserParPDO;
use Modele\modele_bd\ArtistePDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$albumPDO = new AlbumPDO($pdo);
$imagePDO = new ImagePDO($pdo);
$realiserParPDO = new RealiserParPDO($pdo);
$artistePDO = new ArtistePDO($pdo);

// Récupération de l'id de l'album
$id_album = intval($_GET['id_album']);
$album = $albumPDO->getAlbumByIdAlbum($id_album);
$image_album = $imagePDO->getImageByIdImage($album->getIdImage());
$image_path = $image_album->getImage() ? "../images/" . $image_album->getImage() : '../images/default.jpg';
$id_artistes = $realiserParPDO->getIdArtistesByIdAlbum($id_album);
$les_artistes = array();
foreach ($id_artistes as $id_artiste){
    array_push($les_artistes, $artistePDO->getArtisteByIdArtiste($id_artiste));
}
$les_musiques = $albumPDO->getMusiquesByIdAlbum($id_album);
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
        <h2>Titre : <?php echo $album->getTitre(); ?></h2>
        <p>Année de sortie : <?php echo $album->getAnneeSortie(); ?></p>
        <p>Durée de l'album : <?php echo $albumPDO->getDureeTotalByIdAlbum($id_album); ?></p>
        <img class="album-image" src="<?php echo $image_path ?>" alt="Image de l'album <?php echo $album->getTitre(); ?>"/>
    </div>
    <div class="album-container">
        <h2>Liste des musiques de l'album</h2>
        <?php foreach ($les_musiques as $musique):?>
        <div class="musique-container">
            <p>Son : <?php echo $musique->getNomMusique(); ?></p>
            <p>Durée : <?php echo $musique->getDureeMusique(); ?></p>
            <p>Nombre d'écoutes : <?php echo $musique->getNbStreams(); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="album-container">
        <?php foreach ($les_artistes as $artiste):
        $image_artiste = $imagePDO->getImageByIdImage($artiste->getIdImage());
        $image_path_artiste = $image_artiste->getImage() ? "../images/" . $image_artiste->getImage() : '../images/default.jpg';
        ?>
        <p>Nom artiste : <?php echo $artiste->getNomArtiste(); ?></p>
        <img class="album-image" src="<?php echo $image_path_artiste ?>" alt="Image de l'artiste <?php echo $artiste->getNomArtiste(); ?>"/>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>