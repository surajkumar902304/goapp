
import Vue        from 'vue';
import VueRouter  from 'vue-router';

Vue.use(VueRouter);

import CustomerNotFound   from './pages/CustomerNotFound.vue';
import CustomerDashboard  from './pages/CustomerDashboard.vue';
import CustomerReps       from './pages/CustomerReps.vue';
import CustomerCommission from './pages/CustomerCommission.vue';

const routes = [
  { path: '/dashboard',   component: CustomerDashboard, name: 'customer.dashboard' },
  { path: '/list',        component: CustomerReps,       name: 'customer.reps'      },
  { path: '/commission',  component: CustomerCommission, name: 'customer.commission'},

  { path: '*', component: CustomerNotFound, name: 'CustomerNotFound', meta: { layout: 'none' } },
]


export default new VueRouter({
  mode : 'history',
  base : '/rep', 
  routes
})
