<?php

namespace purser;

class scopenode{
	/** @var ?int $parent */
	public $parent = null;
	/** @var int $id */
	public $id = 0;
	/** @var int $use */
	public $use = 0;

	/**
	 * @param int|null $parent
	 * @param int $id
	 */
	public function __construct(?int $parent, int $id){
		$this->parent = $parent;
		$this->id = $id;
	}

	/**
	 * @return int|null
	 */
	public function getParent(): ?int{
		return $this->parent;
	}

	/**
	 * @return int
	 */
	public function getId(): int{
		return $this->id;
	}

	public function onUse(): void{
		$this->use++;
	}

	/**
	 * @return int
	 */
	public function getUse(): int{
		return $this->use;
	}

	public function isUsed(): bool{
		return $this->getUse() !== 0;
	}

}