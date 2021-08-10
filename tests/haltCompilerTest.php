<?php

include_once __DIR__.DIRECTORY_SEPARATOR."BaseTest.php";

class haltCompilerTest extends BaseTest{
	public function providetestisInsideHangingBox(): array{
		return [
			[
				'__halt_compiler();',
				'',
				null,
				0,
			],
			[
				'__halt_compiler();echo "aaa";test',
				'',
				null,
				0,
			],
			[
				'echo "test";__halt_compiler();echo "test";',
				'test',
				null,
				0,
			],
		];
	}
}
