<?php
use Modele\modele_bd\UtilisateurPDO;
use PDO;

$pdo = new PDO('sqlite:Data/sae_php.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$utilisateurPDO = new UtilisateurPDO($pdo);

$message_erreur = "";

// si c'est une méthode POST pour la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && $_POST['submit'] === 'login') {
    
    $username = $_POST['username']; // On récupère le champ username
    $password = $_POST['password']; // On récupère le mot de passe

    if (!empty($username) && !empty($password)) {
        
        $user = $utilisateurPDO->getUtilisateurByUsername($username, $password);

        if ($user != null) {
            // stockage du nom d'utilisateur dans la session
            $_SESSION["username"] = $username;
            exit(header('Location: ?action=main'));
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
            exit(header('Location: ?action=main'));
        }
    } else {
        $message_erreur = "Veuillez saisir votre nom d'utilisateur et votre mot de passe.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background-color: #424242;
            font-family: Arial, sans-serif;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .register-button {
            background-color: #2196F3;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>

    <?php if (!empty($message_erreur)): ?>
        <p style="color: red;"><?php echo $message_erreur; ?></p>
    <?php endif; ?>

    <!-- Formulaire de connexion -->
    <form method="post" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z\d!@#$%^&*]{8,}$" title="Le mot de passe doit comporter au moins 8 caractères, y compris au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.">
        </div>

        <button type="submit" name="submit" value="login">Login</button>
    </form>

    <hr> <!-- Séparateur entre le formulaire de connexion et d'inscription -->

    <h2>Register</h2>

    <!-- Formulaire d'inscription -->
    <form method="post" action="">
        <div class="form-group">
            <label for="username">Username :</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="mail">Mail :</label>
            <input type="email" id="mail" name="mail" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}" title="Entrez une adresse e-mail valide">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z\d!@#$%^&*]{8,}$" title="Le mot de passe doit comporter au moins 8 caractères, y compris au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.">
        </div>

        <button type="submit" name="submit" value="register">Register</button>
    </form>
</div>

</body>
</html>
