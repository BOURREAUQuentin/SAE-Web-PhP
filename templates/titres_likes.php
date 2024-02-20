<?php

use Modele\modele_bd\LikerPDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\GenrePDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$likerPDO = new LikerPDO($pdo);
$utilisateurPDO = new UtilisateurPDO($pdo);
$musiquePDO = new MusiquePDO($pdo);
$imagePDO = new ImagePDO($pdo);
$artistePDO = new ArtistePDO($pdo);
$genrePDO = new GenrePDO($pdo);

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
$utilisateur_connecte = $utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte);
$les_musiques_likes = $likerPDO->getMusiqueByUtilisateur($utilisateur_connecte->getIdUtilisateur());
$duree_titres_likes = $likerPDO->getDureeTotalByIdUtilisateur($utilisateur_connecte->getIdUtilisateur());

$file_attente_sons = array();
$id_musique_file_attente_sons = array();
foreach ($les_musiques_likes as $musique_likee){
    array_push($file_attente_sons, $musique_likee->getSonMusique());
    array_push($id_musique_file_attente_sons, $musique_likee->getIdMusique());
}
// Récupérer les musiques et les encoder en JSON
$musiques_likees_json = json_encode($file_attente_sons);
// Récupérer les id_musiques et les encoder en JSON
$id_musiques_likees_json = json_encode($id_musique_file_attente_sons);

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
    <link rel="stylesheet" href="../static/style/son.css">
    <link rel="stylesheet" href="../static/style/playlist.css">
</head>
<!-- Obligé de mettre ce style en dur) -->
<style>
    .sticky {
        position: sticky;
        top: 0;
        z-index: 1000; 
        background-image: url("../static/images/titres_likes.jpeg");
        background-size: cover;
        background-position: center;
        width: 100%;
        height: 10%;
    }

    .player .control-panel {
        position: relative;
        background-color: #fff;
        border-radius: 15px;
        width: 435px;
        height: 80px;
        z-index: 5;
        box-shadow: 0px 20px 20px 5px rgba(132, 132, 132, 0.3);
        
        .album-art {
            position: absolute;
            left: 20px;
            top: -15px;
            height: 80px;
            width: 80px;
            border-radius: 50%;
            box-shadow: 0px 0px 20px 5px rgba(0, 0, 0, 0);
            transform: scale(1);
            transition: all .5s ease;
    
            &::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 15px;
                height: 15px;
                background-color: #fff;
                border-radius: 50%;
                z-index: 5;
                transform: translate(-50%, -50%);
                -webkit-transform: translate(-50%, -50%);
            }
            
            &::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                border-radius: 50%;
                background-position: center;
                background-repeat: no-repeat;
                background-size: 80px;
                background-image: 	url("../static/images/titres_likes.jpeg");
            }
        }
        
        &.active .album-art {
            box-shadow: 0px 0px 20px 5px rgba(0, 0, 0, 0.2);
            transform: scale(1.2);
            transition: all .5s ease;
        }
        
        &.active .album-art::before {
            animation: rotation 3s infinite linear;
            -webkit-animation: rotation 3s infinite linear;
            animation-fill-mode: forwards;
        }
        
        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }
            
            100% {
                transform: rotate(360deg);
            }
        }
        
        .controls {
            display: flex;
            justify-content: flex-end;
            height: 80px;
            padding: 0 15px;
            
            .prev, 
            .play, 
            .next,
            .restart {
                width: 55px;
                height: auto;
                border-radius: 10px;
                background-position: center center;
                background-repeat: no-repeat;
                background-size: 20px;
                margin: 5px 0;
                background-color: #fff;
                cursor: pointer;
                transition: background-color .3s ease;
                -webkit-transition: background-color .3s ease;
            }
            
            .prev:hover, 
            .play:hover, 
            .next:hover,
            .restart:hover {
                background-color: #eee;
                transition: background-color .3s ease;
                -webkit-transition: background-color .3s ease;
            }
            
            .prev {
                background-image: url("../static/images/previous.png");
            }
            
            .play {
                background-image: url("../static/images/play.png");
            }
            
            .next {
                background-image: url("../static/images/next.png")
            }

            .restart {
                background-image: url("../static/images/restart.png");
            }
        }
        
        &.active .controls .play {
            background-image: url("../static/images/pause.png")
        }
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
            <div class="sticky">
                <div class="top">
                    <div class="infos">
                        <p class="nomart">Durée : <?php echo $duree_titres_likes; ?></p>
                        <p class="nomart"><?php echo count($les_musiques_likes); ?> titres</p>
                    </div>
                    <img src="../static/images/titres_likes.jpeg" alt="" height="200" width="200" class="imgart">
                    <div class="art">
                        <h2 class="nomart">Titres likés</h2>
                        <p class="desc">Par : <?php echo $nom_utilisateur_connecte; ?></p>
                    </div>
            </div>
            </div>
            <div class='main-table-container'>
                <div>
                  <table>
                    <tbody>
                        <tr>
                            <th>Play</th>
                            <th>Nom</th>
                            <th>Artiste</th>
                            <th>Durée</th>
                            <th>Nombre de streams</th>
                            <th>Enlever</th>
                        </tr>
                        <?php foreach($les_musiques_likes as $musique): 
                        $artiste_musique = $artistePDO->getArtisteByIdMusique($musique->getIdMusique()); // récupération de l'artiste ayant réalisé la musique
                        ?>
                            <tr>
                                <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                                <td><?php echo $musique->getNomMusique(); ?></td>
                                <td><?php echo $artiste_musique->getNomArtiste(); ?></td>
                                <td><?php echo $musique->getDureeMusique(); ?></td>
                                <td><?php echo $musique->getNbStreams(); ?></td>
                                <td class="first"><div class='icon-text'><a href="/?action=supprimer_musique_likee&id_musique=<?php echo $musique->getIdMusique(); ?>&id_utilisateur=<?php echo $utilisateur_connecte->getIdUtilisateur(); ?>"><button class="play" ><img src="../static/images/croix.png" alt="" width="15" height="15"></button></a></div></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="lecteur">
                <div class="player">
                    <div id="info" class="info">
                        <span class="name"></span>
                        <span class="artist">Titres likés</span>
                        <div class="progress-bar">
                            <div class="bar">
                                <audio id="audio" controls style="display: none;">
                                <source src="" type="audio/mp3">
                                Your browser does not support the audio element.
                                </audio>
                            </div>
                        </div>
                    </div>
                    <div id="control-panel" class="control-panel">
                        <div class="album-art"></div>
                        <div class="controls">
                            <div class="duration">
                                <div>
                                    <span id="current-time">00:00</span> / <span id="total-time">00:00</span>
                                    <div>
                                        <label for="volume-slider" class="volume-label">Volume</label>
                                        <input type="range" id="volume-slider" min="0" max="100" step="1" value="100">
                                    </div>
                                </div>
                            </div>
                            <div id="prev" class="prev"></div>
                            <div id="play" class="play"></div>
                            <div id="next" class="next"></div>
                            <div id="restart" class="restart"></div>
                        </div>
                    </div>
                </div>
                <ul id="file-attente"></ul>
            </div>
		</main>
	</section>
	<script src="../static/script/search.js"></script>
    <script>
        // Injecter les données JSON dans une variable JavaScript
        const musiques = <?php echo $musiques_likees_json; ?>;
        // Injecter les données JSON dans une variable JavaScript
        const id_musiques = <?php echo $id_musiques_likees_json; ?>;

        const utilisateur_est_connecte = <?php echo isset($utilisateur_connecte) ? 'true' : 'false'; ?>;
    </script>
    <script src="../static/script/son.js"></script>
    <script src="../static/script/likes.js"></script>
</body>
</html>