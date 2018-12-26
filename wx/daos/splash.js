const dao = require('./dao')
const conf = require('../config')
const regeneratorRuntime = require('../libs/regenerator-runtime/runtime')

module.exports = {
  query: async id => {
    try {
      return await dao.post(conf.apis.query_splash, { id: id })
    } catch (e) {
      throw e
    }
  }
}