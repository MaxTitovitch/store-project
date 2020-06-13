import axios from 'axios'

let sendRequest = function (commit, data, path, method, file = false) {
  return new Promise((resolve, reject) => {
    commit('request')
    let sendParams = method === 'POST' ? { url: path, data: data, method: method } : { url: path, params: data, method: method }
    axios(sendParams)
      .then(resp => {
        commit('request_success')
        resolve(resp.data)
      })
      .catch(err => {
        commit('request_error', err)
        reject(err)
      })
  })
}

export default {
  state: {
    status: '',
    error: '',
  },
  mutations: {
    request (state) {
      state.status = 'loading'
    },
    request_success (state) {
      state.status = 'success'
    },
    request_error (state, err) {
      state.status = 'error'
      state.error = err
    },
  },
  actions: {
    getStatistics ({ commit }, data) {
      return sendRequest(commit, data, '/api/statistic', 'GET')
    },
    uploadFile ({ commit }, data) {
      return new Promise((resolve, reject) => {
        let formData = new FormData()
        formData.append('file', data.file, data.file.name)
        formData.append('type', data.type)
        axios.post('/api/mass-upload', formData, {'Content-Type': 'multipart/form-data' })
          .then(resp => {
            commit('request_success')
            resolve(resp.data)
          }).catch(err => {
          commit('request_error', err)
          reject(err)
        })
      })
    },
    getProducts ({ commit }, data) {
      return sendRequest(commit, data, '/api/products', 'GET')
    },
    getUsers ({ commit }, data) {
      return sendRequest(commit, data, '/api/users', 'GET')
    },
    postUsers ({ commit }, data) {
      return sendRequest(commit, data, '/api/users', 'POST')
    },
    putUsers ({ commit }, data) {
      return sendRequest(commit, data.data, '/api/users/' + data.id, 'PUT')
    },
    deleteUsers ({ commit }, data) {
      return sendRequest(commit, null, '/api/users/' + data.id, 'DELETE')
    },
  },
  getters: {
    error: state => state.error,
    status: state => state.status,
    isLoading: state => state.status === 'loading',
  }
}