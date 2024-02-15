<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\RealiserParPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$albumPDO = new AlbumPDO($pdo);
$imagePDO = new ImagePDO($pdo);
$artistePDO = new ArtistePDO($pdo);
$musiquePDO = new MusiquePDO($pdo);
$utilisateurPDO = new utilisateurPDO($pdo);
$realiserParPDO = new RealiserParPDO($pdo);

// récupération de l'utilisateur connecté et s'il est admin
$nom_utilisateur_connecte = "pas connecté";
$est_admin = false;
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $est_admin = ($utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte))->isAdmin();
}

// Récupération de la recherche
$intitule_recherche = $_GET['intitule_recherche'];
$les_albums_recherche = $albumPDO->getAlbumsByRecherche($intitule_recherche);
$les_musiques_recherche = $musiquePDO->getMusiquesByRecherche($intitule_recherche);
$les_artistes_recherche = $artistePDO->getArtistesByRecherche($intitule_recherche);
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
      <div></div>
        </header>
            <h2 class="titre-genre"><?php echo $intitule_recherche; ?></h2>
                <!-- genres -->
            <div class="center-part">
            <h3 class="T-part">Les Albums</h3>
            <div class="album">
                <?php if (!empty($les_albums_recherche)): ?>
                    <?php foreach ($les_albums_recherche as $album_recherche):
                        $image_album = $imagePDO->getImageByIdImage($album_recherche->getIdImage());
                        $image_path_album = $image_album->getImage() ? "../images/" . urlencode($image_album->getImage()) : '../images/default.jpg';
                        ?>
                        <div class="disc-container">
                            <a href="/?action=album&id_album=<?php echo $album_recherche->getIdAlbum(); ?>">
                                <div class="cover">
                                    <img src="../images/<?php echo $image_path_album; ?>" alt="Image de l'album <?php echo $album_recherche->getTitre(); ?>" width="220" height="220">
                                </div>
                                <div class="cd">
                                    <p class="art-name"><?php echo ($artistePDO->getArtisteByIdArtiste(($realiserParPDO->getIdArtistesByIdAlbum($album_recherche->getIdAlbum()))[0]))->getNomArtiste(); ?></p>
                                    <p class="song2"><?php echo $album_recherche->getTitre(); ?></p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun album disponible</p>
                <?php endif; ?>
            </div>
            <?php if (!empty($les_albums_recherche)): ?>
                <button class="btn" id="buttonVoirPlus">
                    <span class="icon" id="icon">+</span>
                    </span>
                    <span class="text" id="voir">Voir plus</span>
                </button>
            <?php endif; ?>
            <h3 class="T-part">Les Sons</h3>
            <div class="album">
                <?php if (!empty($les_musiques_recherche)): ?>
                    <?php foreach ($les_musiques_recherche as $musique_recherche):
                    $id_image_musique = $musiquePDO->getIdImageByIdMusique($musique_recherche->getIdMusique());
                    $image_musique = $imagePDO->getImageByIdImage($id_image_musique);
                    $image_path_musique = $image_musique->getImage() ? "../images/" . $image_musique->getImage() : '../images/default.jpg';
                    ?>
                    <div class="song">
                        <div class="disc-container2">
                            <div class="cover">
                                <img src="../images/<?php echo $image_path_musique; ?>" alt="Image de la musique <?php echo $musique_recherche->getNomMusique(); ?>" width="220" height="220">
                            </div>
                            <div class="cd">
                                <p class="art-name"><?php echo ($artistePDO->getArtisteByIdArtiste(($realiserParPDO->getIdArtistesByIdAlbum($musique_recherche->getIdAlbum()))[0]))->getNomArtiste(); ?></p>
                                <p class="song2"><?php echo $musique_recherche->getNomMusique(); ?></p>
                            </div>
                        </div>
                        <div class="buttons">
                        <button id="buttonfav" onclick="toggleBackgroundColor()">
                            <img class="fav" src="../static/images/fav.png" alt="">
                        </button>
                        <button class="buttonadd open-modal-btn2">
                            <img class="add" src="../static/images/add.png" alt="">
                        </button>
                        <div class="modal-overlay2">
                            <div class="modal2">
                                <div class="modal-header2">
                                    <h2>Playlists</h2>
                                    <button class="close-modal-btn2">&times;</button>
                                </div>
                                <div class="modal-content2">
                                    <a href="" class="para2"><p><img src="../static/images/playlist.jpg" alt="" width="30" height="30">Playlist 1</p></a>
                                    <a href="" class="para2"><p><img src="../static/images/playlist.jpg" alt="" width="30" height="30">Playlist 2</p></a>
                                    <a href="" class="para2"><p><img src="../static/images/playlist.jpg" alt="" width="30" height="30">Playlist 2</p></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune musique disponible</p>
                <?php endif; ?>
            </div>
            <?php if (!empty($les_musiques_recherche)): ?>
                <button class="btn btn2" id="buttonVoirPlus2">
                    <span class="icon" id="icon2">+</span>
                    </span>
                    <span class="text" id="voir2">Voir plus</span>
                </button>
            <?php endif; ?>
            <h3 class="T-part">Les Artistes</h3>
            <div class="album">
                <?php if (!empty($les_artistes_recherche)): ?>
                    <?php foreach ($les_artistes_recherche as $artiste_recherche):
                    $image_artiste = $imagePDO->getImageByIdImage($artiste_recherche->getIdImage());
                    $image_path_artiste = $image_artiste->getImage() ? "../images/" . $image_artiste->getImage() : '../images/default.jpg';
                    ?>
                    <div class="card disc-container3">
                        <div class="profile-pic">
                            <img src="../images/<?php echo $image_path_artiste; ?>" alt="">
                        </div>
                        <div class="bottom">
                            <div class="content">
                                <?php $les_musiques_artiste = $artistePDO->getMusiquesPlusStreamesByIdArtiste($artiste_recherche->getIdArtiste());
                                $texte_artiste_musique = "";
                                for ($i = 0; $i < count($les_musiques_artiste); $i++){
                                    if ($i < count($les_musiques_artiste) - 1) {
                                        $texte_artiste_musique = $texte_artiste_musique . ($les_musiques_artiste[$i])->getNomMusique() . ", ";
                                    }
                                    else {
                                        $texte_artiste_musique = $texte_artiste_musique . " ou encore " . ($les_musiques_artiste[$i])->getNomMusique() . ".";
                                    }
                                }
                                ?>
                                <?php if($texte_artiste_musique != ""): ?>
                                    <span class="about-me"><?php echo $artiste_recherche->getNomArtiste() . " est un artiste connu pour des sons comme " . $texte_artiste_musique; ?></span>
                                <?php else: ?>
                                    <span class="about-me"><?php echo $artiste_recherche->getNomArtiste() . " est un artiste avec aucun sons."; ?></span>
                                <?php endif; ?>
                                </div>
                            <div class="bottom-bottom">
                                <div class="social-links-container">
                                    <span class="name"><?php echo $artiste_recherche->getNomArtiste(); ?></span>
                                </div>
                                <a href="/?action=artiste&id_artiste=<?php echo $artiste_recherche->getIdArtiste(); ?>">
                                    <button class="button">Plus d'infos</button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun artiste disponible</p>
                <?php endif; ?>
        </div>
        <?php if (!empty($les_artistes_recherche)): ?>
            <button class="btn" id="buttonVoirPlus3">
                <span class="icon" id="icon3">+</span>
                </span>
                <span class="text" id="voir3">Voir plus</span>
            </button>
        <?php endif; ?>
    </div>   
	</main>
	</section>
    <script src="../static/script/fav.js"></script>
    <script src="../static/script/genre3.js"></script>
    <script src="../static/script/genre2.js"></script>
    <script src="../static/script/genre.js"></script>
	<script src="../static/script/search.js"></script>
</body>
</html>