/**
* This is the Curatorship Tab component.
*/

<script>
import FormTable from '../../components/general/FormTable.vue';
import ItemCuratorshipExposition from '../../components/contributions/ItemCuratorshipExposition.vue';

export default {
  name: 'CuratorshipContent',
  props: {
    active: {
      type: Boolean,
      default: false,
      required: true,
    },
  },
  components: {
    FormTable,
    ItemCuratorshipExposition,
  },
}
</script>

<template>
  <div
    class="tab"
    v-bind:class="{ active: active }"
  >
    <FormTable
      :columns="['Capa', 'Nome', 'Descrição', 'Ação']"
    >
      <ItemCuratorshipExposition
        imageURL="http://www.vitruvius.com.br/media/images/magazines/grid_9/4b25282ad814_artigas01.jpg"
        :expositionID="1"
        name="FAU USP"
        description="A Faculdade de Arquitetura e Urbanismo da Universidade de São Paulo é responsável pelo ensino de graduação, pós-graduação, pesquisa e extensão de serviços à sociedade nas áreas de Design, Arquitetura e Urbanismo."
        :handleEdit="() => 0"
        :handleRemove="() => 0"
      />
      <ItemCuratorshipExposition
        imageURL="http://www.vitruvius.com.br/media/images/magazines/grid_9/4b25282ad814_artigas01.jpg"
        :expositionID="1"
        name="FAU USP"
        description="A Faculdade de Arquitetura e Urbanismo da Universidade de São Paulo é responsável pelo ensino de graduação, pós-graduação, pesquisa e extensão de serviços à sociedade nas áreas de Design, Arquitetura e Urbanismo."
        :handleEdit="() => 0"
        :handleRemove="() => 0"
      />
    </FormTable>
  </div>
</template>
