<?php
namespace SVApp\Entities;
use SVApp\Classes\Entity;

/**
 * SVApp\Entities\Ticker
 *
 * @Entity
 * @Table(name="tickers")
 */
class Ticker extends Entity {
	/**
	 * @Id
	 * @Column(type="string", name="code", length=25)
	 */
	protected $Code;

	/** @Column(type="string", name="description", length=255) */
	protected $Description;

	public function getID() {
		return $this->getCode();
	}

	/**
	 * @return string
	 */
	public function getCode() {
		return $this->Code;
	}

	/**
	 * @param string $Code
	 * @return Ticker
	 */
	public function setCode($Code) {
		$this->Code = $Code;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->Description;
	}

	/**
	 * @param string $Description
	 * @return Ticker
	 */
	public function setDescription($Description) {
		$this->Description = $Description;

		return $this;
	}


}

