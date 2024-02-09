<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\GenrePDO;
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\RealiserParPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$albumPDO = new AlbumPDO($pdo);
$imagePDO = new ImagePDO($pdo);
$utilisateurPDO = new utilisateurPDO($pdo);
$genrePDO = new GenrePDO($pdo);
$artistePDO = new ArtistePDO($pdo);
$realiserParPDO = new RealiserParPDO($pdo);

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

// Récupération de la liste des albums, des genres, des artistes
$les_albums = $albumPDO->getAlbums();
$les_genres = $genrePDO->getGenres();
$les_artistes = $artistePDO->getArtistes();
?>
<script>
    function confirmSuppressionAlbum(id_album) {
        // Affiche une boîte de dialogue de confirmation
        var confirmation = confirm("Êtes-vous sûr de vouloir supprimer cet album ?");
        // Si l'utilisateur clique sur OK, retourne true (continue la suppression)
        // Sinon, retourne false (arrête la suppression)
        if (confirmation) {
            window.location.href = "?action=supprimer_album&id_album=" + id_album;
        }
        return false;
    }
    function showEditForm(albumId) {
        // Récupérer le formulaire de modification correspondant à l'ID de l'album
        var editForm = document.getElementById("editForm_" + albumId);
        // Afficher le formulaire de modification en le rendant visible
        editForm.style.display = "block";
        // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
        return false;
    }
    function cancelEdit(albumId) {
        // Récupérer le formulaire de modification correspondant à l'ID de l'album
        var editForm = document.getElementById("editForm_" + albumId);
        // Masquer le formulaire de modification
        editForm.style.display = "none";

        // Récupérer les champs de saisie correspondants
        var nomAlbumInput = document.getElementById("nouveau_nom_album_" + albumId);
        var anneeSortieInput = document.getElementById("nouvelle_annee_sortie_" + albumId);

        // Masquer les champs de saisie correspondants
        nomAlbumInput.style.display = "none";
        anneeSortieInput.style.display = "none";
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

        .album-container {
            border: 1px solid #ccc;
            margin: 10px;
            padding: 5px;
            background-color: #ffffff;
            text-align: center;
            display: flex; /* Utilisation de flexbox pour aligner les éléments sur la même ligne */
            align-items: center; /* Alignement vertical */
        }

        .album-container > * {
            margin-inline: auto;
        }

        .album-image {
            width: 10%;
            height: auto;
        }

        .view-album-button {
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
<h1>Ajouter un album</h1>
<div class="album-container">
    <!-- Formulaire pour ajouter un nouvel album -->
    <form action="?action=ajouter_album" method="post" enctype="multipart/form-data">
        <label for="nom_album">Nom de l'album :</label>
        <input type="text" id="nom_album" name="nom_album" required>
        <label for="annee_sortie">Année de sortie :</label>
        <input type="text" id="annee_sortie" name="annee_sortie" required>
        <label for="genre">Genre associé :</label>
        <select name="genre" id="genre">
        <?php foreach ($les_genres as $genre): ?>
            <option value="<?php echo $genre->getIdGenre(); ?>"><?php echo $genre->getNomGenre(); ?></option>
        <?php endforeach; ?>
        </select>
        <label for="artiste">Artiste associé :</label>
        <select name="artiste" id="artiste">
        <?php foreach ($les_artistes as $artiste): ?>
            <option value="<?php echo $artiste->getIdArtiste(); ?>"><?php echo $artiste->getNomArtiste(); ?></option>
        <?php endforeach; ?>
        </select>
        <label for="image_album">Image de l'album :</label>
        <img id="preview" class="album-image" src="#" alt="Image de l'album" style="display: none;">
        <input type="file" id="image_album" name="image_album" accept="image/*" required onchange="previewImage()">
        <button type="submit">Ajouter un album</button>
    </form>
</div>
<h1>Listes des albums</h1>
<?php foreach ($les_albums as $album):
    $image_album = $imagePDO->getImageByIdImage($album->getIdImage());
    $image_path = $image_album->getImage() ? "../images/" . $image_album->getImage() : '../images/default.jpg';
    ?>
    <div class="album-container">
    <img class="album-image" src="<?php echo $image_path ?>" alt="Image de l'album <?php echo $album->getTitre(); ?>"/>
        <p>Titre : <?php echo $album->getTitre(); ?></p>
        <p>Année de sortie : <?php echo $album->getAnneeSortie(); ?></p>
        <p><?php echo ($artistePDO->getArtisteByIdArtiste(($realiserParPDO->getIdArtistesByIdAlbum($album->getIdAlbum()))[0]))->getNomArtiste(); ?></p>
        <a href="/?action=album&id_album=<?php echo $album->getIdAlbum(); ?>">
            <button class="view-album-button">Voir l'album</button>
        </a>
        <a href="#" onclick="return confirmSuppressionAlbum(<?php echo $album->getIdAlbum(); ?>)">
            <button class="view-album-button">Supprimer l'album</button>
        </a>
        <!-- Bouton de modification -->
        <button class="view-album-button" onclick="showEditForm(<?php echo $album->getIdAlbum(); ?>)">Modifier l'album</button>
        <!-- Formulaire de modification -->
        <form id="editForm_<?php echo $album->getIdAlbum(); ?>" style="display: none;" action="/?action=modifier_album&id_album=<?php echo $album->getIdAlbum(); ?>" method="post">
            <input type="hidden" name="id_album" value="<?php echo $album->getIdAlbum(); ?>">
            <label for="nouveau_titre">Nouveau titre de l'album :</label>
            <input type="text" id="nouveau_titre" name="nouveau_titre" value="<?php echo $album->getTitre(); ?>" required>
            <label for="nouvelle_annee_sortie">Nouvelle année de sortie :</label>
            <input type="text" id="nouvelle_annee_sortie" name="nouvelle_annee_sortie" value="<?php echo $album->getAnneeSortie(); ?>" required>
            <button class="view-album-button" type="submit">Modifier</button>
            <!-- Bouton Annuler -->
            <button class="view-album-button" type="button" onclick="cancelEdit(<?php echo $album->getIdAlbum(); ?>)">Annuler</button>
        </form>
    </div>
<?php endforeach; ?>
</body>
</html>