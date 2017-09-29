/**
 * VUEX STORE FOR CONTRIBUTIONS
 */

import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

// Root state object
const state = {
  selectedTab: 'reviews',
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
};

export default new Vuex.Store({
  state,
  actions,
  mutations,
});
