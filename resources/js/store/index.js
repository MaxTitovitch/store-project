import Vue from 'vue'
import Vuex from 'vuex'
import auth from './auth'
import admin from './admin'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    auth,
    admin
  }
})