// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import { AlertPlugin, LoadingPlugin, ConfirmPlugin, ToastPlugin } from 'vux'
import router from './router'
import FastClick from 'fastclick'
require('./dao/interceptors')

Vue.use(AlertPlugin)
Vue.use(LoadingPlugin)
Vue.use(ConfirmPlugin)
Vue.use(ToastPlugin)

FastClick.attach(document.body)
Vue.config.productionTip = false
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})
