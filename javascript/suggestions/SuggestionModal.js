import MathController from '../general/MathController';
import SuggestionController from './SuggestionController';

class SuggestionModal {

  constructor(missingFields) {
    /**
     * This variable stores the array of modals.
     * This variable is needed because when we animate one modal to another
     * we need to hide this modal and close it later (see onClose on showModal function)
     * @type {Array}
     */
    this.suggestionModals = [];

    // Setting Missing fields
    this.missingFields = missingFields;
  }

  /**
   * Generates the jBox TITLE html using Handlebars
   * @param  {String} attributeType  The icon that will be rendered on the title area of jBox
   * @return {String} The HTML string that represents the jBox title
   */
  getTitleHTML(attributeType) {
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
  getContentHTML(type, name, question) {
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
  getFooterHTML(type, currentIndex) {
    // Defining the percentage that will show on footer
    var percentage = 0;
    var numItems = this.missingFields.length;
    percentage = MathController.ceil10((currentIndex/numItems)*100, 1);

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
   * Shows to user the modal asking for a suggestion.
   * @param  {Number} currentIndex The current page (index) that we're at the moment
   */
  askSuggestion(currentIndex) {
    var currentModal = this.suggestionModals[currentIndex];
    var fieldName = this.missingFields[currentIndex].field_name;
    var fieldContent = this.missingFields[currentIndex].field_content;
    var attributeType = this.missingFields[currentIndex].attribute_type;
    // Checking if there's a next page to change
    this.showModal(
      'suggestion',
      'O ' + fieldName + ' atual é: \"' + fieldContent + '\"',
      null,
      'Você sabe a informação correta? Nos ajude sugerindo uma modificação.',
      attributeType,
      currentIndex,
    );

    // Slide the jBox to the left
    this.suggestionModals[currentIndex].animate('pulse', {
      // Once the jBox animated, hide it
      complete: () => {
        // Hiding the current modal
        if (currentModal) currentModal.wrapper.css('display', 'none');
      }
    });
  }

  /**
   * Changes the 'page' of the modal.
   * Basically here we're rendering the next modal and showing the next question.
   * @param  {Number} currentIndex The current page (index) that we're at the moment
   */
  changePage(currentIndex) {
    var currentModal = this.suggestionModals[currentIndex];
    // Checking if there's a next page to change
    if (this.missingFields.length > currentIndex + 1) {
      // Showing the next modal (with index + 1)
      this.showModal(
        this.missingFields[currentIndex + 1].type,
        this.missingFields[currentIndex + 1].field_name,
        this.missingFields[currentIndex + 1].field_content,
        this.missingFields[currentIndex + 1].question,
        this.missingFields[currentIndex + 1].attribute_type,
        currentIndex + 1
      );
    } else if (currentIndex + 1 === this.missingFields.length) {
      // Here we're at the last modal page
      this.showModal(
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
    this.suggestionModals[currentIndex].animate('slideLeft', {
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
  showModal(type, name, content, question, attributeType, currentIndex) {
    // Getting the HTML content that we're gonna show on the modal
    var titleHTML = this.getTitleHTML(attributeType);
    var contentHTML;
    var footerHTML;
    // Getting content and footer based on type
    if (type === 'confirm') {
      contentHTML = this.getContentHTML(type, content, question);
      footerHTML = this.getFooterHTML('confirm', currentIndex);
    } else if (type === 'lastPage') {
      contentHTML = this.getContentHTML(type, content, question);
      footerHTML = this.getFooterHTML(type, currentIndex);
    } else {
      contentHTML = this.getContentHTML(type, name, question);
      footerHTML = this.getFooterHTML('jump', currentIndex);
    }
    // Is this the initial modal?
    var initial = currentIndex == 0;

    // Showing jBox modal
    this.suggestionModals[currentIndex.toString()] = new jBox('Modal', {
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
      onCreated: (() => {
        // We gonna tell the close button to close the currentIndex modal
        $('.close-button').on('click', (() => {
          // Closing current modal
          this.suggestionModals[currentIndex].close();
        }).bind(this));

        $('.enviar-button').on('click', (() => {
          // Getting the text from suggestion-text textarea
          var suggestionText = this.suggestionModals[currentIndex].content.find('#sugestion-text').val();
          if (!suggestionText || suggestionText == '') {
            alert('Você precisa preencher algo para enviar.');
            return
          }
          // Sending suggestion
          SuggestionController.sendSuggestion(user.id, photo.id, this.missingFields[currentIndex].attribute_type, suggestionText);
          // Change page (go to the next modal)
          this.changePage(currentIndex);
        }).bind(this));

        // When click on jump button, changes page doing nothing else
        $('.pular-etapa-button').on('click', (() => {
          // Change page (go to the next modal)
          this.changePage(currentIndex);
        }).bind(this));

        // Event when clicks on sim-button
        $('.sim-button').on('click', (() => {
          // Sending suggestion
          SuggestionController.sendSuggestion(user.id, photo.id, this.missingFields[currentIndex].attribute_type, this.missingFields[currentIndex].field_content);
          // Change page (go to the next modal)
          this.changePage(currentIndex);
        }).bind(this));

        // Event when clicks on nao-button
        $('.nao-button').on('click', (() => {
          // Asking user for suggestion
          this.askSuggestion(currentIndex);
        }).bind(this));

        // Event when clicks on nao-sei-button
        $('.nao-sei-button').on('click', (() => {
          this.changePage(currentIndex);
        }).bind(this));
      }).bind(this),
      // When any of the modals is closed, this function is called
      onClose: (() => {
        // When closing one of the modals, close all modals
        this.suggestionModals.forEach((modal) => {
          modal.close();
        });
        // Cleaning suggestionModals array
        this.suggestionModals = [];
      }).bind(this),
    }).open();
  }

}

export default SuggestionModal;
