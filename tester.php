<?php
include __DIR__."/vendor/autoload.php";

use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);


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
$code = '
for($i = 1; $i <= 10; $i++){
	echo $i;
}';
/*
true;
false;
*/
var_dump(2 >> 1);
$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$stmts = $parser->parse("<?php\n".$code);

$dumper = new NodeDumper(['dumpComments' => true,]);
echo $dumper->dump($stmts, "<?php\n".$code);

/*$main_old = new main_old2();
$output = $main_old->execStmts($stmts);

var_dump($code, $main_old->hexentities($output),$main_old->hexentities1($output));

ob_start();
$decoder = new decoder();
$decoder->decode($output);
$log = ob_get_clean();
var_dump($log);*/