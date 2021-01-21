<?php
include __DIR__."/vendor/autoload.php";

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\AssignOp\Mul;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\Plus;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

$code = '
<?php
$a = 100;
echo 1+2*(3/$a);
';

//$code = file_get_contents("/sdcard/www/public/php-parser/vendor/unphar.php");

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$stmts = $parser->parse($code);
//$oldTokens = $parser->getTokens();
//var_dump($stmts);
$dumper = new NodeDumper([
	'dumpComments' => true,
]);
echo $dumper->dump($stmts, $code);

var_dump($stmts);

class code{
	const ADD = 0;//add 80 1000 $a
	const readv = 1;//readv $a(0)

}

class main_old{
	/** @var int */
	public $blockid = 1;
	/** @var CodeBlock[] */
	public $block = [];

	public function __construct($block){
		$this->block[$this->blockid] = new CodeBlock($this->blockid);
	}

	function getcode($node){
		switch(get_class($node)){
			case Plus::class:
				$this->execScalar_var($node->right);//opcode
				break;
			case Mul::class:
				$node->left;
				$node->right;
				break;

		}

	}

	function execScalar_var($value){//array...?
		if($value instanceof Scalar){
			return $this->execScalar($value);
		}

		if($value instanceof BinaryOp){
			return $this->execBinaryOp($value);
		}

		if($value instanceof Variable){
			return $this->exec_var($value);
		}
	}


	function exec_var(Variable $node){//変数処理...
		if($node->name instanceof Expr){
			return "";//$$b
		}
		return chr(code::readv).$this->getValualueId($node->name);
	}

	public function getValualueId($value){
		return chr($this->block[$this->blockid]->get($value));
	}


	function execBinaryOp($node){
		switch(get_class($node)){
			case Plus::class:
				return "";
				break;
		}
	}

	function execScalar($node){
		switch(get_class($node)){
			case LNumber::class:
				$value = $node->value;
				$intsize = $this->checkIntSize($value);
				//return [$intsize, $value];
				return $value;
				break;
		}
	}

	function checkIntSize($value){
		if($value < 0){
			switch(true){
				case $value >= -0xff://byte
					return 5;
				case $value >= -0xffff://short
					return 6;
				case $value >= -0x7FFFFFFF://int
					return 7;
				case $value >= -0x7FFFFFFFFFFFFFFF://long
					return 8;
			}
		}else{
			switch(true){
				case $value <= 0xff://byte
					return 1;
				case $value <= 0xffff://short
					return 2;
				case $value <= 0x7FFFFFFF://int
					return 3;
				case $value <= 0x7FFFFFFFFFFFFFFF://long
					return 4;
			}
		}
	}
}

class CodeBlock{
	public $ids = [];
	public $values = [];
	public $nowvaluesid = 0;


	public $block;

	public function __construct($block){
		$this->block = $block;
	}

	public function getBlock(): int{
		return $this->block;
	}

	function get($value){
		return $this->ids[$value];
	}

	function add($value){//変数名
		if(isset($this->values[$this->nowvaluesid + 1])){
			$this->values[++$this->nowvaluesid] = [];
		}
		$this->values[$this->nowvaluesid] = $value;
		$this->ids[$value] = $this->nowvaluesid;
		return $this->nowvaluesid;
	}

}
//var_dump(token_get_all($code));


/*
$tokens = token_get_all($code);

foreach ($tokens as $token) {
    if (is_array($token)) {
        echo "Line {$token[2]}: ", token_name($token[0]), " ('{$token[1]}')", PHP_EOL;
    }
}*/
