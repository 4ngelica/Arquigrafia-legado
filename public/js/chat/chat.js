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

function addZero(i) {
  if (i < 10) i = "0" + i;
  return i;
}

function renderChatHeader(userName) {
  $('#chat-header').html(`<h2><a href=''>${userName}</a></h2>`);
}

function renderMessageBlock(position, messageBlock) {
  // Setting hours
  var createdAt = new Date(messageBlock[messageBlock.length - 1]['created_at']);
  var hours = `${addZero(createdAt.getHours())}:${addZero(createdAt.getMinutes())}`;

  // Defining source
  var source;
  if (position === 'right') source = $("#message-right-block-template").html();
  else source = $("#message-left-block-template").html();
  // Compiling template
  var template = Handlebars.compile(source);
  // Setting content to be rendered
  var context = {
    messages: messageBlock,
    avatarURL: 'http://www.gruener-baum-wuerzburg.de/images/avatar/avt-2.jpg',
    hours: hours,
  };
  var html = template(context);

  // Adding message block to chat-messages
  $('#chat-messages').append(html)
}

function renderMessages(messages) {
  // Cleaning chat messages
  $('#chat-messages').html("");

  // Defining last rendered message;
  var lastRendered; // Can be 'me' or 'you'
  var messageBlock; // Array of messages to be rendered

  for (var i = 0; i < messages.length; i += 1) {
    var message = messages[i];

    // If it is the first iteration
    if (typeof lastRendered === 'undefined') {
      // Saving the first messageBlock
      messageBlock = [message];
      if (message['user_id'] === userID) lastRendered = 'me';
      else lastRendered = 'you';
      // Render if it's the only iteration
      if (messages.length === 1) {
        if (lastRendered === 'me') renderMessageBlock('left', messageBlock);
        else renderMessageBlock('right', messageBlock);
      }
      // Continue to the next iteration
      continue;
    }

    // If the message is from me, and the last message was from me
    if (message['user_id'] === userID && lastRendered === 'me') {
      messageBlock.push(message)
    }
    // Else if the message is from me, and the last message was from you
    else if (message['user_id'] === userID && lastRendered === 'you') {
      renderMessageBlock('right', messageBlock);
      messageBlock = [message];
      lastRendered = 'me';
      continue;
    }
    // Else if the message is from you, and the last message is from you
    else if (message['user_id'] !== userID && lastRendered === 'you') {
      messageBlock.push(message);
    }
    // Else if the message is from you, mas the last message is from me
    else if (message['user_id'] !== userID && lastRendered !== 'you') {
      renderMessageBlock('left', messageBlock);
      messageBlock = [message];
      lastRendered = 'you';
      continue;
    }

    // Rendering the last block
    if (i === messages.length - 1) {
      if (lastRendered === 'me') renderMessageBlock('left', messageBlock);
      else renderMessageBlock('right', messageBlock);
    }
  }

  // Scroll to bottom
  $('#chat').scrollTop($('#chat-messages').height())
}

function renderChatItems() {
  var source = $("#chat-item-template").html();
  var template = Handlebars.compile(source);
  var context = {
    chatName: 'John',
    lastMessage: 'This is a test message...',
    avatarURL: 'http://www.gruener-baum-wuerzburg.de/images/avatar/avt-2.jpg',
  };
  var html = template(context);
  $('#chat-items').html(html);
}


function getMessages() {
  params = {
    thread_id: currentThreadID,
  };

  $.ajax({
      type: "POST",
      url : '/threads/messages',
      data: params,
      success : function(messages) {
        console.log(messages);
        currentMessages = messages;
        renderMessages(currentMessages);
      }
  }, "json");
}

function sendMessage() {
  message = $('#message-input').val();
  sendData = {
    thread_id: currentThreadID,
    message,
  };

  // Showing own message on chat
  currentMessages.push({
    body: message,
    created_at: new Date().toLocaleString(),
    user_id: userID,
  });

  renderMessages(currentMessages);

  // Cleaning message-input field
  $('#message-input').val('');

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

  renderChatHeader('John');
  getMessages();
});
