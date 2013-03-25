<?php
/**
 * Panel ładowni
 * 
 * @version $Rev: 460 $
 * @package Engine
 *
 */
class cargoPanel extends basePanel {
	protected $panelTag = "cargoPanel";
	
	/**
	 * Renderowanie panelu ładowni statku
	 *
	 * @param int $userID 
	 * @param stdClass $shipProperties
	 * @return boolean
	 */
	public function render($shipProperties) {
		global $shipCargo;
		
		$this->retVal .= "<h1>";
		$this->retVal .= TranslateController::getDefault()->get ( 'cargo' );
		$this->retVal .= "</h1>";
		
		$this->retVal .= "<div style=\"text-align: center;\">" . $shipProperties->Cargo . "/" . $shipProperties->CargoMax . "</div>";
		
		$tQuery = $shipCargo->getProducts ();
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$this->retVal .= "<div class=\"shortPanelListGreen\" style=\"cursor: default;\">";
			$this->retVal .= $tR1->Name . " [" . $tR1->Amount . "]";
			$this->retVal .= "</div>";
		}
		
		$tQuery = $shipCargo->getItems ();
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$this->retVal .= "<div class=\"shortPanelListYellow\" style=\"cursor: default;\">";
			$this->retVal .= $tR1->Name . " [" . $tR1->Amount . "]";
			$this->retVal .= "</div>";
		}
		
		//Uzbrojenie
		$tQuery = $shipCargo->getWeapons ();
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$this->retVal .= "<div class=\"shortPanelListRed\" style=\"cursor: default;\">";
			$this->retVal .= $tR1->Name . " [" . $tR1->Amount . "]";
			$this->retVal .= "</div>";
		}
		
		//Equipment
		$tQuery = $shipCargo->getEquipments ();
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$this->retVal .= "<div class=\"shortPanelList\" style=\"cursor: default;\">";
			$this->retVal .= $tR1->Name . " [" . $tR1->Amount . "]";
			$this->retVal .= "</div>";
		}
		
		return true;
	}

}
?>