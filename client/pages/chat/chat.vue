<template>
  <div class="chat flex flex-row flex-nowrap divide-x divide-light-blue-400 h-full">
    <div
      :class="{ 'hidden': contactId }"
      class="chat-contacts md:block w-full md:w-1/4">
      <client-only>
        <div
          v-if="$auth.loggedIn"
          class="py-2 px-2 text-center border-t border-b"
        >
          <strong>{{ $auth.user.name }}</strong>
        </div>
      </client-only>
      <contact-list
        :contacts="contacts"
        :selected="contactId"
        @select="onContactSelect"
      />
    </div>
    <div
      :class="{ 'w-full': contactId }"
      class="chat-messages md:w-3/4 flex flex-col justify-between"
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
        @mark-as-read="onMessageMarkAsRead"
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
    const dialogId = store.getters['dialog/id']
    if (dialogId) {
      await store.dispatch('dialog/fetchDialog', dialogId)
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
        return Object.values(items[this.dialogId]).reverse()
      }
      return []
    },
    messagesKey () {
      return this.dialogId
    }
  },
  mounted () {
    this.$echo
      .private(`App.User.${this.$auth.user.id}`)
      .listen('NewMessage', (data) => {
        this.$store.dispatch('dialog/receiveMessage', data.message)
        this.$store.dispatch('contacts/increaseUnread', data.message.user_id)
        this.scrollDown()
      })
      .listen('DeleteMessage', (data) => {
        this.$store.dispatch('dialog/clearMessage', data.message)
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
            await this.$store.dispatch('dialog/fetchDialog', contact.dialog_id)
            this.scrollDown()
          } else {
            await this.$store.dispatch('dialog/createDialog', contact.id)
            await this.$store.dispatch('contacts/fetch', contact.id)
          }
        } catch (e) {
          this.$store.dispatch('contacts/select', null)
          this.$store.dispatch('dialog/selectDialog', null)
        }
      } else {
        this.$store.dispatch('contacts/select', null)
        this.$store.dispatch('dialog/selectDialog', null)
      }
    },
    async onSendMessage () {
      try {
        const data = {
          dialogId: this.dialogId,
          message: this.message
        }
        await this.$store.dispatch('dialog/sendMessage', data)
        this.message = null
        this.scrollDown()
      } catch (e) {
        // console.error(e.toString())
      }
    },
    async onMessageDelete (message) {
      await this.$store.dispatch('dialog/deleteMessage', {
        dialogId: message.dialog_id,
        messageId: message.id
      })
    },
    async onMessageMarkAsRead (messageId) {
      await this.$store.dispatch('dialog/markMessageAsRead', { dialogId: this.dialogId, messageId })
      this.$store.dispatch('contacts/decreaseUnread', { userId: this.contactId })
    },
    onBackToList () {
      this.$store.dispatch('contacts/select', null)
      this.$store.dispatch('dialog/selectDialog', null)
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
