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
       <h3>Aprimorando o acervo</h3>
	<p>Ajude o Arquigrafia a ser uma fonte de conhecimento sobre arquitetura e seus espaços urbanos!</p>
        <p>Você pode colaborar complementado os dados sobre as imagens.</p>
        <p>Clique em "Ajude a completar dados" a partir de qualquer imagem compartilhada por usuários particulares do Arquigrafia.</p>
        <p>Questões vão auxiliar você a completar ou revisar os dados já inseridos.</p>
        <p>Juntos vamos aprimorar esse acervo de mais de 12300 imagens.</p>
        <p>Comece agora mesmo a contribuir acessando uma imagem abaixo. Contamos com você!</p>
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

