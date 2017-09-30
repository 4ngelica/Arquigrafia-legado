<script>
  export default {
    name: 'ContributionsStatistics',
    props: {
      totalSuggestions: {
        type: Number,
        required: true,
      },
      acceptedSuggestions: {
        type: Number,
        required: true,
      },
      waitingSuggestions: {
        type: Number,
        required: true,
      },
      rejectedSuggestions: {
        type: Number,
        required: true,
      },
    },
  }
</script>

<template>
  <div class="container">
    <div class="row">
      <h3>Suas Revisões</h3>
    </div>
    <div class="row">
      <div class="column">
        <p><span class="title">Total:</span> {{ totalSuggestions }}</p>
        <p><span class="title">Aguardando:</span> {{ waitingSuggestions }}</p>
        <p><span class="title">Aceitas:</span> {{ acceptedSuggestions }}</p>
        <p><span class="title">Recusadas:</span> {{ rejectedSuggestions }}</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .container {
    display: flex;
    flex-direction: column;
  }
  .row {
    display: flex;
    flex-direction: row;
    margin: 0;
  }

  .column {
    display: flex;
    flex-direction: column;
    margin: 0;
    margin-right: 20px;
  }

  p {
    font-size: 12px;
    margin: 0;
  }

  .title {
    font-weight: bold;
  }
</style>
