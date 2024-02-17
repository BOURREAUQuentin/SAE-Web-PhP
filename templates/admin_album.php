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
    <title>Lavound</title>
    <link rel="stylesheet" href="../static/style/genre.css">
    <link rel="stylesheet" href="../static/style/admin.css">
</head>
<body ng-app="app">
	<section class='global-wrapper' ng-controller="ctrl">
        <aside>
                <img src="../static/images/logo.png" alt="" width="80px" height="80px">
                <!--top nav -->
                <ul>
                    <li>
            <a href="#" onclick="toggleSearchBar()">
                <div class="nav-item">
                    <img src="../static/images/loupe.png" alt="">
                    <span>Recherche</span>
                </div>
            </a>
        </li>
                    <li class="active">
                <a href="/?action=accueil">
                    <div class="nav-item">
                            <img src="../static/images/home.png" alt="">
                            <span>Accueil</span>
                    </div>
                </a>	
            </li>
            <li>
                <a href="/?action=playlists_utilisateur">
                    <div class="nav-item">
                        <img src="../static/images/add-to-playlist.png" alt="">
                        <span>Playlist</span>
                    </div>
                </a>
                    </li>
                </ul>

                <!--bottom nav -->
                <ul>
                    <li>
                <button class="nav-item open-modal-btn">
                    <img src="../static/images/setting.png" alt="">
                    <span>Paramètres</span>
                </button>
                <div class="modal-overlay">
                    <div class="modal">
                        <div class="modal-header">
                            <h2>Paramètres</h2>
                            <button class="close-modal-btn">&times;</button>
                        </div>
                        <div class="modal-content">
                            <?php if ($est_admin) : ?>
                                <a href="/?action=admin" class="para">Admin</a>
                            <?php endif; ?>
                            <?php if (isset($_SESSION["username"])) : ?>
                                <a href="/?action=profil" class="para"><p>Mon profil</p></a>
                                <a href="/?action=logout" class="para">Déconnexion</a>
                            <?php else: ?>
                                <a href="?action=connexion_inscription" class="para">Connexion</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                    </li>
                </ul>
            </aside>
            <main id="main">
                <div id="blackout-on-hover"></div>
            <header>
                <h2>Lavound</h2>
            <div id="search-bar" class="div-top">
            <div class="search-box">
                <form method="GET" action="">
                    <input type="hidden" name="action" value="rechercher_requete">
                    <input type="text" id="search-input" class="search-input" name="search_query" placeholder="Albums, Artistes...">
                    <button class="search-button">Go</button>
                </form>
            </div>
            <button class="croix-button" onclick="hideSearchBar()"><img class="croix" src="../static/images/croix.png" alt=""></button>
            </div>
            <div></div>
            </header>
            <div class="center-part">
                <h3 class="T-part">Nouveau album</h3>
                <div class="album-container">
                    <!-- Formulaire pour ajouter un nouvel album -->
                    <form action="?action=ajouter_album" method="post" enctype="multipart/form-data">
                        <label for="nom_album">Nom de l'album :</label>
                        <input type="text" id="nom_album" name="nom_album" required>
                        <label for="annee_sortie">Année de sortie :</label>
                        <input type="text" id="annee_sortie" name="annee_sortie" required>
                        <label for="genre">Genres associés :</label>
                        <select name="genres[]" id="genre" multiple required>
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
                <h3 class="T-part">Les albums</h3>
                <?php foreach ($les_albums as $album):
                    $image_album = $imagePDO->getImageByIdImage($album->getIdImage());
                    $image_path = $image_album->getImage() ? "../images/" . urlencode($image_album->getImage()) : '../images/default.jpg';
                    ?>
                    <div class="album-container">
                    <img class="album-image" src="<?php echo $image_path ?>" alt="Image de l'album <?php echo $album->getTitre(); ?>"/>
                        <div id="container_infos_album">
                            <div>
                                <p>Titre : <?php echo $album->getTitre(); ?></p>
                            </div>
                            <div>
                                <p>Année de sortie : <?php echo $album->getAnneeSortie(); ?></p>
                            </div>
                            <div>
                                <p><?php echo ($artistePDO->getArtisteByIdArtiste(($realiserParPDO->getIdArtistesByIdAlbum($album->getIdAlbum()))[0]))->getNomArtiste(); ?></p>
                            </div>
                        </div>
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
            </div>
        </main>
	</section>
    <script src="../static/script/search.js"></script>
</body>
</html>