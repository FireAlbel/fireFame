<?php
/**
 * FireFrame common公用基础类
 * @category   Fire
 * @package  Fire
 * @subpackage  Library
 * @author   zhaokun <739576080@qq.com>
 * @time     2014/11/9 
 */
//error_reporting(1);


/*
 * 自动导入类库
 */
function autoload($class){
	if(empty($class)) exit('对不起,没有找到相应的｛$class｝类');
	if(substr($class,-6)=='Action'){
		$filePath=WEB_ACTION.$class.'.php';
		if(file_exists($filePath)){
			require $filePath;
		}else{
			exit('此'.$class.'在common文件auto下不存在');
		}
	}elseif(substr($class,-5)=='Model'){
		$filePath=WEB_MODEL.$class.'.php';
		if(file_exists($filePath)){
			require $filePath;
		}else{
			exit('此'.$class.'不存在！');
		}
	}
	
}
/** 
* 写入日志文件 
* tags 
* @param $logInfo 
* @return boolean 
* @author zhaokun 
* @date 2014-12-16上午11:41:12 
* @version v1.0.0 
*/
function loger($logInfo=''){
	if(empty($logInfo)){
		
	}else{
		
	}
}
/** 
*  获取language配置文件信息
* tags 
* @param $conf string 
* @return string 
* @author zhaokun 
* @date 2014-12-16上午9:50:19 
* @version v1.0.0 
*/
function L($conf){
	if(strpos($conf, '/')){
		list($Group,$cf) = explode('/', $conf);
		if(isset($Group)){
			$language=require WEB_LANGUAGE.'language/'.trim($Group).'/config.php';
			$l=isset($language[$cf])?trim($language[$cf]):'NO found!';
			return $l;
		}
	}else{
		$language=require WEB_LANGUAGE.'config.php';
		$cf=strtoupper($conf);
		$l=isset($language[$cf])?trim($language[$cf]):'NO found!';
		return $l;
	}
}
/*
 * 跳转到指定的模板
* 尚未完成此功能请勿使用
*/
function redirect($url,$group=''){
	if(!empty($url)){
		if(C('URL_MODEL')==1){
			list($control,$action)=explode('/', $url);
			if(empty($group)){
				$url='index.php?c='.trim($control).'&a='.trim($action);
			}else{
				$url='index.php?g='.trim($group).'c='.trim($control).'&a='.trim($action);
			}
			header('Location: ' . $url);
		}elseif(C('URL_MODEL')==2){
			if(empty($group)){
				$url='index.php/'.trim($url);
			}else{
				$url='index.php/'.trim($group).trim($url);
			}
		}
		header('Location: '.$url);
	}else{
		exit('请填写您要跳转的地址，如：home/index');
	}

}
/** 
* hack跳转到错误提示页面（调试模式下）否则跳转到首页 
* tags 
* @param  $err string
* @return void
* @author zhaokun 
* @date 2014-12-16上午9:03:50 
* @version v1.0.0 
*/
function hack($err){
	if(C('debug')==1){
		$template=new Template();
// 		showInfo($err);
		$template->assign($err);
		$template->display('Tpl/default_err');
	}elseif(C('debug')==0){
		loger($err);
	}
}
/*
 * 导入extend文件类库
 */
function import($class){
	$filePath=WEB_EXTEND.$class.'.php';
	if(file_exists($filePath)){
		require $filePath;
	}
}
/** 
* 格式化数据函数 
* tags 
* @param $array
* @return void 
* @author zhaokun 
* @date 2014-12-14下午10:17:23 
* @version v1.0.0 
*/
function showInfo($info){
	echo "<pre>";
	var_dump($info);
	echo "</pre>";
}
/*
 * 开启session
 */
function session(){
	session_start();
}
/*
 * 读取配置文件
*/
function C($config){
	if(strpos($config, '/')){
		list($Group,$cf) = explode('/', $config);
		if(isset($Group)){
			$confPath=WEB_CONFIG.ucfirst($Group).'/config.php';
			$conf=require $confPath;
			$c=isset($conf[$cf])?trim($conf[$cf]):'NO found!';
			return $c;
		}
	}else{
			$confPath=WEB_CONFIG.'config.php';
			$conf=require $confPath;
			$cf=strtoupper($config);
			$c=isset($conf[$cf])?trim($conf[$cf]):'NO found!';
			return $c;
	}
}
/*
 *创建多级目录 
 */
function  Directory( $dir ){
	return   is_dir ( $dir )  or  (Directory(dirname( $dir ))  and   mkdir ( $dir , 0777));
}
/*
 * 初始化框架，引入配置文件和必要文件
* return void
*/
function init(){
	defined('WEB_ROOT') or define('WEB_ROOT',str_replace('\\', '/', dirname(__FILE__)) );
	defined('WEB_LIBRARY') or define('WEB_ROOT',WEB_ROOT.'/Library/');
	defined('WEB_EXTEND') or define('WEB_EXTEND', WEB_ROOT.'/Extend/');
	defined('WEB_ACTION') or define('WEB_ACTION', WEB_ROOT.'/Action/');
	defined('WEB_MODEL') or define('WEB_MODEL',WEB_ROOT.'/Model/');
	defined('WEB_CONFIG') or define('WEB_CONFIG',WEB_ROOT.'/Config/');
	defined('WEB_TEMPLATE') or define('WEB_TEMPLATE',WEB_ROOT.'/Template/');
	defined('WEB_LANGUAGE') or define('WEB_LANGUAGE',WEB_ROOT.'/Language/');
	//set_error_handler($error_handler)
	//框架核心模块文件列表
	$list=array(
			WEB_LIBRARY.'Controller.php',
			WEB_LIBRARY.'Model.php',
			WEB_LIBRARY.'Template.php',
			WEB_LIBRARY.'MyException.php'
	);
	//导入核心模块
	foreach ($list as $val){
		if(empty($val)) exit('导入的文件'.$val.'不存在');
		require_once $val;
	}
	//导入配置文件

	/* date_zone */
	if(function_exists('date_default_timezone_set')) {
		date_default_timezone_set(C('time_zone'));
	}
	//运行总控制器调用action
	Controller::Run();
}