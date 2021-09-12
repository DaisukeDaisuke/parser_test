<?php

include_once __DIR__."/BaseTest.php";

class CastTest extends BaseTest{
	public function providetestisInsideHangingBox() : array{
		return [
			[
				'var_dump((string) 1);',
				'string(1) "1"'
			],
			[
				'var_dump((int) "1");',
				'int(1)'
			],
			[
				'var_dump((float) 1);',
				'float(1)'
			],
			[
				'var_dump((bool) 1);',
				'bool(true)'
			],
			[
				'var_dump((bool) 0);',
				'bool(false)'
			],
//			[
//				'var_dump((unset) 1);',
//				'string(1) "1"'
//			],
			[
				'$a = (string) 1;$b = 0;var_dump((int) $a, (bool) $a,(string) $b, (bool) $b);',
				'int(1)
bool(true)
string(1) "0"
bool(false)'
			],
			[
				'$a = (string) 1;
				echo (1+2)."\n";
				$b = 0;
				echo (3+3)."\n";
				var_dump((int) $a, (bool) $a,(string) $b, (bool) $b);
				echo (1+1)."\n";',
				'3
6
int(1)
bool(true)
string(1) "0"
bool(false)
2'
			],
			[
				'$a = (int) "1";$a = "2";var_dump($a,(string) $a,(string) 3);',
				'string(1) "2"
string(1) "2"
string(1) "3"',
			],
			[
				'$a = (string) 1+2;
				echo (1+2)."\n";
				$b = 0;
				echo (3+3)."\n";
				var_dump((int) $a, (string) ($a+$b),(string) $b, (bool) $b);
				echo (1+1)."\n";',
				'3
6
int(3)
string(1) "3"
string(1) "0"
bool(false)
2',
			],
		];
	}
}