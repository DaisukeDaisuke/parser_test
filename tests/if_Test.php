<?php


use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use purser\decoder;
use purser\main_old2;

class if_Test extends TestCase{
	/**
	 * (selectedBettingTable)->isInsideHangingBox();
	 * 関数に関します、テストにてございます...
	 *
	 * @dataProvider providetestisInsideHangingBox
	 * @return void
	 */
	public function testisInsideHangingBox($code, $output1){
		$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
		$stmts = $parser->parse("<?php\n".$code);
		$main_old = new main_old2();
		$output = $main_old->execStmts($stmts);

		//var_dump($code, $main_old->hexentities($output));

		ob_start();
		$decoder = new decoder();
		$decoder->decode($output);
		$log = ob_get_clean();

		//var_dump($code,$stmts,$output,$log);

		self::assertEquals(trim($output1), trim($log));
	}

	public function providetestisInsideHangingBox(): array{
		return [
			[
				'if(1+2===3){
					echo "1";
				}else{
					echo "0";
				}',
				'1',
			],
			[
				'if(1+2===3){
					echo "test print";
				}elseif(1===1){
					echo "a";
				}elseif(1===1){
					echo "b";
				}else{
					echo "c";
				}',
				'test print',
			],
			[
				'if(1===2){
					echo "test print";
				}elseif(1===1){
					echo "a";
				}elseif(1===1){
					echo "b";
				}else{
					echo "c";
				}',
				'a',
			],
			[
				'if(2===3){
					echo "test print";
				}elseif(1===2){
					echo "a";
				}elseif(1===1){
					echo "b";
				}else{
					echo "c";
				}',
				'b',
			],
			[
				'if(2===3){
					echo "test print";
				}elseif(1===2){
					echo "a";
				}elseif(2===3){
					echo "b";
				}else{
					echo "c";
				}',
				'c',
			],
			[
				'if(true){
					echo 100;
				}else{
					echo 200;
				}',
				'100',
			],
			[
				'if(false){
					echo "1";
				}else{
					echo "2";
				}',
				"2"
			],
			[
				'if(1+2===3){
					echo "1";
					/*
					echo "2";
					*/
					//echo 5;
				}/*else{
					echo "6";
				}*/',
				"1"
			],
			[
				'if(1+2===3){
					echo "1";
				}else{
					echo "2";
				}',
				"1"
			],
			[
				'if(1+2===3){
					echo "true";
				}else{
					echo "false";
				}',
				"true"
			],
			[
				'if(1+2!==3){
					echo "1";
				}else{
					echo "2";
				}',
				"2"
			],
			[
				'if(1==="1"){
					echo "1";
				}else{
					echo "2";
				}',
				"2"
			],
			[
				'if(1===1){
					echo "10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
				}else{
					echo "2";
				}',
				"10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000"
			],
			[
				'if(0===1){
					echo "10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
				}else{
					echo "2";
				}',
				"2"
			],
			[
				'if(10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000===10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000){
					echo "10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
				}else{
					echo "2";
				}',
				"10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000"
			],
			[
				'if(false){
					echo "1";
				}elseif(false){
					echo "2";
				}
				echo "3";',
				"3"
			],
			[
				'if(true){
					echo "1";
				}elseif(false){
					echo "2";
				}
				echo "3";',
				"13"
			],
			[
				'if(false){
					echo "1";
				}elseif(true){
					echo "2";
				}
				echo "3";',
				"23"
			],
			[
				'if(false){
					echo "1";
				}elseif(false){
					echo "2";
				}elseif(false){
					echo "3";
				}
				echo "5";',
				"5"
			],
			[
				'if(false){
					echo "1";
				}elseif(false){
					echo "2";
				}elseif(false){
					echo "3";
				}else{
					echo "6";
				}',
				"6"
			],
			[
				'if(false){
					echo "1";
				}elseif(false){
					echo "2";
				}elseif(false){
					echo "3";
				}else{
					echo "6";
				}
				echo "7";',
				"67"
			],
			[
				'if(false){
					echo "1";
				}elseif(false){
					echo "2";
				}elseif(false){
					echo "3";
				}else{
					echo "6";
				}
				echo 7;',
				"67"
			],
			[
				'if(false){
					echo "1";
				}elseif(false){
					echo "2";
				}elseif(false){
					echo "3";
				}else{
					echo (200-((100/10*20)+50));
				}',
				"-50"
			],
			[
				'if(false){
					echo "1";
				}elseif(false){
					echo "2";
				}elseif(false){
					echo "3";
				}else{
					echo "6_";
				}
				echo 200-(100/10*20)*(100*200%50);',
				"6_200"
			],
		];

	}

}