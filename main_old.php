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
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use pocketmine\utils\Binary;

use PhpParser\Node\Stmt\if_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;


$code = '
<?php
$a = 100;
//echo 1+2*(3/$a*1);
echo ((2*1+1)+(2/1+3)-(2/(5*6+20)*(5*(6/2)))) & 1;//3+5+50
if(true===true){
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
';

//$code = file_get_contents("/sdcard/www/public/php-parser/vendor/unphar.php");

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$stmts = $parser->parse($code);
//$oldTokens = $parser->getTokens();
//var_dump($stmts);

/*
$dumper = new NodeDumper(['dumpComments' => true,]);
echo $dumper->dump($stmts, $code);
*/

//var_dump($stmts);

class code{

	const READV = "\x00";//readv $a(0)
	const INT = "\x01";
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
	//const ABC = "\x1";
	//const ABC = "\x1";


	const PRINT = "\xff";

}

class main_old{
	const TYPE_BYTE = 0;
	const TYPE_SHORT = 1;
	const TYPE_INT = 2;
	const TYPE_LONG = 3;

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
		//var_dump("execStmt");
		switch(get_class($node)){
			case Echo_::class:
				//var_dump("echo");
				return code::PRINT.$this->execStmts($node->exprs);
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
		$return = "";
		foreach($nodes as $node){
			if($node instanceof Expr){
				$return .= $this->execExpr($node);
			}
			if($node instanceof Stmt){
				$return .= $this->execStmt($node);
			}
			/*if($node instanceof node){

			}*/
		}
		return $return;
	}

	function toBinaryOp(array $binaryOp){
		$binary = "";
		foreach($binaryOp as $value){
			$var = $value[0];
			$output = chr($value[3]);
			$var1 = $this->getInt($value[1]);
			$var2 = $this->getInt($value[2]);

			$binary .= $var.$output.$var1.$var2;
		}
		return $binary;
	}

	function readValue($var, &$i, $values){
		if($var[$i++] === code::READV){
			return $values[ord($var[$i++])];
		}elseif($var[$i++] === code::INT){
			$size = ord($var[$i++]);
			switch($size){
				case self::TYPE_BYTE://byte
					return Binary::readSignedByte($var[$i++]);
					break;
				case self::TYPE_SHORT://short
					return Binary::readLShort($var[$i++].$var[$i++]);
					break;
				case self::TYPE_INT://int
					return Binary::readInt($var[$i++].$var[$i++].$var[$i++].$var[$i++]);
					break;
				case self::TYPE_LONG://long
					return Binary::readLong($var[$i++].$var[$i++].$var[$i++].$var[$i++].$var[$i++].$var[$i++].$var[$i++].$var[$i++]);
					break;
			}
		}
	}

	function execExpr(Expr $expr){
		switch(true){
			case $expr instanceof BinaryOp:
				$return = $this->execBinaryOp($expr);//array
				$return2 = $this->toBinaryOp($return);
				var_dump($return);
				$values = [];
				$len = strlen($return2);
				for($i = 1; $i <= $len; $i++){
					$var = $return2[$i];

					$var1 = $this->test($value[1], $values);//
					$var2 = $this->test($value[2], $values);
					$output = $value[3];

					/** @var mixed $return1 */
					$return1 = 0;

					switch($var){
						case code::ADD:
							$return1 = $var1 + $var2;
							break;
						case code::MUL:
							$return1 = $var1 * $var2;
							break;
						case code::DIV:
							$return1 = $var1 / $var2;
							break;
						case code::MINUS:
							$return1 = $var1 - $var2;
							break;
						case code::B_AND:
							$return1 = $var1 & $var2;
							break;
						case code::B_OR:
							$return1 = $var1 | $var2;
							break;
						case code::B_XOR:
							$return1 = $var1 ^ $var2;
							break;
						case code::BOOL_AND:
							$return1 = (int) $var1&&$var2;
							break;
						case code::BOOL_OR:
							$return1 = (int) $var1||$var2;
							break;
						case code::COALESCE:
							//$return1 = $var1 ?? $var2;
							break;
						case code::CONCAT:
							$return1 = $var1.$var2;
							break;
						case code::EQUAL:
							$return1 = (int) $var1 == $var2;
							break;
						case code::GREATER:
							$return1 = (int) $var1 > $var2;
							break;
						case code::GREATEROREQUAL:
							$return1 = (int) $var1 >= $var2;
							break;
						case code::IDENTICAL:
							$return1 = (int) $var1 === $var2;
							break;
						case code::L_AND:
							$return1 = (int) $var1 and $var2;
							break;
						case code::L_OR:
							$return1 = (int) $var1 or $var2;
							break;
						case code::L_XOR:
							$return1 = $var1 xor $var2;
							break;
						case code::MOD:
							$return1 = $var1 % $var2;
							break;
						case code::NOTEQUAL:
							$return = (int) $var1 != $var2;
							break;
						case code::NOTIDENTICAL:
							$return1 = (int) $var1 !== $var2;
							break;
						case code::SHIFTLEFT:
							$return1 = $var1 << $var2;
							break;
						case code::POW:
							$return1 = $var1 ** $var2;
							break;
						case code::SHIFTRIGHT:
							$return1 = $var1 >> $var2;
							break;
						case code::SMALLER:
							$return1 = (int) $var1 < $var2;
							break;
						case code::SMALLEROREQUAL:
							$return1 = (int) $var1 <= $var2;
							break;
						case code::SPACESHIP:
							$return1 = (int) $var1 <=> $var2;
							break;
					}
					var_dump($output." => ".$return1);
					$values[$output] = $return1;
					//var_dump($values);
				}
				//var_dump($return1);
				//return var_dump($this->execBinaryOp($expr));
				return $return2;
			break;
			case $expr instanceof ConstFetch:
				return $expr->name->parts[0];//true
				break;
		}
	}

	function test($value, &$array){
		if(strpos($value, 'H') !== false){
			return (int) substr($value, 0, -1);
		}
		//var_dump($value);
		$return = $array[$value];
		unset($array[$value]);
		return $return;
	}

	function execBinaryOp($node, $count = 0){//output //add 10 10 1
		//var_dump($count);
		switch(get_class($node)){
			case Plus::class:
				return $this->execbinaryplus($node, code::ADD, $count);
			case Mul::class:
				return $this->execbinaryplus($node, code::MUL, $count);
			case Div::class:
				return $this->execbinaryplus($node, code::DIV, $count);
			case Minus::class:
				return $this->execbinaryplus($node, code::MINUS, $count);
			case BitwiseAnd::class:
				return $this->execbinaryplus($node, code::B_AND, $count);
			case BitwiseOr::class:
				return $this->execbinaryplus($node, code::B_OR, $count);
			case BooleanAnd::class:
				return $this->execbinaryplus($node, code::BOOL_AND, $count);
			case BooleanOr::class:
				return $this->execbinaryplus($node, code::BOOL_OR, $count);
			case Coalesce::class:
				return $this->execbinaryplus($node, code::COALESCE, $count);
			case Concat::class:
				return $this->execbinaryplus($node, code::CONCAT, $count);
			case Equal::class:
				return $this->execbinaryplus($node, code::EQUAL, $count);
			case Greater::class:
				return $this->execbinaryplus($node, code::GREATER, $count);
			case GreaterOrEqual::class:
				return $this->execbinaryplus($node, code::GREATEROREQUAL, $count);
			case Identical::class:
				return $this->execbinaryplus($node, code::IDENTICAL, $count);
			case LogicalAnd::class:
				return $this->execbinaryplus($node, code::L_AND, $count);
			case LogicalOr::class:
				return $this->execbinaryplus($node, code::L_OR, $count);
			case LogicalXor::class:
				return $this->execbinaryplus($node, code::L_XOR, $count);
			case Mod::class:
				return $this->execbinaryplus($node, code::MOD, $count);
			case NotEqual::class:
				return $this->execbinaryplus($node, code::NOTEQUAL, $count);
			case NotIdentical::class:
				return $this->execbinaryplus($node, code::NOTIDENTICAL, $count);
			case Pow::class:
				return $this->execbinaryplus($node, code::POW, $count);
			case ShiftLeft::class:
				return $this->execbinaryplus($node, code::SHIFTLEFT, $count);
			case ShiftRight::class:
				return $this->execbinaryplus($node, code::SHIFTRIGHT, $count);
			case Smaller::class:
				return $this->execbinaryplus($node, code::SMALLER, $count);
			case SmallerOrEqual::class:
				return $this->execbinaryplus($node, code::SMALLEROREQUAL, $count);
			case Spaceship::class:
				return $this->execbinaryplus($node, code::SPACESHIP, $count);
		}

	}

	function execbinaryplus($node, $id, $count = 0){
		$left = $this->execScalar_var($node->left, $count + 1);
		$right = $this->execScalar_var($node->right, $count + 2);

		$return = [];
		if(is_array($left)&&is_array($right)){
			$return = [...$left, ...$right];
			$return[] = [$id, $count + 1, $count + 2, $count];//$left,$right
		}else if(is_array($left)){
			$return = $left;
			$return[] = [$id, $count + 1, $right, $count];
		}else if(is_array($right)){
			$return = $right;
			$return[] = [$id, $left, $count + 2, $count];
		}else{
			//$count++;
			$return[] = [$id, $left, $right, $count];
			//$count-=2;

		}
		//var_dump($return);
		return $return;
	}

	function execScalar_var($value, $count){//array...?
		if($value instanceof Scalar){
			return $this->execScalar($value);
		}

		if($value instanceof BinaryOp){
			return $this->execBinaryOp($value, $count);
			//return $this->execBinaryOp($value);
		}

		if($value instanceof Variable){
			return $this->exec_var($value);
		}

		if($value instanceof Expr){
			return $this->execExpr($value);//再帰...?
		}
	}


	function exec_var(Variable $node){//変数処理...
		if($node->name instanceof Expr){
			return "";//$$b
		}
		return code::READV.$this->getValualueId($node->name);
	}

	public function getValualueId($value){
		return chr($this->block[$this->blockid]->get($value));
	}


	/*function execBinaryOp($node){
		switch(get_class($node)){
			case Plus::class:
				return "";
				break;
		}
	}*/

	function execScalar($node){
		switch(get_class($node)){
			case LNumber::class:
				//$value = $node->value;
				//$intsize = $this->checkIntSize($value);
				//return [$intsize, $value];
				return $this->getInt($node->value);
				break;
		}
	}

	function getInt($value){
		//return $value."H";
		$size = $this->checkIntSize($value);
		$return = code::INT.chr($size);
		switch($size){
			case self::TYPE_BYTE://byte
				$return .= Binary::writeByte($value);//Binary::readSignedByte($value);
				break;
			case self::TYPE_SHORT://short
				$return .= Binary::writeLShort($value);
				break;
			case self::TYPE_INT://int
				$return .= Binary::writeInt($value);
				break;
			case self::TYPE_LONG://long
				$return .= Binary::writeLong($value);
				break;
		}
		return $return;
	}

	function checkIntSize($value){
		switch(true){
			case $value <= 127&&$value >= -128://byte
				return self::TYPE_BYTE;
			case $value <= 0xffff&&$value >= -0xffff://short
				return self::TYPE_SHORT;
			case $value <= 0x7FFFFFFF&&$value >= -0x7FFFFFFF://int
				return self::TYPE_INT;
			case $value <= 0x7FFFFFFFFFFFFFFF&&$value >= -0x7FFFFFFFFFFFFFFF://long
				return self::TYPE_LONG;
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
//file_put_contents("output.txt", $output);
//var_dump(token_get_all($code));


/*
$tokens = token_get_all($code);

foreach ($tokens as $token) {
    if (is_array($token)) {
        echo "Line {$token[2]}: ", token_name($token[0]), " ('{$token[1]}')", PHP_EOL;
    }
}*/