<?php
use Modele\modele_bd\GenrePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\PlaylistPDO;
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$genrePDO = new GenrePDO($pdo);
$imagePDO = new ImagePDO($pdo);
$playlistPDO = new PlaylistPDO($pdo);
$utilisateurPDO = new utilisateurPDO($pdo);

// récupération de l'utilisateur connecté et s'il est admin
$nom_utilisateur_connecte = "pas connecté";
$est_admin = false;
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $est_admin = ($utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte))->isAdmin();
}

// Récupération de la liste des genres
$les_genres = $genrePDO->getGenres();
$les_playlists_utilisateur = $playlistPDO->getPlaylistsByNomUtilisateur($nom_utilisateur_connecte);
$les_filtres_annees = array("1970", "1980", "1990", "2000", "2010", "2020");
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
<?php if ($est_admin) : ?>
    <a href="/?action=admin">
        <button class="login-button">Admin</button>
    </a>
<?php endif; ?>
<?php if (isset($_SESSION["username"])) : ?>
    <h1>Bienvenue <?php echo $nom_utilisateur_connecte ?> !</h1>
    <form method="post" action="?action=logout">
        <button class="logout-button" type="submit">Logout</button>
    </form>
    <?php if (empty($les_playlists_utilisateur)) : ?>
        <h2>Vous n'avez pas encore de playlists.</h2>
    <?php else : ?>
        <?php foreach ($les_playlists_utilisateur as $playlist_utilisateur):
            $image_playlist = $imagePDO->getImageByIdImage($playlist_utilisateur->getIdImage());
            $image_path_playlist = $image_playlist->getImage() ? "../images/" . $image_playlist->getImage() : '../images/default.jpg';
            ?>
            <div class="genre-container">
                <p><?php echo $playlist_utilisateur->getNomPlaylist(); ?></p>
                <img class="image-playlists" src="<?php echo $image_path_playlist ?>" alt="Image de la playlist <?php echo $playlist_utilisateur->getNomPlaylist(); ?>"/>
                <a href="/?action=playlist&id_playlist=<?php echo $playlist_utilisateur->getIdPlaylist(); ?>">
                    <button class="view-genre-button">Voir la playlist</button>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <h2>Ajout d'une nouvelle playlist</h2>
    <form method="post" action="?action=creer_playlist" enctype="multipart/form-data">
        <label for="nom_playlist">Nom de la playlist :</label>
        <input type="text" id="nom_playlist" name="nom_playlist" required>
        
        <label for="image_playlist">Image de la playlist :</label>
        <input type="file" id="image_playlist" name="image_playlist" accept="image/*" required>

        <button type="submit">Créer la playlist</button>
    </form>
<?php else : ?>
    <a href="?action=connexion_inscription">
        <button class="login-button">Login</button>
    </a>
<?php endif; ?>

<div class="genre-list">
    <?php foreach ($les_genres as $genre):
        $image_genre = $imagePDO->getImageByIdImage($genre->getIdImage());
        $image_path = $image_genre->getImage() ? "../images/" . $image_genre->getImage() : '../images/default.jpg';
        ?>
        <div class="genre-container">
            <p>Nom : <?php echo $genre->getNomGenre(); ?></p>
            <img class="genre-image" src="<?php echo $image_path ?>" alt="Image du genre <?php echo $genre->getNomGenre(); ?>"/>
            <a href="/?action=genre&id_genre=<?php echo $genre->getIdGenre(); ?>">
                <button class="view-genre-button">Voir le genre</button>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="genre-list">
    <?php foreach ($les_filtres_annees as $filtre_annee):?>
        <div class="genre-container">
            <p>Année <?php echo $filtre_annee; ?></p>
            <a href="/?action=filtre_annee&annee=<?php echo $filtre_annee; ?>">
                <button class="view-genre-button">Voir le filtre</button>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<form method="GET" action="">
    <input type="hidden" name="action" value="rechercher_requete">
    <input type="text" name="search_query" placeholder="Albums, Artistes...">
    <button type="submit">Rechercher</button>
</form>
</body>
</html>