<?php
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\GenrePDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$musiquePDO = new MusiquePDO($pdo);
$imagePDO = new ImagePDO($pdo);
$utilisateurPDO = new utilisateurPDO($pdo);
$albumPDO = new AlbumPDO($pdo);
$genrePDO = new GenrePDO($pdo);

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

// Récupération de la liste des genres et des filtres par années
$les_genres = $genrePDO->getGenres();
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
                    <li>
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
                    <li class="active">
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
                <h3 class="T-part">Nouvelle musique</h3>
                <div class="album-container">
                    <!-- Formulaire pour ajouter une nouvelle musique -->
                    <form action="?action=ajouter_musique" method="post" enctype="multipart/form-data">
                        <div class="form-ajout">
                            <div class="infos-new-artiste">
                                <div class="flex-container">
                                    <div class="flex-item">
                                        <div class="input-simple">
                                            <label for="nom_musique">Nom de la musique :</label>
                                            <input class="input-infos" type="text" id="nom_musique" name="nom_musique" required>
                                        </div>
                                    </div>
                                    <div class="flex-item">
                                        <div class="input-simple">
                                            <label for="album">Album associé :</label>
                                            <select name="album" id="album">
                                            <?php foreach ($les_albums as $album): ?>
                                                <option value="<?php echo $album->getIdAlbum(); ?>"><?php echo $album->getTitre(); ?></option>
                                            <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex-item">
                                        <div class="input-simple">
                                            <label for="fichier_mp3">Fichier MP3 :</label>
                                            <input type="file" id="fichier_mp3" name="fichier_mp3" accept=".mp3" required>
                                            <!-- balise pour obtenir les informations sur la durée du fichier audio (pas affiché à l'utilisateur) -->
                                            <audio id="audioPreview" style="display:none;" controls></audio>
                                            <!-- pour afficher la durée du fichier audio -->
                                            <input type="hidden" id="duree_audio" name="duree_audio">
                                        </div>
                                    </div>
                                </div>
                                <button class="view-album-button" type="submit">Ajouter une musique</button>
                            </div>
                        </div>
                    </form>
                </div>
                <h3 class="T-part">Les musiques</h3>
                <?php foreach ($les_musiques as $musique):
                    $id_image_musique = $musiquePDO->getIdImageByIdMusique($musique->getIdMusique());
                    $image_musique = $imagePDO->getImageByIdImage($id_image_musique);
                    $image_path = $image_musique->getImage() ? "../images/" . $image_musique->getImage() : '../images/default.jpg';
                    ?>
                    <div class="album-container">
                        <img class="album-image" src="<?php echo $image_path ?>" alt="Image de la musique <?php echo $musique->getNomMusique(); ?>"/>
                        <div id="container_infos_album">
                            <div>
                                <p>Nom : <?php echo $musique->getNomMusique(); ?></p>
                            </div>
                            <div>
                                <p>Durée : <?php echo $musique->getDureeMusique(); ?></p>
                            </div>
                            <div>
                                <p>Nom album : <?php echo ($albumPDO->getAlbumByIdAlbum($musique->getIdAlbum()))->getTitre(); ?></p>
                            </div>
                        </div>
                        <a href="#" onclick="return confirmSuppressionMusique(<?php echo $musique->getIdMusique(); ?>)">
                            <button class="view-album-button">Supprimer la musique</button>
                        </a>
                        <!-- Bouton de modification -->
                        <button class="view-album-button" onclick="showEditForm(<?php echo $musique->getIdmusique(); ?>)">Modifier la musique</button>
                        <!-- Formulaire de modification -->
                        <form id="editForm_<?php echo $musique->getIdMusique(); ?>" class="edit-form" style="display: none;" action="/?action=modifier_musique&id_musique=<?php echo $musique->getIdMusique(); ?>" method="post">
                            <input type="hidden" name="id_musique" value="<?php echo $musique->getIdMusique(); ?>">
                            <div class="input-container">
                                <label for="nouveau_nom">Nouveau nom :</label>
                                <input class="input-infos" type="text" id="nouveau_nom" name="nouveau_nom" value="<?php echo $musique->getNomMusique(); ?>" required>
                            </div>
                            <div class="button-container">
                                <button class="view-album-button" type="submit">Modifier</button>
                                <button class="view-album-button" type="button" onclick="cancelEdit(<?php echo $musique->getIdMusique(); ?>)">Annuler</button>
                            </div>
                        </form>

                    </div>
                <?php endforeach; ?>
            </div>
        </main>
	</section>
    <script src="../static/script/search.js"></script>
    <script src="../static/script/admin_musique.js"></script>
</body>
</html>