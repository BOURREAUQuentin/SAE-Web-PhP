<?php
use Modele\modele_bd\GenrePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$genrePDO = new GenrePDO($pdo);
$imagePDO = new ImagePDO($pdo);
$utilisateurPDO = new utilisateurPDO($pdo);

// vérification de si l'utilisateur est connecté et s'il est admin
$nom_utilisateur_connecte = "pas connecté";
$est_admin = false;
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $est_admin = ($utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte))->isAdmin();
}
if (!$est_admin) {
    // redirigez l'utilisateur vers la page de connexion (n'est pas admin)
    header('Location: ?action=connexion_inscription');
    exit();
}

// Récupération de la liste des genres
$les_genres = $genrePDO->getGenres();

$message_erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nom_genre = $_POST['nom_genre']; // On récupère le champ nom_genre

    $genre = $genrePDO->getGenreByNomGenre($nom_genre);

    if ($genre == null) { // si le genre n'existe pas déjà (le nom est pas déjà utilisé)
        // ajout du genre dans la bd
        $genrePDO->ajouterGenre($nom_genre);
        exit(header('Location: ?action=admin_genre'));
    }
    else {
        $message_erreur = "Nom de genre déjà utilisé.";
    }
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

        .genre-container {
            border: 1px solid #ccc;
            margin: 10px;
            padding: 5px;
            background-color: #ffffff;
            text-align: center;
            display: flex; /* Utilisation de flexbox pour aligner les éléments sur la même ligne */
            align-items: center; /* Alignement vertical */
        }

        .genre-container > * {
            margin-inline: auto;
        }

        .genre-image {
            width: 10%;
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
            color: #ffffff;
        }

        #container_infos_genre {
            width: 70%; /* Set a fixed width for the container (adjust as needed) */
            margin: 0 auto; /* Center the container */
            display: flex;
            align-items: center;
            justify-content: space-around;
        }

        #container_infos_genre > div {
            flex: 1; /* Equal width for each block */
            padding: 10px;
            text-align: left;
        }

        #container_infos_genre p {
            margin: 0;
        }
    </style>
</head>
<body>
<h1>Ajouter un genre</h1>
<div class="genre-container">
    <?php if (!empty($message_erreur)): ?>
        <p style="color: red;"><?php echo $message_erreur; ?></p>
    <?php endif; ?>
    <!-- Formulaire pour ajouter un nouveau genre -->
    <form action="" method="post">
        <label for="nom_genre">Nom du genre :</label>
        <input type="text" id="nom_genre" name="nom_genre" required>
        <button type="submit" value="ajouter_genre">Ajouter un genre</button>
    </form>
</div>
<h1>Listes des genres</h1>
<?php foreach ($les_genres as $genre): ?>
    <div class="genre-container">
        <div id="container_infos_genre">
            <div>
                <p>Nom : <?php echo $genre->getNomGenre(); ?></p>
            </div>
        </div>
        
        <a href="/?action=genre&id_genre=<?php echo $genre->getIdGenre(); ?>">
            <button class="view-genre-button">Voir le genre</button>
        </a>
    </div>
<?php endforeach; ?>
</body>
</html>