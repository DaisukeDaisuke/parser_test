<?php

namespace purser;

use pocketmine\utils\Binary;
use pocketmine\utils\BinaryStream;
use RuntimeException;

error_reporting(E_ALL);

class decoder{
	/** @var BinaryStream $stream */
	public $stream;
	/** @var int $len */
	public $len;
	/** @var mixed[] $values */
	public $values = [];

	public function __construct(){
		//none
	}

	public function decode(string $opcode) : void{
		$this->len = strlen($opcode);
		$this->stream = new BinaryStream($opcode);
		$this->decodeopcode();
	}

	public function decodeopcode() : void{
		$values = [];
		while(!$this->feof()){
			$opcode = $this->getByte();//......!!!!!!!!!!
			$test = bin2hex($opcode);
			//binaryOP
			/*if($opcode === code::NOP){
				continue;
			}*/
			if($opcode >= code::ADD&&$opcode <= code::ABC){
				$this->decodebinaryop_array($opcode);
				continue;
			}
			if($opcode >= code::PRINT){
				$this->decodeStmt_array($opcode);
				continue;
			}
			if($opcode >= code::READV&&$opcode <= code::UNSET){
				$this->decodeScalar($opcode);
				continue;
			}
			//throw new \RuntimeException("Unprocessed opcode ".ord($opcode));
		}
		//var_dump($values);
	}

	public function decodebinaryop_array(string $opcode) : void{
		if($opcode === code::CONCAT){
			//var_dump("!!");
		}
		$output = $this->getAddress();
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


	/**
	 * @param string $opcode
	 * @return void
	 * @throws RuntimeException
	 */
	public function decodeStmt_array(string $opcode) : void{
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
			case code::JMP:
				$jmp = $this->decodeScalar();
				$this->offset_seek($jmp);
				return;
			case code::JMPZ://JMPZ READV === 0 INT size offset ...
				$target = $this->decodeScalar();
				$jmp = $this->decodeScalar();
				if($target === 0){
					$this->offset_seek($jmp);
				}
				return;
			case code::JMPA:
				$jmp = $this->decodeScalar();
				$this->setOffset($jmp);
				return;
			case code::UNSET:
				$unset = $this->getAddress();
				$this->unsetValue($unset);
				return;
		}
		throw new RuntimeException("Stmt ".bin2hex($opcode)." not found");
	}

	/**
	 * @param string|null $opcode
	 * @return float|int|mixed|string|null
	 */
	function decodeScalar(?string $opcode = null){
		if($opcode === null){
			$opcode = $this->get(1);
		}
		//var_dump([ord($opcode),$this->stream->getOffset()]);
		if($opcode === code::READV){
			return $this->getvalue();//$this->getAddress();//$this->getvalue();
		}
		if($opcode === code::VALUE){
			return $this->getvalue(false);
		}
		if($opcode === code::INT){
			return $this->getInt();
		}
		if($opcode === code::STRING){
			$this->getByte();//remove code::INT
			return $this->get((int) $this->getInt());
		}
		if($opcode === code::WRITEV){
			$output = $this->getAddress();
			$var = $this->decodeScalar();
			$this->setvalue($output, $var);
			return null;
		}
		throw new RuntimeException("Scalar ".bin2hex($opcode)." not found");
	}

	/**
	 * @return float|int
	 */
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
		throw new RuntimeException("int or Double not found");
	}

	public function getBinaryStream() : ?BinaryStream{
		return $this->stream;
	}

	public function feof() : bool{
		return $this->stream->feof();
	}

	public function getlen() : int{
		return $this->len;
	}

	public function get(int $len) : string{
		return $this->stream->get($len);
	}

	public function getByte() : string{
		return $this->get(1);
	}

	public function getByteInt() : int{
		return ord($this->getByte());
	}

	public function getShort() : int{
		return $this->stream->getShort();
	}

	public function getAddress() : int{
		return $this->getShort();
	}

	public function offset_seek(int $jmp) : void{
		$this->stream->setOffset($this->getOffset() + $jmp);
	}

	public function getOffset() : int{
		return $this->stream->getOffset();
	}

	public function setOffset(int $offset) : void{
		$this->stream->setOffset($offset);
	}

	/**
	 * @param int $name
	 * @param mixed $var
	 * @return void
	 */
	public function setvalue(int $name, $var) : void{
		$this->values[$name] = $var;
	}

	/**
	 * @return mixed
	 */
	public function getvalue(bool $unset = true){
		$byte = $this->getAddress();
		$value = $this->values[$byte];
		if($unset === true){
			$this->unsetValue($byte);
		}
		return $value;
	}

	public function unsetValue(int $byte) : void{
		unset($this->values[$byte]);
	}

	/**
	 * @param int $name
	 * @return mixed
	 */
	public function value(int $name){
		return $this->values[$name];
	}
}