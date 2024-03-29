var $firstButton = $(".first"),
  $secondButton = $(".second"),
  $input = $("input"),
  $name = $(".name"),
  $more = $(".more"),
  $yourname = $(".yourname"),
  $reset = $(".reset"),
  $ctr = $(".container");

$firstButton.on("click", function(e){
  $(this).text("Sauvegarde...").delay(900).queue(function(){
    var nomPlaylist = $("#nom_playlist").val();
    $(".slider-two label.input input").val(nomPlaylist);
    $ctr.addClass("center slider-two-active").removeClass("full slider-one-active");
  });
  e.preventDefault();
});

$secondButton.on("click", function(e){
  $(this).text("Sauvegarde...").delay(900).queue(function(){
    $ctr.addClass("full slider-three-active").removeClass("center slider-two-active slider-one-active");
    $name = $name.val();
    if($name == "") {
      $yourname.html("Anonyme!");
    }
    else { $yourname.html($name+"!"); }
  });
  e.preventDefault();
});

$(document).ready(function() {
  $(".reset").on("click", function(e) {
      e.preventDefault();
      console.log("Le bouton Ajouter a été cliqué.");
      $(".slider-form.slider-two").submit();
  });
});
