/**
* This is the table prepared to receive a list of suggestions
*/

<script>
import ItemAcceptRejectSuggestion from './ItemAcceptRejectSuggestion.vue';

export default {
  name: 'SuggestionTable',
  components: {
    ItemAcceptRejectSuggestion,
  },
}
</script>

<template>
  <table class="form-table suggestions" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>Foto</th>
        <th>Nome da Foto</th>
        <th>Campo</th>
        <th>Dado Atual</th>
        <th>Sugestão</th>
        <th>Revisor</th>
        <th>Ação</th>
      </tr>
    </thead>
    <tbody>
      <ItemAcceptRejectSuggestion
        :photoID="1"
        photoName="Teste"
        fieldName="Nome"
        :user="{ id: 1, name: 'Usuario' }"
        currentFieldData="Nome Atual"
        suggestedData="Nome Sugerido"
        :suggestionID="10"
      />
    </tbody>
  </table>
</template>

<style>
.form-table.suggestions th {
  text-align: left;
  line-height: 30px;
  padding:0 5px 0 5px;
}
</style>
