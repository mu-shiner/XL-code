<?php

// [ 应用入口文件 ]
// 检测PHP环境  允许前端跨域请求
header("Access-Control-Allow-Origin:*");
// 响应类型
header('Access-Control-Allow-Methods:GET, POST, PUT, DELETE');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with, content-type');


// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
