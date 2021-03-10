<template>
  <div class="message-form px-6">
    <form @submit.prevent="onSubmit">
      <textarea
        v-model="message"
        id="message"
        class="border w-full h-20"
      ></textarea>
      <button
        type="submit"
        class="bg-blue-300 border px-6 py-4"
      >Send</button>
    </form>
  </div>
</template>

<script>
export default {
  name: 'MessageForm',
  props: {
    value: {
      type: Object,
      default: () => ({
        id: null,
        body: ''
      })
    }
  },
  data: () => ({
    message: ''
  }),
  mounted () {
    this.message = (this.value && this.value.body) || ''
  },
  model: {
    prop: 'value',
    method: 'onSubmit'
  },
  methods: {
    onSubmit () {
      this.$emit('input', {
        id: (this.value && this.value.id) | null,
        body: this.message
      })
      this.$emit('send')
      this.message = ''
    }
  }
}
</script>
