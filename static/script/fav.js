var element = document.getElementById('buttonfav');
function toggleBackgroundColor(){
    if (element.classList.contains('background')) {
        element.classList.remove('background');
      } else {
        element.classList.add('background');
      }
}

