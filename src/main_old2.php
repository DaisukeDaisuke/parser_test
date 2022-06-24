<?php

namespace purser;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\AssignOp\BitwiseAnd as AssignBitwiseAnd;
use PhpParser\Node\Expr\AssignOp\BitwiseOr as AssignBitwiseOr;
use PhpParser\Node\Expr\AssignOp\BitwiseXor as AssignBitwiseXor;
use PhpParser\Node\Expr\AssignOp\Coalesce as AssignCoalesce;
use PhpParser\Node\Expr\AssignOp\Concat as AssignConcat;
use PhpParser\Node\Expr\AssignOp\Div as AssignDiv;
use PhpParser\Node\Expr\AssignOp\Minus as AssignMinus;
use PhpParser\Node\Expr\AssignOp\Mod as AssignMod;
use PhpParser\Node\Expr\AssignOp\Mul as AssignMul;
use PhpParser\Node\Expr\AssignOp\Plus as AssignPlus;
use PhpParser\Node\Expr\AssignOp\Pow as AssignPow;
use PhpParser\Node\Expr\AssignOp\ShiftLeft as AssignShiftLeft;
use PhpParser\Node\Expr\AssignOp\ShiftRight as AssignShiftRight;
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
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Expr\Cast\Bool_ as CastBool;
use PhpParser\Node\Expr\Cast\Double as CastDouble;
use PhpParser\Node\Expr\Cast\Int_ as CastInt;
use PhpParser\Node\Expr\Cast\Object_ as CastObject;
use PhpParser\Node\Expr\Cast\String_ as CastString;
use PhpParser\Node\Expr\Cast\Unset_ as CastUnset;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\ErrorSuppress;
use PhpParser\Node\Expr\Exit_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\PreDec;
use PhpParser\Node\Expr\PreInc;
use PhpParser\Node\Expr\Print_;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Continue_;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\HaltCompiler;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\While_;
use PhpParser\Node\VariadicPlaceholder;
use pocketmine\utils\Binary;

use PhpParser\Node\Expr\ArrayDimFetch;

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
	/** @var Logger $logger */
	public $logger;

	/** @var array<int, scopenode> $forscope id, scopenode */
	public $forscope = [];
	/** @var ?int $currentlyScope */
	public $currentlyScope = null;
	/** @var ?int $currentlyContinueScope */
	public $currentlyContinueScope = null;
	/** @var ?string $file */
	public $file = null;
	/** @var array<int, InfoArray> */
	public $array_inference = [];
	//config

	/**
	 * If "true" is specified, the comparison is performed using the spaceship operator instead of using the comparison operator below.
	 * >,>=,==,!=,<,<=
	 * reference
	 * https://wiki.php.net/rfc/combined-comparison-operator
	 * @var bool
	 */
	public $use_spaceship_operator = false;

	public const ADDRESS_SIZE = 2;
	public const PUT_STR_LEN = self::ADDRESS_SIZE + 1;

	public function __construct(bool $is_phpunit = false, ?string $display_program = "Main.php"){
		$this->block[$this->blockid] = new CodeBlock($this->blockid);
		$this->logger = new Logger($is_phpunit, $display_program);
		$this->file = $display_program;
	}

	/**
	 * @param Stmt $node
	 * @param bool $rootscope
	 * @return string
	 */
	public function execStmt(Stmt $node, bool $rootscope = false) : string{
		$return = "";
		switch(get_class($node)){
			case Echo_::class:
				if(!is_array($node->exprs)){
					return $this->execStmts($node->exprs).code::PRINT.$this->put_var($this->count++);
				}
				$result = "";
				foreach($node->exprs as $expr){
					if($expr instanceof Variable){
						$result .= code::PRINT.$this->execExpr($expr, $this->count);
						//$this->count++;//!!!!!!!!!
						continue;
					}
					if($expr instanceof Assign){//echo $i = 100;
						/** @var Variable $var */
						$var = $expr->var;
						$result .= $this->execExpr($expr).code::PRINT.$this->execExpr($var, $this->count);
						//$this->count++;
						continue;
					}
					$return1 = $this->execStmts([$expr], $targetid);//
					$result .= $return1.code::PRINT.$this->put_var($targetid ?? $this->count++);
				}
				$return .= $result;

				return $return;
			case If_::class://...?
				//ConstFetch
				//$return = "";
				$label = $this->label_count++;
				$expr = $this->execStmts([$node->cond]);
				$ifcount = $this->count++;
				$elseifs = null; // 00 = null
				$else = null;

				if(isset($node->elseifs[0])){
					//$return6 = "";
					foreach($node->elseifs as $elseif){
						$elseifs .= $this->execExpr($elseif->cond).$this->putjmpz($this->count++, $this->execStmts($elseif->stmts).$this->putGotoLabel($label));
					}

				}
				if(isset($node->else)){
					$else = $this->execStmt($node->else);
				}

				$stmts = $this->execStmts($node->stmts).$this->putGotoLabel($label);


				//var_dump(opcode_dumper::hexentities($expr.$this->putjmpz($ifcount, $stmts).$elseifs.$else));
				return $this->solveLabel($expr.$this->putjmpz($ifcount, $stmts).$elseifs.$else, $label);//.$this->putLabel($label); $else
			case Else_::class:
				return $this->execStmts($node->stmts);//JMPZ
			case Expression::class:
				return $this->execExpr($node->expr);
			case For_::class:
			case While_::class:
				$scope = $this->label_count++;
				$continueScope = $this->label_count++;

				$scopeNode = new scopenode($this->currentlyScope, $scope, scopenode::TYPE_FOR_WHILE);
				$continueScopeNode = new scopenode($this->currentlyContinueScope, $continueScope, scopenode::TYPE_FOR_WHILE);

				$this->forscope[$scope] = $scopeNode;
				$this->forscope[$continueScope] = $continueScopeNode;

				$this->currentlyScope = $scope;
				$this->currentlyContinueScope = $continueScope;
				$init = "";
				$loop = "";
				if($node instanceof For_){
					foreach($node->init as $value){
						$init .= $this->execExpr($value);
					}
				}

				if(!is_array($node->cond)){
					$condExpr = [$node->cond];
				}else{
					$condExpr = $node->cond;
				}
				$cond = "";
				foreach($condExpr as $item){
					$cond .= $this->execExpr($item);
				}
				$tmpcount = $this->count++;

				$stmts = $this->execStmts($node->stmts);
				if($continueScopeNode->isUsed()){
					//var_dump(opcode_dumper::hexentities($stmts));
					$stmts = $this->solveLabel($stmts, $continueScope);
				}
				if($node instanceof For_){
					foreach($node->loop as $value){
						$loop .= $this->execExpr($value);
					}
				}
				$output = $stmts.$loop;

				if($cond !== ""){//無限ループ
					$cond .= $this->putjmpz($tmpcount, "", $output, 7);
				}


				//$cond = $cond;

				$output = $cond.$output;
				//$output = $init.$this->putunjmp($output);
				//$output = $init.$cond.$this->putunjmp($output);
				$unjmp = $this->putunjmp($output);
				if($scopeNode->isUsed()){
					//$unjmp .= $this->putLabel($scope);
					//var_dump(opcode_dumper::hexentities($unjmp));
					$unjmp = $this->solveLabel($unjmp, $scope);
					//var_dump(opcode_dumper::hexentities($unjmp));
				}
				unset($this->forscope[$scope]);
				$this->currentlyScope = $scopeNode->getParent();
				$this->currentlyContinueScope = $continueScopeNode->getParent();

				return $init.$unjmp;

			case Break_::class;
				/** @var break_ $node */
				return $this->exec_Break_Continue($node->num, $this->currentlyScope, "break", $node->getAttribute("startLine"));
			case Continue_::class:
				/** @var Continue_ $node */
				return $this->exec_Break_Continue($node->num, $this->currentlyContinueScope, "continue", $node->getAttribute("startLine"));
			case Switch_::class:
				/** @var Switch_ $node */
				//var_dump($node);

				$hasjmpid = $this->count++;

				$scope = $this->label_count++;
				$continueScope = $this->label_count++;

				$scopeNode = new scopenode($this->currentlyScope, $scope, scopenode::TYPE_SWITCH);
				$continueScopeNode = new scopenode($this->currentlyContinueScope, $continueScope, scopenode::TYPE_SWITCH);

				$this->forscope[$scope] = $scopeNode;
				$this->forscope[$continueScope] = $continueScopeNode;

				$this->currentlyScope = $scope;
				$this->currentlyContinueScope = $continueScope;

				$switch_cond = $this->execStmts([$node->cond]);
				$count = $this->count++;

				$array = [];

				$hasdefault = false;

				foreach($node->cases as $case){
					$tmp = null;
					//$id = $this->count++;//4
					$recursion = false;
					if($case->cond === null){
						if($hasdefault === true){
							throw new phpFinalException("Switch statements may only contain one default clause", $case->getAttribute("startLine"), $this->file);
						}
						$default = $this->execStmts($case->stmts);
						$array[] = [null, null, $default, true];
						$hasdefault = true;
						continue;
					}
					$expr1 = $this->execExpr($case->cond, null, $tmp, $recursion);

					$exprid = -1;
					if($recursion){
						$exprid = $this->count++;//7
					}
					$outputid = $this->count++;

					$cond = code::EQUAL.$this->write_varId($outputid).$this->write_variableId($count);
					if($recursion){
						$cond = $expr1.$cond.$this->put_var($exprid);
					}else{
						$cond .= $expr1;
					}
					$stmts = $this->execStmts($case->stmts);
					//$tmpjmpz = $this->putjmpz($outputid,"",$stmts);
					$array[] = [$cond, $outputid, $stmts, false];//$cond.$tmpjmp
				}

				$result = "";

//				foreach($array as $key => $item){
//					$tmpjmp = $this->putjmp($array[$key+1][0] ?? "", true);
//					//var_dump([opcode_dumper::hexentities($item[0]), opcode_dumper::hexentities($item[1])]);
//					$result .= $item[0].$this->putjmpz($outputid,$item[2],null, strlen($tmpjmp)).$tmpjmp;//.code::NOP.code::NOP;
//					//$result .= $cond.$this->putjmpz($item[1], "", "", strlen($item[2]) + strlen($array[$key+1][0] ?? "")).$item[2].code::NOP.code::NOP;
//				}

				$defaultpos = null;

				$condfactory = [];

				$previous_stmts = "";
				$previous_cond = "";
				$previous_jmpz = "";
				$previous_default = false;
				$jmp_offset = 0;
				foreach(array_reverse($array) as $key => $item){
					if($item[3] === true||$item[1] === null){
						//$defaultpos = strlen($result);//end
						$tmp = $item[2].$this->write_var($hasjmpid, 0);
						$tmpjmp1 = $tmp.$this->putjmp($condfactory[$key - 1] ?? "", true);
						$defaultpos = strlen($result.$tmpjmp1);
						$tmpjmp = $this->putjmp($tmpjmp1);
						$result = $tmpjmp.$result;
						$previous_default = true;
						//$jmp_offset = strlen($tmpjmp);//4byte ジャンプ
						//$previous_cond .= $tmpjmp;

						//$previous_cond .= $tmpjmp;
						//start
						//var_dump($defaultpos);
						continue;
					}
					if($previous_default){
						$stmts = $item[2];
						$previous_default = false;
					}else{
						$stmts = $item[2].$this->putjmp($previous_cond, true, $jmp_offset);// 4byte
					}
					$condfactory[$key] = $previous_cond = $item[0].$this->putjmpz($item[1], "", $stmts);
					$result = $previous_cond.$stmts.$result;

					$jmp_offset = 0;
				}


				if($defaultpos !== null){
					$tmp = $this->putjmpz($hasjmpid, "", null, 7);
					$result .= $tmp;
					$result .= $this->putunjmp1(-$defaultpos - (strlen($tmp)));//$this->putjmpz($hasjmpid,"",null, -$defaultpos - (strlen($tmp)));  - (strlen($tmp))

//					$exec = substr_replace($result, "aaaaaaaa", -$defaultpos - (strlen($tmp)), 0);//debug
//
//					var_dump(-$defaultpos - (strlen($tmp)), opcode_dumper::hexentities($exec));
				}

				if($continueScopeNode->isUsed()){
					$result = $this->solveLabel($result, $continueScope);
				}

				if($scopeNode->isUsed()){
					$result = $this->solveLabel($result, $scope);
				}
				unset($this->forscope[$scope]);
				$this->currentlyScope = $scopeNode->getParent();
				$this->currentlyContinueScope = $continueScopeNode->getParent();

				//var_dump(opcode_dumper::hexentities($switch_cond.$result));

				return $switch_cond.$this->write_var($hasjmpid, 1).$result;
			/*case Case_::class:

				break;*/
		}
		return "";//code::nop
	}

	/**
	 * @param Expr $expr
	 * @param int|null $outputid
	 * @param int|null $targetid
	 * @param bool $recursion
	 * @param ?int $is_var
	 * @return string
	 *
	 * @throws \RuntimeException
	 */
	public function execExpr(Expr $expr, ?int $outputid = null, ?int &$targetid = null, bool &$recursion = false, ?int &$is_var = null) : string{//array...?
		switch(true){
			case $expr instanceof BinaryOp:
				$recursion = true;
				return $this->execBinaryOp($expr);
			case $expr instanceof ConstFetch:
				$recursion = true;
				$value = strtoupper($expr->name->parts[0]);
				//$return = $this->put_Scalar();
				if($value === "FALSE"){
					return $this->write_var($outputid ?? $this->count, false);
				}

				if($value === "TRUE"){
					return $this->write_var($outputid ?? $this->count, true);
				}
				if($value === "NULL"){
					return $this->write_var($outputid ?? $this->count, null);
				}

				return $expr->name->parts[0];//read const id(global...?)
			case $expr instanceof Scalar:
				return $this->execScalar($expr);
			case $expr instanceof Variable:
				//$recursion = true;//!!!!!!!!!
				//$is_var = true;

				//Added outputid from April 18, 2022
				$var = $this->exec_variable($expr, $outputid ?? $this->count, false, $oldid,false, $name);
				if($oldid === null){
					//$recursion = false;
					$this->logger->warning('Undefined variable $'.$name.', Incompatibility warning: Assign null to $'.$name.'.', $expr->getAttribute("startLine"));
					return $this->write_var(($outputid ?? $this->count++), null);
				}
				return $var;
			case $expr instanceof PreInc://++$i;
				$recursion = true;//!!!!!!!!!
				//$is_var = true;
				//$var = $expr->var;
				/** @var Variable $var */
				$var = $expr->var;
				$oldid = null;
				$name = null;
				$var = $this->exec_variable($var, $this->count, false, $oldid, true, $name);

				$undefined = "";
				if($oldid === null){
					//$undefined = $this->write_var($this->count, 0);
					$this->logger->warning('Undefined variable $'.$name, $expr->getAttribute("startLine"));
					//$this->count++;
					//$recursion = true;
					$recursion = false;
					return $this->write_var($this->count++, 1);//isset $a ?? 1
				}
				//$targetid = $oldid;// ?? $this->count;
				$copy = code::WRITEV.$this->write_varId($this->count).code::VALUE.$var;
				return $undefined.code::ADD.$var.code::READV.$var.code::INT.$this->putRawInt(1).$copy;
			case $expr instanceof PreDec://--$i;
				$recursion = true;//!!!!!!!!!
				//$is_var = true;
				/** @var Variable $var */
				$var = $expr->var;
				$oldid = null;
				$name = null;
				$var = $this->exec_variable($var, $this->count, false, $oldid, true, $name);

				$undefined = "";
				if($oldid === null){
					//$undefined = $this->write_var($this->count, 0);
					$this->logger->warning('Undefined variable $'.$name, $expr->getAttribute("startLine"));
					//$this->count++;
					//$recursion = true;
					$recursion = false;
					return $this->write_var($this->count++, null);//isset $a ?? 1
				}

				//$targetid = $oldid;// ?? $this->count;
				$copy = code::WRITEV.$this->write_varId($this->count).code::VALUE.$var;
				return $undefined.code::MINUS.$var.code::READV.$var.code::INT.$this->putRawInt(1).$copy;
			case $expr instanceof PostInc://$i++;
				$recursion = true;//!!!!!!!!!
				/** @var Variable $var */
				$var = $expr->var;
				$oldid = null;
				$name = null;
				$var = $this->exec_variable($var, $this->count, false, $oldid, true, $name);

				$copy = "";
				$undefined = "";
				if($oldid === null){
					$undefined = $this->write_var($this->count, null);
					$this->logger->warning('Undefined variable $'.$name, $expr->getAttribute("startLine"));
					$copy = code::WRITEV.$this->write_varId($this->count + 1).$this->write_variableId($this->count);
					$this->count++;
				}else{
					$copy .= code::WRITEV.$this->write_varId($this->count).$this->write_variableId($oldid);
				}
				return $undefined.$copy.code::ADD.$var.code::READV.$var.code::INT.$this->putRawInt(1);
			case $expr instanceof PostDec://$i--;
				$recursion = true;//!!!!!!!!!
				/** @var Variable $var */
				$var = $expr->var;
				$oldid = null;
				$name = null;
				$var = $this->exec_variable($var, $this->count, false, $oldid, true, $name);

				$undefined = "";
				$copy = "";
				if($oldid === null){
					$undefined = $this->write_var($this->count, null);
					$this->logger->warning('Undefined variable $'.$name, $expr->getAttribute("startLine"));
					$copy = code::WRITEV.$this->write_varId($this->count + 1).$this->write_variableId($this->count);
					$this->count++;
				}else{
					$copy = code::WRITEV.$this->write_varId($this->count).$this->write_variableId($oldid);
				}

				return $undefined.$copy.code::MINUS.$var.code::READV.$var.code::INT.$this->putRawInt(1);
			case $expr instanceof Assign:
				//$id = $this->execExpr($expr->var);
				/** @var Variable|ArrayDimFetch $value */
				$value = $expr->var;
				//$baseid = $this->count++;
				$oldid1 = $this->count;
				//$baseid = $this->count;

				$baseid = $this->count;
				//$this->count++;
                if($value instanceof ArrayDimFetch){
					$pre_expr = "";
					/** @var Variable $var */
                    $var = $value->var;
                    $dim = $value->dim;
					if($dim === null){
						//$test[] = ""
						//throw new \RuntimeException("\$value->dim === null");

					}
                    $id1 = $this->exec_variable($var, $baseid, false, $is_var, true);
					if($is_var === null){
						$pre_expr = code::ARRAY_CONSTRUCT.$id1;
					}
					$result = code::ARRAY_SET.$id1;
					$recursion = false;
					$tmp = $this->execExpr($dim, $outputid, $targetid, $recursion);
					if($recursion){
						$id2 = $this->count++;
						$pre_expr .= $tmp;
						$result .= $this->put_var($targetid ?? $id2);
					}else{
						$result .= $tmp;
					}

					$recursion = false;
					$tmp = $this->execExpr($expr->expr, null, $targetid, $recursion);
					if($recursion){
						$id3 = $this->count++;
						$pre_expr .= $tmp;
						$result .= $this->put_var($targetid ?? $id3);
					}else{
						$result .= $tmp;
					}
                    return $pre_expr.$result;
                }
				$id1 = $this->exec_variable($value, $baseid, false, $is_var);

				$recursion1 = false;
				if($expr->expr instanceof BinaryOp){
					$recursion1 = true;
					$content = $this->execBinaryOp($expr->expr, $is_var ?? $baseid);
				}else{
					$this->count++;
					$content = $this->execExpr($expr->expr, $is_var ?? $baseid, $targetid, $recursion);
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
					return $this->execExpr($expr->expr).code::PRINT.$this->exec_variable($var, $this->count).$this->write_var($outputid ?? $this->count, 1);
				}
				return $this->execStmts([$expr->expr], $targetid).code::PRINT.$this->put_var($targetid ?? $this->count++).$this->write_var($outputid ?? $this->count, 1);
			case $expr instanceof AssignOp:
				$recursion = true;
				return $this->execAssignOp($expr);
			case $expr instanceof ErrorSuppress:
				$this->getLogger()->setErrorSuppress(true);
				$result2 = $this->execExpr($expr->expr);
				$this->getLogger()->setErrorSuppress(false);
				return $result2;
			case $expr instanceof FuncCall:
				$recursion = true;//
				$name = $expr->name;
				if(!$name instanceof Name){
					return "";
				}
				$result1 = code::FUN_INIT.$this->put_Scalar($name->parts[0]);
				$result2 = "";
				foreach($expr->args as $arg){
					if(!$arg instanceof VariadicPlaceholder){
						$targetid = null;
						$tmprecursion = false;
						$tmp = $this->execExpr($arg->value, null, $targetid, $tmprecursion);
						if($tmprecursion){
							$result2 .= $tmp;
							$result1 .= code::FUN_SEND_ARGS.$this->put_var($this->count++);
						}else{
							$result1 .= code::FUN_SEND_ARGS.$tmp;
						}
					}else{
						throw new \RuntimeException("Variadic arguments are not currently supported.");
					}
				}

				if($outputid === null){
					$result1 .= code::FUN_SUBMIT.$this->write_varId($this->count);
				}else{
					$result1 .= code::FUN_SUBMIT.$this->write_varId($outputid);
				}
				return $result2.$result1;
			case $expr instanceof Isset_:
				$recursion = true;

				return "";
			case $expr instanceof Exit_:
				$recursion = true;//
				if($expr->expr === null){
					return code::EXIT.$this->getInt(0);
				}
				if($expr->expr instanceof LNumber){
					return code::EXIT.$this->getInt($expr->expr->value);
				}
				if($expr->expr instanceof Variable){
					return code::EXIT.$this->exec_variable($expr->expr, $this->count++);
				}
				if($expr->expr instanceof Assign){//print $i = 100;
					/** @var Variable $var */
					$var = $expr->expr->var;
					return $this->execExpr($expr->expr).code::EXIT.$this->exec_variable($var, $this->count);
				}
				return $this->execStmts([$expr->expr], $targetid).code::EXIT.$this->put_var($targetid ?? $this->count++);
			case $expr instanceof Cast:
				$recursion = true;
				return $this->putCast($expr, $outputid);
			case $expr instanceof UnaryMinus:
				$var = $expr->expr;
				if($var instanceof Scalar){
					return $this->execMinusScalar($var);
				}elseif($var instanceof Expr){
					$recursion = true;
					$oldid = null;
					$recursion1 = false;
					$str = $this->execExpr($var, null, $oldid, $recursion1);
					$output_ = $this->count;
					$result = code::MINUS.$this->write_varId(++$this->count).$this->getInt(0);
					if($recursion1){
						return $str.$result.$this->put_var($output_);
					}
					return $result.$str;
				}
				var_dump($var);
				throw new \LogicException("UnaryMinus: \$var is not expected type.");
            case $expr instanceof Array_:
				//Assign
                //$array = new type_array($outputid ?? $this->count++);
				$pre_expr = "";
                $recursion = true;
                $array_id = $outputid ?? $this->count++;

				$this->array_inference[$array_id] = new InfoArray($array_id);

                $result =  code::ARRAY_CONSTRUCT.$this->write_varId($array_id);
                $count = 0;//TODO: object
                foreach ($expr->items as $item) {
                    if(!$item instanceof ArrayItem){
                        $this->logger->warning("internal error: \$item is not ArrayItem object.");
                        continue;
                    }

                   //$array->setKey($item->key, $item->value);
                    //code id key scalar
                    $tmp_result = code::ARRAY_SET.$this->write_varId($array_id);
                    $key = $item->key;
                    $value = $item->value;
                    $byRef = $item->byRef;
                    $unpack = $item->unpack;

                   // $pre_expr = "";

                    //my note: "test" = 0x1. "test1" = 0x2
                    //my note: array_search = ["test1" => 1, "test2" => 2]

                    $key_recursion = false;
                    if($key !== null){
                        $expr1 = $this->execExpr($key, null, $targetid,$key_recursion);
                        $basecount1 = $this->count++;
                        if($key_recursion){
                            $tmp_result .= $tmp_result.$this->put_var($targetid ?? $basecount1);
                            $pre_expr .= $expr1;
						}else{
                            $tmp_result .= $expr1;
                        }
                    }else{
                        $tmp_result .= $this->getInt($count++);//todo: count
                    }

                    $value_recursion = false;
                    $expr2 = $this->execExpr($value, null, $targetid,$value_recursion);
                    $basecount2 = $this->count++;
                    if($value_recursion){
                        $tmp_result .= $this->put_var($targetid ?? $basecount2);
                        $pre_expr .= $expr2;
                    }else{
                        $tmp_result .= $expr2;
                    }


                    //var_dump(opcode_dumper::hexentities1($tmp_result));
                    $result .= $tmp_result;

//

//                    $expr1 = "";
//                    if($key !== null){
//                        $expr1 = $this->execExpr($key, null, $targetid,$key_recursion);
//                        $basecount1 = $this->count++;
//                    }
//                    $expr = $this->execStmts($value);
//                    $basecount2 = $this->count++;
                    ///my note: B3 Id key type var
                    //my none: rand
//                    if($key_recursion){
//
//                    }
//
//                    $result = $expr1.$expr.code::ARRAY_SET.$this->put_var($basecount1).$this->put_var($basecount2);
//                    var_dump(opcode_dumper::hexentities($result));




                }
				//var_dump(opcode_dumper::hexentities1($pre_expr.$result));
                return $pre_expr.$result;
                case $expr instanceof ArrayDimFetch:

					/** @var Variable $var */
					$var = $expr->var;
					$dim = $expr->dim;
					if($dim === null){
						throw new \RuntimeException("\$dim is null.");
					}
					$recursion = true;
					$oldid = null;
					$recursion1 = false;
					$output_ = $this->count;
					$oldid = false;
					$pre_expr = "";

					$result1 = "";

					$pxpr1 = $this->execExpr($dim, null, $targetid, $recursion1);
					if($recursion1){
						$result1 .= $this->put_var($targetid ?? $this->count++);
						$pre_expr .= $pxpr1;
					}else{
						$result1 .= $pxpr1;
					}

					$array_id = $outputid ?? $this->count;//tmp id

					$id = $this->exec_variable($var, $array_id,false, $oldid, true,$name);
					if($oldid === null){
						//変数未定義
						$pre_expr = $this->write_var($array_id, 0);
						$this->logger->warning('Undefined variable $'.$name.' (ArrayDimFetch)', $expr->getAttribute("startLine"));
						$this->count++;
					}
					$result = code::WRITEV.$this->write_varId($array_id).code::ARRAY_GET.$id;



                    //$test["key"];
//                    $var = $expr->var;
//                    $dim = $expr->dim;
//                    $baseid = $this->count;
//                    $var = $this->exec_variable($expr, $outputid ?? $this->count, false, $oldid,false, $name);
//                    $key_expr = $this->execExpr($dim, null);
//                    $recursion1 = false;
//                    $this->count++;
//                    $content = code::ARRAY_SET.$var;

                    //return $content;
                    //break;
					return $pre_expr.$result.$result1;
			case $expr instanceof Expr:
				//var_dump(get_class($expr));
				$recursion = true;
				//var_dump(get_class($expr));

				//return $this->execExpr($expr);//再帰...?
				//break;
				throw new \RuntimeException("expr ".get_class($expr)." is not supported.");
		}
		throw new \RuntimeException('execExpr "'.get_class($expr).'" not found');
	}

	public function putCast(Cast $expr_node, ?int $outputid = null) : string{
		switch(true){
			case $expr_node instanceof CastBool:
				$id = code::TYPE_BOOL;
				break;
			case $expr_node instanceof CastInt:
				$id = code::TYPE_INT;
				break;
			case $expr_node instanceof CastDouble:
				$id = code::TYPE_DOUBLE;
				break;
			case $expr_node instanceof CastObject:
				$id = code::TYPE_OBJECT;
				break;
			case $expr_node instanceof CastString:
				$id = code::TYPE_STRING;
				break;
			case $expr_node instanceof CastUnset:
				$id = code::TYPE_UNSET;
				break;
			default:
				throw new \RuntimeException("Cast ".get_class($expr_node)." not found.");
		}


		if($expr_node->expr instanceof Scalar||$expr_node->expr instanceof Variable){
			$basecount = $outputid ?? ++$this->count;
			return code::CAST.$this->write_varId($basecount).Binary::writeByte($id).$this->execExpr($expr_node->expr);
		}

		$expr = $this->execStmts([$expr_node->expr]);
		$test = $this->count++;
		$basecount = $outputid ?? ++$this->count;
		return $expr.code::CAST.$this->write_varId($basecount).Binary::writeByte($id).$this->put_var($test);

	}

	public function execAssignOp(AssignOp $node) : string{
		switch(get_class($node)){
			case AssignBitwiseAnd::class:
				return $this->writeAssignOp($node, code::B_AND);
			case AssignBitwiseOr::class:
				return $this->writeAssignOp($node, code::B_OR);
			case AssignBitwiseXor::class:
				return $this->writeAssignOp($node, code::B_XOR);
			case AssignCoalesce::class:
				return $this->writeAssignOp($node, code::COALESCE);
			case AssignConcat::class:
				return $this->writeAssignOp($node, code::CONCAT);
			case AssignDiv::class:
				return $this->writeAssignOp($node, code::DIV);
			case AssignMinus::class:
				return $this->writeAssignOp($node, code::MINUS);
			case AssignMod::class:
				return $this->writeAssignOp($node, code::MOD);
			case AssignMul::class:
				return $this->writeAssignOp($node, code::MUL);
			case AssignPlus::class:
				return $this->writeAssignOp($node, code::ADD);
			case AssignPow::class:
				return $this->writeAssignOp($node, code::POW);
			case AssignShiftLeft::class:
				return $this->writeAssignOp($node, code::SHIFTLEFT);
			case AssignShiftRight::class:
				return $this->writeAssignOp($node, code::SHIFTRIGHT);
		}
		return "";
	}

	public function writeAssignOp(AssignOp $node, string $opcode) : string{
		$recursion = false;
		$tmp = null;
		$result = $this->execExpr($node->expr, null, $tmp, $recursion);
		$basecount = $this->count++;

		/** @var Variable $varnode */
		$varnode = $node->var;
		$tmp = null;
		$solvedName = null;
		$var1 = $this->exec_variable($varnode, $this->count, false, $tmp, true, $solvedName);
		$result1 = "";
		if($tmp === null){
			$result1 = $this->write_var($this->count, 0).$result;
			$this->logger->warning('Undefined variable $'.$solvedName.'. opcode(+= etc): '.bin2hex($opcode).'. (writeAssignOp)');// in ?????? on line ?
			$this->count++;
		}
		if($recursion){
			return $result1.$result.$opcode.$var1.code::VALUE.$var1.$this->put_var($basecount);
		}
		return $result1.$opcode.$var1.code::VALUE.$var1.$result;
	}

	public function exec_variable(Variable $node, int $id, bool $force = false, ?int &$oldid = null, bool $raw = false, ?string &$solvedName = null) : string{//変数処理...
		if($node->name instanceof Expr){
			//var_dump("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
			if($node->name instanceof Variable){
				return "";//$$b
			}else{//binaryop...? //$i+100...?

			}
			return "";//!!
		}
		//return $this->write_variableId($this->count);
		$solvedName = $node->name;
		if($raw === true){
			return $this->write_varId($this->getValualueId($solvedName, $force, $id, $oldid));
		}
		return $this->write_variableId($this->getValualueId($solvedName, $force, $id, $oldid));//code::VALUE
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
				case code::FUN_SUBMIT:
				case code::VALUE:
					$i += 3;
					break;
				case code::BOOL:
					$i += 2;
					break;
				case code::PRINT:
				case code::JMP:
				case code::JMPZ:
				case code::SJMP://
				case code::LABEL:
				case code::JMPA:
				case code::FUN_INIT:
				case code::FUN_SEND_ARGS:
					$i++;
					break;
				case code::LGOTO://LGOTO INT SIZE 1
					$start = $i;
					//var_dump("!!",ord($exec[$i+2]),ord($exec[$i+3]),ord($exec[$i+4]));

					$tmpid = Binary::readShort($exec[$i + 3].$exec[$i + 4]);
					//var_dump($tmpsize);
					$i += 5;
					if($tmpid !== $label){
						break;
					}
					$array[] = [$start, 5, $i++];
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
			$new = code::JMP.code::INT.chr(code::TYPE_SHORT).Binary::writeShort($len - ($end + 0));//$this->getInt($len - ($end + 0));

			//var_dump(opcode_dumper::hexentities($exec));

			/** @var string $exec */
			$exec = substr_replace($exec, '', $start, $len1);
			$exec = substr_replace($exec, $new, $start, 0);
			//var_dump(opcode_dumper::hexentities($exec));
			$len = strlen($exec);
		}
		return $exec;
	}

	/**
	 * @param Stmt[]|Expr[] $nodes
	 * @return string
	 */
	public function onexec(array $nodes) : string{
		return $this->execStmts($nodes, $tmp, true);
	}

	/**
	 * @param Stmt[]|Expr[] $nodes
	 * @param int|null $targetid
	 * @param bool $rootscope
	 * @return string
	 */
	public function execStmts(array $nodes, ?int &$targetid = null, bool $rootscope = false) : string{//,bool $array = false
		$return = "";
		/*if($array === true){
			$return = [];
		}*/

		foreach($nodes as $node){
			if($node instanceof Expr){
				$root = false;
				$targetid = null;
				$return1 = $this->execExpr($node, null, $targetid, $root);// ?? "";
				if($root === false){
					$return1 = code::WRITEV.$this->write_varId($targetid ?? $this->count).$return1;
				}
				$return .= $return1;
			}
			if($node instanceof Stmt){
				if($node instanceof Nop){
					continue;
				}
				if($node instanceof HaltCompiler){
					/*if($rootscope === false){
						throw new phpFinalException("__HALT_COMPILER() can only be used from the outermost scope");
					}*/
					return $return.code::EXIT.$this->getInt(0).$node->remaining;//__COMPILER_HALT_OFFSET__
				}
				//if($array === true){
				//$return[] = $this->execStmt($node);
				//}else{

				$return .= ($this->execStmt($node, $rootscope));// ?? ""

//.$return;
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
	 * @param int|null $outputid
	 * @return string
	 */
	public function execBinaryOp(BinaryOp $node, ?int $outputid = null) : string{//output //add 10 10 1 $count = 0
		switch(get_class($node)){
			case Plus::class:
				return $this->execbinaryplus($node, code::ADD, $outputid);
			case Mul::class:
				return $this->execbinaryplus($node, code::MUL, $outputid);
			case Div::class:
				return $this->execbinaryplus($node, code::DIV, $outputid);
			case Minus::class:
				return $this->execbinaryplus($node, code::MINUS, $outputid);
			case BitwiseAnd::class:
				return $this->execbinaryplus($node, code::B_AND, $outputid);
			case BitwiseOr::class:
				return $this->execbinaryplus($node, code::B_OR, $outputid);
			case BooleanAnd::class:
				return $this->execbinaryLogicAnd($node, code::BOOL_AND, $outputid);
			case BooleanOr::class:
				return $this->execbinaryLogicOr($node, code::BOOL_OR, $outputid);
			case Coalesce::class:
				$this->execCoalesce($node, $outputid);
			//return $this->execbinaryplus($node, code::COALESCE, $outputid);
			case Concat::class:
				return $this->execbinaryplus($node, code::CONCAT, $outputid);
			case Equal::class:
				if($this->use_spaceship_operator){
					return $this->execExpr(new Identical(new Spaceship($node->left, $node->right), new LNumber(0)));
				}
				return $this->execbinaryplus($node, code::EQUAL, $outputid);
			case Greater::class:
				if($this->use_spaceship_operator){
					return $this->execExpr(new Identical(new Spaceship($node->left, $node->right), new LNumber(1)));
				}
				return $this->execbinaryplus($node, code::GREATER, $outputid);
			case GreaterOrEqual::class:
				if($this->use_spaceship_operator){
					$tmp = null;
					$tmp_expr = $this->execStmts([new Spaceship($node->left, $node->right)]);
					$spaceship_operator_tmp_Id = $this->count;//unset
					$this->getValualueId("#!s_tmp", true, $spaceship_operator_tmp_Id, $tmp);
					$left = new Identical(new Variable("#!s_tmp"), new LNumber(1));
					$right = new Identical(new Variable("#!s_tmp"), new LNumber(0));
					return $tmp_expr.$this->execExpr((new BooleanOr($left, $right)));
				}
				return $this->execbinaryplus($node, code::GREATEROREQUAL, $outputid);
			case Identical::class:
				return $this->execbinaryplus($node, code::IDENTICAL, $outputid);
			case LogicalAnd::class:
				return $this->execbinaryLogicAnd($node, code::L_AND, $outputid);
			case LogicalOr::class:
				return $this->execbinaryLogicOr($node, code::L_OR, $outputid);
			case LogicalXor::class:
				return $this->execbinaryplus($node, code::L_XOR, $outputid);
			case Mod::class:
				return $this->execbinaryplus($node, code::MOD, $outputid);
			case NotEqual::class:
				if($this->use_spaceship_operator){
					return $this->execExpr(new NotIdentical(new Spaceship($node->left, $node->right), new LNumber(0)));
				}
				return $this->execbinaryplus($node, code::NOTEQUAL, $outputid);
			case NotIdentical::class:
				return $this->execbinaryplus($node, code::NOTIDENTICAL, $outputid);
			case Pow::class:
				return $this->execbinaryplus($node, code::POW, $outputid);
			case ShiftLeft::class:
				return $this->execbinaryplus($node, code::SHIFTLEFT, $outputid);
			case ShiftRight::class:
				return $this->execbinaryplus($node, code::SHIFTRIGHT, $outputid);
			case Smaller::class:
				if($this->use_spaceship_operator){
					return $this->execExpr(new Identical(new Spaceship($node->left, $node->right), new UnaryMinus(new LNumber(1))));
				}
				return $this->execbinaryplus($node, code::SMALLER, $outputid);
			case SmallerOrEqual::class:
				if($this->use_spaceship_operator){
					$tmp = null;
					$tmp_expr = $this->execStmts([new Spaceship($node->left, $node->right)]);
					$spaceship_operator_tmp_Id = $this->count;//unset
					$this->getValualueId("#!s_tmp", true, $spaceship_operator_tmp_Id, $tmp);
					$left = new Identical(new Variable("#!s_tmp"), new UnaryMinus(new LNumber(1)));
					$right = new Identical(new Variable("#!s_tmp"), new LNumber(0));
					return $tmp_expr.$this->execExpr((new BooleanOr($left, $right)));
				}
				return $this->execbinaryplus($node, code::SMALLEROREQUAL, $outputid);
			case Spaceship::class:
				return $this->execbinaryplus($node, code::SPACESHIP, $outputid);
			case BitwiseXor::class:
				return $this->execbinaryplus($node, code::B_XOR, $outputid);
		}
		throw new \RuntimeException('BinaryOp "'.get_class($node).'" is unprocessed.');
	}

	/**
	 * @param BinaryOp $node
	 * @param string $opcode binaryid
	 * @param int|null $outputid
	 * @return string
	 */
	public function execbinaryplus(BinaryOp $node, string $opcode, ?int $outputid = null) : string{
		$recursionLeft = false;
		$recursionRight = false;

		$tmp = null;
		$left = $this->execExpr($node->left, null, $tmp, $recursionLeft, $is_varleft);
		$basecount1 = $this->count++;
		$tmp = null;
		$right = $this->execExpr($node->right, null, $tmp, $recursionRight, $is_varright);
		$basecount2 = $this->count++;

		$count1 = $outputid ?? $this->count;

		$return = "";
		if($recursionLeft&&$recursionRight){
			$return = $left.$right;
			$return .= $opcode.$this->write_varId($count1).$this->put_var($basecount1).$this->put_var($basecount2);//$left,$right
		}elseif($recursionLeft){
			$return .= $left.$opcode.$this->write_varId($count1).$this->put_var($basecount1).$right;
		}elseif($recursionRight){
			$return .= $right.$opcode.$this->write_varId($count1).$left.$this->put_var($basecount2);
		}else{
			$return .= $opcode.$this->write_varId($count1).$left.$right;
		}
		return $return;
	}

	/**
	 * @param BinaryOp $node
	 * @param string $opcode binaryid
	 * @param int|null $outputid
	 * @return string
	 */
	public function execbinaryLogicAnd(BinaryOp $node, string $opcode, ?int $outputid = null) : string{
		$recursionLeft = false;
		$recursionRight = false;

		$targetLeft = null;
		$left = $this->execExpr($node->left, null, $targetLeft, $recursionLeft, $is_varleft);
		$basecount1 = $this->count++;
		$targetRight = null;
		$right = $this->execExpr($node->right, null, $targetRight, $recursionRight, $is_varright);
		$basecount2 = $this->count++;

		$count1 = $outputid ?? $this->count;

		$after = "";
		if($recursionRight){
			if(!$recursionLeft){
				$left = code::WRITEV.$this->write_varId($basecount1).$left;
				$recursionLeft = true;
			}
			//9 = Hard coating!!!!!!!!!!
			//value jmpz
			$tmp1 = $this->write_var($count1, false);
			$jmp = $this->putjmp($tmp1, true);
			$after .= $jmp.$tmp1;//false
			//var_dump(opcode_dumper::hexentities($right));
			$left .= code::JMPZ.$this->write_variableId($basecount1).$this->getInt(1 + self::ADDRESS_SIZE + (self::PUT_STR_LEN * 2) + strlen($right) + strlen($jmp));//$this->putjmpz($basecount1, "", $right, 12);
		}

		$return = "";
		$binaryop = "";
		if($recursionLeft&&$recursionRight){
			$return = $left.$right;
			$return .= $opcode.$this->write_varId($count1).$this->put_var($basecount1).$this->put_var($basecount2);//$left,$right
		}elseif($recursionLeft){
			$return .= $left.$opcode.$this->write_varId($count1).$this->put_var($basecount1).$right;
		}elseif($recursionRight){
			$return .= $right.$opcode.$this->write_varId($count1).$left.$this->put_var($basecount2);
		}else{
			$return .= $opcode.$this->write_varId($count1).$left.$right;
		}

		return $return.$after;
	}

	/**
	 * @param BinaryOp $node
	 * @param string $opcode binaryid
	 * @param int|null $outputid
	 * @return string
	 */
	public function execbinaryLogicOr(BinaryOp $node, string $opcode, ?int $outputid = null) : string{
		$recursionLeft = false;
		$recursionRight = false;

		$tmp = null;
		$left = $this->execExpr($node->left, null, $tmp, $recursionLeft, $is_varleft);
		$basecount1 = $this->count++;
		$tmp = null;
		$right = $this->execExpr($node->right, null, $tmp, $recursionRight, $is_varright);
		$basecount2 = $this->count++;

		$count1 = $outputid ?? $this->count;

		$after = "";
		if($recursionRight){
			if(!$recursionLeft){
				$left = code::WRITEV.$this->write_varId($basecount1).$left;
				$recursionLeft = true;
			}
			//12 = Hard coating!!!!!!!!!!
			$tmp1 = $this->write_var($count1, true);
			$after = $this->putjmp($tmp1, true);
			$jmp2 = $this->putjmp($right, true, 1 + self::ADDRESS_SIZE + (self::PUT_STR_LEN * 2) + strlen($after));
			$jmp = code::JMPZ.$this->write_variableId($basecount1).$this->getInt(strlen($jmp2)).$jmp2;
			$left .= $jmp;// === 0, 1 = jmp
			$after .= $tmp1;
		}

		$return = "";
		$binaryop = "";
		if($recursionLeft&&$recursionRight){
			$return = $left.$right;
			$return .= $opcode.$this->write_varId($count1).$this->put_var($basecount1).$this->put_var($basecount2);//$left,$right
		}elseif($recursionLeft){
			$return .= $left.$opcode.$this->write_varId($count1).$this->put_var($basecount1).$right;
		}elseif($recursionRight){
			$return .= $right.$opcode.$this->write_varId($count1).$left.$this->put_var($basecount2);
		}else{
			$return .= $opcode.$this->write_varId($count1).$left.$right;
		}

		return $return.$after;
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
	 * @see exec_var
	 * @param bool $force
	 * @param int $id
	 * @param int|null $oldid
	 * @param string $value
	 * @return int
	 */
	public function getValualueId(string $value, bool $force, int $id, ?int &$oldid) : int{
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

	public function execMinusScalar(Scalar $node) : string{
		switch(true){
			case $node instanceof LNumber:
			case $node instanceof DNumber:
				/** @var int|float $val */
				$val = $node->value;
				return $this->put_Scalar(-$val);
			case $node instanceof String_:
				throw new phpFinalException("Uncaught TypeError: Unsupported operand types: string * int", $node->getStartLine(), $this->file);
			default:
				throw new \RuntimeException('MinusScalar "'.get_class($node).'" is unprocessed.');
		}
	}

	/**
	 * @see execScalar
	 *
	 * @param null|bool|float|int|string $value
	 * @return string
	 */
	public function put_Scalar($value) : string{
		/*if(is_object($value)){
			throw new \RuntimeException('The function "put_Scalar" cannot accept the object "'.get_class($value).'".');
		}*/
		switch(true){
			case is_null($value):
			case is_bool($value):
				return $this->getBool($value);
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

	public function getBool(?bool $value) : string{
		if($value === null){
			return code::BOOL.chr(code::TYPE_NULL);
		}
		if($value === true){
			return code::BOOL.chr(code::TYPE_TRUE);
		}
		return code::BOOL.chr(code::TYPE_FALSE);
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
				$tmp = Binary::writeByte($value);
				if(strlen($tmp) !== 1){
					throw new \RuntimeException(opcode_dumper::hexentities($tmp)." non 1 byte TYPE_BYTE");
				}
				$return .= $tmp;//Binary::readSignedByte($value);
				break;
			case code::TYPE_SHORT:// 2-byte
				/** @var int $value */
				$tmp = Binary::writeShort($value);
				if(strlen($tmp) !== 2){
					throw new \RuntimeException(opcode_dumper::hexentities($tmp)." non 1 byte TYPE_BYTE");
				}
				$return .= $tmp;
				break;
			case code::TYPE_INT://int 4-byte
				/** @var int $value */
				$tmp = Binary::writeInt($value);
				if(strlen($tmp) !== 4){
					throw new \RuntimeException(opcode_dumper::hexentities($tmp)." non 1 byte TYPE_BYTE");
				}
				$return .= $tmp;
				break;
			case code::TYPE_LONG://long 8-byte
				/** @var int $value */
				$tmp = Binary::writeLong($value);
				if(strlen($tmp) !== 8){
					throw new \RuntimeException(opcode_dumper::hexentities($tmp)." non 1 byte TYPE_BYTE");
				}
				$return .= $tmp;
				break;
			case code::TYPE_DOUBLE://Double 8-byte
				/** @var float $value */
				$tmp = Binary::writeLDouble($value);
				if(strlen($tmp) !== 8){
					throw new \RuntimeException(opcode_dumper::hexentities($tmp)." non 1 byte TYPE_BYTE");
				}
				$return .= $tmp;
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
	public function checkIntSize($value) : int{
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

	public function putjmpz(int $var, string $stmts, ?string $target = null, int $offset = 0) : string{//0 => jmp
		if($target !== null){
			return code::JMPZ.$this->put_var($var).$this->getInt(strlen($target) + $offset).$stmts;//
		}
		return code::JMPZ.$this->put_var($var).$this->getInt(strlen($stmts) + $offset + 0).$stmts;
	}

	public function putjmp(string $stmts, bool $skip = false, int $offset = 0) : string{
		if($skip === true){
			return code::JMP.$this->getInt(strlen($stmts) + $offset);
		}
		return code::JMP.$this->getInt(strlen($stmts) + $offset).$stmts;
	}

	public function putunjmp(string $stmts) : string{
		$tmp = -strlen($stmts.code::JMP.code::INT.Binary::writeByte(4).Binary::writeInt(-strlen($stmts) - 1000));
		return $stmts.code::JMP.code::INT.Binary::writeByte(4).Binary::writeInt($tmp);//8
	}

	public function putunjmp1(int $offset) : string{
		return code::JMP.code::INT.Binary::writeByte(4).Binary::writeInt($offset - 7);
	}

	public function putGotoLabel(int $label) : string{
		//var_dump(strlen(code::LGOTO.code::INT.chr(code::TYPE_SHORT).Binary::writeShort($label)));
		return code::LGOTO.code::INT.chr(code::TYPE_SHORT).Binary::writeShort($label);
	}

	/**
	 * Break_|Continue_
	 *
	 * @param Expr|int|null $num
	 * @param int|null $currentlyScope
	 * @param string $name
	 * @param int $line
	 * @return string
	 */
	public function exec_Break_Continue($num, ?int $currentlyScope, string $name, int $line) : string{
		if($currentlyScope === null){
			//throw new \RuntimeException("Internal error: Unexpected break. (execStmt)");
			throw new phpFinalException("'".$name."' not in the 'loop' or 'switch' context", $line, $this->file);
		}
		$scopeNode = $this->forscope[$currentlyScope] ?? null;
		if($scopeNode === null){
			throw new \RuntimeException("Internal error: scope ".$this->currentlyScope." not found. (execStmt)");
		}
		if($num instanceof LNumber){
			$num = $num->value;
		}elseif($num instanceof Scalar){//$num instanceof Variable||$num instanceof ConstFetch||$num instanceof FuncCall
			throw new phpFinalException("'".$name."' operator accepts only positive integers", $line, $this->file);
		}

		if($num instanceof Expr||($num !== null&&$num < 1)){
			throw new phpFinalException("'".$name."' operator with non-integer operand is no longer supported", $line, $this->file);
		}
		//throw new \RuntimeException('The "break 1+2;" syntax is not supported.');
		$id = $scopeNode->getId();
		$breaknum = $num ?? 1;
		for($i = 1; $i <= $breaknum - 1; $i++){
			$scopeNode = $this->forscope[$scopeNode->getParent()] ?? null;
			if($scopeNode === null){
				throw new phpFinalException("Cannot '".$name."' ".$breaknum." levels", $line, $this->file);
			}
			$id = $scopeNode->getId();
		}
		if($name === "continue"&&$scopeNode->getType() === scopenode::TYPE_SWITCH){
			if($scopeNode->getParent() === null){
				$this->logger->warning73('"continue" targeting switch is equivalent to "break"', $line);
			}else{
				$this->logger->warning73('"continue" targeting switch is equivalent to "break". Did you mean to use "continue 2"?', $line);
			}
		}
		$scopeNode->onUse();
		return $this->putGotoLabel($id);
	}


	public function putLabel(int $label) : string{
		return code::LABEL.$this->getInt($label);
	}

	public function getLogger() : Logger{
		return $this->logger;
	}

	public function execCoalesce(Coalesce $node, ?int $outputid) : string{
		/*if(!$node->left instanceof Variable){
			return $this->execStmts([$node->left]);
		}*/
		if($node->left instanceof Variable){
			$var = $node->left->name;
			if($var instanceof Expr){
				throw new \RuntimeException("\$var instanceof Expr === true");
			}
		}

		if($node->right instanceof Variable){
			$var = $node->right->name;
			if($var instanceof Expr){
				throw new \RuntimeException("\$var instanceof Expr === true");
			}
		}
		return "";//
	}

	public function putIsset(int $address) : string{
		return code::ISSET.$this->write_varId($address);
	}
}
