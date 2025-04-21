require('./vbootstrap');

window.Vue = require('vue').default;

import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';

Vue.use(Vuetify);

Vue.component('admin-dashboard', require('./admin/AdminDashboard.vue').default);
Vue.component('admin-productslist',require('./admin/AdminProductslist.vue').default);
Vue.component('admin-addproduct', require('./admin/AdminAddproduct.vue').default);
Vue.component('admin-editproduct', require('./admin/AdminEditproduct.vue').default);
Vue.component('admin-moptions', require('./admin/AdminMoptions.vue').default);
Vue.component('admin-shopslist',require('./admin/AdminShopslist.vue').default);
Vue.component('admin-mcatlist',require('./admin/AdminMcatlist.vue').default);
Vue.component('admin-addmcat',require('./admin/AdminAddmcat.vue').default);


const app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
})
