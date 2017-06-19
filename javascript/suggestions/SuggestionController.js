/**
 * This is responsable for controlling suggestions
 */

class SuggestionController {
  /**
   * Sending the suggestion through AJAX request to the server
   * @param  {Number} userID        The current logged user ID
   * @param  {Number} photoID       The current photo ID
   * @param  {String} attributeType The attribute that you wanna send the suggestion
   * @param  {String} text          The text of the suggestion that you're sending
   */
  static sendSuggestion(userID, photoID, attributeType, text) {
    // Mounting params
    const data = {
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

  static sendFinalSuggestions(photoID) {
    // Mounting params
    const data = {
      photo_id: photoID,
    };

    console.log('DADOS DA SUGESTAO', data);

    // Sending ajax request
    $.ajax({
        type: "POST",
        url : `/suggestions/sent`,
        data: data,
        success : function(data){
          console.log('RESULTADO', data);
        }
    }, "json");
  }
}

export default SuggestionController;
