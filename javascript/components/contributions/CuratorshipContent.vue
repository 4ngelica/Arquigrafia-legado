<script>
export default {
  name: 'CuratorshipContent',
  props: {
    active: {
      type: Boolean,
      default: false,
      required: true,
    },
  },
}
</script>

<template>
  <div
    class="tab"
    v-bind:class="{ active: active }"
  >
    <p>Aba Curadoria</p>
  </div>
</template>
