<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\MusiquePDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$albumPDO = new AlbumPDO($pdo);
$imagePDO = new ImagePDO($pdo);
$artistePDO = new ArtistePDO($pdo);
$musiquePDO = new MusiquePDO($pdo);

// Récupération de la recherche
$intitule_recherche = $_GET['intitule_recherche'];
$les_albums_recherche = $albumPDO->getAlbumsByRecherche($intitule_recherche);
$les_musiques_recherche = $musiquePDO->getMusiquesByRecherche($intitule_recherche);
$les_artistes_recherche = $artistePDO->getArtistesByRecherche($intitule_recherche);

$nom_utilisateur_connecte = "pas connecté";
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
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
            margin-right: 10px;
        }

        .genre-image {
            max-width: 100%;
            height: auto;
        }

        h2{
            color: #ffffff;
        }
    </style>
</head>
<body>
<div class="album-container">
    <h2>Les résultats d'albums de <?php echo $intitule_recherche ?></h2>
    <div class="genre-list">
        <?php if (!empty($les_albums_recherche)): ?>
            <div class="genre-container">
                <?php foreach ($les_albums_recherche as $album_recherche):
                    $image_album = $imagePDO->getImageByIdImage($album_recherche->getIdImage());
                    $image_path_album = $image_album->getImage() ? "../images/" . $image_album->getImage() : '../images/default.jpg';
                    ?>
                    <p>Titre : <?php echo $album_recherche->getTitre(); ?></p>
                    <img class="genre-image" src="<?php echo $image_path_album ?>" alt="Image de l'album <?php echo $album_recherche->getTitre(); ?>"/>
                    <a href="/?action=album&id_album=<?php echo $album_recherche->getIdAlbum(); ?>">
                        <button class="view-genre-button">Voir l'album</button>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucun album disponible.</p>
        <?php endif; ?>
    </div>
    <h2>Les résultats des musiques de <?php echo $intitule_recherche ?></h2>
    <div class="genre-list">
        <?php if (!empty($les_musiques_recherche)): ?>
            <div class="genre-container">
                <?php foreach ($les_musiques_recherche as $musique_recherche):
                $id_image_musique = $musiquePDO->getIdImageByIdMusique($musique_recherche->getIdMusique());
                $image_musique = $imagePDO->getImageByIdImage($id_image_musique);
                $image_path_musique = $image_musique->getImage() ? "../images/" . $image_musique->getImage() : '../images/default.jpg';
                ?>
                <p>Nom : <?php echo $musique_recherche->getNomMusique(); ?></p>
                <img class="genre-image" src="<?php echo $image_path_musique ?>" alt="Image de la musique <?php echo $musique_recherche->getNomMusique(); ?>"/>
                <a href="/?action=musique&id_musique=<?php echo $musique_recherche->getIdMusique(); ?>">
                    <button class="view-genre-button">Voir la musique</button>
                </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucune musique disponible.</p>
        <?php endif; ?>
    </div>
    <h2>Les résultats d'artistes de <?php echo $intitule_recherche ?></h2>
    <div class="genre-list">
        <?php if (!empty($les_artistes_recherche)): ?>
            <div class="genre-container">
                <?php foreach ($les_artistes_recherche as $artiste_recherche):
                $image_artiste = $imagePDO->getImageByIdImage($artiste_recherche->getIdImage());
                $image_path_artiste = $image_artiste->getImage() ? "../images/" . $image_artiste->getImage() : '../images/default.jpg';
                ?>
                <p>Nom : <?php echo $artiste_recherche->getNomArtiste(); ?></p>
                <img class="genre-image" src="<?php echo $image_path_artiste ?>" alt="Image de l'artiste <?php echo $artiste_recherche->getNomArtiste(); ?>"/>
                <a href="/?action=artiste&id_artiste=<?php echo $artiste_recherche->getIdArtiste(); ?>">
                    <button class="view-genre-button">Voir l'artiste</button>
                </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucun artiste disponible.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>