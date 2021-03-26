<?php

namespace purser;

use pocketmine\utils\Binary;

class opcode_dumper{
	public static function hexentities($str){
		$return = '';
		for($i = 0, $iMax = strlen($str); $i < $iMax; $i++){
			switch(substr($str, $i, 1)){
				case code::READV:
					$return .= ' READV:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' var:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' var:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::WRITEV:
					$return .= ' WRITEV:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
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
							$return1 = Binary::readLDouble(substr($str, $i, 8));
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
							$return1 = Binary::readLDouble(substr($str, $i, 8));
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
					for($f = 1; $f <= $return1; $f++){
						$return .= ' '.substr($str, $i, 1).':'.bin2hex(substr($str, $i++, 1)).';';
					}
					$i--;
					break;
				case code::ADD:
					$return .= ' ADD?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::MUL:
					$return .= ' MUL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::DIV:
					$return .= ' DIV?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::MINUS:
					$return .= ' MINUS?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::B_AND:
					$return .= ' B_AND?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::B_OR:
					$return .= ' B_OR?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::B_XOR:
					$return .= ' B_XOR?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::BOOL_AND:
					$return .= ' BOOL_AND?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::BOOL_OR:
					$return .= ' BOOL_OR?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::COALESCE:
					$return .= ' COALESCE?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::CONCAT:
					$return .= ' CONCAT?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;

				case code::EQUAL:
					$return .= ' EQUAL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::GREATER:
					$return .= ' GREATER?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::GREATEROREQUAL:
					$return .= ' GREATEROREQUAL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;

				case code::IDENTICAL:
					$return .= ' IDENTICAL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::L_AND:
					$return .= ' L_AND?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::L_OR:
					$return .= ' L_OR?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::L_XOR:
					$return .= ' L_XOR?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::MOD:
					$return .= ' MOD?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::NOTIDENTICAL:
					$return .= ' NOTIDENTICAL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::SHIFTLEFT:
					$return .= ' SHIFTLEFT?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::POW:
					$return .= ' POW?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::SHIFTRIGHT:
					$return .= ' SHIFTRIGHT?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::SMALLER:
					$return .= ' SMALLER?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::SMALLEROREQUAL:
					$return .= ' SMALLEROREQUAL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::SPACESHIP:
					$return .= ' SPACESHIP?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::NOTEQUAL:
					$return .= ' NOTEQUAL?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::ABC:
					$return .= ' ABC?:'.bin2hex(substr($str, $i++, 1)).';';
					$return .= ' output:'.bin2hex(substr($str, $i++, 1)).';';
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
				case code::LABEL:
					$return .= ' LABEL:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::LGOTO:
					$return .= ' LGOTO:'.bin2hex(substr($str, $i, 1)).';';
					break;
				case code::JMPA:
					$return .= ' JMPA:'.bin2hex(substr($str, $i, 1)).';';
					break;
				default:
					$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
			}
		}
		return $return;
	}

	public static function hexentities1($str){
		$return = '';
		for($i = 0, $iMax = strlen($str); $i < $iMax; $i++){
			$return .= ' :'.bin2hex(substr($str, $i, 1)).';';
		}
		return $return;
	}
}
