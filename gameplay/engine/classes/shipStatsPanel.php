<?php
/**
 * Panel właściwości statku
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class shipStatsPanel extends basePanel {
	
	protected $panelTag = "shipStatsPanel";
	
	private static $instance = null;
	
	/**
	 * Konstruktor statyczny
	 * @return shipStatsPanel
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			$className = __CLASS__;
	
			global $userProperties;
	
			self::$instance = new $className($userProperties->Language);
		}
		return self::$instance;
	}
	
	public function render() {
		global $shipProperties;
		
		$this->rendered = true;
		$this->retVal = "";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'ship' ) . ": " . $shipProperties->ShipTypeName;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'weapons' ) . ": " . $shipProperties->CurrentWeapons . "/" . $shipProperties->MaxWeapons;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'equipment' ) . ": " . $shipProperties->CurrentEquipment . "/" . $shipProperties->MaxEquipment;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'Off.Rating' ) . ": " . $shipProperties->OffRating;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'Def.Rating' ) . ": " . $shipProperties->DefRating;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'Speed' ) . ": " . $shipProperties->Speed;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'Maneuver' ) . ": " . $shipProperties->Maneuver;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'Gather' ) . ": " . $shipProperties->Gather;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'Scanning' ) . ": " . $shipProperties->Scan;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'Cloak' ) . ": " . $shipProperties->Cloak;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'Targetting' ) . ": " . $shipProperties->Targetting;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'ArmorStrength' ) . ": " . $shipProperties->ArmorStrength;
		$this->retVal .= "</div>";
		$this->retVal .= "<div>";
		$this->retVal .= TranslateController::getDefault()->get ( 'ArmorPiercing' ) . ": " . $shipProperties->ArmorPiercing;
		$this->retVal .= "</div>";
	}

}

?>