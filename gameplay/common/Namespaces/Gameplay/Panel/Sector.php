<?php

namespace Gameplay\Panel;

use Gameplay\Model\SectorEntity;
use Gameplay\Model\ShipPosition;
use Gameplay\Model\SystemProperties;
use Interfaces\Singleton;

use \TranslateController as Translate;

class Sector extends Renderable implements Singleton {
	protected $panelTag = "Sector";

	static private $instance = null;
	
	/**
	 * @throws \Exception
	 * @return Sector
	 */
	static public function getInstance() {
	
		if (empty(self::$instance)) {
			throw new \Exception('Panel not initialized');
		}
		else {
			return self::$instance;
		}
	
	}
	
	static public function initiateInstance($language = 'pl', $localUserID = null) {
		self::$instance = new self($language, $localUserID);
	}
	
	/**
	 * Render
	 *
	 * @param SectorEntity $sectorProperties
	 * @param SystemProperties $systemProperties
	 * @param ShipPosition $shipPosition
	 * @return bool
	 */
	public function render(SectorEntity $sectorProperties, SystemProperties $systemProperties, ShipPosition $shipPosition = null) {

		global $config;

		$this->rendered = true;
		$this->retVal = "";

		$this->retVal .= "<div class='sectorImageCell'>";
		$this->retVal .= "<label>" . Translate::getDefault()->get ( $sectorProperties->Name ) . "</label>";
		$this->retVal .= "<img src='{$config['general']['cdn']}{$sectorProperties->Image}' />";
		$this->retVal .= "</div>";
		$this->retVal .= "<div class='sectorInfoCell'>";
		$this->retVal .= "<div class='galaxyName'>" . Translate::getDefault()->get ( 'galaxy' ) . ": " . $systemProperties->Galaxy . "</div>";
		$this->retVal .= "<div class='systemName'>System: " . $systemProperties->Name . " [" . $systemProperties->Number . "]</div>";
		if ($shipPosition != null) {
			$this->retVal .= "<div class='systemPosition'>[X/Y]: " . $shipPosition->X . "/" . $shipPosition->Y . "</div>";
		}
		$this->retVal .= "<div class=\"sectorOther\">{T:movecost}: " . $sectorProperties->MoveCost . "</div>";
		$this->retVal .= "<div class=\"sectorOther\">{T:visibility}: " . $sectorProperties->Visibility . "%</div>";
		$this->retVal .= "<div class=\"sectorOther\">{T:accuracy}: " . $sectorProperties->Accuracy . "%</div>";
		$this->retVal .= "</div>";

		return true;
	}
}