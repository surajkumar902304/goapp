import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

// Import components
import AdminNotFound from './admin/AdminNotFound.vue';
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
import HomeRoundBanner from './admin/banner/HomeRoundBanner.vue';
import HomeLargeBanner from './admin/banner/HomeLargeBanner.vue';
import HomeSmallBanner from './admin/banner/HomeSmallBanner.vue';
import HomeExploreDealBanner from './admin/banner/HomeExploreDealBanner.vue';
import NewProductSlider from './admin/banner/NewProductSlider.vue';
import HomeFruitBanner from './admin/banner/HomeFruitBanner.vue';
import TopSellerSlider from './admin/banner/TopSellerSlider.vue';
import HomeSliderHeader from './admin/banner/HomeSliderHeader.vue';
import BrowseBanner from './admin/banner/BrowseBanner.vue';
import LoyaltyRewardBanner from './admin/banner/LoyaltyRewardBanner.vue';
import BankDetail from './admin/setting/BankDetail.vue';
import DeliveryMethod from './admin/setting/DeliveryMethod.vue';
import AdminCoupons from './admin/coupon/AdminCoupons.vue';
import RepList from './admin/rep/RepList.vue';
import OrderList from './admin/order/OrderList.vue';
import OrderDetail from './admin/order/OrderDetail.vue';
import ServiceDisplaySolution from './admin/service/ServiceDisplaySolution.vue';


const routes = [
  { path: '/admin/dashboard', component: AdminDashboard },
  { path: '/admin/customers', component: AdminApproval, name: 'customers-list' },
  { path: '/admin/products', component: AdminProductslist, name: 'product-list' },
  { path: '/admin/product/addview', component: AdminAddProduct, name: 'add-product' },
  { path: '/admin/product/:mproid', component: AdminEditProduct, name: 'edit-product', props: true },
  { path: '/admin/product-offers', component: AdminProductCreateOffer, name: 'product-offers-list' },
  { path: '/admin/product-options', component: AdminMoptions, name: 'options-list' },
  { path: '/admin/brands', component: AdminBrandlist, name: 'brands-list' },
  { path: '/admin/main-categories', component: MainMcatlist, name: 'main-cat-list' },
  { path: '/admin/categories', component: Mcatlist, name: 'cat-list' },
  { path: '/admin/sub-categories', component: Msubcatlist, name: 'subcat-list' },
  { path: '/admin/sub-categories/addview', component: AddSubCategory, name: 'add-subcat' },
  { path: '/admin/sub-categories/:msubcatid', component: EditSubCategory, name: 'edit-subcat', props: route => ({ msubcatid: Number(route.params.msubcatid) }) },
  { path: '/admin/round-sliders', component: HomeRoundBanner, name: 'round-sliders-list' },
  { path: '/admin/big-sliders', component: HomeLargeBanner, name: 'big-sliders-list' },
  { path: '/admin/small-sliders', component: HomeSmallBanner, name: 'small-sliders-list' },
  { path: '/admin/deals-sliders', component: HomeExploreDealBanner, name: 'deals-sliders-list' },
  { path: '/admin/new-product-sliders', component: NewProductSlider, name: 'new-product-sliders-list' },
  { path: '/admin/fruit-sliders', component: HomeFruitBanner, name: 'fruit-sliders-list' },
  { path: '/admin/top-seller-sliders', component: TopSellerSlider, name: 'top-seller-sliders-list' },
  { path: '/admin/slider-headers', component: HomeSliderHeader, name: 'slider-headers-list' },
  { path: '/admin/browse-sliders', component: BrowseBanner, name: 'browse-sliders-list' },
  { path: '/admin/loyalty-reward-banner', component: LoyaltyRewardBanner, name: 'loyalty-reward-banner' },
  { path: '/admin/payment-method', component: BankDetail, name: 'bank-detail-list' },
  { path: '/admin/delivery-method', component: DeliveryMethod, name: 'delivery-method-list' },
  { path: '/admin/coupons', component: AdminCoupons, name: 'coupon-list' },
  { path: '/admin/reps', component: RepList, name: 'reps-list' },
  { path: '/admin/orders', component: OrderList, name: 'order-list' },
  { path: '/admin/orders/:orderid', component: OrderDetail, name: 'order-detail', props: route => ({ orderid: Number(route.params.orderid) }) },
  { path: '/admin/services', component: ServiceDisplaySolution, name: 'service-list' },

  { path: '*', component: AdminNotFound, name: 'AdminNotFound', meta: { layout: 'none' } },
];

export default new VueRouter({
  mode: 'history',
  routes
});
