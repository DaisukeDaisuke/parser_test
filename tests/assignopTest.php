<?php

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\Logger;
use purser\main_old2;

class assignopTest extends TestCase{
	/**
	 * (selectedBettingTable)->isInsideHangingBox();
	 * 関数に関します、テストにてございます...
	 *
	 * @dataProvider providetestisInsideHangingBox
	 * @param string $code
	 * @param string $output1
	 * @param string[] $errors
	 * @return void
	 */
	public function testisInsideHangingBox(string $code, string $output1, array $errors = []){
		$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
		$stmts = $parser->parse("<?php\n".$code);

		if($stmts === null){
			throw new \RuntimeException("phpParser crashed");
		}
		$main_old = new main_old2(true);
		$output = $main_old->execStmts($stmts);
		foreach($main_old->getLogger()->getLogs() as $key => $array){
			if($array[Logger::TYPE_LEVEL] === Logger::WARNING){
				self::assertEquals(true, isset($errors[$key]),"key ".$key." not found");
				//var_dump($array[Logger::TYPE_MESSAGE]);
				self::assertEquals(true, $errors[$key] === $array[Logger::TYPE_MESSAGE]);
			}
		}
		self::assertEquals(count($errors), count($main_old->getLogger()->getLogs()));
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
	 * @return string[][]|string[][][]
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
			[
				'$a += 0;
				echo $a;',
				'0',
				[
					'php compiler warning: Undefined variable $a. opcode(+= etc): 02. (writeAssignOp)'
				],
			],
			[
				'$b += 0;
				echo $b;',
				'0',
				[
					'php compiler warning: Undefined variable $b. opcode(+= etc): 02. (writeAssignOp)'
				],
			],
			[
				'$b += 2;
				echo $b;',
				'2',
				[
					'php compiler warning: Undefined variable $b. opcode(+= etc): 02. (writeAssignOp)'
				],
			],
			[
				'$b -= 2;
				echo $b;',
				'-2',
				[
					'php compiler warning: Undefined variable $b. opcode(+= etc): 05. (writeAssignOp)'
				],
			],
			[
				'$a += 0;
				$b += 0;
				echo $a,",",$b;',
				'0,0',
				[
					'php compiler warning: Undefined variable $a. opcode(+= etc): 02. (writeAssignOp)',
					'php compiler warning: Undefined variable $b. opcode(+= etc): 02. (writeAssignOp)'
				],
			],
			[
				'@$a += 0;
				$b += 0;
				echo $a,",",$b;',
				'0,0',
				[
					'php compiler warning: Undefined variable $b. opcode(+= etc): 02. (writeAssignOp)'
				],
			],
			[
				'@$a += 0;
				@$b += 0;
				echo $a,",",$b;',
				'0,0',
			],
		];
	}
}