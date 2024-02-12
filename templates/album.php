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
$image_path = $image_album->getImage() ? "../images/" . urlencode($image_album->getImage()) : '../images/default.jpg';
$id_artistes = $realiserParPDO->getIdArtistesByIdAlbum($id_album);
$les_artistes = array();
foreach ($id_artistes as $id_artiste){
    array_push($les_artistes, $artistePDO->getArtisteByIdArtiste($id_artiste));
}
$les_musiques = $albumPDO->getMusiquesByIdAlbum($id_album);

$file_attente_sons = array();
foreach ($les_musiques as $musique){
    array_push($file_attente_sons, $musique->getSonMusique());
}
// Récupérer les musiques et les encoder en JSON
$musiques_json = json_encode($file_attente_sons);

$nom_utilisateur_connecte = "pas connecté";
if (isset($_SESSION["username"])) {
    $nom_utilisateur_connecte = $_SESSION["username"];
}
$utilisateur = $utilisateurPDO->getUtilisateurByNomUtilisateur($nom_utilisateur_connecte);

// vérifie si la requête est une requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

            .artist {
                color: #222;
                font-size: 16px;
                margin-bottom: 5px;
            }

            .name {
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
            width: 380px;
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
                .next {
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
                .next:hover {
                    background-color: #eee;
                    transition: background-color .3s ease;
                    -webkit-transition: background-color .3s ease;
                }
                
                .prev {
                    background-image: url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMS4xLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDI1MC40ODggMjUwLjQ4OCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjUwLjQ4OCAyNTAuNDg4OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjEyOHB4IiBoZWlnaHQ9IjEyOHB4Ij4KPGcgaWQ9IlByZXZpb3VzX3RyYWNrIj4KCTxwYXRoIHN0eWxlPSJmaWxsLXJ1bGU6ZXZlbm9kZDtjbGlwLXJ1bGU6ZXZlbm9kZDsiIGQ9Ik0yMzcuNDg0LDIyLjU4N2MtMy4yNjYsMC03LjU5MS0wLjQwMS0xMS4wNzIsMi4wMDVsLTkyLjI2NCw3Ny45MVYzNy4yNTIgICBjMC0yLjUwNywwLjA1Ny0xNC42NjYtMTMuMDA0LTE0LjY2NmMtMy4yNjUsMC03LjU5LTAuNDAxLTExLjA3MiwyLjAwNUw4LjEwNywxMTAuNjkzYy05LjY2OSw2LjY3NC03Ljk5NywxNC41NTEtNy45OTcsMTQuNTUxICAgcy0xLjY3MSw3Ljg3OCw3Ljk5NywxNC41NTFsMTAxLjk2NSw4Ni4xMDJjMy40ODIsMi40MDUsNy44MDcsMi4wMDQsMTEuMDcyLDIuMDA0YzEzLjA2MiwwLDEzLjAwNC0xMS43LDEzLjAwNC0xNC42NjZ2LTY1LjI0OSAgIGw5Mi4yNjQsNzcuOTExYzMuNDgyLDIuNDA1LDcuODA3LDIuMDA0LDExLjA3MiwyLjAwNGMxMy4wNjIsMCwxMy4wMDQtMTEuNywxMy4wMDQtMTQuNjY2VjM3LjI1MiAgIEMyNTAuNDg4LDM0Ljc0NiwyNTAuNTQ2LDIyLjU4NywyMzcuNDg0LDIyLjU4N3oiIGZpbGw9IiNjMmM2Y2YiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K);
                }
                
                .play {
                    background-image: url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMS4xLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDIzMi4xNTMgMjMyLjE1MyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjMyLjE1MyAyMzIuMTUzOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjEyOHB4IiBoZWlnaHQ9IjEyOHB4Ij4KPGcgaWQ9IlBsYXkiPgoJPHBhdGggc3R5bGU9ImZpbGwtcnVsZTpldmVub2RkO2NsaXAtcnVsZTpldmVub2RkOyIgZD0iTTIwMy43OTEsOTkuNjI4TDQ5LjMwNywyLjI5NGMtNC41NjctMi43MTktMTAuMjM4LTIuMjY2LTE0LjUyMS0yLjI2NiAgIGMtMTcuMTMyLDAtMTcuMDU2LDEzLjIyNy0xNy4wNTYsMTYuNTc4djE5OC45NGMwLDIuODMzLTAuMDc1LDE2LjU3OSwxNy4wNTYsMTYuNTc5YzQuMjgzLDAsOS45NTUsMC40NTEsMTQuNTIxLTIuMjY3ICAgbDE1NC40ODMtOTcuMzMzYzEyLjY4LTcuNTQ1LDEwLjQ4OS0xNi40NDksMTAuNDg5LTE2LjQ0OVMyMTYuNDcxLDEwNy4xNzIsMjAzLjc5MSw5OS42Mjh6IiBmaWxsPSIjYzJjNmNmIi8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==);
                }
                
                .next {
                    background-image: url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMS4xLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDI1MC40ODggMjUwLjQ4OCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjUwLjQ4OCAyNTAuNDg4OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjEyOHB4IiBoZWlnaHQ9IjEyOHB4Ij4KPGcgaWQ9Ik5leHRfdHJhY2tfMiI+Cgk8cGF0aCBzdHlsZT0iZmlsbC1ydWxlOmV2ZW5vZGQ7Y2xpcC1ydWxlOmV2ZW5vZGQ7IiBkPSJNMjQyLjM4MSwxMTAuNjkzTDE0MC40MTUsMjQuNTkxYy0zLjQ4LTIuNDA2LTcuODA1LTIuMDA1LTExLjA3MS0yLjAwNSAgIGMtMTMuMDYxLDAtMTMuMDAzLDExLjctMTMuMDAzLDE0LjY2NnY2NS4yNDlsLTkyLjI2NS03Ny45MWMtMy40ODItMi40MDYtNy44MDctMi4wMDUtMTEuMDcyLTIuMDA1ICAgQy0wLjA1NywyMi41ODcsMCwzNC4yODcsMCwzNy4yNTJ2MTc1Ljk4M2MwLDIuNTA3LTAuMDU3LDE0LjY2NiwxMy4wMDQsMTQuNjY2YzMuMjY1LDAsNy41OSwwLjQwMSwxMS4wNzItMi4wMDVsOTIuMjY1LTc3LjkxICAgdjY1LjI0OWMwLDIuNTA3LTAuMDU4LDE0LjY2NiwxMy4wMDMsMTQuNjY2YzMuMjY2LDAsNy41OTEsMC40MDEsMTEuMDcxLTIuMDA1bDEwMS45NjYtODYuMTAxICAgYzkuNjY4LTYuNjc1LDcuOTk3LTE0LjU1MSw3Ljk5Ny0xNC41NTFTMjUyLjA0OSwxMTcuMzY3LDI0Mi4zODEsMTEwLjY5M3oiIGZpbGw9IiNjMmM2Y2YiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K)
                }
            }
            
            &.active .controls .play {
                background-image: url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMS4xLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDIzMi42NzkgMjMyLjY3OSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjMyLjY3OSAyMzIuNjc5OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjEyOHB4IiBoZWlnaHQ9IjEyOHB4Ij4KPGcgaWQ9IlBhdXNlIj4KCTxwYXRoIHN0eWxlPSJmaWxsLXJ1bGU6ZXZlbm9kZDtjbGlwLXJ1bGU6ZXZlbm9kZDsiIGQ9Ik04MC41NDMsMEgzNS43OTdjLTkuODg1LDAtMTcuODk4LDguMDE0LTE3Ljg5OCwxNy44OTh2MTk2Ljg4MyAgIGMwLDkuODg1LDguMDEzLDE3Ljg5OCwxNy44OTgsMTcuODk4aDQ0Ljc0NmM5Ljg4NSwwLDE3Ljg5OC04LjAxMywxNy44OTgtMTcuODk4VjE3Ljg5OEM5OC40NCw4LjAxNCw5MC40MjcsMCw4MC41NDMsMHogTTE5Ni44ODIsMCAgIGgtNDQuNzQ2Yy05Ljg4NiwwLTE3Ljg5OSw4LjAxNC0xNy44OTksMTcuODk4djE5Ni44ODNjMCw5Ljg4NSw4LjAxMywxNy44OTgsMTcuODk5LDE3Ljg5OGg0NC43NDYgICBjOS44ODUsMCwxNy44OTgtOC4wMTMsMTcuODk4LTE3Ljg5OFYxNy44OThDMjE0Ljc4MSw4LjAxNCwyMDYuNzY3LDAsMTk2Ljg4MiwweiIgZmlsbD0iI2MyYzZjZiIvPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=)
            }
        }

        .duration{
            padding-left: 95px;
            font-size: x-small;
            margin-top: 10px;
            margin-right: 23px;
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
    <div class="player">
        <div id="info" class="info">
            <span class="artist"><?php echo ($les_artistes[0])->getNomArtiste(); ?></span>
            <span class="name"></span>
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
                    <span id="current-time">00:00</span> / <span id="total-time">00:00</span>
                </div>
                <div id="prev" class="prev"></div>
                <div id="play" class="play"></div>
                <div id="next" class="next"></div>
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
</body>
</html>
