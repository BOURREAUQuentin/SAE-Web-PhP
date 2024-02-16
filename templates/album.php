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
$likePDO = new LikerPDO($pdo);
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

    $utilisteur_a_noteer=$noterPDO->getNoteByIdAlbumIdUtilisateur($id_album,$utilisateur->getIdUtilisateur());

    $note_album=$noterPDO->getMoyenneNoteByIdAlbum($id_album);
}
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
        $isChecked = $_POST['isChecked'] === 'true';

        // ajoute ou supprime le like
        if ($isChecked) {
            $likePDO->ajouterLiker($musiqueId, $utilisateur->getIdUtilisateur());
        } else {
            $likePDO->supprimerLiker($musiqueId, $utilisateur->getIdUtilisateur());
        }

        // envoie une réponse JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
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
    <link rel="stylesheet" href="../static/style/album.css">
</head>
<body>
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
                        <h2 class="nomart"><?php echo $nom_artistes_album; ?></h2>
                        <p class="desc"><?php echo $album->getTitre(); ?></p>
                    </div>
                    <?php if ($note_album > 0): ?>
                        <p class="moyenne-album note"><span id="moyenne-note"><?php echo $note_album; ?></span>/5</p>
                    <?php else: ?>
                        <p class="moyenne-album note">Aucune note</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class='main-table-container'>
            <div>
                <table>
                <tbody>
                    <?php foreach($les_musiques as $musique): ?>
                        <tr>
                            <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/play.png" alt="" width="15" height="15"></button></div></td>
                            <td><?php echo $musique->getNomMusique(); ?></td>
                            <td><?php echo $nom_artistes_album; ?></td>
                            <td><?php echo $musique->getDureeMusique(); ?></td>
                            <td class="first"><div class='icon-text'><button class="play"><img src="../static/images/fav_noir.png" alt="" width="15" height="15"></button></div></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>
            </div>
        </div>
	</main>
	</section>
	<script src="../static/script/search.js"></script>
</body>
</html>