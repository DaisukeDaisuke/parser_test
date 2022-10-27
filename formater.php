<?php
declare(strict_types=1);

use PhpParser\ParserFactory;
use PhpParser\NodeDumper;
use PhpParser\PrettyPrinter\Standard;

include __DIR__."/vendor/autoload.php";

$code = '';
$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$stmts = $parser->parse("<?php\n".$code);

if($stmts === null){
	throw new \RuntimeException("phpParser crashed");
}

$dumper = new NodeDumper(['dumpComments' => true,]);
echo $dumper->dump($stmts, "<?php\n".$code);

$prettyPrinter = new Standard();
echo $prettyPrinter->prettyPrintFile($stmts)."\n";