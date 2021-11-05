<?php
$time_start0 = hrtime(true);
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use purser\decoder;
use purser\main_old2;


include __DIR__."/vendor/autoload.php";

$code='
for($i=0; $i<101; $i++){
    if($i%15 === 0){
        echo "FizzBuzz";
    }else if($i%3 === 0){
        echo "Fizz";
    }else if($i%5 === 0){
        echo "Buzz";
    }else{
        echo $i;
    }
    echo "\n";
}';
$setup = hrtime(true) - $time_start0;
$time_start1 = hrtime(true);

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$stmts = $parser->parse("<?php\n".$code);

if($stmts === null){
	throw new \RuntimeException("phpParser crashed");
}
$phppurser = hrtime(true) - $time_start1;
$time_start2 = hrtime(true);

$main_old = new main_old2();
$output = $main_old->execStmts($stmts);

$com = hrtime(true) - $time_start2;
$time_start3 = hrtime(true);

$decoder = new decoder();
$decoder->decode($output);

$exec = hrtime(true) - $time_start3;
$all = hrtime(true) - $time_start0;

$time_start5 = hrtime(true);
for($i=0; $i<101; $i++){
	if($i%15 === 0){
		echo "FizzBuzz";
	}else if($i%3 === 0){
		echo "Fizz";
	}else if($i%5 === 0){
		echo "Buzz";
	}else{
		echo $i;
	}
	echo "\n";
}
$native = hrtime(true) - $time_start5;

echo "\n";
echo "composer autoload:	".($setup / 1000000000)." 秒\n";
echo "php-parser:			".($phppurser / 1000000000)." 秒\n";
echo "コード生成:			".($com / 1000000000)." 秒\n";
echo "実行:				".($exec / 1000000000)." 秒\n";
echo "===============================\n";
echo "合計:				".($all / 1000000000)." 秒\n";
echo "php ネイティブ:		".($native / 1000000000)." 秒\n";
echo ($all / 1000000000)/($native / 1000000000);