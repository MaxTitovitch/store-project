import Vue from 'vue'
import App from './components/App'
import router from './router'
import vuetify from './plugins/vuetify'
import store from './store/index'
import Axios from 'axios'
import { ImagePicker } from "@nagoos/vue-image-picker"
import Donut from 'vue-css-donut-chart';
import { BarChart, LineChart} from 'dr-vue-echarts';
import VueAwesomeSwiper from 'vue-awesome-swiper'
import { saveAs } from 'file-saver';

// import style
import 'swiper/css/swiper.css'
import 'vue-css-donut-chart/dist/vcdonut.css';

Vue.prototype.$http = Axios;
const token = localStorage.getItem('token')
if (token) {
    Vue.prototype.$http.defaults.headers.common['Authorization'] = 'Bearer ' + token
}
Vue.prototype.$http.defaults.headers.common['Accept'] = 'application/json'

Vue.use(vuetify);
Vue.config.productionTip = false;
Vue.use(ImagePicker);
Vue.use(Donut);
Vue.use(BarChart);
Vue.use(LineChart);
Vue.use(VueAwesomeSwiper)

let vue = new Vue({
    el: '#app',
    router,
    store,
    components: { App },
    template: '<App/>',
    vuetify,
    ImagePicker
});