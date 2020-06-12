import Vue from 'vue'
import Router from 'vue-router'
import Home from '../components/Home';
import NotFound from '../components/NotFound';
import Register from '../components/Auth/Register';
import Login from '../components/Auth/Login';
import ResetSend from '../components/Auth/ResetSend';
import EmailVerify from '../components/Auth/EmailVerify';
import PasswordReset from '../components/Auth/PasswordReset';
import EmailVerifySend from '../components/Auth/EmailVerifySend';
import AdminMain from '../components/Admin/Main';
import MassUpload from '../components/Admin/MassUpload';
import store from '../store/index'

Vue.use(Router);

let router = new Router({
    routes: [
        {
            name: 'home',
            path: '',
            component: Home
        },
        {
            name: 'register',
            path: '/register',
            component: Register
        },
        {
            name: 'login',
            path: '/login',
            component: Login
        },
        {
            name: 'login',
            path: '/login',
            component: Login
        },
        {
            name: 'reset-send',
            path: '/reset-send',
            component: ResetSend
        },
        {
            name: 'main-admin',
            path: '/admin',
            component: AdminMain,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'mass-upload',
            path: '/admin/mass-upload',
            component: MassUpload,
            meta: {
                requiredRole: ['Главный администратор']
            }
        },
        {
            name: 'personal',
            path: '/personal',
            component: AdminMain,
            meta: {
                requiredRole: ['Пользователь','Администратор', 'Главный администратор']
            }
        },
        {
            name: 'email-verify',
            path: '/email-verify',
            component: EmailVerify,
        },
        {
            name: 'change-password',
            path: '/change-password',
            component: PasswordReset,
        },
        {
            name: 'verify-send',
            path: '/verify-send',
            component: EmailVerifySend,
            meta: {
                noRequiredVerify: true,
                requiredRole: ['Пользователь', 'Администратор', 'Главный администратор']
            }
        },
        {
            path: '/404',
            name: '404',
            component: NotFound,
        },
        {
            path: '*',
            redirect: '/404'
        }
    ],
    mode: 'history'
})

router.beforeEach((to, from, next) => {
    if(to.matched.some(record => record.meta.requiredRole)) {
        if (to.matched.some(record => record.meta.requiredRole.lastIndexOf(store.getters.isLoggedRole) !== -1) ) {
            if(!store.getters.isLoggedIn && !to.matched.some(record => record.meta.noRequiredVerify)) {
                next('/verify-send')
            } else {
                next()
            }
            return
        }
        next('/')
    } else {
        next()
    }
})

export default router