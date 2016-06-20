<?php
class warnAction extends Controller{

	private $model;
	
	public function __construct(){
		$this->model = new warnModel();
	}
	
	public function insertwarn(){
		
		if(isset($_POST['goods_id']) && !empty($_POST['img'])){
			$goods_id = intval($_POST['goods_id']);
		}else{
			$goods_id = 0;
		}
		
		if(isset($_POST['img']) && !empty($_POST['img'])){
			$img = trim($_POST['img']);
		}else{
			$img ='';
		}
		
		if(isset($_POST['reason']) && !empty($_POST['reason'])){
			$reason = trim($_POST['reason']) ;
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置reason参数'
			);
			exit(json_encode($info));
		}
		if(isset($_POST['user_id']) && !empty($_POST['user_id'])){
			$user_id = intval($_POST['user_id']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置user_id参数'
			);
			exit(json_encode($info));
		}
		
		if(isset($_POST['user_boss']) && !empty($_POST['user_boss'])){
			$user_boss = intval($_POST['user_boss']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置user_boss参数'
			);
			exit(json_encode($info));
		}
		
		$data =array(
				'goods_id' =>$goods_id,
				'img'      =>$img,
				'reason'   =>$reason,
				'user_id'  =>$user_id,
				'user_boss' =>$user_boss,
				'addtime'  =>time()
				);
		$this->model->createwarn($data);
	}
	
	public function warnimg(){
		import('Image');
		$filepath = WEB_ROOT."/wran";
		$name = Image::upload($filepath);
		$info =array(
				'status' =>1,
				'info'   =>$filepath."/".$name
				);
		exit(json_encode($info));
	}
}

