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
import ContributionsStatistics from '../../components/contributions/ContributionsStatistics.vue';

export default {
  name: 'EditionsContent',
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
    ContributionsStatistics,
  },
  methods: Object.assign({},
    mapActions([
      'getUserSuggestions',
      'getUserSuggestionsStatistics',
    ]),
    {
      handleChangePage(page) {
        this.getUserSuggestions({ page, type: 'editions' });
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
    // Getting user suggestions statistics
    this.getUserSuggestionsStatistics({ type: 'editions' });
    // Getting user suggestions
    this.getUserSuggestions({ page: 1, type: 'editions' });
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
    <div v-if="store.state.editionsSuggestionsStatistics !== null" class="statistics-container">
      <ContributionsStatistics
        title="Suas Edições"
        :acceptedSuggestions="store.state.editionsSuggestionsStatistics.accepted"
        :waitingSuggestions="store.state.editionsSuggestionsStatistics.waiting"
        :rejectedSuggestions="store.state.editionsSuggestionsStatistics.rejected"
        :totalSuggestions="store.state.editionsSuggestionsStatistics.total"
      />
    </div>
    <div v-if="store.state.isLoadingEditionsSuggestions">
      <Spinner />
    </div>
    <div v-if="!store.state.isLoadingEditionsSuggestions && store.state.userEditionsSuggestions.length > 0">
      <ul>
        <ItemNotificationImageText
          v-for="suggestion in store.state.userEditionsSuggestions"
          v-bind:key="suggestion.id"
          :imageURL="`/arquigrafia-images/${suggestion.photo.id}_home.jpg`"
          :text="suggestionText(suggestion.accepted, suggestion.text, suggestion.field.name, suggestion.photo.name, suggestion.photo.user.name)"
          :date="fullDate(suggestion.updated_at)"
          :clickableURL="`/photos/${suggestion.photo.id}`"
        />
      </ul>
      <Pager
        :currentPage="store.state.editionsCurrentSuggestionsPage"
        :numPages="store.state.editionsTotalNumSuggestionPages"
        :handleChangePage="handleChangePage"
      />
    </div>
    <div v-if="!store.state.isLoadingEditionsSuggestions && store.state.userEditionsSuggestions.length === 0">
      <p>Você ainda não realizou nenhuma revisão.</p>
    </div>
  </div>
</template>

<style scoped>
  .statistics-container {
    margin-top: 10px;
    margin-bottom: 20px;
  }
</style>
