<?php
declare(strict_types=1);

class LabelTest extends BaseTest{
	public function providetestisInsideHangingBox() : array{
		return [
			[
				'echo 1;
				goto test;
				
				echo 2;
				
				test:
				echo 3;',
				'13',
			],
			[
				'echo 1;
				goto test;
				
				echo 2;
				start:
				if(true){
					echo 3;
					goto end;
				}
				test:
				echo 5;
				goto start;
				end:
				echo 6;',
				'1536',
			],
			[
				'goto test;
				echo 1;
				if(false){
					test:
					echo 2;
				}
				echo 3;',
				'23',
			],
			[
				'goto test;
				test1:
				echo $value;
				goto end;
				test:
				$value = 0;
				goto test1;
				end:',
				'0',
			],[
				'goto test;
				test1:
				echo $value;
				goto end;
				test:
				$value = "test!";
				goto test1;
				end:',
				'test!',
			],

		];
	}
}