<?php
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\GenrePDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation de la classe PDO Utilisateur et Genre
$utilisateurPDO = new utilisateurPDO($pdo);
$genrePDO = new GenrePDO($pdo);

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

// Récupération de la liste des utilisateurs (non admin)
$les_utilisateurs_non_admin = $utilisateurPDO->getUtilisateursNonAdmin();

$message_erreur = "";

// si c'est une méthode POST pour l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nom_utilisateur = $_POST['nom_utilisateur']; // On récupère le champ nom_utilisateur
    $mail_utilisateur = $_POST['mail_utilisateur']; // On récupère le champ mail_utilisateur
    $mdp = $_POST['mdp']; // On récupère le mot de passe

    if (!empty($nom_utilisateur) && !empty($mdp) && !empty($mail_utilisateur)) {
        
        $user_exists = $utilisateurPDO->getUtilisateurByUsername_mail($nom_utilisateur, $mail_utilisateur);

        if ($user_exists) {
            $message_erreur = "Le nom d'utilisateur ou l'adresse e-mail existe déjà. Veuillez en choisir un autre.";
        }
        else {
            $utilisateurPDO->ajouterUtilisateur($nom_utilisateur, $mail_utilisateur, $mdp);
            exit(header('Location: ?action=admin_utilisateur'));
        }
    }
}

// Récupération de la liste des genres et des filtres par années
$les_genres = $genrePDO->getGenres();
$les_filtres_annees = array("1970", "1980", "1990", "2000", "2010", "2020");
?>
<script>
    function confirmSuppressionUtilisateur(id_utilisateur) {
        // Affiche une boîte de dialogue de confirmation
        var confirmation = confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?");
        // Si l'utilisateur clique sur OK, retourne true (continue la suppression)
        // Sinon, retourne false (arrête la suppression)
        if (confirmation) {
            window.location.href = "?action=supprimer_utilisateur&id_utilisateur=" + id_utilisateur;
        }
        return false;
    }
</script>
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
                <h3 class="T-part">Nouvel utilisateur</h3>
                <?php if (!empty($message_erreur)): ?>
                    <p style="color: red;"><?php echo $message_erreur; ?></p>
                <?php endif; ?>
                <div class="album-container">
                    <!-- Formulaire pour ajouter un nouvel utilisateur -->
                    <form action="" method="post">
                        <div class="form-ajout">
                            <div class="infos-new-artiste">
                                <div class="flex-container">
                                    <div class="flex-item">
                                        <div class="input-simple">
                                            <label for="nom_utilisateur">Nom d'utilisateur :</label>
                                            <input class="input-infos" type="text" id="nom_utilisateur" name="nom_utilisateur" required>
                                        </div>
                                    </div>
                                    <div class="flex-item">
                                        <div class="input-simple">
                                            <label for="mail_utilisateur">Mail d'utilisateur :</label>
                                            <input class="input-infos" type="text" id="mail_utilisateur" name="mail_utilisateur" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}" title="Entrez une adresse e-mail valide">
                                        </div>
                                    </div>
                                    <div class="flex-item">
                                        <div class="input-simple">
                                            <label for="mdp">Mot de passe :</label>
                                            <input class="input-infos" type="text" id="mdp" name="mdp" required pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z\d!@#$%^&*]{8,}$" title="Le mot de passe doit comporter au moins 8 caractères, y compris au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.">
                                        </div>
                                    </div>
                                </div>
                                <button class="view-album-button" type="submit">Ajouter un utilisateur</button>
                            </div>
                        </div>
                    </form>
                </div>
                <h3 class="T-part">Les utilisateurs</h3>
                <?php foreach ($les_utilisateurs_non_admin as $utilisateur_non_admin): ?>
                    <div class="album-container">
                    <div id="container_infos_album">
                            <div>
                            <p>Nom d'utilisateur : <?php echo $utilisateur_non_admin->getNomUtilisateur(); ?></p>

                            </div>
                            <div>
                            <p>Mail utilisateur : <?php echo $utilisateur_non_admin->getMailUtilisateur(); ?></p>

                            </div>
                            <div>
                            <p>Mot de passe : <?php echo $utilisateur_non_admin->getMdp(); ?></p>
                            </div>
                        </div>
                        
                        <a href="#" onclick="return confirmSuppressionUtilisateur(<?php echo $utilisateur_non_admin->getIdUtilisateur(); ?>)">
                            <button class="view-album-button">Supprimer l'utilisateur</button>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
	</section>
    <script src="../static/script/search.js"></script>
</body>
</html>