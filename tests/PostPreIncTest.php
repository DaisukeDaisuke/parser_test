<?php


class PostPreIncTest extends BaseTest{
	/**
	 * @return string[][]
	 */
	public function providetestisInsideHangingBox() : array{
		return [
			[
				'$i=100;
				$i++;
				echo $i;',
				'101',
			],
			[
				'$i=100;
				++$i;
				echo $i;',
				'101',
			],
			[
				'$i=100;
				echo ++$i;',
				'101',
			],
			[
			'$i=100;
				$i--;
				echo $i;',
				'99',
			],
			[
				'$i=100;
				--$i;
				echo $i;',
				'99',
			],
			[
				'$i=100;
				echo --$i;',
				'99',
			],
			[
				'$i=100;echo $i++;echo $i++;echo $i++;',
				'100101102'
			],
			[
				'$i=100;echo $i--;echo $i--;echo $i--;',
				'1009998'
			],
			[
				'$i=100;
				print ++$i;',
				'101'
			],
			[
				'$i=100;
				print --$i;',
				'99'
			],
			[
				'$i=100;
				echo ++$i;',
				'101'
			],
			[
				'$i=100;
				echo --$i;',
				'99'
			],
			[
				'$i++;
				$j--;
				++$a;
				--$b;',
				'',
				null,
				0,
				[
					'php compiler warning: Undefined variable $i',
					'php compiler warning: Undefined variable $j',
					'php compiler warning: Undefined variable $a',
					'php compiler warning: Undefined variable $b',
				],
			],
			[
				'var_dump(--$i);',
				self::TYPE_NULL
			],
		];
	}
}