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
        <h1>O poder da colaboração: a força da inteligência coletiva!</h1>
	<p>Grandes grupos de pessoas podem produzir grandes transformações, somando suas inteligências com o mesmo propósito.</p>
        <p>Pensando nisso, o Arquigrafia quer somar forças com você para aprimorar o acervo de imagens que agora conta com mais de 12300 imagens.</p>
        <p>Para contribuir clique em "Ajude a completar dados" a partir da página de qualquer imagem inserida por usuários particulares do Arquigrafia.</p>
        <p>A partir daí, questões irão auxiliar você a completar dados faltantes ou revisar dados já inseridos.</p>
        <p>Comece agora mesmo a contribuir acessando uma imagem abaixo e ajude a aprimorar a qualidade das informações de nosso acervo!</p>
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

