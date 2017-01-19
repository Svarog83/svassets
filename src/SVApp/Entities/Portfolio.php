<?php
namespace SVApp\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use SVApp\Classes\Entity;

/**
 * SVApp\Entities\Portfolio
 *
 * @Entity
 * @Table(name="portfolio")
 */
class Portfolio extends Entity{
	/** @Id @Column(type="integer", name="pID") @GeneratedValue */
	protected $pID;

	/** @Column(type="string", name="name", length=50) */
	protected $Name;

	/** @Column(type="string", name="description", length=255) */
	protected $Description;

	/** @Column(type="string", name="description2", length=255) */
	protected $Description2;

	/**
	 * @OneToMany(targetEntity="Asset", mappedBy="Portfolio", cascade={"persist"})
	 **/
	protected $Assets;

	function __construct() {
		$this->Assets = new ArrayCollection();
	}

	/**
	 * @return mixed
	 */
	public function getPID() {
		return $this->pID;
	}

	/**
	 * @param mixed $pID
	 * @return Portfolio
	 */
	public function setPID($pID) {
		$this->pID = $pID;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->Name;
	}

	/**
	 * @param mixed $Name
	 * @return Portfolio
	 */
	public function setName($Name) {
		$this->Name = $Name;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDescription() {
		return $this->Description;
	}

	/**
	 * @param mixed $Description
	 * @return Portfolio
	 */
	public function setDescription($Description) {
		$this->Description = $Description;

		return $this;
	}

	/**
	 * @return Asset[]
	 */
	public function getAssets() {
		return $this->Assets;
	}

	/**
	 * @param mixed $Assets
	 * @return Portfolio
	 */
	public function setAssets($Assets) {
		$this->Assets = $Assets;

		return $this;
	}



	public function getID() {
		return $this->getPID();
	}
}

