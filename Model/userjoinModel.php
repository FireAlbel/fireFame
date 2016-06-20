<?php
class userjoinModel {
	private $model;
	
	public function __construct(){
		$this->model = Model::init(); 
	}
	
	public function createjoiner($data){
		return $this->model->Create($data,'user_join');
	}
	
	public function confirmjoinuser($id,$status){
		$column = array(
				'status' =>$status
				);
		$where = "`id` ={$id}";
		return $this->model->Update('user_join',$column,$where);
	}
	
	public function selectuserjoin($id){
		$data = array(
				'fields'    =>'',
				'tableName' =>'user_join',
				'order'     =>'`addtime` DESC'
				);
		return $this->model->Select($data," `goods_id` = {$id} ");
	}

	public function selectgoodsid($uid){
		$data = array(
			'fields'    =>'goods_id',
			'tableName' =>'user_join',
			'order'     =>'`addtime` DESC'
		);
		return $this->model->Select($data," `user_id` = {$uid} ");
	}

	public function checkjoiner($uid,$goodsid){
		$data = array(
			'fields'    =>'goods_id',
			'tableName' =>'user_join',
		);
		return $this->model->Select($data," `user_id` = {$uid} AND `goods_id`={$goodsid} ");
	}
//	public function select(){
//		return $this->model->Select(array('','tableName' =>'user_join','order'     =>'`addtime` DESC'),'');
//	}
}

