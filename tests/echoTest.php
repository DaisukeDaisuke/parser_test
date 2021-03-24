<?php

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

class echoTest extends TestCase{
	/**
	 * (selectedBettingTable)->isInsideHangingBox();
	 * 関数に関します、テストにてございます...
	 *
	 * @dataProvider providetestisInsideHangingBox
	 * @return void
	 */
	public function testisInsideHangingBox($code,$output1){
		$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
		$stmts = $parser->parse("<?php\n".$code);
		$main_old = new main_old2();
		$output = $main_old->execStmts($stmts);

		//var_dump($code, $main_old->hexentities($output));

		ob_start();
		$decoder = new decoder();
		$decoder->decode($output);
		$log = ob_get_clean();

		//var_dump($code,$stmts,$output,$log);

		self::assertEquals(trim($output1),trim($log));
	}

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
			[
				'echo 1 === 0;',
				'false',
			],
			[
				'echo 1 === 1;',
				'true',
			],
			[
				'echo true === false;',
				'false',
			],
			[
				'echo true === true;',
				'true',
			],
		];
	}
}
