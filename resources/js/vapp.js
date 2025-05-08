require('./vbootstrap');

window.Vue = require('vue').default;

// ✅ Toast
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

// ✅ Vuetify
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';

Vue.use(Vuetify);
Vue.use(Toast);

// Dashboard
Vue.component('admin-dashboard', require('./admin/AdminDashboard.vue').default);

// Product
Vue.component('admin-productslist',require('./admin/product/AdminProductslist.vue').default);
Vue.component('admin-addproduct', require('./admin/product/AdminAddproduct.vue').default);
Vue.component('admin-editproduct', require('./admin/product/AdminEditproduct.vue').default);
Vue.component('admin-productcreateoffer', require('./admin/product/ProductCreateOffer.vue').default);

// Option
Vue.component('admin-moptions', require('./admin/option/AdminMoptions.vue').default);

// Brand
Vue.component('admin-brandlist',require('./admin/brand/AdminBrandlist.vue').default);

// Category
Vue.component('admin-mcatlist',require('./admin/category/Mcatlist.vue').default);

// Sub-Category
Vue.component('admin-addsubcat',require('./admin/category/AddSubCategory.vue').default);
Vue.component('admin-msubcatlist',require('./admin/category/Msubcatlist.vue').default);
Vue.component('admin-editmsubcat', require('./admin/category/EditSubCategory.vue').default);

// Banner
Vue.component('admin-browsebanner',require('./admin/banner/BrowseBanner.vue').default);

// User
Vue.component('admin-adminapproval',require('./admin/user/AdminApproval.vue').default);

const app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
})
