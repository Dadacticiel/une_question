import App from "./App";
import Vue from "vue";
window.Vue = Vue;

new Vue({
    components: { App },
    template: "<App/>"
}).$mount("#app");