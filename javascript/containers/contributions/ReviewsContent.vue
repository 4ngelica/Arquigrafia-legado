/**
* This is the Reviews Tab component.
*/

<script>
import { mapActions } from 'vuex';
import ItemNotificationImageText from '../../components/notification/ItemNotificationImageText.vue';
import store from './store.js';
import { fullDate } from '../../services/DateFormatter.js';

export default {
  name: 'ReviewsContent',
  store,
  props: {
    active: {
      type: Boolean,
      default: false,
      required: true,
    },
  },
  components: {
    ItemNotificationImageText,
  },
  methods: mapActions([
    'getUserSuggestions',
  ]),
  created() {
    this.getUserSuggestions();
  },
  data() {
    return {
      store,
      fullDate,
    };
  },
}
</script>

<template>
  <div
    class="tab"
    v-bind:class="{ active: active }"
  >
    <ul>
      <ItemNotificationImageText
        v-for="suggestion in store.state.userSuggestions"
        v-bind:key="suggestion.id"
        :imageURL="`/arquigrafia-images/${suggestion.photo.id}_home.jpg`"
        :text="`Você sugeriu '${suggestion.text}' para o campo '${suggestion.field.name}' na imagem '${suggestion.photo.name}'`"
        :date="fullDate(suggestion.created_at)"
        :clickableURL="`/photos/${suggestion.photo.id}`"
      />
    </ul>
  </div>
</template>
