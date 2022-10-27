<?php

namespace purser;

use pocketmine\utils\Binary;
use function PHPStan\dumpType;

class opcode_dumper{
	protected const PREFIX_VAR = "output";
	protected const PREFIX_OUTPUT = "output";

	public const TYPE_FLAG_USED_VAR =   0b000001;
	public const TYPE_FLAG_BINARYOP =   0b000010;
	public const TYPE_FLAG_READV =      0b000100;
	public const TYPE_FLAG_VALUE =      0b001000;
	public const TYPE_FLAG_WRITEV =     0b010000;
	public const TYPE_FLAG_SCALAR =     0b100000;

	public static function dumpInt(string $str, int &$i) : string{
		$return = ' INT:'.bin2hex($str[$i++]).';';
		$return .= ' size:'.bin2hex($str[$i]).';';
		$size = ord($str[$i++]);
		$return1 = 0;
		switch($size){
			case code::TYPE_BYTE://byte
				$return1 = Binary::readSignedByte($str[$i]);
				$return .= ' '.$return1.':'.bin2hex($str[$i]).';';

				break;
			case code::TYPE_SHORT://short
				$return1 = Binary::readSignedShort(substr($str, $i, 2));
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i]).';';
				break;
			case code::TYPE_INT://int
				$return1 = Binary::readInt(substr($str, $i, 4));
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i]).';';
				break;
			case code::TYPE_LONG://long
				$return1 = Binary::readLong(substr($str, $i, 8));
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i++]).';';

				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i]).';';
				break;
			case code::TYPE_DOUBLE:
				$return1 = Binary::readLDouble(substr($str, $i, 8));
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i++]).';';

				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i++]).';';
				$return .= ' :'.bin2hex($str[$i]).';';
				break;
		}
		return $return;
	}

	/**
	 * @param string $str
	 * @param int $i
	 * @param int|null $size
	 * @return float|int|null
	 */
	public static function readInt(string $str, int &$i, ?int &$size = null){
		$code = $str[$i++];
		if($code !== code::INT){
			throw new \LogicException("readInt: off:".$i.", val: ".ord($code)." is not int");
		}
		$size = ord($str[$i++]);
		$result = null;
		switch($size){
			case code::TYPE_BYTE://byte
				$result = Binary::readSignedByte($str[$i]);
				break;
			case code::TYPE_SHORT://short
				$result = Binary::readSignedShort(substr($str, $i, 2));
				$i += 1;
				break;
			case code::TYPE_INT://int
				$result = Binary::readInt(substr($str, $i, 4));
				$i += 3;
				break;
			case code::TYPE_LONG://long
				$result = Binary::readLong(substr($str, $i, 8));
				$i += 7;
				break;
			case code::TYPE_DOUBLE:
				$result = Binary::readLDouble(substr($str, $i, 8));
				$i += 7;
				break;
		}
		return $result;
	}

	public static function readREADV(string $str, int &$i) : string{
		$code = $str[$i++];
		if($code !== code::READV){
			throw new \LogicException("readInt: off:".$i.", val: ".ord($code)." is not readv");
		}
		//dechex(ord($code1))
		$code1 = $str[$i++];

		$result = "";
		$result .= " original: ".Binary::readSignedShort($str[$i++].$str[$i++]);
		$code2 = $str[$i];
		if($code2 === code::INT){
			$result .= " int: ".self::readInt($str, $i);
		}elseif($code2 === code::READV){
			$result .= " move: ".self::readREADV($str, $i);
		}
		return $result;
	}

	public static function readScalar(string $str, int &$i, mixed &$scalar = null, int &$flag = 0) : string{
		$flag = 0;
		$code1 = $str[$i];
		if($code1 === code::INT){
            $scalar = self::readInt($str, $i);
			return "int: ".$scalar;
		}
        if($code1 === code::STRING){
            $scalar = self::readString($str, $i);
            return "string: ".$scalar;
        }
        if($code1 === code::READV){
			$flag |= self::TYPE_FLAG_READV;
            return self::readREADV($str, $i);
        }
		if($code1 === code::VALUE){
			$flag |= self::TYPE_FLAG_VALUE;
			$flag = 0;
			$i++;
			return self::readVar($str, $i, "VALUE", $scalar, $flag);
		}
        throw new \LogicException("opcode_dumper::readScalar: off: ".$i." val:".dechex(ord($code1))." is not scalar.");
    }

    private static function readString(string $str, int &$i): string{
        $code = $str[$i++];
        if($code !== code::STRING){
            throw new \LogicException("readString: off:".$i.", val: ".ord($code)." is not string");
        }
		$i++;
		$size = ord($str[$i++]);
		//($size);
		$return1 = 0;
		switch($size){
			case code::TYPE_BYTE://byte
				$return1 = Binary::readSignedByte($str[$i++]);
				break;
			case code::TYPE_SHORT://short
				$return1 = Binary::readSignedShort(substr($str, $i, 2));
				$i += 2;
				break;
			case code::TYPE_INT://int
				$return1 = Binary::readInt(substr($str, $i, 4));
				$i += 4;
				break;
			case code::TYPE_LONG://long
				$return1 = Binary::readLong(substr($str, $i, 8));
				$i += 8;
				break;
			case code::TYPE_DOUBLE:
				$return1 = Binary::readLDouble(substr($str, $i, 8));
				$i += 8;
				break;
		}
		$result = "";
		for($f = 1; $f <= $return1; $f++){
			$result .= $str[$i++];
		}
		--$i;
        return $result;
	}

	/** @phpstan-ignore-next-line */
	public static function hexentities(string $str, array &$list = [], array &$symbols = [], array &$var_unused_list = []) : string{
		try{
			$result = '';
			$var_used = null;
			for($i = 0, $iMax = strlen($str); $i < $iMax; $i++){
				//$return .= PHP_EOL.$i." | ";
				$return = "";
				$var_used = null;
				/** @var int $start */
				$start = $i;
				$opcode = $str[$i];
				$flag = 0;
				switch($str[$i]){
					case code::READV:
						$return .= ' READV:'.bin2hex($str[$i++]).';';
						$return .= self::readVar($str, $i, "var", $var_used, $flag);
						//$flag |= self::TYPE_FLAG_READV;
						break;
					case code::WRITEV:
						$return .= ' WRITEV:'.bin2hex($str[$i++]).';';
						$return .= self::readVar($str, $i, "output", $var_used, $flag);
						//$flag |= self::TYPE_FLAG_WRITEV;
						break;
					case code::INT:
						$return .= self::dumpInt($str, $i);
						$return .= PHP_EOL;
						//$flag |= self::TYPE_FLAG_SCALAR;
						break;
					case code::STRING:
						$return .= ' STRING:'.bin2hex($str[$i++]).';';
						$return .= ' INT:'.bin2hex($str[$i++]).';';
						$return .= ' size:'.bin2hex($str[$i]).';';
						$size = ord($str[$i++]);
						//($size);
						$return1 = 0;
						switch($size){
							case code::TYPE_BYTE://byte
								$return .= ' '.Binary::readSignedByte($str[$i]).':'.bin2hex($str[$i]).';';
								$return1 = Binary::readSignedByte($str[$i++]);
								break;
							case code::TYPE_SHORT://short
								$return1 = Binary::readShort(substr($str, $i, 2));
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i]).';';
								break;
							case code::TYPE_INT://int
								$return1 = Binary::readInt(substr($str, $i, 4));
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i]).';';
								break;
							case code::TYPE_LONG://long
								$return1 = Binary::readLong(substr($str, $i, 8));
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i++]).';';

								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i]).';';
								break;
							case code::TYPE_DOUBLE:
								$return1 = Binary::readLDouble(substr($str, $i, 8));
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i++]).';';

								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i++]).';';
								$return .= ' :'.bin2hex($str[$i]).';';
								break;
						}
						for($f = 1; $f <= $return1; $f++){
							$return .= ' '.str_replace("\x0a", "\\n", $str[$i]).':'.bin2hex($str[$i++]).';';
						}
						$i--;
						//$flag |= self::TYPE_FLAG_SCALAR;
						break;
					case code::VALUE:
						$return .= ' VALUE:'.bin2hex($str[$i++]).';';
						$return .= self::readVar($str, $i, "var", $var_used, $flag);
						//$flag |= self::TYPE_FLAG_VALUE;
						break;
					case code::BOOL:
						$return .= ' BOOL:'.bin2hex($str[$i++]).';';
						$return .= ' var:';

						$bool = ord($str[$i]);
						//var_dump($bool);
						if($bool === code::TYPE_NULL){
							$return .= "null";
						}elseif($bool === code::TYPE_TRUE){
							$return .= "true";
						}elseif($bool === code::TYPE_FALSE){
							$return .= "false";
						}
						$return .= ";".PHP_EOL;
						//$flag |= self::TYPE_FLAG_SCALAR;
						break;
					case code::ADD:
						$return .= self::readBinaryop($str, $i, "ADD", $var_used, $flag);
						break;
					case code::MUL:
						$return .= self::readBinaryop($str, $i, "MUL", $var_used, $flag);
						break;
					case code::DIV:
						$return .= self::readBinaryop($str, $i, "DIV", $var_used, $flag);
						break;
					case code::MINUS:
						$return .= self::readBinaryop($str, $i, "MINUS", $var_used, $flag);
						break;
					case code::B_AND:
						$return .= self::readBinaryop($str, $i, "B_AND", $var_used, $flag);
						break;
					case code::B_OR:
						$return .= self::readBinaryop($str, $i, "B_OR", $var_used, $flag);
						break;
					case code::B_XOR:
						$return .= self::readBinaryop($str, $i, "B_XOR", $var_used, $flag);
						break;
					case code::BOOL_AND:
						$return .= self::readBinaryop($str, $i, "BOOL_AND", $var_used, $flag);
						break;
					case code::BOOL_OR:
						$return .= self::readBinaryop($str, $i, "BOOL_OR", $var_used, $flag);
						break;
					case code::COALESCE:
						$return .= self::readBinaryop($str, $i, "COALESCE", $var_used, $flag);
						break;
					case code::CONCAT:
						$return .= self::readBinaryop($str, $i, "CONCAT", $var_used, $flag);
						break;
					case code::EQUAL:
						$return .= self::readBinaryop($str, $i, "EQUAL", $var_used, $flag);
						break;
					case code::GREATER:
						$return .= self::readBinaryop($str, $i, "GREATER", $var_used, $flag);
						break;
					case code::GREATEROREQUAL:
						$return .= self::readBinaryop($str, $i, "GREATEROREQUAL", $var_used, $flag);
						break;

					case code::IDENTICAL:
						$return .= self::readBinaryop($str, $i, "IDENTICAL", $var_used, $flag);
						break;
					case code::L_AND:
						$return .= self::readBinaryop($str, $i, "L_AND", $var_used, $flag);
						break;
					case code::L_OR:
						$return .= self::readBinaryop($str, $i, "L_OR", $var_used, $flag);
						break;
					case code::L_XOR:
						$return .= self::readBinaryop($str, $i, "L_XOR", $var_used, $flag);
						break;
					case code::MOD:
						$return .= self::readBinaryop($str, $i, "MOD", $var_used, $flag);
						break;
					case code::NOTIDENTICAL:
						$return .= self::readBinaryop($str, $i, "NOTIDENTICAL", $var_used, $flag);
						break;
					case code::SHIFTLEFT:
						$return .= self::readBinaryop($str, $i, "SHIFTLEFT", $var_used, $flag);
						break;
					case code::POW:
						$return .= self::readBinaryop($str, $i, "POW", $var_used, $flag);
						break;
					case code::SHIFTRIGHT:
						$return .= self::readBinaryop($str, $i, "SHIFTRIGHT", $var_used, $flag);
						break;
					case code::SMALLER:
						$return .= self::readBinaryop($str, $i, "SMALLER", $var_used, $flag);
						break;
					case code::SMALLEROREQUAL:
						$return .= self::readBinaryop($str, $i, "SMALLEROREQUAL", $var_used, $flag);
						break;
					case code::SPACESHIP:
						$return .= self::readBinaryop($str, $i, "SPACESHIP", $var_used, $flag);
						break;
					case code::NOTEQUAL:
						$return .= self::readBinaryop($str, $i, "NOTEQUAL", $var_used, $flag);
						break;
					case code::ABC:
						$return .= self::readBinaryop($str, $i, "ABC", $var_used, $flag);
						self::readBinaryop($str, $i, "aaa", $var_used, $flag);
						break;
					case code::PRINT:
						$return .= ' PRINT:'.bin2hex($str[$i]).';';
						break;
					case code::JMP:
						$return .= ' JMP:'.bin2hex($str[$i++]).', ';
						$int = self::readInt($str, $i, $size);
						$return .= "size: ".$size.", ".$int.", set: ".($i + $int).";";
						break;
					case code::JMPZ:
						$return .= ' JMPZ:'.bin2hex($str[$i]);//.' '.self::readScalar($str, $i, );
						break;
					case code::SJMP:
						$return .= ' SJMP:'.bin2hex($str[$i]).';';
						break;
					case code::LABEL:
						$return .= ' LABEL:'.bin2hex($str[$i++]).' ';
						/* @phpstan-ignore-next-line */
						$return .= 'label: 0x'.dechex(self::readInt($str, $i, $size)).";";
						break;
					case code::LGOTO:
						$return .= ' LGOTO:'.bin2hex($str[$i++]).' ';
						/* @phpstan-ignore-next-line */
						$return .= 'jmp: 0x'.dechex(self::readInt($str, $i, $size)).";";
						break;
					case code::JMPA:
						$return .= ' JMPA:'.bin2hex($str[$i]).';';
						break;
					case code::FUN_INIT:
						$return .= ' FUN_INIT:'.bin2hex($str[$i]).';';
						break;
					case code::FUN_SEND_ARGS://FUN_SUBMIT output
						$return .= ' FUN_SEND_ARGS:'.bin2hex($str[$i]).';';
						break;
					case code::FUN_SUBMIT://FUN_SUBMIT output
						$return .= ' FUN_SUBMIT:'.bin2hex($str[$i++]).';';
						$return .= self::readVar($str, $i, "var", $var_used, $flag);
						break;
					case code::EXIT:
						$return .= ' EXIT:'.bin2hex($str[$i]).';';
						break;
					case code::CAST:
						$return .= ' CAST:'.bin2hex($str[$i++]).';';
						$return .= self::readVar($str, $i, "output", $var_used, $flag);

						$type = ord($str[++$i]);
						$type1 = "unknown";
						switch($type){
							case code::TYPE_BOOL:
								$type1 = "bool";
								break;
							case code::TYPE_INT:
								$type1 = "int";
								break;
							case code::TYPE_DOUBLE:
								$type1 = "float";
								break;
							case code::TYPE_OBJECT:
								$type1 = "object";
								break;
							case code::TYPE_STRING:
								$type1 = "string";
								break;
							case code::TYPE_UNSET:
								$type1 = null;
								break;
						}
						$return .= ' type:'.$type."(".$type1.');';

						break;
					case code::ARRAY_CONSTRUCT:
						$return .= ' ARRAY_CONSTRUCT:'.bin2hex($str[$i++]).';';
						self::readVar($str, $i, "output", $var_used, $flag);
						$return .= " output: ".$var_used.';';;
						break;
					case code::ARRAY_SET:
						$return .= ' ARRAY_SET:'.bin2hex($str[$i++]).';';
						self::readVar($str, $i, "output", $var_used, $flag);
						$return .= ' #'.$var_used.';';;
//						++$i;
//						self::readScalar($str, $i, $key, $is_value);
//						if($is_value){
//							$return .= ' key: VALUE '.$key.';';;
//						}else{
//							$return .= ' key: '.$key.';';;
//						}
//						$is_value = false;
//						self::readScalar($str, $i, $value, $is_value);
//						if($is_value){
//							$return .= ' VALUE: '.$value.';';
//						}else{
//							$return .= ' scalar: '.$value.';';
//						}

						break;
						case code::ARRAY_GET:
						$return .= ' ARRAY_GET:'.bin2hex($str[$i++]).';';
						self::readVar($str, $i, "output", $var_used, $flag);
						$return .= ' #'.$var_used.';';
//						++$i;
//							$flag = 0;
//						self::readScalar($str, $i, $key, $flag);
//						if(($flag & self::TYPE_FLAG_READV) !== 0){
//							$return .= ' key: READV '.dechex($key).';';;
//						}elseif(($flag & self::TYPE_FLAG_VALUE) !== 0){
//							$return .= ' key: VALUE '.dechex($key).';';;
//						}
						break;
					case code::ARRAY_APPEND:
						$return .= ' ARRAY_APPEND:'.bin2hex($str[$i++]).';';
						self::readVar($str, $i, "output", $var_used, $flag);
						$return .= ' #'.$var_used.';';
						break;
					default:
						$return .= ' :'.bin2hex($str[$i]).';';
				}

				$result .= PHP_EOL." | ".$start."-".$i." | ".($i - $start + 1)." | ".$return;
				$list[$start] = trim(PHP_EOL." | ".$start."-".$i." | ".($i - $start + 1)." | ".$return);

//			if($var_used !== null){
//				/*
//				 * $var_unused_list[$var_used] >= 0: used
//				 * $var_unused_list[$var_used] === -1: unused
//				 */
//				if(!isset($var_unused_list[$var_used])){
//					$var_unused_list[$var_used] = 0;
//				}
//				var_dump($var_used, ((($flag & self::TYPE_FLAG_WRITEV) !== 0)), ((($flag & (self::TYPE_FLAG_USED_VAR)) === 0)||((($flag & (self::TYPE_FLAG_WRITEV)) !== 0))));
//				if(((($flag & (self::TYPE_FLAG_USED_VAR)) === 0)||((($flag & (self::TYPE_FLAG_WRITEV)) !== 0)))&&$var_unused_list[$var_used] >= 0){
//					++$var_unused_list[$var_used];
//				}else{
//					$var_unused_list[$var_used] = -1;
//				}
//			}

				$symbols[] = [$opcode, $start, $i, ($i - $start + 1), $var_used, $flag];
			}
		}catch(\Throwable $exception){
			echo "\n=====opcode_dumper crashed!=====\n";
			var_dump($result);
			echo "\n=====opcode_dumper crashed!=====\n";
			var_dump(self::hexentities1($str));

			if(isset($start)){
				var_dump(self::hexentities1(substr($str, $start, 10)));
			}else{
				var_dump("");
			}
			var_dump(self::hexentities1(substr($str, $i-10, 10)));
			var_dump(self::hexentities1(substr($str, $i, 20)));

			echo "\n=====opcode_dumper crashed!=====\n";
			throw $exception;
		}
		return $result;
	}

	public static function readVar(string $str, int &$i, string $prefix, ?int &$var_used, int &$flag) : string{
		$var_used = Binary::readShort(substr($str, $i, 2));
		$return = ' '.$prefix.':'.bin2hex($str[$i++]).'; ';
		$return .= ' '.$prefix.':'.bin2hex($str[$i]).'; ';
//		if($prefix === "var"){//output
//			$flag |= self::TYPE_FLAG_USED_VAR;
//		}
		return $return;
	}

	public static function readBinaryop(string $str, int &$i, string $prefix, ?int &$var_used, int &$flag) : string{
		$return = ' '.$prefix.'?:'.bin2hex($str[$i++]).'; ';
		$return .= self::readVar($str, $i, self::PREFIX_OUTPUT, $var_used, $flag);
		//$flag |= self::TYPE_FLAG_BINARYOP;
		return $return;

	}

	public static function hexentities1(string $str) : string{
		$return = '';
		for($i = 0, $iMax = strlen($str); $i < $iMax; $i++){
			$return .= ' :'.bin2hex($str[$i]).';';
		}
		return $return;
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public static function dump($value){
		var_dump($value);
		return $value;
	}
}