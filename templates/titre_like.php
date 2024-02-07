<?php

use Modele\modele_bd\LikerPDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\ImagePDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



$likePDO = new LikerPDO($pdo);
$utilisateurPDO = new UtilisateurPDO($pdo);
$musiquePDO = new MusiquePDO($pdo);
$imagePDO = new ImagePDO($pdo);

if (!isset($_SESSION['username'])) {
    header('Location: ?action=page_connexion');
    exit;
}

$utilisateur = $utilisateurPDO->getUtilisateurByNomUtilisateur($_SESSION['username']);
$les_musiques_likes = $likePDO->getMusiqueByUtilisateur($utilisateur->getIdUtilisateur());


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère les données de la requête
    $musiqueId = intval($_POST['musiqueId']);
    $isChecked = $_POST['isChecked'] === 'true';

    // Ajoute ou supprime le like
    if ($isChecked) {
        $likePDO->ajouterLiker($musiqueId, $utilisateur->getIdUtilisateur());
    } else {
        $likePDO->supprimerLiker($musiqueId, $utilisateur->getIdUtilisateur());
    }

    // Envoie une réponse JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}
?>

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

        .album-container {
        display: flex;
        flex-direction: column; /* Définir la direction du flux en colonne */
        border: 1px solid #ccc;
        margin: 10px;
        padding: 10px;
        background-color: #ffffff;
        width: 300px;
        text-align: center;
        }

        .musique-container {
            margin-bottom: 10px; /* Espacement entre les conteneurs de musique */
        }

        .album-image {
            max-width: 100%;
            height: auto;
        }


        .container input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
  }
  
  .container {
    display: block;
    position: relative;
    cursor: pointer;
    user-select: none;
  }
  
  .container svg {
    position: relative;
    top: 0;
    left: 0;
    height: 50px;
    width: 50px;
    transition: all 0.3s;
    fill: #666;
  }
  
  .container svg:hover {
    transform: scale(1.1);
  }
  
  .container input:checked ~ svg { 
    fill: #E3474F;
  }
    </style>
</head>
<body>
    <div class="album-container">
        <h1>Musiques likées</h1>
        <?php foreach ($les_musiques_likes as $musique):
            $id_image_musique = $musiquePDO->getIdImageByIdMusique($musique->getIdMusique());
            $image_musique = $imagePDO->getImageByIdImage($id_image_musique);
            $image_path_musique = $image_musique->getImage() ? "../images/" . $image_musique->getImage() : '../images/default.jpg';
            
            ?>
            <div class="musique-container">
                <p>Son : <?php echo $musique->getNomMusique(); ?></p>
                <p>Durée : <?php echo $musique->getDureeMusique(); ?></p>
                <p>Nombre d'écoutes : <?php echo $musique->getNbStreams(); ?></p>
                <img class="genre-image" src="<?php echo $image_path_musique ?>" alt="Image de la musique <?php echo $musique->getNomMusique(); ?>"/>
                
                <!-- permet de liker une musique-->
                <div id="like" data-id="<?php echo $musique->getIdMusique(); ?>">
                <label class="container">
                            <input type="checkbox" checked>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        </label>
                </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
    
    // Récupère tous les éléments avec l'ID "like"
    const likeElements = document.querySelectorAll('#like');

    // Ajoute un écouteur d'événements à chaque élément
    likeElements.forEach(likeElement => {
        likeElement.addEventListener('change', async (event) => {
            const musiqueId = likeElement.getAttribute('data-id');
            const isChecked = event.target.checked;

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
            likeElement.closest('.musique-container').remove();
            // Vérifie si la requête a réussi
            if (response.ok) {
                console.log('Like ajouté ou supprimé');
                
            } else {
                console.error('Erreur lors de la requête');
            }
        });
    });
    </script>
</body>
</html>
