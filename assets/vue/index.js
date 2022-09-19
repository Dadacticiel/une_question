import App from "./App";
import Vue from "vue";
import vuetify from '../plugins/vuetify';
import VueResource from 'vue-resource';

window.Vue = Vue;
Vue.use(VueResource);

new Vue({
    vuetify,
    VueResource,
    components: { App },
    template: "<App/>"
}).$mount("#app");