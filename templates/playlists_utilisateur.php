<?php
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\PlaylistPDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\GenrePDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$imagePDO = new ImagePDO($pdo);
$playlistPDO = new PlaylistPDO($pdo);
$utilisateurPDO = new utilisateurPDO($pdo);
$genrePDO = new GenrePDO($pdo);

// récupération de l'utilisateur connecté et s'il est admin
$nom_utilisateur_connecte = "pas connecté";
$est_admin = false;
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $est_admin = ($utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte))->isAdmin();
}
else{
    // redirigez l'utilisateur vers la page de connexion
    header('Location: ?action=connexion_inscription');
    exit();
}

// Récupération de la liste des playlists
$les_playlists_utilisateur = $playlistPDO->getPlaylistsByNomUtilisateur($nom_utilisateur_connecte);

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
    <link rel="stylesheet" href="../static/style/playlists_utilisateur.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
        <li  class="active">
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
        <div class="content">
                <div class="playlist">
                <a href="/?action=titres_likes" style="text-decoration: none;">
                    <div class="card">
                        <div class="img">
                            <img src="../static/images/titres_likes.jpeg">
                            <button>
                                <svg height="16" role="img" width="16" viewBox="0 0 24 24" aria-hidden="true"><polygon points="21.57 12 5.98 3 5.98 21 21.57 12" fill="currentColor"></polygon></svg>
                            </button>
                        </div>
                        <div class="textos">
                            <h2>Favoris</h2>
                            <p>Tes musiques favorites</p>
                        </div>
                    </div>
                </a>
                </div>
                <?php if (empty($les_playlists_utilisateur)) : ?>
                    <h2>Vous n'avez pas encore de playlists</h2>
                <?php else : ?>
                    <?php foreach ($les_playlists_utilisateur as $playlist_utilisateur):
                        $image_playlist = $imagePDO->getImageByIdImage($playlist_utilisateur->getIdImage());
                        $image_path_playlist = $image_playlist->getImage() ? "../images/" . $image_playlist->getImage() : '../images/default.jpg';
                        ?>
                        <div class="playlist">
                            <a href="/?action=playlist&id_playlist=<?php echo $playlist_utilisateur->getIdPlaylist(); ?>" style="text-decoration: none;">
                                <div class="card">
                                    <div class="img">
                                    <img src="../images/<?php echo $image_path_playlist; ?>">
                                    <button>
                                        <svg height="16" role="img" width="16" viewBox="0 0 24 24" aria-hidden="true"><polygon points="21.57 12 5.98 3 5.98 21 21.57 12" fill="currentColor"></polygon></svg>
                                    </button>
                                    </div>
                                    <div class="textos">
                                    <h2><?php echo $playlist_utilisateur->getNomPlaylist(); ?></h2>
                                    <p>Votre playlist <?php echo $playlist_utilisateur->getNomPlaylist(); ?></p>
                                    </div>
                                </div>
                            </a>
                            <div class="genre-container">
                                <!-- Bouton de modification -->
                                <div class="boutons">
                                    <button class="custom-btn bouton-modif" onclick="showEditForm(<?php echo $playlist_utilisateur->getIdPlaylist(); ?>)">Modifier</button>
                                    <button class="buttonadd" onclick="return confirmSuppressionPlaylist(<?php echo $playlist_utilisateur->getIdPlaylist(); ?>)">
                                        <img class="add" src="../static/images/croix.png" alt="">
                                    </button>
                                </div>
                                <!-- Formulaire de modification -->
                                <form id="editForm_<?php echo $playlist_utilisateur->getIdPlaylist(); ?>" style="display: none;" action="/?action=modifier_playlist&id_playlist=<?php echo $playlist_utilisateur->getIdPlaylist(); ?>" method="post">
                                    <input type="hidden" name="id_playlist" value="1">
                                    <div class="form-control">
                                        <input type="text" id="nouveau_nom" name="nouveau_nom" required>
                                        <label>
                                            <span style="transition-delay:0ms">N</span>
                                            <span style="transition-delay:50ms">o</span>
                                            <span style="transition-delay:100ms">u</span>
                                            <span style="transition-delay:150ms">v</span>
                                            <span style="transition-delay:200ms">e</span>
                                            <span style="transition-delay:250ms">a</span>
                                            <span style="transition-delay:300ms">u</span>
                                            <span style="transition-delay:400ms"> </span>
                                            <span style="transition-delay:450ms">n</span>
                                            <span style="transition-delay:500ms">o</span>
                                            <span style="transition-delay:550ms">m</span>
                                            <span style="transition-delay:600ms"> </span>
                                            <span style="transition-delay:650ms">:</span>
                                        </label>
                                    </div>
                                    <button class="custom-btn bouton-modif" type="submit">valider</button>
                                    <!-- Bouton Annuler -->
                                    <button class="custom-btn bouton-modif" type="button" onclick="cancelEdit(<?php echo $playlist_utilisateur->getIdPlaylist(); ?>)">Annuler</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <!-- formadd -->
                <div class="container slider-one-active" id="container">
                <div class="steps">
                    <div class="step step-one">
                    <div class="liner"></div>
                    <span>Nom</span>
                    </div>
                    <div class="step step-two">
                    <div class="liner"></div>
                    <span>Image</span>
                    </div>
                    <div class="step step-three">
                    <div class="liner"></div>
                    <span>Validation</span>
                    </div>
                </div>
                <div class="line">
                    <div class="dot-move"></div>
                    <div class="dot zero"></div>
                    <div class="dot center"></div>
                    <div class="dot full"></div>
                </div>
                <div class="slider-ctr">
                    <div class="slider">
                    <form class="slider-form slider-one">
                        <h2>Rentrez le nom de la playlist</h2>
                        <label class="input">
                        <input type="text" id="nom_playlist" class="name" name="nom_playlist" placeholder="Nom playlist">
                        </label>
                        <button class="first next">Prochaine étape</button>
                    </form>
                    <form class="slider-form slider-two dragger padding_2x" method="post" action="/?action=creer_playlist" enctype="multipart/form-data">
                        <label class="input">
                            <input type="text" style="display: none;" id="nom_playlist form-nom-playlist" class="name" name="nom_playlist" placeholder="Nom playlist" readonly>
                        </label>
                        <input type="text" style="display: none;" id="url_image" name="url_image">
                        <div class="label-ctr">
                            <div class="border">
                                <label class="fixed_flex padding_3x">
                                    <i class="fa fa-cloud-upload"></i>
                                    <h2 class="title">Drag & Drop<br>une image</h2>
                                    <p>Accepté : png, jpg, jpeg</p>
                                    <input class="input-image" type="file" name="dragger[]" input-mode="file" accept=".png,.jpg,.jpeg" required/>
                                </label>
                            </div>
                            <ul class="file_preview"></ul>
                            <button class="second next">Prochaine étape</button>
                        </div>
                    </form>
                    <div class="slider-form slider-three">
                        <h2>Validation de la création</h2>
                        <h3>Appuyez sur le bouton ci-dessous</h3>
                        <a class="reset" href="#" target="_blank">Ajouter</a>
                        <a class="cancel" href="/?action=playlists_utilisateur">Annuler</a>
                    </div>
                    </div>
                </div>
                </div>
                <button class="buttonadd addplay" onclick="actionPopup()">
                    <img class="add" src="../static/images/add.png" alt="">
                </button>
        </main>
	</section>
	<script src="../static/script/search.js"></script>
    <script src="../static/script/image.js"></script>
    <script src="../static/script/popup.js"></script>
    <script src="../static/script/form.js"></script>
    <script src="../static/script/playlists_utilisateur.js"></script>
</body>
</html>