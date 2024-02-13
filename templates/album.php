<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\LikerPDO;
use Modele\modele_bd\NoterPDO;
use Modele\modele_bd\RealiserParPDO;
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$albumPDO = new AlbumPDO($pdo);
$imagePDO = new ImagePDO($pdo);
$realiserParPDO = new RealiserParPDO($pdo);
$artistePDO = new ArtistePDO($pdo);
$likePDO = new LikerPDO($pdo);
$utilisateurPDO = new UtilisateurPDO($pdo);
$noterPDO = new NoterPDO($pdo);

// Récupération de l'id de l'album
$id_album = intval($_GET['id_album']);
$album = $albumPDO->getAlbumByIdAlbum($id_album);
$image_album = $imagePDO->getImageByIdImage($album->getIdImage());
$image_path = $image_album->getImage() ? "../images/" . urlencode($image_album->getImage()) : '../images/default.jpg';
$id_artistes = $realiserParPDO->getIdArtistesByIdAlbum($id_album);
$les_artistes = array();
foreach ($id_artistes as $id_artiste){
    array_push($les_artistes, $artistePDO->getArtisteByIdArtiste($id_artiste));
}
$les_musiques = $albumPDO->getMusiquesByIdAlbum($id_album);
$nom_utilisateur_connecte = "pas connecté";
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];

    $utilisateur = $utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte);

    $utilisteur_a_noteer=$noterPDO->getNoteByIdAlbumIdUtilisateur($id_album,$utilisateur->getIdUtilisateur());

    $note_album=$noterPDO->getMoyenneNoteByIdAlbum($id_album);
}


// vérifie si la requête est une requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si la clé 'albumNote' existe dans $_POST, cela signifie que la note de l'album est envoyée
    if (isset($_POST['albumNote'])) {
        // Récupère les données de la requête
        $albumNote = intval($_POST['albumNote']);
        $isChecked = $_POST['isChecked'] === 'true';
        
        if ($utilisteur_a_noteer>0){
            $noterPDO->mettreAJourNote($id_album, $utilisateur->getIdUtilisateur(), $albumNote);
        }
        else {
            $noterPDO->ajouterNoter($id_album, $utilisateur->getIdUtilisateur(), $albumNote);
        }
        // envoie une réponse JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    // Si la clé 'musiqueId' existe dans $_POST, cela signifie que le like pour une musique est envoyé
    if (isset($_POST['musiqueId'])) {
        // Récupère les données de la requête
        $musiqueId = intval($_POST['musiqueId']);
        $isChecked = $_POST['isChecked'] === 'true';

        // ajoute ou supprime le like
        if ($isChecked) {
            $likePDO->ajouterLiker($musiqueId, $utilisateur->getIdUtilisateur());
        } else {
            $likePDO->supprimerLiker($musiqueId, $utilisateur->getIdUtilisateur());
        }

        // envoie une réponse JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music'O</title>
    <link rel="stylesheet" href="../static/style/testavis.css">
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
        <h2>Titre : <?php echo $album->getTitre(); ?></h2>
        <p>Année de sortie : <?php echo $album->getAnneeSortie(); ?></p>
        <p>Durée de l'album : <?php echo $albumPDO->getDureeTotalByIdAlbum($id_album); ?></p>
        <img class="album-image" src="<?php echo $image_path ?>" alt="Image de l'album <?php echo $album->getTitre(); ?>"/>
    </div>
    <div>
        <?php if ($note_album>0): ?>
            <p>Note moyenne de l'album : <?php echo $note_album; ?></p>
        <?php else: ?>
            <p>Pas de note</p>
        <?php endif; ?>
    </div>
    <button class="feedback-btn">
        Avis
      </button>
      <div class="modal">
        <button class="close">
          
        </button>
        
        <h3 class="title">
          Avez-vous aimé cet album ?
        </h3>
        
        <form action="" class="feedback">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="score">
                <?php if ($utilisteur_a_noteer==$i): ?>
                    <input  id="score<?php echo $i ?>" type="radio" value="<?php echo $i ?>" name="score">
                    <label class="active" for="score<?php echo $i ?>"><?php echo $i ?></label>
                
                <?php else: ?>
                    <input id="score<?php echo $i ?>" type="radio" value="<?php echo $i ?>" name="score">
                    <label for="score<?php echo $i ?>"><?php echo $i ?></label>
                <?php endif; ?>
                
            </div>
        <?php endfor; ?>
        </form>

        
        
        <div class="options">
          <button class="cancel" type="button">Annuler</button>
          <button class="submit" type="submit" data-album-id="<?php echo $id_album; ?>">Confirmer</button>
        </div>
      </div>
    <div class="album-container">
        <h2>Liste des musiques de l'album</h2>
        <?php foreach ($les_musiques as $musique):?>
            <div class="musique-container">
            <p>Son : <?php echo $musique->getNomMusique(); ?></p>
            <p>Durée : <?php echo $musique->getDureeMusique(); ?></p>
            <p>Nombre d'écoutes : <?php echo $musique->getNbStreams(); ?></p>
            
            <!-- permet de liker une musique-->
            <div id="like" data-id="<?php echo $musique->getIdMusique(); ?>">
            <?php if (isset($utilisateur)): ?>
                    <?php if ($likePDO->verifieMusiqueLiker($musique->getIdMusique(),$utilisateur->getIdUtilisateur())): ?>   
                            <label class="container">
                                <input type="checkbox" checked>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            </label>
                    <?php else: ?>
                            <label class="container">
                                <input type="checkbox">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            </label>
                    <?php endif; ?>
                <?php else: ?>
                    <label class="container">
                        <input type="checkbox">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    </label>
            <?php endif; ?>
            </div>
        </div>

        <?php endforeach; ?>
    </div>
    <div class="album-container">
        <?php foreach ($les_artistes as $artiste):
        $image_artiste = $imagePDO->getImageByIdImage($artiste->getIdImage());
        $image_path_artiste = $image_artiste->getImage() ? "../images/" . $image_artiste->getImage() : '../images/default.jpg';
        ?>
        <p>Nom artiste : <?php echo $artiste->getNomArtiste(); ?></p>
        <img class="album-image" src="<?php echo $image_path_artiste ?>" alt="Image de l'artiste <?php echo $artiste->getNomArtiste(); ?>"/>
        <?php endforeach; ?>
    </div>
</div>
<script>
    
    // Récupère tous les éléments avec l'ID "like"
    const likeElements = document.querySelectorAll('#like');

    // Ajoute un écouteur d'événements à chaque élément
    likeElements.forEach(likeElement => {
        likeElement.addEventListener('change', async (event) => {
            // Vérifie si l'utilisateur est connecté
            if (!<?php echo isset($utilisateur) ? 'true' : 'false' ?>) {
                // Redirige l'utilisateur vers la page de connexion
                window.location.href = '/?action=connexion_inscription';
                return;
            }

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

            // Vérifie si la requête a réussi
            if (response.ok) {
                console.log('Like ajouté ou supprimé');
            } else {
                console.error('Erreur lors de la requête');
            }
        });
    });
</script>

<script>

document.addEventListener('DOMContentLoaded', function () {
    const scoreInputs = document.querySelectorAll('.feedback input[name="score"]');
    const submitBtn = document.querySelector('.submit');
    const albumId = document.querySelector('.feedback-btn').getAttribute('data-album-id');

    submitBtn.addEventListener('click', async function () {
        const selectedScore = document.querySelector('.feedback input[name="score"]:checked');
        if (!selectedScore) {
            console.log('Aucune note sélectionnée');
            return;
        }
        if (!<?php echo isset($utilisateur) ? 'true' : 'false' ?>) {
                // Redirige l'utilisateur vers la page de connexion
                window.location.href = '/?action=connexion_inscription';
                return;
            }

        const albumNote = parseInt(selectedScore.value); // Récupérez la note sélectionnée
        const isChecked = true; // Mettez à jour en fonction de votre logique

        // enlever le css des autres boutons et garder celui du bouton cliqué
        scoreInputs.forEach(input => {
            input.nextElementSibling.classList.remove('active');
        });

        const response = await fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                albumNote,
                isChecked,
                albumId, // Ajoutez l'ID de l'album à la requête POST
            }),
        });

        if (response.ok) {
            console.log('Note de l album ajoutée avec succès');
            // close la pop-up
            document.querySelector('.modal').style.display = 'none';
        } else {
            console.error('Erreur lors de la requête');
        }
    });
});

</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const scoreInputs = document.querySelectorAll('.feedback input[name="score"]');

    // Ajoute un écouteur d'événements à chaque input de note
    scoreInputs.forEach(input => {
        input.addEventListener('change', function () {
            // enlever le css des autres boutons
            scoreInputs.forEach(otherInput => {
                otherInput.nextElementSibling.classList.remove('active');
            });

            // mettre en surbrillance le bouton sélectionné
            if (this.checked) {
                this.nextElementSibling.classList.add('active');
            }
        });
    });
});

</script>
<script src="../static/script/testavis.js"></script>
</body>
</html>