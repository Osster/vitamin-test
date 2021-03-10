<template>
  <div class="container">
    <div>
      <div class="links">
        <nuxt-link v-if="!isLoggedIn" to="/login">
          <button class="px-6 py-3 border border-green-500 rounded">Login</button>
        </nuxt-link>
        <nuxt-link v-if="isLoggedIn" to="/chat">
          <button class="px-6 py-3 border border-blue-500 rounded">Go to chat</button>
        </nuxt-link>
        <button
          v-if="isLoggedIn"
          @click="onLogout"
          class="px-6 py-3 border border-red-500 rounded"
        >
          Log out
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Index',
  auth: 'guest',
  computed: {
    isLoggedIn () {
      return this.$auth.loggedIn
    }
  },
  methods: {
    onLogout () {
      this.$auth.logout()
    }
  }
}
</script>

<style>
/* Sample `apply` at-rules with Tailwind CSS
.container {
@apply min-h-screen flex justify-center items-center text-center mx-auto;
}
*/
.container {
  margin: 0 auto;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.title {
  font-family:
    'Quicksand',
    'Source Sans Pro',
    -apple-system,
    BlinkMacSystemFont,
    'Segoe UI',
    Roboto,
    'Helvetica Neue',
    Arial,
    sans-serif;
  display: block;
  font-weight: 300;
  font-size: 100px;
  color: #35495e;
  letter-spacing: 1px;
}

.subtitle {
  font-weight: 300;
  font-size: 42px;
  color: #526488;
  word-spacing: 5px;
  padding-bottom: 15px;
}

.links {
  padding-top: 15px;
}
</style>
