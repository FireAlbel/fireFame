<?php
/**
 * 图片处理文件
* ==============================================
* 版权所有 森普网
* ----------------------------------------------
* 森普网手机客户端。
* ==============================================
* @date: 2015-8-18
* @author: zhaokun
* @version: V1.0
*/
final class Image {
	private $type; // 图片类型
	private $width; // 实际宽度
	private $height; // 实际高度
	private $resize_width; // 改变后的宽度
	private $resize_height; // 改变后的高度
	private $cut; // 是否裁图
	private $srcimg; // 源图象
	private $dstimg; // 目标图象地址
	private $im; // 临时创建的图象
	private $quality; // 图片质量
	private static $image;
	private function __construct(){}
	public static function getImage(){
		if(!(self::$image instanceof self)){
			self::$image = new self;
		}
		return self::$image;
	}
	
	
	/**
	 * 删除图片
	 * @date: 2015-8-19
	 * @author: zhaokun
	 * @return: boolean
	 */
	public static function del($imagedir = ''){
		if(!empty($imagedir)){
			$dir = trim($imagedir);
			$state = unlink($dir);
			return $state;
		}else{
			return false;
		}
	}
	/**
	* 更新图片
	* @date: 2015-8-21
	* @author: zhaokun
	* @return: null
	*/
	public static function updateimg(){
		if(isset($_POST['id']) && !empty($_POST['id'])){
			if(isset($_POST['img']) && !empty($_POST['img'])){
				$img = trim($_POST['img']);
				$id =intval($_POST['id']);
				if(isset($_POST['tag']) && !empty($_POST['tag'])){
					$tag = intval($_POST['tag']);
				}else{
					$info = array(
							'status' =>0,
							'info'   =>'没有设置tag参数或者tag参数为空'
							);
					echo json_encode($info);
					exit();
				}
				if($tag === 1 ){
					$sql = 'SELECT `img` FROM `c_caigou` WHERE `user` = '.$id;
				}elseif($tag === 2){
					$sql = 'SELECT `img` FROM `content` WHERE `user` = '.$id;
				}elseif($tag === 3){
					$sql = 'SELECT `logo` FROM `ziliao` WHERE `uid` = '.$id;
				}elseif($tag === 4){
					$sql = 'SELECT `img` FROM `users` WHERE `id` = '.$id;
				}
				$mod = Sqlhelper::db();	
				$res = $mod->show_dql($sql,1);
				$res = explode(',',$res[0]['img']);
				
				if(in_array($img, $res)){
					$index = array_search($img,$res);
					unset($res[$index]);
					$res = implode(',',$res);
					//1:求购  2:供应 3:企业logo 4:个人logo
					if($tag === 1 ){
						if(isset($_POST['cate']) && !empty($_POST['cate'])){
							$category = intval($_POST['cate']);
						}else{
							$info = array(
									'status' => 0,
									'info'   =>'没有设置三级品名cate或者cate为空'
							);
						}
						$sql = 'UPDATE `c_caigou` SET `img` = "'.$res.'" WHERE `user` = '.$id;
						$img = new Getdir($id,$category);
						$dir = $img->dir_img();//图片目录
					}elseif($tag === 2){
						if(isset($_POST['cate']) && !empty($_POST['cate'])){
							$category = intval($_POST['cate']);
						}else{
							$info = array(
									'status' => 0,
									'info'   =>'没有设置三级品名cate或者cate为空'
							);
						}
						$sql = 'UPDATE `content` SET `img` = "'.$res.'" WHERE `user` = '.$id;
						$img = new Getdir($id,$category);
						$dir = $img->dir_img();//图片目录
					}elseif($tag === 3){
						$sql = 'UPDATE `ziliao` SET `logo` = "'.$res.'" WHERE `uid` = '.$id;
						$img = new Getdir($id);
						$dir = $img->dir_img();//图片目录
					}elseif($tag === 4){
						$sql = 'UPDATE `users` SET `img` = "'.$res.'" WHERE `id` = '.$id;
						$img = new Getdir($id);
						$dir = $img->dir_img();//图片目录
					}
					$result = $mod->exe_dml($sql);
					if($result !== false){
						$state1 = Image::del($dir['img'].$img);
						$state2 = Image::del($dir['swap'].'thump_'.$img);
						$state3 = Image::del($dir['img'].'app_'.$img);
						$state4 = Image::del($dir['swap'].'app_thump_'.$img);
						if($state1 === false){
							$info = array(
									'status' =>0,
									'info'   =>'pc图片删除失败，请检查目录是否正确'
									);
							echo json_encode($info);
							exit();							
						}elseif($state2 === false){
							$info = array(
									'status' =>0,
									'info'   =>'pc缩略图删除失败，请检查目录是否正确'
							);
							echo json_encode($info);
							exit();
						}elseif($state3 === false){
							$info = array(
									'status' =>0,
									'info'   =>'app图片删除失败，请检查目录是否正确'
							);
							echo json_encode($info);
							exit();
						}elseif($state4 === false){
							$info = array(
									'status' =>0,
									'info'   =>'app缩略图删除失败，请检查目录是否正确'
							);
							echo json_encode($info);
							exit();
						}else{
							$directory = 'tem/upimg';
							Image::upload($directory);
						}
						
					}else{
						$info = array(
								'status' =>0,
								'info'   =>'数据库图片删除失败'
								);
						echo json_encode($info);
						exit();
					}
				}else{
					$directory = 'tem/upimg';
					Image::upload($directory);
				}
				
			}else{
				$directory = 'tem/upimg';
				Image::upload($directory);
			}
		}else{
			$info =array(
					'status' =>0,
					'info'   =>'没有设置用户id或者用户id为空'
					);			
			die(json_encode($info));
		}
	}
	/**
	* 将tem文件里的图片移动到对应目录
	* @date: 2015-8-19
	* @author: 赵坤
	* @param: $imagedir=array OR string & $tardir = string
	* @return: boolean
	*/   
	public static function move($imagedir = '',$tardir = '',$thumbdir = '',$appdir = '',$appthumbdir = '', $imgname = ''){
		if(is_array($imagedir) && count($imagedir) > 0){
			foreach($imgname as $v){
				$dir = $imagedir.$v;
				$pcimg = $tardir.$v;
				$appimg = $appdir.$v;
				$thumppic = 'thump_'. $v;
				$thumpdir = $thumbdir.$thumppic;
				$appthump = $appthumbdir.$thumppic;
				$thump = Image::getImage();
				$thump->resizeimage($dir, 506, 350, $pcimg, 100, 1);
				$thump->resizeimage($dir, 300, 300, $appimg, 100, ''); //待定手机端的尺寸
				$thump->resizeimage($dir, 80, 80, $thumpdir, 100, '');
				$thump->resizeimage($dir, 300, 300, $appthump, 100, '');
			}
			return ;
		}elseif(!empty($imagedir)){
			$imagename = trim($imgname);
			$dir = $imagedir.$imagename;
			$pcimg = $tardir.$imagename;
			$appimg = $appdir.$imagename;
			$thumppic = 'thump_'. $imagename;
			$thumpdir = $thumbdir.$thumppic;
			$appthump = $appthumbdir.$thumppic;
			
			$thump = Image::getImage();
			$thump->resizeimage($dir, 506, 350, $pcimg, 100, 1);
			$thump->resizeimage($dir, 80, 80, $appimg, 100, ''); //待定手机端的尺寸
			$thump->resizeimage($dir, 80, 80, $thumpdir, 100, '');
			$thump->resizeimage($dir, 300, 300, $appthump, 100, '');
			return ;
		}else{
			return false;
		}
	}
	
	/**
	* 生成缩略图
	* @date: 2015-8-19
	* @author: zhaokun
	* @return: null
	*/
	public function resizeimage($img='', $wid='', $hei='', $dstpath = '', $quality = 100, $c = '') {
		$this->srcimg = $img;
		$this->resize_width = $wid;
		$this->resize_height = $hei;
		$this->cut = $c;
		$this->quality = $quality;
		$this->type = strtolower ( substr ( strrchr ( $this->srcimg, '.' ), 1 ) ); // 图片的类型
		$this->init_img (); // 初始化图象
		$this->dstimg = $dstpath ; // 目标图象地址
		@$this->width = imagesx ( $this->im );
		@$this->height = imagesy ( $this->im );
		$this->newimg (); // 生成图象
		@ImageDestroy ( $this->im );
	}
	/**
	* 生成缩略图
	* @date: 2015-8-19
	* @author: zhaokun
	* @return: null
	*/
	private function newimg() {
		$resize_ratio = ($this->resize_width) / ($this->resize_height); // 改变后的图象的比例
		@$ratio = ($this->width) / ($this->height); // 实际图象的比例
		if ($this->cut) { // 裁图
// 			if ($img_func === 'imagepng' && (str_replace ( '.', '', PHP_VERSION ) >= 512)) { // 针对php版本大于5.12参数变化后的处理情况
// 				$quality = 9;
// 			}
			if ($ratio >= $resize_ratio) { // 高度优先
				$newimg = imagecreatetruecolor ( $this->resize_width, $this->resize_height );
				@imagecopyresampled ( $newimg, $this->im, 0, 0, 0, 0, $this->resize_width, $this->resize_height, (($this->height) * $resize_ratio), $this->height );
				@imagejpeg ( $newimg, $this->dstimg, $this->quality );
				@imagedestroy($newimg);
			}elseif ($ratio < $resize_ratio) { // 宽度优先
				$newimg = imagecreatetruecolor ( $this->resize_width, $this->resize_height );
				@imagecopyresampled ( $newimg, $this->im, 0, 0, 0, 0, $this->resize_width, $this->resize_height, $this->width, (($this->width) / $resize_ratio) );
				@imagejpeg ( $newimg, $this->dstimg, $this->quality );
				@imagedestroy($newimg);
			}
		} else { // 不裁图
			if ($ratio >= $resize_ratio) {
				$newimg = imagecreatetruecolor ( $this->resize_width, ($this->resize_width) / $ratio );
				@imagecopyresampled ( $newimg, $this->im, 0, 0, 0, 0, $this->resize_width, ($this->resize_width) / $ratio, $this->width, $this->height );
				@imagejpeg ( $newimg, $this->dstimg, $this->quality );
				@imagedestroy($newimg);
			}elseif ($ratio < $resize_ratio) {
				@$newimg = imagecreatetruecolor ( ($this->resize_height) * $ratio, $this->resize_height );
				@imagecopyresampled ( $newimg, $this->im, 0, 0, 0, 0, ($this->resize_height) * $ratio, $this->resize_height, $this->width, $this->height );
				@imagejpeg ( $newimg, $this->dstimg, $this->quality );
				@imagedestroy($newimg);
			}
		}
	}
	/**
	* 初始化图象
	* @date: 2015-8-18
	* @author: zhaokun
	* @return: null
	*/
	private function init_img() { 
		if ($this->type == 'jpg' || $this->type == 'jpeg') {
			$this->im = imagecreatefromjpeg ( $this->srcimg );
		}elseif ($this->type == 'gif') {
			$this->im = imagecreatefromgif ( $this->srcimg );
		}elseif ($this->type == 'png') {
			$this->im = imagecreatefrompng ( $this->srcimg );
		}elseif ($this->type == 'wbm') {
			@$this->im = imagecreatefromwbmp ( $this->srcimg );
		}elseif ($this->type == 'bmp') {
			$this->im = $this->ImageCreateFromBMP ( $this->srcimg );
		}
		return ;
	}
	/**
	* 图象目标地址(暂时废弃)
	* @date: 2015-8-18
	* @author: zhaokun
	* @return: string
	*/
// 	private function dst_img($dstpath) { 
// 		$full_length = strlen ( $this->srcimg );
// 		$type_length = strlen ( $this->type );
// 		$name_length = $full_length - $type_length;
// 		$name = substr ( $this->srcimg, 0, $name_length - 1 );
// 		$this->dstimg = $dstpath.$name;
// 	}
	/**
	* 自定义函数处理bmp图片
	* @date: 2015-8-18
	* @author: zhaokun
	* @return: json
	*/
	private function ImageCreateFromBMP($filename) { 
		if (! $f1 = fopen ( $filename, "rb" ))
			returnFALSE;
		$FILE = unpack ( "vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread ( $f1, 14 ) );
		if ($FILE ['file_type'] != 19778)
			returnFALSE;
		$BMP = unpack ( 'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important', fread ( $f1, 40 ) );
		$BMP ['colors'] = pow ( 2, $BMP ['bits_per_pixel'] );
		if ($BMP ['size_bitmap'] == 0)
			$BMP ['size_bitmap'] = $FILE ['file_size'] - $FILE ['bitmap_offset'];
		$BMP ['bytes_per_pixel'] = $BMP ['bits_per_pixel'] / 8;
		$BMP ['bytes_per_pixel2'] = ceil ( $BMP ['bytes_per_pixel'] );
		$BMP ['decal'] = ($BMP ['width'] * $BMP ['bytes_per_pixel'] / 4);
		$BMP ['decal'] -= floor ( $BMP ['width'] * $BMP ['bytes_per_pixel'] / 4 );
		$BMP ['decal'] = 4 - (4 * $BMP ['decal']);
		if ($BMP ['decal'] == 4)
			$BMP ['decal'] = 0;
		$PALETTE = array ();
		if ($BMP ['colors'] < 16777216) {
			$PALETTE = unpack ( 'V' . $BMP ['colors'], fread ( $f1, $BMP ['colors'] * 4 ) );
		}
		$IMG = fread ( $f1, $BMP ['size_bitmap'] );
		$VIDE = chr ( 0 );
		$res = imagecreatetruecolor ( $BMP ['width'], $BMP ['height'] );
		$P = 0;
		$Y = $BMP ['height'] - 1;
		while ( $Y >= 0 ) {
			$X = 0;
			while ( $X < $BMP ['width'] ) {
				if ($BMP ['bits_per_pixel'] == 24)
					$COLOR = unpack ( "V", substr ( $IMG, $P, 3 ) . $VIDE );
				elseif ($BMP ['bits_per_pixel'] == 16) {
					$COLOR = unpack ( "n", substr ( $IMG, $P, 2 ) );
					$COLOR [1] = $PALETTE [$COLOR [1] + 1];
				} elseif ($BMP ['bits_per_pixel'] == 8) {
					$COLOR = unpack ( "n", $VIDE . substr ( $IMG, $P, 1 ) );
					$COLOR [1] = $PALETTE [$COLOR [1] + 1];
				} elseif ($BMP ['bits_per_pixel'] == 4) {
					$COLOR = unpack ( "n", $VIDE . substr ( $IMG, floor ( $P ), 1 ) );
					if (($P * 2) % 2 == 0)
						$COLOR [1] = ($COLOR [1] >> 4);
					else
						$COLOR [1] = ($COLOR [1] & 0x0F);
					$COLOR [1] = $PALETTE [$COLOR [1] + 1];
				} elseif ($BMP ['bits_per_pixel'] == 1) {
					$COLOR = unpack ( "n", $VIDE . substr ( $IMG, floor ( $P ), 1 ) );
					if (($P * 8) % 8 == 0)
						$COLOR [1] = $COLOR [1] >> 7;
					elseif (($P * 8) % 8 == 1)
						$COLOR [1] = ($COLOR [1] & 0x40) >> 6;
					elseif (($P * 8) % 8 == 2)
						$COLOR [1] = ($COLOR [1] & 0x20) >> 5;
					elseif (($P * 8) % 8 == 3)
						$COLOR [1] = ($COLOR [1] & 0x10) >> 4;
					elseif (($P * 8) % 8 == 4)
						$COLOR [1] = ($COLOR [1] & 0x8) >> 3;
					elseif (($P * 8) % 8 == 5)
						$COLOR [1] = ($COLOR [1] & 0x4) >> 2;
					elseif (($P * 8) % 8 == 6)
						$COLOR [1] = ($COLOR [1] & 0x2) >> 1;
					elseif (($P * 8) % 8 == 7)
						$COLOR [1] = ($COLOR [1] & 0x1);
					$COLOR [1] = $PALETTE [$COLOR [1] + 1];
				} else
					returnFALSE;
				imagesetpixel ( $res, $X, $Y, $COLOR [1] );
				$X ++;
				$P += $BMP ['bytes_per_pixel'];
			}
			$Y --;
			$P += $BMP ['decal'];
		}
		fclose ( $f1 );
		return $res;
	}
	
	/**
	* 上传图片
	* @date: 2015-8-18
	* @author: zhaokun
	* @return: json
	*/
    public static function upload($directory = ''){
    	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	header("Cache-Control: post-check=0, pre-check=0", false);
    	header("Pragma: no-cache");
    	header("content-type:text/html; charset=utf-8");
    	$targetDir = empty($directory) ? 'tem' : trim($directory);
    	$fileExt = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
    	$filename = time().rand(10000,99999);
    	if (!file_exists($targetDir)) {
    		@mkdir($targetDir,0777,true);
    	}
    	
    	if (in_array(strtolower($fileExt),array('gif','jpg','jpeg','png')) && $_FILES["file"]["size"] < 800000){
    		if ($_FILES["file"]["error"] > 0){
    			$info=array(
    					'status' => 0,
    					'info' => "Return Code: " . $_FILES["file"]["error"]
    			);
    			echo json_encode($info);
    			exit();
    		}else{
    			if (file_exists($targetDir.'/'. $_FILES["file"]["name"])){
    				$info=array(
    						'status' => 0,
    						'info' => "alreday exists".$_FILES["file"]["name"] . " already exists. "
    				);
    				echo json_encode($info);
    				exit();
    			}else{
    				if(is_uploaded_file($_FILES["file"]["tmp_name"])){
    					$picname = $filename.'.'.$fileExt;
    					$dir = $targetDir.'/'.$picname;
    					$status = move_uploaded_file($_FILES["file"]["tmp_name"],$dir);
//     					$thumppic = 'thump_'. $picname;
//     					$thumpdir = $targetDir.'/'.$thumppic; 
//     					$thump = Image::getImage();
//     					$thump->resizeimage($dir, $width, $height, $thumpdir, $quality, $cut);
    				}else{
    					$info=array(
    							'status' => 0,
    							'info' => 'tmp_name not exits'
    					);
    					echo json_encode($info);
    					exit();
    				}
    				if(!$status){
    					$info=array(
    							'status' => 0,
    							'info' => 'failed 保存失败 （stored failed）'
    					);
    					echo json_encode($info);
    					exit();
    	
    				}else{
    					return  $picname;
    				}
    			}
    		}
    	}else{
    		$info = array(
    				'status' => 0,
    				'info' => 'not fine 文件类型不匹配或者文件大小超过限制大小（type and size is not fine）'
    		);
    		echo json_encode($info);
    		exit();
    	}
    }
}