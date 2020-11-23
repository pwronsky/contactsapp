require('./bootstrap');
import VueRouter from "vue-router";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import router from "./routes";
import Main from "./Main";

window.Vue = require('vue');

Vue.use(VueRouter);
Vue.use(Toast);

const app = new Vue({
  el: '#app',
  router: router,
  components: {
    'contacts-app': Main
  }
});
