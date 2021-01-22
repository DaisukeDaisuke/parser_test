<?php
include __DIR__."/vendor/autoload.php";

use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Mul;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\Plus;
use PhpParser\Node\Expr\BinaryOp\Div;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

use PhpParser\Node\Stmt;

use pocketmine\utils\Binary;

$code = '
<?php
$a = 100;
echo 1+2*(3/$a*1);
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


//var_dump($stmts);

class code{
    const ADD = "\x00";//add 80 1000 $a
    const READV = "\x01";//readv $a(0)
    const INT = "\x02";
    const MUL = "\x03";
    const DIV = "\x04";

    const PRINT = "\xff";
}

class main_old{
    const TYPE_BYTE = 0;
    const TYPE_SHORT = 1;
    const TYPE_INT = 2;
    const TYPE_LONG = 3;

    public $count = -1;


    /** @var int */
    public $blockid = 1;
    /** @var CodeBlock[] */
    public $block = [];

    public function __construct(){
        $this->block[$this->blockid] = new CodeBlock($this->blockid);
        var_dump($this->checkIntSize(254));
    }

    function execStmt($node){
        var_dump("execStmt");
        switch(get_class($node)){
            case Echo_::class:
                var_dump("echo");
                return code::PRINT.$this->execStmts($node->exprs);
                break;
        }
    }

    function execStmts(array $nodes){
        $return = "";
        foreach($nodes as $node){
            if($node instanceof Expr){
                $return .= $this->execExpr($node);
            }
            if($node instanceof Stmt){
                $return .= $this->execStmt($node);
            }
            /*if($node instanceof node){

            }*/
        }
        return $return;
    }

    function execExpr(Expr $expr){
        if($expr instanceof BinaryOp){
            return var_dump($this->execBinaryOp($expr));
        }
    }

    function execBinaryOp($node){//output //add 10 10 1
        var_dump($this->count);
        switch(get_class($node)){
            case Plus::class:
                return $this->execbinaryplus($node,"plus");;
                break;
            case Mul::class:
                return $this->execbinaryplus($node,"mul");;
                break;
            case Div::class:
                //$left = $this->execScalar_var($node->left);
                //$right = $this->execScalar_var($node->right);
                //return code::DIV.$left.$right;
                //++$this->count;
                return $this->execbinaryplus($node,"div");
                break;

        }

    }

    function execbinaryplus($node, $id){
        $left = $this->execScalar_var($node->left);
        $right = $this->execScalar_var($node->right);

        $return = [];
        if(is_array($left)&&is_array($right)){
            $return = [...$left,...$right];
            $return[] = [$id,++$this->count, ++$this->count, $this->count+1];//$left,$right
        }else if(is_array($left)){
            $return = $left;
            $return[] = [$id,++$this->count, $right, $this->count+1];
        }else if(is_array($right)){
            $return = $right;
            $return[] = [$id,$left, ++$this->count, $this->count+1];
        }else{
            $return[] = [$id, $left, $right, $this->count+1];
        }
        //var_dump($return);
        return $return;
    }

    function execScalar_var($value){//array...?
        if($value instanceof Scalar){
            return $this->execScalar($value);
        }

        if($value instanceof BinaryOp){
            return $this->execBinaryOp($value);
            //return $this->execBinaryOp($value);
        }

        if($value instanceof Variable){
            return $this->exec_var($value);
        }
    }


    function exec_var(Variable $node){//変数処理...
        if($node->name instanceof Expr){
            return "";//$$b
        }
        return code::READV.$this->getValualueId($node->name);
    }

    public function getValualueId($value){
        return chr($this->block[$this->blockid]->get($value));
    }


    /*function execBinaryOp($node){
        switch(get_class($node)){
            case Plus::class:
                return "";
                break;
        }
    }*/

    function execScalar($node){
        switch(get_class($node)){
            case LNumber::class:
                //$value = $node->value;
                //$intsize = $this->checkIntSize($value);
                //return [$intsize, $value];
                return $this->getInt($node->value);
                break;
        }
    }

    function getInt($value){
        $size = $this->checkIntSize($value);
        $return = code::INT.chr($size);
        switch($size){
            case self::TYPE_BYTE://byte
                $return .= Binary::readSignedByte($value);
                break;
            case self::TYPE_SHORT://short
                $return .= Binary::writeLShort($value);
                break;
            case self::TYPE_INT://int
                $return .= Binary::writeInt($value);
                break;
            case self::TYPE_LONG://long
                $return .= Binary::writeLong($value);
                break;
        }
        return $return;
    }

    function checkIntSize($value){
        switch(true){
            case $value <= 127&&$value >= -128://byte
                return self::TYPE_BYTE;
            case $value <= 0xffff&&$value >= -0xffff://short
                return self::TYPE_SHORT;
            case $value <= 0x7FFFFFFF&&$value >= -0x7FFFFFFF://int
                return self::TYPE_INT;
            case $value <= 0x7FFFFFFFFFFFFFFF&&$value >= -0x7FFFFFFFFFFFFFFF://long
                return self::TYPE_LONG;
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
        return $this->ids[$value] ?? 1;//debug
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

$main_old = new main_old();
$output = $main_old->execStmts($stmts);
var_dump($output);
file_put_contents("output.txt", $output);
//var_dump(token_get_all($code));


/*
$tokens = token_get_all($code);

foreach ($tokens as $token) {
    if (is_array($token)) {
        echo "Line {$token[2]}: ", token_name($token[0]), " ('{$token[1]}')", PHP_EOL;
    }
}*/