<?php

class binaryopTest extends BaseTest{
	/**
	 * @return string[][]
	 */
	public function providetestisInsideHangingBox() : array{
		return [
			[
				'var_dump(20 + 30);',
				'int(50)',
			],
			[
				'var_dump(20 * 30);',
				'int(600)',
			],
			[
				'var_dump(20 / 20);',
				'int(1)',
			],
			[
				'var_dump(20 - 30);',
				'int(-10)',
			],
			[
				'var_dump(1 & 2);',
				'int(0)',
			],
			[
				'var_dump(1 | 2);',
				'int(3)',
			],
			[
				'var_dump(1 ^ 2);',
				'int(3)',
			],
			[
				'var_dump(1 && 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 && 0);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(0 && 1);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(0 && 0);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 || 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 || 0);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(0 || 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(0 || 0);',
				self::TYPE_FALSE,
			],
			//code::COALESCE
			[
				'var_dump("1"."2");',
				'string(2) "12"',
			],
			[
				'var_dump((1).(2));',
				'string(2) "12"',
			],
			[
				'var_dump(1 == 2);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 == 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 == "1");',
				self::TYPE_TRUE,
			],
			[
				'var_dump(2 > 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 > 2);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 > 1);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(2 >= 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 >= 2);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 >= 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 === 2);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 === 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 === "1");',
				self::TYPE_FALSE,
			],

			[
				'var_dump(0 and 0);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 and 0);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 and 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 and "1");',
				self::TYPE_TRUE,
			],

			[
				'var_dump(0 or 0);',
				self::TYPE_FALSE,
			],
			[
				'var_dump(1 or 0);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(0 or 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 or 1);',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 or "1");',
				self::TYPE_TRUE,
			],
			[
				'var_dump(1 or "1");',
				self::TYPE_TRUE,
			],
			[
				'var_dump(0 xor 0);',
				self::TYPE_FALSE
			],
			[
				'var_dump(0 xor 1);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 xor 0);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 xor 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump(3 % 2);',
				'int(1)'
			],
			[
				'var_dump(1 != 2);',
				self::TYPE_TRUE
			],
			[
				'var_dump(2 != 1);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 != 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump("1" != 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump(1 !== 2);',
				self::TYPE_TRUE
			],
			[
				'var_dump(2 !== 1);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 !== 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump("1" !== 1);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 << 2);',
				'int(4)'
			],
			[
				'var_dump(2 ** 3);',
				'int(8)'
			],
			[
				'var_dump(2 >> 1);',
				'int(1)'
			],
			[
				'var_dump(1 < 2);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 < 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump(2 < 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump(1 <= 2);',
				self::TYPE_TRUE
			],
			[
				'var_dump(1 <= 1);',
				self::TYPE_TRUE
			],
			[
				'var_dump(2 <= 1);',
				self::TYPE_FALSE
			],
			[
				'var_dump(1 <=> 2);',
				'int(-1)'
			],
			[
				'var_dump(2 <=> 1);',
				'int(1)'
			],
			[
				'var_dump(1 <=> 1);',
				'int(0)'
			],
			[
				'print 1||print 0||print 0;',
				'1'
			],
			[
				'(print 1)||(print 0)||(print 0);',
				'1'
			],
			[
				'var_dump((0)||(1)||(print 6));',
				'bool(true)'
			],
			[
				'print "hello " && print "world";',
				'world1'
			],
			[
				'print "hello " || print "world";',
				'1'
			],
			[
				'$i=0;var_dump((++$i)&&9);',
				self::TYPE_TRUE
			],
			[
				'$i=0;var_dump(($i++)&&9);',
				self::TYPE_FALSE
			],
			[
				'var_dump(($i++)&&9);',
				self::TYPE_FALSE,
				null,
				0,
				[
					'php compiler warning: Undefined variable $i'
				],
			],
			[
				'var_dump(($i--)&&9);',
				self::TYPE_FALSE,
				null,
				0,
				[
					'php compiler warning: Undefined variable $i'
				],
			],
			[
				'var_dump((++$i)&&9);',
				self::TYPE_TRUE,
				null,
				0,
				[
					'php compiler warning: Undefined variable $i'
				],
			],
			[
				'var_dump((--$i));',
				self::TYPE_NULL,
				null,
				0,
				[
					'php compiler warning: Undefined variable $i'
				],
			],
			[
				'var_dump(false||null||0||0.0||"");',
				self::TYPE_FALSE
			],
			[
				'var_dump(false||null||true||0||0.0||"");',
				self::TYPE_TRUE
			],
			[
				'var_dump(false||null||0||0.0||""||(print "1\n"));',
				"1\n".self::TYPE_TRUE
			],
			[
				'var_dump(true&&1&&1.0&&"test"&&strval(1));',
				self::TYPE_TRUE
			],
			[
				'var_dump(true&&1&&1.0&&strval(0)&&"test");',
				self::TYPE_FALSE
			],
			[
				'var_dump(true&&(false||true));',
				self::TYPE_TRUE
			],
//			[
//				'var_dump(null ?? true);',
//				self::TYPE_TRUE,
//			],
//			[
//				'$a=1;var_dump($a ?? true);',
//				'int(1)',
//			],
//			[
//				'var_dump($a ?? true);',
//				self::TYPE_TRUE,
//			],
		];
	}
}