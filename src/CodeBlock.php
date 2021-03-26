<?php

namespace purser;

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

	//return id
	function get(string $value,int &$ifcount){
		if(!isset($this->ids[$value])){
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