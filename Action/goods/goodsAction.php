<?php
class goodsAction extends Controller {
	private $model;
	public function __construct(){
		$this->model = new goodsModel();
	}

//	public function selectgoodsend(){
//		$this->model->selectgoodsend();
//		exit();
//	}

	/*
	 * 首页商品列表
	 *   
	 *   */
	public function selectgoods(){
//		$_POST['page'] = 1;
//		$_POST['pagesize'] =5;
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
			$page = (intval($_POST['page']) - 1) * $pagesize;
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置page参数'
			);
			exit(json_encode($info));
		}
		
		$result = $this->model->selectgoods($page, $pagesize);
		
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
					'info'   =>'查询语句出错'
			);
			exit(json_encode($info));
		}
	}
	/*
	 * 创建商品
	 * zhaokun 
	 * 2015 10 06
	 * */
	public function creategoods(){
//		echo strtotime('2015-11-27 13:13');
//		echo date('Y-m-d H:i:s',1448601180);
//		exit();
//		$_POST['uid']=1;
//		$_POST['title']="测试232";
//		$_POST['description'] ="测试1";
//		$_POST['time_start'] = '2015-12-19 8:00';
//		$_POST['time_end'] = '2015-12-19 18:00';
//		$_POST['tel'] = '18363974491';
//		$_POST['address']="青岛农业大学";
//		$_POST['is_hurry'] = 1;
//		$_POST['count'] = 10;
//		$_POST['price'] = 10;
//		$_POST['price_unit'] = 1;
		if(isset($_POST['uid']) && !empty($_POST['uid'])){
			$uid = intval($_POST['uid']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置uid参数'
			);
			exit(json_encode($info));
		}
		//title
		if(isset($_POST['title']) && !empty($_POST['title'])){
			$title = trim($_POST['title']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置title参数'
			);
			exit(json_encode($info));
		}
		//description
		if(isset($_POST['description'])){
			$description = trim($_POST['description']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置description参数'
			);
			exit(json_encode($info));
		}
		//获取开始时间参数
		if(isset($_POST['time_start']) && !empty($_POST['time_start'])){
			$time_start = strtotime($_POST['time_start']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置time_start参数'
			);
			exit(json_encode($info));
		}
		//time_end
		if(isset($_POST['time_end']) && !empty($_POST['time_end'])){
			$time_end = strtotime($_POST['time_end']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置time_end参数'
			);
			exit(json_encode($info));
		}
		//tel
		if(isset($_POST['tel']) && !empty($_POST['tel'])){
			$tel = trim($_POST['tel']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置tel参数'
			);
			exit(json_encode($info));
		}
		//address
		if(isset($_POST['address']) && !empty($_POST['address'])){
			$address = trim($_POST['address']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置address参数'
			);
			exit(json_encode($info));
		}
		//is_hurry
		if(isset($_POST['is_hurry'])){
			$is_hurry = intval($_POST['is_hurry']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置is_hurry参数'
			);
			exit(json_encode($info));
		}
		//count
		if(isset($_POST['count']) && !empty($_POST['count'])){
			$count = intval($_POST['count']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置count参数'
			);
			exit(json_encode($info));
		}
		//price
		if(isset($_POST['price']) && !empty($_POST['price'])){
			$price = floatval($_POST['price']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置price参数'
			);
			exit(json_encode($info));
		}
		//price_unit
		if(isset($_POST['price_unit']) && !empty($_POST['price_unit'])){
			$price_unit = intval($_POST['price_unit']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置price_unit参数'
			);
			exit(json_encode($info));
		}
		$data=array(
				'uid' =>$uid,
				'title' =>$title,
				'description' =>$description,
				'time_start' =>$time_start,
				'time_end'   =>$time_end,
				'tel'        =>$tel,
				'address'    =>$address,
				'is_hurry'   =>$is_hurry,
				'count'      =>$count,
				'price'      =>$price,
				'price_unit' =>$price_unit,
				'addtime'    =>time()
		);
		$result = $this->model->insertgoods($data);
		if($result != false){
			$info =array(
					'status' =>1,
					'info'   =>$result
			);
			exit(json_encode($info));
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'插入语句出错'
			);
			exit(json_encode($info));
		}
	}
	/*
	 * 删除goods
	 *   
	 *   */ 
	public function deletegoods(){
		if(isset($_POST['uid']) && !empty($_POST['uid'])){
			$uid = intval($_POST['uid']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置uid参数'
			);
			exit(json_encode($info));
		}
		
		if(isset($_POST['id']) && !empty($_POST['id'])){
			$id = intval($_POST['id']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置id参数'
			);
			exit(json_encode($info));
		}
		$result = $this->model->deletegoods($uid, $id);
		if($result != false){
			$info =array(
					'status' =>1,
					'info'   =>$result
			);
			exit(json_encode($info));
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'更新语句出错'
			);
			exit(json_encode($info));
		}
	}
	
	public function selectgoodsbyid(){
//		$_POST['uid'] = 2;
//		$_POST['page'] =1;
//		$_POST['pagesize'] =2;
		if(isset($_POST['uid']) && !empty($_POST['uid'])){
			$uid = intval($_POST['uid']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置uid参数'
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
			$page = (intval($_POST['page']) - 1) * $pagesize;
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置page参数'
			);
			exit(json_encode($info));
		}
		
		$result=$this->model->selectgoodsbyid($uid,$page,$pagesize);
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
					'info'   =>'查询出错'
			);
			exit(json_encode($info));
		}
		
	}
	
	public function updategoodsuser(){

		if(isset($_POST['goods_id']) && !empty($_POST['goods_id'])){
			$goods_id = intval($_POST['goods_id']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置goods_id参数'
			);
			exit(json_encode($info));
		}
		if(isset($_POST['uid']) && !empty($_POST['uid'])){
			$uid = intval($_POST['uid']);
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置uid参数'
			);
			exit(json_encode($info));
		}

		$status = $this->checkgoods($uid, $goods_id);
		if($status == 306){
			$info =array(
				'status' =>0,
				'info'   =>'不能重复抢单'
			);
			exit(json_encode($info));
		}

		if($status == 305){
			$info =array(
				'status' =>0,
				'info'   =>'不能抢自己的单子'
			);
			exit(json_encode($info));
		}
		if($status == 301){
			$res = $this->model->updatecount($goods_id);
		}elseif($status == 302){
			$res = $this->model->updatestatus($goods_id);
		}elseif($status == 303){
			$info =array(
					'status' =>0,
					'info'   =>'不好意思，此单已被抢完'
			);
			exit(json_encode($info));
		}elseif($status == 304){
			$info =array(
					'status' =>0,
					'info'   =>'不好意思，此单不存在，可能已被删除'
			);
			exit(json_encode($info));
		}
		
		if($res != false){
			if(isset($_POST['user_name']) && !empty($_POST['user_name'])){
				$user_name = trim($_POST['user_name']);
			}else{
				$info =array(
						'status' =>0,
						'info'   =>'没有设置user_name参数'
				);
				exit(json_encode($info));
			}
			
			if(isset($_POST['tel']) && !empty($_POST['tel'])){
				$tel = trim($_POST['tel']);
			}else{
				$info =array(
						'status' =>0,
						'info'   =>'没有设置tel参数'
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
					'goods_id' =>$goods_id,
					'user_id'  =>$uid,
					'user_name' =>$user_name,
					'tel' =>$tel,
					'rank' =>$rank,
					'addtime' =>time()
			);
			$joinmodel = new userjoinModel();
			$result = $joinmodel->createjoiner($data);
			if($result != false){
				$info =array(
						'status' =>1,
						'info'   =>$result
				);
				exit(json_encode($info));
			}else{
				$info =array(
						'status' =>0,
						'info'   =>'插入抢单人表失败'
				);
				exit(json_encode($info));
			}
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'更新count字段失败'
			);
			exit(json_encode($info));
		}
	}
	
	public function updategoods(){
		
	}
	
	private function checkgoods($uid,$goods_id){
		$joinmodel = new userjoinModel();
		$res = $joinmodel->checkjoiner($uid,$goods_id);
		if(count($res) > 0){
			return 306;
		}
		$fields = array(
				'uid','count','usercount'
				);
		$result = $this->model->findgoods($fields, $goods_id);
		if($result[0]['uid'] == $uid){
			return 305;
		}
		if(count($result) > 0){
			if(intval($result[0]['usercount']) < intval($result[0]['count']) && intval($result[0]['usercount'])+1 < intval($result[0]['count']) ){
				return 301;
			}elseif(intval($result[0]['usercount'])+1 == intval($result[0]['count'])){
				return 302;
			}elseif(intval($result[0]['usercount']) == intval($result[0]['count'])){
				return 303;
			}
		}else{
			return 304;
		}
	}
}

