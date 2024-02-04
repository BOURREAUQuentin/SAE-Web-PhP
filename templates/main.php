<?php
use Modele\modele_bd\GenrePDO;
use Modele\modele_bd\ImagePDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$nom_utilisateur_connecte = "pas connecté";
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
}

// Instanciation des classes PDO
$genrePDO = new GenrePDO($pdo);
$imagePDO = new ImagePDO($pdo);

// Récupération de la liste des genres
$les_genres = $genrePDO->getGenres();

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
    </style>
</head>
<body>
<h1><?php echo $nom_utilisateur_connecte ?></h1>
<?php if (isset($_SESSION["username"])) : ?>
    <form method="post" action="?action=logout">
        <button class="logout-button" type="submit">Logout</button>
    </form>
<?php else : ?>
    <a href="?action=page_connexion">
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

</body>
</html>