<?php

namespace purser;

use Throwable;

class phpFinalException extends \RuntimeException{
	/** @var ?int $line */
	public $virtualline = null;
	/** @var ?string $file */
	public $virtualfile = null;

	/**
	 * @param string $message
	 * @param ?int $line
	 * @param ?string $file
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct($message, $line = null, $file = null, $code = 0, Throwable $previous = null){
		$this->virtualline = $line;
		$this->virtualfile = $file;
		parent::__construct($message, $code, $previous);
	}

	public function getIssue() : string{
		return "PHP Fatal error: ".$this->getMessage();
	}

	public function toString() : string{
		$issuse = $this->getIssue();
		if($this->virtualfile !== null){
			$issuse .= " in ".$this->virtualfile;
		}
		if($this->virtualline !== null){
			$issuse .= " on ".$this->virtualline;
		}
		return $issuse;
	}
}