<?php 
class templateTag{
	private $Tag=array(
			// 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
			'php'       =>  array(),
			'volist'    =>  array('attr'=>'name,id,offset,length,key,mod','level'=>3,'alias'=>'iterate'),
			'foreach'   =>  array('attr'=>'name,item,key','level'=>3),
			'if'        =>  array('attr'=>'condition','level'=>2),
			'elseif'    =>  array('attr'=>'condition','close'=>0),
			'else'      =>  array('attr'=>'','close'=>0),
			'switch'    =>  array('attr'=>'name','level'=>2),
			'case'      =>  array('attr'=>'value,break'),
			'default'   =>  array('attr'=>'','close'=>0),
			'compare'   =>  array('attr'=>'name,value,type','level'=>3,'alias'=>'eq,equal,notequal,neq,gt,lt,egt,elt,heq,nheq'),
			'range'     =>  array('attr'=>'name,value,type','level'=>3,'alias'=>'in,notin,between,notbetween'),
			'empty'     =>  array('attr'=>'name','level'=>3),
			'notempty'  =>  array('attr'=>'name','level'=>3),
			'present'   =>  array('attr'=>'name','level'=>3),
			'notpresent'=>  array('attr'=>'name','level'=>3),
			'defined'   =>  array('attr'=>'name','level'=>3),
			'notdefined'=>  array('attr'=>'name','level'=>3),
			'import'    =>  array('attr'=>'file,href,type,value,basepath','close'=>0,'alias'=>'load,css,js'),
			'assign'    =>  array('attr'=>'name,value','close'=>0),
			'define'    =>  array('attr'=>'name,value','close'=>0),
			'for'       =>  array('attr'=>'start,end,name,comparison,step', 'level'=>3),
			);
	public function __construct(){}
	public function display(){}
	public static function assign(){}
	
}

