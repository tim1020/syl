//网络请求封装
const regeneratorRuntime = require('../libs/regenerator-runtime/runtime')

const get = async (url) => {
    return new Promise((resolve, reject) => {
        wx.request({
            url:url,
            success:resolve,
            fail:reject
        })        
    })
}

const post = (url, data={}, header={}) => {
    header['content-type'] = 'application/json'
    return new Promise((resolve, reject) => {
        wx.request({
            url: url,
            method: 'POST',
            header:header,
            data:data,
            success:resolve,
            fail:reject
        })        
    })
}

module.exports = {
    get,post
}