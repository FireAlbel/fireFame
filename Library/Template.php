<?php
/**
 * FireFrame Template控制器基类 抽象类
 * @category   Fire
 * @package  Fire
 * @subpackage  Library
 * @author   zhaokun <739576080@qq.com>
 * @time     2014/11/9 
 */
class Template{
	
	private $_val = array();
	/*
	 * 分配变量和值给模板
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
		
		if(empty($charset))  $charset = C('DEFAULT_CHARSET');
		if(empty($contentType)) $contentType = C('TMPL_CONTENT_TYPE');
		// 网页字符编码
		header('Content-Type:'.$contentType.'; charset='.$charset);
		//header('Cache-control: '.C('HTTP_CACHE_CONTROL'));  // 页面缓存控制
		header('X-Powered-By:FireFrame');
	
		if($templateFile!==''){
			$template=WEB_TEMPLATE.$templateFile.'.html';
		}elseif($templateFile===''){
			exit('<center><h1>请填写模板路径，如：user/index，代表user分组下面的index.html页面</h1><center>');
		}
		if(!file_exists($template)) exit('<center><h1>找不到对应的模板路径，请检查：'.$template.'</h1><center>');
		$tem=explode('/', $templateFile);
		$cache=isset($tem[1]) ? WEB_ROOT.'/Cache/'.$tem[0] : WEB_ROOT.'/Cache/';
		Directory($cache);
		$complize=WEB_ROOT.'/Cache/'.$templateFile.'_complized.html';
		if(!file_exists($complize)) file_put_contents($complize,'') ;
	    if(filemtime($template)>filemtime($complize) || file_get_contents($complize)===''||filemtime($complize) < time()-6){
	    	 $content=$this->fetch($template, $this->_val);
	    	 file_put_contents($complize, $content);
	    	 echo $content;
	    }else{
			include($complize);

	    }
	    
	}
	
	/*
	 * 解析模板并编译
	 * return $content
	 */
	public function fetch($template='',$val='',$content=''){
		ob_start();
		ob_implicit_flush(0);
		extract($val,EXTR_OVERWRITE);
		if(is_file($template) && file_exists($template)) include $template;
		$content=ob_get_clean();
		return $content;
	}
	/*
	 * 
	 */
}