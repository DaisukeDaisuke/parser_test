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
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use pocketmine\utils\Binary;

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

include __DIR__."/decoder.php";

$code = '
<?php
//echo ((2*1+1)+(2/1+3));
if(20 === 28){
//if(1+2===3){
	echo "test print";
}elseif(1===1){
	echo "a";
}elseif(1===1){
	echo "b";
}else{
	echo "c";
}

';

//$a = 100;
//echo ((2*1+1)+(2/1+3)-(2/(5*6+20)*(5*(6/2))))+7.4===true;//3+5+50
//echo 1+2*(3/$a*1);
/*
if(true){
	echo "test print";
}elseif(1===1){

}elseif(2===2){

}*/
/*
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
//$code = file_get_contents("/sdcard/www/public/php-parser/vendor/unphar.php");
/*
$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$stmts = $parser->parse($code);*/

//$oldTokens = $parser->getTokens();
//var_dump($stmts);

/*
$dumper = new NodeDumper(['dumpComments' => true,]);
echo $dumper->dump($stmts, $code);
*/
//var_dump($stmts);

class main_old2{
	public $count = 0;


	/** @var int */
	public $blockid = 1;
	/** @var CodeBlock[] */
	public $block = [];

	public function __construct(){
		$this->block[$this->blockid] = new CodeBlock($this->blockid);
		//var_dump($this->checkIntSize(254));
	}

	public function execStmt($node){
		$return = "";
		switch(get_class($node)){
			case Echo_::class:
				//var_dump("echo");
				//var_dump([...$this->execStmts($node->exprs), [code::PRINT."echo", $this->count]]);

				return $this->execStmts($node->exprs).code::PRINT.$this->put_var($this->count++);
				break;
			case PhpParser\Node\Stmt\If_::class://...?
				//ConstFetch
				$return = "";
				$expr = $this->execExpr($node->cond);
				$ifcount = $this->count++;

				//var_dump($this->hexentities($return));
				$elseifs = null;
				$else = null;

				if(isset($node->elseifs[0])){
					$elseifs = $this->execStmts($node->elseifs,true);
				}
				if(isset($node->else)){
					$else = $this->execStmt($node->else);
				}

				$stmts = $this->execStmts($node->stmts);

				//var_dump(["test",$else,$this->hexentities1($else)]);


				//var_dump($elseifs);
				$elseifs1 = [];
				$elseifs2 = "";
				if($elseifs !== null){
					$elseifs = array_reverse($elseifs);
					$count1 = count($elseifs)-1;
					$tmp1 = strlen($else);
					foreach($elseifs as $key => $else_array){
						list($ifcount_elseif, $return_elseif, $stmts_elseif) = $else_array;
						$tmp2 = count($elseifs1);
						//$elseifs1[$tmp2] = $return_elseif.code::JMPZ.$this->put_var($ifcount_elseif).$this->getInt(strlen($stmts_elseif)).$stmts_elseif;
						if($tmp1 === 0){
							var_dump($this->hexentities($stmts_elseif));
							$elseifs1[$tmp2] = $return_elseif.code::JMPZ.$this->put_var($ifcount_elseif).$this->getInt(strlen($stmts_elseif)).$stmts_elseif;
						}else{
							var_dump($this->hexentities($stmts_elseif));
							$elseifs1[$tmp2] = $return_elseif.code::JMPZ.$this->put_var($ifcount_elseif).$this->getInt(strlen($stmts_elseif.code::JMP.$this->getInt($tmp1))).$stmts_elseif.code::JMP.$this->getInt($tmp1);
						}
						$tmp1 += strlen($elseifs1[$tmp2]);
					}
					$elseifs1 = array_reverse($elseifs1);
					$elseifs2 = implode("",$elseifs1);
					//var_dump($elseifs2);
					//var_dump("elseifs1",$this->hexentities($elseifs2));
					/*$tmp = $stmts.
						code::JMP.$this->getInt(strlen($else));
					$return .=
						code::JMPZ.$this->put_var($ifcount).$this->getInt(strlen($tmp)).$tmp.$else;//-1 //if code::JMP
					*/
				}



				if($elseifs !== null){
					if($else !== null){
						//$tmp = $this->putjmp($else);
						//var_dump(["!!!!!!!!!!!!!!!!!!!!!!!!!!!!",$tmp]);
						//$elseifs2 .= $this->putjmpz($ifcount,$tmp);//-1 //if code::JMP
						if(count($elseifs1) === 0){
							$elseifs1[] = $else;
						}
						$elseifs2 .= $else;
					}

					$tmp1 = $stmts.$this->putjmp($elseifs2);

					//$tmp = $expr.$this->putjmpz($ifcount,$tmp1);// $elseifs1[0]
					//$tmp = $expr.code::JMPZ.$this->put_var($ifcount).$this->getInt(strlen($elseifs1[count($elseifs1)-1])).$tmp1;
					$return .= $expr.code::JMPZ.$this->put_var($ifcount).$this->getInt(strlen($elseifs1[count($elseifs1)-1])).$tmp1;//-1 //if code::JMP // $this->getInt(strlen($tmp))
				}elseif($else !== null){
					$tmp1 = $stmts.$this->putjmp($else,true);
					$return .= $expr.$this->putjmpz($ifcount,$tmp1).$else;
				}else{
					$return .= $expr.$this->putjmpz($ifcount, $stmts);//-1 //if code::JMP // $this->getInt(strlen($tmp))
				}
			 return $return;
				break;
			case Else_::class:
				return $this->execStmts($node->stmts);//JMPZ
				break;
			case ElseIf_::class:
				//var_dump("ElseIf_");
				$return = $this->execExpr($node->cond);
				//var_dump($this->hexentities($return));
				$ifcount = $this->count++;

				//var_dump($ifcount);
				$stmts = $this->execStmts($node->stmts);

				//var_dump($stmts);

				//var_dump(["stmts",$this->hexentities($stmts)]);
				//$return1 = $return.code::JMPZ.$this->put_var($ifcount).$this->getInt(strlen($stmts)).$stmts;
				//var_dump($this->hexentities($return1));

				return [$ifcount, $return, $stmts];
				break;

		}
	}

	public function execStmts(array $nodes,$array = false){
		/** @var string|string[] $return */
		$return = "";
		if($array === true){
			$return = [];
		}

		foreach($nodes as $node){
			if($node instanceof Expr){
				$root = false;
				$return1 = $this->execExpr($node, $root) ?? "";
				//var_dump(["??Expr",$root,$return1,$return1[0] === code::STRING]);

				if($root === false){
					$return1 = code::WRITEV.$this->write_varId($this->count).$return1;
				}
				$return .= $return1.$return;

			}
			if($node instanceof Stmt){
				if($node instanceof Nop){
					continue;
				}
				if($array === true){
					/** @var string[] $return */
					$return[] = $this->execStmt($node);
				}else{
					$return .= ($this->execStmt($node) ?? "").$return;
				}
			}
			/*if($node instanceof node){

			}*/
		}
		return $return;
	}

	public function encode_opcode_array(array $binaryOp){
		$binary = "";
		foreach($binaryOp as $value){
			foreach($value as $code){
				$binary .= $code;
			}
		}
		return $binary;
	}

	public function execBinaryOp($node, $count = 0): string{//output //add 10 10 1
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

	public function execbinaryplus($node, $id): string{
		$recursionLeft = false;
		$recursionRight = false;

		$left = $this->execExpr($node->left, $recursionLeft);
		$basecount1 = $this->count++;

		$right = $this->execExpr($node->right, $recursionRight);
		$basecount2 = $this->count++;

		$count1 = $this->count;

		//var_dump([$recursionLeft, $recursionRight]);

		// id output Read_v.id Read_v.id

		$return = "";
		if($recursionLeft&&$recursionRight){
			//$count2 = ++$this->count;
			$return = $left.$right;
			$return .= $id.chr($count1).$this->put_var($basecount1).$this->put_var($basecount2);//$left,$right
		}elseif($recursionLeft){
			//$return = $left;
			$return .= $left.$id.chr($count1).$this->put_var($basecount1).$right;
		}elseif($recursionRight){
			$return .= $right.$id.chr($count1).$left.$this->put_var($basecount2);
		}else{
			$return .= $id.chr($count1).$left.$right;
		}
		//var_dump($return);
		return $return;
	}

	public function execExpr($expr, &$recursion = false){//array...?
		switch(true){
			case $expr instanceof BinaryOp:
				$recursion = true;
				return $this->execBinaryOp($expr);
			case $expr instanceof ConstFetch:
				$recursion = true;
				$value = $expr->name->parts[0];
				//$return = $this->put_Scalar();
				if($value === "false"){
					return $this->write_var($this->count, 0);
				}

				if($value === "true"){
					return $this->write_var($this->count, 1);
				}

				return $expr->name->parts[0];//read const id(global...?)
			case $expr instanceof Scalar:
				return $this->execScalar($expr);
			case $expr instanceof Variable:
				return $this->exec_var($expr);
			case $expr instanceof Expr:
				$recursion = true;
				return $this->execExpr($expr);//再帰...?
				break;
		}
	}


	public function exec_var(Variable $node): string{//変数処理...
		if($node->name instanceof Expr){
			return "";//$$b
		}
		return code::READV.$this->getValualueId($node->name);
	}

	/**
	 * @param int $var
	 * @param $value
	 * @return string
	 *
	 * 指定した変数に指定した値を代入する指示を書きます...
	 */
	public function write_var(int $var, $value): string{
		return code::WRITEV.$this->write_varId($var).$this->put_Scalar($value);
	}

	public function write_varId(int $var): string{
		return chr($var);
	}

	/**
	 * @param int $var
	 * @return string
	 *
	 * 指定したidの変数を読みます...
	 */
	public function put_var(int $var): string{
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

	public function execScalar(Scalar $node): string{
		return $this->put_Scalar($node->value);
		/*switch(get_class($node)){
			case LNumber::class:
			case DNumber::class:
				//$value = $node->value;
				//$intsize = $this->checkIntSize($value);
				//return [$intsize, $value];
				return $this->getInt($node->value);
			case LString::class;
				return $this->getString($node->value);

		}*/
	}

	public function put_Scalar($value){
		switch(true){
			case is_int($value):
			case is_float($value):
				//$value = $node->value;
				//$intsize = $this->checkIntSize($value);
				//return [$intsize, $value];
				return $this->getInt($value);
			case is_string($value);
				return $this->getString($value);

		}
	}

	/*public function decodeString($var, &$offset){

	}*/

	public function getInt($value): string{
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


	public function checkIntSize($value){
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

	public function putjmpz(string $var,?string $stmts = null,?string $target = null){//0 => jmp
		if($target !== null){
			return code::JMPZ.$this->put_var($var).$this->getInt(strlen($target)).$stmts;
		}
		return code::JMPZ.$this->put_var($var).$this->getInt(strlen($stmts)).$stmts;
	}

	public function putjmp(string $stmts,$skip = false){
		if($skip === true){
			return code::JMP.$this->getInt(strlen($stmts));
		}
		return code::JMP.$this->getInt(strlen($stmts)).$stmts;
	}

	public function hexentities($str){
		$return = '';
		for($i = 0, $iMax = strlen($str); $i < $iMax;$i++){
			switch(substr($str, $i, 1)){
				case code::READV:
					$return .= ' READV:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' var:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::WRITEV:
					$return .= ' WRITEV:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::INT:
					$return .= ' INT:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' size:'.bin2hex(substr($str, $i, 1)).';';
					$size = ord(substr($str, $i++, 1));
					$return1 = 0;
					switch($size){
						case code::TYPE_BYTE://byte
							$return1 = Binary::readSignedByte(substr($str, $i, 1));
							$return .= ' '.$return1.':'.bin2hex(substr($str, $i, 1)).';';

							break;
						case code::TYPE_SHORT://short
							$return1 = Binary::readLShort(substr($str, $i, 2));
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
							break;
						case code::TYPE_INT://int
							$return1 = Binary::readInt(substr($str, $i, 4));
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
							break;
						case code::TYPE_LONG://long
							$return1 = Binary::readLong(substr($str, $i, 8));
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';

							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
							break;
						case code::TYPE_DOUBLE:
							$return1 =  Binary::readLDouble(substr($str, $i, 8));
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';

							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
							break;
					}

					break;
				case code::STRING:
					$return .= ' STRING:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' INT:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' size:'.bin2hex(substr($str, $i, 1)).';';
					$size = ord(substr($str, $i++, 1));
					//($size);
					$return1 = 0;
					switch($size){
						case code::TYPE_BYTE://byte
							$return .= ' '.Binary::readSignedByte(substr($str, $i, 1)).':'.bin2hex(substr($str, $i, 1)).';';
							$return1 = Binary::readSignedByte(substr($str, $i++, 1));
							break;
						case code::TYPE_SHORT://short
							$return1 = Binary::readLShort(substr($str, $i, 2));
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
							break;
						case code::TYPE_INT://int
							$return1 = Binary::readInt(substr($str, $i, 4));
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
							break;
						case code::TYPE_LONG://long
							$return1 = Binary::readLong(substr($str, $i, 8));
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';

							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
							break;
						case code::TYPE_DOUBLE:
							$return1 =  Binary::readLDouble(substr($str, $i, 8));
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';

							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i++, 1)).';';
							$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
							break;
					}
					for ($f = 1; $f <= $return1; $f++) {
						$return .= ' '.substr($str, $i, 1).':'.bin2hex(substr($str, $i++, 1)).';';
					}
					$i--;
					break;
				case code::ADD:
					$return .= ' ADD?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::MUL:
					$return .= ' MUL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::DIV:
					$return .= ' DIV?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::MINUS:
					$return .= ' MINUS?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::B_AND:
					$return .= ' B_AND?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::B_OR:
					$return .= ' B_OR?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::B_XOR:
					$return .= ' B_XOR?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::BOOL_AND:
					$return .= ' BOOL_AND?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::BOOL_OR:
					$return .= ' BOOL_OR?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::COALESCE:
					$return .= ' COALESCE?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::CONCAT:
					$return .= ' CONCAT?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;

				case code::EQUAL:
					$return .= ' EQUAL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::GREATER:
					$return .= ' GREATER?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::GREATEROREQUAL:
					$return .= ' GREATEROREQUAL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;

				case code::IDENTICAL:
					$return .= ' IDENTICAL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::L_AND:
					$return .= ' L_AND?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::L_OR:
					$return .= ' L_OR?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::L_XOR:
					$return .= ' L_XOR?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::MOD:
					$return .= ' MOD?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::NOTIDENTICAL:
					$return .= ' NOTIDENTICAL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::SHIFTLEFT:
					$return .= ' SHIFTLEFT?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::POW:
					$return .= ' POW?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::SHIFTRIGHT:
					$return .= ' SHIFTRIGHT?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::SMALLER:
					$return .= ' SMALLER?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::SMALLEROREQUAL:
					$return .= ' SMALLEROREQUAL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::SPACESHIP:
					$return .= ' SPACESHIP?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::NOTEQUAL:
					$return .= ' NOTEQUAL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::ABC:
					$return .= ' ABC?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::PRINT:
					$return .= ' PRINT:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::JMP:
					$return .= ' JMP:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::JMPZ:
					$return .= ' JMPZ:'.bin2hex(substr($str, $i, 1)).';';
					break;
				default:
					$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
			}
		}
		return $return;
	}

	function hexentities1($str){
		$return = '';
		for($i = 0, $iMax = strlen($str); $i < $iMax; $i++){
			$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
		}
		return $return;
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
/*
$main_old = new main_old();
$output = $main_old->execStmts($stmts);
var_dump($output);
*/
//$output = $main_old->encode_opcode_array($output);
//var_dump($output);


function hexentities($str){
	$return = '';
	for($i = 0, $iMax = strlen($str); $i < $iMax; $i++){
		$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
	}
	return $return;
}

//var_dump(["!!", hexentities($output),$main_old->hexentities($output)]);

//$decoder = new decoder();
//$decoder->decode($output);

//$main_old->decodeop_array($output);
//file_put_contents("output2.txt", $output);
//var_dump(token_get_all($code));


/*
$tokens = token_get_all($code);

foreach ($tokens as $token) {
    if (is_array($token)) {
        echo "Line {$token[2]}: ", token_name($token[0]), " ('{$token[1]}')", PHP_EOL;
    }
}*/