<template>
  <div class="message-form px-6 py-2 h-1/6">
    <div class="attachment" ref="preview"></div>
    <form @submit.prevent="onSubmit" class="flex flex-row justify-between">
      <button
        class="border rounded px-2 py0"
        @click.prevent="onImageSelect"
      >Attach</button>
      <textarea
        ref="messageText"
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
        body: '',
        files: []
      })
    }
  },
  data: () => ({
    imageEl: null,
    files: [],
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
  watch: {
    value: {
      handler () {
        this.message = (this.value && this.value.body) || ''
      },
      deep: true
    }
  },
  methods: {
    onSubmit () {
      const data = {
        id: (this.value && this.value.id) | null,
        body: this.message,
        files: this.files
      }
      this.message = ''
      this.files = []
      this.$refs.preview.innerHTML = ''
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
    },
    onImageSelect () {
      const vm = this
      this.imageEl = document.createElement('input')
      this.imageEl.type = 'file'
      this.imageEl.accept = 'image/*'
      this.imageEl.addEventListener('change', function () { vm.onImageSelected(vm, this.files) }, false)
      this.imageEl.click()
    },
    onImageSelected (vm, files) {
      vm.imageEl = null
      vm.files = files
      const preview = vm.$refs.preview
      if (!files.length) {
        return
      }
      const file = files[0]
      if ((!file.type.startsWith('image/') || !preview)) {
        return
      }
      const img = document.createElement('img')
      img.classList.add('obj')
      img.file = file
      preview.appendChild(img)
      const reader = new FileReader()
      reader.onload = (function (aImg) {
        return function (e) {
          aImg.src = e.target.result
        }
      })(img)
      reader.readAsDataURL(file)
      if (vm.$refs.messageText) {
        vm.$refs.messageText.focus()
      }
    }
  }
}
</script>

<style lang="scss">
.attachment {
  @apply .flex .flex-row .justify-end;

  & > * {
    @apply .h-24 .px-2 .py-2 .border;
  }
}
</style>
