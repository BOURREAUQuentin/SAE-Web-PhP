<?php
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation de la classe PDO Utilisateur
$utilisateurPDO = new utilisateurPDO($pdo);

// récupération de l'utilisateur connecté et s'il est admin
$nom_utilisateur_connecte = "pas connecté";
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
    $utilisateur_connecte = $utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte);
}
else{
    // redirigez l'utilisateur vers la page de connexion (n'est pas admin)
    header('Location: ?action=connexion_inscription');
    exit();
}
?>
<script>
    function showEditForm() {
        // Récupérer le formulaire de modification
        var editForm = document.getElementById("editForm_");
        // Afficher le formulaire de modification en le rendant visible
        editForm.style.display = "block";
        // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
        return false;
    }
    function cancelEdit() {
        // Récupérer le formulaire de modification
        var editForm = document.getElementById("editForm_");
        // Masquer le formulaire de modification
        editForm.style.display = "none";

        // Récupérer les champs de saisie correspondants
        var nomUtilisateurInput = document.getElementById("nom_utilisateur");
        var mailUtilisateurInput = document.getElementById("mail_utilisateur");
        var mdpUtilisateurInput = document.getElementById("mdp_utilisateur");

        // Masquer les champs de saisie correspondants
        nomUtilisateurInput.style.display = "none";
        mailUtilisateurInput.style.display = "none";
        mdpUtilisateurInput.style.display = "none";
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

        .genre-list {
            display: flex;
            overflow-x: auto;
            white-space: nowrap; /* Empêche le retour à la ligne des éléments */
        }

        .genre-container {
            border: 1px solid #ccc;
            margin: 10px;
            padding: 10px;
            background-color: #ffffff;
            width: 300px;
            text-align: center;
        }

        .genre-image {
            max-width: 100%;
            height: auto;
        }

        .view-genre-button {
            margin-top: 10px;
            background-color: #2196F3;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        h1{
            color: white;
        }

        .login-button,
        .logout-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .image-playlists{
            max-width: 20%;
            height: auto;
        }
    </style>
</head>
<body>
<p><?php echo $utilisateur_connecte->getNomUtilisateur(); ?></p>
<p><?php echo $utilisateur_connecte->getMailUtilisateur(); ?></p>
<p><?php echo $utilisateur_connecte->getMdp(); ?></p>
<!-- Bouton de modification -->
<button class="view-genre-button" onclick="showEditForm()">Modifier vos informations</button>
<!-- Formulaire de modification -->
<form id="editForm_" style="display: none;" action="/?action=modifier_infos_utilisateur&id_utilisateur=<?php echo $utilisateur_connecte->getIdUtilisateur(); ?>" method="post">
    <input type="hidden" name="id_utilisateur" value="<?php echo $utilisateur_connecte->getIdUtilisateur(); ?>">
    <label for="nouveau_nom_utilisateur">Nouveau nom d'utilisateur :</label>
    <input type="text" id="nouveau_nom_utilisateur" name="nouveau_nom_utilisateur" value="<?php echo $utilisateur_connecte->getNomUtilisateur(); ?>" required>
    <label for="nouveau_mail_utilisateur">Nouveau mail utilisateur :</label>
    <input type="mail" id="nouveau_mail_utilisateur" name="nouveau_mail_utilisateur" value="<?php echo $utilisateur_connecte->getMailUtilisateur(); ?>" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}" title="Entrez une adresse e-mail valide">
    <label for="nouveau_mdp">Nouveau mot de passe :</label>
    <input type="text" id="nouveau_mdp" name="nouveau_mdp" value="<?php echo $utilisateur_connecte->getMdp(); ?>" required pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z\d!@#$%^&*]{8,}$" title="Le mot de passe doit comporter au moins 8 caractères, y compris au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.">
    <button class="view-genre-button" type="submit">Modifier</button>
    <!-- Bouton Annuler -->
    <button class="view-genre-button" type="button" onclick="cancelEdit()">Annuler</button>
</form>
</body>
</html>