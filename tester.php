<?php
include __DIR__."/vendor/autoload.php";

error_reporting(E_ALL);

use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use purser\decoder;
use purser\ExitException;
use purser\main_old2;
use purser\opcode_dumper;

ini_set('xdebug.var_display_max_children', "-1");
ini_set('xdebug.var_display_max_data', "-1");
ini_set('xdebug.var_display_max_depth', "-1");

function hexentities(string $str): string{
	$return = '';
	for($i = 0, $iMax = strlen($str); $i < $iMax; $i++){
		$return .= ' :'.bin2hex($str[$i]).';';
	}
	return $return;
}

echo "\n";

//$code = 'echo true === false;';
//$code = "echo ((2*1+1)+(2/1+3)-(2/(5*6+20)*(5*(6/2))))+7.4;";
/*$code = '
if(1+2===3){
	echo "1";
}else{
	echo "2";
}';*/
//$code = "echo ((2*1+1)+(2/1+3)-(2/(5*6+20)*(5*(6/2))))+7.4;";
/*$code = '
if(1+2===3){
	echo "test print";
}elseif(1===1){
	echo "a";
}elseif(1===1){
	echo "b";
}else{
	echo "c";
}';*/

/*$code = 'if(false){
	echo "1";
}else{
	echo "2";
}';*/
/*$code = 'if(10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000===10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000){
	echo "10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
}else{
	echo "2";
}';*/
/*$code = 'if(0===1){
	echo "10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
}else{
	echo "2";
}';*/
/*else{
	echo "0";
}*/
/*$code = 'if(1+2===3){
	echo "test print";
}elseif(1===1){
	echo "a";
}elseif(1===1){
	echo "b";
}else{
	echo "c";
}';*/
/*$code = 'if(2===2){
	echo "test print";
}elseif(1===3){
	echo "a";
}elseif(2===2){
	echo "b";
}else{
	echo "c";
}';*/
/*$code = '
if(true){
	echo "1";
}elseif(false){
	echo "2";
}*/
/*$code = 'if(false){
	echo "1";
}elseif(false){
	echo "2";
}
echo "3";';*/
/*$code = 'if(1+2===3){
	echo "true";
}else{
	echo "false";
}';*/
/*
$code = "echo 1 xor 1;";
*/
/*$code = '
for($i = 1; $i <= 10; $i++){
	echo $i;
}';*/
/*
$code = '
echo "test print";
echo 100+200;
//if(false){
//	echo 100+200;
//}else{
//	echo 300+300;
//}
//$i="test";
//echo $i;
//echo $i+200;';*/

/*$code='
echo "100","_","200";
';*/

/*$code = '
echo ((100+100+100)+200),"_",(50+50);
';*/
/*$code='
$i = print "test";
$i = 12;
echo $i;
';*/
/*$code='
$i = print "test";
$i = 12;
$i = 13;
echo $i;
';*/
/*$code='$i=-200;
echo $i;';*/
/*$code = '$i = print "test_";
echo $i;';*/
//$code = '$i=100;$k=200;echo $k,",",$i;';
/*$code = '$i=100;$k=200;echo 100,",",$i;';*/
/*$code='$i=100;$k=200;echo ",",$k,",",$i;';*/
/*$code='$i = 100;
$j=200;
$k=$i-$j;
echo $k;';*/
//$code='$i=100;$k=200;echo $k,",",$i;';
/*$code='print $j=200;';*/
/*$code='$i = 100;
$j=200;
$k=$i-$j;
echo $k;';*/
/*$code='echo print $k = print $j = $i = 100;';*/
/*$code='echo $j = $i = 100;';
$code='$i = 100;$i = 200;';*/
/*$code = '$i = 100;
				$j=200;
				$k=$i-$j;
				echo $k;'*/;
/*$code= '$i = print "test_";echo $i;';*/

/*$code='
for($i=0; $i<101; $i++){
	if($i % 15 === 0){
		echo "FizzBuzz,";
		continue;
	}
	if($i % 3 === 0){
		echo "Fizz,";
		continue;
	}
	if($i % 5 === 0){
		echo "Buzz,";
		continue;
	}
	echo $i.",";
}';*/

//for ($i = 1, $j = 0; $i <= 10; $j += $i, print $i, $i++);
//$j=0;$j += (1+2);echo $j;
//$code='@$j += 1;echo $j;';

//$code='$i=1;$b=var_dump(null,true,false,$i+1,2,2+3,"test");';
//$code='$a="test";echo strlen(substr($a,1,2));';

//$code='$a="test";echo strlen($a,1,2);';//!!

//$code='for ($i = 1, $j = 0; $i <= 10; $j += $i, print $i, $i++);';

/*$code='$i=100;echo $i--;echo $i--;echo $i--;';*/
/*$code='$i=100;
print ++$i;';*/
//$code='var_dump(@++$i <= 10,$i);';
//$code='var_dump(@++$i <= 10,$i);';
//$code='while (@++$i <= 10) echo $i++;';
//$code='for($i=0;false;$i++){
//					echo 1;
//				}
//				echo $i;';
//$code='while (@++$i <= 10) echo $i++;';
////$code='if(isset($a));';
//$code='if(1){$a=1;die($a);}';

$code = 'switch(3){
	case 100:
		echo "print1";
		break;
	case 2:
		echo "print2";
	default:
		echo "print default";
	case 3:
		echo "print3";
		break;
}';

$code = 'switch(1000){
	case 100:
		echo "print1";
		//break;
	case 2:
		echo "print2";
	default:
		echo "print default";
		break;
	case 3:
		echo "print3";
		break;
	case 4:
		echo "print4";
		break;
	case 5:
		echo "print5";
		break;
	case 6:
		echo "print6";
		break;
	case 7:
		echo "print6";
		break;
	case 8:
		echo "print6";
		break;
	case 9:
		echo "print6";
		break;
	case 10:
		echo "print6";
		break;
	case 11:
		echo "print6";
		break;
	case 12:
		echo "print6";
		break;
}';
$code = 'for($i = 0; $i <= 3; $i++){
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
				}';

$code = '$a = (int) "1";$a = "2";var_dump($a,(string) $a,(string) 3);';
$code = '$a = 0;$b = 0;var_dump((string) ($a+$b));
';
$code = 'echo print $tdm="tdm",print $tdm,$tdm,"\n";';
$code = 'echo print "a",print "b";';//a1b1
$code = 'echo print print $tdm="test",print $tdm,1-(int)$tdm,$tdm,"\n";';
//$code = 'exit(1);';
$code='(print 1)||(print 0)||(print 0);';
//(print 1)||(print 0)||(print 0);
$code = "(print 0)&&0;//&&intval(1);";
$code = "(print 0)&&(print 0)&&1;";
//(print 0)&&(print 0)&&1;
//echo "\n";
$code = '$i=0;(++$i)&&9;';
$code = '$i=0;($i+=1)&&9;';
$code = '$i=0;(++$i)&&9;';
$code = '$i = 100;echo ++$i;echo ++$i;';
$code = 'var_dump(((++$i)+1)&&1);echo $i;';
$code = 'var_dump($i);echo $i;';
$code = '$i=1;var_dump((++$i)&&(print "test"));';
$code = '100&&print "test";';
$code = '($i = 100)&&print "test";echo $i;';
//$code = '$i = 100;($j = $i)&&false&&print "test";echo $i,$j;';
//$i = 100;($j = $i)&&false&&print "test";echo $i,$j;
$code = 'var_dump((0)||(1)||(print 6));';
$code = 'print ("a" || (print ("b" || (print "c"))));';
//$code = 'var_dump((--$i)||false);';
$code = 'var_dump((--$i)||false);';
//var_dump((--$i)||false);
//$code = '(print 1)||(print 0)||(print 0);';
//(print 1)||(print 0)||(print 0);
//print "hello " && print "world";
//$code='var_dump((++$i)&&9);';
//var_dump((++$i)&&9);
//$code = 'print "hello " || print "world";';
//print "hello " || print "world";
//print (1||print (0||print 0));
//($i = 100)&&print "test";echo $i;
//$i=1;var_dump((++$i)&&(print "test"));


//var_dump(((++$i)+1)&&1);echo $i;
//$code = '$i++;echo $i++;';
//ob_start();
//echo $i++;echo $i++;
//var_dump(hexentities(ob_get_clean()));
//$code = '$i = 100;if($i){echo 1;}';


$code = 'echo -(1+100000);';
$code = 'echo -100;';
$code = 'echo -"aaaaa";';
//$code = 'echo -100.5;';
//echo (("aaaaaaa")&&intval(1));

$code = '$i = 1;
				while ($i <= 10) {
					echo $i++;
				}';
$code = '
$i=["test"];
$i[1] = "test";
';
$code ='($a <=> $b) === -1;';
$code = 'var_dump(1 < 100);';
$code = '(bool)(($a <=> $b) === -1 || ($a <=> $b) === 0);';
$code = 'var_dump(101 <= intval(101));';
//var_dump(101 <= intval(101));
$code='var_dump(intval(101) <= 10 || (101 <= 101);';//error
$code='var_dump((101 <= 10) || (101 <= 101));';
//var_dump((101 <= 10) || (101 <= 101));
$code='var_dump((0 != 0));';
//var_dump((0 != 0));
$code='var_dump((-1 >= 0));';
//var_dump((-1 >= 0));

$code='var_dump((1 > 0));';
//var_dump((1 > 0));

$code='var_dump("test") || true;';
$code = 'var_dump(var_dump("test"));';
//var_dump(var_dump("test"));

//$code='var_dump(100>10||10>=100||10 == 100||10 != 10||100<=10||10<=10);';
//var_dump(100>10||10>=100||10 == 100||10 != 10||100<=10||10<=10);

$code='for(;true,true;){
	echo "test";
	break;
}
';//echo 0;

$code='
for(;;($i++)){
}';//$i = always 1

$code='
for ($y = -1; $y < 2; $y++) {
    var_dump("test!");
	for ($x = -1; $x < 2; $x++) {
		for ($z = -1; $z < 2; $z++) {
		    var_dump("for!");
		    break 3;
		}
	}
	var_dump("Never call");
}
var_dump("exit!");';//$i = always 1
$code='if(true):
    echo "if\n";    
endif;

if(false):
    echo "if true\n";
else:
     echo "if false\n";
endif;

for($i = 0; $i < 1; $i++):
	echo "for\n";
endfor;

while(true):
	echo "while\n";
	break;
endwhile;

/*$array = array("foreach");
foreach($array as $value):
    echo $value,"\n"; 
endforeach;*/';

$code='
for(;;($i++)){
echo $i;//$i = 未定義、警告...?
break;
}';//$i = always 1

$code='
var_dump($i);
var_dump(++$i);
';

$code='
for(;;($i++)){
echo $i;//$i = 未定義、警告...?
break;
}';//$i = always 1

$code='$true = false;
for(print 1; print 2; print 3, $true = true){
	print 4;
	if($true) break;
}';
$code='
$test = true;
';

$code = '
if(true){
        for($i=0; $i<5; $i++){
                echo $i;
        }
}else{
        for($i=0; $i<5; $i++){
                echo $i++;
        }
}';

$code='$true = false;
for(print 1; print 2; print 3, $true = true){
	print 4;
	if($true) break;
}';

$code='$a = (true&&true&&true); var_dump($a);';

/*
 | 0-2 | 3 |  POW?:17;  output:00;  output:03;
 | 3-5 | 3 |  INT:93; size:01; 3:03;
 | 6-8 | 3 |  INT:93; size:01; 2:02;
*/
$code='3**2;';
/*
 | 0-2 | 3 |  MUL?:03;  output:00;  output:03;
 | 3-5 | 3 |  INT:93; size:01; 3:03;
 | 6-8 | 3 |  INT:93; size:01; 2:02;
*/
$code='3*2;';

$code = '
if(true){
        for($i=0; $i<5;){
                echo $i;
                break;
        }
}';

$code = '
//$i = 0;
switch($i){
	case 1:
	break;
}';

$code = 'while (true) {
    echo "test";
    break;
}';

$code = 'for ($i = 0; $i < 10; $i++) {
    echo $i;
}';

// Github copilot test in php
$code = '
$a = 1;
$b = 2;
$c = 3;
$d = 4;
$e = 5;
$f = 6;
    
$a = $b + $c;
$b = $c + $d;
$c = $d + $e;
$d = $e + $f;
$e = $f + $a;
$f = $a + $b;
var_dump($a, $b, $c, $d, $e, $f);
';

$code = '
$test = [11];
$test["!!!"] = "???";
var_dump($test["!!!"]);
$test = [];
';
//バブルソート
$code = '
$test = [11,2,3,4,5,6,7,8,9,10];
$i = 0;
$j = 0;
$temp = 0;
for($i = 0; $i < count($test); $i++){
	for($j = 0; $j < count($test) - 1; $j++){
		if($test[$j] > $test[$j + 1]){
			$temp = $test[$j];
			$test[$j] = $test[$j + 1];
			$test[$j + 1] = $temp;
		}
	}
}
var_dump($test);
';

//quicksort
$code = '
$array = [11,2,3,4,5,6,7,8,9,10];
$pivot = $array[0];
$less = $greater = [];
for ($i = 1; $i < count($array); $i++) {
	if ($array[$i] < $pivot) {
		$less[] = $array[$i];
	} else {
		$greater[] = $array[$i];
	}
}
$array = array_merge(quicksort($less), [$pivot], quicksort($greater));

var_dump($array);
';



$test = [11,2,3,4,5,6,7,8,9,10];
$i = 0;
$j = 0;
$temp = 0;
for($i = 0; $i < count($test); $i++){
	for($j = 0; $j < count($test) - 1; $j++){
		if($test[$j] > $test[$j + 1]){
			$temp = $test[$j];
			$test[$j] = $test[$j + 1];
			$test[$j + 1] = $temp;
		}
	}
}
var_dump($test);


//none: STDIN is unspported in this program.
//$code = '
//$n = intval(fgets(STDIN));
//echo $n % 100;
//';
//echo 254 % 101;

// Github copilot test in php






//$code='
//var_dump($i);
//var_dump(++$i);';
//
//var_dump($i);
//var_dump(++$i);

//$code='
//$true = false;
//for(;;){
//	$true = true;
//	if($true) break;
//}';

//$code='
//$true = false;
//echo $true;
//$true = true;
//echo $true;
//';

//124324
//124324
//$true = false;
//for(print 1; print 2; print 3, $true = true){
//	print 4;
//	if($true) break;
//}




//
//$a = 101;
//$b = 101;
//$s_tmp = ($a <=> $b);
//echo (($s_tmp === -1)||($s_tmp === 0));

//$code = 'echo -null;';
//echo -null;

//echo -"aaaaa";
//var_dump(0 xor (print "0\n") xor (print "2\n"));
/*

jmpz //case //2
print "test" //1
goto label //break; //1
jmp 4 //1

jmpz
print "test"
jmp 4

*/
echo "\n";
$time_start = microtime(true);

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$stmts = $parser->parse("<?php\n".$code);

if($stmts === null){
	throw new \RuntimeException("phpParser crashed");
}

//$prettyPrinter = new Standard();
//var_dump($prettyPrinter->prettyPrintFile($stmts));

$dumper = new NodeDumper(['dumpComments' => true,]);
echo $dumper->dump($stmts, "<?php\n".$code);

$main_old = new main_old2();
$output = $main_old->onexec($stmts);

//var_dump($main_old->block);

$time = microtime(true) - $time_start;
echo $time." 秒";

//var_dump($main_old);

//file_put_contents("output.bin", $output);
$list1 = [];
$symbols = [];
$var_use_list = [];
var_dump(opcode_dumper::hexentities($output, $list1, $symbols, $var_use_list), opcode_dumper::hexentities1($output));
//var_dump($list1);
var_dump(strlen($output));
//var_dump($symbols);
var_dump($var_use_list);
var_dump("======decoder=====");

//ob_start();
$decoder = new decoder();
try{
	$time_start = microtime(true);
	$decoder->decode($output, true);
	$time = microtime(true) - $time_start;
}catch(ExitException $exception){
	var_dump("exit code: ".$exception->getMessagecode());
	$exception->exec();
}

//echo $time." 秒";

//$log = ob_get_clean();
//var_dump($log);
$binaryStream = $decoder->getBinaryStream();
if(isset($binaryStream)){
	if(strlen($output) !== $binaryStream->getOffset()){
		throw new \RuntimeException("A program overrun has been detected. Expected: ".strlen($output).", Actual: ".$binaryStream->getOffset());
	}
}else{
	var_dump("!!");
}
