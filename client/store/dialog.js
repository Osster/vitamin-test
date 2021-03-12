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
    Vue.set(state, 'waiting', waiting)
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
    Vue.set(state, 'id', id)
    Cookies.set('selectedDialog', id)
  },
  setItems (state, items) {
    const _items = state.items
    items.forEach((i) => {
      if (typeof _items[i.dialog_id] === 'undefined') {
        _items[i.dialog_id] = {}
      }
      _items[i.dialog_id][i.at] = i
    })
    Vue.set(state, 'items', _items)
  },
  addMessage (state, message) {
    if (typeof state.items[message.dialog_id] === 'undefined') {
      state.items[message.dialog_id] = {}
    }
    Vue.set(state.items[message.dialog_id], message.at, message)
  },
  clearMessage (state, { dialogId, messageId }) {
    if (typeof state.items[dialogId] === 'undefined') {
      state.items[dialogId] = {}
    }
    const message = Object.values(state.items[dialogId])
      .find(m => m.id === messageId)
    if (message) {
      Vue.delete(state.items[dialogId], message.at)
    }
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
  async fetchDialod ({ commit, dispatch }, dialogId) {
    commit('setWaiting', true)
    try {
      const items = await this.$axios.$get(`api/v1/dialog/${dialogId}`)
      commit('setItems', items)
      dispatch('selectDialog', dialogId)
    } catch (e) {
      // console.error(e.toString())
    }
    commit('setWaiting', false)
  },
  async createDialod ({ commit, dispatch }, contactId) {
    commit('setWaiting', true)
    try {
      const dbDialog = await this.$axios.$post(`api/v1/dialog/new/${contactId}`, {})
      await dispatch('fetch', dbDialog.id)
    } catch (e) {
      // console.error(e.toString())
    }
    commit('setWaiting', false)
  },
  selectDialog ({ commit }, dialogId) {
    commit('setId', dialogId)
  },
  async sendMessage ({ commit }, { dialogId, message }) {
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
  async deleteMessage ({ commit }, { dialogId, messageId }) {
    commit('setWaiting', true)
    try {
      await this.$axios.$delete(`api/v1/dialog/${dialogId}/${messageId}`)
      commit('clearMessage', { dialogId, messageId })
    } catch (e) {
      // console.error(e.toString())
    }
    commit('setWaiting', false)
  },
  receiveMessage ({ commit }, wsMessage) {
    commit('addMessage', wsMessage)
  },
  clearMessage ({ commit }, wsMessage) {
    commit('clearMessage', {
      dialogId: wsMessage.dialog_id,
      messageId: wsMessage.id
    })
  }
}
