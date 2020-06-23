import Vue from 'vue'
import Vuex from 'vuex'
import auth from './auth'
import admin from './admin'
import cart from './cart'
import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    auth,
    admin,
    cart
  },
  plugins: [createPersistedState()],
})