<?php
use Modele\modele_bd\MusiquePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;
use Modele\modele_bd\AlbumPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$musiquePDO = new MusiquePDO($pdo);
$imagePDO = new ImagePDO($pdo);
$utilisateurPDO = new utilisateurPDO($pdo);
$albumPDO = new AlbumPDO($pdo);

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

// Récupération de la liste des musiques et albums
$les_musiques = $musiquePDO->getMusiques();
$les_albums = $albumPDO->getAlbums();
?>
<script>
    function confirmSuppressionMusique(id_musique) {
        // Affiche une boîte de dialogue de confirmation
        var confirmation = confirm("Êtes-vous sûr de vouloir supprimer cette musique ?");
        // Si l'utilisateur clique sur OK, retourne true (continue la suppression)
        // Sinon, retourne false (arrête la suppression)
        if (confirmation) {
            window.location.href = "?action=supprimer_musique&id_musique=" + id_musique;
        }
        return false;
    }
    function showEditForm(id_musique) {
        // Récupérer le formulaire de modification correspondant à l'ID de la musique
        var editForm = document.getElementById("editForm_" + id_musique);
        // Afficher le formulaire de modification en le rendant visible
        editForm.style.display = "block";
        // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
        return false;
    }
    function cancelEdit(id_musique) {
        // Récupérer le formulaire de modification correspondant à l'ID de la musique
        var editForm = document.getElementById("editForm_" + id_musique);
        // Masquer le formulaire de modification
        editForm.style.display = "none";

        // Récupérer le champ de saisie correspondant
        var nomMusiqueInput = document.getElementById("nouveau_nom_musique" + id_musique);

        // Masquer le champ de saisie correspondant
        nomMusiqueInput.style.display = "none";
    }
</script>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lavound</title>
    <link rel="stylesheet" href="../static/style/genre.css">
    <link rel="stylesheet" href="../static/style/admin.css">
</head>
<body ng-app="app">
	<section class='global-wrapper' ng-controller="ctrl">
        <aside>
                <img src="../static/images/logo.png" alt="" width="80px" height="80px">
                <!--top nav -->
                <ul>
                    <li>
            <a href="#" onclick="toggleSearchBar()">
                <div class="nav-item">
                    <img src="../static/images/loupe.png" alt="">
                    <span>Recherche</span>
                </div>
            </a>
        </li>
                    <li class="active">
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
                            <?php if ($est_admin) : ?>
                                <a href="/?action=admin" class="para">Admin</a>
                            <?php endif; ?>
                            <?php if (isset($_SESSION["username"])) : ?>
                                <a href="/?action=profil" class="para"><p>Mon profil</p></a>
                                <a href="/?action=logout" class="para">Déconnexion</a>
                            <?php else: ?>
                                <a href="?action=connexion_inscription" class="para">Connexion</a>
                            <?php endif; ?>
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
            <div class="center-part">
                <h3 class="T-part">Nouvelle musique</h3>
                <div class="album-container">
                    <!-- Formulaire pour ajouter une nouvelle musique -->
                    <form action="?action=ajouter_musique" method="post" enctype="multipart/form-data">
                        <label for="nom_musique">Nom de la musique :</label>
                        <input type="text" id="nom_musique" name="nom_musique" required>
                        <label for="album">Album associé :</label>
                        <select name="album" id="album">
                        <?php foreach ($les_albums as $album): ?>
                            <option value="<?php echo $album->getIdAlbum(); ?>"><?php echo $album->getTitre(); ?></option>
                        <?php endforeach; ?>
                        </select>
                        <label for="fichier_mp3">Fichier MP3 :</label>
                        <input type="file" id="fichier_mp3" name="fichier_mp3" accept=".mp3" required>
                        <!-- balise pour obtenir les informations sur la durée du fichier audio (pas affiché à l'utilisateur) -->
                        <audio id="audioPreview" style="display:none;" controls></audio>
                        <!-- pour afficher la durée du fichier audio -->
                        <input type="hidden" id="duree_audio" name="duree_audio">
                        <button type="submit">Ajouter une musique</button>
                    </form>
                </div>
                <h3 class="T-part">Les musiques</h3>
                <?php foreach ($les_musiques as $musique):
                    $id_image_musique = $musiquePDO->getIdImageByIdMusique($musique->getIdMusique());
                    $image_musique = $imagePDO->getImageByIdImage($id_image_musique);
                    $image_path = $image_musique->getImage() ? "../images/" . $image_musique->getImage() : '../images/default.jpg';
                    ?>
                    <div class="album-container">
                        <img class="album-image" src="<?php echo $image_path ?>" alt="Image de la musique <?php echo $musique->getNomMusique(); ?>"/>
                        <div id="container_infos_album">
                            <div>
                                <p>Nom : <?php echo $musique->getNomMusique(); ?></p>
                            </div>
                            <div>
                                <p>Durée : <?php echo $musique->getDureeMusique(); ?></p>
                            </div>
                            <div>
                                <p>Nom album : <?php echo ($albumPDO->getAlbumByIdAlbum($musique->getIdAlbum()))->getTitre(); ?></p>
                            </div>
                        </div>
                        <a href="#" onclick="return confirmSuppressionMusique(<?php echo $musique->getIdMusique(); ?>)">
                            <button class="view-album-button">Supprimer la musique</button>
                        </a>
                        <!-- Bouton de modification -->
                        <button class="view-album-button" onclick="showEditForm(<?php echo $musique->getIdmusique(); ?>)">Modifier la musique</button>
                        <!-- Formulaire de modification -->
                        <form id="editForm_<?php echo $musique->getIdMusique(); ?>" style="display: none;" action="/?action=modifier_musique&id_musique=<?php echo $musique->getIdMusique(); ?>" method="post">
                            <input type="hidden" name="id_musique" value="<?php echo $musique->getIdMusique(); ?>">
                            <label for="nouveau_nom">Nouveau nom de la musique :</label>
                            <input type="text" id="nouveau_nom" name="nouveau_nom" value="<?php echo $musique->getNomMusique(); ?>" required>
                            <button class="view-album-button" type="submit">Modifier</button>
                            <!-- Bouton Annuler -->
                            <button class="view-album-button" type="button" onclick="cancelEdit(<?php echo $musique->getIdMusique(); ?>)">Annuler</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
	</section>
    <script>
        // Fonction pour obtenir la durée formatée du fichier audio
        function obtenirDureeFormattee(duree) {
            var minutes = Math.floor(duree / 60);
            var secondes = Math.floor(duree % 60);
            // Formatage de la durée au format MM:SS
            var duree_formattee = (minutes < 10 ? '0' : '') + minutes + ':' + (secondes < 10 ? '0' : '') + secondes;
            return duree_formattee;
        }

        // Fonction pour obtenir la durée du fichier audio
        function obtenirDureeAudio(input) {
            if (input.files && input.files[0]) {
                var audio = document.getElementById('audioPreview');
                var file = input.files[0];
                var reader = new FileReader();

                reader.onload = function(e) {
                    // Charge le fichier audio pour obtenir ses informations
                    audio.src = e.target.result;
                };

                // Attente que les métadonnées du fichier audio soient chargées
                audio.onloadedmetadata = function() {
                    // Affiche la durée du fichier audio formatée dans l'élément HTML
                    var duree_formattee = obtenirDureeFormattee(audio.duration);
                    document.getElementById('duree_audio').value = duree_formattee;
                    console.log(duree_formattee);
                };

                reader.readAsDataURL(file);
            }
        }

        // Ajout d'un écouteur d'événements au champ de fichier MP3 pour détecter les changements
        document.getElementById('fichier_mp3').addEventListener('change', function() {
            obtenirDureeAudio(this);
        });
    </script>
</body>
</html>