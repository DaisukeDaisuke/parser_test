<?php

namespace purser;

class CodeBlock{
	/** @var int[] $ids */
	public $ids = [];
	//public $values = [];
	//public $nowvaluesid = 0;

	/** @var int $block */
	public $block;

	public function __construct(int $blockid){
		$this->block = $blockid;
	}

	public function getBlock(): int{
		return $this->block;
	}

	//return id
	function get(string $value,int &$ifcount,bool $force = false): int{
		if($force||!isset($this->ids[$value])){
			$this->ids[$value] = $ifcount;//!!
		}
		return $this->ids[$value];//debug
	}

	/*function add($value){//変数名
		if(isset($this->values[$this->nowvaluesid + 1])){
			$this->values[++$this->nowvaluesid] = [];
		}
		$this->values[$this->nowvaluesid] = $value;
		$this->ids[$value] = $this->nowvaluesid;
		return $this->nowvaluesid;
	}*/
}