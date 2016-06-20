<?php
class goodsModel {
	
	private $model;
	
	public function __construct(){
		$this->model = Model::init();
	}
	
	public function selectgoods($page,$pagesize){
		$limitTime = time() - 8*3600;
		$sql = "SELECT `goods`.*, `user`.`img`,`user`.`name`,`user`.`rank` FROM `goods` LEFT JOIN `user` ON `goods`.`uid` = `user`.`id` WHERE (`goods`.`time_start` > ".time()." AND `is_delete` = 0 AND `goods`.`count` > `goods`.`usercount`) OR (`goods`.`time_end` > {$limitTime}) ORDER BY `goods`.`price` DESC LIMIT {$page},{$pagesize}";
		$result =  $this->model->Query($sql);
		$res = array();
	    while($row=$result->fetch_assoc()){
			$row = array_change_key_case($row, CASE_LOWER);
			array_push( $res, $row);
		}
		$result->free();
//		$sql2 = "SELECT COUNT(*) as `num` FROM `goods`  WHERE (`goods`.`time_start` > ".time()." AND `is_delete` = 0 AND `goods`.`count` > `goods`.`usercount`) OR ((".time()." - `goods`.`endtime`) < (8 * 3600))";
		$sql2 = "SELECT COUNT(*) as `num` FROM `goods`  WHERE (`goods`.`time_start` > ".time()." AND `is_delete` = 0 AND `goods`.`count` > `goods`.`usercount`) OR (`goods`.`time_end` > {$limitTime})";

		$result2 =  $this->model->Query($sql2);
		$row = $result2->fetch_assoc();
		$result2->free();
		$info = array(
				'result' =>$res,
				'count'  =>$row['num']
		);
		return $info;
	}

//	public function selectgoodsend(){
//		$limitTime = time() - 8*3600;
//		$sql2 = "SELECT COUNT(*) as `num` FROM `goods`  WHERE  `goods`.`time_start` > ".time()." AND `is_delete` = 0 AND `goods`.`count` > `goods`.`usercount`";
//		echo $sql2;
//		$result2 =  $this->model->Query($sql2);
//		$row = $result2->fetch_assoc();
//		$result2->free();
//		$count = $row['num'];
//		echo $count;
//	}

	public function insertgoods($data){
		return $this->model->Create($data,'goods');
	}

	//获取用户发布的商品
	public function selectgoodsbyid($uid,$page,$pagesize){
		$sql = "SELECT * FROM `goods` WHERE `uid` = {$uid} AND `is_delete` = 0 AND 1=1 ORDER BY `addtime` DESC LIMIT {$page},{$pagesize} ";
		$result =  $this->model->Query($sql);
		$res = array();
		while($row=$result->fetch_assoc()){
			$row = array_change_key_case($row, CASE_LOWER);
			array_push( $res, $row);
		}
		$result->free();
		//获取总数count
		$sql2 = "SELECT COUNT(*) AS `num` FROM `goods` WHERE `uid` = {$uid} AND `is_delete` = 0 AND 1=1";
		$result2 =  $this->model->Query($sql2);
		$row = $result2->fetch_assoc();
		$result2->free();
		$info = array(
				'result' =>$res,
				'count'  =>$row['num']
				);
		return $info;
	}
	//删除商品
	public function deletegoods($uid,$id){
		$column = array(
				'is_delete' =>1
				);
		$where = "`uid` = $uid AND `id` = $id";
		return $this->model->Update('goods',$column,$where);
	}
	//查询某一字段
	public function findgoods($fields,$goods_id){
		$data = array(
				'fields'    =>$fields,
				'tableName' =>'goods',
				);
		return $this->model->Select($data," `id` = {$goods_id} AND `is_delete` = 0 ");
	}
	//update count
	public function updatecount($id){
		$sql = "UPDATE `goods` SET `usercount` = `usercount`+1 WHERE  `id` = $id";
		return $this->model->Query($sql);
	}
	//update status
	public function updatestatus($id,$uid){
		$sql = "UPDATE `goods` SET `usercount` = `usercount`+1,`status` = 1  WHERE  `id` = $id";
		return $this->model->Query($sql);
	}

	//交易记录，（抢）加入了哪些商品
	public function selectgoodslist($uid,$page,$pagesize){
		$sql = "SELECT `goods`.*,`user`.`name`,`user`.`rank` FROM `goods` LEFT JOIN `user` ON `goods`.`uid` = `user`.`id` WHERE `goods`.`id` IN (SELECT `goods_id` FROM `user_join` WHERE `user_id` = {$uid}) ORDER BY `goods`.`addtime` DESC LIMIT {$page},{$pagesize}";
		$result =  $this->model->Query($sql);
		$res = array();
		while($row=$result->fetch_assoc()){
			$row = array_change_key_case($row, CASE_LOWER);
			array_push( $res, $row);
		}
		$result->free();
		//获取总数count
		$sql2 = "SELECT COUNT(*) AS `num` FROM `goods` WHERE `goods`.`id` IN (SELECT `goods_id` FROM `user_join` WHERE `user_id` = {$uid})";
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

