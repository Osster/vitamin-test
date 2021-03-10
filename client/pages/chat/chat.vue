<template>
  <div class="chat flex flex-row flex-nowrap divide-x divide-light-blue-400">
    <div class="chat-contacts w-1/4">
      <contact-list
        :contacts="contacts"
        :selected="contactId"
        @select="onContactSelect"></contact-list>
    </div>
    <div class="chat-messages w-3/4">
      <message-list
        :contact="contactId"
        :messages="messages"
        :key="messagesKey"
      >
        <div class="text-center text-xl font-bold">Select contact to start chattig.</div>
      </message-list>
      <message-form
        v-if="contactId"
        v-model="message"
        @send="onSendMessage"
      ></message-form>
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
    if (store.getters['contacts/selected'] !== null) {
      await store.dispatch('dialog/fetch', store.getters['contacts/selected'])
    }
  },
  mounted () {
    this.$echo.channel('home')
      .listen('NewMessage', (data) => {
        this.$store.dispatch('dialog/receive', data.message)
      })
    this.$store.dispatch('dialog/hydrate', this.contacts.filter(c => c.dialog_id).map(c => c.dialog_id))
  },
  computed: {
    contacts () {
      return this.$store.getters['contacts/items'] || []
    },
    contactId () {
      return this.$store.getters['contacts/selected']
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
        console.log({ items, dId: this.dialogId, dItems: items[this.dialogId] })
        return Object.values(items[this.dialogId])
      }
      return []
    },
    messagesKey () {
      return this.messages.length
    }
  },
  watch: {
    messages (messages) {
      console.log({ messages })
    }
  },
  methods: {
    async onContactSelect (contact) {
      if (this.$store.getters['contacts/selected'] !== contact.id) {
        this.$store.dispatch('contacts/select', contact.id)
        try {
          if (contact.dialog_id !== null) {
            await this.$store.dispatch('dialog/fetch', contact.dialog_id)
          } else {
            await this.$store.dispatch('dialog/create', contact.id)
            await this.$store.dispatch('contacts/fetch', contact.id)
          }
        } catch (e) {
          this.$store.dispatch('contacts/select', null)
          console.error(e.toString())
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
      } catch (e) {
        console.error(e.toString())
      }
    }
  }
}
</script>

<style scoped>
.chat {
  text-align: center;
}
</style>
