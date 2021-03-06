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
        console.log(err.response.data)
        commit('request_error', err)
        reject(err)
      })
  })
}

let sendFileRequest = function (commit, data, path) {'/api/mass-upload'
  return new Promise((resolve, reject) => {
    let formData = new FormData()
    formData.append('file', data.file.imageFile, data.file.name)
    formData.append('type', data.type)
    if(data.slug !== undefined) {
      formData.append('slug', data.slug)
    }
    axios.post(path, formData, {'Content-Type': 'multipart/form-data' })
      .then(resp => {
        commit('request_success')
        resolve(resp.data)
      }).catch(err => {
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
    getSchedulePie ({ commit }, data) {
      return sendRequest(commit, data, `/api/pie-chart/${data.param}`, 'GET')
    },
    getScheduleBar ({ commit }, data) {
      return sendRequest(commit, data.data, `/api/bar-chart/${data.entity}/${data.param}`, 'GET')
    },
    getScheduleLine ({ commit }, data) {
      return sendRequest(commit, data.data, `/api/line-chart/${data.entity}/${data.id}/${data.param}`, 'GET')
    },

    getStatistics ({ commit }, data) {
      return sendRequest(commit, data, '/api/statistic', 'GET')
    },
    uploadFile ({ commit }, data) {
      return sendFileRequest (commit, data, '/api/mass-upload');
    },
    uploadPhoto ({ commit }, data) {
      return sendFileRequest (commit, data, '/api/photo');
    },
    uploadPhotoUser ({ commit }, data) {
      return sendFileRequest (commit, data.data, '/api/photo/user-image/' + data.id);
    },
    getProducts ({ commit }, data) {
      return sendRequest(commit, data, '/api/products', 'GET')
    },

    getOneEntity ({ commit }, data) {
      return sendRequest(commit, data.data, `/api/${data.entity}/${data.id}`, 'GET')
    },
    getEntity ({ commit }, data) {
      return sendRequest(commit, data.data, `/api/${data.entity}`, 'GET')
    },
    postEntity({ commit }, data) {
      return sendRequest(commit, data.data, `/api/${data.entity}`, 'POST')
    },
    putEntity ({ commit }, data) {
      return sendRequest(commit, data.data, `/api/${data.entity}/${data.id}`, 'PUT')
    },
    deleteEntity ({ commit }, data) {
      return sendRequest(commit, null, `/api/${data.entity}/${data.id}`, 'DELETE')
    },
    checkDeviceType ({ commit }, data) {
      if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        return true;
      } else {
        return false;
      }
    },
  },
  getters: {
    error: state => state.error,
    status: state => state.status,
    isLoading: state => state.status === 'loading',
  }
}