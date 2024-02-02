<?php
use Modele\modele_bd\UtilisateurPDO;
use PDO;

$pdo = new PDO('sqlite:Data/sae_php.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$utilisateurPDO = new UtilisateurPDO($pdo);

// si c'est une methode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = $_POST['username']; // On récupere le champs username
    $mail = $_POST['mail']; // On récupere le champs mail
    $password = $_POST['password']; // On récupere le mot de passe

    if (!empty($username) && !empty($password) && !empty($mail)) {
        
        $user_exists = $utilisateurPDO->getUtilisateurByUsername_mail($username, $mail);

        if ($user_exists) {
            $error_message = "username or mail exists. Please choose another one.";
        } 
        else {
            $utilisateurPDO->ajouterUtilisateur($username, $mail, $password);
            exit(header('Location: ?action=page_connexion'));
        }
    } else {
        $error_message = "Please enter both username and password.";
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
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="username">Username :</label>
            <input type="text" id="username" name="username" required>
        </div>


        <div class="form-group">
            <label for="mail">Mail :</label>
            <input type="mail" id="mail" name="mail" required>
        </div>


        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
