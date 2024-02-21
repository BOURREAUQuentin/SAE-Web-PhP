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