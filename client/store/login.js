export const state = () => ({
  waiting: false,
  email: null,
  password: null
})

export const mutations = {
  setWaiting (state, waiting) {
    state.waiting = waiting
  },
  setEmail (state, email) {
    state.email = email
  },
  setPassword (state, password) {
    state.password = password
  }
}



export const getters = {
  waiting: s => s.waiting,
  email: s => s.email,
  password: s => s.password
}

export const actions = {
  async login ({ state, commit }) {
    commit('setWaiting', true)
    await this.$auth.loginWith('laravelSanctum', {
      data: {
        email: state.email,
        password: state.password
      }
    }).then((resp) => {
      console.log('Login resp', resp.headers.authorization)
      this.$auth.strategy.$auth.setUserToken(resp.headers.authorization)
    })
    // console.log('Login 2 resp', this.$auth.$storage, this.$auth.$storage.getCookies())
    commit('setWaiting', false)
  }
}
