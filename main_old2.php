<?php
include __DIR__."/vendor/autoload.php";

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\BitwiseAnd;
use PhpParser\Node\Expr\BinaryOp\BitwiseOr;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\BinaryOp\Div;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalOr;
use PhpParser\Node\Expr\BinaryOp\LogicalXor;
use PhpParser\Node\Expr\BinaryOp\Minus;
use PhpParser\Node\Expr\BinaryOp\Mod;
use PhpParser\Node\Expr\BinaryOp\Mul;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BinaryOp\Plus;
use PhpParser\Node\Expr\BinaryOp\Pow;
use PhpParser\Node\Expr\BinaryOp\ShiftLeft;
use PhpParser\Node\Expr\BinaryOp\ShiftRight;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;
use PhpParser\Node\Expr\BinaryOp\Spaceship;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_ as LString;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\if_;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use pocketmine\utils\Binary;

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

include __DIR__."/decoder.php";

$code = '
<?php
$a = 100;
//echo 1+2*(3/$a*1);
echo ((2*1+1)+(2/1+3)-(2/(5*6+20)*(5*(6/2))))+7.4;//3+5+50

/*if(true===true){
	echo "test print";
}


if(1+2===3){

}else{
	echo "else_test";
}


if(1+5===true){
	echo "if_test";
}else if($a === 100){
	echo "else_test";
}

const TEST = "A";
echo TEST;
*/
';

//$code = file_get_contents("/sdcard/www/public/php-parser/vendor/unphar.php");

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$stmts = $parser->parse($code);
//$oldTokens = $parser->getTokens();
//var_dump($stmts);


$dumper = new NodeDumper(['dumpComments' => true,]);
echo $dumper->dump($stmts, $code);


//var_dump($stmts);

class code{
	//metadata
	const TYPE_BYTE = 1;
	const TYPE_SHORT = 2;
	const TYPE_INT = 4;
	const TYPE_LONG = 8;
	const TYPE_DOUBLE = 9;

	const TYPE_SIZE_DOUBLE = 8;

	//opcode

	//valueOP(Scalar)
	const READV = "\xC1";//readv $a(0)
	const INT = "\xC2";
	const STRING = "\xC3";
	//const DOUBLE = "\xC4";
	//const READV = "\x00";

	//binaryOP
	const ADD = "\x02";//add 80 1000 $a
	const MUL = "\x03";
	const DIV = "\x04";
	const MINUS = "\x05";
	const B_AND = "\x06";
	const B_OR = "\x07";
	const B_XOR = "\x08";
	const BOOL_AND = "\x09";
	const BOOL_OR = "\x0A";
	const COALESCE = "\x0B";
	const CONCAT = "\x0C";

	const EQUAL = "\x0D";
	const GREATER = "\x0E";
	const GREATEROREQUAL = "\x0F";

	const IDENTICAL = "\x10";
	const L_AND = "\x11";
	const L_OR = "\x12";
	const L_XOR = "\x13";
	const MOD = "\x14";
	const NOTIDENTICAL = "\x15";
	const SHIFTLEFT = "\x16";
	const POW = "\x17";
	const SHIFTRIGHT = "\x18";
	const SMALLER = "\x19";
	const SMALLEROREQUAL = "\x1A";
	const SPACESHIP = "\x1B";
	const NOTEQUAL = "\x1C";
	const ABC = "\x1D";
	//const STRING = "\x1F";
	//const ABC = "\x1";
	//const ABC = "\x1";


	const PRINT = "\xf9";

}

class main_old{
	public $count = 0;


	/** @var int */
	public $blockid = 1;
	/** @var CodeBlock[] */
	public $block = [];

	public function __construct(){
		$this->block[$this->blockid] = new CodeBlock($this->blockid);
		var_dump($this->checkIntSize(254));
	}

	function execStmt($node){
		switch(get_class($node)){
			case Echo_::class:
				//var_dump("echo");
				//var_dump([...$this->execStmts($node->exprs), [code::PRINT."echo", $this->count]]);
				return [...$this->execStmts($node->exprs), [code::PRINT, $this->put_var($this->count)]];
				break;
			case if_::class:
				$return = $this->execExpr($node->cond);
				$stmts = $this->execStmts($node->stmts);
				$elseifs = $this->execStmts($node->elseifs);
				$else = $this->execStmts($node->else);

				//elseifs
				//else
				break;
			case Else_::class:
				break;
			case ElseIf_::class:
				break;

		}
	}

	function execStmts(array $nodes){
		$return = [];
		foreach($nodes as $node){
			if($node instanceof Expr){
				$return = array_merge($this->execExpr($node) ?? [], $return);
			}
			if($node instanceof Stmt){
				$return = array_merge($this->execStmt($node) ?? [], $return);
			}
			/*if($node instanceof node){

			}*/
		}
		return $return;
	}

	function encode_opcode_array(array $binaryOp){
		$binary = "";
		foreach($binaryOp as $value){
			foreach($value as $code){
				$binary .= $code;
			}
		}
		return $binary;
	}

	function execExpr(Expr $expr){
		switch(true){
			case $expr instanceof BinaryOp:
				$return = $this->execBinaryOp($expr);//array
				//$return2 = $this->toBinaryOp($return);
				//var_dump($return);

				//var_dump($this->decodeop_array($return));
				return $return;
			case $expr instanceof ConstFetch:
				return $expr->name->parts[0];//true
				break;
		}
	}

	function execBinaryOp($node, $count = 0): array{//output //add 10 10 1
		//var_dump($count);
		switch(get_class($node)){
			case Plus::class:
				return $this->execbinaryplus($node, code::ADD);
			case Mul::class:
				return $this->execbinaryplus($node, code::MUL);
			case Div::class:
				return $this->execbinaryplus($node, code::DIV);
			case Minus::class:
				return $this->execbinaryplus($node, code::MINUS);
			case BitwiseAnd::class:
				return $this->execbinaryplus($node, code::B_AND);
			case BitwiseOr::class:
				return $this->execbinaryplus($node, code::B_OR);
			case BooleanAnd::class:
				return $this->execbinaryplus($node, code::BOOL_AND);
			case BooleanOr::class:
				return $this->execbinaryplus($node, code::BOOL_OR);
			case Coalesce::class:
				return $this->execbinaryplus($node, code::COALESCE);
			case Concat::class:
				return $this->execbinaryplus($node, code::CONCAT);
			case Equal::class:
				return $this->execbinaryplus($node, code::EQUAL);
			case Greater::class:
				return $this->execbinaryplus($node, code::GREATER);
			case GreaterOrEqual::class:
				return $this->execbinaryplus($node, code::GREATEROREQUAL);
			case Identical::class:
				return $this->execbinaryplus($node, code::IDENTICAL);
			case LogicalAnd::class:
				return $this->execbinaryplus($node, code::L_AND);
			case LogicalOr::class:
				return $this->execbinaryplus($node, code::L_OR);
			case LogicalXor::class:
				return $this->execbinaryplus($node, code::L_XOR);
			case Mod::class:
				return $this->execbinaryplus($node, code::MOD);
			case NotEqual::class:
				return $this->execbinaryplus($node, code::NOTEQUAL);
			case NotIdentical::class:
				return $this->execbinaryplus($node, code::NOTIDENTICAL);
			case Pow::class:
				return $this->execbinaryplus($node, code::POW);
			case ShiftLeft::class:
				return $this->execbinaryplus($node, code::SHIFTLEFT);
			case ShiftRight::class:
				return $this->execbinaryplus($node, code::SHIFTRIGHT);
			case Smaller::class:
				return $this->execbinaryplus($node, code::SMALLER);
			case SmallerOrEqual::class:
				return $this->execbinaryplus($node, code::SMALLEROREQUAL);
			case Spaceship::class:
				return $this->execbinaryplus($node, code::SPACESHIP);
		}

	}

	function execbinaryplus($node, $id): array{
		$left = $this->execScalar_var($node->left);
		$basecount1 = $this->count;

		$right = $this->execScalar_var($node->right);
		$basecount2 = $this->count++;

		$count1 = $this->count;

		// id output Read_v.id Read_v.id

		$return = [];
		if(is_array($left)&&is_array($right)){
			//$count2 = ++$this->count;
			$return = [...$left, ...$right];
			$return[] = [$id, chr($count1), $this->put_var($basecount1), $this->put_var($basecount2)];//$left,$right
		}elseif(is_array($left)){
			/** @var array $left */
			$return = $left;
			$return[] = [$id, chr($count1), $this->put_var($basecount1), $right];
		}elseif(is_array($right)){
			/** @var array $right */
			$return = $right;
			$return[] = [$id, chr($count1), $left, $this->put_var($basecount2)];
		}else{
			$return[] = [$id, chr($count1), $left, $right];
		}
		//var_dump($return);
		return $return;
	}

	function execScalar_var($value){//array...?
		if($value instanceof Scalar){
			return $this->execScalar($value);
		}

		if($value instanceof BinaryOp){
			return $this->execBinaryOp($value);
			//return $this->execBinaryOp($value);
		}

		if($value instanceof Variable){
			return $this->exec_var($value);
		}

		if($value instanceof Expr){
			return $this->execExpr($value);//再帰...?
		}
	}


	function exec_var(Variable $node): string{//変数処理...
		if($node->name instanceof Expr){
			return "";//$$b
		}
		return code::READV.$this->getValualueId($node->name);
	}

	function put_var(int $var): string{
		return code::READV.chr($var);
	}

	public function getValualueId($value): string{
		return chr($this->block[$this->blockid]->get($value));
	}


	/*function execBinaryOp($node){
		switch(get_class($node)){
			case Plus::class:
				return "";
				break;
		}
	}*/

	function execScalar($node): string{
		var_dump(get_class($node));
		switch(get_class($node)){
			case LNumber::class:
			case DNumber::class:
				//$value = $node->value;
				//$intsize = $this->checkIntSize($value);
				//return [$intsize, $value];
				return $this->getInt($node->value);
			case LString::class;
				return $this->getString($node->value);

		}
	}

	/*public function decodeString($var, &$offset){

	}*/

	function getInt($value): string{
		//return $value."H";
		$size = $this->checkIntSize($value);
		$return = code::INT.chr($size);
		switch($size){
			case code::TYPE_BYTE://byte 1-byte
				$return .= Binary::writeByte($value);//Binary::readSignedByte($value);
				break;
			case code::TYPE_SHORT:// 2-byte
				$return .= Binary::writeLShort($value);
				break;
			case code::TYPE_INT://int 4-byte
				$return .= Binary::writeInt($value);
				break;
			case code::TYPE_LONG://long 8-byte
				$return .= Binary::writeLong($value);
				break;
			case code::TYPE_DOUBLE://Double 8-byte
				$return .= Binary::writeLDouble($value);
				break;
		}
		return $return;
	}

	/*public function getDouble($var){
		return code::INT.code::TYPE_DOUBLE.Binary::writeLDouble($var);//;
	}*/

	public function getString(string $value): string{
		$len = strlen($value);
		$return = code::STRING.$this->getInt($len).$value;//string_op int_op size int... string
		return $return;
	}


	function checkIntSize($value){
		if(is_float($value)){
			return code::TYPE_DOUBLE;
		}

		switch(true){
			case $value <= 127&&$value >= -128://byte
				return code::TYPE_BYTE;
			case $value <= 0xffff&&$value >= -0xffff://short
				return code::TYPE_SHORT;
			case $value <= 0x7FFFFFFF&&$value >= -0x7FFFFFFF://int
				return code::TYPE_INT;
			case $value <= 0x7FFFFFFFFFFFFFFF&&$value >= -0x7FFFFFFFFFFFFFFF://long
				return code::TYPE_LONG;
		}
	}
}

class CodeBlock{
	public $ids = [];
	public $values = [];
	public $nowvaluesid = 0;


	public $block;

	public function __construct($block){
		$this->block = $block;
	}

	public function getBlock(): int{
		return $this->block;
	}

	function get($value){
		return $this->ids[$value] ?? 1;//debug
	}

	function add($value){//変数名
		if(isset($this->values[$this->nowvaluesid + 1])){
			$this->values[++$this->nowvaluesid] = [];
		}
		$this->values[$this->nowvaluesid] = $value;
		$this->ids[$value] = $this->nowvaluesid;
		return $this->nowvaluesid;
	}

}

$main_old = new main_old();
$output = $main_old->execStmts($stmts);
var_dump($output);
$output = $main_old->encode_opcode_array($output);
var_dump($output);


function hexentities($str){
	$return = '';
	for($i = 0, $iMax = strlen($str); $i < $iMax; $i++){
		$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
	}
	return $return;
}

var_dump(hexentities($output));

$decoder = new decoder();
$decoder->decode($output);

//$main_old->decodeop_array($output);
//file_put_contents("output.txt", $output);
//var_dump(token_get_all($code));


/*
$tokens = token_get_all($code);

foreach ($tokens as $token) {
    if (is_array($token)) {
        echo "Line {$token[2]}: ", token_name($token[0]), " ('{$token[1]}')", PHP_EOL;
    }
}*/