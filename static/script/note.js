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

