<?php

header("content-type:text/html;charset=utf-8");
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式
define('APP_DEBUG', true);

// 定义应用目录
define('APP_PATH','./Application/');
// 引入ThinkPHP入口文件1
require './ThinkPHP/ThinkPHP.php';
?>
