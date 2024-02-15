<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\GenrePDO;
use Modele\modele_bd\PlaylistPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$albumPDO = new AlbumPDO($pdo);
$imagePDO = new ImagePDO($pdo);
$artistePDO = new ArtistePDO($pdo);
$musiquePDO = new MusiquePDO($pdo);
$genrePDO = new GenrePDO($pdo);
$playlistPDO = new PlaylistPDO($pdo);

// Récupération de l'id de l'album
$id_genre = intval($_GET['id_genre']);
$genre = $genrePDO->getGenreByIdGenre($id_genre);
$les_albums_genre = $albumPDO->getAlbumsByIdGenre($id_genre);
$les_musiques_genre = $musiquePDO->getMusiquesByIdGenre($id_genre);
$les_artistes_genre = $artistePDO->getArtistesByIdGenre($id_genre);

$nom_utilisateur_connecte = "pas connecté";
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
}
$playlists_utilisateur = $playlistPDO->getPlaylistsByNomUtilisateur($nom_utilisateur_connecte);
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
    <h2>Liste des albums de <?php echo $genre->getNomGenre() ?></h2>
    <div class="genre-list">
        <?php if (!empty($les_albums_genre)): ?>
            <div class="genre-container">
                <?php foreach ($les_albums_genre as $album_genre):
                    $image_album = $imagePDO->getImageByIdImage($album_genre->getIdImage());
                    $image_path_album = $image_album->getImage() ? "../static/images/" . urlencode($image_album->getImage()) : '../static/images/default.jpg';
                    ?>
                    <p>Titre : <?php echo $album_genre->getTitre(); ?></p>
                    <img class="genre-image" src="<?php echo $image_path_album ?>" alt="Image de l'album <?php echo $album_genre->getTitre(); ?>"/>
                    <a href="/?action=album&id_album=<?php echo $album_genre->getIdAlbum(); ?>">
                        <button class="view-genre-button">Voir l'album</button>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucun album disponible.</p>
        <?php endif; ?>
    </div>
    <h2>Liste des musiques de <?php echo $genre->getNomGenre() ?></h2>
    <div class="genre-list">
        <?php if (!empty($les_musiques_genre)): ?>
            <div class="genre-container">
                <?php foreach ($les_musiques_genre as $musique_genre):
                $id_image_musique = $musiquePDO->getIdImageByIdMusique($musique_genre->getIdMusique());
                $image_musique = $imagePDO->getImageByIdImage($id_image_musique);
                $image_path_musique = $image_musique->getImage() ? "../static/images/" . $image_musique->getImage() : '../static/images/default.jpg';
                ?>
                <p>Nom : <?php echo $musique_genre->getNomMusique(); ?></p>
                <img class="genre-image" src="<?php echo $image_path_musique ?>" alt="Image de la musique <?php echo $musique_genre->getNomMusique(); ?>"/>
                <a href="/?action=musique&id_musique=<?php echo $musique_genre->getIdMusique(); ?>">
                    <button class="view-genre-button">Voir la musique</button>
                </a>

                <!-- formulaire pour choisir la playlist dans laquelle ajouter la musique-->
                <?php if (count($playlists_utilisateur) > 0): ?>
                    <form method="post" action="?action=ajouter_playlist">
                        <input type="hidden" name="id_musique" value="<?php echo $musique_genre->getIdMusique(); ?>">
                        <select name="id_playlist">
                            <?php foreach ($playlists_utilisateur as $playlist): ?>
                                <option value="<?php echo $playlist->getIdPlaylist(); ?>"><?php echo $playlist->getNomPlaylist(); ?></option>
                            <?php endforeach; ?>
                        </select>
                            <button type="submit">Ajouter à la playlist</button>
                </form>
                <?php else: ?>
                    <p>Vous n'avez pas de playlists.</p>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucune musique disponible.</p>
        <?php endif; ?>
    </div>
    <h2>Liste des artistes de <?php echo $genre->getNomGenre() ?></h2>
    <div class="genre-list">
        <?php if (!empty($les_artistes_genre)): ?>
            <div class="genre-container">
                <?php foreach ($les_artistes_genre as $artiste_genre):
                $image_artiste = $imagePDO->getImageByIdImage($artiste_genre->getIdImage());
                $image_path_artiste = $image_artiste->getImage() ? "../static/images/" . $image_artiste->getImage() : '../static/images/default.jpg';
                ?>
                <p>Nom : <?php echo $artiste_genre->getNomArtiste(); ?></p>
                <img class="genre-image" src="<?php echo $image_path_artiste ?>" alt="Image de l'artiste <?php echo $artiste_genre->getNomArtiste(); ?>"/>
                <a href="/?action=artiste&id_artiste=<?php echo $artiste_genre->getIdArtiste(); ?>">
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