// pages/list/detail.js
const app = getApp()
const poiDao  = require('../../daos/point')
const userDao = require('../../daos/user')
const util = require('../../utils/util')
const conf = require('../../config')

Page({
  data: {
    imgUrl: conf.imgUrl,
    showModal:false,
    currentType: null, //记录授权前的当前操作
    poiId: null,  //地点ID
    poi: {},
    uStats:{
      signin: false,
      like: false,
      recommend: false
    },
    tagsColor:[], //标签色
    relatedColor:[],//相关推荐颜色,
    gallery:{ 
      src: '',
      total:0,
      pos:1 //当前放大第几张
    }
  },
  onLoad: function (options) {
    if(!options.id) {
      util.showErr('非法访问：未指定id', true)
      return
    }
    this.data.poiId = options.id
    //wx.setNavigationBarTitle({title:''}) //设置导航栏标题
    this.loadData(options.id)
  },
  //下拉刷新
  onPullDownRefresh: function(e) {
    this.loadData()
    wx.stopPullDownRefresh()
  },
  //滑动开始事件
  touchstart: function (e) {
    this.data.lastX = e.touches[0].pageX
  },
  //滑动结束事件, 控制gallery切换
  touchend: function (e) {
    let now = e.changedTouches[0].pageX
    let dist = now - this.data.lastX
    if(Math.abs(dist)> 50) { //滑动返回
      if(now < this.data.lastX) { //向右划，下一张
        if(this.data.gallery.pos < this.data.gallery.total)             this.data.gallery.pos ++
        else this.data.gallery.pos = 1
      } else if(this.data.lastX < now ) { //向左,上一张
        if(this.data.gallery.pos > 1) this.data.gallery.pos --
        else this.data.gallery.pos = this.data.gallery.total
      }
      this.swGallery()
    }
  },
  //分享处理
  onShareAppMessage: function () {
    return {
      title: '为你推荐好玩路线：' + this.data.poi.title,
      imageUrl: conf.imgUrl + this.data.poi.cover,
      path:'/pages/index/index?detail_id=' + this.data.poiId
    }
  },
  //加载数据
  loadData: function(poiId = '') {
    wx.showLoading({
      title: '加载中',
    })
    //有poiId，查询指定，没有则后端随机
    poiDao.getDetail(poiId).then(res => {  //res返回详情及 点赞等数据
      this.data.poiId = res.id
      let g_len = res.photos ? res.photos.length : 0
      let t_len = res.tags ? res.tags.length : 0
      this.setData({
        poi:res, 
        gallery:{
          total: g_len,
          indx:0
        },
        tagsColor: util.randColor(t_len),
        //relatedColor: util.randColor(res.related.length)
      })
      wx.hideLoading()
      //查询当前用户的相关操作
      poiDao.getStats(res.id).then(res => {
        this.setData({uStats:res})
      })
    })
  },
  toSearch: function(e) {
    let tag = e.currentTarget.dataset.tag
    getApp().globalData.searchKw = 'tag:'+tag
    wx.switchTab({
      url: '/pages/list/search'
    })
  },
  swGallery: function(pos = '') {
    if(!pos) {
      pos = this.data.gallery.pos
    }
    let url = this.data.poi.photos[pos - 1]
    this.setData({
      showGallery:true,
      gallery: {
        src: url,
        total: this.data.gallery.total,
        pos : pos
      }
    })
  },
  openGallery: function(e){
    let pos = e.currentTarget.dataset.idx
    this.swGallery(pos)
  },
  closeGallery: function(e){
    this.setData({
      showGallery:false
    })
  },
  //弹出蒙板时防止事件穿透
  preventTouchMove:function(){
    return;
  },
  //pick
  pickState: function(e){
    let sType = e.currentTarget.dataset.type
    let picked = false;
    switch(sType) {
      case 'signin':
        picked = this.data.uStats.signin
        break;
      case 'like':
        picked = this.data.uStats.like
        break;
      case 'recommend':
        picked = this.data.uStats.recommend
        break;
      default:
        return
    }
    util.dlog('type=%s,picked=%s',sType,picked)
    if(picked){ 
      wx.showToast({
          title: '你已提交过此操作',
          duration: 2000,
          icon:'none'
      });
      return
    }
    //设置当前操作
    this.data.currentType = sType
    if(!app.globalData.userInfo) { //未授权，跳到授权，并在回调中完成处理
      this.setData({showModal:true})
    } else { 
      util.dlog('userinfo=%s',app.globalData.userInfo)
      this.setStats()
    }
  },
  //更新
  setStats: function() {
    poiDao.setStats(this.data.poiId, this.data.currentType)
    .then(res => { //返回 uState对象
      let poi = this.data.poi
      poi.signin = res.pick_stats.signin
      poi.like = res.pick_stats.like
      poi.recommend = res.pick_stats.recommend
      this.setData({uStats: res.user_stats, poi: poi})
      wx.showToast({
          title: '操作已完成，感谢支持',
          duration: 2000,
          icon:'none'
      });
    })
    .catch(e => { //服务器返回错误的处理

    })
  },
  //授权回调获取用户资料
  getUserInfo: function(e) {
    if(e.detail.errMsg == 'getUserInfo:ok'){
      app.globalData.userInfo = e.detail.userInfo
      userDao.updateProfile(e.detail.userInfo)
      this.setStats()
    }
  },
  hiddenModal: function(e){
    this.setData({showModal:false})
  }
})