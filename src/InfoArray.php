<?php

namespace purser;

class InfoArray{
	protected $internal_id;
	/**	@var array<int|float|string|bool, int> */
	protected $keys = [];
	/**	@var int */
	protected $lastKey = 0;

	public function __construct(int $internal_id){
		$this->internal_id = $internal_id;
	}


}