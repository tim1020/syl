const dao = require('./dao')
const regeneratorRuntime = require('../libs/regenerator-runtime/runtime')
const conf = require('../config')

module.exports = {
    login: async code => {
      try{
        return await dao.post(conf.apis.login, {code:code})
      }catch(e) {
        throw e
      }
    },
    updateProfile: async(userInfo) => {
      try {
        return await dao.post(conf.apis.updateProfile, userInfo)
      } catch (e) {
        throw e
      }
    }
}