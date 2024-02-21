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