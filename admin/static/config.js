//配置项
module.exports = {
	api_host: 'http://localhost:8181/index.php?app_id=&secret=&',
	api_timeout: 5000,
	apis: {
		signin: 		'c=admin-signin',
		//标签管理
		tag_get: 		'c=admin-tag',
		tag_add: 		'c=admin-addTag',
		tag_del:		'c=admin-delTag',
		//热词管理
		hotword_get: 	'c=admin-hotword',
		hotword_add: 	'c=admin-addHotword',
		hotword_del:	'c=admin-delHotword',
		//poi
		poi_lst: 		'c=admin-lstPoi',
		poi_add:		'c=admin-addPoi',
		poi_detail:		'c=admin-detailPoi',
		poi_edit: 		'c=admin-editPoi'    
	}
}