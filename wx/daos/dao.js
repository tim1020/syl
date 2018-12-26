const req = require('../utils/request')
const regeneratorRuntime = require('../libs/regenerator-runtime/runtime')
const conf = require('../config')
const util = require('../utils/util')
const store = require('../utils/store')

//发起get请求
const get = async (api) => {
  let url = conf.basePath + api
  let token = store.get('access_token') || ''
  if (token) url += "&access_token="+ token
  return new Promise((resolve, reject) => {
    req.get(url).then(res => {
      util.dlog('url=%s, res=%o', url, res)
      if (res.data.code == 0) {
        resolve(res.data.data)
      } else {
        wx.showToast({
          title: res.data.error,
          icon: 'none',
          duration: 2000
        })
      }
    })
      .catch(err => {
        reject(err)
      })
  })
}

//发起post请求
const post = async (api, data = {}, header = {}) => {
    let url = conf.basePath + api
    let token = store.get('access_token') || ''
    util.dlog('access_token=%s',token)
    if(token) data.access_token = token
    return new Promise((resolve, reject) => {
        req.post(url, data, header).then(res => {
            util.dlog('url=%s,data=%o, res=%o', url, data, res)
            if(res.data.code == 0) {
                resolve(res.data.data)
            } else{
              wx.showToast({
                title: res.data.error,
                icon: 'none',
                duration: 2000
              })
            }
        })
        .catch(err => {
            reject(err)
        })
    })
}

module.exports = {
    post,get
}