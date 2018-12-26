/**
 * 开机闪屏，作为第一个page
 * 
 * 先从store取出splash，如果有，则进入判断显示流程
 *  1. 时间符合(某天，某天之前)
 *  2. 显示次数(一天最多一次，全局一次)
 * 判断完成后，进入异步更新的流程，请求后端获取splash存入store
 */

const util = require('../../utils/util')
const store = require('../../utils/store')
const splashDao   = require('../../daos/splash')
const conf = require('../../config')

Page({
    data: {
        delay: 0,
        imgData: ''
    },
    onLoad() {
        let splash = store.get('splash') // {id, conf, record, data}
        let id    = splash.id || 1
        this.updateSplash(id)
        util.dlog('store get splash:',splash)
        if(splash && splash.conf && splash.data) {
            let now = util.unixtime()
            let timeSpan = splash.conf.timeSpan || {min:0, max: now + 86400 * 100} //时间跨度判断
            let max = splash.conf.max || 1 //显示次数
            let record = splash.record || []
            let times = record.length

            let last = times > 0 ? record[times-1] : 0
            util.dlog('timespan.min=%d,timespan.max=%d,max=%d,times=%s,last=%s', timeSpan.min, timeSpan.max,max,times,last)

            if(now > timeSpan.min && now < timeSpan.max && times < max){ //在设置的时间跨度内，且显示次数未超出设置
                if(now - last > 86400 ) { //上次显示已在一天前
                    this.delay = conf.splash_time || 5000
                    //预加载首页内容
                    this.updateIndex()
                    this.setData({
                        imgData: splash.data
                    })
                    record.push(now)
                    splash.record = record
                    store.set('splash', splash)
                }
            }
        }

        let timer = setTimeout(() => {
            clearTimeout(timer)
            wx.switchTab({url: '/pages/index/index'})
        },  this.delay)
    },

    //异步请求服务器更新splash
    updateSplash(id){
        splashDao.query(id).then((res) => {
            if(res.data) {
                store.set('splash',res.data)
            } 
            util.dlog('updateSplash end,res',res)
        }).catch(e=>{util.dlog('err',e)})
        util.dlog('updateSplash begin')
    },
    //TODO:异步加载首页，加快载入速度
    updateIndex(){
        util.dlog('updateIndex begin')
        //根据配置，调用相应的dao，更新到store

    }
})