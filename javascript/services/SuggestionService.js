/**
 * This is responsable for controlling suggestions
 * This is where we will put the network requests
 */

import $ from 'jquery';
import { request, GET, POST } from './Network';

/**
 * Sending the suggestion through AJAX request to the server
 * @param  {Number} userID        The current logged user ID
 * @param  {Number} photoID       The current photo ID
 * @param  {String} attributeType The attribute that you wanna send the suggestion
 * @param  {String} text          The text of the suggestion that you're sending
 */
export const sendSuggestion = (userID, photoID, attributeType, text) => {
  // Mounting params
  const data = {
    user_id: userID,
    photo_id: photoID,
    attribute_type: attributeType,
    text,
  };

  console.info('DADOS DA SUGESTAO', data);

  // Sending ajax request
  $.ajax({
    type: 'POST',
    url: '/suggestions',
    data,
    success: (res) => {
      console.info('SUGESTAO ENVIADA', res);
    },
    error: (error) => {
      console.info('ERROR', error);
    },
  }, 'json');
};

/**
 * Sended at the end, to get the final pictures
 * @param  {String} photoID    The ID of the picture that we're in
 * @param  {Number} points     The points that the user may get
 * @param  {String} status     Can be 'none', 'complete' or 'incomplete'
 * @return {Promise}           Promise with the result of the request
 */
export const sendFinalSuggestions = (photoID, points, numberSuggestions, status) => {
  // Mounting params
  const data = {
    photo: photoID,
    points,
    status,
    suggestions: numberSuggestions,
  };

  console.info('DADOS ENVIADOS', data);

  return new Promise((resolve, reject) => {
    // Sending ajax request
    $.ajax({
      type: 'POST',
      url: '/suggestions/sent',
      data,
      success: (res) => {
        console.info('DADOS RECEBIDOS', res);
        resolve(data);
      },
      error: (error) => {
        console.info('ERRO AO ENVIAR SUGESTAO FINAL', error);
        reject(error);
      },
    }, 'json');
  });
};

export const createChat = userID => new Promise((resolve, reject) => {
  // Defining data to create chat
  const data = {
    participants: [userID],
  };
  // Making ajax request
  $.ajax({
    type: 'POST',
    url: '/chats',
    data,
    success: (response) => {
      console.info('CHAT CRIADO', response);
      if (response !== false) resolve(response);
      else reject(response);
    },
    error: (error) => {
      console.info('ERRO AO CRIAR CHAT', error);
      reject(error);
    },
  }, 'json');
});

/**
 * Getting user suggestions
 * @param {Number} page The page that you wanna get
 * @param {Number} limit The number of items per page
 * @param {String} type The type of the suggestions. Can be 'reviews' or 'editions'.
 */
export const getUserSuggestions = (page, limit, type) => new Promise((resolve, reject) => {
  // Creating URL
  const url = `${window.location.origin}/suggestions/user_suggestions`;
  // Mounting params
  const params = {
    page,
    limit,
    type,
  };
  // Get user suggestions (async)
  request(GET, url, params)
    .then((res) => {
      resolve(res);
    })
    .catch((err) => {
      console.info(err);
      reject('Erro ao pegar as sugestões do usuário');
    });
});

/**
 * This function get the statistics about suggestions provided for a user
 * @param {String} type The type of the suggestions. Can be 'reviews' or 'editions'.
 * @return  A promise with the response of the request
 */
export const getUserSuggestionsStatistics = type => new Promise((resolve, reject) => {
  // Creating URL
  const url = `${window.location.origin}/suggestions/user_statistics`;
  // Defining params
  const params = {
    type,
  };
  // Getting user suggestion statistics (async)
  request(GET, url, params)
    .then((res) => {
      resolve(res);
    })
    .catch((err) => {
      console.info(err);
      reject('Erro ao pegar as estatisticas do usuário');
    });
});

export const logOpenModal = (photoID, origin) => new Promise((resolve, reject) => {
  // Creating URL
  const url = `${window.location.origin}/suggestions/log_open_modal`;
  // Creating params
  const params = {
    origin,
    photo_id: photoID,
  };
  // Making request
  request(GET, url, params)
    .then(res => resolve(res))
    .catch(err => reject(err));
});
