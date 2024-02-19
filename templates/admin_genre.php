<?php
use Modele\modele_bd\GenrePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$genrePDO = new GenrePDO($pdo);
$imagePDO = new ImagePDO($pdo);
$utilisateurPDO = new utilisateurPDO($pdo);

// vérification de si l'utilisateur est connecté et s'il est admin
$nom_utilisateur_connecte = "pas connecté";
$est_admin = false;
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $est_admin = ($utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte))->isAdmin();
}
if (!$est_admin) {
    // redirigez l'utilisateur vers la page de connexion (n'est pas admin)
    header('Location: ?action=connexion_inscription');
    exit();
}

// Récupération de la liste des genres
$les_genres = $genrePDO->getGenres();

$message_erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nom_genre = $_POST['nom_genre']; // On récupère le champ nom_genre

    $genre = $genrePDO->getGenreByNomGenre($nom_genre);

    if ($genre == null) { // si le genre n'existe pas déjà (le nom est pas déjà utilisé)
        // ajout du genre dans la bd
        $genrePDO->ajouterGenre($nom_genre);
        exit(header('Location: ?action=admin_genre'));
    }
    else {
        $message_erreur = "Nom de genre déjà utilisé.";
    }
}

// Récupération de la liste des filtres par années
$les_filtres_annees = array("1970", "1980", "1990", "2000", "2010", "2020");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lavound</title>
    <link rel="stylesheet" href="../static/style/genre.css">
    <link rel="stylesheet" href="../static/style/admin.css">
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
                <h3 class="T-part">Nouveau genre</h3>
                <?php if (!empty($message_erreur)): ?>
                    <p style="color: red;"><?php echo $message_erreur; ?></p>
                <?php endif; ?>
                <div class="album-container">
                    <!-- Formulaire pour ajouter un nouveau genre -->
                    <form action="" method="post">
                        <div class="form-ajout">
                            <div class="infos-new-artiste">
                                <div class="flex-container">
                                    <div class="flex-item">
                                        <div class="input-simple">
                                            <label for="nom_genre">Nom du genre :</label>
                                            <input class="input-infos" type="text" id="nom_genre" name="nom_genre" required>
                                        </div>
                                    </div>
                                </div>
                                <button class="view-album-button" type="submit" value="ajouter_genre">Ajouter un genre</button>
                            </div>
                        </div>
                    </form>
                </div>
                <h3 class="T-part">Les genres</h3>
                <?php foreach ($les_genres as $genre): ?>
                    <div class="album-container">
                        <div id="container_infos_album">
                            <div>
                                <p>Nom : <?php echo $genre->getNomGenre(); ?></p>
                            </div>
                        </div>
                        
                        <a href="/?action=genre&id_genre=<?php echo $genre->getIdGenre(); ?>">
                            <button class="view-album-button">Voir le genre</button>
                        </a>
                    </div>
                <?php endforeach; ?>
                </div>
        </main>
	</section>
    <script src="../static/script/search.js"></script>
</body>
</html>