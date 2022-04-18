<?php

namespace purser\exception;

/**
 * __halt_compiler();
 */
class HaltCompilerException extends \RuntimeException{
	/** @var string $remaining */
	public $remaining;

	public function __construct(string $remaining){
		$this->remaining = $remaining;
		parent::__construct("", 0, null);
	}

	public function getRemaining() : string{
		return $this->remaining;
	}


}