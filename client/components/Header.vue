<template>
  <nav class="bg-white py-2 md:py-4">
    <div class="container px-4 mx-auto md:flex md:items-center">
      <div class="flex justify-between items-center">
        <nuxt-link
          to="/"
          class="font-bold text-xl text-indigo-600"
        >VCh</nuxt-link>
        <button
          class="border border-solid border-gray-600 px-3 py-1 rounded text-gray-600 opacity-50 hover:opacity-75 md:hidden"
          id="navbar-toggle"
          @click="onNavToggle"
        >
          {{ isHidden ? 'O' : 'X' }}
        </button>
      </div>
      <div
        :class="{'hidden': isHidden, 'flex': isHidden}"
        class="md:flex flex-col md:flex-row md:ml-auto mt-3 md:mt-0"
        id="navbar-collapse"
      >
        <nuxt-link
          v-if="isLoggedIn"
          to="/"
          class="p-2 lg:px-4 md:mx-2 text-white rounded bg-indigo-600"
        >Home</nuxt-link>
        <nuxt-link
          v-if="isLoggedIn"
          to="/chat"
          class="p-2 lg:px-4 md:mx-2 text-gray-600 rounded hover:bg-gray-200 hover:text-gray-700 transition-colors duration-300"
        >Chat</nuxt-link>
        <nuxt-link
          v-if="!isLoggedIn"
          to="/login"
          class="p-2 lg:px-4 md:mx-2 text-indigo-600 text-center border border-transparent rounded hover:bg-indigo-100 hover:text-indigo-700 transition-colors duration-300"
        >Login</nuxt-link>
        <button
          v-if="isLoggedIn"
          @click="onLogout"
          class="p-2 lg:px-4 md:mx-2 text-indigo-600 text-center border border-solid border-indigo-600 rounded hover:bg-indigo-600 hover:text-white transition-colors duration-300 mt-1 md:mt-0 md:ml-1"
        >Logout</button>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'Header',
  data () {
    return {
      isHidden: true
    }
  },
  computed: {
    isLoggedIn () {
      return this.$auth.loggedIn
    }
  },
  methods: {
    onLogout () {
      this.$auth.logout()
    },
    onNavToggle () {
      this.isHidden = !this.isHidden
    }
  }
}
</script>
