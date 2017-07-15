<?php
return array(
	//'配置项'=>'配置值'
    'TMPL_ACTION_ERROR'     =>  THINK_PATH.'Tpl/jump1.tpl', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  THINK_PATH.'Tpl/jump1.tpl', // 默认成功跳转对应的模板文件
    //'SHOW_PAGE_TRACE' => true, // 页面底部追踪信息
    //配置数据库
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'OnlineOrderingSystem',
    'DB_USER' => 'root',
    'DB_PWD' => '123456',
    'DB_PREFIX' => 'yd_',
    'DB_CHARSET' => 'utf8',
    'DB_PORT' => '3306',
    'TMPL_L_DELIM' => '<{', // 模板引擎普通标签开始标记
    'TMPL_R_DELIM' => '}>', // 模板引擎普通标签结束标记
    'ROOT_SITE' => 'www.ydors.com',
    //MD5时用来复杂化的
    'MD5_KEY' => 'ydors',
    'DEFAULT_FILTER' => 'trim,removeXSS',// 过滤方法 用于I函数...
    //发送邮件配置
    'MAIL_ADDRESS' => 'jiang1273398724@163.com',   // 发件人的email
    'MAIL_FROM' => 'www.ydors.com',      // 发件人姓名
    'MAIL_SMTP' => 'smtp.163.com',      // 邮件服务器的地址
    'MAIL_LOGINNAME' => 'jiang1273398724@163.com',
    'MAIL_PASSWORD' => 'jxs19941007',
    // 文件上传参数
    'IMG_ROOTPATH' => './Public/Upload/',
    'IMG_MAX_SIZE' => '20M',
    'IMG_EXT' => array(
        'jpg',
        'png',
        'jpeg',
        'bmp',
        'gif'
    ),
);