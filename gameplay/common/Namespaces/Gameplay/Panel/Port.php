<?php

namespace Gameplay\Panel;

use \TranslateController as Translate;

class Port extends Base {
	protected $panelTag = "Port";
	protected $onEmpty = "clearIfRendered"; //Jak ma się zachować panel gdy jego zawartość jest pusta: none / hide / clear

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
	 * Wyrenderowanie panelu
	 *
	 * @param stdClass $shipPosition
	 * @param stdClass $portProperties
	 * @param stdClass $shipProperties
	 * @param stdClass $jumpNode
	 * @return boolean
	 */
	public function render($shipPosition, $portProperties, $shipProperties, $jumpNode) {
		global $config;
		$this->rendered = true;
		$this->retVal = "";
		if ($portProperties->PortID != null) {
				
			$this->retVal .= "<div class='sectorImageCell'>";
			$this->retVal .= "<label>" . $portProperties->PortTypeName . "</label>";
			$this->retVal .= "<img src='{$config['general']['cdn']}{$portProperties->Image}' />";
			$this->retVal .= "</div>";
			$this->retVal .= "<div class='sectorInfoCell'>";
			$this->retVal .= "<div class='portName'>" . $portProperties->Name . "</div>";
			$this->retVal .= "<div style='padding-left: 1em; color: #c0c000;'>" . Translate::getDefault()->get ( 'level' ) . " " . \portProperties::computeLevel ( $portProperties->Experience ) . "</div>";
				
			$this->retVal .= "<div style=\"text-align: center; margin-top: 30px;\">";
			//Info o stanie Portu
			if ($shipPosition->Docked == "no") {
				$this->retVal .= "<button class='btn' onclick=\"Playpulsar.gameplay.execute('shipDock',null,null,null,null);\"><i class='icon-white icon-resize-small'></i> " . Translate::getDefault()->get ( 'dock' ) . "</button>";
			} elseif ($shipPosition->Docked == "yes") {
				$this->retVal .= "<button class='btn' onclick=\"Playpulsar.gameplay.execute('shipUnDock',null,null,null,null);\"><i class='icon-white icon-resize-full'></i> " . Translate::getDefault()->get ( 'undock' ) . "</button>";
			}
			//Sprawdzenie, czy można atakować port
			if (!empty($config['raiding']['enabled']) && $shipProperties->RookieTurns == 0 && $portProperties->State != "raided" && $shipPosition->Docked == "no") {
				$this->retVal .= "<button class='btn btn-danger' onclick=\"Playpulsar.gameplay.execute('portRaid',null,null,null,null);\"><i class='icon-white icon-fire'></i> " . Translate::getDefault()->get ( 'raid' ) . "</button>";
			}
			$this->retVal .= "</div>";
			$this->retVal .= "</div>";
		}

		//Sparwdz, czy w sektorze jest JUMP NODE
		if ($jumpNode != null) {
				
			$jumpNodeObject = new \jumpNode ( );
			$jumpNode = $jumpNodeObject->load ( $shipPosition, true, true );
				
			$destination = $jumpNodeObject->getDestination ( $shipPosition );
				
			unset($jumpNodeObject);
				
			$destination = \systemProperties::getGalaxy ( $destination->System ) . "/" . $destination->System . "/" . $destination->X . "/" . $destination->Y;
				
			$this->retVal .= "<div class='sectorImageCell'>";
			$this->retVal .= "<label>" . Translate::getDefault()->get ( 'jumpgate' ) . "</label>";
			$this->retVal .= "<img src='{$config['general']['cdn']}gfx/node.png' />";
			$this->retVal .= "</div>";
			$this->retVal .= "<div class='sectorInfoCell'>";
			$this->retVal .= "<div class=\"portOther\"><span style=\"color: #00f000; font-size: 10pt; font-weight: bold;\">" . Translate::getDefault()->get ( 'destination' ) . ": " . $destination . "</span></div>";
				
			$this->retVal .= "<div style=\"text-align: center; margin-top: 30px;\">";
				
			if ($shipPosition->Docked == "no") {
				$this->retVal .= "<button class='btn' onclick=\"Playpulsar.gameplay.execute('shipNodeJump',null,null,null,null);\"><i class='icon-white icon-asterisk'></i> " . Translate::getDefault()->get ( 'jump' ) . "</button>";
			}
				
			$this->retVal .= "</div>";
			$this->retVal .= "</div>";
		}
		$this->rendered = true;
		return true;
	}
}