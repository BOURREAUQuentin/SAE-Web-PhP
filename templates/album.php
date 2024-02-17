<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\LikerPDO;
use Modele\modele_bd\NoterPDO;
use Modele\modele_bd\RealiserParPDO;
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\PlaylistPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$albumPDO = new AlbumPDO($pdo);
$imagePDO = new ImagePDO($pdo);
$realiserParPDO = new RealiserParPDO($pdo);
$artistePDO = new ArtistePDO($pdo);
$likerPDO = new LikerPDO($pdo);
$utilisateurPDO = new UtilisateurPDO($pdo);
$noterPDO = new NoterPDO($pdo);
$playlistPDO = new PlaylistPDO($pdo);

// Récupération de l'id de l'album
$id_album = intval($_GET['id_album']);
$album = $albumPDO->getAlbumByIdAlbum($id_album);
$image_album = $imagePDO->getImageByIdImage($album->getIdImage());
$image_path = $image_album->getImage() ? "../images/" . urlencode($image_album->getImage()) : '../images/default.jpg';
$id_artistes = $realiserParPDO->getIdArtistesByIdAlbum($id_album);
$les_artistes = array();
foreach ($id_artistes as $id_artiste){
    array_push($les_artistes, $artistePDO->getArtisteByIdArtiste($id_artiste));
}
$les_musiques = $albumPDO->getMusiquesByIdAlbum($id_album);

$file_attente_sons = array();
$id_musique_file_attente_sons = array();
foreach ($les_musiques as $musique){
    array_push($file_attente_sons, $musique->getSonMusique());
    array_push($id_musique_file_attente_sons, $musique->getIdMusique());
}
// Récupérer les musiques et les encoder en JSON
$musiques_json = json_encode($file_attente_sons);
// Récupérer les id_musiques et les encoder en JSON
$id_musiques_json = json_encode($id_musique_file_attente_sons);

$nom_utilisateur_connecte = "pas connecté";
$est_admin = false;
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $est_admin = ($utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte))->isAdmin();

    $utilisateur = $utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte);

    $utilisteur_a_noteer = $noterPDO->getNoteByIdAlbumIdUtilisateur($id_album,$utilisateur->getIdUtilisateur());
}
$note_album = $noterPDO->getMoyenneNoteByIdAlbum($id_album);
$utilisateur = $utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte);
$playlists_utilisateur = $playlistPDO->getPlaylistsByNomUtilisateur($nom_utilisateur_connecte);
$nbNote = $noterPDO->getNbPersonneAyantNote($id_album);

// vérifie si la requête est une requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si la clé 'albumNote' existe dans $_POST, cela signifie que la note de l'album est envoyée
    if (isset($_POST['albumNote'])) {
        // Récupère les données de la requête
        $albumNote = intval($_POST['albumNote']);
        $isChecked = $_POST['isChecked'] === 'true';
        
        if ($utilisteur_a_noteer>0){
            $noterPDO->mettreAJourNote($id_album, $utilisateur->getIdUtilisateur(), $albumNote);
        }
        else {
            $noterPDO->ajouterNoter($id_album, $utilisateur->getIdUtilisateur(), $albumNote);
        }
        // envoie une réponse JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'nvMoyenne' => $noterPDO->getMoyenneNoteByIdAlbum($id_album),'nbNotes' => $noterPDO->getNbPersonneAyantNote($id_album)]);
        exit;
    }

    // Si la clé 'musiqueId' existe dans $_POST, cela signifie que le like pour une musique est envoyé
    if (isset($_POST['musiqueId'])) {
        // Récupère les données de la requête
        $musiqueId = intval($_POST['musiqueId']);
        $isChecked = $_POST['isChecked'] === 'false';

        // ajoute ou supprime le like
        if ($isChecked) {
            $likerPDO->ajouterLiker($musiqueId, $utilisateur->getIdUtilisateur());
        } 
        else {
            $likerPDO->supprimerLiker($musiqueId, $utilisateur->getIdUtilisateur());
        }

        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lavound</title>
    <link rel="stylesheet" href="../static/style/son.css">
    <link rel="stylesheet" href="../static/style/album.css">
    <link rel="stylesheet" href="../static/style/note.css">
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
                background-image: 	url('<?php echo $image_path; ?>');
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
            <div class="sticky">
                <div class="top">
                    <div class="infos">
                        <p class="nomart">Durée : <?php echo $albumPDO->getDureeTotalByIdAlbum($id_album); ?></p>
                        <p class="nomart"><?php echo count($les_musiques); ?> titres</p>
                        <p class="nomart">Année de sortie : <?php echo $album->getAnneeSortie(); ?></p>
                    </div>
                    <img src="../images/<?php echo $image_path; ?>" alt="" height="200" width="200" class="imgart">
                    <div class="art">
                        <?php $nom_artistes_album = "";
                            for ($i = 0; $i < count($les_artistes); $i++){
                                if ($i < count($les_artistes) - 1) {
                                    $nom_artistes_album = $nom_artistes_album . ($les_artistes[$i])->getNomArtiste() . ", ";
                                }
                                else {
                                    $nom_artistes_album = $nom_artistes_album . ($les_artistes[$i])->getNomArtiste();
                                }
                            }
                        ?>
                        <h2 class="nomart"><?php echo $album->getTitre(); ?></h2>
                        <p class="desc"><?php echo $nom_artistes_album; ?></p>
                    </div>
                    <div class="infos">
                        <?php if ($note_album > 0): ?>
                            <p class="moyenne-album note"><span id="moyenne-note"><?php echo $note_album; ?></span>/5</p>
                        <?php else: ?>
                            <p class="moyenne-album note"><span id="moyenne-note">Aucune note</span></p>
                        <?php endif; ?>
                        <p id="nbPersonnesNotes">Nombre de notes : <?php echo $nbNote ?></p>
                        <button class="feedback-btn">Noter cet album</button>
                    </div>
                    <div class="modal-note" style="display:none;">
                        <button class="close-note"></button>
                        <h3 class="title-note">Avez-vous aimé cet album ?</h3>
                        <form action="" class="feedback">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="score">
                                <input id="score<?php echo $i ?>" type="radio" value="<?php echo $i ?>" name="score">
                                <?php if (!isset($utilisateur) || $utilisteur_a_noteer != $i): ?>
                                    <label for="score<?php echo $i ?>"><?php echo $i ?></label>
                                <?php else: ?>
                                    <label class="active-note" for="score<?php echo $i ?>"><?php echo $i ?></label>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                        </form>
                        <div class="options">
                            <button class="cancel" type="button">Annuler</button>
                            <button class="submit" type="submit" data-album-id="<?php echo $id_album; ?>">Confirmer</button>
                        </div>
                    </div>
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
                        <th>Favoris</th>
                    </tr>
                    <?php foreach($les_musiques as $musique): ?>
                        <tr>
                            <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                            <td><?php echo $musique->getNomMusique(); ?></td>
                            <td><?php echo $nom_artistes_album; ?></td>
                            <td><?php echo $musique->getDureeMusique(); ?></td>
                            <td><?php echo $musique->getNbStreams(); ?></td>
                            <?php if (!isset($utilisateur)): ?>
                                <td class="first"><div class='icon-text'><button id="buttonfav" class="play background" value="<?php echo $musique->getIdMusique(); ?>"><img class="fav" src="../static/images/fav_noir.png" alt="" width="15" height="15"></button></div></td>
                            <?php else:
                                // Vérifie si la musique est likée par l'utilisateur connecté
                                $isLiked = $likerPDO->verifieMusiqueLiker($musique->getIdMusique(), $utilisateur->getIdUtilisateur()); ?>
                                <!-- Ajoutez la classe "background" si la musique est déjà likée -->
                                <td class="first"><div class='icon-text'><button id="buttonfav" class="play <?php echo $isLiked ? 'background' : ''; ?>" value="<?php echo $musique->getIdMusique(); ?>"><img class="fav" src="../static/images/<?php echo $isLiked ? "fav_rouge.png" : "fav_noir.png"; ?>" alt="" width="15" height="15"></button></div></td>
                            <?php endif; ?>
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
                    <span class="artist"><?php echo ($les_artistes[0])->getNomArtiste(); ?></span>
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
        // Récupère tous les éléments avec l'ID "like"
        const likeElements = document.querySelectorAll('#buttonfav');

        // Ajoute un écouteur d'événements à chaque élément
        likeElements.forEach(likeElement => {
            likeElement.addEventListener('click', async (event) => {
                // Vérifie si l'utilisateur est connecté
                if (!<?php echo isset($utilisateur) ? 'true' : 'false' ?>) {
                    // Redirige l'utilisateur vers la page de connexion
                    window.location.href = '/?action=connexion_inscription';
                    return;
                }

                const musiqueId = likeElement.value;
                const isChecked = likeElement.classList.contains('background');

                // Envoie une requête POST à la page actuelle
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        musiqueId,
                        isChecked,
                    }),
                });

                // Appeler la fonction pour mettre à jour l'image
                updateImageSource(!isChecked, likeElement);

                // Vérifie si la requête a réussi
                if (response.ok) {
                    console.log('Like ajouté ou supprimé');
                    // Ajoute ou supprime la classe "background" selon l'état précédent
                    likeElement.classList.toggle('background');
                } else {
                    console.error('Erreur lors de la requête');
                }
            });
        });

        function updateImageSource(isLiked, buttonElement) {
            const imgElement = buttonElement.querySelector('.fav');
            if (isLiked) {
                imgElement.src = '../static/images/fav_rouge.png';
            } else {
                imgElement.src = '../static/images/fav_noir.png';
            }
        }
    </script>
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            const scoreInputs = document.querySelectorAll('.feedback input[name="score"]');
            const submitBtn = document.querySelector('.submit');
            const albumId = document.querySelector('.feedback-btn').getAttribute('data-album-id');

            submitBtn.addEventListener('click', async function () {
                const selectedScore = document.querySelector('.feedback input[name="score"]:checked');
                if (!selectedScore) {
                    console.log('Aucune note sélectionnée');
                    return;
                }
                if (!<?php echo isset($utilisateur) ? 'true' : 'false' ?>) {
                        // Redirige l'utilisateur vers la page de connexion
                        window.location.href = '/?action=connexion_inscription';
                        return;
                    }

                const albumNote = parseInt(selectedScore.value); // Récupération de la note sélectionnée
                const isChecked = true;

                // Enleve le css des autres boutons et garder celui du bouton cliqué
                scoreInputs.forEach(input => {
                    input.nextElementSibling.classList.remove('active-note');
                });

                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        albumNote,
                        isChecked,
                        albumId,
                    }),
                });   

                if (response.ok) {
                    // Fermez la pop-up
                    document.querySelector('.modal-note').style.display = 'none';
                    // Mettre à jour la note moyenne
                    const moyenneNote = document.querySelector('#moyenne-note');
                    const nbPersonnesNotes = document.querySelector('#nbPersonnesNotes');
                    const jsonResponse = await response.json();
                    const nvMoyenne = jsonResponse.nvMoyenne;
                    const nbNotes = jsonResponse.nbNotes;
                    moyenneNote.textContent = nvMoyenne + "/5";
                    nbPersonnesNotes.textContent = "Nombre de notes : " + nbNotes;
                }
                else {
                    console.error('Erreur lors de la requête');
                }    
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
        const scoreInputs = document.querySelectorAll('.feedback input[name="score"]');

        // Ajoute un écouteur d'événements à chaque input de note
        scoreInputs.forEach(input => {
            input.addEventListener('change', function () {
                // enlever le css des autres boutons
                scoreInputs.forEach(otherInput => {
                    otherInput.nextElementSibling.classList.remove('active-note');
                });

                // mettre en surbrillance le bouton sélectionné
                if (this.checked) {
                    this.nextElementSibling.classList.add('active-note');
                }
            });
        });
    });
    </script>
    <script src="../static/script/note.js"></script>
    <script>
        // Injecter les données JSON dans une variable JavaScript
        const musiques = <?php echo $musiques_json; ?>;
        // Injecter les données JSON dans une variable JavaScript
        const id_musiques = <?php echo $id_musiques_json; ?>;
    </script>
    <script src="../static/script/son.js"></script>
</body>
</html>