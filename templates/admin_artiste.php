<?php
use Modele\modele_bd\ArtistePDO;
use Modele\modele_bd\ImagePDO;
use Modele\modele_bd\UtilisateurPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:Data/sae_php.db');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Instanciation des classes PDO
$artistePDO = new ArtistePDO($pdo);
$imagePDO = new ImagePDO($pdo);
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

// Récupération de la liste des artistes
$les_artistes = $artistePDO->getArtistes();
?>
<script>
    function confirmSuppressionArtiste(id_artiste) {
        // Affiche une boîte de dialogue de confirmation
        console.log(id_artiste);
        var confirmation = confirm("Êtes-vous sûr de vouloir supprimer l'artiste ?");
        // Si l'utilisateur clique sur OK, retourne true (continue la suppression)
        // Sinon, retourne false (arrête la suppression)
        if (confirmation) {
            window.location.href = "?action=supprimer_artiste&id_artiste=" + id_artiste;
        }
        return false;
    }


    function showEditForm(artisteId) {
        // Récupérer le formulaire de modification correspondant à l'ID de l'artiste
        var editForm = document.getElementById("editForm_" + artisteId);
        // Afficher le formulaire de modification en le rendant visible
        editForm.style.display = "block";
        // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
        return false;
    }

    function cancelEdit(artisteId) {
        // Récupérer le formulaire de modification correspondant à l'ID de l'artiste
        var editForm = document.getElementById("editForm_" + artisteId);
        // Masquer le formulaire de modification
        editForm.style.display = "none";
        // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
        return false;
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
                <h3 class="T-part">Nouveau artiste</h3>
                <div class="album-container">
                    <!-- Formulaire pour ajouter un nouvel artiste -->
                    <form action="?action=ajouter_artiste" method="post" enctype="multipart/form-data">
                        <div class="form-ajout">
                            <div class="container">
                                <div class="header">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> 
                                    <path d="M7 10V9C7 6.23858 9.23858 4 12 4C14.7614 4 17 6.23858 17 9V10C19.2091 10 21 11.7909 21 14C21 15.4806 20.1956 16.8084 19 17.5M7 10C4.79086 10 3 11.7909 3 14C3 15.4806 3.8044 16.8084 5 17.5M7 10C7.43285 10 7.84965 10.0688 8.24006 10.1959M12 12V21M12 12L15 15M12 12L9 15" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg> <p>Parcourir l'image à télécharger</p>
                                    <img id="preview-image" src="#" alt="Preview Image" style="display: none;">
                                </div>
                                <label for="file" class="footer"> 
                                    <svg fill="#000000" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M15.331 6H8.5v20h15V14.154h-8.169z"></path><path d="M18.153 6h-.009v5.342H23.5v-.002z"></path></g></svg> 
                                    <p id="upload-text">Pas d'image sélectionnée</p> 
                                </label>
                                <input id="file" name="image_artiste" type="file" accept="image/jpeg"> 
                            </div>
                            <div class="infos-new-album">
                                <div class="flex-container">
                                    <div class="flex-item">
                                        <div class="input-simple">
                                            <label for="nom_artiste">Nom de l'artiste :</label>
                                            <input class="input-infos" type="text" id="nom_artiste" name="nom_artiste" required>
                                        </div>
                                    </div>
                                </div>
                                <button class="view-album-button" type="submit">Ajouter un artiste</button>
                            </div>
                        </div>
                    </form>
                </div>
                <h3 class="T-part">Les artistes</h3>
                <?php foreach ($les_artistes as $artiste):
                    $image_artiste = $imagePDO->getImageByIdImage($artiste->getIdImage());
                    $image_path = $image_artiste->getImage() ? "../images/" . $image_artiste->getImage() : '../images/default.jpg';
                    ?>
                    <div class="album-container">
                        <img class="album-image" src="<?php echo $image_path ?>" alt="Image de l'artiste <?php echo $artiste->getNomArtiste(); ?>"/>
                        <div id="container_infos_album">
                            <div>
                                <p>Nom : <?php echo $artiste->getNomArtiste(); ?></p>
                            </div>
                        </div>
                        <a href="/?action=artiste&id_artiste=<?php echo $artiste->getIdArtiste(); ?>">
                            <button class="view-album-button">Voir l'artiste</button>
                        </a>
                        <!-- Bouton de Suppression -->
                        <a href="#" onclick="return confirmSuppressionArtiste(<?php echo $artiste->getIdArtiste() ?>)">
                            <button class="view-album-button">Supprimer l'artiste</button>
                        </a>
                        <!-- Bouton de modification -->
                        <button class="view-album-button" onclick="showEditForm(<?php echo $artiste->getIdArtiste(); ?>)">Modifier l'artiste</button>
                        <!-- Formulaire de modification -->
                        <form id="editForm_<?php echo $artiste->getIdArtiste(); ?>" style="display: none;" action="/?action=modifier_artiste&id_artiste=<?php echo $artiste->getIdArtiste() ?>" method="post">
                            <input type="hidden" name="id_artiste" value="<?php echo $artiste->getIdArtiste() ?>">
                            <label for="nouveau_nom">Nom de l'artiste :</label>
                            <input type="text" id="nouveau_nom" name="nouveau_nom" value="<?php echo $artiste->getNomArtiste(); ?>" required>
                            <button class="view-album-button" type="submit">Modifier</button>
                            <!-- Bouton Annuler -->
                            <button class="view-album-button" type="button" onclick="cancelEdit(<?php echo $artiste->getIdArtiste(); ?>)">Annuler</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                </div>
        </main>
	</section>
    <script src="../static/script/search.js"></script>
    <script>
        const previewImage = document.getElementById('preview-image');
        const uploadText = document.getElementById('upload-text');
        const input = document.getElementById('file');
        const header = document.querySelector('.header'); // Sélectionnez l'élément contenant le texte "Browse File to upload!"

        // Ajoutez un écouteur d'événements pour détecter les changements dans le champ de fichier
        input.addEventListener('change', function() {
            // Vérifiez si des fichiers ont été sélectionnés
            if (input.files && input.files[0]) {
                // Créez un objet URL à partir du premier fichier sélectionné
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Mettez à jour l'attribut src de l'élément img avec l'URL de l'image
                    previewImage.src = e.target.result;
                    // Réduisez la taille de l'image à 206x120 pixels
                    previewImage.style.width = '200px';
                    previewImage.style.height = '180px';
                    // Affichez l'aperçu de l'image
                    previewImage.style.display = 'block';
                    // Affichez le nom de l'image sélectionnée à la place de "No uploaded image"
                    uploadText.textContent = input.files[0].name;
                    // Supprimez tous les enfants du header sauf l'image de prévisualisation
                    while (header.firstChild !== previewImage) {
                        header.removeChild(header.firstChild);
                    }
                }
                // Lisez le contenu du premier fichier sélectionné en tant qu'URL de données
                reader.readAsDataURL(input.files[0]);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action="?action=ajouter_artiste"]');
        const fileInput = document.getElementById('file');

        form.addEventListener('submit', function(event) {
            if (fileInput.files.length === 0) {
                event.preventDefault(); // Empêche la soumission du formulaire
                alert('Veuillez sélectionner une image avant de soumettre le formulaire.');
            }
        });
    });
    </script>
</body>
</html>