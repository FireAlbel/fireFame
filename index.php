<?php
header("Content-type:text/html;charset=utf-8");
define('WEB_ROOT',str_replace('\\','/',dirname(__FILE__)) );//设置根目录常量
define('WEB_LIBRARY',WEB_ROOT.'/Library/');
require WEB_LIBRARY.'Common.php';
init();
