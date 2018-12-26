//search.js
//获取应用实例
const regeneratorRuntime = require('../../libs/regenerator-runtime/runtime')
const WxSearch  = require('../../libs/wxSearch/wxSearch')
const pointDao  = require('../../daos/point')
const searchDao = require('../../daos/search')
const store     = require('../../utils/store')
const util      = require('../../utils/util')
const conf      = require('../../config')

Page({
  data: {
    imgUrl: conf.imgUrl,
    cpage: 1,
    hasMore: true,
    hiddenLoading:true,
    sortSelected: store.get('sort_key') || {},
    sortIcon: {
      like: '',
      recommend: ''
    },
    dataset:[//数据集
    ], 
    tags:[], //标签
    tagsColor:[],
    //滑动的标记记录
    lastY:0,
    lastTs:0,
    timeTick:0 //两次touchEnd的间隔时间
  },
  //分享处理
  onShareAppMessage: function () {
    return { title: '户外徒步登山线路列表' }
  },
  onShow: function() {
    let kw = getApp().globalData.searchKw
    if (kw != '' && this.data.wxSearchData.value != kw) {
      var tmpSearchData = this.data.wxSearchData
      tmpSearchData.value = kw
      this.setData({ wxSearchData: tmpSearchData })
      this.resetData()
      this.loadData()
    }
    //打乱标签云
    if(this.data.tags.length > 0) {
      let tags = this.data.tags
      tags.sort((a,b) => {return Math.random() > 0.5 ? -1: 1})
      this.setData({ tags: tags, tagsColor: util.randColor(tags.length)})
    }
  },
  onLoad: function (options) {
      //设置搜索相关
      WxSearch.init(this, 43, store.get('wx_search_hotwords') || conf.default_hotwords);
      WxSearch.initMindKeys(store.get('wx_search_mindkeys') || []) 
      var tmpSearchData = this.data.wxSearchData
      if (getApp().globalData.searchKw != 'undefined') {
        tmpSearchData.value = getApp().globalData.searchKw
        getApp().globalData.searchKw = ''
        this.setData({ wxSearchData: tmpSearchData })
      } 
      searchDao.getHotWords().then(res => {
        if(res.hotWords) {
          store.set('wx_search_hotwords', res.hotWords)
          tmpSearchData.keys = res.hotWords
        }
        if(res.mindKeys) {
          store.set('wx_search_mindkeys', res.mindKeys)
          tmpSearchData.mindKeys = res.mindKeys
        }
        this.setData({ wxSearchData: tmpSearchData })
      })
      //标签云
      let tags = store.get('index_tags') || conf.default_tags
      this.setData({tags:tags, tagsColor: util.randColor(tags.length)})
      searchDao.getTags().then(res => {
        if(res){
          store.set('index_tags', res)
          this.setData({tags:tags, tagsColor: util.randColor(tags.length)})
        }
      })
      //加载结果数据
       this.loadData()
  },
  //排序设置
  setSortKey: function(k,selected) {
    let icons = {};
    if(selected == 1) {
      icons[k] = 'fa fa-angle-double-down'
    } else if(selected == 2) {
      icons[k] = 'fa fa-angle-double-up'
    } else {
      icons[k] = ''
    }
    let sortKey = {}
    sortKey[k] = selected
    this.setData({ sortIcon: icons })
    store.set('sort_key', sortKey)
    this.data.sortSelected = sortKey
    this.resetData()
  },
  //切换排序
  swSort: function(e) {
    let key = e.target.id
    let selected = this.data.sortSelected[key] == 1 ? 2 : 1
    util.dlog('swsort: key=%s,selected=%s',key,selected)
    this.setSortKey(key, selected)
    this.loadData()
  },
  //加载数据
  loadData: function(){
    if(!this.data.hasMore) {
      wx.showToast({
        title: '没有更多数据',
        duration: 2000,
        icon:'none'
      });
      return
    }
    this.setData({hiddenLoading:false})
    let kw = this.data.wxSearchData.value
    util.dlog('loadData,kw=%s,sortKey=%o,cpage=%s', kw, this.data.sortSelected,this.data.cpage)
    pointDao.search(kw, this.data.sortSelected, this.data.cpage).then(res => {
      let data = {hiddenLoading:true}
      if(res.list){
        let tmpDs = this.data.dataset
        if(res.list.length > 0) {
          tmpDs.push(...res.list)
        }
        this.data.cpage ++
        data.hasMore = res.total > tmpDs.length
        data.dataset = tmpDs
      }
      util.dlog('data=%o',data)
      this.setData(data)
    })
  },
  //标签搜索
  searchTag: function(e) {
    let tag = e.currentTarget.dataset.tag
    var tmpSearchData = this.data.wxSearchData
    tmpSearchData.value = 'tag:'+ tag
    this.setData({wxSearchData:tmpSearchData})
    this.resetData()
    this.loadData()
  },
  //重置相关数据
  resetData: function(){
    this.data.hasMore = true
    this.data.cpage = 1
    this.data.dataset = []
  },
  //滑动开始事件
  touchstart: function (e) {
    this.data.lastY = e.touches[0].pageY
  },
  //滑动结束事件
  touchend: function (e) {
    if(!wx.pageScrollTo) return
    let nowY = e.changedTouches[0].pageX
    let distY = this.data.lastY - nowY
    if(distY > 30) {
      if(e.timeStamp - this.data.lastTs < 600) {
          wx.pageScrollTo({
            scrollTop: 0
          })
      }
      this.data.lastTs = e.timeStamp
    }
  },

  //上拉到底，加载更多
  onReachBottom: function(){
    if(this.data.cpage > 1) {
      this.loadData()
    }
  },
  wxSearchFn: function (e) {
    var that = this
    WxSearch.wxSearchAddHisKey(that);
    this.resetData()
    this.loadData()
  },
  wxSearchInput: function (e) {
    var that = this
    WxSearch.wxSearchInput(e, that);
  },
  wxSerchFocus: function (e) {
    var that = this
    WxSearch.wxSearchFocus(e, that);
  },
  wxSearchBlur: function (e) {
    var that = this
    WxSearch.wxSearchBlur(e, that);
  },
  wxSearchKeyTap: function (e) {
    var that = this
    WxSearch.wxSearchKeyTap(e, that);
  },
  wxSearchDeleteKey: function (e) {
    var that = this
    WxSearch.wxSearchDeleteKey(e, that);
  },
  wxSearchDeleteAll: function (e) {
    var that = this;
    WxSearch.wxSearchDeleteAll(that);
  },
  wxSearchTap: function (e) {
    var that = this
    WxSearch.wxSearchHiddenPancel(that);
    this.wxSearchFn()
  }
})
