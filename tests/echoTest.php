<?php

class echoTest extends BaseTest{
	/**
	 * @return string[][]
	 */
	public function providetestisInsideHangingBox() : array{
		return [
			[
				'echo "test print";',
				'test print',
			],
			[
				'echo ((2*1+1)+(2/1+3));',
				'8',
			],
			[
				'echo ((2*1+1)*(2/1+3));',
				'15',
			],
			[
				'echo ((2*1+1000000)*(2/1+3));',
				'5000010',
			],
			[
				'echo ((2*1+1)+(2/1+3))."_test";',
				'8_test',
			],
/*			[
				'var_dump(1 === 0);',
				'bool(false)',
			],
			[
				'var_dump(1 === 1);',
				'bool(true)',
			],
			[
				'var_dump(true === false);',
				'bool(false)',
			],
			[
				'var_dump(true === true);',
				'bool(true)',
			],*/
			[
				"echo ((2*1+1)+(2/1+3)-(2/(5*6+20)*(5*(6/2))))+7.4;",
				"14.8"
			],
			[
				'echo "100","_","200";',
				"100_200"
			],
			[
				'echo (50+50),"_",((100+100+100)+200);',
				"100_500"
			],
			[
				'echo ((100+100+100)+200),"_",(50+50);',
				"500_100"
			],
		];
	}
}
