<?php
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\AlbumPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$musiquePDO = new MusiquePDO($pdo);
$imagePDO = new ImagePDO($pdo);
$utilisateurPDO = new utilisateurPDO($pdo);
$albumPDO = new AlbumPDO($pdo);

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

// Récupération de la liste des musiques et albums
$les_musiques = $musiquePDO->getMusiques();
$les_albums = $albumPDO->getAlbums();
?>
<script>
    function confirmSuppressionArtiste(id_musique) {
        // Affiche une boîte de dialogue de confirmation
        var confirmation = confirm("Êtes-vous sûr de vouloir supprimer cette musique ?");
        // Si l'utilisateur clique sur OK, retourne true (continue la suppression)
        // Sinon, retourne false (arrête la suppression)
        if (confirmation) {
            window.location.href = "?action=supprimer_musique&id_musique=" + id_musique;
        }
        return false;
    }
    function showEditForm(id_musique) {
        // Récupérer le formulaire de modification correspondant à l'ID de la musique
        var editForm = document.getElementById("editForm_" + id_musique);
        // Afficher le formulaire de modification en le rendant visible
        editForm.style.display = "block";
        // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
        return false;
    }
    function cancelEdit(id_musique) {
        // Récupérer le formulaire de modification correspondant à l'ID de la musique
        var editForm = document.getElementById("editForm_" + id_musique);
        // Masquer le formulaire de modification
        editForm.style.display = "none";

        // Récupérer le champ de saisie correspondant
        var nomMusiqueInput = document.getElementById("nouveau_nom_musique" + id_musique);

        // Masquer le champ de saisie correspondant
        nomMusiqueInput.style.display = "none";
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
<h1>Ajouter une musique</h1>
<div class="musique-container">
    <!-- Formulaire pour ajouter une nouvelle musique -->
    <form action="?action=ajouter_musique" method="post" enctype="multipart/form-data">
        <label for="nom_musique">Nom de la musique :</label>
        <input type="text" id="nom_musique" name="nom_musique" required>
        <label for="duree_musique">Durée du son :</label>
        <input type="time" id="duree_musique" name="duree_musique" required>
        <label for="album">Album associé :</label>
        <select name="album" id="album">
        <?php foreach ($les_albums as $album): ?>
            <option value="<?php echo $album->getIdAlbum(); ?>"><?php echo $album->getTitre(); ?></option>
        <?php endforeach; ?>
        </select>
        <button type="submit">Ajouter une musique</button>
    </form>
</div>
<h1>Listes des musiques</h1>
<?php foreach ($les_musiques as $musique):
    $id_image_musique = $musiquePDO->getIdImageByIdMusique($musique->getIdMusique());
    $image_musique = $imagePDO->getImageByIdImage($id_image_musique);
    $image_path = $image_musique->getImage() ? "../images/" . $image_musique->getImage() : '../images/default.jpg';
    ?>
    <div class="musique-container">
        <img class="musique-image" src="<?php echo $image_path ?>" alt="Image de la musique <?php echo $musique->getNomMusique(); ?>"/>
        <p>Nom : <?php echo $musique->getNomMusique(); ?></p>
        <p>Durée : <?php echo $musique->getDureeMusique(); ?></p>
        <p>Nom album : <?php echo ($albumPDO->getAlbumByIdAlbum($musique->getIdAlbum()))->getTitre(); ?></p>
        <a href="/?action=musique&id_musique=<?php echo $musique->getIdMusique(); ?>">
            <button class="view-musique-button">Voir la musique</button>
        </a>
        <a href="#" onclick="return confirmSuppressionArtiste(<?php echo $musique->getIdMusique(); ?>)">
            <button class="view-musique-button">Supprimer la musique</button>
        </a>
        <!-- Bouton de modification -->
        <button class="view-musique-button" onclick="showEditForm(<?php echo $musique->getIdmusique(); ?>)">Modifier la musique</button>
        <!-- Formulaire de modification -->
        <form id="editForm_<?php echo $musique->getIdMusique(); ?>" style="display: none;" action="/?action=modifier_musique&id_musique=<?php echo $musique->getIdMusique(); ?>" method="post">
            <input type="hidden" name="id_musique" value="<?php echo $musique->getIdMusique(); ?>">
            <label for="nouveau_nom">Nouveau nom de la musique :</label>
            <input type="text" id="nouveau_nom" name="nouveau_nom" value="<?php echo $musique->getNomMusique(); ?>" required>
            <button class="view-musique-button" type="submit">Modifier</button>
            <!-- Bouton Annuler -->
            <button class="view-musique-button" type="button" onclick="cancelEdit(<?php echo $musique->getIdMusique(); ?>)">Annuler</button>
        </form>
    </div>
<?php endforeach; ?>
</body>
</html>