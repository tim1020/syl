import axios from 'axios'
import Vue from 'vue'
import router from '../router'
import conf from '../../static/config'

axios.defaults.timeout = conf.api_timeout
axios.defaults.retry = 4
axios.defaults.retryDelay = 3000

axios.interceptors.request.use(conf => {
  console.log('axios req, conf=', conf)
  return conf
}, error => {
  return Promise.reject(error)
})

axios.interceptors.response.use(res => {
  console.log('res', res)
  let code = res.data.code || 0
  if (code === 0) {
    return res.data.data
  } else {
    if (code === 2) {
      Vue.$vux.alert.show({
        title: '请求失败',
        content: '登录状态失效，请重新登录',
        onHide () {
          router.replace('/auth')
        }
      })
    } else {
      Vue.$vux.alert.show({
        title: '请求失败',
        content: res.data.error || '服务器无正常响应'
      })
    }
    return false
  }
}, error => {
  let msg = error
  if (error.response) {
    msg = 'HTTP ' + error.response.status + ' ' + error.response.statusText
  } else if (error.code) {
    msg = error.code
  }
  Vue.$vux.alert.show({
    title: '请求失败!!',
    content: msg
  })
  return Promise.reject(error)
})
