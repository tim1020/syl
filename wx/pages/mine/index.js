//mine/index.js
//获取应用实例
const app = getApp()
const poiDao  = require('../../daos/point')
const userDao = require('../../daos/user')
const util = require('../../utils/util')
const conf = require('../../config')

Page({
  data: {
    imgUrl: conf.imgUrl,
    hiddenLoading:true,
    lastX:0,
    userInfo: {
      "avatarUrl":"../../assets/imgs/logo.png",
      "nickName":"请登录"
    },
    hasUserInfo: false,
    canIUse: wx.canIUse('button.open-type.getUserInfo'),
    tabsSelected:'1', 
    signin:{
      total:0,
      cpage:1,
      hasMore:false,
      list:[]
    },
    like:{
      total:0,
      cpage:1,
      hasMore:false,
      list:[]
    }
  },
  onShow: function(){
    //非第一次show,判断是否有更新，如有则清空重新载入第一页
    if(this.data.signin.cpage != 1) {
      poiDao.getMine('signin').then(res => {
        if(res && res.list && res.total != this.data.signin.total) {
          util.dlog('reload my singin')
          let signin = {
            hasMore: res.list.length < res.total ? true : false,
            cpage: 2,
            total: res.total,
            list: res.list
          }
          this.setData({ signin: signin })
        }
      })
    }
    if (this.data.like.cpage != 1) {
      poiDao.getMine('like').then(res => {
        if (res && res.list && res.total != this.data.like.total) {
          util.dlog('reload my like')
          let like = {
            hasMore: res.list.length < res.total ? true : false,
            cpage: 2,
            total: res.total,
            list: res.list
          }
          this.setData({like:like})
        }
      })
    }
  },
  onLoad: function () {
    if (app.globalData.userInfo) {
      this.initData()
    } else if (this.data.canIUse){
      // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
      // 所以此处加入 callback 以防止这种情况
      app.userInfoReadyCallback = res => {
        app.globalData.userInfo = res.userInfo
        this.initData()
      }
    } else {
      // 在没有 open-type=getUserInfo 版本的兼容处理
      wx.getUserInfo({
        success: res => {
          app.globalData.userInfo = res.userInfo
          this.initData()
        }
      })
    }
  },

  //拉到底,加载更多
  onReachBottom: function(){
    let hasMore = this.data.tabsSelected == 1 ? this.data.signin.hasMore : this.data.like.hasMore
    if(!hasMore) {
      wx.showToast({
          title: '已加载全部数据',
          duration: 2000,
          icon:'none'
      });
      return
    }
    this.loadMine()
  },

  //btn调用
  getUserInfo: function(e) {
    app.globalData.userInfo = e.detail.userInfo
    this.initData()
  },
  initData: function() {
    userDao.updateProfile(app.globalData.userInfo)
    this.setData({
      userInfo: app.globalData.userInfo,
      hasUserInfo: true
    })
    this.loadMine('signin')
    this.loadMine('like')
  },
  //加载数据
  loadMine: function(sType = ''){
    wx.showLoading({
      title: '加载中...',
    })
    let cpage = 1
    if(!sType) {
      sType = this.data.tabsSelected == '1' ? 'signin': 'like'
    }
    switch (sType) {
      case 'signin':
        cpage = this.data.signin.cpage
        break
      case 'like':
        cpage = this.data.like.cpage
        break;
    }
    poiDao.getMine(sType, cpage).then(res => {
      let hasMore = res.list.length < res.total ? true : false
      let data = {
        hasMore: hasMore,
        total: res.total
      }
      if(res.list && res.list.length > 0) {
        if (sType == 'signin') {
          let signin = data
          signin.cpage = cpage + 1
          signin.list = this.data.signin.list
          signin.list.push(...res.list)
          this.setData({ hiddenLoading: true, signin: signin })
        } else if (sType == 'like') {
            let like = data
            like.cpage = cpage + 1
            like.list = this.data.like.list
            like.list.push(...res.list)
            this.setData({ hiddenLoading: true, like: like })
        }
      }
      wx.hideLoading()
    })
  },
  switchTab: function(e){
    this.setData({
      tabsSelected: e.target.dataset.index,
    })
  },
  //滑动开始事件
  touchstart: function (e) {
    this.data.lastX = e.touches[0].pageX
  },
  //滑动结束事件
  touchend: function (e) {
    let now = e.changedTouches[0].pageX
    let dist = now - this.data.lastX
    if(Math.abs(dist)> 50) {
      this.setData({
        tabsSelected: this.data.tabsSelected == 1 ? 2 : 1,
      })
    }
  },
  
})
