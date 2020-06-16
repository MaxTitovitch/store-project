import Vue from 'vue'
import App from './components/App'
import router from './router'
import vuetify from './plugins/vuetify'
import store from './store/index'
import Axios from 'axios'
import { ImagePicker } from "@nagoos/vue-image-picker"

Vue.prototype.$http = Axios;
const token = localStorage.getItem('token')
if (token) {
    Vue.prototype.$http.defaults.headers.common['Authorization'] = 'Bearer ' + token
}
Vue.prototype.$http.defaults.headers.common['Accept'] = 'application/json'

Vue.use(vuetify);
Vue.config.productionTip = false;
Vue.use(ImagePicker);

let vue = new Vue({
    el: '#app',
    router,
    store,
    components: { App },
    template: '<App/>',
    vuetify,
    ImagePicker
});