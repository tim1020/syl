const dao = require('./dao')
const conf = require('../config')
const regeneratorRuntime = require('../libs/regenerator-runtime/runtime')

module.exports = {
    //搜索
    search: async (kw, sortKey, page) => {
        try {
          return dao.post(conf.apis.poi_search, {kw:kw,sort:sortKey, page:page,size: conf.search_nums })
        } catch (e) {
          throw e
        }
    },
    //获取地点详情
    getDetail: async (poiId) => {
      try {
        return dao.post(conf.apis.get_detail, { poi_id: poiId })
      } catch (e) {
        throw e
      }
    },
    //获取当前用户对指定地点的标记
    getStats: async (poiId) => {
      try {
        return dao.post(conf.apis.get_stats, { poi_id: poiId })
      } catch (e) {
        throw e
      }
    },
    //设置地点标记
    setStats: async (poiId, sType) => {
      try {
        return dao.post(conf.apis.add_stats, { poi_id: poiId, type:sType })
      } catch (e) {
        throw e
      }
    },
    //获取我的
    getMine: async (sType, page = 1) => {
      try {
        return dao.post(conf.apis.get_mine, {type: sType, page:page, size:conf.mine_nums })
      } catch (e) {
        throw e
      }
    },
    //首页-最新收录
    getNewest: async () => {
      try {
        return dao.post(conf.apis.get_newest, { nums: conf.newest_nums})
      } catch (e) {
        throw e
      }
    },
    //首页-最热门
    getHot: async () => {
      try {
        return dao.post(conf.apis.get_hot, { nums: conf.hot_nums })
      } catch (e) {
        throw e
      }
    },
    //首页-页头 随机推荐
    getRand: async () => {
      try {
        return dao.post(conf.apis.get_rand, { nums: conf.rand_nums })
      } catch (e) {
        throw e
      }
    }
}