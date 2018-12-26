//环境变量设置

const mode = 'prod'
//开发环境
const dev = {
	isDebug:true,
  basePath: 'http://localhost:8181/index.php?app_id=&secret=&',
  imgUrl: 'http://localhost:8181/imgs/',
}
//生产环境
const prod = {
  isDebug:true,
	basePath: 'https://syl.fullyoung.vip/index.php?app_id=&secret=&', 
  imgUrl: 'https://img.fullyoung.vip/',
}

//通用配置,会被mode对应的配置覆盖
const common = {
	splash_time:  5000,
	apis:{
		query_splash:   'c=splash-query',
    login:          'c=user-signin',         //登录
    updateProfile:  'c=user-update',        // 更新资料
    get_hotwords:   'c=conf-hotWords',      // 搜索热词
    get_tags:       'c=conf-tags',          // 标签云
    get_newest:     'c=poi-getNewest',      // 获取最新收录
    get_hot:        'c=poi-getHot',         // 获取热门
    get_rand:       'c=poi-getRand',        // 首页随机推荐
    get_detail:     'c=poi-getDetail',      // 获取指定路线详情
    get_stats:      'c=user-queryStats',    // 查询指定路线的用户标记状态
    add_stats:      'c=user-addStats',      // 添加用户标记
    get_mine:       'c=user-getPoi',        // 获取我去过和想去的地点
    poi_search:     'c=poi-search',         // 搜索
	},
	default_hotwords: ['武功山','大南山', '天堂顶'], //缺省的搜索执词
	default_tags: ['五星营地','花海','草甸','最高峰', '夜路传说'], //缺省的标签云
  newest_nums: 5, //最新的数量
  hot_nums: 12,   //热门的数量
  rand_nums: 3,   //随机的数量
  search_nums: 10, //搜索列表每次查询条数
  mine_nums: 10, //我的列表查询条数
}


const config = mode == 'prod' ? prod : dev

module.exports =   {
	mode,
	...common,
	...config
}