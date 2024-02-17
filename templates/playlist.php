<?php
use Modele\modele_bd\PlaylistPDO;
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\LikerPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$playlistPDO = new PlaylistPDO($pdo);
$musiquePDO = new MusiquePDO($pdo);
$imagePDO = new ImagePDO($pdo);
$utilisateurPDO = new UtilisateurPDO($pdo);
$artistePDO = new ArtistePDO($pdo);
$likerPDO = new LikerPDO($pdo);

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

// vérifie si la requête est une requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si la clé 'musiqueId' existe dans $_POST, cela signifie que le like pour une musique est envoyé
    if (isset($_POST['musiqueId'])) {
        // Récupère les données de la requête
        $musiqueId = intval($_POST['musiqueId']);
        $isChecked = $_POST['isChecked'] === 'false';

        // ajoute ou supprime le like
        if ($isChecked) {
            $likerPDO->ajouterLiker($musiqueId, $utilisateur_connecte->getIdUtilisateur());
        } 
        else {
            $likerPDO->supprimerLiker($musiqueId, $utilisateur_connecte->getIdUtilisateur());
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
                            <th>Play</th>
                            <th>Nom</th>
                            <th>Artiste</th>
                            <th>Durée</th>
                            <th>Nombre de streams</th>
                            <th>Favori</th>
                            <th>Enlever</th>
                        </tr>
                        <?php foreach($musiques_playlist as $musique_playlist): 
                        $artiste_musique = $artistePDO->getArtisteByIdMusique($musique_playlist->getIdMusique()); // récupération de l'artiste ayant réalisé la musique
                        ?>
                            <tr>
                                <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                                <td><?php echo $musique_playlist->getNomMusique(); ?></td>
                                <td><?php echo $artiste_musique->getNomArtiste(); ?></td>
                                <td><?php echo $musique_playlist->getDureeMusique(); ?></td>
                                <td><?php echo $musique_playlist->getNbStreams(); ?></td>
                                <?php if (!isset($utilisateur_connecte)): ?>
                                    <td class="first"><div class='icon-text'><button id="buttonfav" class="play background" value="<?php echo $musique_playlist->getIdMusique(); ?>"><img class="fav" src="../static/images/fav_noir.png" alt="" width="15" height="15"></button></div></td>
                                <?php else:
                                    // Vérifie si la musique_playlist est likée par l'utilisateur connecté
                                    $isLiked = $likerPDO->verifieMusiqueLiker($musique_playlist->getIdMusique(), $utilisateur_connecte->getIdUtilisateur()); ?>
                                    <!-- Ajoutez la classe "background" si la musique_playlist est déjà likée -->
                                    <td class="first"><div class='icon-text'><button id="buttonfav" class="play <?php echo $isLiked ? 'background' : ''; ?>" value="<?php echo $musique_playlist->getIdMusique(); ?>"><img class="fav" src="../static/images/<?php echo $isLiked ? "fav_rouge.png" : "fav_noir.png"; ?>" alt="" width="15" height="15"></button></div></td>
                                <?php endif; ?>
                                <td class="first"><div class='icon-text'><a href="/?action=supprimer_musique_playlist&id_musique=<?php echo $musique_playlist->getIdMusique(); ?>&id_playlist=<?php echo $id_playlist; ?>"><button class="play" ><img src="../static/images/croix.png" alt="" width="15" height="15"></button></a></div></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
                
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
                if (!<?php echo isset($utilisateur_connecte) ? 'true' : 'false' ?>) {
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
</body>
</html>