<?php

namespace purser;

class phpFinalException extends \RuntimeException{
	public function getIssue(): string{
		return "PHP Fatal error: ".$this->getMessage();
	}
}