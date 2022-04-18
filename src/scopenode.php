<?php

namespace purser;

class scopenode{
	//クラス化
	public const TYPE_FOR_WHILE = 0;
	public const TYPE_SWITCH = 1;

	/** @var ?int $parent */
	public $parent = null;
	/** @var int $id */
	public $id = 0;
	/** @var int $use */
	public $use = 0;
	/** @var int $type */
	protected $type;

	/**
	 * @param int|null $parent
	 * @param int $id
	 * @param int $type
	 */
	public function __construct(?int $parent, int $id, int $type = self::TYPE_FOR_WHILE){
		$this->parent = $parent;
		$this->id = $id;
		$this->type = $type;
	}

	/**
	 * @return int|null
	 */
	public function getParent() : ?int{
		return $this->parent;
	}

	/**
	 * @return int
	 */
	public function getId() : int{
		return $this->id;
	}

	public function onUse() : void{
		$this->use++;
	}

	/**
	 * @return int
	 */
	public function getUse() : int{
		return $this->use;
	}

	public function isUsed() : bool{
		return $this->getUse() !== 0;
	}

	/**
	 * @return int
	 */
	public function getType() : int{
		return $this->type;
	}
}
