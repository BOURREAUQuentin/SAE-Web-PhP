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

// Récupération de la liste des albums, des genres, des artistes, des filtres par année
$les_albums = $albumPDO->getAlbums();
$les_genres = $genrePDO->getGenres();
$les_artistes = $artistePDO->getArtistes();
$les_filtres_annees = array("1970", "1980", "1990", "2000", "2010", "2020");
?>
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
            <form method="GET" action="">
                <div class="search-box">
                    <input type="hidden" name="action" value="rechercher_requete">
                    <input type="text" id="search-input" class="search-input" name="search_query" placeholder="Albums, Artistes...">
                    <button class="search-button">Go</button>
                </div>
                <!-- Sélecteur de genre -->
                <select class="search-select" name="genre" id="genre">
                    <option value="0">Tous les genres</option>
                    <?php foreach ($les_genres as $genre): ?>
                        <option value="<?php echo $genre->getIdGenre(); ?>"><?php echo $genre->getNomGenre(); ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- Sélecteur d'année -->
                <select class="search-select" name="annee" id="annee">
                    <option value="0">Toutes les années</option>
                    <?php foreach ($les_filtres_annees as $filtre_annee): ?>
                        <option value="<?php echo $filtre_annee; ?>"><?php echo $filtre_annee; ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        <button class="croix-button" onclick="hideSearchBar()"><img class="croix" src="../static/images/croix.png" alt=""></button>
        </div>
            <div></div>
            </header>
            <div class="center-part">
                <h3 class="T-part">Nouveau album</h3>
                <div class="album-container">
                    <!-- Formulaire pour ajouter un nouvel album -->
                    <form action="?action=ajouter_album" method="post" enctype="multipart/form-data">
                        <div class="form-ajout">
                            <div class="container">
                                <div class="header">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> 
                                    <path d="M7 10V9C7 6.23858 9.23858 4 12 4C14.7614 4 17 6.23858 17 9V10C19.2091 10 21 11.7909 21 14C21 15.4806 20.1956 16.8084 19 17.5M7 10C4.79086 10 3 11.7909 3 14C3 15.4806 3.8044 16.8084 5 17.5M7 10C7.43285 10 7.84965 10.0688 8.24006 10.1959M12 12V21M12 12L15 15M12 12L9 15" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg> <p>Parcourir l'image à télécharger</p>
                                    <img id="preview-image" src="#" alt="Preview Image" style="display: none;">
                                </div>
                                <label for="file" class="footer"> 
                                    <svg fill="#000000" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M15.331 6H8.5v20h15V14.154h-8.169z"></path><path d="M18.153 6h-.009v5.342H23.5v-.002z"></path></g></svg> 
                                    <p id="upload-text">Pas d'image sélectionnée</p> 
                                </label>
                                <input id="file" name="image_album" type="file" accept="image/jpeg"> 
                            </div>
                            <div class="infos-new-artiste">
                                <div class="flex-container">
                                    <div class="flex-item">
                                        <div class="input-simple">
                                            <label for="nom_album">Nom de l'album :</label>
                                            <input class="input-infos" type="text" id="nom_album" name="nom_album" required>
                                        </div>
                                        <div class="input-simple">
                                            <label for="annee_sortie">Année de sortie :</label>
                                            <input class="input-infos" type="text" id="annee_sortie" name="annee_sortie" required>
                                        </div>
                                    </div>
                                    <div class="flex-item">
                                        <div class="input-simple">
                                            <label for="genre">Genres associés :</label>
                                            <select name="genres[]" id="genre" multiple required>
                                                <?php foreach ($les_genres as $genre): ?>
                                                    <option value="<?php echo $genre->getIdGenre(); ?>"><?php echo $genre->getNomGenre(); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="input-simple">
                                            <label for="artiste">Artiste associé :</label>
                                            <select name="artiste" id="artiste">
                                                <?php foreach ($les_artistes as $artiste): ?>
                                                    <option value="<?php echo $artiste->getIdArtiste(); ?>"><?php echo $artiste->getNomArtiste(); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button class="view-album-button" type="submit">Ajouter un album</button>
                            </div>
                        </div>
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
                        <form id="editForm_<?php echo $album->getIdAlbum(); ?>" class="edit-form" style="display: none;" action="/?action=modifier_album&id_album=<?php echo $album->getIdAlbum(); ?>" method="post">
                            <div class="input-container">
                                <label for="nouveau_titre">Nouveau titre de l'album :</label>
                                <input class="input-infos" type="text" id="nouveau_titre" name="nouveau_titre" value="<?php echo $album->getTitre(); ?>" required>
                            </div>
                            <div class="input-container">
                                <label for="nouvelle_annee_sortie">Nouvelle année de sortie :</label>
                                <input class="input-infos" type="text" id="nouvelle_annee_sortie" name="nouvelle_annee_sortie" value="<?php echo $album->getAnneeSortie(); ?>" required>
                            </div>
                            <div class="button-container">
                                <button class="view-album-button" type="submit">Modifier</button>
                                <button class="view-album-button" type="button" onclick="cancelEdit(<?php echo $album->getIdAlbum(); ?>)">Annuler</button>
                            </div>
                            <input type="hidden" name="id_album" value="<?php echo $album->getIdAlbum(); ?>">
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
	</section>
    <script src="../static/script/search.js"></script>
    <script src="../static/script/admin_album.js"></script>
</body>
</html>