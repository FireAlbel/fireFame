<?php
/**
 * FireFrame Model模板基类 
 * @category   Fire
 * @package  Fire
 * @subpackage  Library
 * @author   zhaokun <739576080@qq.com>
 * @time     2014/11/9 
 */
class Model{
	public static $instance=NULL;
	protected static $mError=NULL;
	private static $link=NULL;
	private $host;
	private $dbUser;
	private $dbpass;
	private $charset;
	private $db ;
	private function __construct(){
			$this->conn();	
	}
	public static function &init(){
		if(empty(self::$instance)){
			self::$instance=new self();
		}
		return self::$instance;
	}
	/*
	 * 连接数据库函数
	 * void Return
	 */
	private function conn(){
		$this->loadConfig();
		//创建对象并打开连接，最后一个参数是选择的数据库名称
		Model::$link = new mysqli($this->host,$this->dbUser,$this->dbpass,$this->db);
		//检查连接是否成功
		if (mysqli_connect_errno()){
			//注意mysqli_connect_error()新特性
			die('Unable to connect!'). mysqli_connect_error();
		}
		@Model::$link->query("SET NAMES UTF8;");
	}
	/*
	 * 
	 * 
	 */
	private function loadConfig(){
		$this->dbUser=C('Db/DB_USER');
		$this->dbpass=C('Db/DB_PASS');
		$this->db=C('Db/DB_DB');
		$this->host=C('Db/DB_HOST');
	}
	/*
	 * SQL语句执行
	 * @param String $Sql
	 */
	public function Query($sql){
		if(!empty($sql)){
			$result=@Model:: $link->query($sql);
		}else{
			echo "sql is null";
			return ;
		}
		if ( $result === false )
		{
			self::$mError = Model:: $link->errno."错误提示：".Model:: $link->error;
			echo self::$mError;
			return false;
		}
		else
		{
			return $result;
		}
	}
	/*
	 * 插入函数
	 * @param $data array  要插入的数据（数组）
	 * @param $table 表名
	 * return  $state 插入状态
	 */
	public  function Create($data,$table=''){
		//insert into tableName(clumn,clumn2) values (data1,data2)
		if(empty($table)) exit('请检查你要插入的数据表名是否为空');
		if(empty($data)) exit('请检查要添加的数据');
		$k=array();
		$v=array();
		foreach ($data as $key=>$val){
			if(is_scalar($val)){
				$key=$this->parseKey($key);
				array_push($k, $key);
				$val=$this->parseValue($val);
				//if(!is_scalar($val) && is_array($val)) $val=implode(' ', $val);
				array_push($v,$val);
			}
		}
		$k=implode(',', $k);
		$v=implode(',',$v);
		$sql="INSERT INTO $table($k) VALUES ($v)";
		$state=$this->Query($sql);
		return $state;
		
	}
	/*
	 * @param $data array
	 * @param $where string
	 * return 查询出的结果（数组）
	 */
	public function Select($data,$where=''){
		//select a，b，c from tablename where id=‘’
		if(!isset($data['fields']) ) $data['fields']='';
		if(is_array($data['fields']) && count($data['fields'])>0){
			$fields_arr = array();
			foreach($data['fields'] as $val){
				array_push($fields_arr, $this->parseKey($val));
			}
			$fields=implode(',', $fields_arr);
		}elseif ( empty($data['fields'])){
			$fields="*";
		}elseif(is_string($data['fields']) && !empty($data['fields'])){
			$fields=$this->parseKey($data['fields']);
		}
		if(isset($data['tableName'])&& !empty($data['tableName'])){
			$data['tableName'] = $this->parseKey($data['tableName']);
		}else{
			echo "tableName is null";
			return false;
		}
		
		if(empty($where)){
			$wheres=" WHERE 1=1";
		}else{
			$wheres=" WHERE ".$where." AND 1=1";
		}
		
		if(isset($data['order']) && !empty($data['order'])){
			$order = " ORDER BY {$data['order']}";
		}else{
			$order = '';
		}
	
		$sql='SELECT '.$fields.' FROM '.trim($data['tableName']).$wheres.$order;
		$results=$this->Query($sql);
		$res=array();
		while($row=$results->fetch_assoc()){
			$row = array_change_key_case($row, CASE_LOWER);
			array_push( $res, $row);
		}
		$results->free();
		return $res;
		
	}
	/*
	 * 更新操作
	 * @param $table 数组或值
	 * @param $column 更新的字段
	 * @param $where 更新条件
	 * return boolean
	 */
	public function Update($table,$column,$where=''){
	//update table1,table2 set cloumn1='value',column2= "values2" where id="" and 1=1
		if(empty($table)) return false;
		if(is_string($table) && !empty($table)){
			$table =$this->parseKey($table);
		}
		$data=$this->parseSet($column);
		if(empty($where)){
			$where='1=1';
		}else{
			$where=$where.' AND 1=1';
		}
		$sql='UPDATE '.$table.' SET '. $data.' WHERE ';
		$results=$this->Query($sql);
		return $results;
	}
	/** 
	* 删除方法 
	* tags 
	* @param $table(表名)
	* @param $where (条件)
	* @return BOLLEAN 
	* @author zhaokun 
	* @date 2014-12-11下午10:08:26 
	* @version v1.0.0 
	*/
	public function Delete($table,$where){
		if(empty($where)){ 
			echo "删除条件为空,会将整个表内容删除";
			return false;
		}else{
			$where=$where.' and 1=1';
		}
		$sql='DELETE FROM '.$this->parseKey($table).' WHERE '.$where;
		$state=$this->Query($sql);
		return $state;
		
	}
	/** 
	* 关闭所有连接和资源 
	*  
	*  
	* @return void 
	* @author zhaokun 
	* @date 2014-12-22下午3:31:45 
	* @version v1.0.0 
	*/
	private function Close(){
		if ( is_resource( self::$link ) )
		{
			self::$link->close();
		}
		
		self::$link = null;
		self::$instance = null;
	}
	/*
	 * 
	 */
	public function __destruct(){
		if ( is_resource( self::$link ) )
		{
			$this->Close();
		}
	}
	/**
	 * value分析     截取Thinkphp
	 * @access protected
	 * @param mixed $value
	 * @return string
	 */
	protected function parseValue($value) {
		if(is_string($value)) {
			$value =  '\''.$this->escapeString($value).'\'';
		}elseif(is_bool($value)){
			$value =  $value ? '1' : '0';
		}elseif(is_int($value)){
			$value = '\''.$value.'\'';
		}elseif(is_null($value)){
			$value =  '';
		}
		return $value;
	}
	/*
	 * 转义可疑字符
	 * @param $val string
	 * return string
	 * 
	 */
	protected function escapeString($val){
// 		return mysql_real_escape_string($val);
		return $val;
	}
	/**
	 * 字段和表名添加`
	 * 保证指令中使用关键字不出错 针对mysql
	 * @access protected
	 * @param mixed $value
	 * @return mixed
	 */
	protected function parseKey(&$value) {
		$value   =  trim($value);
		if( false !== strpos($value,' ') || false !== strpos($value,',') || false !== strpos($value,'*') ||  false !== strpos($value,'(') || false !== strpos($value,'.') || false !== strpos($value,'`')) {
			//如果包含* 或者 使用了sql方法 则不作处理
		}else{
			$value = '`'.$value.'`';
		}
		return $value;
	}
	/** 
	* 更新前对更新数据进行分析整合 
	* tags 
	* @param $data
	* @return array 
	* @author zhaokun 
	* @date 2014-12-11下午9:31:55 
	* @version v1.0.0 
	*/
	protected function parseSet($data){
		if(!(count($data) > 0)){
			echo "要更新的字段为空";
			return false;
		}
		foreach($data as $key=>$value){
			$value=$this->parseValue($value);
			if(is_scalar($value)){
				$set[]=$this->parseKey($key).'='.trim($value);
			}
			
		}
		return implode(',', $set);
	}
	
}