/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
 
require('./bootstrap')

window.Vue = require('vue')

window.Event = new Vue()

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('home-page', require('./components/home-page.vue').default)
Vue.component('nav-menu', require('./components/header/nav-menu.vue').default)
Vue.component('profile-page', require('./components/profile-page.vue').default)

Vue.component('read-page', require('./components/read-page.vue').default)
Vue.component('write-page', require('./components/write-page.vue').default)
Vue.component('cart-summary-page', require('./components/cart-summary-page.vue').default)
Vue.component('book-page', require('./components/book/index.vue').default)
Vue.component('landing-page', require('./components/index.vue').default)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import locale from 'element-ui/lib/locale/lang/en'
import mixins from './mixins/mixins.js'

Vue.use(ElementUI, { locale })
Vue.mixin(mixins)

const app = new Vue({
    el: '#app',
    created(){
		this.initial_log_messages.forEach((arg) => console.log(arg.msg, arg.style) )
    },
})