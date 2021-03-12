<template>
  <div
    class="message border rounded-lg bg-green-500 text-white py-4 px-6 my-1 text-left"
    :class="$auth.user.id === message.user_id ? 'bg-blue-500 self-end' : 'bg-green-500 self-start'"
  >
    <div v-if="message.attachments.length">
      <div
        v-for="attach in message.attachments"
        :key="attach.id"
        class="h-36 px-2 py-2"
      >
        <a :href="attach.public_path" target="_blank">
          <img
            :src="attach.public_path"
            :alt="attach.hash"
            class="h-32"
          >
        </a>
      </div>
    </div>
    <div class="text-md" v-html="displayBody" />
    <div class="text-xs text-grey-300">
      {{ displayTime }}
    </div>
    <button v-if="$auth.user.id === message.user_id" class="text-yellow-200" @click="onMessageEdit">Edit</button>
    <button v-if="$auth.user.id === message.user_id" class="text-red-500" @click="onMessageDelete">Delete</button>
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
  mounted () {
    // console.log('message', this.message.id)
    if (this.message.unread) {
      this.$emit('mark-as-read', this.message.id)
    }
  },
  computed: {
    displayBody () {
      return this.message.body && this.message.body.replaceAll(/\n/g, '<br>')
    },
    displayTime () {
      const date = new Date(this.message.at * 1000)
      const hours = date.getHours()
      const minutes = date.getMinutes()
      const day = date.getDay()
      const month = date.getMonth()
      return `${month < 10 ? `0${month}` : month}/${day < 10 ? `0${day}` : day} at ${hours}:${minutes}`
    }
  },
  methods: {
    onMessageEdit () {
      this.$emit('edit', this.message)
    },
    onMessageDelete () {
      this.$emit('delete', this.message)
    }
  }
}
</script>
