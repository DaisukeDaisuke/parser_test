<?php

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

include "vendor/autoload.php";

class main{
	public $metadata = [];

	function exec2($nodes){
		foreach($nodes as $node){
			$this->exec1($node);
		}
	}
	function exec1($node){
		//$nodes = is_array($nodes) ? [$nodes] : $nodes;

		//var_dump($nodes);

			switch(get_class($node)){
				case Namespace_::class:
					$name = $this->exec1($node->name)[0];
					$this->metadata["Namespace"] = $name;
					$this->exec2($node->stmts);
					break;
				case Name::class:
					return $node->parts;
					break;
				case Use_::class:
					//type

					$this->exec2($node->uses);
					break;
				case UseUse::class:
					var_dump($node);
					return "";
					break;
			}
	}
}

$code = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "unphar.txt");

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$ast = [];
try {
	$ast = $parser->parse($code);
} catch (Error $error) {
	echo "Parse error: ".$error->getMessage()."\n";
	return;
}

//var_dump($ast);
/*
$dumper = new NodeDumper;
echo $dumper->dump($ast) . "\n";
*/

$main = new main();
foreach($ast as $stmt){
	$main->exec1($stmt);
	//var_dump($stmt);
}

