<?php


class if_Test extends BaseTest{

	/**
	 * @return string[][]
	 */
	public function providetestisInsideHangingBox() : array{
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
			[
				'
				echo "1_";
				echo (200+300)."_";
				if(false){
					echo "1";
				}elseif(200+300 !== 500){
					echo "2";
				}elseif(false){
					echo "3";
				}elseif(false){
					echo "3";
				}elseif(false){
					echo "3";
				}elseif(false){
					echo "3";
				}elseif(false){
					echo "3";
				}elseif(false){
					echo "3";
				}elseif(false){
					echo "3";
				}else{
					echo "6_";
					echo "12_";
				}
				echo "12_";
				echo "12";',
				'1_500_6_12_12_12',
			],
			[
				'if(false){
					
				}elseif(false){
					
				}elseif(false){
					
				}elseif(false){
					
				}elseif(false){
					
				}elseif(false){
					
				}elseif(false){
					
				}elseif(false){
					
				}elseif(false){
					
				}elseif(false){
					
				}elseif(false){
					
				}else{
					echo "6_";
					echo "12_";
				}',
				'6_12_'
			],
			[
				'if(true){
					if(true){
						echo "0";
					}else{
						echo "1";
					}
				}else{
					echo "2";
				}',
				'0',
			],
			[
				'if(true){
					if(false){
						echo "0";
					}else{
						echo "1";
					}
				}else{
					echo "2";
				}',
				'1',
			],
			[
				'if(false){
					if(false){
						echo "0";
					}else{
						echo "1";
					}
				}else{
					echo "2";
				}',
				'2',
			],
			[
				'if(false){
					if(true){
						echo "0";
					}else{
						echo "1";
					}
				}else{
					echo "2";
				}',
				'2',
			],
			[
				'if(false){
					if(true){
						echo "0";
					}else{
						echo "1";
					}
				}else{
					if(true){
						echo "2";
					}else{
						echo "3";
					}
				}',
				'2',
			],
			[
				'if(false){
					if(true){
						echo "0";
					}else{
						echo "1";
					}
				}else{
					if(false){
						echo "2";
					}else{
						echo "3";
					}
				}',
				'3',
			],
			[
				'if(false){
					echo "0";
				}else{
					if(false){
						echo "1";
					}else{
						echo "2";
					}
				}',
				'2',
			],
			[
				'echo "1";
				if(true){
					echo "2";
					if(true){
						echo "3";
					}else{
						echo "5";
					}
					echo "6";
				}else{
					echo "7";
				}
				echo "8";',
				'12368',
			],
			[
				'echo "1";
				if(true){
					echo "2";
					if(true){
						echo "3";
						if(true){
							echo "5";
						}
						echo "6";
					}
					echo "7";
				}else{
					echo "8";
				}
				echo "9";',
				'1235679',
			],
			[
				'echo "1";
				if(true){
					echo "2";
				}else{
					echo "3";
					if(true){
						echo "4";
						if(true){
							echo "5";
						}
						echo "6";
					}
					echo "7";
				}
				echo "9";',
				'129',
			],
//			[
//				'if(!print 1||print 2||!print 3||print 4||print 5||print 6){
//					echo "true";
//				}else{
//					echo "false";
//				}',
//				'1true',
//			],
		];

	}

}