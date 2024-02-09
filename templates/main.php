<?php
use Modele\modele_bd\GenrePDO;
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$genrePDO = new GenrePDO($pdo);
$utilisateurPDO = new UtilisateurPDO($pdo);

// récupération de l'utilisateur connecté et s'il est admin
$nom_utilisateur_connecte = "pas connecté";
$est_admin = false;
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $est_admin = ($utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte))->isAdmin();
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
    <title>Music'O</title>
    <link rel="stylesheet" href="../static/style/accueil.css">
</head>
<body>
<body ng-app="app">
	<section class='global-wrapper' ng-controller="ctrl">
		<aside>
			<h1>logo</h1>
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
            <a href="">
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
          <a href="/?action=profil">
              <div class="nav-item">
                  <img src="../static/images/setting.png" alt="">
                  <span>Paramètres</span>
              </div>
          </a>
				</li>
			</ul>
		</aside>

		<main id="main">
			<div id="blackout-on-hover"></div>
        <header>
            <h2>Music'O</h2>
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
            <?php if ($est_admin) : ?>
                <a href="/?action=admin">Admin</a>
            <?php endif; ?>
            <?php if (isset($_SESSION["username"])) : ?>
                <a href="/?action=logout">Logout</a>
            <?php else: ?>
                <a href="?action=connexion_inscription">Login</a>
            <?php endif; ?>
        </header>
        <!-- genres -->
        <div class="genres">
            <?php foreach ($les_filtres_annees as $filtre_annee):?>
                <div class="ag-format-container">
                    <div class="ag-courses_box">
                        <div class="ag-courses_item">
                            <a href="?action=filtre_annee&annee=<?php echo $filtre_annee; ?>" class="ag-courses-item_link">
                                <div class="ag-courses-item_bg">
                                </div>
                                <div class="ag-courses-item_title">
                                    <p>Année <?php echo $filtre_annee; ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php foreach ($les_genres as $genre): ?>
				<div class="ag-format-container">
                    <div class="ag-courses_box">
                        <div class="ag-courses_item">
                            <a href="?action=genre&id_genre=<?php echo $genre->getIdGenre(); ?>" class="ag-courses-item_link">
                                <div class="ag-courses-item_bg">
                                </div>
                                <div class="ag-courses-item_title">
                                    <p><?php echo $genre->getNomGenre(); ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
	</main>
	</section>
	<script src="../static/script/search.js"></script>
</body>
</html>