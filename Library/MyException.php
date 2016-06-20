<?php
/**
 * FireFrame MyException 自定义异常处理类
 * @category   Fire
 * @package  Fire
 * @subpackage  Library
 * @author   zhaokun <739576080@qq.com>
 * @time     2014/11/9
 */
class MyException extends Exception{
	/**
	 * 异常类型
	 * @var string
	 * @access private
	 */
	private $type;
	//自定义附加信息
	private $ext;
	/**
	 * 构造函数
	 * @access public
	 * @param string $message  异常信息
	 */
	public function __construct($message,$code=0,$extra=false) {
		parent::__construct($message,$code);//执行父类构造函数
		$this->type = get_class($this); //
		$this->ext = $extra;
	}
	/**
     * 异常输出 所有异常处理类均通过__toString方法输出错误
     * 每次异常都会写入系统日志
     * 该方法可以被子类重载
     * @access public
     * @return array
     */
	public function __toString(){
		$trace=$this->getTrace();
		if($this->extra)
			// 通过throw_exception抛出的异常要去掉多余的调试信息
			array_shift($trace);
		$this->class    =   isset($trace[0]['class'])?$trace[0]['class']:'';
		$this->function =   isset($trace[0]['function'])?$trace[0]['function']:'';
		$this->file     =   $trace[0]['file'];
		$this->line     =   $trace[0]['line'];
		$file           =   file($this->file);
		$traceInfo      =   '';
		$time = date('y-m-d H:i:m');
		foreach($trace as $t) {
			$traceInfo .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
			$traceInfo .= $t['class'].$t['type'].$t['function'].'(';
			$traceInfo .= implode(', ', $t['args']);
			$traceInfo .=")\n";
		}
		$error['message']   = $this->message;
		$error['type']      = $this->type;
		$error['detail']    = L('_MODULE_').'['.MODULE_NAME.'] '.L('_ACTION_').'['.ACTION_NAME.']'."\n";
		$error['detail']   .=   ($this->line-2).': '.$file[$this->line-3];
		$error['detail']   .=   ($this->line-1).': '.$file[$this->line-2];
		$error['detail']   .=   '<font color="#FF6600" >'.($this->line).': <strong>'.$file[$this->line-1].'</strong></font>';
		$error['detail']   .=   ($this->line+1).': '.$file[$this->line];
		$error['detail']   .=   ($this->line+2).': '.$file[$this->line+1];
		$error['class']     =   $this->class;
		$error['function']  =   $this->function;
		$error['file']      = $this->file;
		$error['line']      = $this->line;
		$error['trace']     = $traceInfo;
		
		// 记录 Exception 日志
		if(C('LOG_EXCEPTION_RECORD')) {
// 			Log::Write('('.$this->type.') '.$this->message);
		}
		return $error ;
		
	}
		public static function fatalError(){
			if ($e = error_get_last()) {
				MyException::showError($e['type'], $e['message'], $e['file'], $e['line']);
			}
			
		}
		public static function showError( $errno ,  $errstr ,  $errfile ,  $errline){
			switch ($errno){
				case E_ERROR:
	            case E_PARSE:
		        case E_CORE_ERROR:
		        case E_COMPILE_ERROR:
		        case E_USER_ERROR:
				echo "$errno<br/>$errstr<br/>$errfile<br/>$errline<br/>";
				break;
				case E_STRICT:
				case E_USER_WARNING:
				case E_USER_NOTICE:
				default:
					$err = array(
					'errno'=>$errno,
					'errstr'=> $errstr ,
					'errfile'=>$errfile,
					'errline'=>$errline 
					);
					hack($err);
					break;
			}
		}
		public static function showException($exception){
			$trace=$exception->getTrace();
			$err=array(
				"file"=>$trace[0]['file'],
				"line"=>$trace[0]['line'],
				"function"=>$trace[0]['function'],
				"class"=>$trace[0]['class'],
				"type"=>$trace[0]['type'],
				"args"=>$trace[0]['args'],
				'message'=>$exception->message,
					);
			if(C('LOG_EXCEPTION_RECORD')) {
				// 			Log::Write('('.$this->type.') '.$this->message);
			}
			hack($err);
			//showInfo($trace);
// 			echo $exception->message;
			
		}
	
}