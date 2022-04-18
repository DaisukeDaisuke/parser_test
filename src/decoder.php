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
	/** @var ?string $tmpfuncName */
	public $tmpfuncName = null;
	/** @var array<int, mixed> $tmpfuncargs */
	public $tmpfuncargs = [];
	/** @var array<int, string> */
	public $dump;
	/** @var bool $debug */
	public $debug;

	public function __construct(){
		//none
	}

	public function decode(string $opcode, bool $debug = false) : void{
		$this->len = strlen($opcode);
		$this->stream = new BinaryStream($opcode);
		$this->debug = $debug;
		$this->dump = [];
		opcode_dumper::hexentities($opcode, $this->dump);

		$this->decodeopcode();
	}

	public function decodeopcode() : void{
		$values = [];
		while(!$this->feof()){
			if($this->debug === true){
				$string = $this->dump[$this->stream->getOffset()] ?? null;
				if($string === null){
					echo("> ".$this->stream->getOffset()."\n");
				}
				echo($string."\n");
			}
			$opcode = $this->getByte();//......!!!!!!!!!!
			$test = bin2hex($opcode);
			//binaryOP
			if($opcode === code::NOP){
				//echo "「nop: ".$this->getOffset()."」";
				continue;
			}
			if($opcode >= code::ADD&&$opcode <= code::ABC){
				$this->decodebinaryop_array($opcode);
				continue;
			}
			if($opcode >= code::PRINT){
				$this->decodeStmt_array($opcode);
				continue;
			}
			if($opcode >= code::READV&&$opcode <= code::ISSET){
				$this->decodeScalar($opcode);
				continue;
			}
			//throw new \RuntimeException("Unprocessed opcode ".ord($opcode));
		}
		//var_dump($values);
	}

	public function decodebinaryop_array(string $opcode) : void{
		/*if($opcode === code::CONCAT){
			//var_dump("!!");
		}*/
		$output = $this->getAddress();
		if($opcode === code::COALESCE){

			return;
		}

		$var1 = $this->decodeScalar();//
		$var2 = $this->decodeScalar();
		//var_dump([$output, $var1, $var2]);
		$return1 = null;

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
				$return1 = ($var1&&$var2);
				break;
			case code::BOOL_OR:
				$return1 = ($var1||$var2);
				break;
			case code::COALESCE:
				$return1 = $var1 ?? $var2;
				break;
			case code::CONCAT:
				$return1 = $var1.$var2;
				break;
			case code::EQUAL:
				$return1 = ($var1 == $var2);
				break;
			case code::GREATER:
				$return1 = ($var1 > $var2);
				break;
			case code::GREATEROREQUAL:
				$return1 = ($var1 >= $var2);
				break;
			case code::IDENTICAL:
				$return1 = ($var1 === $var2);
				break;
			case code::L_AND:
				$return1 = ($var1 and $var2);
				break;
			case code::L_OR:
				$return1 = ($var1 or $var2);
				break;
			case code::L_XOR:
				$return1 = ($var1 xor $var2);
				break;
			case code::MOD:
				$return1 = $var1 % $var2;
				break;
			case code::NOTEQUAL:
				$return1 = ($var1 != $var2);
				break;
			case code::NOTIDENTICAL:
				$return1 = ($var1 !== $var2);
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
				$return1 = ($var1 < $var2);
				break;
			case code::SMALLEROREQUAL:
				$return1 = ($var1 <= $var2);
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
	 * @throws RuntimeException|\Throwable
	 */
	public function decodeStmt_array(string $opcode) : void{
		switch($opcode){
			case code::PRINT:
//				$tmp = $this->decodeScalar();
//				if($tmp === null){
//					return;
//				}
//				echo $tmp;
				echo $this->decodeScalar();
				return;
			case code::JMP:
			case code::SJMP:
				$jmp = $this->decodeScalar();
				$this->offset_seek($jmp);
				return;
			case code::JMPZ://JMPZ READV === 0 INT size offset ...
				$target = $this->decodeScalar();
				$jmp = $this->decodeScalar();
				if($target == 0){
					$this->offset_seek($jmp);
					if($this->debug === true){
						$binaryStream = $this->getBinaryStream();
						if($binaryStream instanceof BinaryStream){
							echo "jumped, offset: ", $binaryStream->getOffset(), "\n";
						}
					}
				}
				return;
			case code::JMPA:
				$jmp = $this->decodeScalar();
				$this->setOffset($jmp);
				return;
			case code::FUN_INIT:
				$this->tmpfuncName = $this->decodeScalar();
				$this->tmpfuncargs = [];
				return;
			case code::FUN_SEND_ARGS:
				if($this->tmpfuncName === null){
					throw new \RuntimeException("Arguments cannot be sent before the function is initialized.");
				}
				$this->tmpfuncargs[] = $this->decodeScalar();
				return;
			case code::FUN_SUBMIT:
				$output = $this->getAddress();
				if($this->tmpfuncName === null){
					throw new \RuntimeException("The function cannot be sent before the function is initialized.");
				}
				$func = $this->tmpfuncName;
				if($func === "var_dump"){
					$this->user_var_dump($this->tmpfuncargs);
					$this->setvalue($output, null);
					return;
				}
				if(!function_exists($func)){
					throw new \RuntimeException("function ".$func." not found.");
				}
				//var_dump($func, $this->tmpfuncargs);
				try{
					/** @phpstan-ignore-next-line */
					$result = ($func)(...$this->tmpfuncargs);
					$this->setvalue($output, $result);
				}catch(\Throwable $e){
					echo "FUN_SUBMIT: final: catch Throwable.";//
					throw new $e;//
				}
				$this->tmpfuncName = null;
				$this->tmpfuncargs = [];
				return;
			case code::EXIT:
				$var = $this->decodeScalar();
				throw new ExitException($var);
			case code::CAST:
				$address = $this->getAddress();
				$type = $this->getByteInt();
				$scalar = $this->decodeScalar();
				$return = null;
				switch($type){
					case code::TYPE_BOOL:
						$return = (bool) $scalar;
						break;
					case code::TYPE_INT:
						$return = (int) $scalar;
						break;
					case code::TYPE_DOUBLE:
						$return = (float) $scalar;
						break;
					case code::TYPE_OBJECT:
						$return = (object) $scalar;
						break;
					case code::TYPE_STRING:
						$return = (string) $scalar;
						break;
					case code::TYPE_UNSET:
						$return = null;
						break;
				}
				$this->setvalue($address, $return);
				return;
		}
		throw new RuntimeException("Unexpected Stmt: off:".$this->getOffset().", op:".bin2hex($opcode)." not found");
	}

	/**
	 * @param string|null $opcode
	 * @return float|int|mixed|string|null
	 */
	function decodeScalar(?string $opcode = null){
		if($opcode === null){
			$opcode = $this->get(1);
		}
		$opcodedebug = bin2hex($opcode);
		//var_dump([ord($opcode),$this->stream->getOffset()]);
		if($opcode === code::READV){
			return $this->getvalue();//$this->getAddress();//$this->getvalue();
		}
		if($opcode === code::VALUE){
			return $this->getvalue(false);
		}
		if($opcode === code::BOOL){
			return $this->getBool();
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
			return $var;
			//return null;
		}

		$binaryStream = $this->getBinaryStream();
		if($binaryStream === null){
			throw new \RuntimeException('$binaryStream === null');
		}
		$offset = $binaryStream->offset;
		throw new RuntimeException("Scalar ".bin2hex($opcode)." not found. opcode off: ".$offset);
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
				return Binary::readSignedShort($this->get(code::TYPE_SHORT));
			case code::TYPE_INT://int
				return Binary::readInt($this->get(code::TYPE_INT));
			case code::TYPE_LONG://long
				return Binary::readLong($this->get(code::TYPE_LONG));
			case code::TYPE_DOUBLE:
				return Binary::readLDouble($this->get(code::TYPE_SIZE_DOUBLE));
		}
		throw new RuntimeException("int or Double not found");
	}

	private function getBool() : ?bool{
		$str = $this->getByteInt();
		if($str === code::TYPE_NULL){
			return null;
		}
		return (bool) $str;

		/*if($str === self::NULL_ID){
			return null;
		}elseif($str === 1){//TYPE_TRUE
			return true;
		}
		return false;*/

		/*if($str === self::TYPE_NULL){
			return 0;
		}
		return $str;*/
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
		return $this->stream->getSignedShort();
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
			unset($this->values[$byte]);
		}
		return $value;
	}

	/**
	 * @param int $name
	 * @return mixed
	 */
	public function value(int $name){
		return $this->values[$name];
	}

	/**
	 * @param array<int, mixed> $tmpfuncargs
	 */
	private function user_var_dump(array $tmpfuncargs) : void{
		foreach($tmpfuncargs as $arg){
			switch(true){
				case is_null($arg);
					echo "NULL";
					break;
				case $arg === true;
					echo "bool(true)";
					break;
				case $arg === false;
					echo "bool(false)";
					break;
				case is_int($arg);
					echo "int(".((string) $arg).")";
					break;
				case is_float($arg);
					echo "float(".((string) $arg).")";
					break;
				case is_string($arg);
					echo 'string('.strlen($arg).') "'.$arg.'"';
					break;
				//case is_array($arg);
				//echo "array(0) {\n}";
				//break;
				default:
					var_dump($arg);
					throw new \RuntimeException("dump: Received an unsupported value.");
			}
			echo PHP_EOL;
		}

	}


}