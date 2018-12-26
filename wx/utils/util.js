//通用工具函数
const conf = require('../config')

//当前时间戳
const unixtime = (ms = false) => {
  let ts = Date.parse(new Date())
  if(!ms) {
    ts = ts / 1000
  }
  return ts
}
//debug log
const dlog = (format, ...msg) => {
  if(conf.isDebug){
    console.log('[debug] '+format, ...msg)
  }
}

//错误处理
const showErr = (msg,back=false) => {
 // wx.navigateTo({url:'/pages/about/error?msg='+msg})
  wx.showModal({
      content: msg,
      showCancel: false,
      success: function (res) {
          if(back) {
            wx.navigateBack()
          }
      }
  });
}
//标签云颜色
const randColor = (nums) => {
  let colorArr = ["#EE2C2C", "#ff7070", "#4876FF", "#ff6100", "#7DC67D", 
                  "#E17572", "#7898AA", "#C35CFF", "#33BCBA", "#C28F5C",
                  "#FF8533", "#6E6E6E", "#428BCA", "#5cb85c", "#FF674F", 
                  "#E9967A","#66CDAA", "#00CED1", "#9F79EE", "#CD3333", 
                  "#32CD32","#00BFFF", "#68A2D5", "#FF69B4", "#DB7093", 
                  "#CD3278", "#607B8B"]
  let randomColorArr = []
  for(let i=0; i < nums; i++) {
    let random = colorArr[Math.floor(Math.random() * colorArr.length)];
    randomColorArr.push(random);
  }
  return randomColorArr
}

module.exports = {
  dlog,unixtime,showErr,randColor
}
