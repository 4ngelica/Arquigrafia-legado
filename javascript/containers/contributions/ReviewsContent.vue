/**
* This is the Reviews Tab component.
*/

<script>
import { mapActions } from 'vuex';
import Pager from '../../components/general/Pager.vue';
import ItemNotificationImageText from '../../components/notification/ItemNotificationImageText.vue';
import store from './store.js';
import { fullDate } from '../../services/DateFormatter.js';
import Spinner from '../../components/general/Spinner.vue';

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
    Pager,
    Spinner,
  },
  methods: Object.assign({},
    mapActions([
      'getUserSuggestions',
    ]),
    {
      handleChangePage(page) {
        this.getUserSuggestions({ page });
      },
      suggestionText(status, text, fieldName, photoName, userName) {
        if (status === null) {
          return `Sua sugestão '${text}' para o campo '${fieldName}' na imagem '${photoName}' foi enviada para a revisão do autor da imagem '${userName}'.`;
        } else if (status) {
          return `Sua sugestão '${text}' para o campo '${fieldName}' na imagem '${photoName}' foi aceita pelo autor da imagem '${userName}'!`;
        } else if (!status) {
          return `Sua sugestão '${text}' para o campo '${fieldName}' na imagem '${photoName}' foi recusada pelo autor da imagem '${userName}'.`;
        }
        return '';
      },
    }
  ),
  created() {
    this.getUserSuggestions({ page: 1 });
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
    <div v-if="store.state.isLoadingSuggestions">
      <Spinner />
    </div>
    <div v-if="!store.state.isLoadingSuggestions && store.state.userSuggestions.length > 0">
      <ul>
        <ItemNotificationImageText
          v-for="suggestion in store.state.userSuggestions"
          v-bind:key="suggestion.id"
          :imageURL="`/arquigrafia-images/${suggestion.photo.id}_home.jpg`"
          :text="suggestionText(suggestion.accepted, suggestion.text, suggestion.field.name, suggestion.photo.name, suggestion.photo.user.name)"
          :date="fullDate(suggestion.updated_at)"
          :clickableURL="`/photos/${suggestion.photo.id}`"
        />
      </ul>
      <Pager
        :currentPage="store.state.currentSuggestionsPage"
        :numPages="store.state.totalNumSuggestionPages"
        :handleChangePage="handleChangePage"
      />
    </div>
    <div v-if="!store.state.isLoadingSuggestions && store.state.userSuggestions.length === 0">
      <p>Você ainda não realizou nenhuma revisão.</p>
    </div>
  </div>
</template>
