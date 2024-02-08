<?php
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$musiquePDO = new MusiquePDO($pdo);
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

// Récupération de la liste des musiques
$les_musiques = $musiquePDO->getMusiques();
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

        .musique-container {
            border: 1px solid #ccc;
            margin: 10px;
            padding: 5px;
            background-color: #ffffff;
            text-align: center;
            display: flex; /* Utilisation de flexbox pour aligner les éléments sur la même ligne */
            align-items: center; /* Alignement vertical */
        }

        .musique-container > * {
            margin-inline: auto;
        }

        .musique-image {
            width: 10%;
            height: auto;
        }

        .view-musique-button {
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
<?php foreach ($les_musiques as $musique):
    $id_image_musique = $musiquePDO->getIdImageByIdMusique($musique->getIdMusique());
    $image_musique = $imagePDO->getImageByIdImage($id_image_musique);
    $image_path = $image_musique->getImage() ? "../images/" . $image_musique->getImage() : '../images/default.jpg';
    ?>
    <div class="musique-container">
        <img class="musique-image" src="<?php echo $image_path ?>" alt="Image de la musique <?php echo $musique->getNomMusique(); ?>"/>
        <p>Nom : <?php echo $musique->getNomMusique(); ?></p>
        <p>Durée : <?php echo $musique->getDureeMusique(); ?></p>
        <p>Id Album : <?php echo $musique->getIdAlbum(); ?></p>
        <a href="/?action=musique&id_musique=<?php echo $musique->getIdMusique(); ?>">
            <button class="view-musique-button">Voir la musique</button>
        </a>
    </div>
<?php endforeach; ?>
</body>
</html>