<?php

namespace purser;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
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
use PhpParser\Node\Expr\PreDec;
use PhpParser\Node\Expr\PreInc;
use PhpParser\Node\Expr\Print_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Nop;
use pocketmine\utils\Binary;

error_reporting(E_ALL);

ini_set('xdebug.var_display_max_children', "-1");
ini_set('xdebug.var_display_max_data', "-1");
ini_set('xdebug.var_display_max_depth', "-1");

class main_old2{
	/** @var int $count */
	public $count = 1;
	/** @var int $label_count */
	public $label_count = 0;
	//public $label = [];

	/** @var int $blockid */
	public $blockid = 1;
	/** @var CodeBlock[] $block */
	public $block = [];

	public function __construct(){
		$this->block[$this->blockid] = new CodeBlock($this->blockid);
	}

	/**
	 * @param Stmt $node
	 * @return string
	 */
	public function execStmt(Stmt $node) : string{
		$return = "";
		switch(get_class($node)){
			case Echo_::class:
				if(!is_array($node->exprs)){
					return $this->execStmts($node->exprs).code::PRINT.$this->put_var($this->count++);
				}
				$result = "";
				foreach($node->exprs as $expr){
					if($expr instanceof Variable){
						$result .= code::PRINT.$this->exec_variable($expr,$this->count);
						//$this->count++;//!!!!!!!!!
						continue;
					}
					if($expr instanceof Assign){//echo $i = 100;
						/** @var Variable $var */
						$var = $expr->var;
						$result .= $this->execExpr($expr).code::PRINT.$this->exec_variable($var,$this->count);
						//$this->count++;
						continue;
					}
					$return .= $this->execStmts([$expr]);//
					$result .= code::PRINT.$this->put_var($this->count++);
				}
				$return .= $result;

				return $return;
			case If_::class://...?
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
			case Expression::class:
				return $this->execExpr($node->expr);
		}
		return "";//code::nop
	}

	/**
	 * @param Expr $expr
	 * @param int|null $outputid
	 * @param bool $recursion
	 * @param ?int $is_var
	 * @return string
	 */
	public function execExpr(Expr $expr,?int $outputid = null, bool &$recursion = false, ?int &$is_var = null){//array...?
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
				//$recursion = true;//!!!!!!!!!
				//$is_var = true;

				$id = $this->exec_variable($expr,$this->count);
				return $id;
			case $expr instanceof PreInc://++$i;
				$recursion = true;//!!!!!!!!!
				//$is_var = true;
				//$var = $expr->var;
				$var = $this->execExpr($expr->var);
				return code::ADD.$var.code::READV.$var.code::INT.$this->putRawInt(1);
			case $expr instanceof PreDec://++$i;
				$recursion = true;//!!!!!!!!!
				//$is_var = true;
				$var = $this->execExpr($expr->var);
				return code::MINUS.$var.code::READV.$var.code::INT.$this->putRawInt(1);
			case $expr instanceof Assign:
				//var_dump("!!!!!!!!!!!!!!!!!");

				//$id = $this->execExpr($expr->var);
				;
				/** @var Variable $value */
				$value = $expr->var;

				//$baseid = $this->count++;


				$oldid1 = $this->count;


				//$baseid = $this->count;

				$baseid = $this->count;
				//$this->count++;
				$id1 = $this->exec_variable($value, $baseid,false,$is_var);

				$recursion1 = false;
				if($expr->expr instanceof BinaryOp){
					$recursion1 = true;
					$content = $this->execBinaryOp($expr->expr, $is_var ?? $baseid);
				}else{
					$this->count++;
					$content = $this->execExpr($expr->expr,$is_var ?? $baseid, $recursion);
				}


				/*$content1 = "";
				if($is_var !== null){
					$content1 .= code::WRITEV.$this->write_varId($is_var).code::READV.$this->write_varId($baseid);
				}*/
				//var_dump([$oldid1,$is_var,$baseid]);


				if($recursion === false&&$recursion1 === false){
					$content = code::WRITEV.$this->write_varId($is_var ?? $baseid).$content;
					//$this->count++;
				}
				//$this->count++;
				//$count = $this->count++;
				//var_dump(opcode_dumper::hexentities($content));//割り当て... copy //.code::WRITEV.$this->put_Scalar($count).$this->put_var($this->count))
				//var_dump($id,$content);
				return $content;//.$this->put_Scalar($count).$this->put_var($this->count);//$id
			case $expr instanceof Print_:
				$recursion = true;//
				if($expr->expr instanceof Variable){
					return code::PRINT.$this->exec_variable($expr->expr, $this->count++).$this->write_var($this->count, 1);
				}
				if($expr->expr instanceof Assign){//print $i = 100;
					/** @var Variable $var */
					$var = $expr->expr->var;
					return $this->execExpr($expr->expr).code::PRINT.$this->exec_variable($var,$this->count).$this->write_var($outputid ?? $this->count, 1);;
				}
				return $this->execStmts([$expr->expr]).code::PRINT.$this->put_var($this->count++).$this->write_var($outputid ?? $this->count, 1);
			case $expr instanceof Expr:
				//var_dump(get_class($expr));
				$recursion = true;
				return $this->execExpr($expr);//再帰...?

		}
		throw new \RuntimeException('execExpr "'.get_class($expr).'" not found');
	}

	public function exec_variable(Variable $node, int $id, bool $force = false,?int &$oldid = null) : string{//変数処理...
		if($node->name instanceof Expr){
			//var_dump("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
			if($node->name instanceof Variable){
				return "";//$$b
			}else{//binaryop...? //$i+100...?

			}
			return "";//!!
		}
		//return $this->write_variableId($this->count);
		return $this->write_variableId($this->getValualueId($node->name, $force, $id, $oldid));//code::VALUE
	}

	public function write_variableId(int $node) : string{//変数処理...
		return code::VALUE.$this->write_varId($node);//code::VALUE
	}

	public function solveLabel(string $exec, int $label) : string{
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
							$return1 = Binary::readSignedShort(substr($exec, $i, 2));
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
			/** @var string $exec */
			$exec = substr_replace($exec, '', $start, $len1);
			$exec = substr_replace($exec, $new, $start, 0);
			$len = strlen($exec);
		}
		return $exec;
	}

	/**
	 * @param Stmt[]|Expr[] $nodes
	 * @return string
	 */
	public function execStmts(array $nodes){//,bool $array = false
		$return = "";
		/*if($array === true){
			$return = [];
		}*/

		foreach($nodes as $node){
			if($node instanceof Expr){
				$root = false;
				$return1 = $this->execExpr($node,null, $root) ?? "";
				if($root === false){
					$return1 = code::WRITEV.$this->write_varId($this->count).$return1;
				}
				$return .= $return1;
			}
			if($node instanceof Stmt){
				if($node instanceof Nop){
					continue;
				}
				//if($array === true){
				//$return[] = $this->execStmt($node);
				//}else{
				$return .= ($this->execStmt($node) ?? "");//.$return;
				//}
			}
		}
		return $return;
	}

	/*public function encode_opcode_array(array $binaryOp){
		$binary = "";
		foreach($binaryOp as $value){
			foreach($value as $code){
				$binary .= $code;
			}
		}
		return $binary;
	}*/

	/**
	 * @param BinaryOp $node
	 * @return string
	 * @throws \RuntimeException
	 */
	public function execBinaryOp(BinaryOp $node,?int $outputid = null) : string{//output //add 10 10 1 $count = 0
		switch(get_class($node)){
			case Plus::class:
				return $this->execbinaryplus($node, code::ADD,$outputid);
			case Mul::class:
				return $this->execbinaryplus($node, code::MUL,$outputid);
			case Div::class:
				return $this->execbinaryplus($node, code::DIV,$outputid);
			case Minus::class:
				return $this->execbinaryplus($node, code::MINUS,$outputid);
			case BitwiseAnd::class:
				return $this->execbinaryplus($node, code::B_AND,$outputid);
			case BitwiseOr::class:
				return $this->execbinaryplus($node, code::B_OR,$outputid);
			case BooleanAnd::class:
				return $this->execbinaryplus($node, code::BOOL_AND,$outputid);
			case BooleanOr::class:
				return $this->execbinaryplus($node, code::BOOL_OR,$outputid);
			case Coalesce::class:
				return $this->execbinaryplus($node, code::COALESCE,$outputid);
			case Concat::class:
				return $this->execbinaryplus($node, code::CONCAT,$outputid);
			case Equal::class:
				return $this->execbinaryplus($node, code::EQUAL,$outputid);
			case Greater::class:
				return $this->execbinaryplus($node, code::GREATER,$outputid);
			case GreaterOrEqual::class:
				return $this->execbinaryplus($node, code::GREATEROREQUAL,$outputid);
			case Identical::class:
				return $this->execbinaryplus($node, code::IDENTICAL,$outputid);
			case LogicalAnd::class:
				return $this->execbinaryplus($node, code::L_AND,$outputid);
			case LogicalOr::class:
				return $this->execbinaryplus($node, code::L_OR,$outputid);
			case LogicalXor::class:
				return $this->execbinaryplus($node, code::L_XOR,$outputid);
			case Mod::class:
				return $this->execbinaryplus($node, code::MOD,$outputid);
			case NotEqual::class:
				return $this->execbinaryplus($node, code::NOTEQUAL,$outputid);
			case NotIdentical::class:
				return $this->execbinaryplus($node, code::NOTIDENTICAL,$outputid);
			case Pow::class:
				return $this->execbinaryplus($node, code::POW,$outputid);
			case ShiftLeft::class:
				return $this->execbinaryplus($node, code::SHIFTLEFT,$outputid);
			case ShiftRight::class:
				return $this->execbinaryplus($node, code::SHIFTRIGHT,$outputid);
			case Smaller::class:
				return $this->execbinaryplus($node, code::SMALLER,$outputid);
			case SmallerOrEqual::class:
				return $this->execbinaryplus($node, code::SMALLEROREQUAL,$outputid);
			case Spaceship::class:
				return $this->execbinaryplus($node, code::SPACESHIP,$outputid);
			case BitwiseXor::class:
				return $this->execbinaryplus($node, code::B_XOR,$outputid);
		}
		throw new \RuntimeException('BinaryOp "'.get_class($node).'" is unprocessed.');
	}

	/**
	 * @param BinaryOp $node
	 * @param string $opcode binaryid
	 * @return string
	 */
	public function execbinaryplus(BinaryOp $node, string $opcode,?int $outputid = null) : string{
		$recursionLeft = false;
		$recursionRight = false;

		$left = $this->execExpr($node->left,null, $recursionLeft, $is_varleft);
		$basecount1 = $this->count++;

		$right = $this->execExpr($node->right,null, $recursionRight, $is_varright);
		$basecount2 = $this->count++;

		//var_dump([$is_varleft,$is_varright]);


		$count1 = $outputid ?? $this->count;

		// id output Read_v.id Read_v.id

		//!!

		$return = "";
		if($recursionLeft&&$recursionRight){
			/*if($is_varleft&&$is_varright){
				$return .= $opcode.$this->write_varId($count1).$this->write_variableId($basecount1).$this->write_variableId($basecount2);
			}elseif($is_varleft&&!$is_varright){
				$return .= $opcode.$this->write_varId($count1).$this->write_variableId($basecount1).$this->write_variableId($basecount2);
			}elseif(!$is_varleft&&$is_varright){
				$return .= $opcode.$this->write_varId($count1).$this->write_variableId($basecount1).$this->write_variableId($basecount2);
			}else{*/
			$return = $left.$right;
			$return .= $opcode.$this->write_varId($count1).$this->put_var($basecount1).$this->put_var($basecount2);//$left,$right
			//}
			//$count2 = ++$this->count;

		}elseif($recursionLeft){
			//$return = $left;
			$return .= $left.$opcode.$this->write_varId($count1).$this->put_var($basecount1).$right;
		}elseif($recursionRight){
			$return .= $right.$opcode.$this->write_varId($count1).$left.$this->put_var($basecount2);
		}else{
			$return .= $opcode.$this->write_varId($count1).$left.$right;
		}
		return $return;
	}

	/**
	 * 指定した変数に指定した値を代入する指示を書きます
	 *
	 * @param int $var
	 * @param mixed $value
	 * @return string
	 */
	public function write_var(int $var, $value) : string{
		return code::WRITEV.$this->write_varId($var).$this->put_Scalar($value);
	}

	public function write_varId(int $var) : string{

		return Binary::writeShort($var);
	}

	/**
	 * @param int $var
	 * @return string
	 *
	 * 指定したidの変数を読みます...
	 */
	public function put_var(int $var) : string{
		return code::READV.$this->write_varId($var);
	}

	/**
	 * @param string $value
	 * @param bool $force
	 * @param int $id
	 * @param int|null $oldid
	 * @return int
	 * @see exec_var
	 */
	public function getValualueId(string $value, bool $force,int $id, ?int &$oldid) : int{
		return $this->block[$this->blockid]->get($value, $id, $force, $oldid);//$this->write_varId();
	}

	public function execScalar(Scalar $node) : string{
		switch(true){
			case $node instanceof LNumber:
			case $node instanceof DNumber:
			case $node instanceof String_:
				return $this->put_Scalar($node->value);
			default:
				throw new \RuntimeException('scalar "'.get_class($node).'" is unprocessed.');
		}
	}

	/**
	 * @param mixed $value
	 * @return string
	 * @see execScalar
	 *
	 */
	public function put_Scalar($value){
		if(is_object($value)){
			throw new \RuntimeException('The function "put_Scalar" cannot accept the object "'.get_class($value).'".');
		}
		switch(true){
			case is_int($value):
			case is_float($value):
				//$value = $node->value;
				//$intsize = $this->checkIntSize($value);
				//return [$intsize, $value];
				return $this->getInt($value);
			case is_string($value);
				return $this->getString($value);
			default:
				throw new \RuntimeException('put_Scalar: "'.$value.'" is unprocessed.');
		}
	}

	/**
	 * @param float|int $value
	 * @return string
	 */
	public function getInt($value) : string{
		return code::INT.$this->putRawInt($value);
	}

	/**
	 * @param float|int $value
	 * @return string
	 */
	function putRawInt($value) : string{
		$size = $this->checkIntSize($value);
		$return = chr($size);
		switch($size){
			case code::TYPE_BYTE://byte 1-byte
				/** @var int $value */
				$return .= Binary::writeByte($value);//Binary::readSignedByte($value);
				break;
			case code::TYPE_SHORT:// 2-byte
				/** @var int $value */
				$return .= Binary::writeShort($value);
				break;
			case code::TYPE_INT://int 4-byte
				/** @var int $value */
				$return .= Binary::writeInt($value);
				break;
			case code::TYPE_LONG://long 8-byte
				/** @var int $value */
				$return .= Binary::writeLong($value);
				break;
			case code::TYPE_DOUBLE://Double 8-byte
				/** @var float $value */
				$return .= Binary::writeLDouble($value);
				break;
		}
		return $return;
	}

	public function getString(string $value) : string{
		return code::STRING.$this->getInt(strlen($value)).$value;//string_op int_op size int... string
	}


	/**
	 * @param float|int $value
	 * @return int
	 * @throws \RuntimeException
	 */
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
		throw new \RuntimeException("checkIntSize overflow [".$value."]");
	}

	public function putjmpz(int $var, string $stmts, ?string $target = null) : string{//0 => jmp
		if($target !== null){
			return code::JMPZ.$this->put_var($var).$this->getInt(strlen($target)).$stmts;//
		}
		return code::JMPZ.$this->put_var($var).$this->getInt(strlen($stmts)+1).$stmts;
	}

	public function putjmp(string $stmts, bool $skip = false) : string{
		if($skip === true){
			return code::JMP.$this->getInt(strlen($stmts));
		}
		return code::JMP.$this->getInt(strlen($stmts)).$stmts;
	}

	public function putGotoLabel(int $label) : string{
		return code::LGOTO.$this->getInt($label);
	}


	public function putLabel(int $label) : string{
		return code::LABEL.$this->getInt($label);
	}
}
