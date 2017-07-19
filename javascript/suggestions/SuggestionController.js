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
        success : (data) => {
          console.log('SUGESTAO ENVIADA', data);
        },
        error: (error) => {
          console.log('ERROR', error);
        },
    }, "json");
  }


  /**
   * Sended at the end, to get the final pictures
   * @param  {String} photoID    The ID of the picture that we're in
   * @param  {Number} points     The points that the user may get
   * @param  {String} status     Can be 'none', 'complete' or 'incomplete'
   * @return {Promise}           Promise with the result of the request
   */
  static sendFinalSuggestions(photoID, points, numberSuggestions, status) {
    // Mounting params
    const data = {
      photo: photoID,
      points,
      status,
      suggestions: numberSuggestions,
    };

    console.log('DADOS ENVIADOS', data);

    return new Promise((resolve, reject) => {
      // Sending ajax request
      $.ajax({
          type: "POST",
          url : `/suggestions/sent`,
          data: data,
          success : (data) => {
            console.log('DADOS RECEBIDOS', data);
            resolve(data);
          },
          error: (error) => {
            console.log('ERRO AO ENVIAR SUGESTAO FINAL', error);
            reject(error);
          },
      }, "json");
    });
  }

  static createChat(userID) {
    return new Promise((resolve, reject) => {
      // Defining data to create chat
      const data = {
  		    participants: [userID],
  		}
      // Making ajax request
  		$.ajax({
          type: "POST",
          url : `/chats`,
          data: data,
          success : (response) => {
            console.log('CHAT CRIADO', response);
            if (response !== false) resolve(response);
            else reject(response);
          },
          error: (error) => {
            console.log('ERRO AO CRIAR CHAT', error);
            reject(error);
          },
      }, "json");
    });
  }

}

export default SuggestionController;
