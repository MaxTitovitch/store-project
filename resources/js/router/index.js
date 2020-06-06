import Vue from 'vue'
import Router from 'vue-router'
import ExampleComponent from '../components/ExampleComponent';

Vue.use(Router);

export default new Router({
    routes: [
        {
            name: 'example',
            path: '',
            component: ExampleComponent
        }
    ],
    mode: 'history'
})