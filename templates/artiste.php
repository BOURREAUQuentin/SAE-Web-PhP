<?php
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\RealiserParPDO;
use Modele\modele_bd\LikerPDO;
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\PlaylistPDO;
use Modele\modele_bd\GenrePDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$artistePDO = new ArtistePDO($pdo);
$imagePDO = new ImagePDO($pdo);
$utilisateurPDO = new UtilisateurPDO($pdo);
$realiserParPDO = new RealiserParPDO($pdo);
$likerPDO = new LikerPDO($pdo);
$musiquePDO = new MusiquePDO($pdo);
$playlistPDO = new PlaylistPDO($pdo);
$genrePDO = new GenrePDO($pdo);

// Récupération de l'id de l'artiste
$id_artiste = intval($_GET['id_artiste']);

$artiste = $artistePDO->getArtisteByIdArtiste($id_artiste);
$image = $imagePDO->getImageByIdImage($artiste->getIdImage());
$image_path = $image->getImage() ? "../images/" . $image->getImage() : '../images/default.jpg';

// récupération de l'utilisateur connecté et s'il est admin
$nom_utilisateur_connecte = "pas connecté";
$est_admin = false;
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $est_admin = ($utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte))->isAdmin();
    $utilisateur_connecte = $utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte);
}
$liste_albums = $artistePDO->getAlbumsByIdArtiste($id_artiste);
$liste_musiques = $artistePDO->getMusiquesByIdArtiste($id_artiste);
$playlists_utilisateur = $playlistPDO->getPlaylistsByNomUtilisateur($nom_utilisateur_connecte);

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
    <link rel="stylesheet" href="../static/style/artiste.css">
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
                    <img src="<?php echo $image_path; ?>" alt="" height="200" width="200" class="imgart">
                    <div class="art">
                        <h2 class="nomart"><?php echo $artiste->getNomArtiste(); ?></h2>
                        <?php $les_musiques_artiste = $artistePDO->getMusiquesPlusStreamesByIdArtiste($artiste->getIdArtiste());
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
                            <p class="desc"><?php echo $artiste->getNomArtiste() . " est un artiste connu pour des sons comme " . $texte_artiste_musique; ?></p>
                        <?php else: ?>
                            <p class="desc"><?php echo $artiste->getNomArtiste() . " est un artiste avec aucun sons."; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <h3 class="T-part">Les Albums</h3>
            <div class="album">
                <?php if (!empty($liste_albums)): ?>
                    <?php foreach($liste_albums as $album):
                    $image_album = $imagePDO->getImageByIdImage($album->getIdImage());
                    $image_path_album = $image_album->getImage() ? "../images/" . urlencode($image_album->getImage()) : '../images/default.jpg';
                    ?>
                        <div class="disc-container">
                            <a href="/?action=album&id_album=<?php echo $album->getIdAlbum(); ?>">
                                <div class="cover"><img src="../images/<?php echo $image_path_album; ?>" alt="" width="220" height="220"></div>
                                <div class="cd">
                                    <p class="art-name"><?php echo ($artistePDO->getArtisteByIdArtiste(($realiserParPDO->getIdArtistesByIdAlbum($album->getIdAlbum()))[0]))->getNomArtiste(); ?></p>
                                    <p class="song2"><?php echo $album->getTitre(); ?></p></div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun album de <?php echo $artiste->getNomArtiste(); ?></p>
                <?php endif; ?>
            </div>
            <?php if (count($liste_albums) > 5): ?>
                <button class="btn" id="buttonVoirPlus">
                    <span class="icon" id="icon">+</span>
                    </span>
                    <span class="text" id="voir">Voir plus</span>
                </button>
            <?php endif; ?>
            <h3 class="T-part">Les Sons</h3>
            <div class="album">
                <?php if (!empty($liste_musiques)): ?>
                    <?php foreach ($liste_musiques as $musique):
                    $id_image_musique = $musiquePDO->getIdImageByIdMusique($musique->getIdMusique());
                    $image_musique = $imagePDO->getImageByIdImage($id_image_musique);
                    $image_path_musique = $image_musique->getImage() ? "../images/" . $image_musique->getImage() : '../images/default.jpg';
                    ?>
                    <div class="song">
                        <div class="disc-container2">
                            <div class="cover">
                                <img src="../images/<?php echo $image_path_musique; ?>" alt="Image de la musique <?php echo $musique->getNomMusique(); ?>" width="220" height="220">
                            </div>
                            <div class="cd">
                                <p class="art-name"><?php echo ($artistePDO->getArtisteByIdArtiste(($realiserParPDO->getIdArtistesByIdAlbum($musique->getIdAlbum()))[0]))->getNomArtiste(); ?></p>
                                <p class="song2"><?php echo $musique->getNomMusique(); ?></p>
                            </div>
                        </div>
                        <div class="buttons">
                        <?php if (!isset($utilisateur_connecte)): ?>
                            <button id="buttonfav" value="<?php echo $musique->getIdMusique(); ?>">
                                <img class="fav" src="../static/images/fav_noir.png" alt="">
                            </button>
                        <?php else:
                            // Vérifie si la musique est likée par l'utilisateur connecté
                            $isLiked = $likerPDO->verifieMusiqueLiker($musique->getIdMusique(), $utilisateur_connecte->getIdUtilisateur()); ?>
                            <!-- Ajoutez la classe "background" si la musique est déjà likée -->
                            <button id="buttonfav" <?php echo $isLiked ? 'class="background"' : ''; ?> value="<?php echo $musique->getIdMusique(); ?>">
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
                                        $playlists_utilisateur_sans_musique = $playlistPDO->getPlaylistsUtilisateurSansMusiqueByIdMusique($utilisateur_connecte->getIdUtilisateur(), $musique->getIdMusique());
                                        ?>
                                        <?php if (count($playlists_utilisateur) == 0): ?>
                                            <a href="/?action=playlists_utilisateur" class="para2">Aucune playlist. Créez une nouvelle playlist</a>
                                        <?php elseif (count($playlists_utilisateur_sans_musique) == 0): ?>
                                            <p class="para2">Déjà dans vos playlists</p>
                                        <?php else: ?>
                                            <?php foreach($playlists_utilisateur_sans_musique as $playlist_utilisateur): ?>
                                                <a href="/?action=ajouter_playlist&id_musique=<?php echo $musique->getIdMusique(); ?>&id_playlist=<?php echo $playlist_utilisateur->getIdPlaylist(); ?>" class="para2">
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
                    <p>Aucune musique de <?php echo $artiste->getNomArtiste(); ?></p>
                <?php endif; ?>
            </div>
            <?php if (count($liste_musiques) > 5): ?>
                <button class="btn btn2" id="buttonVoirPlus2">
                    <span class="icon" id="icon2">+</span>
                    </span>
                    <span class="text" id="voir2">Voir plus</span>
                </button>
            <?php endif; ?>
		</main>

	</section>
        </div>
</body>
    <script src="../static/script/genre2.js"></script>
    <script src="../static/script/genre.js"></script>
	<script src="../static/script/search.js"></script>
    <script>
        const utilisateur_est_connecte = <?php echo isset($utilisateur_connecte) ? 'true' : 'false'; ?>;
    </script>
    <script src="../static/script/likes.js"></script>
</html>