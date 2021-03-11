<template>
  <div class="home-container">
    <div class="home-title">
      Welcome to chat
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
} */

.home-container {
  margin: 0 auto;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.home-title {
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
</style>
