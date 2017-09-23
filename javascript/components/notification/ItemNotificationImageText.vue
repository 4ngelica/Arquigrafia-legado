/**
* This component holds a list item with a Image and a Text.
* Can be use to give a "notification" style to the list.
*/

<template>
  <div>
    <li>
      <a v-if="clickableURL != null" :href="clickableURL">
        <div>
          <img class="mini" :src="imageURL"> {{ text }}
          </br>
        </div>
      </a>
      <div v-else>
        <div>
          <img class="mini" :src="imageURL"> {{ text }}
          </br>
        </div>
      </div>
    </li>
  </div>
</template>

<script>
export default {
  name: 'ItemNotificationImageText',
  props: {
    imageURL: {
      type: String,
      required: true,
    },
    text: {
      type: String,
      required: true,
    },
    clickableURL: {
      type: String,
      default: null,
    },
  },
}
</script>
