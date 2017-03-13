// Connects with pusher
function connectPusher() {
  // Enable log in console, only for debugging
  Pusher.logToConsole = true;

  var pusher = new Pusher('2b939ef19651ba2daa48', {
    encrypted: true
  });

  // Subscribing to this user channel
  var channel = pusher.subscribe(`${userID}`);

  // Binding to new message received
  channel.bind('new_message', function(data) {
    // Setting the current chat last message
    setLastMessage(data.thread_id, data.message);

    // Rendering chat items
    renderChatItems();

    // If the currentChat displayed is the chat that the message arrived
    if (currentChat.thread.id === data.thread_id) {
      console.log("ENTROUU", currentChat.thread.id);
      // Adding the new message to message block
      currentMessages.push(data.message);
      // Rendering messages again
      renderMessages();
    }
  });

  // Binding to new thread created
  channel.bind('new_thread', function(newChat) {
    currentChats.push(newChat);
    // Re-rendering the chats
    renderChatItems();
  });
}

function configureSOL() {
  sol = $('#select-users').searchableOptionList({
    showSelectAll: false,
  });
}

// Add zero to hour
function addZero(i) {
  if (i < 10) i = "0" + i;
  return i;
}

// Renders the chat header (name of the chat)
function renderChatHeader(userName) {
  $('#chat-header').html(`<h2><a href=''>${userName}</a></h2>`);
}

// Render a message block (one block of messages)
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

// Rendering messages
function renderMessages() {
  // Cleaning chat messages
  $('#chat-messages').html("");

  // Defining last rendered message;
  var lastRendered; // Can be 'me' or 'you'
  var messageBlock; // Array of messages to be rendered

  for (var i = 0; i < currentMessages.length; i += 1) {
    var message = currentMessages[i];

    // If it is the first iteration
    if (typeof lastRendered === 'undefined') {
      // Saving the first messageBlock
      messageBlock = [message];
      if (message['user_id'] === userID) lastRendered = 'me';
      else lastRendered = 'you';
      // Render if it's the only iteration
      if (currentMessages.length === 1) {
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
    }
    // Else if the message is from you, and the last message is from you
    else if (message['user_id'] !== userID && lastRendered === 'you') {
      messageBlock.push(message);
    }
    // Else if the message is from you, but the last message is from me
    else if (message['user_id'] !== userID && lastRendered !== 'you') {
      renderMessageBlock('left', messageBlock);
      messageBlock = [message];
      lastRendered = 'you';
    }

    // Rendering the last block
    if (i === currentMessages.length - 1) {
      if (lastRendered === 'me') renderMessageBlock('left', messageBlock);
      else renderMessageBlock('right', messageBlock);
    }
  }

  // Scroll to bottom
  $('#chat').scrollTop($('#chat-messages').height())
}

function sortChatItems() {
  currentChats.sort(function(obj1, obj2) {
    a = 0;
    b = 0;
    if (obj1.last_message) a = new Date(obj1.last_message.created_at);
    if (obj2.last_message) b = new Date(obj2.last_message.created_at);
    return a>b ? -1 : a<b ? 1 : 0;
  });
}

// Rendering chats
function renderChatItems() {
  // Cleaning HTML
  $('#chat-items').html('');

  // Sorting Chat Items on the right order
  sortChatItems();

  // Rendering chats
  currentChats.forEach(function(chat, index) {
    // Setting currentChat if it's the first chat
    if (typeof currentChat === 'undefined' && index === 0) currentChat = chat;

    var source = $("#chat-item-template").html();
    var template = Handlebars.compile(source);

    var lastMessage = '';
    if (chat['last_message']) {
      lastMessage = chat['last_message'].body;
    }

    var context = {
      chatIndex: index,
      chatName: chat.names,
      lastMessage: lastMessage,
      avatarURL: 'http://www.gruener-baum-wuerzburg.de/images/avatar/avt-2.jpg',
    };
    var html = template(context);
    $('#chat-items').append(html)
  });
}

// Getting messages from server
function getMessages() {
  params = {
    thread_id: currentChat.thread.id,
  };

  $.ajax({
      type: "POST",
      url : '/threads/messages',
      data: params,
      success : function(messages) {
        console.log(messages);
        currentMessages = messages;
        renderMessages();
      }
  }, "json");
}

// Sending message behavior
function sendMessage() {
  message = $('#message-input').val();
  sendData = {
    thread_id: currentChat.thread.id,
    message,
  };

  // Setting the last message
  const lastMessage = {
    body: message,
    created_at: new Date().toLocaleString(),
    user_id: userID,
  }
  // Push the last message to the array
  currentMessages.push(lastMessage);

  // Setting the current chat last message
  setLastMessage(currentChat.thread.id, lastMessage);

  // Rendering chat items
  renderChatItems();

  // Rendering all messags
  renderMessages();

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

function createChat() {
  selectedInputs = sol.getSelection();
  selectedUserIDs = [];
  for (var i = 0; i < selectedInputs.length; i += 1) {
     selectedUserIDs.push($(selectedInputs[i]).data('sol-item').value);
  }

  if (selectedUserIDs.length === 0) {
    alert('Você precisa selecionar pelo menos um usuário!')
    return;
  }

  data = {
    participants: selectedUserIDs,
  }

  $.ajax({
      type: "POST",
      url : `/users/${userID}/chats`,
      data: data,
      success : function(data){
        console.log('CHAT CRIADO', data);
        configureSOL();
        $('#select-users-container').hide();
      }
  }, "json");
}

function pressedNewChat() {
  $('#select-users-container').toggle();
}

// Render current chat header and messages
function renderCurrentChat() {
  renderChatHeader(currentChat.names);
  getMessages();
}

// This function is called when the user clicks on a chat
function pressedChat(chatIndex) {
  currentChat = currentChats[chatIndex];
  console.log(currentChat);
  renderCurrentChat();
}

function setLastMessage(threadID, lastMessage) {
  currentChats.forEach(function(chat, index) {
    if (chat.thread.id === threadID) {
      currentChats[index].last_message = lastMessage;
    }
  });
}

function searchUsers(name) {
  $.ajax({
      type: "POST",
      url : '/users/searchName',
      success : function(users) {
        console.log('USERS', users);
        $('#select-users').html('');
        users.map(function (user, index) {
          $('#select-users')
            .append(`<option value="${users[index].id}">${users[index].name}</option>`);
        })

        configureSOL();
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

  // Event when click on btn-create-chat button
  $("#btn-create-chat").click(function () {
    createChat();
  });

  // When press enter on message-input
  $('#message-input').keypress(function (e) {
    // If pressed ENTER
    if (e.which == 13) sendMessage();
  });

  // Render Chat Items
  renderChatItems();
  renderCurrentChat();

  // Hiding select users container at the begining
  $('#select-users-container').hide();
  // Getting all users -- JUST FOR TESTING
  searchUsers();
});
