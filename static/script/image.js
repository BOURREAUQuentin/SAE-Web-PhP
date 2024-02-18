//Global variables
var file, file_name, ext, formData = new FormData(), data, xhr, url = "https://bboysdreamspells.000webhostapp.com/dragdrop/root/index.php", allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

// preventing page from redirecting
$("html").on("dragover", function(e) { stop(e); });

// on drop append file
$("html").on("drop", function(e) {
    stop(e);
    file = e.originalEvent.dataTransfer.files[0].name;
    formData.append('file', e.originalEvent.dataTransfer.files[0]);
    append_file(e);
});

//Onchange input file
$(".dragger input[type='file']").on("change", function(e){
    let path = e.target.files;
    file = path[0].name;
    formData.append('file', $(this)[0].files[0]);
    append_file(e);
});

//Append file
function append_file(e){
    if(!allowedExtensions.exec(file)){
        popup(0, "Ce fichier n'est pas accepté");
    } else {
        if ($(".file_preview li").length >= 1){
            popup(0, "Maximum de fichier atteint");
          return 1;
        } else {
            ext = file.split('.').pop();
            file_name = file.split('.').slice(0, -1).join('.').replace(/\s+/g, '_');
            file_name.length > 10 ? file_name = file_name.substr(0,15) + "......" +ext : file_name = file;
            $(".file_preview").append('<li class="fixed_flex del" ><progress value="0" max="100"></progress></li>');
        }
    }
    Upload(formData, e);
}

//Remove appended files
function remove(val, e){
    $.ajax({
        url: url,
        type: 'POST',
        data: "FormType=pathremove&path="+val,
        beforeSend: function() {
            $(".dragger").css("opacity","0.6");
            $(".dragger").css("pointer-events","none");
        },
        success: function(response){
          return e.parentNode.remove();
        },
        complete: function() {
            $(".dragger").css("opacity","1");
            $(".dragger").css("pointer-events","auto");
        }
    });
}

//Stop
function stop(e){
    e.preventDefault();
    e.stopPropagation();
}


function Upload(formdata, e){
    $.ajax({
         url: url,
         type: 'post',
         data: formdata,
         contentType: false,
         processData: false,
         dataType: 'json',
         beforeSend: function() {
            $(".dragger").css("opacity","0.6");
            $(".dragger").css("pointer-events","none");
         },
         success: function(response){
           $(".file_preview li:last-child progress").val(100);
            if (JSON.stringify(response['type']).replace(/"/g, "") == 0){
                popup(0, response['mssg']);
                $(".file_preview li:last-child").remove();
            }
            else {
                $(".file_preview li:last-child").html('<a href=https://bboysdreamspells.000webhostapp.com/dragdrop/assets/images/'+response["name"]+' class="link" target="_blank">'+ file_name +'</a><a href="javascript:void(0)" class="remove" onclick="remove(\''+JSON.stringify(response['name']).replace(/"/g, "")+'\', this)"><i class="fa fa-times">Supprimer</i></a>');
                $("#url_image").val("https://bboysdreamspells.000webhostapp.com/dragdrop/assets/images/"+response["name"]); // Change la valeur de l'input caché avec l'ID "url_image"
            }
         },
         complete: function() {
            $(".dragger").css("opacity","1");
            $(".dragger").css("pointer-events","auto");
         }
    });
}

//Variables globales
var popup_content, timer, count = 0;

//PopUp
function popup(type, mssg){
    switch (type){
        case 0:
            popup_content = '<section class="fixed_flex del"><a href="javascript:void(0)" class="close" onclick="this.parentNode.remove()"><i class="fa fa-times">Supprimer</i></a><iconify-icon icon="ph:warning" class="icon-error big"></iconify-icon><article><h2 class="title small">Warning</h2><p>'+mssg+'</p></article></section>';
        break;
    }
    if ($(".popup").length > 0){
        if ($(".popup section").length > 0) {
            $(".popup section").remove();
            $(".popup").append(popup_content);
        } else {
            $(".popup").append(popup_content);
        }
    } else {
        $("body").append('<div class="popup">'+popup_content+'</div>');
    }
    clearTimeout(timer);
    timer = setTimeout(function(){ if($(".popup section").length > 0){ $(".popup section").remove(); }},10000);
}

