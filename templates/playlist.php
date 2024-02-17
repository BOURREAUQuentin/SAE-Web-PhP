<?php
use Modele\modele_bd\PlaylistPDO;
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$playlistPDO = new PlaylistPDO($pdo);
$musiquePDO = new MusiquePDO($pdo);
$imagePDO = new ImagePDO($pdo);
$utilisateurPDO = new UtilisateurPDO($pdo);

// Récupération de l'id de l'album
$id_playlist = intval($_GET['id_playlist']);
$playlist = $playlistPDO->getPlaylistByIdPlaylist($id_playlist);
$musiques_playlist = $playlistPDO->getMusiquesByIdPlaylist($id_playlist);
$duree_playlist = $playlistPDO->getDureeTotalByIdPlaylist($id_playlist);
$image_playlist = $imagePDO->getImageByIdImage($playlist->getIdImage());
$image_path = $image_playlist->getImage() ? "../images/" . $image_playlist->getImage() : '../images/default.jpg';

$nom_utilisateur_connecte = "pas connecté";
$est_admin = false;
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $est_admin = ($utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte))->isAdmin();
}
$utilisateur_connecte = $utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lavound</title>
    <link rel="stylesheet" href="../static/style/playlist.css">
</head>
<!-- Obligé de mettre ce style en dur (pas dans un fichier css car on veut récupérer l'image de l'album actuel) -->
<style>
    .sticky {
        position: sticky;
        top: 0;
        z-index: 1000; 
        background-image: url(<?php echo $image_path; ?>);
        background-size: cover;
        background-position: center;
        width: 100%;
        height: 10%;
    }
</style>
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
                <!-- genres -->
            <div class="center-part">
            <div class="sticky">
                <div class="top">
                    <div class="infos">
                        <p class="nomart">Durée : <?php echo $duree_playlist; ?></p>
                        <p class="nomart"><?php echo count($musiques_playlist); ?> titres</p>
                    </div>
                    <img src="../images/<?php echo $image_path; ?>" alt="" height="200" width="200" class="imgart">
                    <div class="art">
                        <h2 class="nomart"><?php echo $playlist->getNomPlaylist(); ?></h2>
                        <p class="desc">Par : <?php echo $nom_utilisateur_connecte; ?></p>
                    </div>
            </div>
            </div>
            <div class='main-table-container'>
                <div>
                  <table>
                    <tbody>
                      <tr>
                        <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                        <td>En vrai</td>
                         <td>Fave</td>
                         <td>3:10</td>
                         <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/croix.png" alt="" width="15" height="15"></button></div></td>
                      </tr>
                      <tr>
                        <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                        <td>En vrai</td>
                         <td>Fave</td>
                         <td>3:10</td>
                         <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/croix.png" alt="" width="15" height="15"></button></div></td>
                      </tr>
                      <tr>
                        <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                        <td>En vrai</td>
                         <td>Fave</td>
                         <td>3:10</td>
                         <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/croix.png" alt="" width="15" height="15"></button></div></td>
                      </tr>
                      <tr>
                        <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                        <td>En vrai</td>
                         <td>Fave</td>
                         <td>3:10</td>
                         <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/croix.png" alt="" width="15" height="15"></button></div></td>
                      </tr>
                      <tr>
                        <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                        <td>En vrai</td>
                         <td>Fave</td>
                         <td>3:10</td>
                         <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/croix.png" alt="" width="15" height="15"></button></div></td>
                      </tr>
                      <tr>
                        <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                        <td>En vrai</td>
                         <td>Fave</td>
                         <td>3:10</td>
                         <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/croix.png" alt="" width="15" height="15"></button></div></td>
                      </tr>
                      <tr>
                        <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                        <td>En vrai</td>
                         <td>Fave</td>
                         <td>3:10</td>
                         <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/croix.png" alt="" width="15" height="15"></button></div></td>
                      </tr>
                      <tr>
                        <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                        <td>En vrai</td>
                         <td>Fave</td>
                         <td>3:10</td>
                         <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/croix.png" alt="" width="15" height="15"></button></div></td>
                      </tr>
                      <tr>
                        <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                        <td>En vrai</td>
                         <td>Fave</td>
                         <td>3:10</td>
                         <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/croix.png" alt="" width="15" height="15"></button></div></td>
                      </tr>
                      <tr>
                        <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                        <td>En vrai</td>
                         <td>Fave</td>
                         <td>3:10</td>
                         <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/croix.png" alt="" width="15" height="15"></button></div></td>
                      </tr>
                      <tr>
                        <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                        <td>En vrai</td>
                         <td>Fave</td>
                         <td>3:10</td>
                         <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/croix.png" alt="" width="15" height="15"></button></div></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                
              </div>
		</main>

	</section>
	<script src="../static/script/search.js"></script>
</body>
</html>