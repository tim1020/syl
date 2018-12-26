import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

const router = new Router({
  routes: [
    {
      path: '/',
      redirect: '/tags'
    },
    {
      path: '/auth', // 认证页
      name: 'auth',
      component: resolve => require(['@/views/auth.vue'], resolve)
    },
    {
      path: '/tags',
      name: 'tags',
      component: resolve => require(['@/views/tags.vue'], resolve)
    },
    {
      path: '/pois_add',
      name: 'pois_add',
      component: resolve => require(['@/views/pois_add.vue'], resolve)
    },
    {
      path: '/pois_list',
      name: 'pois_list',
      component: resolve => require(['@/views/pois_list.vue'], resolve)
    },
    {
      path: '*',
      redirect: '/tags'
    }
  ]
})

router.beforeEach((to, from, next) => {
  let logined = localStorage.getItem('token')
  if (!logined && to.name !== 'auth') {
    next('/auth')
  } else {
    next()
  }
})

router.afterEach(page => {
})

export default router
