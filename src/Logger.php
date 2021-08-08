<?php

namespace purser;

class Logger{
	public const TYPE_MESSAGE = 0;
	public const TYPE_LEVEL = 1;


	/**
	 * @var bool $errorSuppress
	 */
	private $errorSuppress = false;

	public const WARNING = 2;
	public const FINAL = 3;
	/**
	 * @var array<int, array{string, int}> $logs
	 */
	public $logs = [];

	/**
	 * @var bool $is_phpunit
	 */
	public $is_phpunit;

	/**
	 * @param bool $is_phpunit
	 */
	public function __construct(bool $is_phpunit = false){
		$this->is_phpunit = $is_phpunit;
	}

	public function log(string $message, int $level): void{
		switch($level){
			case self::WARNING:
				if($this->isErrorSuppress()) return;
				$error = "php compiler warning: ".$message;
				$this->echo($error.PHP_EOL);
				$this->logs[] = [$error, $level];
				break;
		}
	}

	public function echo(string $message): void{
		if($this->is_phpunit) return;
		echo $message;
	}

	public function warning(string $message): string{
		$this->log($message, self::WARNING);
		return "";
	}

	public function setErrorSuppress(bool $errorSuppress): void{
		$this->errorSuppress = $errorSuppress;
	}

	public function isErrorSuppress(): bool{
		return $this->errorSuppress;
	}

	/**
	 * @return array<int, array{string, int}>
	 */
	public function getLogs(): array{
		return $this->logs;
	}

	public function final(\RuntimeException $exception): void{
		$this->log("PHP Fatal error:".$exception->getMessage(), self::FINAL);
	}
}