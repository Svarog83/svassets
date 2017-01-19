<?php
namespace SVApp\Entities;
use SVApp\Classes\Entity;

/**
 * SVApp\Entities\Asset
 *
 * @Entity
 * @Table(name="assets")
 */
class Asset extends Entity {
	/** @Id @Column(type="integer") @GeneratedValue */
	protected $aID;

	/**
	 * @ManyToOne(targetEntity="Ticker")
	 * @JoinColumn(name="ticker", referencedColumnName="code")
	 * options={"comment" = "Ticker"})
	 */
	protected $Ticker;

	/** @Column(type="string", name="type", length=5) */
	protected $Type;

	/** @Column(type="string", name="sector", length=50) */
	protected $Sector;

	/** @Column(type="integer", name="lotSize") */
	protected $LotSize;

	/** @Column(type="integer", name="lotNum") */
	protected $LotNum;

	/** @Column(type="string", name="currency", length=3) */
	protected $Currency;

	/** @Column(type="string", name="strategy", length=50) */
	protected $Strategy;

	/**
	 * @ManyToOne(targetEntity="Portfolio", inversedBy="Assets")
	 * @JoinColumn(name="portfolioID", referencedColumnName="pID", onDelete="CASCADE")
	 * options={"comment" = "Portfolio ID"})
	 */
	protected $Portfolio;

	/**
	 * @return int
	 */
	public function getAID() {
		return $this->aID;
	}

	public function __construct() {
	}

	public function getID() {
		return $this->getAID();
	}

	/**
	 * @return Ticker
	 */
	public function getTicker() {
		return $this->Ticker;
	}

	/**
	 * @param Ticker $Ticker
	 * @return Asset
	 */
	public function setTicker($Ticker) {
		$this->Ticker = $Ticker;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->Type;
	}

	/**
	 * @param mixed $Type
	 * @return Asset
	 */
	public function setType($Type) {
		$this->Type = $Type;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSector() {
		return $this->Sector;
	}

	/**
	 * @param mixed $Sector
	 * @return Asset
	 */
	public function setSector($Sector) {
		$this->Sector = $Sector;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLotSize() {
		return $this->LotSize;
	}

	/**
	 * @param mixed $LotSize
	 * @return Asset
	 */
	public function setLotSize($LotSize) {
		$this->LotSize = $LotSize;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLotNum() {
		return $this->LotNum;
	}

	/**
	 * @param mixed $LotNum
	 * @return Asset
	 */
	public function setLotNum($LotNum) {
		$this->LotNum = $LotNum;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCurrency() {
		return $this->Currency;
	}

	/**
	 * @param mixed $Currency
	 * @return Asset
	 */
	public function setCurrency($Currency) {
		$this->Currency = $Currency;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getStrategy() {
		return $this->Strategy;
	}

	/**
	 * @param mixed $Strategy
	 * @return Asset
	 */
	public function setStrategy($Strategy) {
		$this->Strategy = $Strategy;

		return $this;
	}

	/**
	 * @return Portfolio
	 */
	public function getPortfolio() {
		return $this->Portfolio;
	}

	/**
	 * @param Portfolio $Portfolio
	 * @return Asset
	 */
	public function setPortfolio($Portfolio) {
		$this->Portfolio = $Portfolio;

		return $this;
	}


}

