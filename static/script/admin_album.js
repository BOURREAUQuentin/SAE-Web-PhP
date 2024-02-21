function confirmSuppressionAlbum(id_album) {
    // Affiche une boîte de dialogue de confirmation
    var confirmation = confirm("Êtes-vous sûr de vouloir supprimer cet album ?");
    // Si l'utilisateur clique sur OK, retourne true (continue la suppression)
    // Sinon, retourne false (arrête la suppression)
    if (confirmation) {
        window.location.href = "?action=supprimer_album&id_album=" + id_album;
    }
    return false;
}
function showEditForm(albumId) {
    // Récupérer le formulaire de modification correspondant à l'ID de l'album
    var editForm = document.getElementById("editForm_" + albumId);
    // Afficher le formulaire de modification en le rendant visible
    editForm.style.display = "block";
    // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
    return false;
}
function cancelEdit(albumId) {
    // Récupérer le formulaire de modification correspondant à l'ID de l'album
    var editForm = document.getElementById("editForm_" + albumId);
    // Masquer le formulaire de modification
    editForm.style.display = "none";

    // Récupérer les champs de saisie correspondants
    var nomAlbumInput = document.getElementById("nouveau_nom_album_" + albumId);
    var anneeSortieInput = document.getElementById("nouvelle_annee_sortie_" + albumId);

    // Masquer les champs de saisie correspondants
    nomAlbumInput.style.display = "none";
    anneeSortieInput.style.display = "none";
}

// partie pour la preview de l'image
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
const form = document.querySelector('form[action="?action=ajouter_album"]');
const fileInput = document.getElementById('file');

form.addEventListener('submit', function(event) {
    if (fileInput.files.length === 0) {
        event.preventDefault(); // Empêche la soumission du formulaire
        alert('Veuillez sélectionner une image avant de soumettre le formulaire.');
    }
});
});