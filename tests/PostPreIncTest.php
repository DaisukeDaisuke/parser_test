<?php


use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\main_old2;

class PostPreIncTest extends TestCase{
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

		//var_dump($code,$stmts,$output,$log);

		if($log === false){
			throw new \RuntimeException("The output is empty.");
		}

		self::assertEquals(trim($output1), trim($log));
	}

	/**
	 * @return string[][]
	 */
	public function providetestisInsideHangingBox() : array{
		return [
			[
				'$i=100;
				$i++;
				echo $i;',
				'101',
			],
			[
				'$i=100;
				++$i;
				echo $i;',
				'101',
			],
			[
				'$i=100;
				echo ++$i;',
				'101',
			],
			[
			'$i=100;
				$i--;
				echo $i;',
				'99',
			],
			[
				'$i=100;
				--$i;
				echo $i;',
				'99',
			],
			[
				'$i=100;
				echo --$i;',
				'99',
			],
			[
				'$i=100;echo $i++;echo $i++;echo $i++;',
				'100101102'
			],
			[
				'$i=100;echo $i--;echo $i--;echo $i--;',
				'1009998'
			],
			[
				'$i=100;
				print ++$i;',
				'101'
			],
			[
				'$i=100;
				print --$i;',
				'99'
			],
			[
				'$i=100;
				echo ++$i;',
				'101'
			],
			[
				'$i=100;
				echo --$i;',
				'99'
			],

		];
	}
}