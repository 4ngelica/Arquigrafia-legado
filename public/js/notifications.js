function markRead(object) {
  var id = object.parentElement.parentElement.id;
  var notes = document.getElementsByClassName(id);
  for (i = 0; i < notes.length; i++) {
    notes[i].className = "notes " + id;
  }
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
    if (bubble) bubble.style.display = "none";
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

function toggleNotes(){
  var notes_box = document.getElementById("notes-box");
  if(notes_box.style.opacity == 1){
    notes_box.style.opacity = 0;
    // Remove it from active screen space
    notes_box.style.display = "none";
  } else {
    // Return it to active screen space
    notes_box.style.display = "block";
    notes_box.style.opacity = 1;
    // Marking notifications as read
    readAll();
  }
}

$(document).ready(function() {

  $(".fancybox").fancybox({
    'opacity' : true,
    'margin'  : 2
  });

  $('body').click(function(e){
    var notes_box = document.getElementById("notes-box");
    var icon = document.getElementById("notification");
    var target = e.target;
    var in_or_out = 0;
    if (typeof icon != null) {
      if ( target == icon ) {
        return;
      }
      while ( target !=  document.body) {
        if ( target == notes_box ) {
          in_or_out = 1;
          break;
        }
        if (target) target = target.parentElement;
        else break;
      }
      if (notes_box != null) {
        if (in_or_out == 0) {
          notes_box.style.display = "none";
          notes_box.style.opacity = 0;
        }
      }
    }
  });
});
