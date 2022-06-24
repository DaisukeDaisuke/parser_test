<?php

namespace purser;

use PhpParser\Node\Expr;

class type_array{
    protected int $count = 0;
    protected array $type_inference = [];

    public function __construct(
        protected int $id
    ){
    }

    public function setKey(Expr $key, Expr $type){
        $this->type_inference[$key] = $type;
        $this->count++;
    }
}