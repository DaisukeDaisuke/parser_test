<?php

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\main_old2;

class assignopTest extends TestCase{
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
				'$a = 1;
				$a += 2;
				echo $a;',
				'3',
			],
			[
				'$a = 1;
				$a += (1+1);
				echo $a;',
				'3',
			],
			[
				'$a = 1;
				$a -= 2;
				echo $a;',
				'-1',
			],
			[
				'$a = 1;
				$a -= (1+1);
				echo $a;',
				'-1',
			],
			[
				'$a = 5;
				$a *= 2;
				echo $a;',
				'10',
			],
			[
				'$a = 5;
				$a *= (1+1);
				echo $a;',
				'10',
			],
			[
				'$a = 10;
				$a /= 2;
				echo $a;',
				'5',
			],
			[
				'$a = 10;
				$a /= (1+1);
				echo $a;',
				'5',
			],
			[
				'$a = 10;
				$a %= 9;
				echo $a;',
				'1',
			],
			[
				'$a = 10;
				$a %= (3+6);
				echo $a;',
				'1',
			],
			[
				'$a = 10;
				$a **= 2;
				echo $a;',
				'100',
			],
			[
				'$a = 10;
				$a **= (1+1);
				echo $a;',
				'100',
			],
		];
	}
}