/**
* This is the Reviews Tab component.
*/

<script>
import ItemNotificationImageText from '../../components/notification/ItemNotificationImageText.vue';

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
