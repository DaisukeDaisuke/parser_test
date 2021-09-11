<?php


class printTest extends BaseTest{
	/**
	 * @return string[][]
	 */
	public function providetestisInsideHangingBox() : array{
		return [
			[
				'print "test print";',
				'test print',
			],
			[
				'print ((2*1+1)+(2/1+3));',
				'8',
			],
			[
				'print ((2*1+1)*(2/1+3));',
				'15',
			],
			[
				'print ((2*1+1000000)*(2/1+3));',
				'5000010',
			],
			[
				'print ((2*1+1)+(2/1+3))."_test";',
				'8_test',
			],
			/*[
				'print 1 === 0;',
				'false',
			],
			[
				'print 1 === 1;',
				'true',
			],
			[
				'print true === false;',
				'false',
			],
			[
				'print true === true;',
				'true',
			],*/
			[
				"print ((2*1+1)+(2/1+3)-(2/(5*6+20)*(5*(6/2))))+7.4;",
				"14.8"
			],
			[
				'echo (print "100_");',
				"100_1"
			],
			[
				'$i = print "100_";
				print $i;',
				"100_1"
			],
		];
	}
}