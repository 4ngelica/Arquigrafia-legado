/**
* This is the Curatorship Tab component.
*/

<script>
import FormTable from '../../components/general/FormTable.vue';

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
    
    </FormTable>
  </div>
</template>
