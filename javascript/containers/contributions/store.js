/**
 * VUEX STORE FOR CONTRIBUTIONS
 */

import Vue from 'vue';
import Vuex from 'vuex';
import { getUserSuggestions } from '../../services/SuggestionService';

Vue.use(Vuex);

// Root state object
const state = {
  selectedTab: 'reviews',
  userSuggestions: [],
  currentSuggestionsPage: 1,
  totalNumSuggestionPages: 1,
  isLoadingSuggestions: false,
};

// Mutations are operations that actually mutates the state.
const mutations = {
  changeTab(storeState, tab) {
    storeState.selectedTab = tab.id;
  },
  acceptRejectSuggestion(storeState, { suggestionID, type }) {
    console.info('Accepting Rejecting Suggestion', suggestionID, type);
  },
  createChat(storeState, { userID }) {
    console.info('Creating chat', userID);
  },
  editExpo(storeState, { expoID }) {
    console.info('Editing expo', expoID);
  },
  removeExpo(storeState, { expoID }) {
    console.info('Remove expo', expoID);
  },
  createExpo() {
    console.info('Create Expo');
  },
  setCurrentSuggestionPage(storeState, { page }) {
    storeState.currentSuggestionsPage = page;
  },
  setUserSuggestions(storeState, { suggestions }) {
    storeState.userSuggestions = suggestions;
  },
  setTotalNumSuggestionPages(storeState, { totalNumPages }) {
    storeState.totalNumSuggestionPages = totalNumPages;
  },
  setIsLoadingSuggestions(storeState, { loading }) {
    storeState.isLoadingSuggestions = loading;
  },
};

// Actions are functions that cause side effects and can involve async ops
const actions = {
  changeTab({ commit }, tab) {
    commit('changeTab', tab);
  },
  acceptRejectSuggestion({ commit }, { suggestionID, type }) {
    commit('acceptRejectSuggestion', { suggestionID, type });
  },
  createChat({ commit }, { userID }) {
    commit('createChat', { userID });
  },
  editExpo({ commit }, { expoID }) {
    commit('editExpo', { expoID });
  },
  removeExpo({ commit }, { expoID }) {
    commit('removeExpo', { expoID });
  },
  createExpo({ commit }) {
    commit('createExpo');
  },
  getUserSuggestions({ commit }, { page }) {
    // Setting fixed limit (max number of items for each page)
    const limit = 10;
    // Setting the current page on state
    commit('setCurrentSuggestionPage', { page });
    // Setting that we're loading suggestions
    commit('setIsLoadingSuggestions', { loading: true });

    getUserSuggestions(page, limit)
      .then((response) => {
        console.info(response);
        // Getting total number of pages
        const totalNumPages = Math.ceil(response.total_items / limit);
        // Setting total number of pages
        commit('setTotalNumSuggestionPages', { totalNumPages });
        // Getting suggestions array
        const suggestions = response.suggestions;
        // Setting suggestions
        commit('setUserSuggestions', { suggestions });
        // Setting that we've finish to load suggestions
        commit('setIsLoadingSuggestions', { loading: false });
      });
  },
};

export default new Vuex.Store({
  state,
  actions,
  mutations,
});
