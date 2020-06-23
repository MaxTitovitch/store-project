import Vue from 'vue'
import Router from 'vue-router'
import store from '../store/index'

import Home from '../components/Main/Home';
import Order from '../components/Main/Order';
import NotFound from '../components/Main/NotFound';
import Personal from '../components/Auth/Personal';
import Register from '../components/Auth/Register';
import Login from '../components/Auth/Login';
import ResetSend from '../components/Auth/ResetSend';
import EmailVerify from '../components/Auth/EmailVerify';
import PasswordReset from '../components/Auth/PasswordReset';
import EmailVerifySend from '../components/Auth/EmailVerifySend';
import AdminMain from '../components/Admin/Main';
import MassUpload from '../components/Admin/MassUpload';
import UsersList from '../components/Admin/Users/List';
import CommentsList from '../components/Admin/Comments/List';
import TagsList from '../components/Admin/Tags/List';
import SaleCategoriesList from '../components/Admin/SaleCategories/List';
import SalesList from '../components/Admin/SaleCategories/SaleList';
import PostsList from '../components/Admin/Posts/List';
import CharacteristicsList from '../components/Admin/Characteristics/List';
import OrdersList from '../components/Admin/Orders/List';
import AddressesList from '../components/Admin/Addresses/List';
import CategoriesList from '../components/Admin/Categories/List';
import ProductsList from '../components/Admin/Products/List';
import ProductCharacteristicList from '../components/Admin/Products/ProductCharacteristicList';
import SchedulePie from '../components/Admin/Schedule/Pie';
import ScheduleBar from '../components/Admin/Schedule/Bar';
import ScheduleLine from '../components/Admin/Schedule/Line';
import About from '../components/Static/About';
import PayAndGet from '../components/Static/PayAndGet';
import Contact from '../components/Static/Contact';
import Products from '../components/Pages/Products/Products';
import Product from '../components/Pages/Products/Product';
import Posts from '../components/Pages/Posts/Posts';
import Post from '../components/Pages/Posts/Post';
import Tops from '../components/Pages/Tops/Tops';
import Top from '../components/Pages/Tops/Top';
import Sales from '../components/Pages/Sales/Sales';
import Sale from '../components/Pages/Sales/Sale';
import User from '../components/Pages/Users/User';


Vue.use(Router);

let router = new Router({
    routes: [
        {
            name: 'home',
            path: '',
            component: Home
        },
        {
            name: 'order',
            path: '/order',
            component: Order
        },
        {
            name: 'about',
            path: '/about',
            component: About
        },
        {
            name: 'delivery',
            path: '/delivery',
            component: PayAndGet
        },
        {
            name: 'contacts',
            path: '/contacts',
            component: Contact
        },
        {
            name: 'register',
            path: '/register',
            component: Register
        },
        {
            name: 'categories-many',
            path: '/categories/:id?',
            component: Products
        },
        {
            name: 'product',
            path: '/products/:id',
            component: Product
        },
        {
            name: 'pages',
            path: '/pages',
            component: Posts
        },
        {
            name: 'page',
            path: '/pages/:id',
            component: Post
        },
        {
            name: 'tops',
            path: '/tops',
            component: Tops
        },
        {
            name: 'top',
            path: '/tops/:id',
            component: Top
        },
        {
            name: 'sales',
            path: '/sales',
            component: Sales
        },
        {
            name: 'sale',
            path: '/sales/:id',
            component: Sale
        },
        {
            name: 'user',
            path: '/users/:id',
            component: User
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
            name: 'personal',
            path: '/personal',
            component: Personal,
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
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'users',
            path: '/admin/users',
            component: UsersList,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'comments',
            path: '/admin/comments',
            component: CommentsList,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'tags',
            path: '/admin/tags',
            component: TagsList,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'sale-categories',
            path: '/admin/sale-categories',
            component: SaleCategoriesList,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'sales',
            path: '/admin/sales',
            component: SalesList,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'posts',
            path: '/admin/posts',
            component: PostsList,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'characteristics',
            path: '/admin/characteristics',
            component: CharacteristicsList,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'orders',
            path: '/admin/orders',
            component: OrdersList,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'addresses',
            path: '/admin/addresses',
            component: AddressesList,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'categories',
            path: '/admin/categories',
            component: CategoriesList,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'products',
            path: '/admin/products',
            component: ProductsList,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'product-characteristics',
            path: '/admin/product-characteristics',
            component: ProductCharacteristicList,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'schedule-pie',
            path: '/admin/schedule/pie',
            component: SchedulePie,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'schedule-bar',
            path: '/admin/schedule/bar',
            component: ScheduleBar,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            name: 'schedule-line',
            path: '/admin/schedule/line',
            component: ScheduleLine,
            meta: {
                requiredRole: ['Администратор', 'Главный администратор']
            }
        },
        {
            path: '/404',
            name: '404',
            component: NotFound,
        },
        // {
        //     path: '*',
        //     redirect: '/404'
        // }
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