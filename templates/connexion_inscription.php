<?php
use Modele\modele_bd\UtilisateurPDO;

$pdo = new PDO('sqlite:Data/sae_php.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$utilisateurPDO = new UtilisateurPDO($pdo);

$message_erreur = "";

// si c'est une méthode POST pour la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && $_POST['submit'] === 'login') {
    
    $username = $_POST['username']; // On récupère le champ username (nom utilisateur ou email)
    $password = $_POST['password']; // On récupère le mot de passe

    if (!empty($username) && !empty($password)) {
        
        $user = $utilisateurPDO->getUtilisateurByUsername($username, $password);

        if ($user != null) {
            // stockage du nom d'utilisateur dans la session
            $utilisateur_connecte = $utilisateurPDO->getUtilisateurByNomUtilisateur($username);
            if ($utilisateur_connecte != null) { // si l'utilisateur s'est connecté avec son nom d'utilisateur
                $_SESSION["username"] = $username;
            }
            else{ // si l'utilisateur s'est connecté avec son mail, dans la session on stocke son nom d'utilisateur et pas son mail
                $_SESSION["username"] = ($utilisateurPDO->getUtilisateurByMailUtilisateur($username))->getNomUtilisateur();
            }
            exit(header('Location: ?action=accueil'));
        }
        else {
            $message_erreur = "Nom d'utilisateur ou mot de passe invalide.";
        }
    } else {
        $message_erreur = "Veuillez saisir votre nom d'utilisateur et votre mot de passe.";
    }
}

// si c'est une méthode POST pour l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && $_POST['submit'] === 'register') {
    
    $username = $_POST['username']; // On récupère le champ username
    $mail = $_POST['mail']; // On récupère le champ mail
    $password = $_POST['password']; // On récupère le mot de passe

    if (!empty($username) && !empty($password) && !empty($mail)) {
        
        $user_exists = $utilisateurPDO->getUtilisateurByUsername_mail($username, $mail);

        if ($user_exists) {
            $message_erreur = "Le nom d'utilisateur ou l'adresse e-mail existe déjà. Veuillez en choisir un autre.";
        }
        else {
            $utilisateurPDO->ajouterUtilisateur($username, $mail, $password);
            $user = $utilisateurPDO->getUtilisateurByUsername($username, $password);
            // stockage du nom d'utilisateur dans la session
            $_SESSION["username"] = $username;
            exit(header('Location: ?action=accueil'));
        }
    } else {
        $message_erreur = "Veuillez saisir votre nom d'utilisateur et votre mot de passe.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lavound</title>
    <link rel="stylesheet" href="../static/style/connexion_inscription.css">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
</head>
<body ng-app="app">
	<section class='global-wrapper' ng-controller="ctrl">
		<aside>
            <img src="../static/images/logo.png" alt="" width="80px" height="80px">
			<!--top nav -->
			<ul>
				<li class="active">
          <a href="#" onclick="toggleSearchBar()">
              <div class="nav-item">
                  <img src="../static/images/loupe.png" alt="">
                  <span>Recherche</span>
              </div>
          </a>
      </li>
				<li>
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
                        <a href="?action=connexion_inscription" class="para">Connexion</a>
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
        <div class="container">
            <div class="forms-container">
                <div class="signin-signup">
                    <form  action="" method="post" class="sign-in-form form">
                        <h2 class="title">Sign in</h2>
                        <?php if (!empty($message_erreur) && $_POST['submit'] === 'login'): ?>
                            <p style="color: red;"><?php echo $message_erreur; ?></p>
                        <?php endif; ?>
                        <div class="input-field">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" placeholder="Nom d'utilisateur ou email" required/>
                        </div>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" placeholder="Mot de passe" required pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z\d!@#$%^&*]{8,}$" title="Le mot de passe doit comporter au moins 8 caractères, y compris au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial."/>
                        </div>
                        <input type="submit" name="submit" value="login" class="btn solid" />
                    </form>
                    <form action="" method="post" class="sign-up-form form">
                        <h2 class="title">Sign up</h2>
                        <?php if (!empty($message_erreur) && $_POST['submit'] === 'register'): ?>
                            <p style="color: red;"><?php echo $message_erreur; ?></p>
                        <?php endif; ?>
                        <div class="input-field">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" placeholder="Nom d'utilisateur" required/>
                        </div>
                        <div class="input-field">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="mail" placeholder="Email" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}" title="Entrez une adresse e-mail valide"/>
                        </div>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" placeholder="Mot de passe" required pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z\d!@#$%^&*]{8,}$" title="Le mot de passe doit comporter au moins 8 caractères, y compris au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial."/>
                        </div>
                        <input type="submit" name="submit" value="register" class="btn" />
                    </form>
                </div>
            </div>
    
            <div class="panels-container">
                <div class="panel left-panel">
                    <div class="content">
                        <h3>Vous êtes nouveau ?</h3>
                        <p>
                            Rejoignez nous en vous inscrivant avec le bouton ci-dessous. 
                            Vous donnant accès à de nouvelles fonctionnalités tel que le création de playlist et bien plus encore
                        </p>
                        <button class="btn transparent" id="sign-up-btn">
                            Sign up
                        </button>
                    </div>
                </div>
                <div class="panel right-panel">
                    <div class="content">
                        <h3>Vous possédez déjà un compte.</h3>
                        <p>
                            Merci de nous soutenir. 
                            Connectez vous a votre compte en appuyant sur le bouton ci-dessous.
                        </p>
                        <button class="btn transparent" id="sign-in-btn">
                            Sign in
                        </button>
                    </div>
                </div>
            </div>
        </div>
    
        <script src="../static/script/connexion_inscription.js"></script>


		</main>

	</section>
	<script src="../static/script/search.js"></script>
</body>
</html>