<?php

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\main_old2;

class function_callTest extends TestCase{
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
				'$i=1;$b=var_dump(null,true,false,$i+1,2,2+3,"test");',
				'NULL
bool(true)
bool(false)
int(2)
int(2)
int(5)
string(4) "test"
'
			],
			[
				'$a="test";echo strlen(substr($a,1,2));',
				'2',
			],
			[
				'$a="test";echo strlen($a);',
				'4',
			],
			[
				'$a="testing";echo $a=strlen($a),$a;',
				'77',
			],
			[
				'$a="test";$a=substr($a,1,2);echo $a;',
				'es'
			],
			[
				'$a="test";$a=strlen(substr($a,1,2));echo $a;',
				'2'
			],
			[
				'var_dump(null);',
				'NULL'
			],
			[
				'var_dump(true);',
				'bool(true)'
			],
			[
				'var_dump(false);',
				'bool(false)'
			],
			[
				'$i=1;var_dump($i+1);',
				'int(2)'
			],
			[
				'$i=1;var_dump(2);',
				'int(2)'
			],
			[
				'var_dump(2+3);',
				'int(5)'
			],
			[
				'var_dump("test");',
				'string(4) "test"'
			],
		];
	}
}