<?php
class indexAction extends Controller {
	private $userModel;
	public function __construct(){
		parent::__construct();
		$this->userModel = new userModel();
	}
	public function login() {
//		$_POST['tel'] = "18363974491";
//		$_POST['pass'] = "123456";
		if(isset($_POST['tel']) && !empty($_POST['tel'])){
			$tel = trim($_POST['tel']);			
		}else{
			$info = array(
					'status' =>0,
					'info' => "没有获取到用户帐号tel" 
					);
			echo json_encode($info);
			exit();
		}
		if(isset($_POST['pass']) && !empty($_POST['pass'])){
			$pass = trim($_POST['pass']);
		}else{
			$info = array(
					'status' =>0,
					'info' => "没有获取到用户密码"
			);
			echo json_encode($info);
			exit();
		}
		
		$result = $this->userModel->selectUserPass($tel);
		if(empty($result)){
			$info = array(
				'status' =>1,
				'info' => array(
					'status' =>0,
					'info'    =>'此帐号未注册，请注册后登陆！'
				)
			);
			exit(json_encode($info));
		}
		if(md5($pass) == $result[0]['pass']){
			unset($result[0]['pass']);
			$info = array(
					'status' =>1,
					'info' => array(
							'status' =>1,
							'info'    =>$result
					)
			);
			exit(json_encode($info));
		}else{
			$info = array(
					'status' =>1,
					'info' => array(
							'status' =>0,
							'info'    =>"帐号密码错误"
							)
					);
			exit(json_encode($info));
		}
	}
	public function sendsms(){
		import('SendSMS');
		if(empty($_POST['tel']) || !isset($_POST['tel'])){
			$info = array(
				'status' =>0,
				'info' => '没有获取到手机号'
			);
			exit(json_encode($info));
		}
		$tel = trim($_POST['tel']);
		$code = rand(1,9).rand(1,9).rand(1,9).rand(1,9);
		$datas = array($code,'2');
		$tempId = 1;
		$res = sendTemplateSMS($tel,$datas,$tempId);
		if($res == 200){
			$info = array(
				'status' =>1,
				'info' => $code
			);
			echo json_encode($info);
			exit();
		}else if($res == 400){
			$info = array(
				'status' =>0,
				'info' => '验证码发送失败，请重新点击发送'
			);
			echo json_encode($info);
			exit();
		}
	}
	public function register() {
// 				$_POST['tel'] = "18363974691";
// 				$_POST['pass'] = "123456";
// 				$_POST['name']  ='zhaokun';
		if(isset($_POST['tel']) && !empty($_POST['tel'])){
			$tel = trim($_POST['tel']);
		}else{
			$info = array(
					'status' =>0,
					'info' => "没有获取到用户帐号tel"
			);
			echo json_encode($info);
			exit();
		}
		
		$result = $this->userModel->selectUserPass($tel);
		if(count($result) > 0){
			$info = array(
					'status' =>0,
					'info'   =>"用户已存在"
					);
			exit(json_encode($info));	
		}
		
		if(isset($_POST['name']) && !empty($_POST['name'])){
			$name = trim($_POST['name']);
		}else{
			$info = array(
					'status' =>0,
					'info' => "没有获取到用户帐号name"
			);
			echo json_encode($info);
			exit();
		}
		
		if(isset($_POST['pass']) && !empty($_POST['pass'])){
			$pass = trim($_POST['pass']);
		}else{
			$info = array(
					'status' =>0,
					'info' => "没有获取到用户帐号pass"
			);
			echo json_encode($info);
			exit();
		}
		
		$array = array(
				'name' =>$name,
				'tel' =>$tel,
				'pass' =>md5($pass),
				'addtime'=>time()
		);
		
		$userid = $this->userModel->createuser($array);
		if($userid != false){
			$info = array(
					'status' =>1,
					'info' => array(
							'uid'=>$userid,
							'name' =>$name,
							'tel' =>$tel
					)
			);
			echo json_encode($info);
			exit ();
		}else{
			$info = array(
					'status' =>0,
					'info' => "插入数据库失败"
			);
			echo json_encode($info);
			exit ();
		}
		
	}
	//查找加入的商品列表
	public function selectgoods(){
		if(isset($_POST['user_id']) && !empty($_POST['user_id'])){
			$user_id = intval($_POST['user_id']);
		}else{
			$info = array(
				'status' =>0,
				'info' => "没有获取到您的id，请确定已登陆"
			);
			echo json_encode($info);
			exit();
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
		$model = new goodsModel();
		$res = $model->selectgoodslist($user_id,$page,$pagesize);
		if($res != false){
			$info =array(
				'status' =>1,
				'info'   =>$res['result'],
				'count'  =>$res['count']
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
	//查找加入人列表
	public function selectjoinuser(){
//		$model = new userjoinModel();
//		$res = $model->select();
//		var_dump($res);
//		exit();
		if(isset($_POST['goodsid']) && !empty($_POST['goodsid'])){
			$goodsid = intval($_POST['goodsid']);
		}else{
			$info = array(
					'status' =>0,
					'info' => "没有获取到goodsid"
			);
			echo json_encode($info);
			exit();
		}
		
		if(isset($_POST['pagesize']) && !empty($_POST['pagesize'])){
			$pagesize = intval($_POST['pagesize']);
		}else{
			$info = array(
					'status' =>0,
					'info' => "没有获取到pagesize"
			);
			echo json_encode($info);
			exit();
		}
		
		if(isset($_POST['page']) && !empty($_POST['page'])){
			$page = (trim($_POST['page'])-1)*$pagesize;
		}else{
			$info = array(
					'status' =>0,
					'info' => "没有获取到page"
			);
			echo json_encode($info);
			exit();
		}
		
		$joinmodel = new userjoinModel();
		$result = $joinmodel->selectuserjoin($goodsid);
		if($result === false){
			$info = array(
				'status' =>0,
				'info' => "服务器繁忙，获取失败"
			);
			echo json_encode($info);
			exit();
		}else{
			$info = array(
				'status' =>1,
				'info' => $result
			);
			echo json_encode($info);
			exit();
		}
		
	}
	//查找用户加入的所有商品id
	public function selectgoodsid(){
//		$_POST['user_id']=2;
		if(isset($_POST['user_id']) && !empty($_POST['user_id'])){
			$user_id = intval($_POST['user_id']);
		}else{
			$info = array(
				'status' =>0,
				'info' => "没有获取到您的id，请确定已登陆"
			);
			echo json_encode($info);
			exit();
		}
		$joinmodel = new userjoinModel();
		$result = $joinmodel->selectgoodsid($user_id);

		if($result === false){
			$info = array(
				'status' =>0,
				'info'  =>'获取失败'
			);
			echo json_encode($info);
			exit();
		}else{
			$res = array();
			foreach($result as $ke=>$val){
				array_push($res,$val['goods_id']);
			}
			$info = array(
				'status' =>1,
				'info'  =>$res
			);
			echo json_encode($info);
			exit();
		}
	}
	//确认添加用户为干活的人
	public function confirmuser(){
		
		if(isset($_POST['id']) && !empty($_POST['id'])){
			$id = intval($_POST['id']);
		}else{
			$info = array(
					'status' =>0,
					'info' => "没有获取到id"
			);
			echo json_encode($info);
			exit();
		}
		
		
		$joinmodel = new userjoinModel();
		$result = $joinmodel->confirmjoinuser($id);
		if($result != false){
			$info = array(
					'status' =>1,
					'info' => true
			);
			echo json_encode($info);
			exit();
		}else{
			$info = array(
					'status' =>0,
					'info' => "选中失败"
			);
			echo json_encode($info);
			exit();
		}
	}
	//更新用户基本资料
	public function updateuserinfo(){
		if(isset($_POST['id']) && !empty($_POST['id'])){
			$uid = intval($_POST['id']);
		}else{
			$info = array(
					'status' =>0,
					'info' => "没有获取到id"
			);
			echo json_encode($info);
			exit();
		}
		
		if(isset($_POST['name']) && !empty($_POST['name'])){
			$name = intval($_POST['name']);
		}else{
			$info = array(
					'status' =>0,
					'info' => "没有获取到name"
			);
			echo json_encode($info);
			exit();
		}
		$fields= array(
				'name' =>$name
				);
		$result = $this->userModel->updateuserinfo($fields, $uid);
		if($result != false){
			$info = array(
					'status' =>1,
					'info' => $result
			);
			echo json_encode($info);
			exit();
		}else{
			$info = array(
					'status' =>0,
					'info' => "更新失败"
			);
			echo json_encode($info);
			exit();
		}
	}
//忘记密码
	public function forget(){
		if(isset($_POST['tel']) && !empty($_POST['tel'])){
			$tel = trim($_POST['tel']);
		}else{
			$info = array(
				'status' =>0,
				'info' => "没有获取到用户帐号"
			);
			echo json_encode($info);
			exit();
		}
		//修改数据库更新用户密码信息
		$pass = rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9);
		$data = array(
			'pass' =>md5('123456')
		);
		$status = $this->userModel->updateuserPass($data,$tel);
		if($status == false){
			$info = array(
				'status'=>1,
				'info'  =>'密码修改失败，请重新提交'
			);
			echo json_encode($info);
			exit();
		}else{
			import('SendSMS');
			$datas = array($pass,'2');
			$tempId = 1;
			$res = sendTemplateSMS($tel,$datas,$tempId);
			if($res){
				$info = array(
					'status'=>1,
					'info'  =>'密码修改成功，将发送到您的手机上'
				);
				echo json_encode($info);
				exit();
			}else{
				$info = array(
					'status'=>1,
					'info'  =>'发送手机短信失败请重新提交'
				);
				echo json_encode($info);
				exit();
			}

		}
	}
	//图片上传
	public function imgupload(){
		if(isset($_POST['id']) && !empty($_POST['id'])){
			$id = intval($_POST['id']);
		}else{
			$info = array(
					'status' =>0,
					'info' => "没有获取到id"
			);
			echo json_encode($info);
			exit();
		}
		
		if(isset($_POST['type']) && !empty($_POST['type'])){
			$type = intval($_POST['type']);
		}else{
			$info = array(
					'status' =>0,
					'info' => "没有获取到type"
			);
			echo json_encode($info);
			exit();
		}
		import('Image');
		//1:head img 2:verify img
		if($type == 1){
			$path = WEB_ROOT."/upload/userhead".$id;
			$name = Image::upload($path);
			$fields = array(
					'img' =>$path."/".$name
					);
			$this->userModel->updateuserinfo($fields, $id);
			
		}elseif($type == 2){
			$path = WEB_ROOT."/upload/userverify".$id;
			$name = Image::upload($path);
			$fields = array(
					'img' =>$path."/".$name
					);
			$this->userModel->updateuserinfo($fields, $id);
		}
	}
}
