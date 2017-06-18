(function(){

	/**
	 * Decimal adjustment of a number.
	 *
	 * @param	{String}	type	The type of adjustment.
	 * @param	{Number}	value	The number.
	 * @param	{Integer}	exp		The exponent (the 10 logarithm of the adjustment base).
	 * @returns	{Number}			The adjusted value.
	 */
	function decimalAdjust(type, value, exp) {
		// If the exp is undefined or zero...
		if (typeof exp === 'undefined' || +exp === 0) {
			return Math[type](value);
		}
		value = +value;
		exp = +exp;
		// If the value is not a number or the exp is not an integer...
		if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
			return NaN;
		}
		// Shift
		value = value.toString().split('e');
		value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
		// Shift back
		value = value.toString().split('e');
		return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
	}

	// Decimal round
	if (!Math.round10) {
		Math.round10 = function(value, exp) {
			return decimalAdjust('round', value, exp);
		};
	}
	// Decimal floor
	if (!Math.floor10) {
		Math.floor10 = function(value, exp) {
			return decimalAdjust('floor', value, exp);
		};
	}
	// Decimal ceil
	if (!Math.ceil10) {
		Math.ceil10 = function(value, exp) {
			return decimalAdjust('ceil', value, exp);
		};
	}

})();

/**
 * This variable stores the array of modals.
 * This variable is needed because when we animate one modal to another
 * we need to hide this modal and close it later (see onClose on showModal function)
 * @type {Array}
 */
var suggestionModals = [];

/**
 * Generates the jBox TITLE html using Handlebars
 * @param  {String} attributeType  The icon that will be rendered on the title area of jBox
 * @return {String} The HTML string that represents the jBox title
 */
function getTitleHTML(attributeType) {
  // Getting icon base on field type
  var icon;
  switch (attributeType) {
    case 'workDate':
      icon = 'date-icon';
      break;
    case 'street':
    case 'district':
    case 'city':
    case 'state':
    case 'country':
      icon = 'location-icon';
      break;
    case 'name':
    case 'description':
    case 'imageAuthor':
    case 'workAuthor':
      icon = 'image-author-icon';
      break;
    case 'lastPage':
      icon = 'feedback-icon';
      break;
		case 'projectAuthor':
			icon = 'author-icon';
			break;
    default:
      break;
  }

  var sourceTitle = $("#suggestion-modal-title").html();
  var templateTitle = Handlebars.compile(sourceTitle);
  var titleHTML = templateTitle({ icon });

  return titleHTML;
}

/**
 * Generates the jBox CONTENT html using Handlebars
 * @param  {String} type     Can be 'confirm' or 'text'. Represents the type of content
 * @param  {String} name     The top name that will show on modal
 * @param  {String} question The question that you wanna ask to the user
 * @return {String}          The HTML that represents the jBox content
 */
function getContentHTML(type, name, question) {
  var sourceContent;
  // Rendering the right content by type
  if (type === 'confirm') {
     sourceContent = $("#suggestion-modal-confirm-content").html();
  } else if (type === 'suggestion') {
    sourceContent = $("#suggestion-modal-text-content").html();
  } else if (type === 'lastPage') {
    sourceContent = $("#suggestion-modal-last-page-content").html();
  } else {
    return '';
  }
  var templateContent = Handlebars.compile(sourceContent);
  var contentHTML = templateContent({ name, question });

  return contentHTML;
}

/**
 * Generates the jBox FOOTER html using Handlebars
 * @param  {String} type Can be 'confirm' or 'jump'. Represents the type of footer.
 * @return {String}      The HTML that represents the jBox footer
 */
function getFooterHTML(type, currentIndex) {
  // Defining the percentage that will show on footer
  var percentage = 0;
  var numItems = missingFields.length;
  percentage = Math.ceil10((currentIndex/numItems)*100, 1);

  if (type === 'confirm') {
    var sourceFooter = $("#suggestion-modal-confirm-footer").html();
  } else if (type === 'jump') {
    var sourceFooter = $("#suggestion-modal-jump-footer").html();
  } else if (type === 'lastPage') {
    var sourceFooter = $("#suggestion-modal-close-footer").html();
  }
  var templateTitle = Handlebars.compile(sourceFooter);
  var footerHTML = templateTitle({ percentage });

  return footerHTML;
}

/**
 * Sending the suggestion through AJAX request to the server
 * @param  {Number} userID        The current logged user ID
 * @param  {Number} photoID       The current photo ID
 * @param  {String} attributeType The attribute that you wanna send the suggestion
 * @param  {String} text          The text of the suggestion that you're sending
 */
function sendSuggestion(userID, photoID, attributeType, text) {
  // Mounting params
  data = {
    user_id: userID,
    photo_id: photoID,
    attribute_type: attributeType,
    text: text,
  };

  console.log('DADOS DA SUGESTAO', data);

  // Sending ajax request
  $.ajax({
      type: "POST",
      url : `/suggestions`,
      data: data,
      success : function(data){
        console.log('SUGESTAO ENVIADA', data);
      }
  }, "json");
}

/**
 * Shows to user the modal asking for a suggestion.
 * @param  {Number} currentIndex The current page (index) that we're at the moment
 */
function askSuggestion(currentIndex) {
  var currentModal = suggestionModals[currentIndex];
  var fieldName = missingFields[currentIndex].field_name;
  var fieldContent = missingFields[currentIndex].field_content;
  var attributeType = missingFields[currentIndex].attribute_type;
  // Checking if there's a next page to change
  showModal(
    'suggestion',
    'O ' + fieldName + ' atual é: \"' + fieldContent + '\"',
    null,
    'Você sabe a informação correta? Nos ajude sugerindo uma modificação.',
    attributeType,
    currentIndex,
  );

  // Slide the jBox to the left
  suggestionModals[currentIndex].animate('pulse', {
    // Once the jBox animated, hide it
    complete: function () {
      // Hiding the current modal
      if (currentModal) currentModal.wrapper.css('display', 'none');
    }.bind(this)
  });
}

/**
 * Changes the 'page' of the modal.
 * Basically here we're rendering the next modal and showing the next question.
 * @param  {Number} currentIndex The current page (index) that we're at the moment
 */
function changePage(currentIndex) {
  var currentModal = suggestionModals[currentIndex];
  // Checking if there's a next page to change
  if (missingFields.length > currentIndex + 1) {
    // Showing the next modal (with index + 1)
    showModal(
      missingFields[currentIndex + 1].type,
      missingFields[currentIndex + 1].field_name,
      missingFields[currentIndex + 1].field_content,
      missingFields[currentIndex + 1].question,
      missingFields[currentIndex + 1].attribute_type,
      currentIndex + 1
    );
  } else if (currentIndex + 1 === missingFields.length) {
    // Here we're at the last modal page
    showModal(
      'lastPage',
      null,
      null,
      null,
      'lastPage',
      currentIndex + 1
    );
  } else {
    // Close the current modal and return
    if (currentModal) currentModal.close();
    return;
  }

  // Slide the jBox to the left
  suggestionModals[currentIndex].animate('slideLeft', {
    // Once the jBox animated, hide it
    complete: function () {
      // Hiding the current modal
      if (currentModal) currentModal.wrapper.css('display', 'none');
    }.bind(this)
  });
}

/**
 * This function shows a Suggestion Modal
 * @param  {String} type          The type of the modal. Can be 'confirm' or 'text'
 * @param  {String} name          The item name
 * @param  {String} content       The item content (sometimes is null, if the field is not filled)
 * @param  {String} question      The question that the modal will show to user
 * @param  {String} currentIndex  The currentIndex that we're showing the modal (represents the current question)
 */
function showModal(type, name, content, question, attributeType, currentIndex) {
  // Getting the HTML content that we're gonna show on the modal
  var titleHTML = getTitleHTML(attributeType);
  var contentHTML;
  var footerHTML;
  // Getting content and footer based on type
  if (type === 'confirm') {
    contentHTML = getContentHTML(type, content, question);
    footerHTML = getFooterHTML('confirm', currentIndex);
  } else if (type === 'lastPage') {
    contentHTML = getContentHTML(type, content, question);
    footerHTML = getFooterHTML(type, currentIndex);
  } else {
    contentHTML = getContentHTML(type, name, question);
    footerHTML = getFooterHTML('jump', currentIndex);
  }
  // Is this the initial modal?
  var initial = currentIndex == 0;

  // Showing jBox modal
  suggestionModals[currentIndex.toString()] = new jBox('Modal', {
    animation: 'zoomIn',
    overlay: initial ? true : false, // Only shows the overlay for the first modal
    blockScroll: false,
    closeButton: false,
    closeOnEsc: initial ? true : false, // Only sets the closeOnEsc for the first modal
    footer: footerHTML,
    title: titleHTML,
    content: contentHTML,
    zIndex: 10000,

    // Once this jBox is created, the onCreated function is called
    onCreated: function () {
      // We gonna tell the close button to close the currentIndex modal
      $('.close-button').on('click', function () {
        // Closing current modal
        suggestionModals[currentIndex].close();
      });

      $('.enviar-button').on('click', function() {
        // Getting the text from suggestion-text textarea
        var suggestionText = suggestionModals[currentIndex].content.find('#sugestion-text').val();
        if (!suggestionText || suggestionText == '') {
          alert('Você precisa preencher algo para enviar.');
          return
        }
        // Sending suggestion
        sendSuggestion(user.id, photo.id, missingFields[currentIndex].attribute_type, suggestionText);
        // Change page (go to the next modal)
        changePage(currentIndex);
      })

      // When click on jump button, changes page doing nothing else
      $('.pular-etapa-button').on('click', function() {
        // Change page (go to the next modal)
        changePage(currentIndex);
      });

      // Event when clicks on sim-button
      $('.sim-button').on('click', function() {
        // Sending suggestion
        sendSuggestion(user.id, photo.id, missingFields[currentIndex].attribute_type, missingFields[currentIndex].field_content);
        // Change page (go to the next modal)
        changePage(currentIndex);
      });

      // Event when clicks on nao-button
      $('.nao-button').on('click', function() {
        // Asking user for suggestion
        askSuggestion(currentIndex);
      });

      // Event when clicks on nao-sei-button
      $('.nao-sei-button').on('click', function() {
        changePage(currentIndex);
      });
    },
    // When any of the modals is closed, this function is called
    onClose: function () {
      // When closing one of the modals, close all modals
      suggestionModals.forEach(function(modal) {
        modal.close();
      });
      // Cleaning suggestionModals array
      suggestionModals = [];
    },
  }).open();
}

$(document).ready(function() {
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
      showModal(
        missingFields[0].type,
        missingFields[0].field_name,
        missingFields[0].field_content,
        missingFields[0].question,
        missingFields[0].attribute_type,
        0,
      );
    }
  });
});
