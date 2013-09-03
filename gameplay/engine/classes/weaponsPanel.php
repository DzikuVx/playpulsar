<?php
/**
 * Klasa skróconego panelu uzbrojenia
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class weaponsPanel extends basePanel {
	protected $panelTag = "weaponsPanel";
	
	/**
	 * Wyrenderowanie panelu
	 *
	 * @return boolean
	 */
	public function render() {
		global $colorTable, $shipWeapons;
		$this->rendered = true;
		$this->retVal .= "<h1 onclick=\"Playpulsar.gameplay.execute('weaponsManagement',null,null,null,null);\" style='cursor: pointer;'>";
		$this->retVal .= TranslateController::getDefault()->get ( 'weapons' );
		$this->retVal .= "</h1>";
		
		$tQuery = $shipWeapons->get ( "all" );
		while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			
			if ($tR1->Enabled == '1') {
				$modifier = "style=\"color: " . $colorTable ['green'] . "\"";
				if ($tR1->Ammo == '0') {
					$modifier = "style=\"color: " . $colorTable ['red'] . "\"";
				}
			} else {
				$modifier = "style=\"color: " . $colorTable ['yellow'] . "\"";
			}
			
			if ($tR1->Damaged != '0') {
				$modifier = "style=\"color: " . $colorTable ['red'] . "\"";
			}
			
			$this->retVal .= "<div $modifier >";
			$this->retVal .= $tR1->Symbol;
			if ($tR1->Ammo != null) {
				$this->retVal .= "<span style=\"padding-left: 10px;\">[" . $tR1->Ammo . "]</span>";
			}
			$this->retVal .= "</div>";
		}
		return true;
	}

}