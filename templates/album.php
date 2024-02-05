<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\LikerPDO;
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

// Récupération de l'id de l'album
$id_album = intval($_GET['id_album']);
$album = $albumPDO->getAlbumByIdAlbum($id_album);
$image_album = $imagePDO->getImageByIdImage($album->getIdImage());
$image_path = $image_album->getImage() ? "../images/" . $image_album->getImage() : '../images/default.jpg';
$id_artistes = $realiserParPDO->getIdArtistesByIdAlbum($id_album);
$les_artistes = array();
foreach ($id_artistes as $id_artiste){
    array_push($les_artistes, $artistePDO->getArtisteByIdArtiste($id_artiste));
}
$les_musiques = $albumPDO->getMusiquesByIdAlbum($id_album);
$utilisateur = $utilisateurPDO->getUtilisateurByNomUtilisateur($_SESSION['username']);
// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_musique = $_POST['id_musique'];
    $isChecked = $_POST['isChecked'];
    if ($isChecked) {
        $likePDO->ajouterLiker($id_musique, $utilisateur->getIdUtilisateur());
    } else {
        $likePDO->supprimerLiker($id_musique, $utilisateur->getIdUtilisateur());
    }
    exit(json_encode(['status' => 'success']));
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
        <h2>Titre : <?php echo $album->getTitre(); ?></h2>
        <p>Année de sortie : <?php echo $album->getAnneeSortie(); ?></p>
        <p>Durée de l'album : <?php echo $albumPDO->getDureeTotalByIdAlbum($id_album); ?></p>
        <img class="album-image" src="<?php echo $image_path ?>" alt="Image de l'album <?php echo $album->getTitre(); ?>"/>
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
    document.querySelectorAll('#like').forEach(like => {
        like.addEventListener('change', async (e) => {
            const idMusique = like.dataset.id;
            const isChecked = e.target.checked;
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id_musique=${idMusique}&isChecked=${isChecked}`
            });
            const data = await response.json();
            if (data.status === 'success') {
                console.log('Like ajouté ou supprimé');
            }
        });
    });
</script>
</body>
</html>
