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

// mutations are operations that actually mutates the state.
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
};

export default new Vuex.Store({
  state,
  actions,
  mutations,
});
