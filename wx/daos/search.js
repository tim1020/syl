//搜索相关
const dao = require('./dao')
const conf = require('../config')
const regeneratorRuntime = require('../libs/regenerator-runtime/runtime')

module.exports = {
  getHotWords: () => {
    try {
      return dao.post(conf.apis.get_hotwords)
    } catch (e) {
      throw e
    }
  },
  getTags: async () => {
    try {
      return dao.post(conf.apis.get_tags)
    } catch (e) {
      throw e
    }
  }
}