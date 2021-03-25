<?php


use pocketmine\utils\Binary;
use pocketmine\utils\BinaryStream;

class decoder{
	/** @var BinaryStream $stream */
	public $stream;

	/** @var int $len */
	public $len;

	public $values = [];

	public function __construct(){
		//none
	}

	public function decode(string $opcode){
		$this->len = strlen($opcode);
		$this->stream = new BinaryStream($opcode);
		$this->decodeopcode();
	}

	public function decodeopcode(){
		$values = [];
		while(!$this->feof()){
			$opcode = $this->getByte();//......!!!!!!!!!!
			//binaryOP
			if($opcode >= code::ADD&&$opcode <= code::ABC){
				$this->decodebinaryop_array($opcode);
			}
			if($opcode >= code::PRINT){
				$this->decodeStmt_array($opcode);
			}
			if($opcode >= code::READV&&$opcode <= code::STRING){
				$this->decodeScalar($opcode);
			}
		}
		//var_dump($values);
	}

	public function decodebinaryop_array($opcode){
		if($opcode === code::CONCAT){
			//var_dump("!!");
		}
		$output = $this->getInt();
		var_dump($output);
		$var1 = $this->decodeScalar();//
		$var2 = $this->decodeScalar();
		//var_dump([$output, $var1, $var2]);
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
				$return1 = (int) ($var1&&$var2);
				break;
			case code::BOOL_OR:
				$return1 = (int) ($var1||$var2);
				break;
			case code::COALESCE:
				//$return1 = $var1 ?? $var2;
				break;
			case code::CONCAT:
				$return1 = $var1.$var2;
				break;
			case code::EQUAL:
				$return1 = (int) ($var1 == $var2);
				break;
			case code::GREATER:
				$return1 = (int) ($var1 > $var2);
				break;
			case code::GREATEROREQUAL:
				$return1 = (int) ($var1 >= $var2);
				break;
			case code::IDENTICAL:
				$return1 = (int) ($var1 === $var2);
				break;
			case code::L_AND:
				$return1 = (int) ($var1 and $var2);
				break;
			case code::L_OR:
				$return1 = (int) ($var1 or $var2);
				break;
			case code::L_XOR:
				$return1 = (int) ($var1 xor $var2);
				break;
			case code::MOD:
				$return1 = $var1 % $var2;
				break;
			case code::NOTEQUAL:
				$return1 = (int) ($var1 != $var2);
				break;
			case code::NOTIDENTICAL:
				$return1 = (int) ($var1 !== $var2);
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
				$return1 = (int) ($var1 < $var2);
				break;
			case code::SMALLEROREQUAL:
				$return1 = (int) ($var1 <= $var2);
				break;
			case code::SPACESHIP:
				$return1 = ($var1 <=> $var2);
				break;
		}
		//var_dump($output." => ".$return1);
		$this->setvalue($output, $return1);
	}


	public function decodeStmt_array($opcode){
		//$opcode = $this->get(1);
		//$var1 = $this->value($this->decodeScalar());
		$return1 = 0;

		switch($opcode){
			case code::PRINT:
				//var_dump($this->values);
				$var1 = $this->decodeScalar();

				echo $var1;
				//$return1 = 1;
				return;
				break;
			case code::JMP:
				$jmp = $this->decodeScalar();
				$this->offset_seek($jmp);
				return;
				break;
			case code::JMPZ://JMPZ READV === 0 INT size offset ...
				$target = $this->decodeScalar();
				$jmp = $this->decodeScalar();
				if($target === 0){
					$this->offset_seek($jmp);
				}
				return;
				break;
			case code::JMPA:
				$jmp = $this->decodeScalar();
				$this->setOffset($jmp);
				return;
				break;
		}
		throw new \RuntimeException("Stmt ".bin2hex($opcode)." not found");
	}

	function decodeScalar(?string $opcode = null){
		if($opcode === null){
			$opcode = $this->get(1);
		}
		//var_dump([ord($opcode),$this->stream->getOffset()]);
		if($opcode === code::READV){
			return $this->getvalue();
		}
		if($opcode === code::INT){
			return $this->getInt();
		}
		if($opcode === code::STRING){
			$this->getByte();//remove code::INT
			return $this->get($this->getInt());
		}
		if($opcode === code::WRITEV){
			$output = $this->getInt();
			$var = $this->decodeScalar();
			$this->setvalue($output, $var);
			return null;
		}
		throw new \RuntimeException("Scalar ".bin2hex($opcode)." not found");
	}

	function getInt(){
		$size = $this->getByteInt();
		switch($size){
			case code::TYPE_BYTE://byte
				return Binary::readSignedByte($this->getByte());
			case code::TYPE_SHORT://short
				return Binary::readLShort($this->get(code::TYPE_SHORT));
			case code::TYPE_INT://int
				return Binary::readInt($this->get(code::TYPE_INT));
			case code::TYPE_LONG://long
				return Binary::readLong($this->get(code::TYPE_LONG));
			case code::TYPE_DOUBLE:
				return Binary::readLDouble($this->get(code::TYPE_SIZE_DOUBLE));
		}
		throw new \RuntimeException("int or Double not found");
	}

	public function getBinaryStream(): ?BinaryStream{
		return $this->stream;
	}

	public function feof(): bool{
		return $this->stream->feof();
	}

	public function getlen(){
		return $this->len;
	}

	public function get($len){
		return $this->stream->get($len);
	}

	public function getByte(){
		return $this->get(1);
	}

	public function getByteInt(): int{
		return ord($this->getByte());
	}

	public function offset_seek($jmp){
		$this->stream->setOffset($this->getOffset()+$jmp);
	}

	public function getOffset(): int{
		return $this->stream->getOffset();
	}

	public function setOffset(int $offset){
		$this->stream->setOffset($offset);
	}

	public function setvalue($name, $var){
		$this->values[$name] = $var;
	}

	public function getvalue(){
		$byte = $this->getInt();
		$value = $this->values[$byte];
		unset($this->values[$byte]);
		return $value;
	}

	public function value($name){
		return $this->values[$name];
	}
}