<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\RealiserParPDO;
use Modele\modele_bd\PlaylistPDO;
use Modele\modele_bd\LikerPDO;
use Modele\modele_bd\GenrePDO;

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
$playlistPDO = new PlaylistPDO($pdo);
$likerPDO = new LikerPDO($pdo);
$genrePDO = new GenrePDO($pdo);

// récupération de l'utilisateur connecté et s'il est admin
$nom_utilisateur_connecte = "pas connecté";
$est_admin = false;
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $est_admin = ($utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte))->isAdmin();
    $utilisateur_connecte = $utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte);
}
$playlists_utilisateur = $playlistPDO->getPlaylistsByNomUtilisateur($nom_utilisateur_connecte);

// Récupération de la recherche
$intitule_recherche = $_GET['intitule_recherche'];
$id_genre_recherche = $_GET['genre_recherche'];
$annee_recherche = $_GET['annee_recherche'];
$les_albums_recherche = $albumPDO->getAlbumsByRecherche($intitule_recherche, $id_genre_recherche, $annee_recherche);
$les_musiques_recherche = $musiquePDO->getMusiquesByRecherche($intitule_recherche);
$les_artistes_recherche = $artistePDO->getArtistesByRecherche($intitule_recherche);

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
            <?php if ($id_genre_recherche != 0 && $annee_recherche != "0"): ?>
                <h2 class="titre-genre">Résultats avec : <?php echo $intitule_recherche; ?>, <?php echo ($genrePDO->getGenreByIdGenre($id_genre_recherche))->getNomGenre(); ?>, <?php echo $annee_recherche; ?></h2>
            <?php elseif ($id_genre_recherche != 0): ?>
                <h2 class="titre-genre">Résultats avec : <?php echo $intitule_recherche; ?>, <?php echo ($genrePDO->getGenreByIdGenre($id_genre_recherche))->getNomGenre(); ?></h2>
            <?php elseif ($annee_recherche != "0"): ?>
                <h2 class="titre-genre">Résultats avec : <?php echo $intitule_recherche; ?>, <?php echo $annee_recherche; ?></h2>
            <?php else: ?>
                <h2 class="titre-genre">Résultats avec : <?php echo $intitule_recherche; ?></h2>
            <?php endif; ?>
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
                        <?php if (!isset($utilisateur_connecte)): ?>
                            <button id="buttonfav" onclick="toggleBackgroundColor()" value="<?php echo $musique_recherche->getIdMusique(); ?>">
                                <img class="fav" src="../static/images/fav_noir.png" alt="">
                            </button>
                        <?php else:
                            // Vérifie si la musique est likée par l'utilisateur connecté
                            $isLiked = $likerPDO->verifieMusiqueLiker($musique_recherche->getIdMusique(), $utilisateur_connecte->getIdUtilisateur()); ?>
                            <!-- Ajoutez la classe "background" si la musique est déjà likée -->
                            <button id="buttonfav" <?php echo $isLiked ? 'class="background"' : ''; ?> onclick="toggleBackgroundColor()" value="<?php echo $musique_recherche->getIdMusique(); ?>">
                                <img class="fav" src="../static/images/<?php echo $isLiked ? "fav_rouge.png" : "fav_noir.png"; ?>" alt="">
                            </button>
                        <?php endif; ?>
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
                                    <?php if (!isset($_SESSION["username"])): ?>
                                        <a href="/?action=connexion_inscription" class="para2">Connectez vous pour choisir une playlist</a>
                                    <?php else:
                                        $playlists_utilisateur_sans_musique_recherche = $playlistPDO->getPlaylistsUtilisateurSansMusiqueByIdMusique($utilisateur_connecte->getIdUtilisateur(), $musique_recherche->getIdMusique());
                                        ?>
                                        <?php if (count($playlists_utilisateur) == 0): ?>
                                            <a href="/?action=playlists_utilisateur" class="para2">Aucune playlist. Créez une nouvelle playlist</a>
                                        <?php elseif (count($playlists_utilisateur_sans_musique_recherche) == 0): ?>
                                            <p class="para2">Déjà dans vos playlists</p>
                                        <?php else: ?>
                                            <?php foreach($playlists_utilisateur_sans_musique_recherche as $playlist_utilisateur): ?>
                                                <a href="/?action=ajouter_playlist&id_musique=<?php echo $musique_recherche->getIdMusique(); ?>&id_playlist=<?php echo $playlist_utilisateur->getIdPlaylist(); ?>" class="para2">
                                                    <?php echo $playlist_utilisateur->getNomPlaylist(); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
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
    <script src="../static/script/genre3.js"></script>
    <script src="../static/script/genre2.js"></script>
    <script src="../static/script/genre.js"></script>
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