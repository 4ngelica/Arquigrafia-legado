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
      // Adding the new message to message block
      currentMessages.push(data.message);
      // Rendering messages again
      renderMessages();
      // If we are at the current thread, we automatically have to mark that the chat is read
      setChatAsRead(currentChat.thread.id);
    }
  });

  // Binding to new thread created
  channel.bind('new_thread', function(newChat) {
    currentChats.push(newChat);
    // Re-rendering the chats
    renderChatItems();
  });
}

// Gets the participant with userID from Current Chat
function getParticipantFromChat(userIDRequired, chat) {
  participants = chat.participants;
  for (var pCount = 0; pCount < participants.length; pCount++) {
    if (userIDRequired == participants[pCount].user.id) {
      return participants[pCount];
    }
  }
}

// Initializes searchableOptionList
function configureSOL() {
  sol = $('#select-users').searchableOptionList({
    showSelectAll: false,
    maxHeight: 100,
    data: '/data/users.json',
    converter: function (sol, rawData) {
      var solData = [];

      for (var i = 0; i < rawData.length; i++) {
        option = {
          "type": "option",
          "value": rawData[i].id,
          "label": rawData[i].name,
        };

        solData.push(option);
      }

      return solData;
    },
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
  var participant = getParticipantFromChat(messageBlock[0].user_id, currentChat);
  console.log('MESSAGE BLOCK', messageBlock);
  // Defining source
  var source;
  if (position === 'right') source = $("#message-right-block-template").html();
  else source = $("#message-left-block-template").html();
  // Compiling template
  var template = Handlebars.compile(source);
  // Setting content to be rendered
  var context = {
    messages: messageBlock,
    avatarURL: '/img/avatar-48.png',
    hours: hours,
    userName: participant.user.name,
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

function checkThreadRead(chat) {
  lastMessage = chat.last_message;
  // If there's no last message, I read the chat
  if (!lastMessage) return true;
  // If the last message is mine, I read the chat
  if (lastMessage.user_id === userID) return true;

  // Checking if we've already read the message
  participant = getParticipantFromChat(userID, chat);
  lastMessageTime = new Date(lastMessage.created_at).getTime();
  lastReadTime = new Date(participant.last_read).getTime();

  // Returning that thread is read if the date is bigger than the message time
  if (lastReadTime > lastMessageTime) {
    return true;
  } else {
    return false;
  }

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
      notRead: !checkThreadRead(chat),
      avatarURL: '/img/avatar-48.png',
    };
    var html = template(context);
    $('#chat-items').append(html)

    // Setting chat as active
    if (currentChat === chat) {
      setChatActive(index);
    }
  });
}

// Getting messages from server
function getMessages() {
  if (!currentChat) return;
  params = {
    thread_id: currentChat.thread.id,
  };

  $.ajax({
      type: "GET",
      url : '/messages',
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
    user_id: userID,
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

  // Sending message to server
  $.ajax({
      type: "POST",
      url : `/messages`,
      data: sendData,
      success : function(data){
        console.log(data);
      }
  }, "json");
}

// Get the selected users and creates a chat
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
      url : `/chats`,
      data: data,
      success : function(data){
        console.log('CHAT CRIADO', data);
        configureSOL();
        $('#select-users-container').hide();
      }
  }, "json");
}

// Called when pressed New Chat
function pressedNewChat() {
  $('#select-users-container').toggle(200);
}

// Render current chat header and messages
function renderCurrentChat() {
  if (currentChat) renderChatHeader(currentChat.names);
  getMessages();
}

// This functions send to API that this chat is read
function setChatAsRead(threadID) {
  // Payload data to send
  data = {
    thread_id: threadID,
  }

  $.ajax({
      type: "POST",
      url : `/chats/read`,
      data: data,
      success : function(data) {
        console.log('CHAT MARCADO COMO LIDO', data);
        // Marking chat as read locally
        // Mapping through all chats and checkin which one is the thread that we wanna set as read
        currentChats = currentChats.map(function (chat) {
          if (chat.thread.id === threadID) {
            // Map through all participants and check which is one is the current user
            chat.participants = chat.participants.map(function (participant) {
              // If the participant is the current user, we set the last read to now
              if(participant.user_id === userID) {
                participant.last_read = new Date();
              }
              return participant;
            })
          }
          return chat;
        });
        // Rendering chat items again
        renderChatItems();
      }
  }, "json");
}

// This function is called when the user clicks on a chat
function pressedChat(chatIndex) {
  currentChat = currentChats[chatIndex];
  console.log(currentChat);
  setChatActive(chatIndex);
  renderCurrentChat();
  setChatAsRead(currentChat.thread.id);
}

// Sets the chat as active
function setChatActive(chatIndex) {
  for (var i_chats = 0; i_chats < currentChats.length; i_chats += 1) {
    if (i_chats === chatIndex) {
      $(`#chat-item-${i_chats}`).addClass('active');
    } else {
      $(`#chat-item-${i_chats}`).removeClass('active');
    }
  }
}

// Sets the last message for a specific thread
function setLastMessage(threadID, lastMessage) {
  currentChats.forEach(function(chat, index) {
    if (chat.thread.id === threadID) {
      currentChats[index].last_message = lastMessage;
    }
  });
}

// Getting users
function searchUsers() {
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
    if (e.which == 13) {
      sendMessage();
      // Prevent enter on text area
      if(e.preventDefault) e.preventDefault();
    }
  });

  // Render Chat Items
  renderChatItems();
  renderCurrentChat();

  // Hiding select users container at the begining
  $('#select-users-container').hide();
  // Getting all users -- JUST FOR TESTING
  configureSOL();
});
