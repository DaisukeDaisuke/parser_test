<?php


use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\main_old2;

class printTest extends TestCase{
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
				'print "test print";',
				'test print',
			],
			[
				'print ((2*1+1)+(2/1+3));',
				'8',
			],
			[
				'print ((2*1+1)*(2/1+3));',
				'15',
			],
			[
				'print ((2*1+1000000)*(2/1+3));',
				'5000010',
			],
			[
				'print ((2*1+1)+(2/1+3))."_test";',
				'8_test',
			],
			/*[
				'print 1 === 0;',
				'false',
			],
			[
				'print 1 === 1;',
				'true',
			],
			[
				'print true === false;',
				'false',
			],
			[
				'print true === true;',
				'true',
			],*/
			[
				"print ((2*1+1)+(2/1+3)-(2/(5*6+20)*(5*(6/2))))+7.4;",
				"14.8"
			],
			[
				'echo (print "100_");',
				"100_1"
			],
			[
				'$i = print "100_";
				print $i;',
				"100_1"
			],
		];
	}
}