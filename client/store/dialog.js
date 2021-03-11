import Vue from 'vue'
import * as Cookies from 'js-cookie'
import cookie from 'cookie'
import FormData from 'form-data'

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
      if (typeof state.items[id] === 'undefined') {
        Vue.set(state.items, id, {})
      }
    })
  },
  setId (state, id) {
    state.id = id
    Cookies.set('selectedDialog', id)
  },
  setItems (state, items) {
    const _items = state.items
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
  },
  clearMessage (state, { dialogId, messageId }) {
    if (typeof state.items[dialogId] === 'undefined') {
      state.items[dialogId] = {}
    }
    Vue.delete(state.items[dialogId], messageId)
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
      // console.error(e.toString())
    }
    commit('setWaiting', false)
  },
  async send ({ commit }, { dialogId, message }) {
    commit('setWaiting', true)
    try {
      const data = new FormData()
      data.append('id', message.id)
      data.append('body', message.body)
      if (message.files.length) {
        data.append('file', message.files[0], message.files[0].fileName)
      }
      const dbMessage = await this.$axios.$post(
        `api/v1/dialog/${dialogId}`,
        data,
        {
          headers: {
            accept: 'application/json',
            'Content-Type': 'multipart/form-data'
          }
        })
      commit('addMessage', dbMessage)
    } catch (e) {
      // console.error(e.toString())
    }
    commit('setWaiting', false)
  },
  async delete ({ commit }, { dialogId, messageId }) {
    commit('setWaiting', true)
    try {
      await this.$axios.$delete(`api/v1/dialog/${dialogId}/${messageId}`)
      commit('clearMessage', { dialogId, messageId })
    } catch (e) {
      // console.error(e.toString())
    }
    commit('setWaiting', false)
  },
  async create ({ commit, dispatch }, contactId) {
    commit('setWaiting', true)
    try {
      const dbDialog = await this.$axios.$post(`api/v1/dialog/new/${contactId}`, {})
      await dispatch('fetch', dbDialog.id)
    } catch (e) {
      // console.error(e.toString())
    }
    commit('setWaiting', false)
  },
  receive ({ commit }, wsMessage) {
    commit('addMessage', wsMessage)
  },
  clear ({ commit }, wsMessage) {
    commit('clearMessage', {
      dialogId: wsMessage.dialog_id,
      messageId: wsMessage.id
    })
  }
}
