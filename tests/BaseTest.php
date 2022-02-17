<?php

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\ExitException;
use purser\Logger;
use purser\main_old2;
use purser\phpFinalException;

abstract class BaseTest extends TestCase{
	/**
	 * (selectedBettingTable)->isInsideHangingBox();
	 * 関数に関します、テストにてございます...
	 *
	 * @dataProvider providetestisInsideHangingBox
	 * @param string $code
	 * @param string $output1
	 * @param string|null $compilerfinalerror
	 * @param string|int|null $exitcode
	 * @param string[]|null $logs
	 * @return void
	 */
	public function testisInsideHangingBox(string $code, string $output1, ?string $compilerfinalerror = null, $exitcode = null, ?array $logs = null){
		$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
		$stmts = $parser->parse("<?php\n".$code);

		if($stmts === null){
			throw new \RuntimeException("phpParser crashed");
		}

		$main_old = new main_old2(true);
		try{
			$output = $main_old->onexec($stmts);
		}catch(phpFinalException $exception){
			if($compilerfinalerror !== null){
				self::assertSame($exception->getMessage(), $compilerfinalerror);
				return;
			}
			var_dump($exception->getMessage());
			throw new $exception;
		}

		if($compilerfinalerror !== null){
			throw new \RuntimeException("phpFinalException '".$compilerfinalerror."' was not thrown.");
		}
		if($logs !== null){
			foreach($main_old->getLogger()->getLogs() as $key => $log){
				if($log[Logger::TYPE_LEVEL] === Logger::WARNING||$log[Logger::TYPE_LEVEL] === Logger::WARNING73){
					self::assertTrue(isset($logs[$key]), "key ".$key." not found");
					self::assertSame($logs[$key], $log[0]);
				}
			}
			self::assertCount(count($logs), $main_old->getLogger()->getLogs());
		}

		//var_dump($test = opcode_dumper::hexentities($output));

		ob_start();
		$decoder = new decoder();
		try{
			$decoder->decode($output);
		}catch(ExitException $exception){
			self::assertSame($exitcode, $exception->exec(false), "ExitException falled");
		}catch(\RuntimeException $exception){
			ob_get_clean();
			var_dump($code);
			throw $exception;
		}

		$log = ob_get_clean();

		//var_dump($code,$stmts,$output,$log);

		if($log === false){
			throw new \RuntimeException("The output is empty.");
		}

		self::assertSame($this->convert(trim($output1)), $this->convert(trim($log)));
	}

	/**
	 * @return string[][]
	 */
	public function providetestisInsideHangingBox(): array{
		return [
			[
				'target code',
				'expected execution result',
				'expected Compiler Final Error Message',
				'expected exit code',
				[
					'expected logs',
				],
			]
		];
	}

	public function convert(string $string, string $to = "\n") : string{
		return strtr($string, array(
			"\r\n" => $to,
			"\r" => $to,
			"\n" => $to,
		));
	}
}