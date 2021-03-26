<?php
include __DIR__."/vendor/autoload.php";

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\BitwiseAnd;
use PhpParser\Node\Expr\BinaryOp\BitwiseOr;
use PhpParser\Node\Expr\BinaryOp\BitwiseXor;
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
use PhpParser\Node\Stmt\Nop;
use pocketmine\utils\Binary;

error_reporting(E_ALL);

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

include __DIR__."/decoder.php";

class main_old2{
	public $count = 0;
	public $label_count = 0;
	public $label = [];

	/** @var int */
	public $blockid = 1;
	/** @var CodeBlock[] */
	public $block = [];

	public function __construct(){
		$this->block[$this->blockid] = new CodeBlock($this->blockid);
	}

	public function execStmt(Stmt $node){
		$return = "";
		switch(get_class($node)){
			case Echo_::class:
				return $this->execStmts($node->exprs).code::PRINT.$this->put_var($this->count++);
				break;
			case PhpParser\Node\Stmt\If_::class://...?
				//ConstFetch
				$return = "";
				$label = $this->label_count;
				$expr = $this->execExpr($node->cond);
				$ifcount = $this->count++;
				$elseifs = null; // 00 = null
				$else = null;

				if(isset($node->elseifs[0])){
					$return6 = "";
					foreach($node->elseifs as $elseif){
						$elseifs .= $this->execExpr($elseif->cond).$this->putjmpz($this->count++, $this->execStmts($elseif->stmts).$this->putGotoLabel($label));
					}

				}
				if(isset($node->else)){
					$else = $this->execStmt($node->else);
				}

				$stmts = $this->execStmts($node->stmts).$this->putGotoLabel($label);

				return $this->solveLabel($expr.$this->putjmpz($ifcount, $stmts).$elseifs.$else, $label);//.$this->putLabel($label); $else
			case Else_::class:
				return $this->execStmts($node->stmts);//JMPZ
				break;
		}
	}

	public function solveLabel(string $exec, int $label): string{
		//return $exec;
		$array = [];
		$len = strlen($exec);
		for($i = 0, $iMax = strlen($exec); $i < $iMax;){
			switch($exec[$i]){
				case code::INT:
					$i++;
					$size = ord($exec[$i++]);
					switch($size){
						case code::TYPE_BYTE://byte
							$i++;
							break;
						case code::TYPE_SHORT://short
							$i += 2;
							break;
						case code::TYPE_INT://int
							$i += 4;
							break;
						case code::TYPE_LONG://long
							$i += 8;
							break;
						case code::TYPE_DOUBLE:
							$i += 8;
							break;
					}
					break;
				case code::STRING:
					$i++;
					$i++;
					$size = ord($exec[$i++]);
					//($size);
					$return1 = 0;
					switch($size){
						case code::TYPE_BYTE://byte
							$return1 = Binary::readSignedByte($exec[$i++]);
							break;
						case code::TYPE_SHORT://short
							$return1 = Binary::readLShort(substr($exec, $i, 2));
							$i += 2;
							break;
						case code::TYPE_INT://int
							$return1 = Binary::readInt(substr($exec, $i, 4));
							$i += 4;
							break;
						case code::TYPE_LONG://long
							$return1 = Binary::readLong(substr($exec, $i, 8));
							$i += 8;
							break;
						case code::TYPE_DOUBLE:
							$return1 = Binary::readLDouble(substr($exec, $i, 8));
							$i += 8;
							break;
					}
					for($f = 1; $f <= $return1; $f++){
						$i++;
					}
					break;
				case code::READV:
				case code::WRITEV:
				case code::ADD:
				case code::MUL:
				case code::DIV:
				case code::MINUS:
				case code::B_AND:
				case code::B_OR:
				case code::B_XOR:
				case code::BOOL_AND:
				case code::BOOL_OR:
				case code::COALESCE:
				case code::CONCAT:
				case code::EQUAL:
				case code::GREATER:
				case code::GREATEROREQUAL:
				case code::IDENTICAL:
				case code::L_AND:
				case code::L_OR:
				case code::L_XOR:
				case code::MOD:
				case code::NOTIDENTICAL:
				case code::SHIFTLEFT:
				case code::POW:
				case code::SHIFTRIGHT:
				case code::SMALLER:
				case code::SMALLEROREQUAL:
				case code::SPACESHIP:
				case code::NOTEQUAL:
				case code::ABC:
					$i += 3;
					break;
				case code::PRINT:
				case code::JMP:
				case code::JMPZ:
				case code::LABEL:
				case code::JMPA:
					$i++;
					break;
				case code::LGOTO:
					$start = $i;
					$i += 3;
					$array[] = [$start, 3, $i++];
					/*if($label === $return1){

					}*/
					break;
				default:
					$i++;
			}
		}
		$len = strlen($exec);
		foreach(array_reverse($array) as $value){
			[$start, $len1, $end] = $value; //$skip_replace
			$new = code::JMP.$this->getInt($len - ($end + 0));
			$exec = substr_replace($exec, '', $start, $len1);
			$exec = substr_replace($exec, $new, $start, 0);
			$len = strlen($exec);
		}
		return $exec;
	}

	public function execStmts(array $nodes, $array = false){
		/** @var string|string[] $return */
		$return = "";
		if($array === true){
			$return = [];
		}

		foreach($nodes as $node){
			if($node instanceof Expr){
				$root = false;
				$return1 = $this->execExpr($node, $root) ?? "";
				if($root === false){
					$return1 = code::WRITEV.$this->write_varId($this->count).$return1;
				}
				$return .= $return1;
			}
			if($node instanceof Stmt){
				if($node instanceof Nop){
					continue;
				}
				if($array === true){
					/** @var string[] $return */
					$return[] = $this->execStmt($node);
				}else{
					$return .= ($this->execStmt($node) ?? "");//.$return;
				}
			}
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
			case BitwiseXor::class:
				return $this->execbinaryplus($node, code::B_XOR);
		}
		throw new \RuntimeException('BinaryOp "'.get_class($node).'" is unprocessed.');
	}

	public function execbinaryplus($node, $id): string{
		$recursionLeft = false;
		$recursionRight = false;

		$left = $this->execExpr($node->left, $recursionLeft);
		$basecount1 = $this->count++;

		$right = $this->execExpr($node->right, $recursionRight);
		$basecount2 = $this->count++;

		$count1 = $this->count;

		// id output Read_v.id Read_v.id

		$return = "";
		if($recursionLeft&&$recursionRight){
			//$count2 = ++$this->count;
			$return = $left.$right;
			$return .= $id.$this->write_varId($count1).$this->put_var($basecount1).$this->put_var($basecount2);//$left,$right
		}elseif($recursionLeft){
			//$return = $left;
			$return .= $left.$id.$this->write_varId($count1).$this->put_var($basecount1).$right;
		}elseif($recursionRight){
			$return .= $right.$id.$this->write_varId($count1).$left.$this->put_var($basecount2);
		}else{
			$return .= $id.$this->write_varId($count1).$left.$right;
		}
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
		return Binary::writeShort($var);
	}

	/**
	 * @param int $var
	 * @return string
	 *
	 * 指定したidの変数を読みます...
	 */
	public function put_var(int $var): string{
		return code::READV.$this->write_varId($var);
	}

	public function getValualueId($value): string{
		return chr($this->block[$this->blockid]->get($value));
	}

	public function execScalar(Scalar $node): string{
		return $this->put_Scalar($node->value);
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

	public function getInt($value): string{
		return code::INT.$this->putRawInt($value);
	}

	function putRawInt($value): string{
		$size = $this->checkIntSize($value);
		$return = chr($size);
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

	public function getString(string $value): string{
		return code::STRING.$this->getInt(strlen($value)).$value;//string_op int_op size int... string
	}


	public function checkIntSize($value){
		if(is_float($value)){
			return code::TYPE_DOUBLE;
		}

		switch(true){
			case $value <= 126&&$value >= -127://byte overflow ff
				return code::TYPE_BYTE;
			case $value <= 0xffff&&$value >= -0xffff://short
				return code::TYPE_SHORT;
			case $value <= 0x7FFFFFFF&&$value >= -0x7FFFFFFF://int
				return code::TYPE_INT;
			case $value <= 0x7FFFFFFFFFFFFFFF&&$value >= -0x7FFFFFFFFFFFFFFF://long
				return code::TYPE_LONG;
		}
	}

	public function putjmpz(string $var, ?string $stmts = null, ?string $target = null){//0 => jmp
		if($target !== null){
			return code::JMPZ.$this->put_var($var).$this->getInt(strlen($target)).$stmts;
		}
		return code::JMPZ.$this->put_var($var).$this->getInt(strlen($stmts)).$stmts;
	}

	public function putjmp(string $stmts, $skip = false){
		if($skip === true){
			return code::JMP.$this->getInt(strlen($stmts));
		}
		return code::JMP.$this->getInt(strlen($stmts)).$stmts;
	}

	public function putGotoLabel($label){
		return code::LGOTO.$this->getInt($label);
	}

	public function putLabel($label){
		return code::LABEL.$this->getInt($label);
	}
}
