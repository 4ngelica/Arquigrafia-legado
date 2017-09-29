/**
* This is the main Contributions component.
* It centralizes all other components.
*/

<script>
  import { mapActions } from 'vuex'
  import Tabs from '../../components/general/Tabs.vue';
  import TabContent from '../../components/general/TabContent.vue';
  import ReviewsContent from './ReviewsContent.vue';
  import ModerationContent from './ModerationContent.vue';
  import CuratorshipContent from './CuratorshipContent.vue';
  import store from './store';

  // Creating our tabs object
  const tabProps = [
    {
      id: 'reviews',
      name: 'Revisões',
      href: '#reviews',
    },
    {
      id: 'moderation',
      name: 'Moderação',
      href: '#moderation',
      // hidden: true,
    },
    {
      id: 'curatorship',
      name: 'Curadoria',
      href: '#curatorship',
      locked: true,
    },
  ];

  /** Exporting Vue Component */
  export default {
    name: 'Contributions',
    store,
    components: {
      Tabs,
      TabContent,
      ReviewsContent,
      ModerationContent,
      CuratorshipContent,
    },
    data() {
      return {
        tabProps,
        store,
      }
    },
    methods: mapActions([
      'changeTab',
    ]),
  };
</script>

<template>
  <div id="container">
    <Tabs
      :tabProps="tabProps"
      :selectedTab="store.state.selectedTab"
      :changeTab="changeTab"
    />
    <TabContent
      :selectedTab="store.state.selectedTab"
    >
      <ReviewsContent key="reviews" :active="store.state.selectedTab === 'reviews'" />
      <ModerationContent key="moderation" :active="store.state.selectedTab === 'moderation'" />
      <CuratorshipContent key="curatorship" :active="store.state.selectedTab === 'curatorship'" />
    </TabContent>
  </div>
</template>
