<?php

class indexAction extends Controller {
      public function  index(){
      	$this->assign('hello','hello');
      	$this->display('Home/hello');
      }
      public function test(){
      	echo 'zhaokun';
      }
}

?>