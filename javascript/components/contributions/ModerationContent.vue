/**
* This is the content of the Moderation Tab
*/

<script>
import FormTable from '../general/FormTable.vue';
import ItemAcceptRejectSuggestion from './ItemAcceptRejectSuggestion.vue';

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
    FormTable,
    ItemAcceptRejectSuggestion,
  },
}
</script>

<template>
  <div
    class="tab"
    v-bind:class="{ active: active }"
  >
    <FormTable
      :columns="['Foto', 'Nome da Foto', 'Campo', 'Dado Atual', 'Sugestão', 'Revisor', 'Ação']"
    >
      <ItemAcceptRejectSuggestion
        :photoID="1"
        photoName="Teste"
        fieldName="Nome"
        :user="{ id: 1, name: 'Usuario' }"
        currentFieldData="Nome Atual"
        suggestedData="Nome Sugerido"
        :suggestionID="10"
      />
    </FormTable>
  </div>
</template>
