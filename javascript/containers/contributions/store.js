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
  changeTab(state, tab) {
    state.selectedTab = tab.id;
  },
};

// Actions are functions that cause side effects and can involve async ops
const actions = {
  changeTab: ({ commit }, tab) => commit('changeTab', tab),
};

export default new Vuex.Store({
  state,
  actions,
  mutations,
});
