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
       <!-- <h1>O poder da colaboração: a força da inteligência coletiva!</h1>-->
        <b>
	<p>Ajude o Arquigrafia a ser uma fonte de conhecimento e informação sobre Arquitetura e seus Espaços Urbanos no mundo!</p>
        <p>Você pode colaborar complementado os dados sobre as imagens.</p>
        <p>Clique em "Ajude a completar dados" a partir da página de qualquer imagem compartilhada por usuários particulares do Arquigrafia.</p>
        <p>Questões vão auxiliar você a completar ou revisar os dados já inseridos.</p>
        <p>Juntos vamos aprimorar esse acervo de mais de 12300 imagens.</p>
<!--<p>Comece agora mesmo a contribuir acessando uma imagem abaixo e ajude a aprimorar a qualidade das informações de nosso acervo!</p>-->
<p> Contamos com você!</p>
      </b>
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

