<?php

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\main_old2;

class binaryopTest extends TestCase{
	public const TYPE_TRUE = "bool(true)";
	public const TYPE_FALSE = "bool(false)";

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
				'var_dump(20 + 30);',
				'50',
			],
			[
				'var_dump(20 * 30);',
				'600',
			],
			[
				'var_dump(20 / 20);',
				'1',
			],
			[
				'var_dump(20 - 30);',
				'-10',
			],
			[
				'var_dump(1 & 2);',
				'0',
			],
			[
				'var_dump(1 | 2);',
				'3',
			],
			[
				'var_dump(1 ^ 2);',
				'3',
			],
			[
				'var_dump(1 && 1);',
				'1',
			],
			[
				'var_dump(1 && 0);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(0 && 1);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(0 && 0);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 || 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 || 0);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(0 || 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(0 || 0);',
				self::TYPE_FALSE,
			],
			//code::COALESCE
			[
				'var_dump("1"."2");',
				'12',
			],
			[
				'var_dump((1).(2));',
				'12',
			],
			[
				'var_dump(1 == 2);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 == 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 == "1");',
				self::TYPE_TRUE,
			],
			[
				'var_dump(2 > 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 > 2);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 > 1);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(2 >= 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 >= 2);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 >= 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 === 2);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 === 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 === "1");',
				self::TYPE_FALSE,
			],

			[
				'var_dump(0 and 0);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 and 0);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 and 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 and "1");',
				self::TYPE_TRUE,
			],

			[
				'var_dump(0 or 0);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 or 0);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(0 or 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 or 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 or "1");',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 or "1");',
				self::TYPE_TRUE,
			],
			[
				'var_dump(0 xor 0);',
				self::TYPE_FALSE
			],
			[
				'var_dump(0 xor 1);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 xor 0);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 xor 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump(3 % 2);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 != 2);',
				self::TYPE_TRUE
			],
			[
				'var_dump(2 != 1);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 != 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump("1" != 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump(1 !== 2);',
				self::TYPE_TRUE
			],
			[
				'var_dump(2 !== 1);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 !== 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump("1" !== 1);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 << 2);',
				'4'
			],
			[
				'var_dump(2 ** 3);',
				'8'
			],
			[
				'var_dump(2 >> 1);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 < 2);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 < 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump(2 < 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump(1 <= 2);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 <= 1);',
				self::TYPE_TRUE
			],
			[
				'var_dump(2 <= 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump(1 <=> 2);',
				'-1'
			],
			[
				'var_dump(2 <=> 1);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 <=> 1);',
				self::TYPE_FALSE
			],
		];
	}
}