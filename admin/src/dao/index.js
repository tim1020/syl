
let conf = require('../../static/config')
let axios = require('axios')

const post = (api, data = {}, headers = {}) => {
  let url = conf.api_host + api
  let token = localStorage.getItem('token')
  if (token) {
    data.access_token = token
  }
  return new Promise((resolve, reject) => {
    axios.post(url, data).then(res => {
      resolve(res)
    }, err => {
      reject(err)
    })
  })
}

const postFile = (api, data = {}, headers = {}) => {
  let url = conf.api_host + api
  let body = new FormData()
  let token = localStorage.getItem('token')
  if (token) {
    body.append('access_token', token)
  }
  for (let i in data) {
    if (data[i] instanceof Array) {
      let k = i + '[]'
      for (let e of data[i]) {
        if (e) body.append(k, e)
      }
    } else body.append(i, data[i])
  }
  console.log(body.keys())
  return new Promise((resolve, reject) => {
    axios.post(url, body).then(res => {
      resolve(res)
    }, err => {
      reject(err)
    })
  })
}

export default {
  signin: (user, pass) => {
    return post(conf.apis.signin, {user, pass})
  },
  getHotword: () => {
    return post(conf.apis.hotword_get)
  },
  addHotword: (word) => {
    return post(conf.apis.hotword_add, {word})
  },
  delHotword: (id) => {
    return post(conf.apis.hotword_del, {id})
  },
  getTag: () => {
    return post(conf.apis.tag_get)
  },
  addTag: (tag) => {
    return post(conf.apis.tag_add, {tag})
  },
  delTag: (id) => {
    return post(conf.apis.tag_del, {id})
  },
  lstPoi: (page, size = 5) => {
    return post(conf.apis.poi_lst, {page: page, size: size})
  },
  addPoi: (data = {}) => {
    return postFile(conf.apis.poi_add, data)
  }
}
