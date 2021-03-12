<template>
  <div
    v-if="contact"
    class="message-list px-2 py-4 md:px-8 md:py-10 divide-y divide-light-blue-400 h-5/6 overflow-hidden overflow-y-auto flex flex-col-reverse flex-grow"
  >
    <message
      v-for="message in messages"
      :key="message.id"
      :message="message"
      :contact="contact"
      @edit="onMessageEdit"
      @delete="onMessageDelete"
      @mark-as-read="markAsRead"
    />
  </div>
</template>

<script>
export default {
  name: 'MessageList',
  props: {
    contact: {
      type: [Number, null],
      default: null
    },
    messages: {
      type: Array,
      default: () => ([])
    }
  },
  watch: {
    contact (newContact, oldContact) {
      if (newContact !== null && newContact !== oldContact) {
        // fetch messages
      }
    }
  },
  methods: {
    onMessageEdit (message) {
      this.$emit('edit', message)
    },
    onMessageDelete (message) {
      this.$emit('delete', message)
    },
    markAsRead (messageId) {
      this.$emit('mark-as-read', messageId)
    }
  }
}
</script>
