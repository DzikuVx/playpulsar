<?php

namespace Gameplay\Panel;

use Interfaces\Singleton;

use \TranslateController as Translate;

class Sector extends Renderable implements Singleton {
	protected $panelTag = "Sector";

	static private $instance = null;
	
	/**
	 * @throws \Exception
	 * @return \Gameplay\Panel\ShortStats
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
	 * @param stdClass $sectorProperties
	 * @param stdClass $systemProperties
	 * @param stdClass $shipPosition
	 * @return boolean
	 */
	public function render($sectorProperties, $systemProperties, $shipPosition = null) {

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
		$this->retVal .= "<div class=\"sectorOther\">" . Translate::getDefault()->get ( 'movecost' ) . ": " . $sectorProperties->MoveCost . "</div>";
		$this->retVal .= "<div class=\"sectorOther\">" . Translate::getDefault()->get ( 'visibility' ) . ": " . $sectorProperties->Visibility . "%</div>";
		$this->retVal .= "<div class=\"sectorOther\">" . Translate::getDefault()->get ( 'accuracy' ) . ": " . $sectorProperties->Accuracy . "%</div>";
		$this->retVal .= "</div>";

		return true;
	}
}