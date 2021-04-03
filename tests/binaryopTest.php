<?php

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\main_old2;

class binaryopTest extends TestCase{
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
				'echo 20 + 30;',
				'50',
			],
			[
				'echo 20 * 30;',
				'600',
			],
			[
				'echo 20 / 20;',
				'1',
			],
			[
				'echo 20 - 30;',
				'-10',
			],
			[
				'echo 1 & 2;',
				'0',
			],
			[
				'echo 1 | 2;',
				'3',
			],
			[
				'echo 1 ^ 2;',
				'3',
			],
			[
				'echo 1 && 1;',
				'1',
			],
			[
				'echo 1 && 0;',
				'0',
			],
			[
				'echo 0 && 1;',
				'0',
			],
			[
				'echo 0 && 0;',
				'0',
			],
			[
				'echo 1 || 1;',
				'1',
			],
			[
				'echo 1 || 0;',
				'1',
			],
			[
				'echo 0 || 1;',
				'1',
			],
			[
				'echo 0 || 0;',
				'0',
			],
			//code::COALESCE
			[
				'echo "1"."2";',
				'12',
			],
			[
				'echo (1).(2);',
				'12',
			],
			[
				'echo 1 == 2;',
				'0',
			],
			[
				'echo 1 == 1;',
				'1',
			],
			[
				'echo 1 == "1";',
				'1',
			],
			[
				'echo 2 > 1;',
				'1',
			],
			[
				'echo 1 > 2;',
				'0',
			],
			[
				'echo 1 > 1;',
				'0',
			],
			[
				'echo 2 >= 1;',
				'1',
			],
			[
				'echo 1 >= 2;',
				'0',
			],
			[
				'echo 1 >= 1;',
				'1',
			],
			[
				'echo 1 === 2;',
				'0',
			],
			[
				'echo 1 === 1;',
				'1',
			],
			[
				'echo 1 === "1";',
				'0',
			],

			[
				'echo 0 and 0;',
				'0',
			],
			[
				'echo 1 and 0;',
				'0',
			],
			[
				'echo 1 and 1;',
				'1',
			],
			[
				'echo 1 and "1";',
				'1',
			],

			[
				'echo 0 or 0;',
				'0',
			],
			[
				'echo 1 or 0;',
				'1',
			],
			[
				'echo 0 or 1;',
				'1',
			],
			[
				'echo 1 or 1;',
				'1',
			],
			[
				'echo 1 or "1";',
				'1',
			],
			[
				'echo 1 or "1";',
				'1',
			],
			[
				'echo 0 xor 0;',
				'0'
			],
			[
				'echo 0 xor 1;',
				'1'
			],
			[
				'echo 1 xor 0;',
				'1'
			],
			[
				'echo 1 xor 1;',
				'0'
			],
			[
				'echo 3 % 2;',
				'1'
			],
			[
				'echo 1 != 2;',
				'1'
			],
			[
				'echo 2 != 1;',
				'1'
			],
			[
				'echo 1 != 1;',
				'0'
			],
			[
				'echo "1" != 1;',
				'0'
			],
			[
				'echo 1 !== 2;',
				'1'
			],
			[
				'echo 2 !== 1;',
				'1'
			],
			[
				'echo 1 !== 1;',
				'0'
			],
			[
				'echo "1" !== 1;',
				'1'
			],
			[
				'echo 1 << 2;',
				'4'
			],
			[
				'echo 2 ** 3;',
				'8'
			],
			[
				'echo 2 >> 1;',
				'1'
			],
			[
				'echo 1 < 2;',
				'1'
			],
			[
				'echo 1 < 1;',
				'0'
			],
			[
				'echo 2 < 1;',
				'0'
			],
			[
				'echo 1 <= 2;',
				'1'
			],
			[
				'echo 1 <= 1;',
				'1'
			],
			[
				'echo 2 <= 1;',
				'0'
			],
			[
				'echo 1 <=> 2;',
				'-1'
			],
			[
				'echo 2 <=> 1;',
				'1'
			],
			[
				'echo 1 <=> 1;',
				'0'
			],
		];
	}
}