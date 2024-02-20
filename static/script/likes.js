// Récupère tous les éléments avec l'ID "like"
const likeElements = document.querySelectorAll('#buttonfav');

// Ajoute un écouteur d'événements à chaque élément
likeElements.forEach(likeElement => {
    likeElement.addEventListener('click', async (event) => {
        // Vérifie si l'utilisateur est connecté
        if (!utilisateur_est_connecte) {
            // Redirige l'utilisateur vers la page de connexion
            window.location.href = '/?action=connexion_inscription';
            return;
        }

        const musiqueId = likeElement.value;
        const isChecked = likeElement.classList.contains('background');

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

        // Appeler la fonction pour mettre à jour l'image
        updateImageSource(!isChecked, likeElement);

        // Vérifie si la requête a réussi
        if (response.ok) {
            console.log('Like ajouté ou supprimé');
            // Ajoute ou supprime la classe "background" selon l'état précédent
            likeElement.classList.toggle('background');
        } else {
            console.error('Erreur lors de la requête');
        }
    });
});

function updateImageSource(isLiked, buttonElement) {
    const imgElement = buttonElement.querySelector('.fav');
    if (isLiked) {
        imgElement.src = '../static/images/fav_rouge.png';
    } else {
        imgElement.src = '../static/images/fav_noir.png';
    }
}