<?php

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\main_old2;
use purser\opcode_dumper;

class for_Test extends TestCase{
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

		//var_dump($test = opcode_dumper::hexentities($output));

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
	public function providetestisInsideHangingBox(): array{
		return [
			[
				'for($i = 1; $i <= 10; $i++){
					echo $i;
				}',
				'12345678910'
			],
			[
				'for($i=0;false;$i++){
					echo 1;
				}
				echo $i;',
				'0',
			],
			[
				'for(;false;){
					echo 1;
				}
				echo 0;',
				'0',
			],
			[
				'for(;false,false,false,false,false;){
					echo 1;
				}
				echo 0;',
				'0',
			],
			[
				'for ($i = 1, $j = 0; $i <= 10; $j += $i, print $i, $i++);',
				'12345678910',
			],
			[
				'for($i=0; $i<101; $i++){
					if($i%15 === 0){
					    echo "FizzBuzz";
					}else if($i%3 === 0){
					    echo "Fizz";
					}else if($i%5 === 0){
					    echo "Buzz";
					}else{
					    echo $i;
					}
					echo ",";
				}',
				'FizzBuzz,1,2,Fizz,4,Buzz,Fizz,7,8,Fizz,Buzz,11,Fizz,13,14,FizzBuzz,16,17,Fizz,19,Buzz,Fizz,22,23,Fizz,Buzz,26,Fizz,28,29,FizzBuzz,31,32,Fizz,34,Buzz,Fizz,37,38,Fizz,Buzz,41,Fizz,43,44,FizzBuzz,46,47,Fizz,49,Buzz,Fizz,52,53,Fizz,Buzz,56,Fizz,58,59,FizzBuzz,61,62,Fizz,64,Buzz,Fizz,67,68,Fizz,Buzz,71,Fizz,73,74,FizzBuzz,76,77,Fizz,79,Buzz,Fizz,82,83,Fizz,Buzz,86,Fizz,88,89,FizzBuzz,91,92,Fizz,94,Buzz,Fizz,97,98,Fizz,Buzz,',

			],
			[
				'for(;true;){
					echo 1;
					if(true){
						echo 2;
						break;
						echo 3;
					}
					echo 4;
				}
				echo 5;',
				'125'
			],
		];
	}
}