<?php
use Modele\modele_bd\PlaylistPDO;
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\ImagePDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$playlistPDO = new PlaylistPDO($pdo);
$musiquePDO = new MusiquePDO($pdo);
$imagePDO = new ImagePDO($pdo);

// Récupération de l'id de l'album
$id_playlist = intval($_GET['id_playlist']);
$playlist = $playlistPDO->getPlaylistByIdPlaylist($id_playlist);
$musiques_playlist = $playlistPDO->getMusiquesByIdPlaylist($id_playlist);
$image_playlist = $imagePDO->getImageByIdImage($playlist->getIdImage());
$image_path = $image_playlist->getImage() ? "../static/images/" . $image_playlist->getImage() : '../static/images/default.jpg';
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

        h2, p{
            color: #ffffff;
        }

        .image-playlist{
            max-width: 15%;
            height: auto;
        }

        .image-musique{
            max-width: 5%;
            height: auto;
        }
    </style>
</head>
<body>
<h2>Vos sons de votre playlist <?php echo $playlist->getNomPlaylist() ?></h2>
<img class="image-playlist" src="<?php echo $image_path ?>" alt="Image de la playlist <?php echo $playlist->getNomPlaylist(); ?>"/>
<div class="genre-list">
    <?php if (!empty($musiques_playlist)): ?>
        <div class="genre-container">
            <?php foreach ($musiques_playlist as $musique_playlist):
                $id_image_musique = $musiquePDO->getIdImageByIdMusique($musique_playlist->getIdMusique());
                $image_musique = $imagePDO->getImageByIdImage($id_image_musique);
                $image_path_musique = $image_musique->getImage() ? "../static/images/" . $image_musique->getImage() : '../static/images/default.jpg';
                ?>
                <p>Son : <?php echo $musique_playlist->getNomMusique(); ?></p>
                <p>Durée : <?php echo $musique_playlist->getDureeMusique(); ?></p>
                <p>Nombre d'écoutes : <?php echo $musique_playlist->getNbStreams(); ?></p>
                <img class="image-musique" src="<?php echo $image_path_musique ?>" alt="Image de la musique <?php echo $musique_playlist->getNomMusique(); ?>"/>
                
                <a href="/?action=supprimer_musique_playlist&id_musique=<?php echo $musique_playlist->getIdMusique(); ?>&id_playlist=<?php echo $id_playlist; ?>">
                    <button class="delete-button">Supprimer</button>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucune musique dans votre playlist.</p>
    <?php endif; ?>
</div>
</div>
</body>
</html>