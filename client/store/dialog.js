import Vue from 'vue'
import * as Cookies from 'js-cookie'
import cookie from 'cookie'

export const store = () => ({
  waiting: false,
  id: null,
  items: {}
})

export const mutations = {
  setWaiting (state, waiting) {
    state.waiting = waiting
  },
  hydrate (state, ids) {
    if (typeof state.items === 'undefined') {
      Vue.set(state, 'items', {})
    }
    ids.forEach((id) => {
      Vue.set(state.items, id, {})
    })
  },
  setId (state, id) {
    state.id = id
    Cookies.set('selectedDialog', id)
  },
  setItems (state, items) {
    const _items = {}
    items.forEach((i) => {
      if (typeof _items[i.dialog_id] === 'undefined') {
        _items[i.dialog_id] = {}
      }
      _items[i.dialog_id][i.id] = i
    })
    state.items = _items
  },
  addMessage (state, message) {
    if (typeof state.items[message.dialog_id] === 'undefined') {
      state.items[message.dialog_id] = {}
    }
    Vue.set(state.items[message.dialog_id], message.id, message)
  }
}

export const getters = {
  id: s => s.id,
  items: s => s.items,
  waiting: s => s.waiting
}

export const actions = {
  nuxtServerInit ({ commit }, { req }) {
    if (req.headers.cookie) {
      const parsedCookies = cookie.parse(req.headers.cookie)
      commit('setId', (parsedCookies && parsedCookies.selectedDialog) | null)
    }
  },
  hydrate ({ commit }, ids) {
    commit('hydrate', ids)
  },
  async fetch ({ commit }, dialogId) {
    commit('setWaiting', true)
    try {
      const items = await this.$axios.$get(`api/v1/dialog/${dialogId}`)
      commit('setId', dialogId)
      commit('setItems', items)
    } catch (e) {
      console.error(e.toString())
    }
    commit('setWaiting', false)
  },
  async send ({ commit }, { dialogId, message }) {
    commit('setWaiting', true)
    try {
      const dbMessage = await this.$axios.$post(`api/v1/dialog/${dialogId}`, message)
      console.log('dbMessage', dbMessage)
      commit('addMessage', dbMessage)
    } catch (e) {
      console.error(e.toString())
    }
    commit('setWaiting', false)
  },
  async create ({ commit, dispatch }, contactId) {
    commit('setWaiting', true)
    try {
      const dbDialog = await this.$axios.$post(`api/v1/dialog/new/${contactId}`, {})
      await dispatch('fetch', dbDialog.id)
      console.log('dbDialog', dbDialog)
    } catch (e) {
      console.error(e.toString())
    }
    commit('setWaiting', false)
  },
  receive ({ commit }, wsMessage) {
    console.log('wsMessage', wsMessage)
    commit('addMessage', wsMessage)
  }
}
