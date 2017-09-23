/**
 * This file is the connection between the HTML page and the VUE components.
 * This is the file that will be the enter to bundle with Webpack.
 */

import Vue from 'vue';
import Contributions from './Contributions.vue';

/**
 * In this function we create the Vue component for Contributions
 * @return  The vue component
 */
const createContributionsComponent = () => (
  new Vue({
    el: '#contributions-content',
    template: '<Contributions />',
    components: { Contributions },
  })
);

$(document).ready(() => {
  // When document is ready, we create the Vue component for page
  createContributionsComponent();
});
