<?php

namespace purser;

class ExitException extends \RuntimeException{
	/** @var int|string $messagecode */
	private $messagecode;

	/**
	 * @param scalar $message
	 */
	public function __construct($message){
		if(is_int($message)){
			$this->messagecode = $message;
		}else{
			$this->messagecode = (string) $message;
		}

		parent::__construct("exit code: ".$message, 0, null);
	}

	/**
	 * @return int|string
	 */
	public function getMessagecode(){
		return $this->messagecode;
	}

	/**
	 * @param bool $canExit
	 * @return int|string|never-return
	 */
	public function exec(bool $canExit = true){
		if(!$canExit){
			if(!is_int($this->messagecode)){
				echo $this->messagecode;
				return 0;
			}
			return $this->messagecode;
		}
		exit($this->messagecode);
	}
}