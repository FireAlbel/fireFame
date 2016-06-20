<?php
/**
 * FireFrame Action控制器基类 抽象类
 * @category   Fire
 * @package  Fire
 * @subpackage  Library
 * @author   zhaokun <739576080@qq.com>
 * @time     2014/11/9 
 */

class Controller{
	private $template = NULL;
	private $_val=array();
	private static $group=NULL;
	private static $control=NULL;
	private static $action=NULL;
	public function  __construct(){
		
		
	}	

	/*
	 * 执行由Analysis 获取的控制器和action
	 * void Return
	 */
	public static function Run(){

		//判断是否自动加入转义字符，删除转义字符
		if (get_magic_quotes_gpc()) {
			function stripslashes_deep($value){
				$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);//去掉转义字符
				return $value;
			}
			$_POST = array_map('stripslashes_deep', $_POST);
			$_GET = array_map('stripslashes_deep', $_GET);
			$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
		}
		//注册自动载入函数
		spl_autoload_register('autoload');
		// 设定错误和异常处理
		register_shutdown_function(array('MyException','fatalError'));
		set_error_handler(array('MyException','showError'));
		set_exception_handler(array('MyException','showException'));
		//运行路由解析方法
		Controller::Analysis();
		$filePath=WEB_ACTION;
		//echo $this->cont;
		if(!empty(Controller::$group)){
			$ControlPath = Controller::$group.'/'.Controller::$control .'Action';
			$ControlName = Controller::$control .'Action';
		}else{
			$ControlPath = Controller::$control .'Action';
			$ControlName = Controller::$control .'Action';
		}
		$filePath.=$ControlPath.'.php';
		if(file_exists($filePath)){
			require  $filePath;
		}else{
			throw new Exception("此{$ControlName}控制器不存在",E_USER_WARNING);
		}
		$Control=new $ControlName;
		$action=Controller::$action;
		if(method_exists($Control, $action)){
			$Control->$action();
		}else{
			throw new Exception("<font size='7' color='blue'>此{$ControlName}下的{$action}方法不存在</font>");
		}
		
	}
	/*
	 * init_view 初始化template对象
	 */
	public function init_view(){
		$this->template = new Template();
		if($this->_val) $this->template->assign($this->_val);
	}
	/*
	 * 分配变量给模板
	 * name可为数组进行批量设置
	 */
	
	public  function assign($names,$val=''){
		if(is_array($names)){
			$this->_val=array_merge($this->_val,$names);
		}else{
			$this->_val[$names]=$val;
		}
	}
	/*
	 * 展示模板
	 */
	public function display($templateFile='',$charset='',$contentType='',$content='',$prefix=''){
		//if(empty($templateFile)) exit('请填写要展示的模板名称，如：user/index。代表user分组下面的index.html页面');		
	    $this->init_view();
		$this->template->display($templateFile,$charset,$contentType,$content,$prefix);
		
	}
	/*
	 * 通过路由模式获取控制器
	 * void Return
	 */
	private static function Analysis(){
		/*
		 * 1:判断url模式
		 * 2：进行解析
		 * 3：返回解析结果到类私有属性
		 */
		if(C('URL_MODEL')==1){
			if(C('DEFAULT_GROUP')){
				$G=isset($_GET['g']) ? trim($_GET['g']) : trim(C('DEFAULT_GROUP'));
			}else{
				$G=isset($_GET['g']) ? trim($_GET['g']) : '';
			}
			$Ct=isset($_GET['c']) ? trim($_GET['c']) : trim(C('DEFAULT_CONTROL'));
			$A=isset($_GET['a']) ? trim($_GET['a']) : trim(C('DEFAULT_ACTION'));
			if(!empty($_GET['g']))  unset($_GET['g']);
			if(!empty($_GET['c']))   unset($_GET['c']);
			if(!empty($_GET['a']))   unset($_GET['a']);
			Controller::$group = $G;
			Controller::$control = $Ct;
			Controller::$action = $A;
			
		}elseif(C('URL_MODEL')==2){
			if(isset($_SERVER['PATH_INFO'])){
				$arr=explode('/', substr($_SERVER['PATH_INFO'], 1));
				$expt=array_shift($arr);
			}else{
				$arr=array();
				
			}
			if(is_array($arr) && !empty($arr)){
				if(count($arr)>=3){
					$G=array_shift($arr);
					$Ct=array_shift($arr);
					$A=array_shift($arr);
					if(!C('DEFAULT_GROUP')){
						$G=empty($G)?'':trim($G);
					}else{
						$G=empty($G)?C('DEFAULT_GROUP'):trim($G);
					}
					$Ct=trim($Ct);
					$A=trim($A);
				}else{
					if(C('DEFAULT_GROUP')){
						$G=trim(C('DEFAULT_GROUP'));
				    }else{
						$G='';
				    }
				    
				    $Ct=array_shift($arr);
				    $A=array_shift($arr);
				    $Ct=trim($Ct);
				    $A=trim($A);
				    
				}
				
			}else{
				if(!is_array($arr)) exit('请检查您的路径是否是/分割');
				if(empty($arr)){
					if(!C('DEFAULT_GROUP')){
						$G=empty($G)?'':trim($G);
					}else{
						$G=empty($G)?C('DEFAULT_GROUP'):trim($G);
					}
					if(!C('DEFAULT_ACTION')){
						$A='index';
					}else{
						$A=C('DEFAULT_ACTION');
					}
					if(!C('DEFAULT_ACTION')){
						$Ct='index';
					}else{
						$Ct=C('DEFAULT_ACTION');
					}
				}
			}
			Controller::$group = $G;
			Controller::$control = $Ct;
			Controller::$action = $A;
			
			//echo $_SERVER['PATH_INFO'].'<br/>';
				
// 			echo $_SERVER['SERVER_NAME'];
			
		}
	}
	
	
}