/**
 * This is responsable for controlling suggestions
 * This is where we will put the network requests
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


  /**
   * Sended at the end, to get the final pictures
   * @param  {String} photoID    The ID of the picture that we're in
   * @param  {Number} points     The points that the user may get
   * @return {Promise}           Promise with the result of the request
   */
  static sendFinalSuggestions(photoID, points) {
    // Mounting params
    const data = {
      photo: photoID,
      points,
    };

    console.log('DADOS ENVIADOS', data);

    return new Promise((resolve, reject) => {
      // Sending ajax request
      $.ajax({
          type: "POST",
          url : `/suggestions/sent`,
          data: data,
          success : (data) => {
            resolve(data);
          },
          error: (error) => {
            reject(error);
          },
      }, "json");
    });
  }
}

export default SuggestionController;
