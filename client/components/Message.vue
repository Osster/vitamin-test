<template>
  <div
    class="message border rounded-lg bg-green-500 text-white py-4 px-6 my-1 text-left"
    :class="contact === message.user_id ? 'bg-green-500 self-start' : 'bg-blue-500 self-end'"
  >
    <div class="text-md" v-html="displayBody" />
    <div class="text-xs text-grey-300">
      {{ message.at }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'Message',
  props: {
    message: {
      type: Object,
      default: () => ({})
    },
    contact: {
      type: [Number, null],
      default: null
    }
  },
  computed: {
    displayBody () {
      return this.message.body.replaceAll(/\n/g, '<br>')
    }
  }
}
</script>
