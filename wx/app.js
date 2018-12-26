//app.js
const regeneratorRuntime = require('./libs/regenerator-runtime/runtime')
const util = require('./utils/util')
const userDao = require('./daos/user')
const store = require('./utils/store')

App({
  onLaunch: function () {
    if(!this.globalData.token) {
      wx.login({ //进行登录，用code换取token(后台用code换取openid，并与本地用户id绑定，再生成对应的token返回)
        success: res => { 
          userDao.login(res.code)
          .then(data => { //登录成功，保存token
            util.dlog('token=%s',data.token)
            this.globalData.token = data.token
            store.set('access_token', data.token)
            //更新用户资料
            wx.getSetting({
              success: res => {
                if (res.authSetting['scope.userInfo']) {
                  // 已经授权，可以直接调用 getUserInfo 获取头像昵称，不会弹框
                  wx.getUserInfo({
                    success: res => {
                      this.globalData.userInfo = res.userInfo
                      util.dlog('userinfo=%o', res.userInfo)
                      userDao.updateProfile(res.userInfo)
                      // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
                      // 所以此处加入 callback 以防止这种情况
                      if (this.userInfoReadyCallback) {
                        this.userInfoReadyCallback(res)
                      }
                    }
                  })
                }
              }
            })
          })
        }
      })
    }
  },
  onError: function (e){
    util.dlog('onError,e=%o',e)
    // wx.showToast({
    //   title:e
    // })
  },
  //全局数据
  globalData: {
    token: null,   //后端的会话标识
    userInfo: null,//wx.getUserInfo获得的用户信息
  }
})