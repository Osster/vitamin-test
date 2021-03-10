<template>
  <div class="message-list px-8 py-10 divide-y divide-light-blue-400">
    <div v-if="contact" class="flex flex-col">
      <message
        v-for="message in messages"
        :key="message.id"
        :message="message"
        :contact="contact"
      >
      </message>
    </div>
    <slot v-else></slot>
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
  }
}
</script>
