<?php

/**
 * Skrócone statystyki statku
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class shortShipStatsPanel extends basePanel {
	protected $panelTag = "shortShipStatsPanel";
	
	/**
	 * Renderowanie
	 *
	 * @param shipProperties $shipProperties
	 */
	public function render($shipProperties) {
		
		$this->rendered = true;
		$this->retVal = "";
		
		$this->retVal .= "<div " . getParameterColor ( $shipProperties->Shield, $shipProperties->ShieldMax ) . ">";
		$this->retVal .= TranslateController::getDefault()->get ( 'shield' ) . ": " . $shipProperties->Shield . "/" . $shipProperties->ShieldMax;
		$this->retVal .= "</div>";
		$this->retVal .= "<div " . getParameterColor ( $shipProperties->Armor, $shipProperties->ArmorMax ) . ">";
		$this->retVal .= TranslateController::getDefault()->get ( 'armor' ) . ": " . $shipProperties->Armor . "/" . $shipProperties->ArmorMax;
		$this->retVal .= "</div>";
		$this->retVal .= "<div " . getParameterColor ( $shipProperties->Power, $shipProperties->PowerMax ) . ">";
		$this->retVal .= TranslateController::getDefault()->get ( 'power' ) . ": " . $shipProperties->Power . "/" . $shipProperties->PowerMax;
		$this->retVal .= "</div>";
		$this->retVal .= "<div " . getParameterColor ( $shipProperties->Turns, $shipProperties->Turns ) . ">";
		$this->retVal .= TranslateController::getDefault()->get ( 'turns' ) . ": " . $shipProperties->Turns;
		$this->retVal .= "</div>";
		$this->retVal .= "<div " . getParameterColor ( $shipProperties->EmpMax - $shipProperties->Emp, $shipProperties->EmpMax ) . ">";
		$this->retVal .= TranslateController::getDefault()->get ( 'EMP' ) . ": " . $shipProperties->Emp . '/' . $shipProperties->EmpMax;
		$this->retVal .= "</div>";
		
		if ($shipProperties->RookieTurns > 0) {
			$this->retVal .= "<div>";
			$this->retVal .= TranslateController::getDefault()->get ( 'RookieTurns' ) . ": " . $shipProperties->RookieTurns;
			$this->retVal .= "</div>";
		}
	}

}
