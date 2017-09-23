/**
* This is the Reviews Tab component.
*/

<template>
  <div
    class="tab"
    v-bind:class="{ active: active }"
  >
    <ul>
      <ItemNotificationImageText
        imageURL="/img/avatar-48.png"
        text="Você fez uma sugestão"
      />
    </ul>
  </div>
</template>

<script>
import ItemNotificationImageText from './ItemNotificationImageText.vue';

export default {
  name: 'ReviewsContent',
  props: {
    active: {
      type: Boolean,
      default: false,
      required: true,
    },
  },
  components: {
    ItemNotificationImageText,
  },
}
</script>


