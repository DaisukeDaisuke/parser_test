<?php

namespace purser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Scalar;
use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Expr\BinaryOp;

class InfoArray{
	//Inference type flags
	protected const TYPE_STRING = 	0b0000000000000001;
	protected const TYPE_FLOAT = 	0b0000000000000010;
	protected const TYPE_BOOL   = 	0b0000000000000100;
	protected const TYPE_FALSE = 	0b0000000000001000;
	protected const TYPE_TRUE = 	0b0000000000010000;
	protected const TYPE_NULL = 	0b0000000000100000;
	protected const TYPE_INT   = 	0b0000000001000000;
	//TYPE_CONST: my note: NULL, true, false
	protected const TYPE_CONST   = 	0b0000000010000000;
	protected const TYPE_ARRAY  = 	0b0000000100000000;
	protected const TYPE_OBJECT = 	0b0000001000000000;
	//TYPE_UNKNOWN: Inference failure/error
	protected const TYPE_UNKNOWN = 	0b0000010000000000;

	/** @var int */
	protected $internal_id;
	/**	@var array<int|float|string|bool, int> */
	protected $keys = [];
	/**	@var int */
	protected $lastKey = 0;

	public function __construct(int $internal_id){
		$this->internal_id = $internal_id;
	}

	public function getInternalId(): int{
		return $this->internal_id;
	}

	public function getLastIndex(): int{
		return $this->lastKey++;
	}

//	public function addKey(?Expr $key = null): int{
//
//	}
//
//	protected function doInference(?Expr $key = null){
//		$type = 0;
//		$value = null;
////
////		$this->keys[$key] = $this->lastKey++;
////		return $this->keys[$key];
//		if($key instanceof Scalar){
//			switch(true){
//				case $key instanceof String_:
//					$type = self::TYPE_STRING;
//					$value = $key->value;
//					break;
//				case $key instanceof LNumber:
//					$type = self::TYPE_INT;
//					$value = $key->value;
//					break;
//				case $key instanceof DNumber:
//					$type = self::TYPE_FLOAT;
//					$value = $key->value;
//					break;
//			}
//		}elseif($key instanceof ConstFetch){
//			$type = self::TYPE_CONST;
//			$value = strtoupper($key->name->parts[0]);
//			if($value === 'FALSE'){
//				$type = self::TYPE_FALSE;
//				$value = true;
//			}
//			if($value === 'TRUE'){
//				$type = self::TYPE_TRUE;
//				$value = true;
//			}
//			if($value === 'NULL'){
//				$type = self::TYPE_NULL;
//				$value = null;
//			}
//		}elseif($key instanceof UnaryMinus){
//			$var = $key->expr;
//			if($var instanceof Scalar){
//				[$type, $value] = $this->addKey($key->expr);
//				$value = -$value;
//			}else{
//				//TODO
//			}
//		}elseif($key instanceof BinaryOp){
//			$right = $key->right;
//			$left = $key->left;
//			$type = self::TYPE_UNKNOWN;
////			if($right instanceof Scalar && $left instanceof Scalar){
////
////			}else{
////				//TODO
////			}
//		}else{
//			$type = self::TYPE_UNKNOWN;
//		}
//		return [$type, $value];
//	}
}