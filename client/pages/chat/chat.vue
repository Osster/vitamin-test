<template>
  <div class="chat flex flex-row flex-nowrap divide-x divide-light-blue-400 h-full">
    <div
      :class="{ 'hidden': contactId }"
      class="chat-contacts md:block w-full md:w-1/4">
      <contact-list
        :contacts="contacts"
        :selected="contactId"
        @select="onContactSelect"
      />
    </div>
    <div
      :class="{ 'w-full': contactId }"
      class="chat-messages md:w-3/4 flex flex-col justify-end"
    >
      <div
        v-if="contact"
        class="sm:hidden border py-2 px-4 flex flex-row justify-between"
      >
        <strong>{{ contact ? contact.name : '' }}</strong>
        <button
          class="border rounded px-2 py0"
          @click="onBackToList"
        >X</button>
      </div>
      <message-list
        ref="messageList"
        :key="messagesKey"
        :messages="messages"
        :contact="contactId"
        @edit="onMessageEdit"
        @delete="onMessageDelete"
      />
      <message-form
        v-if="contactId"
        v-model="message"
        @send="onSendMessage"
      />
    </div>
  </div>
</template>

<script>
import ContactList from '~/components/ContactList'

export default {
  name: 'Chat',
  components: { ContactList },
  auth: true,
  data: () => ({
    message: null
  }),
  async fetch ({ store }) {
    if (store.getters['contacts/items'].length === 0) {
      await store.dispatch('contacts/fetch')
    }
    const contacts = store.getters['contacts/items']
      .filter(c => c.dialog_id)
      .map(c => c.dialog_id)
    store.dispatch('dialog/hydrate', contacts)
    if (store.getters['dialog/id'] !== null) {
      await store.dispatch('dialog/fetch', store.getters['dialog/id'])
    }
  },
  computed: {
    contacts () {
      return this.$store.getters['contacts/items'] || []
    },
    contactId () {
      return this.$store.getters['contacts/selected']
    },
    contact () {
      return this.contactId && this.contacts
        ? this.contacts.find(c => c.id === this.contactId)
        : null
    },
    dialogId () {
      return this.$store.getters['dialog/id']
    },
    messages () {
      const items = this.$store.getters['dialog/items']
      if (
        this.dialogId &&
        items &&
        typeof items[this.dialogId] !== 'undefined'
      ) {
        return Object.values(items[this.dialogId])
      }
      return []
    },
    messagesKey () {
      return this.messages.length
    }
  },
  mounted () {
    this.$echo.channel('home')
      .listen('NewMessage', (data) => {
        this.$store.dispatch('dialog/receive', data.message)
        this.scrollDown()
      })
      .listen('DeleteMessage', (data) => {
        this.$store.dispatch('dialog/clear', data.message)
        this.scrollDown()
      })
    this.scrollDown()
  },
  methods: {
    async onContactSelect (contact) {
      if (this.$store.getters['contacts/selected'] !== contact.id) {
        this.$store.dispatch('contacts/select', contact.id)
        try {
          if (contact.dialog_id !== null) {
            await this.$store.dispatch('dialog/fetch', contact.dialog_id)
            this.scrollDown()
          } else {
            await this.$store.dispatch('dialog/create', contact.id)
            await this.$store.dispatch('contacts/fetch', contact.id)
          }
        } catch (e) {
          this.$store.dispatch('contacts/select', null)
          // console.error(e.toString())
        }
      } else {
        this.$store.dispatch('contacts/select', null)
      }
    },
    async onSendMessage () {
      try {
        const data = {
          dialogId: this.dialogId,
          message: this.message
        }
        await this.$store.dispatch('dialog/send', data)
        this.message = null
        this.scrollDown()
      } catch (e) {
        // console.error(e.toString())
      }
    },
    async onMessageDelete (message) {
      await this.$store.dispatch('dialog/delete', {
        dialogId: message.dialog_id,
        messageId: message.id
      })
      console.log('delete', message)
    },
    onBackToList () {
      this.$store.dispatch('contacts/select', null)
    },
    onMessageEdit (message) {
      this.message = {
        id: message.id,
        body: message.body
      }
    },
    scrollDown () {
      this.$nextTick(() => {
        if (this.$refs.messageList) {
          const el = this.$refs.messageList.$el
          el.scrollTop = el.scrollHeight
        }
      })
    }
  }
}
</script>

<style lang="scss">
.chat {
  height: calc(100vh - 74px);
}

.chat-messages {
  @apply h-full;
}
</style>
