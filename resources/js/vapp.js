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
Vue.component('admin-brandlist',require('./admin/AdminBrandlist.vue').default);
Vue.component('admin-addsubcat',require('./admin/category/AddSubCategory.vue').default);
Vue.component('admin-mcatlist',require('./admin/category/Mcatlist.vue').default);
Vue.component('admin-msubcatlist',require('./admin/category/Msubcatlist.vue').default);

// Banner
Vue.component('admin-browsebanner',require('./admin/banner/BrowseBanner.vue').default);


const app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
})
