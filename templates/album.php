<?php
use Modele\modele_bd\AlbumPDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\LikerPDO;
use Modele\modele_bd\NoterPDO;
use Modele\modele_bd\RealiserParPDO;
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\PlaylistPDO;

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
$playlistPDO = new PlaylistPDO($pdo);

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

$file_attente_sons = array();
$id_musique_file_attente_sons = array();
foreach ($les_musiques as $musique){
    array_push($file_attente_sons, $musique->getSonMusique());
    array_push($id_musique_file_attente_sons, $musique->getIdMusique());
}
// Récupérer les musiques et les encoder en JSON
$musiques_json = json_encode($file_attente_sons);
// Récupérer les id_musiques et les encoder en JSON
$id_musiques_json = json_encode($id_musique_file_attente_sons);

$nom_utilisateur_connecte = "pas connecté";
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];

    $utilisateur = $utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte);

    $utilisteur_a_noteer=$noterPDO->getNoteByIdAlbumIdUtilisateur($id_album,$utilisateur->getIdUtilisateur());

    $note_album=$noterPDO->getMoyenneNoteByIdAlbum($id_album);
}
$utilisateur = $utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte);
$playlists_utilisateur = $playlistPDO->getPlaylistsByNomUtilisateur($nom_utilisateur_connecte);
$nbNote = $noterPDO->getNbPersonneAyantNote($id_album);

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
        echo json_encode(['success' => true, 'nvMoyenne' => $noterPDO->getMoyenneNoteByIdAlbum($id_album),'nbNotes' => $noterPDO->getNbPersonneAyantNote($id_album)]);
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

    @import url("https://fonts.googleapis.com/css?family=Fira+Sans");

    html,body {
        position: relative;
        min-height: 100vh;
        background-color: #FFF0F5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: "Fira Sans", Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    }

    .player {
        position: relative;
    }    
    .player .info {
            position: absolute;
            height: 60px;
            top: 0;
            opacity: 0;
            left: 10px;
            right: 10px;
            background-color: rgba(255, 255, 255, 0.5);
            padding: 5px 15px 5px 110px;
            border-radius: 15px;
            transition: all .5s ease;

            .artist,
            .name {
                display: block;
            }

            .name {
                color: #222;
                font-size: 16px;
                margin-bottom: 5px;
            }

            .artist {
                color: #999;
                font-size: 12px;
                margin-bottom: 8px;
            }

            .progress-bar {
                background-color: #ddd;
                height: 2px;
                width: 100%;
                position: relative;

                .bar {
                    position:absolute;
                    left: 0;
                    top: 0;
                    bottom: 0;
                    background-color: red;
                    width: 10%;
                    transition: all .2s ease;
                }
            }
            &.active {
                top: -60px;
                opacity: 1;
                transition: all .5s ease;
            }
        }
        .player .control-panel {
            position: relative;
            background-color: #fff;
            border-radius: 15px;
            width: 435px;
            height: 80px;
            z-index: 5;
            box-shadow: 0px 20px 20px 5px rgba(132, 132, 132, 0.3);
            
            .album-art {
                position: absolute;
                left: 20px;
                top: -15px;
                height: 80px;
                width: 80px;
                border-radius: 50%;
                box-shadow: 0px 0px 20px 5px rgba(0, 0, 0, 0);
                transform: scale(1);
                transition: all .5s ease;
        
                &::after {
                    content: '';
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    width: 15px;
                    height: 15px;
                    background-color: #fff;
                    border-radius: 50%;
                    z-index: 5;
                    transform: translate(-50%, -50%);
                    -webkit-transform: translate(-50%, -50%);
                }
                
                &::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    border-radius: 50%;
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: 80px;
                    background-image: 	url('<?php echo $image_path; ?>');
                }
            }
            
            &.active .album-art {
                box-shadow: 0px 0px 20px 5px rgba(0, 0, 0, 0.2);
                transform: scale(1.2);
                transition: all .5s ease;
            }
            
            &.active .album-art::before {
                animation: rotation 3s infinite linear;
                -webkit-animation: rotation 3s infinite linear;
                animation-fill-mode: forwards;
            }
            
            @keyframes rotation {
                0% {
                    transform: rotate(0deg);
                }
                
                100% {
                    transform: rotate(360deg);
                }
            }
            
            .controls {
                display: flex;
                justify-content: flex-end;
                height: 80px;
                padding: 0 15px;
                
                .prev, 
                .play, 
                .next,
                .restart {
                    width: 55px;
                    height: auto;
                    border-radius: 10px;
                    background-position: center center;
                    background-repeat: no-repeat;
                    background-size: 20px;
                    margin: 5px 0;
                    background-color: #fff;
                    cursor: pointer;
                    transition: background-color .3s ease;
                    -webkit-transition: background-color .3s ease;
                }
                
                .prev:hover, 
                .play:hover, 
                .next:hover,
                .restart:hover {
                    background-color: #eee;
                    transition: background-color .3s ease;
                    -webkit-transition: background-color .3s ease;
                }
                
                .prev {
                    background-image: url("../static/images/previous.png");
                }
                
                .play {
                    background-image: url("../static/images/play.png");
                }
                
                .next {
                    background-image: url("../static/images/next.png")
                }

                .restart {
                    background-image: url("../static/images/restart.png");
                }
            }
            
            &.active .controls .play {
                background-image: url("../static/images/pause.png")
            }
        }

        .duration{
            padding-left: 105px;
            font-size: x-small;
            margin-top: 10px;
        }

        .volume-label {
            font-size: x-small;
        }

        input[type="range"] {
            width: 100px;
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
    <?php if ($note_album > 0): ?>
        <p class="moyenne-album">Note moyenne de l'album : <span id="moyenne-note"><?php echo $note_album; ?></span></p>
    <?php else: ?>
        <p class="moyenne-album">Note moyenne de l'album : <span id="moyenne-note">Pas de note</span></p>
    <?php endif; ?>
    <p id="nbPersonnesNotes">Nombre de personne ayant noter : <?php echo $nbNote ?></p>

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
    <div class="player">
        <div id="info" class="info">
            <span class="name"></span>
            <span class="artist"><?php echo ($les_artistes[0])->getNomArtiste(); ?></span>
            <div class="progress-bar">
                <div class="bar">
                    <audio id="audio" controls style="display: none;">
                    <source src="" type="audio/mp3">
                    Your browser does not support the audio element.
                    </audio>
                </div>
            </div>
        </div>
        <div id="control-panel" class="control-panel">
            <div class="album-art"></div>
            <div class="controls">
                <div class="duration">
                    <div>
                        <span id="current-time">00:00</span> / <span id="total-time">00:00</span>
                        <div>
                            <label for="volume-slider" class="volume-label">Volume</label>
                            <input type="range" id="volume-slider" min="0" max="100" step="1" value="100">
                        </div>
                    </div>
                </div>
                <div id="prev" class="prev"></div>
                <div id="play" class="play"></div>
                <div id="next" class="next"></div>
                <div id="restart" class="restart"></div>
            </div>
        </div>
    </div>
    <ul id="file-attente"></ul>
    <div class="album-container">
        <h2>Liste des musiques de l'album</h2>
        <?php foreach ($les_musiques as $musique):?>
            <div class="musique-container">
            <p>Son : <?php echo $musique->getNomMusique(); ?></p>
            <p>Durée : <?php echo $musique->getDureeMusique(); ?></p>
            <p>Nombre d'écoutes : <?php echo $musique->getNbStreams(); ?></p>

            <!-- formulaire pour choisir la playlist dans laquelle ajouter la musique-->
            <form method="post" action="?action=ajouter_playlist">
                <input type="hidden" name="id_musique" value="<?php echo $musique->getIdMusique(); ?>">
                <select name="id_playlist">
                    <?php foreach ($playlists_utilisateur as $playlist): ?>
                        <option value="<?php echo $playlist->getIdPlaylist(); ?>"><?php echo $playlist->getNomPlaylist(); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Ajouter à la playlist</button>
            </form>
            
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
    // Injecter les données JSON dans une variable JavaScript
    const musiques = <?php echo $musiques_json; ?>;
    // Injecter les données JSON dans une variable JavaScript
    const id_musiques = <?php echo $id_musiques_json; ?>;
</script>
<script src="../static/script/son.js"></script>
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
                albumId,
            }),
        });
        

        if (response.ok) {
            // Fermez la pop-up
            document.querySelector('.modal').style.display = 'none';
            // Mettre à jour la note moyenne
            const moyenneNote = document.querySelector('#moyenne-note');
            const nbPersonnesNotes = document.querySelector('#nbPersonnesNotes');
            const jsonResponse = await response.json();
            const nvMoyenne = jsonResponse.nvMoyenne;
            const nbNotes = jsonResponse.nbNotes;
            moyenneNote.textContent = nvMoyenne;
            nbPersonnesNotes.textContent = "Nombre de personne ayant noter : " + nbNotes;


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