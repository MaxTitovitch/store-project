import Vue from 'vue'
import App from './components/App'
import router from './router'
import vuetify from './plugins/vuetify'
import store from './store/index'
import Axios from 'axios'

Vue.prototype.$http = Axios;
const token = localStorage.getItem('token')
Vue.prototype.$http.defaults.headers.common['Accept'] = 'application/json'
if (token) {
    Vue.prototype.$http.defaults.headers.common['Authorization'] = 'Bearer ' + token
}

Vue.use(vuetify);
Vue.config.productionTip = false;

new Vue({
    el: '#app',
    router,
    store,
    components: { App },
    template: '<App/>',
    vuetify
});