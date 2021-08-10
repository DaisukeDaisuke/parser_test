<?php

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\ExitException;
use purser\main_old2;
use purser\phpFinalException;

include_once __DIR__.DIRECTORY_SEPARATOR."BaseTest.php";

class whileTest extends BaseTest{


	/**
	 * @return string[][]
	 */
	public function providetestisInsideHangingBox(): array{
		return [
			[
				'while(false){
					echo 1;
				}
				echo 2;',
				'2'
			],
			[
				'$i = 1;
				while ($i <= 10) {
					echo $i++;
				}',
				'12345678910'
			],
			/*[
				'while (@++$i <= 10) echo $i++;',
				'12345678910'
			],*/
			[
				'$i = 1;
				while ($i <= 10):
				    echo $i;
				    $i++;
				endwhile;',
				'12345678910'
			],
		];
	}
}