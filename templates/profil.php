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

// récupération de l'utilisateur connecté et s'il est admin
$nom_utilisateur_connecte = "pas connecté";
$est_admin = false;
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $utilisateur_connecte = $utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte);
    $est_admin = $utilisateur_connecte->isAdmin();
}
else{
    // redirigez l'utilisateur vers la page de connexion (n'est pas admin)
    header('Location: ?action=connexion_inscription');
    exit();
}

$message_erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nouveau_nom_utilisateur = $_POST['nouveau_nom_utilisateur']; // On récupère le champ nouveau_nom_utilisateur
    $nouveau_mail_utilisateur = $_POST['nouveau_mail_utilisateur']; // On récupère le champ nouveau_mail_utilisateur
    $nouveau_mdp = $_POST['nouveau_mdp']; // On récupère le champ nouveau_mdp

    $utilisateur_by_nom_utilisateur = $utilisateurPDO->getUtilisateurByNomUtilisateur($nouveau_nom_utilisateur);
    $utilisateur_by_mail_utilisateur = $utilisateurPDO->getUtilisateurByMailUtilisateur($nouveau_mail_utilisateur);

    if ($utilisateur_by_nom_utilisateur == null || $nouveau_nom_utilisateur == $nom_utilisateur_connecte) {
        // si le nom d'utilisateur n'est pas déjà pris ou que c'est déjà celui de l'utilisateur connecté
        if ($utilisateur_by_mail_utilisateur == null || $utilisateur_by_mail_utilisateur->getNomUtilisateur() == $nom_utilisateur_connecte) {
            // si le mail utilisateur n'est pas déjà pris ou que c'est déjà celui de l'utilisateur connecté
            $_SESSION["username"] = $nouveau_nom_utilisateur;
            $utilisateurPDO->mettreAJourInfosUtilisateur($utilisateur_connecte->getIdUtilisateur(), $nouveau_nom_utilisateur, $nouveau_mail_utilisateur, $nouveau_mdp);
            exit(header('Location: ?action=profil'));
        }
        else {
            $message_erreur = "Mail utilisateur déjà utilisé";
        }
    }
    else {
        $message_erreur = "Nom d'utilisateur déjà utilisé";
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
    <link rel="stylesheet" href="../static/style/profil.css">
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
            <div class="profil">
                <div class="card">
                  <div class="content">
                    <div class="details">
                      <div class="data">
                        <?php if (!empty($message_erreur)): ?>
                            <p style="color: red;"><?php echo $message_erreur; ?></p>
                        <?php endif; ?>
                        <h3>Pseudo <br> <span><?php echo $utilisateur_connecte->getNomUtilisateur(); ?></span></h3>
                        <h3>Email <br> <span><?php echo $utilisateur_connecte->getMailUtilisateur(); ?></span></h3>
                        <h3>Mot de passe<br> <span><?php echo $utilisateur_connecte->getMdp(); ?></span></h3>
                      </div>
                
                    </div>
                    <div class="modif">
                      <form class="modif-form" id="editForm_" action="" method="post">
                        <input type="hidden" name="id_utilisateur" value="">
                        <label for="nouveau_nom_utilisateur">Nouveau pseudo :</label>
                        <div class="bottom">
                        <input type="text" id="nouveau_nom_utilisateur" name="nouveau_nom_utilisateur" value="<?php echo $utilisateur_connecte->getNomUtilisateur(); ?>" required>
                      </div>
                        <label for="nouveau_mail_utilisateur">Nouveau mail :</label>
                        <div class="bottom">
                        <input type="mail" id="nouveau_mail_utilisateur" name="nouveau_mail_utilisateur" value="<?php echo $utilisateur_connecte->getMailUtilisateur(); ?>" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}" title="Entrez une adresse e-mail valide">
                      </div>
                        <label for="nouveau_mdp">Nouveau mot de passe :</label>
                        <div class="bottom">
                        <input type="text" id="nouveau_mdp" name="nouveau_mdp" value="<?php echo $utilisateur_connecte->getMdp(); ?>" required pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z\d!@#$%^&*]{8,}$" title="Le mot de passe doit comporter au moins 8 caractères, y compris au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.">
                      </div>
                        <button class="custom-btn bouton-modif" type="submit">Modifier</button>
                    </form>
                    </div>
                  </div>
                </div>
              </div>
        </main>
	</section>
	<script src="../static/script/search.js"></script>
</body>
</html>