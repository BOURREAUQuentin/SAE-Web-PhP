<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;
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
$artistePDO = new ArtistePDO($pdo);
$realiserParPDO = new RealiserParPDO($pdo);

// récupération de l'utilisateur connecté et s'il est admin
$nom_utilisateur_connecte = "pas connecté";
$est_admin = false;
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $est_admin = ($utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte))->isAdmin();
}

// Récupération du filtre année choisi
$filtre_annee = $_GET['annee'];
$les_albums_filtre_annee = $albumPDO->getAlbumsByFiltreAnnee($filtre_annee);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lavound</title>
    <link rel="stylesheet" href="../static/style/genre.css">
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
						<input id="search-input" class="search-input" type="text" placeholder="Rechercher...">
            <button class="search-button">Go</button>
        </div>
        <button class="croix-button" onclick="hideSearchBar()"><img class="croix" src="../static/images/croix.png" alt=""></button>
      </div>
        </header>
            <h2 class="titre-genre"><?php echo $filtre_annee; ?></h2>
                <!-- genres -->
            <div class="center-part">
                <h3 class="T-part">Les Albums</h3>
                <div class="album">
                    <?php if (!empty($les_albums_filtre_annee)): ?>
                        <?php foreach ($les_albums_filtre_annee as $album_filtre_annee):
                            $image_album = $imagePDO->getImageByIdImage($album_filtre_annee->getIdImage());
                            $image_path_album = $image_album->getImage() ? "../images/" . urlencode($image_album->getImage()) : '../images/default.jpg';
                            ?>
                            <div class="disc-container">
                                <div class="cover">
                                    <img src="../images/<?php echo $image_path_album; ?>" alt="Image de l'album <?php echo $album_filtre_annee->getTitre(); ?>" width="220" height="220">
                                </div>
                                <div class="cd">
                                    <p class="art-name"><?php echo ($artistePDO->getArtisteByIdArtiste(($realiserParPDO->getIdArtistesByIdAlbum($album_filtre_annee->getIdAlbum()))[0]))->getNomArtiste(); ?></p>
                                    <p class="song2"><?php echo $album_filtre_annee->getTitre(); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucun album disponible</p>
                    <?php endif; ?>
                </div>
                <?php if (!empty($les_albums_filtre_annee)): ?>
                    <button class="btn" id="buttonVoirPlus">
                        <span class="icon" id="icon">+</span>
                        </span>
                        <span class="text" id="voir">Voir plus</span>
                    </button>
                <?php endif; ?>
            </div>
    </div>   
	</main>
	</section>
    <script src="../static/script/fav.js"></script>
    <script src="../static/script/genre2.js"></script>
    <script src="../static/script/genre.js"></script>
	<script src="../static/script/search.js"></script>
</body>
</html>