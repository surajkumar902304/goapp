import Vue       from 'vue'
import VueRouter from 'vue-router'
import Vuetify   from 'vuetify'
import Toast     from 'vue-toastification'

import 'vuetify/dist/vuetify.min.css'
import 'vue-toastification/dist/index.css'
import '@mdi/font/css/materialdesignicons.css'

import CustomerApp from './App.vue'  
import router      from './router'   

Vue.use(VueRouter)
Vue.use(Vuetify)
Vue.use(Toast)

const vuetify = new Vuetify({ icons: { iconfont: 'mdi' } })

new Vue({
  el: '#customerapp',   
  router,
  vuetify,
  render: h => h(CustomerApp)
})
