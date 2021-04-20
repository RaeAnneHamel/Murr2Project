// add libraries
import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios'
import VueAxios from 'vue-axios'
import VueEllipseProgress from 'vue-ellipse-progress'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

// add css
import 'bootstrap/dist/css/bootstrap.css'
// import 'bootswatch/dist/sketchy/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css'
import Vuelidate from 'vuelidate'

import MurrMixin from '@/mixins/murr-mixin'

// add libraries to vue context
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.use(VueAxios, axios)
Vue.use(Vuelidate)
Vue.use(VueEllipseProgress)

Vue.config.productionTip = false
Vue.mixin(MurrMixin)
new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
