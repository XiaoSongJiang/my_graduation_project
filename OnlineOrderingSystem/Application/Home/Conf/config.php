<?php
return array(
	'HTML_CACHE_ON'     =>    true, // 开启静态缓存
	'HTML_CACHE_TIME'   =>    60,   // 全局静态缓存有效期（秒）
	'HTML_FILE_SUFFIX'  =>    '.html', // 设置静态缓存文件后缀
	// 可以为前台的每个页面单独配置
	'HTML_CACHE_RULES'  =>     array(
		'Index:index' => array('index', 3600), // 为首页生成一个1小时的缓存页面，页面名叫index.html
		'Index:foods' => array("{foods_id|foodsdir}/foods_{foods_id}",3600),
		'Member:login' => array("Member/login",3600),
		'Member:register' => array("Member/register",3600),
		
	)
    
);
/**
 * 
 * @param int  $id  $_GET['id']
 * @return number
 */
function foodsdir($foods_id){
    return ceil($foods_id/50);
}