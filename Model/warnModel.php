<?php
class warnModel {

	private $model;
	
	public function __construct(){
		$this->model = Model::init();
	}
	
	public function createwarn($data){
		return $this->model->Create($data,'warning');
	}
}

