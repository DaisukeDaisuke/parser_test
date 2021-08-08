<?php

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\main_old2;

class echoTest extends TestCase{
	/**
	 * (selectedBettingTable)->isInsideHangingBox();
	 * 関数に関します、テストにてございます...
	 *
	 * @dataProvider providetestisInsideHangingBox
	 * @param string $code
	 * @param string $output1
	 * @return void
	 */
	public function testisInsideHangingBox(string $code, string $output1){
		$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
		$stmts = $parser->parse("<?php\n".$code);

		if($stmts === null){
			throw new \RuntimeException("phpParser crashed");
		}

		$main_old = new main_old2();
		$output = $main_old->execStmts($stmts);

		//var_dump($code, $main_old->hexentities($output));

		ob_start();
		$decoder = new decoder();
		$decoder->decode($output);
		$log = ob_get_clean();

		if($log === false){
			throw new \RuntimeException("The output is empty.");
		}

		//var_dump($code,$stmts,$output,$log);

		self::assertEquals(trim($output1), trim($log));
	}

	/**
	 * @return string[][]
	 */
	public function providetestisInsideHangingBox(): array{
		return [
			[
				'echo "test print";',
				'test print',
			],
			[
				'echo ((2*1+1)+(2/1+3));',
				'8',
			],
			[
				'echo ((2*1+1)*(2/1+3));',
				'15',
			],
			[
				'echo ((2*1+1000000)*(2/1+3));',
				'5000010',
			],
			[
				'echo ((2*1+1)+(2/1+3))."_test";',
				'8_test',
			],
/*			[
				'var_dump(1 === 0);',
				'bool(false)',
			],
			[
				'var_dump(1 === 1);',
				'bool(true)',
			],
			[
				'var_dump(true === false);',
				'bool(false)',
			],
			[
				'var_dump(true === true);',
				'bool(true)',
			],*/
			[
				"echo ((2*1+1)+(2/1+3)-(2/(5*6+20)*(5*(6/2))))+7.4;",
				"14.8"
			],
			[
				'echo "100","_","200";',
				"100_200"
			],
			[
				'echo (50+50),"_",((100+100+100)+200);',
				"100_500"
			],
			[
				'echo ((100+100+100)+200),"_",(50+50);',
				"500_100"
			],
		];
	}
}
