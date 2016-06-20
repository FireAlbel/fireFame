<?php
class commentModel {
	private $model;
	
	public function __construct(){
		$this->model = Model::init();
	}
	
	public function createcomment($data){
		return $this->model->Create($data,'comment');
	}
	
	public function selectcomment($goodsid,$page,$pagesize){
		$sql = "SELECT * FROM `comment` WHERE `goods_id`= {$goodsid}  ORDER BY `addtime` DESC LIMIT {$page},{$pagesize}";
		$result =  $this->model->Query($sql);
		$res = array();
	    while($row=$result->fetch_assoc()){
			$row = array_change_key_case($row, CASE_LOWER);
			array_push( $res, $row);
		}
		$result->free();
		//获取总数count
		$sql2 = "SELECT COUNT(*) AS `num` FROM `comment` WHERE `goods_id`= {$goodsid} AND 1=1";
		$result2 =  $this->model->Query($sql2);
		$row = $result2->fetch_assoc();
		$result2->free();
		$info = array(
				'result' =>$res,
				'count'  =>$row['num']
		);
		return $info;
	}
}

