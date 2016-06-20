<?php
class commentAction extends Controller{

	private $model;
	
	public function __construct(){
		$this->model = new commentModel(); 
	}
	
	public function createcomment(){
		if(isset($_POST['content']) && !empty($_POST['content'])){
			$content = trim($_POST['content']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置content参数'
			);
			exit(json_encode($info));
		}
		
		if(isset($_POST['comment_rank']) && !empty($_POST['comment_rank'])){
			$comment_rank = intval($_POST['comment_rank']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置comment_rank参数'
			);
			exit(json_encode($info));
		}
		
		if(isset($_POST['goods_id']) && !empty($_POST['goods_id'])){
			$goods_id = intval($_POST['goods_id']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置goods_id参数'
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
		
		if(isset($_POST['user_name']) && !empty($_POST['user_name'])){
			$user_name = trim($_POST['user_name']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置user_name参数'
			);
			exit(json_encode($info));
		}
		
		if(isset($_POST['parent_id']) && !empty($_POST['parent_id'])){
			$parent_id = intval($_POST['parent_id']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置parent_id参数'
			);
			exit(json_encode($info));
		}
		
		if(isset($_POST['rank']) && !empty($_POST['rank'])){
			$rank = intval($_POST['rank']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置rank参数'
			);
			exit(json_encode($info));
		}
		$data = array(
				'content' =>$content,
				'comment_rank'=>$comment_rank,
				'goods_id'=>$goods_id,
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'parent_id'=>$parent_id,
				'rank' =>$rank,
				'addtime'=>time()
				);
				
		$result = $this->model->createcomment($data);
		if($result != false){
		switch($comment_rank){ 
				case 1:
					$sql = "UPDATE `user` SET `negative_comment` = `negative_comment`+1 WHERE `id` = {$user_id}";
					break;
				case 2: 
				case 3:
					$sql = "UPDATE `user` SET `kind_comment` = `kind_comment`+1 WHERE `id` = {$user_id}";
					break; 
				case 4:
				case 5:
					$sql = "UPDATE `user` SET `good_comment` = `good_comment`+1 WHERE `id` = {$user_id}";
					break;
				} 
			$usermodel = new userModel();
			$status = $usermodel->query($sql);
			if($status != false){
				$info =array(
						'status' =>1,
						'info'   =>$status
				);
				exit(json_encode($info));
			}else{
				$info =array(
						'status' =>0,
						'info'   =>"更新用户评价失败"
				);
				exit(json_encode($info));
			}
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'插入语句出错'
			);
			exit(json_encode($info));
		}
	}
	
	public function selectcomment(){
		if(isset($_POST['goods_id']) && !empty($_POST['goods_id'])){
			$goodsid = intval($_POST['goods_id']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置goods_id参数'
			);
			exit(json_encode($info));
		}
		if(isset($_POST['pagesize']) && !empty($_POST['pagesize'])){
			$pagesize = intval($_POST['pagesize']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置pagesize参数'
			);
			exit(json_encode($info));
		}
		if(isset($_POST['page']) && !empty($_POST['page'])){
			$page = (intval($_POST['page'])-1)*$pagesize;
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置page参数'
			);
			exit(json_encode($info));
		}
		$result = $this->model->selectcomment( $goodsid,$page,$pagesize);
		if($result != false){
			$info =array(
					'status' =>1,
					'info'   =>$result['result'],
					'count'  =>$result['count']
			);
			exit(json_encode($info));
		}else{
			$info =array(
					'status' =>0,
					'info'   =>"获取评论失败"
			);
			exit(json_encode($info));
		}
	} 
}

