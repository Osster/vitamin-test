<template>
  <div class="message-form px-6 pt-2 h-1/6">
    <form @submit.prevent="onSubmit">
      <textarea
        id="message"
        v-model="message"
        :style="`height:${taHeight}px`"
        class="border w-full p-2"
        @keydown.enter.prevent="onHandleEnter"
      />
    </form>
  </div>
</template>

<script>
export default {
  name: 'MessageForm',
  model: {
    prop: 'value',
    method: 'onSubmit'
  },
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
  computed: {
    taHeight () {
      return (24 * this.lineCount) + 16
    },
    lineCount () {
      const lineBreaks = [...this.message.toString().matchAll(/\n/g)]
      return lineBreaks.length + 1
    }
  },
  mounted () {
    this.message = (this.value && this.value.body) || ''
  },
  methods: {
    onSubmit () {
      const data = {
        id: (this.value && this.value.id) | null,
        body: this.message
      }
      this.message = ''
      this.$nextTick(() => {
        this.$emit('input', data)
        this.$emit('send')
      })
    },
    onHandleEnter (e) {
      if (e.ctrlKey) {
        this.message += '\n'
      } else {
        this.onSubmit()
      }
    }
  }
}
</script>
