import $ from 'jquery';
import SuggestionController from '../suggestions/SuggestionController';

// On DOM ready
$(document).ready(() => {
  /**
   * Click event to the open modal button
   */
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
        // Showing error
        console.info('ERRO', error);
      });
  });

  /**
   * User pressed thumbs-link
   */
  $('.thumbs-link').click((e) => {
    // When the user clicks on Thumbs Down or
    // Thumbs Up on Suggestions List, submits the children form.
    // Important: The form must be the CHILDREN element.
    $(e.currentTarget).children('form').submit();
  });
});
