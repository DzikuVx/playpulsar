<?php
/**
 * Klasa panelu informacji o porcie i Jump Node
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class portInfoPanel extends basePanel {
	protected $panelTag = "portInfoPanel";
	protected $onEmpty = "clearIfRendered"; //Jak ma się zachować panel gdy jego zawartość jest pusta: none / hide / clear
	

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
			
			$this->retVal .= "<table border=\"0\" width=\"95%\">";
			$this->retVal .= "<tr>";
			$this->retVal .= "<td class=\"sectorImageCell\">";
			/*
			if (file_exists ( "../../" . $portProperties->Image )) {
				$this->retVal .= "<img src=\"{$portProperties->Image}\" class=\"sectorImage\" />";
			}
			if (file_exists ( "../" . $portProperties->Image )) {
				$this->retVal .= "<img src=\"{$portProperties->Image}\" class=\"sectorImage\" />";
			}
			*/
			$this->retVal .= "<img src='{$config['general']['cdn']}{$portProperties->Image}' class='sectorImage' />";

			$this->retVal .= "</td>";
			$this->retVal .= "<td class=\"sectorInfoCell\">";
			$this->retVal .= "<div class=\"portName\">" . $portProperties->Name . "</div>";
			$this->retVal .= "<div class=\"portOther\"><span>" . $portProperties->PortTypeName . "</span><span style='padding-left: 12px; color: #c0c000;'>" . TranslateController::getDefault()->get ( 'level' ) . ": " . portProperties::computeLevel ( $portProperties->Experience ) . "</span></div>";
			
			$this->retVal .= "<div style=\"text-align: center; margin-top: 30px;\">";
			//Info o stanie Portu
			if ($shipPosition->Docked == "no") {
				$this->retVal .= "<span class=\"actionButton\" onclick=\"Playpulsar.gameplay.execute('shipDock',null,null,null,null);\">" . TranslateController::getDefault()->get ( 'dock' ) . "</span>";
			} elseif ($shipPosition->Docked == "yes") {
				$this->retVal .= "<span class=\"actionButton\" onclick=\"Playpulsar.gameplay.execute('shipUnDock',null,null,null,null);\">" . TranslateController::getDefault()->get ( 'undock' ) . "</span>";
			}
			//Sprawdzenie, czy można atakować port
			if (!empty($config['raiding']['enabled']) && $shipProperties->RookieTurns == 0 && $portProperties->State != "raided" && $shipPosition->Docked == "no") {
				$this->retVal .= "<span class=\"actionButton\" onclick=\"Playpulsar.gameplay.execute('portRaid',null,null,null,null);\">" . TranslateController::getDefault()->get ( 'raid' ) . "</span>";
			}
			$this->retVal .= "</div>";
			$this->retVal .= "</td>";
			$this->retVal .= "</tr>";
			$this->retVal .= "</table>";
		}
		
		//Sparwdz, czy w sektorze jest JUMP NODE
		if ($jumpNode != null) {
			
			$jumpNodeObject = new jumpNode ( );
			$jumpNode = $jumpNodeObject->load ( $shipPosition, true, true );
			
			$destination = $jumpNodeObject->getDestination ( $shipPosition );
			
			unset($jumpNodeObject);
			
			$destination = systemProperties::getGalaxy ( $destination->System ) . "/" . $destination->System . "/" . $destination->X . "/" . $destination->Y;
			
			$this->retVal .= "<table border=\"0\" width=\"95%\">";
			$this->retVal .= "<tr>";
			$this->retVal .= "<td class=\"sectorImageCell\">";
			$this->retVal .= "<img src='{$config['general']['cdn']}gfx/node.png' class=\"sectorImage\" />";
			$this->retVal .= "</td>";
			$this->retVal .= "<td class=\"sectorInfoCell\">";
			$this->retVal .= "<div class=\"portName\">" . TranslateController::getDefault()->get ( 'jumpgate' ) . "</div>";
			$this->retVal .= "<div class=\"portOther\"><span style=\"color: #00f000; font-size: 10pt; font-weight: bold;\">" . TranslateController::getDefault()->get ( 'destination' ) . ": " . $destination . "</span></div>";
			
			$this->retVal .= "<div style=\"text-align: center; margin-top: 30px;\">";
			
			if ($shipPosition->Docked == "no") {
				$this->retVal .= "<span class=\"actionButton\" onClick=\"Playpulsar.gameplay.execute('shipNodeJump',null,null,null,null);\">[" . TranslateController::getDefault()->get ( 'jump' ) . "]</span>";
			}
			
			$this->retVal .= "</div>";
			$this->retVal .= "</td>";
			$this->retVal .= "</tr>";
			$this->retVal .= "</table>";
		}
		$this->rendered = true;
		return true;
	}
}