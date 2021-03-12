export const state = () => ({
  counter: 0
})

export const mutations = {
  increment (state) {
    state.counter++
  }
}

export const actions = {
  // async nuxtServerInit ({ commit, dispatch }, { req }) {
  //   if (req.headers.cookie) {
  //     // cookie found
  //     try {
  //       // check data user login with cookie
  //       const { data } = await this.$axios.get('api/v1/auth/user')
  //       await this.$auth.setUser(data)
  //     } catch (err) {
  //       // No valid cookie found
  //       console.error(err.toString())
  //     }
  //   } else {
  //     console.log('No cookies')
  //   }
  //   dispatch('contacts/nuxtServerInit', { req })
  //   dispatch('dialog/nuxtServerInit', { req })
  // }
}
