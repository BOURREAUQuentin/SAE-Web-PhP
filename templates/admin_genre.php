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
?>
<script>
    function confirmSuppressionGenre(id_genre) {
        // Affiche une boîte de dialogue de confirmation
        console.log(id_genre);
        var confirmation = confirm("Êtes-vous sûr de vouloir supprimer le genre ?");
        // Si l'utilisateur clique sur OK, retourne true (continue la suppression)
        // Sinon, retourne false (arrête la suppression)
        if (confirmation) {
            window.location.href = "?action=supprimer_genre&id_genre=" + id_genre;
        }
        return false;
    }

    function showEditForm(id_genre) {
        // Récupérer le formulaire de modification correspondant à l'ID du genre
        var editForm = document.getElementById("editForm_" + id_genre);
        // Afficher le formulaire de modification en le rendant visible
        editForm.style.display = "block";
        // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
        return false;
    }

    function cancelEdit(id_genre) {
        // Récupérer le formulaire de modification correspondant à l'ID du genre
        var editForm = document.getElementById("editForm_" + id_genre);
        // Masquer le formulaire de modification
        editForm.style.display = "none";
        // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
        return false;
    }
</script>

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
    </style>
</head>
<body>
<h1>Listes des genres</h1>
<?php foreach ($les_genres as $genre):
    $image_genre = $imagePDO->getImageByIdImage($genre->getIdImage());
    $image_path = $image_genre->getImage() ? "../images/" . $image_genre->getImage() : '../images/default.jpg';
    ?>
    <div class="genre-container">
        <img class="genre-image" src="<?php echo $image_path ?>" alt="Image du genre <?php echo $genre->getNomGenre(); ?>"/>
        <p>Nom : <?php echo $genre->getNomGenre(); ?></p>
        <a href="/?action=genre&id_genre=<?php echo $genre->getIdGenre(); ?>">
            <button class="view-genre-button">Voir le genre</button>
        </a>
        <!-- Bouton de Suppression -->
        <a href="#" onclick="return confirmSuppressionGenre(<?php echo $genre->getIdGenre() ?>)">
            <button class="view-genre-button">Supprimer le genre</button>
        </a>
        <!-- Bouton de modification -->
        <button class="view-genre-button" onclick="showEditForm(<?php echo $genre->getIdGenre(); ?>)">Modifier le genre</button>
        <!-- Formulaire de modification -->
        <form id="editForm_<?php echo $genre->getIdGenre(); ?>" style="display: none;" action="/?action=modifier_genre&id_genre=<?php echo $genre->getIdGenre() ?>" method="post">
            <input type="hidden" name="id_genre" value="<?php echo $genre->getIdGenre() ?>">
            <label for="nouveau_nom">Nom du genre :</label>
            <input type="text" id="nouveau_nom" name="nouveau_nom" value="<?php echo $genre->getNomGenre(); ?>" required>
            <button class="view-genre-button" type="submit">Modifier</button>
            <!-- Bouton Annuler -->
            <button class="view-genre-button" type="button" onclick="cancelEdit(<?php echo $genre->getIdGenre(); ?>)">Annuler</button>
        </form>
    </div>
<?php endforeach; ?>
</body>
</html>