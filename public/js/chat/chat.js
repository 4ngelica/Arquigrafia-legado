function connectPusher() {
  // Enable log in console, only for debugging
  Pusher.logToConsole = true;

  var pusher = new Pusher('2b939ef19651ba2daa48', {
    encrypted: true
  });

  var channel = pusher.subscribe("1");
  channel.bind(`${userID}`, function(data) {
    showMessage(data.name, data.message);
  });
}

function showMessage(userName, message) {
  $('#chat-list').append(`<li class='chat-message'><b>${userName}:</b> ${message}</li>`);
}

function sendMessage() {
  thread_id = $('#thread-id-input').val();
  message = $('#message-input').val();
  sendData = {
    thread_id,
    message,
  };

  // Showing own message on chat
  showMessage(userName, message);

  // Cleaning message-input field
  $('#message-input').val("");

  $.ajax({
      type: "POST",
      url : `/users/${userID}/chats/test`,
      data: sendData,
      success : function(data){
        console.log(data);
      }
  }, "json");
}

/**
 * ON DOCUMENT READY
**/
$(document).ready(function() {
  // Connecting client with Pusher
  connectPusher();

  // Event when click on send-message button
  $("#send-message").click(function () {
    sendMessage();
  });

  // When press enter on message-input
  $('#message-input').keypress(function (e) {
    // If pressed ENTER
    if (e.which == 13) sendMessage();
  });
});
