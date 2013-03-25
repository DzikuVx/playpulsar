<?php

/**
 * Panel ruchu statku
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class shipMovePanel extends basePanel {
	protected $panelTag = "movePanel";

	/**
	 * Komórka ruchu statku
	 *
	 * @param stdClass $systemProperties
	 * @param stdClass $shipPosition
	 * @return boolean;
	 */
	public function render($systemProperties, $shipPosition, $portProperties, $shipRouting, $shipProperties) {

		global $icons;

		$this->rendered = true;
		$this->retVal .= "<center>";
		$this->retVal .= "<table class='moveCell' border='0' cellspacing='0' cellpadding='2'>";
		if ($shipPosition->Docked == "no") {
			//Komórka ruchu
			$this->retVal .= "<tr>";

			if (!empty($portProperties->PortID)) {
				$this->retVal .= "<td><img src='{$icons['Dock']}' title='".TranslateController::getDefault()->get ( 'dock' )."' class='imgMove' onclick=\"executeAction('shipDock','up',null,null,null);\" /></td>";
			}else {
				$this->retVal .= "<td>&nbsp;</td>";
			}

			if ($shipPosition->Y > 1) {
				$this->retVal .= "<td><img src=\"{$icons['MoveUp']}\" class=\"imgMove\" onclick=\"executeAction('shipMove','up',null,null,null);\" /></td>";
			} else {
				$this->retVal .= "<td>&nbsp;</td>";
			}

			if ($shipRouting->System != null) {
				$this->retVal .= "<td>".\General\Controls::renderImgButton('follow', "executeAction('nextWaypoint',null,null,null,null)", TranslateController::getDefault()->get ( 'Next waypoint' ))."</td>";
			}else {
				$this->retVal .= "<td>&nbsp;</td>";
			}

			$this->retVal .= "</tr>";

			$this->retVal .= "<tr>";
			if ($shipPosition->X > 1) {
				$this->retVal .= "<td><img src=\"{$icons['MoveLeft']}\" class=\"imgMove\" onclick=\"executeAction('shipMove','left',null,null,null);\" /></td>";
			} else {
				$this->retVal .= "<td>&nbsp;</td>";
			}
			$this->retVal .= "<td><img src=\"{$icons['Refresh']}\" class=\"imgMove\" onclick=\"executeAction('shipRefresh',null,null,null,null);\" /></td>";
			if ($shipPosition->X < $systemProperties->Width) {
				$this->retVal .= "<td><img src=\"{$icons['MoveRight']}\" class=\"imgMove\" onclick=\"executeAction('shipMove','right',null,null,null);\" /></td>";
			} else {
				$this->retVal .= "<td>&nbsp;</td>";
			}
			$this->retVal .= "</tr>";

			$this->retVal .= "<tr>";
				
			if (!empty($shipProperties->CanActiveScan)) {
				$this->retVal .= "<td>".\General\Controls::renderImgButton('activeScan', "executeAction('engageActiveScanner',null,null,null,null);", TranslateController::getDefault()->get ( 'Engage Active Scanner' ))."</td>";
			}else {
				$this->retVal .= "<td>&nbsp;</td>";
			}
				
			if ($shipPosition->Y < $systemProperties->Height) {
				$this->retVal .= "<td><img src=\"{$icons['MoveDown']}\" class=\"imgMove\" onclick=\"executeAction('shipMove','down',null,null,null);\" /></td>";
			} else {
				$this->retVal .= "<td>&nbsp;</td>";
			}
			if (!empty($shipRouting->System) && !empty($shipProperties->CanWarpJump)) {
				$this->retVal .= "<td>".\General\Controls::renderImgButton('warpjump', "executeAction('engageFtl',null,null,null,null)", TranslateController::getDefault()->get ( 'Engage FTL Jump Drive' ))."</td>";
			}else {
				$this->retVal .= "<td>&nbsp;</td>";
			}
			$this->retVal .= "</tr>";
		} else {
			//Komórka bezruchu :)
			$this->retVal .= "<tr>";
			$this->retVal .= "<td><img src='{$icons['Undock']}' title='".TranslateController::getDefault()->get ( 'undock' )."' class='imgMove' onclick=\"executeAction('shipUnDock',null,null,null,null);\" /></td>";
			$this->retVal .= "<td>&nbsp;</td>";
			$this->retVal .= "<td>&nbsp;</td>";
			$this->retVal .= "</tr>";
			$this->retVal .= "<tr>";
			$this->retVal .= "<td>&nbsp;</td>";
			$this->retVal .= "<td><img src=\"{$icons['Refresh']}\" class=\"imgMove\" onclick=\"executeAction('shipRefresh',null,null,null,null);\" /></td>";
			$this->retVal .= "<td>&nbsp;</td>";
			$this->retVal .= "</tr>";
			$this->retVal .= "<tr>";
			$this->retVal .= "<td>&nbsp;</td>";
			$this->retVal .= "<td>&nbsp;</td>";
			$this->retVal .= "<td>&nbsp;</td>";
			$this->retVal .= "</tr>";

		}

		$this->retVal .= "</table>";
		$this->retVal .= "</center>";
		return true;
	}

}