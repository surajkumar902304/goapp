import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

// Import components
import AdminDashboard from './admin/AdminDashboard.vue';
import AdminApproval from './admin/user/AdminApproval.vue';
import AdminProductslist from './admin/product/AdminProductslist.vue';
import AdminAddProduct from './admin/product/AdminAddproduct.vue';
import AdminEditProduct from './admin/product/AdminEditproduct.vue';
import AdminProductCreateOffer from './admin/product/ProductCreateOffer.vue';
import AdminMoptions from './admin/option/AdminMoptions.vue';
import AdminBrandlist from './admin/brand/AdminBrandlist.vue';
import MainMcatlist from './admin/category/MainMcatlist.vue';
import Mcatlist from './admin/category/Mcatlist.vue';
import Msubcatlist from './admin/category/Msubcatlist.vue';
import AddSubCategory from './admin/category/AddSubCategory.vue';
import EditSubCategory from './admin/category/EditSubCategory.vue';
// import HomeRoundBanner from './admin/banner/HomeRoundBanner.vue';
// import HomeLargeBanner from './admin/banner/HomeLargeBanner.vue';
// import HomeSmallBanner from './admin/banner/HomeSmallBanner.vue';
// import HomeExploreDealBanner from './admin/banner/HomeExploreDealBanner.vue';
// import HomeFruitBanner from './admin/banner/HomeFruitBanner.vue';
import BrowseBanner from './admin/banner/BrowseBanner.vue';


const routes = [
  { path: '/admin/dashboard', component: AdminDashboard },
  { path: '/admin/customers', component: AdminApproval, name: 'customers-list' },
  { path: '/admin/products/list', component: AdminProductslist, name: 'product-list' },
  { path: '/admin/product/addview', component: AdminAddProduct, name: 'add-product' },
  { path: '/admin/product/:mproid', component: AdminEditProduct, name: 'edit-product' },
  { path: '/admin/product-offers/list', component: AdminProductCreateOffer, name: 'product-offers-list' },
  { path: '/admin/product-options/list', component: AdminMoptions, name: 'options-list' },
  { path: '/admin/brands/list', component: AdminBrandlist, name: 'brands-list' },
  { path: '/admin/main-categories/list', component: MainMcatlist, name: 'main-cat-list' },
  { path: '/admin/categories/list', component: Mcatlist, name: 'cat-list' },
  { path: '/admin/sub-categories/list', component: Msubcatlist, name: 'subcat-list' },
  { path: '/admin/sub-categories/addview', component: AddSubCategory, name: 'add-subcat' },
  { path: '/admin/sub-categories/:msubcatid', component: EditSubCategory, name: 'edit-subcat' },
  // { path: '/admin/round-sliders', component: HomeRoundBanner, name: 'round-sliders-list' },
  // { path: '/admin/big-sliders', component: HomeLargeBanner, name: 'big-sliders-list' },
  // { path: '/admin/small-sliders', component: HomeSmallBanner, name: 'small-sliders-list' },
  // { path: '/admin/deals-sliders', component: HomeExploreDealBanner, name: 'deals-sliders-list' },
  // { path: '/admin/fruit-sliders', component: HomeFruitBanner, name: 'fruit-sliders-list' },
  { path: '/admin/browse-sliders', component: BrowseBanner, name: 'browse-sliders-list' },

  // Add more as needed
];

export default new VueRouter({
  mode: 'history',
  routes
});
