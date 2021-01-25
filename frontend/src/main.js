// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import App from './App';
import { router } from './router';
import axios from 'axios';
import { store } from './store/store';

import VueProgressBar from 'vue-progressbar';
import VueGoogleCharts from 'vue-google-charts';

import VueI18n from 'vue-i18n';
import { ENGLISH_TRANSLATIONS } from './translations/en';
import { PASHTO_TRANSLATIONS } from './translations/ps';
import { DARI_TRANSLATIONS } from './translations/da';


Vue.prototype.moment = window.moment;

Vue.use(VueI18n);

Vue.use(VueGoogleCharts);

Vue.use(VueProgressBar, {
  color: '#01579B',
  failedColor: 'red',
  height: '2px', thickness: '6px',

});



const TRANSLATIONS = {
  en: ENGLISH_TRANSLATIONS,
  ps: PASHTO_TRANSLATIONS,
  da: DARI_TRANSLATIONS,
};

const i18n = new VueI18n({
  locale: 'en',
  messages: TRANSLATIONS,
});



// global configuration for axios
// Main API URL
axios.defaults.baseURL = 'http://127.0.0.1:8000/mobile';

// EMIS Integration API URL
axios.defaults.emisURL = 'http://localhost:54916/api';


axios.defaults.headers.post['Content-Type'] = 'application/json';



// Global request interceptor
axios.interceptors.request.use(function (config) {
  vm.$Progress.start();

  return config;
}, function (error) {
  vm.$Progress.fail();
  return Promise.reject(error);
});

// Global response interceptor
axios.interceptors.response.use(function (response) {
  vm.$Progress.finish();
  return response;
}, function (error) {
  vm.$Progress.fail();
  return Promise.reject(error);
});




// check if logged in
router.beforeEach((to, from, next) => {
  if (to.path != '/login') {
    if (!store.getters.userObj) {
      router.push("/login");
    }

    if (to.path.search("/admin") != -1) {
      if (!store.getters.userObj.admin) {
        router.push("/login");
        next(false);
      }
    }
  }
  next(true);
});

// print out from/to route names
router.afterEach((to, from) => {
  console.log("Navigated from " + from.fullPath + " to " + to.fullPath);
});


Vue.prototype.$http = axios;
Vue.config.productionTip = false;

// Add this code for 'mounted' life cycle hook of every component
Vue.mixin({
  mounted: function () {
    console.log("Main: Initializing MaterializeCSS Components.");
    M.AutoInit();
  }
});




/* eslint-disable no-new */
var vm = new Vue({
  el: '#app',
  store,
  router,
  i18n,
  components: { App },
  template: '<App/>'
});
