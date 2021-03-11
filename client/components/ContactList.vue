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
