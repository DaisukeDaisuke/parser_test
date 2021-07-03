<?php


use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\main_old2;

class assignTest extends TestCase{
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
				'$i = print "test_";
				echo $i;',
				'test_1',
			],
			[
				'$i = print "test_";
				$i = 12;
				echo $i;',
				'test_12',
			],
			[
				'$i = print "test_";
				$i = 12;
				$i = 13;
				echo $i;',
				'test_13',
			],
			[
				'$i = ((200+300)*2);
				echo $i;',
				'1000',
			],
			[
				'$i = ((200+300)*2);
				print $i;',
				'1000',
			],
			[
				'$i = ((200+300)*2);
				$i = $i . "_1";
				echo $i;',
				'1000_1',
			],
			[
				'$i = ((200+300)*2);
				$i = $i . "_1";
				print $i;',
				'1000_1',
			],
			[
				'$i = ((200+300)*2);
				$i = $i + 1000;
				echo $i;',
				'2000',
			],
			[
				'$i = ((200+300)*2);
				$i = $i + 1000;
				print $i;',
				'2000',
			],
			[
				'$i = ((200+300)*2);
				$i = ((200+300)*6);
				$i = ((200+300)*12);
				print $i;',
				'6000',
			],
			[
				'$i = 200;
				$i = $i+300;
				$i = 500+$i;
				echo $i;
				print $i;',
				'10001000',
			],
			[
				'$i = 200;
				$i = $i+$i+$i+$i;
				echo $i;
				print $i;',
				'800800',
			],
			[
				'$i = 100;
				echo $i;
				print $i;
				$i = 200;
				echo $i;
				print $i;
				$i = 300;
				echo $i;
				print $i;',
				'100100200200300300',
			],
			[
				'$i = 100;
				echo $i+1;
				print 1+$i;',
				'101101',
			],
			[
				'$i = 100;
				$i = $i+1;
				echo $i+1;
				print 1+$i;',
				'102102',
			],
			[
				'$i = 100;
				$i = $i;
				echo $i+2;
				$i = $i+6;
				print $i;',
				'102106',
			],
			[
				'$i = 100;
				$j=200;
				$k=$i-$j;
				echo $k;',
				'-100',
			],
			[
				'echo $j=200;',
				'200',
			],
			[
				'print $j=200;',
				'200',
			],
			[
				'$j = print $i=200;
				echo $j;',
				'2001',
			],
			[
				'$j = $i = 100;
				echo $j;',
				'100'
			],
			[
				'$k = $j = $i = 100;
				echo $k;',
				'100'
			],
			[
				'echo $j = $i = 100;',
				'100'
			],
			[
				'echo print $k = print $j = $i = 100;',
				'10011'
			],
			[
				'$i=100;echo $i,100+100,$i;',
				'100200100',
			],
			[
				'echo print $k = print $j = $i = 100,",",$k,",",$i;',
				'10011,1,100'
			],
			[
				'$i=100;$k=200;echo $k,",",$i;',
				'200,100'
			],
		];
	}
}