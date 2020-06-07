import axios from 'axios'

let authUser = (commit, user, domain) => {
  return new Promise((resolve, reject) => {
    commit('auth_request')
    axios({ url:  domain, data: user, method: 'POST' })
      .then(resp => {
        let data = resp.data.data;
        console.log(resp.data.data)
        const token = data.token;
        const user = data.user
        localStorage.setItem('token', token)
        localStorage.setItem('tokenRole', user.role)
        localStorage.setItem('tokenName',  user.name + ' ' + user.last_name)
        localStorage.setItem('tokenId', user.id)
        localStorage.setItem('tokenVerify', user.email_verified_at || '')
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + token
        commit('auth_success', token, user)
        resolve(resp)
      })
      .catch(err => {
        console.log(err)
        commit('auth_error', err)
        localStorage.removeItem('token')
        localStorage.removeItem('tokenRole')
        localStorage.removeItem('tokenName')
        localStorage.removeItem('tokenId')
        localStorage.removeItem('tokenVerify')
        reject(err)
      })
  })
}

export default {
  state: {
    domain: location.protocol+'//'+location.hostname+(location.port ? ':'+location.port : ''),
    status: '',
    error: '',
    token: localStorage.getItem('token') || '',
    tokenRole: localStorage.getItem('tokenRole') || '',
    tokenName: localStorage.getItem('tokenName') || 'Гость',
    tokenId: localStorage.getItem('tokenId') || '',
    tokenVerify: localStorage.getItem('tokenVerify') || '',
    user : null
  },
  mutations: {
    auth_request(state){
      state.status = 'loading'
    },
    auth_success(state, token, user){
      state.status = 'success'
      state.token = token
      state.tokenRole = localStorage.getItem('tokenRole') || '';
      state.tokenName = localStorage.getItem('tokenName') || '';
      state.tokenId = localStorage.getItem('tokenId') || '';
      state.tokenVerify = localStorage.getItem('tokenVerify') || '';
      state.user = user
    },
    send_success(state){
      state.status = 'success'
    },
    auth_error(state, error){
      state.status = 'error'
      state.error = error
    },
    logout(state){
      state.status = ''
      state.token = ''
      state.tokenRole = ''
      state.tokenName = 'Гость'
      state.tokenId = ''
      state.tokenVerify = null
    },
  },
  actions: {
    login({commit}, user) {
      return authUser(commit, user, '/api/login')
    },
    verifySend({commit}, user) {
          return new Promise((resolve, reject) => {
            commit('auth_request')
            axios({ url:  '/api/verify/send', data: user, method: 'POST' })
              .then(resp => {
                console.log(resp.data)
                commit('send_success')
                resolve(resp)
              })
              .catch(err => {
                commit('auth_error', err)
                reject(err)
              })
          })
    },
    resetSend({commit}, user) {
          return new Promise((resolve, reject) => {
            commit('auth_request')
            axios({ url:  '/api/reset/send', data: user, method: 'POST' })
              .then(resp => {
                console.log(resp.data)
                commit('send_success')
                resolve(resp)
              })
              .catch(err => {
                console.log(err)
                commit('auth_error', err)
                reject(err)
              })
          })
    },
    resetSave({commit}, user) {
      return authUser(commit, user, '/api/reset/save');
    },
    verifySave({commit}, verifyData) {
      return authUser(commit, verifyData, '/api/verify/save')
    },
    register({commit}, user) {
      return authUser(commit, user, '/api/register')
    },
    logout({commit}){
      return new Promise((resolve, reject) => {
        commit('logout')
        localStorage.removeItem('token')
        localStorage.removeItem('tokenRole')
        localStorage.removeItem('tokenName')
        localStorage.removeItem('tokenId')
        localStorage.removeItem('tokenVerify')
        delete axios.defaults.headers.common['Authorization']
        resolve()
      })
    }
  },
  getters: {
    isLoggedIn: state => !!state.tokenVerify,
    isLoggedInForVerify: state => !!state.token,
    isLoggedRole: state => state.tokenRole,
    isLoggedName: state => state.tokenName,
    isLoggedId: state => state.tokenId,
    authStatus: state => state.status,
  }
}