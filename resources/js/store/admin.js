import axios from 'axios'

let sendRequest = function (commit, data, path, method, file = false) {
  return new Promise((resolve, reject) => {
    commit('request')
    let sendParams = method !== 'GET' ? { url: path, data: data, method: method } : {
      url: path,
      params: data,
      method: method
    }
    console.log(sendParams)
    axios(sendParams)
      .then(resp => {
        console.log(resp)
        commit('request_success')
        resolve(resp.data)
      })
      .catch(err => {
        console.log(err)
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
  },
  getters: {
    error: state => state.error,
    status: state => state.status,
    isLoading: state => state.status === 'loading',
  }
}