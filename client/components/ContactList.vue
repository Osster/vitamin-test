<template>
  <div class="contact-list px-2 py-4 md:px-8 md:py-10 divide-y divide-light-blue-400">
    <a
      v-for="contact in contacts"
      :key="contact.id"
      class="block text-left py-2 px-4"
      :class="{'bg-blue-100': contact.id === selected}"
      @click.prevent="onContactSelected(contact)"
    >
      <span>
        {{ contact.name }}
        <span
          v-if="contact.m_unread > 0"
          class="bg-green-400 rounded-full text-xs text-white px-1 py0"
        >{{ contact.m_unread }}</span>
      </span>
    </a>
  </div>
</template>

<script>
export default {
  name: 'ContactList',
  props: {
    contacts: {
      type: Array,
      default: () => []
    },
    selected: {
      type: [Number, null],
      default: null
    }
  },
  methods: {
    onContactSelected (contact) {
      this.$emit('select', contact)
    }
  }
}
</script>
