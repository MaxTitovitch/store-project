import Vue from 'vue'
import App from './components/App'
import router from './router'
import vuetify from './plugins/vuetify'

Vue.use(vuetify);
Vue.config.productionTip = false;

new Vue({
    el: '#app',
    router,
    components: { App },
    template: '<App/>',
    vuetify
});