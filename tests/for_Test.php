<?php

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\main_old2;
use purser\phpFinalException;

class for_Test extends TestCase{
	/**
	 * (selectedBettingTable)->isInsideHangingBox();
	 * 関数に関します、テストにてございます...
	 *
	 * @dataProvider providetestisInsideHangingBox
	 * @param string $code
	 * @param string $output1
	 * @param string|null $compilerfinalerror
	 * @return void
	 */
	public function testisInsideHangingBox(string $code, string $output1, ?string $compilerfinalerror = null){
		$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
		$stmts = $parser->parse("<?php\n".$code);

		if($stmts === null){
			throw new \RuntimeException("phpParser crashed");
		}

		try{
			$main_old = new main_old2();
			$output = $main_old->execStmts($stmts);
		}catch(phpFinalException $exception){
			if($compilerfinalerror !== null){
				self::assertEquals($exception->getMessage(), $compilerfinalerror);
				return;
			}
			throw new $exception;
		}

		if($compilerfinalerror !== null){
			throw new \RuntimeException("phpFinalException '".$compilerfinalerror."' was not thrown.");
		}


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
				'for($i=0; $i<101; $i++){
					$i++;
					echo $i;
					if($i >= 10){
						echo ",";
						break;
						echo ",";
					}
				}
				echo 5;',
				'1357911,5'
			],
			[
				'for($i=0; $i<101; $i++){
					echo $i;
					if($i === 10){
						echo ",";
						break;
						echo ",";
					}
				}
				echo 5;',
				'012345678910,5'
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
			[
				'for(;true;){
					for(;true;){
						echo 1;
						if(true){
							echo 2;
							break 2;
							echo 3;
						}
						echo 4;
					}
				}
				echo 5;',
				'125',
			],
			[
				'for(;true;){
					break 2;
				}
				echo 1;',
				'',
				"Cannot 'break' 2 levels",
			],
			[
				'for(;true;){
					for(;true;){
						break 3;
					}
				}
				echo 1;',
				'',
				"Cannot 'break' 3 levels",
			],
			[
				'for(;true;){
					break -1;
				}
				echo 5;',
				'',
				"'break' operator with non-integer operand is no longer supported",
			],
			[
				'for(;true;){
					break 1.5;
				}
				echo 5;',
				'',
				"'break' operator accepts only positive integers",
			],
			[
				'for(;true;){
					break 1+1;
				}
				echo 5;',
				'',
				"'break' operator with non-integer operand is no longer supported",
			],
			//continue
			[
				'for(;true;){
					continue 2;
				}
				echo 1;',
				'',
				"Cannot 'continue' 2 levels",
			],
			[
				'for(;true;){
					for(;true;){
						continue 3;
					}
				}
				echo 1;',
				'',
				"Cannot 'continue' 3 levels",
			],
			[
				'for(;true;){
					continue -1;
				}
				echo 5;',
				'',
				"'continue' operator with non-integer operand is no longer supported",
			],
			[
				'for(;true;){
					continue 1.5;
				}
				echo 5;',
				'',
				"'continue' operator accepts only positive integers",
			],
			[
				'for(;true;){
					continue 1+1;
				}
				echo 5;',
				'',
				"'continue' operator with non-integer operand is no longer supported",
			],
			[
				'break;',
				'',
				"'break' not in the 'loop' or 'switch' context",
			],
			[
				'continue;',
				'',
				"'continue' not in the 'loop' or 'switch' context",
			],
			[
				'echo 1;
				break;
				echo 2;',
				'',
				"'break' not in the 'loop' or 'switch' context",
			],
			[
				'echo 1;
				continue;
				echo 2;',
				'',
				"'continue' not in the 'loop' or 'switch' context",
			],
			[
				'for($i=0; $i<5; $i++){
					continue;
					echo 1;
				}',
				'',
			],
			[
				'for($i=0; $i<5; $i++){
					if(1===0){
						echo 1;
					}elseif(1===0){
						echo 2;
					}else{
						echo 5;
						continue;
					}
					echo 6;
				}',
				'55555',
			],
			[
				'for($i=0; $i<20; $i++){
					echo $i,",";
					if($i % 2){
						echo ",";
						continue;
						echo 1;
					}
				}
				echo 5;',
				'0,1,,2,3,,4,5,,6,7,,8,9,,10,11,,12,13,,14,15,,16,17,,18,19,,5',
			],
			[
				'for($i=0; $i<6; $i++){
					echo $i,",";
					if($i % 2){
						echo "8";
						continue;
						echo 1;
					}
					if($i % 5){
						echo "9";
						continue;
						echo 2;
					}
					echo ",";
				}
				echo 5;',
				'0,,1,82,93,84,95,85',
			],
			[
				'for($i=0; $i<20; $i++){
					if($i % 2){
						echo ",";
						continue;
					}
					echo $i,",";
				}
				echo 5;',
				'0,,2,,4,,6,,8,,10,,12,,14,,16,,18,,5',
			],
			[
				'for($i=0; $i<101; $i++){
					if($i % 15 === 0){
						echo "FizzBuzz,";
						continue;
					}
					if($i % 3 === 0){
						echo "Fizz,";
						continue;
					}
					if($i % 5 === 0){
						echo "Buzz,";
						continue;
					}
					echo $i.",";
				}',
				'FizzBuzz,1,2,Fizz,4,Buzz,Fizz,7,8,Fizz,Buzz,11,Fizz,13,14,FizzBuzz,16,17,Fizz,19,Buzz,Fizz,22,23,Fizz,Buzz,26,Fizz,28,29,FizzBuzz,31,32,Fizz,34,Buzz,Fizz,37,38,Fizz,Buzz,41,Fizz,43,44,FizzBuzz,46,47,Fizz,49,Buzz,Fizz,52,53,Fizz,Buzz,56,Fizz,58,59,FizzBuzz,61,62,Fizz,64,Buzz,Fizz,67,68,Fizz,Buzz,71,Fizz,73,74,FizzBuzz,76,77,Fizz,79,Buzz,Fizz,82,83,Fizz,Buzz,86,Fizz,88,89,FizzBuzz,91,92,Fizz,94,Buzz,Fizz,97,98,Fizz,Buzz,',
			],
		];
	}
}