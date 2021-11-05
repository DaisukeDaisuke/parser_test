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
	public const WARNING73 = 2;
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
	 * @var ?string $display_program
	 */
	public $display_program;

	/**
	 * @param bool $is_phpunit
	 * @param string|null $display_program
	 */
	public function __construct(bool $is_phpunit = false, ?string $display_program = null){
		$this->is_phpunit = $is_phpunit;
		$this->display_program = $display_program;
	}

	public function log(string $message, int $level, ?int $line = null) : void{
		switch($level){
			case self::WARNING:
				if($this->isErrorSuppress()) return;
				$error = "php compiler warning: ".$message;
				$this->logs[] = [$error, $level];
				if($line !== null){
					if($this->display_program !== null){
						$error .= " in ".$this->display_program;
					}
					$error .= " on line ".$line;
				}
				$this->echo($error.PHP_EOL);
				break;
		}
	}

	public function echo(string $message) : void{
		if($this->is_phpunit) return;
		echo $message;
	}

	public function warning(string $message, ?int $line = null) : string{
		$this->log($message, self::WARNING, $line);
		return "";
	}

	public function warning73(string $message, ?int $line = null) : string{
		$this->log($message, self::WARNING73, $line);
		return "";
	}

	public function setErrorSuppress(bool $errorSuppress) : void{
		$this->errorSuppress = $errorSuppress;
	}

	public function isErrorSuppress() : bool{
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