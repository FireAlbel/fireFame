<?php
class userModel {
	private $model;
	public function __construct(){
		$this->model= Model::init();
	}
	
	public function query($sql){
		return $this->model->Query($sql);
	}
	//生成用户
	public function createuser($array){
		if($this->model->Create($array,'user')){
			$data = array(
				'fields'=>'id',
				'tableName' =>'user'
				);
			$where = "`tel` = '{$array['tel']}'";	
			$result = $this->model->Select($data,$where);
			return $result[0]['id'];
		}else{
			return false;
		}
	}
	//根据tel查询用户密码
	public function selectUserPass($tel){
		$data = array(
				'fields'=>array('pass','tel','rank','registerid','name','img','id'),
				'tableName' =>'user'
				);
		$where = "`tel` = '$tel'";
		return $this->model->Select($data,$where);
	}
	
	public function selectUserfield($data,$id){
		$where = "`id` = '{$id}'";
		return $this->model->Select($data,$where);
	}
	
	public function updateuserinfo($fields,$uid){
		return $this->model->Update('user',$fields,"`id`={$uid}");
	}

	public function updateuserPass($fields,$tel){
		return $this->model->Update('user',$fields,"`tel`={$tel}");
	}
}

?>