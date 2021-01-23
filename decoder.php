<?php


use pocketmine\utils\Binary;
use pocketmine\utils\BinaryStream;

class decoder{
	/** @var BinaryStream $stream */
	public $stream;

	public $values = [];

	public function __construct(){
		//none
	}

	public function decode(string $opcode){
		$this->stream = new BinaryStream($opcode);
	}

	public function decodeop_array($target){
		$values = [];
		foreach($target as $value){
			$opcode = $value[0];
			//binaryOP
			if($opcode >= code::ADD&&$opcode <= code::NOTEQUAL){
				var_dump("!!");
				$this->decodebinaryop_array($value, $values);
			}
			if($opcode >= code::PRINT){
				$this->decodeStmt_array($value, $values);
			}
			//var_dump($values);
		}
		var_dump($values);
		//var_dump($return1);
		//return var_dump($this->execBinaryOp($expr));
		//return $values[array_key_last($values)];
	}
}

	public function decodebinaryop_array($value){
		$opcode = $value[0];
		$output = $value[1];
		$var1 = $this->test($value[2], $values);//
		$var2 = $this->test($value[3], $values);
		var_dump([$var1, $var2]);


		$return1 = 0;

		switch($opcode){
			//binaryOP
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
	}


	public function decodeStmt_array($value, &$values){
		$opcode = $value[0];
		$var1 = $this->test($value[1], $values);//
		$return1 = 0;

		switch($opcode){
			case code::PRINT:
				echo $var1;
				//$return1 = 1;
				break;
		}
	}

	function decodeScalar(&$var, &$offset, $values){
		$opcode = $var[$offset++];
		if($opcode === code::READV){
			return $values[ord($var[$offset++])];
		}
		if($opcode === code::INT){
			return $this->decodeint($var, $offset);
		}
		if($opcode === code::STRING){
			$len = $this->decodeint($var, $offset);
			$target = $offset;
			$offset += $len;
			return substr($var, $target, $len);
		}
	}

	function decodeint($var, &$offset){
		if($var[$offset++] === code::INT){
			$size = ord($var[$offset++]);
			switch($size){
				case self::TYPE_BYTE://byte
					return Binary::readSignedByte($var[$offset++]);
				case self::TYPE_SHORT://short
					return Binary::readLShort($this->get($var, self::TYPE_SHORT, $offset));//...?
				case self::TYPE_INT://int
					return Binary::readInt($this->get($var, self::TYPE_INT, $offset));
				case self::TYPE_LONG://long
					return Binary::readLong($this->get($var, self::TYPE_LONG, $offset));
			}
		}
	}

	public function get($var, $len, &$i){
		$test = $i;
		$i += $len;
		return substr($var, $test, $len);
	}
}