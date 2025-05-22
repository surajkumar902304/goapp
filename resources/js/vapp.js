require('./vbootstrap');

import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuetify from 'vuetify';
import Toast from 'vue-toastification';

import 'vuetify/dist/vuetify.min.css';
import 'vue-toastification/dist/index.css';
import '@mdi/font/css/materialdesignicons.css';

// Import router & root component
import App from './App.vue';
import router from './router';

Vue.use(VueRouter);
Vue.use(Vuetify);
Vue.use(Toast);

const vuetify = new Vuetify({
  icons: {
    iconfont: 'mdi',
  }
});

// Mount the Vue App
new Vue({
  el: '#app',
  router,
  vuetify,
  render: h => h(App)
});
