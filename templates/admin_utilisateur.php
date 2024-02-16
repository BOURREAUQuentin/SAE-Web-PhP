<?php
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation de la classe PDO Utilisateur
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

// Récupération de la liste des utilisateurs (non admin)
$les_utilisateurs_non_admin = $utilisateurPDO->getUtilisateursNonAdmin();

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
    <title>Music'O</title>
    <style>
        body{
            background-color: #424242;
        }

        .utilisateur-container {
            border: 1px solid #ccc;
            margin: 10px;
            padding: 5px;
            background-color: #ffffff;
            text-align: center;
            display: flex; /* Utilisation de flexbox pour aligner les éléments sur la même ligne */
            align-items: center; /* Alignement vertical */
        }

        .utilisateur-container > * {
            margin-inline: auto;
        }

        .utilisateur-image {
            width: 10%;
            height: auto;
        }

        .view-utilisateur-button {
            margin-top: 10px;
            background-color: #2196F3;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #container_infos_utilisateur {
            width: 70%; /* Set a fixed width for the container (adjust as needed) */
            margin: 0 auto; /* Center the container */
            display: flex;
            align-items: center;
            justify-content: space-around;
        }

        #container_infos_utilisateur > div {
            flex: 1; /* Equal width for each block */
            padding: 10px;
            text-align: left;
        }

        #container_infos_utilisateur p {
            margin: 0;
        }
    </style>
</head>
<body>
<h1>Ajouter un utilisateur</h1>
<div class="utilisateur-container">
    <!-- Formulaire pour ajouter un nouvel utilisateur -->
    <form action="" method="post">
        <label for="nom_utilisateur">Nom d'utilisateur :</label>
        <input type="text" id="nom_utilisateur" name="nom_utilisateur" required>
        <label for="mail_utilisateur">Mail d'utilisateur :</label>
        <input type="text" id="mail_utilisateur" name="mail_utilisateur" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}" title="Entrez une adresse e-mail valide">
        <label for="mdp">Mot de passe :</label>
        <input type="text" id="mdp" name="mdp" required pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z\d!@#$%^&*]{8,}$" title="Le mot de passe doit comporter au moins 8 caractères, y compris au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.">
        <button type="submit">Ajouter un utilisateur</button>
    </form>
</div>
<h1>Listes des utilisateurs</h1>
<?php foreach ($les_utilisateurs_non_admin as $utilisateur_non_admin): ?>
    <div class="utilisateur-container">
    <div id="container_infos_utilisateur">
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
            <button class="view-utilisateur-button">Supprimer l'utilisateur</button>
        </a>
    </div>
<?php endforeach; ?>
</body>
</html>