<?php

namespace purser;

use pocketmine\utils\Binary;

class opcode_dumper{
	public static function hexentities(string $str): string{
		$return = '';
		for($i = 0, $iMax = strlen($str); $i < $iMax; $i++){
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
							$return1 = Binary::readLShort(substr($str, $i, 2));
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
							$return1 = Binary::readLShort(substr($str, $i, 2));
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
				case code::LABEL:
					$return .= ' LABEL:'.bin2hex($str[$i]).';';
					break;
				case code::LGOTO:
					$return .= ' LGOTO:'.bin2hex($str[$i]).';';
					break;
				case code::JMPA:
					$return .= ' JMPA:'.bin2hex($str[$i]).';';
					break;
				default:
					$return .= ' :'.bin2hex($str[$i]).';';
			}
		}
		return $return;
	}

	public static function hexentities1(string $str): string{
		$return = '';
		for($i = 0, $iMax = strlen($str); $i < $iMax; $i++){
			$return .= ' :'.bin2hex($str[$i]).';';
		}
		return $return;
	}
}
