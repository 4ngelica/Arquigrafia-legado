<script>
/** Exporting Vue Component */
export default {
  name: 'Tabs',
  props: {
    tabProps: {
      type: Array,
      default: [],
      required: true,
    },
    selectedTab: {
      type: String,
      default: '',
      required: true,
    },
    changeTab: {
      type: Function,
      required: true,
    }
  },
};
</script>

<template>
  <div class="tabs">
    <ul class="tab-links">
      <li
        v-for="tab in tabProps"
        v-bind:key="tab.id"
        v-bind:class="{ active: selectedTab === tab.id }"
      >
        <a :href="tab.href" @click="changeTab(tab)">
          {{ tab.name }}
        </a>
      </li>
    </ul>
  </div>
</template>
