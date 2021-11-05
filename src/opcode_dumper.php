<?php

namespace purser;

use pocketmine\utils\Binary;

class opcode_dumper{
	/** @phpstan-ignore-next-line */
	public static function hexentities(string $str, array &$list = []) : string{
		$result = '';
		for($i = 0, $iMax = strlen($str); $i < $iMax; $i++){
			//$return .= PHP_EOL.$i." | ";
			$return = "";
			$start = $i;
			switch($str[$i]){
				case code::READV:
					$return .= ' READV:'.bin2hex($str[$i++]).';';
					$return .= ' var:'.bin2hex($str[$i++]).';';
					$return .= ' var:'.bin2hex($str[$i]).';';
					break;
				case code::WRITEV:
					$return .= ' WRITEV:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::INT:
					$return .= ' INT:'.bin2hex($str[$i++]).';';
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
					$return .= PHP_EOL;

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
						$return .= ' '.$str[$i].':'.bin2hex($str[$i++]).';';
					}
					$i--;
					break;
				case code::VALUE:
					$return .= ' VALUE:'.bin2hex($str[$i++]).';';
					$return .= ' var:'.bin2hex($str[$i++]).';';
					$return .= ' var:'.bin2hex($str[$i]).';';
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
					break;
				case code::ADD:
					$return .= ' ADD?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::MUL:
					$return .= ' MUL?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::DIV:
					$return .= ' DIV?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::MINUS:
					$return .= ' MINUS?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::B_AND:
					$return .= ' B_AND?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::B_OR:
					$return .= ' B_OR?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::B_XOR:
					$return .= ' B_XOR?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::BOOL_AND:
					$return .= ' BOOL_AND?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::BOOL_OR:
					$return .= ' BOOL_OR?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::COALESCE:
					$return .= ' COALESCE?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::CONCAT:
					$return .= ' CONCAT?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;

				case code::EQUAL:
					$return .= ' EQUAL?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::GREATER:
					$return .= ' GREATER?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::GREATEROREQUAL:
					$return .= ' GREATEROREQUAL?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;

				case code::IDENTICAL:
					$return .= ' IDENTICAL?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::L_AND:
					$return .= ' L_AND?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::L_OR:
					$return .= ' L_OR?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::L_XOR:
					$return .= ' L_XOR?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::MOD:
					$return .= ' MOD?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::NOTIDENTICAL:
					$return .= ' NOTIDENTICAL?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::SHIFTLEFT:
					$return .= ' SHIFTLEFT?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::POW:
					$return .= ' POW?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::SHIFTRIGHT:
					$return .= ' SHIFTRIGHT?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::SMALLER:
					$return .= ' SMALLER?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::SMALLEROREQUAL:
					$return .= ' SMALLEROREQUAL?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::SPACESHIP:
					$return .= ' SPACESHIP?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::NOTEQUAL:
					$return .= ' NOTEQUAL?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::ABC:
					$return .= ' ABC?:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i]).';';
					break;
				case code::PRINT:
					$return .= ' PRINT:'.bin2hex($str[$i]).';';
					break;
				case code::JMP:
					$return .= ' JMP:'.bin2hex($str[$i]).';';
					break;
				case code::JMPZ:
					$return .= ' JMPZ:'.bin2hex($str[$i]).';';
					break;
				case code::SJMP:
					$return .= ' SJMP:'.bin2hex($str[$i]).';';
					break;
				case code::LABEL:
					$return .= ' LABEL:'.bin2hex($str[$i]).';';
					break;
				case code::LGOTO:
					$return .= ' LGOTO:'.bin2hex($str[$i]).';';
					//$return .= ' var:'.bin2hex($str[$i++]).';';
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
					$return .= ' var:'.bin2hex($str[$i++]).';';
					$return .= ' var:'.bin2hex($str[$i]).';';
					break;
				case code::EXIT:
					$return .= ' EXIT:'.bin2hex($str[$i]).';';
					break;
				case code::CAST:
					$return .= ' CAST:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' output:'.bin2hex($str[$i++]).';';
					$return .= ' id:'.bin2hex($str[$i]).';';
					break;
				default:
					$return .= ' :'.bin2hex($str[$i]).';';
			}

			$result .= PHP_EOL." | ".$start."-".$i." | ".($i - $start + 1)." | ".$return;
			$list[$start] = trim(PHP_EOL." | ".$start."-".$i." | ".($i - $start + 1)." | ".$return);
		}
		return $result;
	}

	public static function hexentities1(string $str) : string{
		$return = '';
		for($i = 0, $iMax = strlen($str); $i < $iMax; $i++){
			$return .= ' :'.bin2hex($str[$i]).';';
		}
		return $return;
	}

//	public static function hexentities_array(string $str) : array{
//		$stream = new BinaryStream($str);
//		$result = '';
//		while(!$stream->feof()){
//			//$return .= PHP_EOL.$i." | ";
//			$return = "";
//			$start = $i;
//			switch($str[$i]){
//				case code::READV:
//					$return .= ' READV:'.bin2hex($stream->get(1)).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::WRITEV:
//					$return .= ' WRITEV:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::INT:
//					$return .= ' INT:'.bin2hex($str[$i++]).';';
//					$return .= ' size:'.bin2hex($str[$i]).';';
//					$size = ord($str[$i++]);
//					$return1 = 0;
//					switch($size){
//						case code::TYPE_BYTE://byte
//							$return1 = Binary::readSignedByte($str[$i]);
//							$return .= ' '.$return1.':'.bin2hex($str[$i]).';';
//
//							break;
//						case code::TYPE_SHORT://short
//							$return1 = Binary::readSignedShort(substr($str, $i, 2));
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i]).';';
//							break;
//						case code::TYPE_INT://int
//							$return1 = Binary::readInt(substr($str, $i, 4));
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i]).';';
//							break;
//						case code::TYPE_LONG://long
//							$return1 = Binary::readLong(substr($str, $i, 8));
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i]).';';
//							break;
//						case code::TYPE_DOUBLE:
//							$return1 = Binary::readLDouble(substr($str, $i, 8));
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i]).';';
//							break;
//					}
//					$return .= PHP_EOL;
//
//					break;
//				case code::STRING:
//					$return .= ' STRING:'.bin2hex($str[$i++]).';';
//					$return .= ' INT:'.bin2hex($str[$i++]).';';
//					$return .= ' size:'.bin2hex($str[$i]).';';
//					$size = ord($str[$i++]);
//					//($size);
//					$return1 = 0;
//					switch($size){
//						case code::TYPE_BYTE://byte
//							$return .= ' '.Binary::readSignedByte($str[$i]).':'.bin2hex($str[$i]).';';
//							$return1 = Binary::readSignedByte($str[$i++]);
//							break;
//						case code::TYPE_SHORT://short
//							$return1 = Binary::readShort(substr($str, $i, 2));
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i]).';';
//							break;
//						case code::TYPE_INT://int
//							$return1 = Binary::readInt(substr($str, $i, 4));
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i]).';';
//							break;
//						case code::TYPE_LONG://long
//							$return1 = Binary::readLong(substr($str, $i, 8));
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i]).';';
//							break;
//						case code::TYPE_DOUBLE:
//							$return1 = Binary::readLDouble(substr($str, $i, 8));
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i++]).';';
//							$return .= ' :'.bin2hex($str[$i]).';';
//							break;
//					}
//					for($f = 1; $f <= $return1; $f++){
//						$return .= ' '.$str[$i].':'.bin2hex($str[$i++]).';';
//					}
//					$i--;
//					break;
//				case code::VALUE:
//					$return .= ' VALUE:'.bin2hex($str[$i++]).';';
//					$return .= ' var:'.bin2hex($str[$i++]).';';
//					$return .= ' var:'.bin2hex($str[$i]).';';
//					break;
//				case code::BOOL:
//					$return .= ' BOOL:'.bin2hex($str[$i++]).';';
//					$return .= ' var:';
//
//					$bool = ord($str[$i]);
//					//var_dump($bool);
//					if($bool === code::TYPE_NULL){
//						$return .= "null";
//					}elseif($bool === code::TYPE_TRUE){
//						$return .= "true";
//					}elseif($bool === code::TYPE_FALSE){
//						$return .= "false";
//					}
//					$return .= ";".PHP_EOL;
//					break;
//				case code::ADD:
//					$return .= ' ADD?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					$tmp1 = self::readScalar($stream);
//					$tmp2 = self::readScalar($stream);
//					$return
//					break;
//				case code::MUL:
//					$return .= ' MUL?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::DIV:
//					$return .= ' DIV?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::MINUS:
//					$return .= ' MINUS?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::B_AND:
//					$return .= ' B_AND?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::B_OR:
//					$return .= ' B_OR?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::B_XOR:
//					$return .= ' B_XOR?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::BOOL_AND:
//					$return .= ' BOOL_AND?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::BOOL_OR:
//					$return .= ' BOOL_OR?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::COALESCE:
//					$return .= ' COALESCE?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::CONCAT:
//					$return .= ' CONCAT?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//
//				case code::EQUAL:
//					$return .= ' EQUAL?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::GREATER:
//					$return .= ' GREATER?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::GREATEROREQUAL:
//					$return .= ' GREATEROREQUAL?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//
//				case code::IDENTICAL:
//					$return .= ' IDENTICAL?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::L_AND:
//					$return .= ' L_AND?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::L_OR:
//					$return .= ' L_OR?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::L_XOR:
//					$return .= ' L_XOR?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::MOD:
//					$return .= ' MOD?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::NOTIDENTICAL:
//					$return .= ' NOTIDENTICAL?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::SHIFTLEFT:
//					$return .= ' SHIFTLEFT?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::POW:
//					$return .= ' POW?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::SHIFTRIGHT:
//					$return .= ' SHIFTRIGHT?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::SMALLER:
//					$return .= ' SMALLER?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::SMALLEROREQUAL:
//					$return .= ' SMALLEROREQUAL?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::SPACESHIP:
//					$return .= ' SPACESHIP?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::NOTEQUAL:
//					$return .= ' NOTEQUAL?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::ABC:
//					$return .= ' ABC?:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i++]).';';
//					$return .= ' output:'.bin2hex($str[$i]).';';
//					break;
//				case code::PRINT:
//					$return .= ' PRINT:'.bin2hex($str[$i]).';';
//					break;
//				case code::JMP:
//					$return .= ' JMP:'.bin2hex($str[$i]).';';
//					break;
//				case code::JMPZ:
//					$return .= ' JMPZ:'.bin2hex($str[$i]).';';
//					break;
//				case code::SJMP:
//					$return .= ' SJMP:'.bin2hex($str[$i]).';';
//					break;
//				case code::LABEL:
//					$return .= ' LABEL:'.bin2hex($str[$i]).';';
//					break;
//				case code::LGOTO:
//					$return .= ' LGOTO:'.bin2hex($str[$i]).';';
//					//$return .= ' var:'.bin2hex($str[$i++]).';';
//					break;
//				case code::JMPA:
//					$return .= ' JMPA:'.bin2hex($str[$i]).';';
//					break;
//				case code::FUN_INIT:
//					$return .= ' FUN_INIT:'.bin2hex($str[$i]).';';
//					break;
//				case code::FUN_SEND_ARGS://FUN_SUBMIT output
//					$return .= ' FUN_SEND_ARGS:'.bin2hex($str[$i]).';';
//					break;
//				case code::FUN_SUBMIT://FUN_SUBMIT output
//					$return .= ' FUN_SUBMIT:'.bin2hex($str[$i++]).';';
//					$return .= ' var:'.bin2hex($str[$i++]).';';
//					$return .= ' var:'.bin2hex($str[$i]).';';
//					break;
//				case code::EXIT:
//					$return .= ' EXIT:'.bin2hex($str[$i]).';';
//					break;
//				default:
//					$return .= ' :'.bin2hex($str[$i]).';';
//			}
//
//			$result .= PHP_EOL." | ".$start."-".$i." | ".($i - $start + 1)." | ".$return;
//			$list[$start] = trim(PHP_EOL." | ".$start."-".$i." | ".($i - $start + 1)." | ".$return);
//		}
//		return $result;
//	}
//
//	/**
//	 * @param BinaryStream $stream
//	 * @return non-empty-string[]|int[]
//	 */
//	public static function readScalar(BinaryStream $stream) : array{
//		$opcode = $stream->get(1);
//		//var_dump([ord($opcode),$this->stream->getOffset()]);
//		if($opcode === code::READV){
//			$address = $stream->getShort();
//			return ['readv '.$address, '$'.$address];
//		}
//		if($opcode === code::VALUE){
//			$address = $stream->getShort();
//			return ['value '.$address, '$'.$address];
//		}
//		if($opcode === code::BOOL){
//			$address = $stream->getByte();
//			$result = null;
//			if($address === 0){
//				$result = "false";
//			}elseif($address === 1){
//				$result = "true";
//			}elseif($address === 2){
//				$result = "null";
//			}
//			return ["BOOL ".$result, $result];
//		}
//		if($opcode === code::INT){
//			$int = null;
//			$size = $stream->getByte();
//			switch($size){
//				case code::TYPE_BYTE://byte
//					$int = Binary::readSignedByte($stream->get(1));
//					break;
//				case code::TYPE_SHORT://short
//					$int = Binary::readSignedShort($stream->get(code::TYPE_SHORT));
//					break;
//				case code::TYPE_INT://int
//					$int = Binary::readInt($stream->get(code::TYPE_INT));
//					break;
//				case code::TYPE_LONG://long
//					$int = Binary::readLong($stream->get(code::TYPE_LONG));
//					break;
//				case code::TYPE_DOUBLE:
//					$int = Binary::readLDouble($stream->get(code::TYPE_SIZE_DOUBLE));
//					break;
//				default:
//					throw new \RuntimeException("int or Double not found");
//			}
//			return ['value '.$size.", ".$int, $int];
//		}
//		if($opcode === code::STRING){
//			$stream->getByte();//remove code::INT
//			$size = (int) $stream->getInt();
//			$str = $stream->get($size);
//			return ["STRING INT ".$size." ".$str, $str];
//		}
//		if($opcode === code::WRITEV){
//			$address = $stream->getShort();
//			$var = $this->readScalar($stream);
//
//			return ["WRITEV ".$address." ".$var[0], '$'.$address." = ".$var[1]];
//			//return null;
//		}
//
//		throw new \RuntimeException("Scalar ".bin2hex($opcode)." not found.");
//	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public static function dump($value){
		var_dump($value);
		return $value;
	}
}
