import Vue from 'vue'
import * as Cookies from 'js-cookie'
import cookie from 'cookie'

export const state = () => ({
  waiting: false,
  items: [],
  selected: null
})

export const mutations = {
  setWaiting (state, waiting) {
    state.waiting = waiting
  },
  setItems (state, items) {
    state.items = items
  },
  setSelected (state, itemId) {
    state.selected = itemId
    Cookies.set('selectedContact', itemId)
  },
  increaseUnread (state, userId) {
    if (state.selected !== userId) {
      const contact = state.items.find(i => i.id === userId)
      if (contact) {
        Vue.set(contact, 'm_unread', contact.m_unread + 1)
      }
    }
  },
  decreaseUnread (state, userId) {
    const contact = state.items
      .find((i) => {
        return parseInt(i.id, 10) === parseInt(userId, 10)
      })
    if (contact) {
      Vue.set(contact, 'm_unread', contact.m_unread - 1)
    }
  }
}

export const getters = {
  waiting: s => s.waiting,
  items: s => s.items,
  selected: s => s.selected
}

export const actions = {
  nuxtServerInit ({ commit }, { req }) {
    if (req.headers.cookie) {
      const parsedCookies = cookie.parse(req.headers.cookie)
      commit('setSelected', (parsedCookies && parsedCookies.selectedContact) | null)
    }
  },
  async fetch ({ commit }) {
    commit('setWaiting', true)
    const contacts = await this.$axios.$get('api/v1/contacts/')
    commit('setItems', contacts)
    commit('setWaiting', false)
  },
  select ({ commit }, itemId) {
    commit('setSelected', itemId)
  },
  increaseUnread ({ commit }, userId) {
    commit('increaseUnread', userId)
  },
  decreaseUnread ({ commit }, { userId }) {
    commit('decreaseUnread', userId)
  }
}
