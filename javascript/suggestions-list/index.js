import $ from 'jquery';
import SuggestionController from '../suggestions/SuggestionController';

$(document).ready(() => {
  // On DOM ready, add the click event to the open modal button
  $('.create-chat-link').click((e) => {
    // Getting userID
    const userID = $(e.currentTarget).data('val');
    // Setting redirectWindow variable
    const redirectWindow = window.open('', '_blank');

    // Creating chat
    SuggestionController.createChat(userID)
      .then((data) => {
        // Open chat tab
        redirectWindow.location = `/chats/${data}`;
      }).catch((error) => {
        console.log('ERRO', error);
        return;
      })
  });

})
