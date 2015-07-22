function markRead(object) {
    object.parentElement.parentElement.className = "notes";
    var id = object.parentElement.parentElement.id;
    var url = "/markRead/".concat(id);
    $.get(url)
        .done(function( data ) {
            var bubble = document.getElementById("bubble");
            var noteIcon = document.getElementById("notification");
            if (data != 1) noteIcon.title = "Você tem " + data + " notificações não lidas";
            else noteIcon.title = "Você tem " + data + " notificação não lida";
            if (data > 0) bubble.innerHTML = data;
            else bubble.style.display = "none";     
        });
    }

function readAll() {
    var notes = document.getElementsByClassName("notes");
    for (i = 0; i < notes.length; i++) {
        notes[i].className = "notes";
    }
    $.get("/readAll")
        .done(function( data ) {
            var bubble = document.getElementById("bubble");
            var noteIcon = document.getElementById("notification");
            noteIcon.title = "Você tem " + data + " notificações não lidas";
            bubble.style.display = "none";
        });
    }

function refreshBubbleCounter() {
    $.get("/refreshBubble")
        .done(function( data ) {
            var bubble = document.getElementById("bubble");
            var noteIcon = document.getElementById("notification");
            if (data != 1) noteIcon.title = "Você tem " + data + " notificações não lidas";
            else noteIcon.title = "Você tem " + data + " notificação não lida";
            if (data > 0) bubble.innerHTML = data;
            else bubble.style.display = "none"; 
        })
}

$(document).ready(function() {
    
    $(".fancybox").fancybox({
    });
});