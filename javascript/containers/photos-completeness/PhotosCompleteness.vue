<script>
  import { mapActions } from 'vuex';
  import PhotoGrid from '../../components/photos/PhotoGrid.vue';
  import Pager from '../../components/general/Pager.vue';
  import Spinner from '../../components/general/Spinner.vue';
  import store from './store';

  export default {
    name: 'PhotosCompleteness',
    store,
    components: {
      PhotoGrid,
      Pager,
      Spinner,
    },
    methods: mapActions([
      'getPhotosToComplete',
    ]),
    data() {
      return {
        store,
      };
    },
    created() {
      this.getPhotosToComplete();
    },
  };
</script>

<template>
  <div>
    <div class="container">
      <div id="search_result" class="twelve columns row">
        <h1>Aprimorando o acervo</h1>
        <p>Agora você pode compartilhar seu conhecimento sobre a arquitetura representada em uma imagem!</p>
        <p>Para contribuir clique em "Ajude a completar dados" a partir da página com os detalhes de qualquer imagem inserida por membros do Arquigrafia.</p>
        <p>Você será questionado para completar dados faltantes ou revisar dados já inseridos.</p>
        <p>O objetivo é completar e verificar dados de imagens, garantindo maior qualidade. Ajude a completar e aprimorar o nosso acervo!</p>
      </div>
    </div>
    <div v-if="store.state.isLoadingPhotos">
      <Spinner />
    </div>
    <div v-else>
      <PhotoGrid :photos="store.state.photos" />
    </div>
  </div>
</template>

