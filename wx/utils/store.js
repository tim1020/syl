/**
 * 数据本地缓存封装
 */
const util = require('./util.js')

//获取数据
const get = (k) => {
    let data = wx.getStorageSync(k) || null
    return data
}

//保存数据
const set = (k, v) => {
    try {
        wx.setStorageSync(k, v)
    } catch (e) {

    }
}


module.exports = {
    get,set
}