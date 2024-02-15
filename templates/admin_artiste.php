<?php
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$artistePDO = new ArtistePDO($pdo);
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

// Récupération de la liste des artistes
$les_artistes = $artistePDO->getArtistes();
?>
<script>
    function confirmSuppressionArtiste(id_artiste) {
        // Affiche une boîte de dialogue de confirmation
        console.log(id_artiste);
        var confirmation = confirm("Êtes-vous sûr de vouloir supprimer l'artiste ?");
        // Si l'utilisateur clique sur OK, retourne true (continue la suppression)
        // Sinon, retourne false (arrête la suppression)
        if (confirmation) {
            window.location.href = "?action=supprimer_artiste&id_artiste=" + id_artiste;
        }
        return false;
    }


    function showEditForm(artisteId) {
        // Récupérer le formulaire de modification correspondant à l'ID de l'artiste
        var editForm = document.getElementById("editForm_" + artisteId);
        // Afficher le formulaire de modification en le rendant visible
        editForm.style.display = "block";
        // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
        return false;
    }

    function cancelEdit(artisteId) {
        // Récupérer le formulaire de modification correspondant à l'ID de l'artiste
        var editForm = document.getElementById("editForm_" + artisteId);
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

        .artiste-container {
            border: 1px solid #ccc;
            margin: 10px;
            padding: 5px;
            background-color: #ffffff;
            text-align: center;
            display: flex; /* Utilisation de flexbox pour aligner les éléments sur la même ligne */
            align-items: center; /* Alignement vertical */
        }

        .artiste-container > * {
            margin-inline: auto;
        }

        .artiste-image {
            width: 10%;
            height: auto;
        }

        .view-artiste-button {
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
<h1>Ajouter un artiste</h1>
<div class="artiste-container">
    <!-- Formulaire pour ajouter un nouvel artiste -->
    <form action="?action=ajouter_artiste" method="post" enctype="multipart/form-data">
        <label for="nom_artiste">Nom de l'artiste :</label>
        <input type="text" id="nom_artiste" name="nom_artiste" required>
        <label for="image_artiste">Image de l'artiste :</label>
        <img id="preview" class="artiste-image" src="#" alt="Image de l'artiste" style="display: none;">
        <input type="file" id="image_artiste" name="image_artiste" accept="image/*" required onchange="previewImage()">
        <button type="submit">Ajouter un artiste</button>
    </form>
</div>
<h1>Listes des artistes</h1>
<?php foreach ($les_artistes as $artiste):
    $image_artiste = $imagePDO->getImageByIdImage($artiste->getIdImage());
    $image_path = $image_artiste->getImage() ? "../static/images/" . $image_artiste->getImage() : '../static/images/default.jpg';
    ?>
    <div class="artiste-container">
        <img class="artiste-image" src="<?php echo $image_path ?>" alt="Image de l'artiste <?php echo $artiste->getNomArtiste(); ?>"/>
        <p>Nom : <?php echo $artiste->getNomArtiste(); ?></p>
        <a href="/?action=artiste&id_artiste=<?php echo $artiste->getIdArtiste(); ?>">
            <button class="view-artiste-button">Voir l'artiste</button>
        </a>
        <!-- Bouton de Suppression -->
        <a href="#" onclick="return confirmSuppressionArtiste(<?php echo $artiste->getIdArtiste() ?>)">
            <button class="view-artiste-button">Supprimer l'artiste</button>
        </a>
        <!-- Bouton de modification -->
        <button class="view-album-button" onclick="showEditForm(<?php echo $artiste->getIdArtiste(); ?>)">Modifier l'artiste</button>
        <!-- Formulaire de modification -->
        <form id="editForm_<?php echo $artiste->getIdArtiste(); ?>" style="display: none;" action="/?action=modifier_artiste&id_artiste=<?php echo $artiste->getIdArtiste() ?>" method="post">
            <input type="hidden" name="id_artiste" value="<?php echo $artiste->getIdArtiste() ?>">
            <label for="nouveau_nom">Nom de l'artiste :</label>
            <input type="text" id="nouveau_nom" name="nouveau_nom" value="<?php echo $artiste->getNomArtiste(); ?>" required>
            <button class="view-album-button" type="submit">Modifier</button>
            <!-- Bouton Annuler -->
            <button class="view-album-button" type="button" onclick="cancelEdit(<?php echo $artiste->getIdArtiste(); ?>)">Annuler</button>
        </form>
    </div>
<?php endforeach; ?>


<script>
    function previewImage() {
        var fileInput = document.getElementById('image_artiste');
        var preview = document.getElementById('preview');

        // Vérifie si un fichier a été sélectionné
        if (fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'inline';
            }

            reader.readAsDataURL(fileInput.files[0]);
        }
    }
</script>
</body>
</html>