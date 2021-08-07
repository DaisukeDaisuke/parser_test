<?php
include __DIR__."/vendor/autoload.php";

error_reporting(E_ALL);

use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use purser\decoder;
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


/*$code='
for(; false,false,false,false,false;){
echo 1;
}echo 0;';*/
$code='for($i = 1; $i <= 10; $i++){
					echo $i;
				}';


/*$code='$i=100;echo $i--;echo $i--;echo $i--;';*/
/*$code='$i=100;
print ++$i;';*/
$time_start = microtime(true);

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$stmts = $parser->parse("<?php\n".$code);

if($stmts === null){
	throw new \RuntimeException("phpParser crashed");
}

$dumper = new NodeDumper(['dumpComments' => true,]);
echo $dumper->dump($stmts, "<?php\n".$code);

$main_old = new main_old2();
$output = $main_old->execStmts($stmts);

$time = microtime(true) - $time_start;
echo $time." 秒";

var_dump($main_old);

//file_put_contents(".\\output.bin", $output);

var_dump(opcode_dumper::hexentities($output),opcode_dumper::hexentities1($output));


//ob_start();
$decoder = new decoder();
$decoder->decode($output);
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

