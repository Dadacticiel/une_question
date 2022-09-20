import App from "./App";
import Vue from "vue";
import vuetify from '../plugins/vuetify';
import VueResource from 'vue-resource';
import VueRouter from 'vue-router';
import Messages from "./Messages";

window.Vue = Vue;
Vue.use(VueResource);

// 1. Define route components.
// These can be imported from other files

// 2. Define some routes
// Each route should map to a component.
// We'll talk about nested routes later.
const routes = [
    { path: '/', component: Messages },
    { path: '/about', component: Messages },
]

// 3. Create the router instance and pass the `routes` option
// You can pass in additional options here, but let's
// keep it simple for now.
const router = new VueRouter({
    routes // short for `routes: routes`
})
Vue.use(VueRouter);

import VueAos from 'vue-aos'
Vue.use(VueAos);

import QrcodeVue from 'qrcode.vue'
Vue.use(QrcodeVue);

new Vue({
    router,
    vuetify,
    VueResource,
    components: { App },
    template: "<App/>"
}).$mount("#app");