<?php
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$artistePDO = new ArtistePDO($pdo);
$imagePDO = new ImagePDO($pdo);
$utilisateurPDO = new UtilisateurPDO($pdo);

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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lavound</title>
    <link rel="stylesheet" href="../static/style/artiste.css">
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
        </div>
</body>
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
</html>