const feedbackBtn = document.querySelector('.feedback-btn');
const modal = document.querySelector('.modal-note');

feedbackBtn.addEventListener('pointerdown', () => {
   modal.style.display = 'block';
   setTimeout(() => modal.classList.add('show'), 0)
});

modal.querySelector('.close-note').addEventListener('pointerdown', () => {
  hideModal();
});

modal.querySelector('.cancel').addEventListener('pointerdown', () => {
  hideModal();
});

document.addEventListener('pointerdown', (e) => {
  if (!e.composedPath().includes(modal)) {
    hideModal();  
  }
});

modal.addEventListener('transitionend', function(e) {
  if (!this.classList.contains('show')) {
    if (e.propertyName == 'transform') {
      this.style.display = '';
    }
  }
});

function hideModal() {
  modal.classList.remove('show')
}

// partie sur la gestion de la mise d'une note sur un album
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
      if (!utilisateur_est_connecte) {
              // Redirige l'utilisateur vers la page de connexion
              window.location.href = '/?action=connexion_inscription';
              return;
          }

      const albumNote = parseInt(selectedScore.value); // Récupération de la note sélectionnée
      const isChecked = true;

      // Enleve le css des autres boutons et garder celui du bouton cliqué
      scoreInputs.forEach(input => {
          input.nextElementSibling.classList.remove('active-note');
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
          document.querySelector('.modal-note').style.display = 'none';
          // Mettre à jour la note moyenne
          const moyenneNote = document.querySelector('#moyenne-note');
          const nbPersonnesNotes = document.querySelector('#nbPersonnesNotes');
          const jsonResponse = await response.json();
          const nvMoyenne = jsonResponse.nvMoyenne;
          const nbNotes = jsonResponse.nbNotes;
          moyenneNote.textContent = nvMoyenne;
          nbPersonnesNotes.textContent = "Nombre de notes : " + nbNotes;
      }
      else {
          console.error('Erreur lors de la requête');
      }    
  });
});

document.addEventListener('DOMContentLoaded', function () {
const scoreInputs = document.querySelectorAll('.feedback input[name="score"]');

// Ajoute un écouteur d'événements à chaque input de note
scoreInputs.forEach(input => {
  input.addEventListener('change', function () {
      // enlever le css des autres boutons
      scoreInputs.forEach(otherInput => {
          otherInput.nextElementSibling.classList.remove('active-note');
      });

      // mettre en surbrillance le bouton sélectionné
      if (this.checked) {
          this.nextElementSibling.classList.add('active-note');
      }
  });
});
});