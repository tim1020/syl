//index.js
const conf = require('../../config')
const regeneratorRuntime = require('../../libs/regenerator-runtime/runtime')
const util       = require('../../utils/util')
const store      = require('../../utils/store')

const searchDao  = require('../../daos/search')
const poiDao     = require('../../daos/point')

Page({
  data: {
    imgUrl: conf.imgUrl,
    rand_list:[], //随机(顶部轮播)
    new_list:[],  //最新
    hot_list:[],  //热门
    tags:[], //热词
    tagsColor:[]
  },

  //分享处理
  onShareAppMessage: function () {
    return {title:'户外徒步登山线路集合'}
  },

  onLoad: function (options) {
    if (options.detail_id){
      wx.navigateTo({
        url: '/pages/list/detail?id=' + options.detail_id,
      })
    }
    wx.showLoading({
      title: '加载中',
    })
    let tags = store.get('index_tags') || conf.default_tags
    util.dlog('get tags, store=%s, conf=%s', store.get('index_tags'), conf.default_tags)
    this.setData({ tags: tags, tagsColor: util.randColor(tags.length) })
    searchDao.getTags().then(res => {
      if (res) {
        store.set('index_tags', res)
        this.setData({ tags: res, tagsColor: util.randColor(res.length) })
      }
    })

    //更新最新内容
    poiDao.getNewest().then(res => {
      this.setData({new_list: res})
      wx.hideLoading()
    })
    //更新随机
    poiDao.getRand().then(res => {
      this.setData({rand_list: res})
      wx.hideLoading()
    })
    //热门
    poiDao.getHot().then(res => {
      this.setData({ hot_list: res })
    })
  },
  //热词搜索
  toSearch: function(e) {
    let words = e.currentTarget.dataset.words
    getApp().globalData.searchKw = "tag:"+words
    wx.switchTab({
      url: '/pages/list/search'
    })
  }
})
