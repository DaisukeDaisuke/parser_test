<?php

include_once __DIR__."/BaseTest.php";

class switchTest extends BaseTest{
	public function providetestisInsideHangingBox() : array{
		return [
			[
				'switch(1000){
					case 100:
						echo "print1";
						break;
					case 2:
						echo "print2";
						break;
					default:
						echo "print default";
						break;
					case 3:
						echo "print3";
						break;
				}',
				'print default',
			],
			[
				'switch(1000){
					case 100:
						echo "print1";
						break;
					case 2:
						echo "print2";
						break;
					case 3:
						echo "print3";
						break;
				}',
				'',
			],
			[
				'switch(100){
					case 100:
						echo "print1";
						break;
					case 2:
						echo "print2";
						break;
					default:
						echo "print default";
						break;
					case 3:
						echo "print3";
						break;
				}',
				'print1',
			],
			[
				'switch(2){
					case 100:
						echo "print1";
						break;
					case 2:
						echo "print2";
						break;
					default:
						echo "print default";
						break;
					case 3:
						echo "print3";
						break;
				}',
				'print2',
			],
			[
				'switch(3){
					case 100:
						echo "print1";
						break;
					case 2:
						echo "print2";
						break;
					default:
						echo "print default";
						break;
					case 3:
						echo "print3";
						break;
				}',
				'print3',
			],
			[
				'switch(100){
					case 100:
						echo "print1";
					case 2:
						echo "print2";
						break;
					default:
						echo "print default";
						break;
					case 3:
						echo "print3";
						break;
				}',
				'print1print2',
			],
			[
				'switch(100){
					case 100:
						echo "print1\n";
					case 2:
						echo "print2\n";
					default:
						echo "print default\n";
						break;
					case 3:
						echo "print3\n";
						break;
				}',
				'print1
print2
print default',
			],
			[
				'switch(100){
					case 100:
						echo "print1\n";
					case 2:
						echo "print2\n";
					default:
						echo "print default\n";
					case 3:
						echo "print3\n";
				}',
				'print1
print2
print default
print3',
			],
			[
				'switch(100){
					case 100:
						echo "print1\n";
					case 2:
						echo "print2\n";
					default:
						echo "print default\n";
					case 3:
						echo "print3\n";
						break;
				}',
				'print1
print2
print default
print3',
			],
			[
				'switch(100){}',
				'',
			],
			[
				'switch(100){
					case 100:
						echo "print1\n";
						continue;
					case 2:
						echo "print2\n";
					default:
						echo "print default\n";
					case 3:
						echo "print3\n";
						break;
					}',
				'print1',
				null,
				0,
				[
					'php compiler warning: "continue" targeting switch is equivalent to "break"',
				],
			],
			[
				'for($i = 1; $i <= 2; $i++){
					switch(100){
					case 100:
						echo "print1\n";
						continue;
					case 2:
						echo "print2\n";
					default:
						echo "print default\n";
					case 3:
						echo "print3\n";
						break;
					}
				}',
				'print1
print1',
				null,
				0,
				[
					'php compiler warning: "continue" targeting switch is equivalent to "break". Did you mean to use "continue 2"?',
				],
			],
			[
				'for($i = 0; $i <= 3; $i++){
					switch($i){
						case 1:
							echo "print1\n";
							break;
						case 2:
							echo "print2\n";
							break;
						default:
							echo "print default\n";
							break;
						case 3:
							echo "print3\n";
							break;
					}
				}',
				'print default
print1
print2
print3',
			],
			[
				'switch(100){
					case 100:
						switch(2){
							case 1:
								echo "print1\n";
								break;
							case 2:
								echo "print2\n";
								break;
							default:
								echo "print default\n";
								break;
							case 3:
								echo "print3\n";
								break;
						}
						break;
				}',
				'print2',
			],
			[
				'switch(1000){
					case 100:
						switch(2){
							case 1:
								echo "print1\n";
								break;
							case 2:
								echo "print2\n";
								break;
							default:
								echo "print default\n";
								break;
							case 3:
								echo "print3\n";
								break;
						}
						break;
				}',
				'',
			],
			[
				'switch(100){
					case 100:
						switch(1){
							case 1:
								for($i = 1; $i <= 2; $i++){
									echo "print1\n";
								}
								break;
						}
						break;
				}',
				'print1
print1',
			],
			[
				'switch(100){
					case 100:
						switch(1){
							case 1:
								for($i = 1; $i <= 2; $i++){
									for($i = 1; $i <= 2; $i++){
										continue;
										echo "print2\n";
										break;
									}
									echo "print1\n";
								}
								break;
						}
						break;
				}',
				'print1',
			],
		];
	}
}