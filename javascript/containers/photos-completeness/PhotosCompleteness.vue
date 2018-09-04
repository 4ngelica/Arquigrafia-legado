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
      <div class="twelve columns row">
        <h1>Nota de repúdio</h1>
        <table bgcolor = "black">
        <font color="white">
        <p>Devido aos nossos esforços para preservar a herança e cultura brasileiras,</p>
        <p>consideramos importante apresentar uma nota pública de repúdio aos cortes</p>
        <p>orçamentários que comprometeram a manutenção do Museu Nacional no</p>
        <p>Rio de Janeiro, levando ao incêndio que destruiu sua coleção de mais de 20</p>
        <p>milhões de itens no dia 2/09/2018. É também para evitar perdas dessa</p>
        <p>natureza que continuaremos trabalhando em colaboração.</p>
        </font>
        </table>
      </div>

	
      <div class="twelve columns row">
      <h1>Colabore conosco na preservação do patrimônio cultural</h1>
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
