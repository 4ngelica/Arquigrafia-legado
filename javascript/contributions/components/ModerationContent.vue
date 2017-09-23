/**
* This is the content of the Moderation Tab
*/

<script>
import SuggestionTable from './SuggestionTable.vue';

export default {
  name: 'ModerationContent',
  props: {
    active: {
      type: Boolean,
      default: false,
      required: true,
    },
  },
  components: {
    SuggestionTable,
  },
}
</script>

<template>
  <div
    class="tab"
    v-bind:class="{ active: active }"
  >
    <SuggestionTable />
  </div>
</template>
