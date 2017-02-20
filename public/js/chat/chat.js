function connectPusher() {
  // Enable log in console, only for debugging
  Pusher.logToConsole = true;

  var pusher = new Pusher('2b939ef19651ba2daa48', {
    encrypted: true
  });

  var channel = pusher.subscribe('my-channel');
  channel.bind('my-event', function(data) {
    showMessage(data.name, data.message);
  });
}

function showMessage(userName, message) {
  $('#chat-list').append(`<li class='chat-message'><b>${userName}:</b> ${message}</li>`);
}

/**
 * ON READY DOCUMENT
**/
$(document).ready(function() {
  // Connecting site with Pusher
  connectPusher();

  // Event when click on send-message button
  $("#send-message").click(function () {
    thread_id = $('#thread-id-input').val();
    message = $('#message-input').val();
    sendData = {
      thread_id,
      message,
    };

    $.ajax({
        type: "POST",
        url : "/users/1/chats/test",
        data: sendData,
        success : function(data){
          console.log(data);
        }
    }, "json");
  });
});
