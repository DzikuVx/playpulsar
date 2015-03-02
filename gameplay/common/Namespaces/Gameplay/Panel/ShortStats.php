<?php

namespace Gameplay\Panel;

use Gameplay\Model\ShipEquipments;
use Gameplay\Model\ShipProperties;
use Gameplay\Model\ShipWeapons;
use Interfaces\Singleton;

use \TranslateController as Translate;

class ShortStats extends Renderable implements Singleton {

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

	protected $panelTag = "ShortStats";

	/**
	 * 
	 * Panel render
	 * @param ShipProperties $shipProperties
	 * @param ShipWeapons $shipWeapons
	 * @param ShipEquipments $shipEquipment
	 */
	public function render(ShipProperties $shipProperties, ShipWeapons $shipWeapons, ShipEquipments $shipEquipment) {

		$this->rendered = true;
 
		$this->retVal = "";
		$this->retVal .= "<h1>Ship stats</h1>";
		$this->retVal .= "<div class='row'>";

		$this->retVal .= "<div class='column25 center'>";
		$this->retVal .= "<label class='center' style='color: #00C608;'>".Translate::getDefault()->get ( 'shield' )."</label>";
		$this->retVal .= "<input data-skin='tron' class='knob' data-width='55' data-fgColor='#00C608' data-thickness='.2' data-readOnly=true data-max='100' value='".\General\Formater::sGetPercentage($shipProperties->Shield, $shipProperties->ShieldMax)."'>";
		$this->retVal .= "</div>";
		
		$this->retVal .= "<div class='column25 center'>";
		$this->retVal .= "<label class='center' style='color: #9BAAC6;'>".Translate::getDefault()->get ( 'armor' )."</label>";
		$this->retVal .= "<input data-skin='tron' class='knob' data-width='55' data-fgColor='#9BAAC6' data-thickness='.2' data-readOnly=true data-max='100' value='".\General\Formater::sGetPercentage($shipProperties->Armor, $shipProperties->ArmorMax)."'>";
		$this->retVal .= "</div>";

		$this->retVal .= "<div class='column25 center'>";
		$this->retVal .= "<label class='center' style='color: #c0c000;'>".Translate::getDefault()->get ( 'power' )."</label>";
		$this->retVal .= "<input data-skin='tron' class='knob' data-width='55' data-fgColor='#c0c000' data-thickness='.2' data-readOnly=true data-max='100' value='".\General\Formater::sGetPercentage($shipProperties->Power, $shipProperties->PowerMax)."'>";
		$this->retVal .= "</div>";

		$this->retVal .= "<div class='column25 center'>";
		$this->retVal .= "<label class='center' style='color: #FF871E;'>".Translate::getDefault()->get ( 'EMP' )."</label>";
		$this->retVal .= "<input data-skin='tron' class='knob' data-width='55' data-fgColor='#FF871E' data-thickness='.2' data-readOnly=true data-max='100' value='".(100-\General\Formater::sGetPercentage($shipProperties->Emp, $shipProperties->EmpMax))."'>";
		$this->retVal .= "</div>";

		$this->retVal .= "</div>";

		//Free/Healthy row
		$this->retVal .= "<div class='row'>";
		$this->retVal .= "<div class='column50 center strong'>".Translate::getDefault()->get ( 'Free' )."</div>";
		$this->retVal .= "<div class='column50 center strong'>".Translate::getDefault()->get ( 'Operational' )."</div>";
		$this->retVal .= "</div>";
		
		$this->retVal .= "<div class='row'>";
		$this->retVal .= "<div class='column25 center'>";
		$this->retVal .= "<label title='".Translate::getDefault()->get ( 'Free weapon slots' )."'>".Translate::getDefault()->get ( 'weapons' )."</label>";
		$this->retVal .= "<input data-skin='tron' class='knob' data-width='50' data-fgColor='#007000' data-thickness='.3' data-readOnly=true data-max='".$shipProperties->MaxWeapons."' value='".($shipProperties->MaxWeapons - $shipProperties->CurrentWeapons)."'>";
		$this->retVal .= "</div>";
		
		$this->retVal .= "<div class='column25 center'>";
		$this->retVal .= "<label title='".Translate::getDefault()->get ( 'Free equipment slots' )."'>".Translate::getDefault()->get ( 'equipment' )."</label>";
		$this->retVal .= "<input data-skin='tron' class='knob' data-width='50' data-fgColor='#007000' data-thickness='.3' data-readOnly=true data-max='".$shipProperties->MaxEquipment."' value='".($shipProperties->MaxEquipment - $shipProperties->CurrentEquipment)."'>";
		$this->retVal .= "</div>";
		
		$this->retVal .= "<div class='column25 center'>";
		$this->retVal .= "<label title='".Translate::getDefault()->get ( 'Operational weapons' )."'>".Translate::getDefault()->get ( 'weapons' )."</label>";
		$this->retVal .= "<input data-skin='tron' class='knob' data-width='50' data-fgColor='#00E1FF' data-thickness='.3' data-readOnly=true data-max='".$shipProperties->CurrentWeapons."' value='".$shipWeapons->getOperationalCount()."'>";
		$this->retVal .= "</div>";
		
		$this->retVal .= "<div class='column25 center'>";
		$this->retVal .= "<label title='".Translate::getDefault()->get ( 'Operational equipment' )."'>".Translate::getDefault()->get ( 'equipment' )."</label>";
		$this->retVal .= "<input data-skin='tron' class='knob' data-width='50' data-fgColor='#00E1FF' data-thickness='.3' data-readOnly=true data-max='".$shipProperties->CurrentEquipment."' value='".$shipEquipment->getOperationalCount()."'>";
		$this->retVal .= "</div>";
		
		$this->retVal .= "</div>";
		$this->retVal .= "</div>";

		$this->retVal .= "</div>";

		/* Buttons row */
		$this->retVal .= "<div style='margin-top: 1em;' class='btn-group'>";
		$this->retVal .= "<button class='btn btn-small' onclick=\"Playpulsar.gameplay.execute('equiapmentManagement',null,null,null,null);\" title=\"".Translate::getDefault()->get('ship')."\"><i class='icon-share-alt'></i>".Translate::getDefault()->get('ship')."</button>";
		$this->retVal .= "<button class='btn btn-small' onclick=\"Playpulsar.gameplay.execute('weaponsManagement',null,null,null,null);\" title=\"".Translate::getDefault()->get('weapons')."\"><i class='icon-share-alt'></i>".Translate::getDefault()->get('weapons')."</button>";
		$this->retVal .= "<button class='btn btn-small' onclick=\"Playpulsar.gameplay.execute('cargoManagement',null,null,null,null);\" title=\"".Translate::getDefault()->get('cargo')."\"><i class='icon-share-alt'></i>".Translate::getDefault()->get('cargo')."</button>";
		$this->retVal .= "</div>";
		
		/* Antimatter row */
		$this->retVal .= "<div style='margin-top: 1em;'>";
		
		$this->retVal .= "<div class='em12'>";
		$this->retVal .= "<strong>{T:turns}: </strong>" . $shipProperties->Turns;
		$this->retVal .= "</div>";
		
		if ($shipProperties->RookieTurns > 0) {
			$this->retVal .= "<div class='em12'>";
			$this->retVal .= "<strong>{T:RookieTurns}: </strong>" . $shipProperties->RookieTurns;
			$this->retVal .= "</div>";
		}
		
		$this->retVal .= "</div>";
		$this->retVal .= "</div>";

	}

}