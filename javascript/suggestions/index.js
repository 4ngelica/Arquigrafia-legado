import $ from 'jquery';
import SuggestionModal from './SuggestionModal';

$(document).ready(() => {
  // Don't show modal when it's a institution
  if (photo.institution) $('#OpenModal').hide();
  else $('#OpenModal').show();

  // On DOM ready, add the click event to the open modal button
  $('#OpenModal').click(function () {
    // Don't show the modal when we don't have a user logged in
    if (!user || !user.id) {
      // Go to login page
      window.location = "/users/login";
      return;
    };

    // If the user is the owner, go to edit page
    if (user.id == photo.user_id) {
      window.location = "/photos/" + photo.id + "/edit"
      return;
    }

    // Only shows the modal if we have missing fields
    if (missingFields && missingFields.length > 0) {
      const suggestionModal = new SuggestionModal(missingFields);
      suggestionModal.showModal(
        missingFields[0].type,
        missingFields[0].field_name,
        missingFields[0].field_content,
        missingFields[0].question,
        missingFields[0].attribute_type,
        0,
      );
    }
  });
})
