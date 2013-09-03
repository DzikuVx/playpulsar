<?php
/**
 * Panel zasobów sektora
 *
 * @version $Rev: 460 $
 * @package Engine
 *
 */
class sectorResourcePanel extends basePanel {

	private static $instance = null;
	
	/**
	 * Konstruktor statyczny
	 * @return sectorResourcePanel
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			$className = __CLASS__;
	
			global $userProperties;
	
			self::$instance = new $className($userProperties->Language);
		}
		return self::$instance;
	}
	
	protected $onEmpty = "hideIfRendered";
	protected $panelTag = "sectorResourcePanel";

	function render($shipPosition, $shipProperties, $sectorProperties) {

		global $itemPickCost;

		if ($shipPosition->Docked == "yes") {
			$this->hide ();
			return false;
		}

		$this->rendered = true;

		$sectorCargo = new sectorCargo($shipPosition);

		$nameField = "Name" . strtoupper($this->language);

		/*
		 * Pobierz towary
		 */
		$tProducts = $sectorCargo->getList('product');
		$tContent = '';
		foreach ($tProducts as $tR1) {
			if ($tR1->Amount < 1) {
				continue;
			}
			$tContent .= "<tr>";
			$tContent .= "<td>" . $tR1->{$nameField} . "</td>";
			$tContent .= "<td class='amount'>" . $tR1->Amount . "</td>";
			$tContent .= "<td class='opers'>";
			/*
			 * Oblicz ile jesteś w stanie zebrać
			 */
			$toGather = floor ( ($shipProperties->CargoMax - $shipProperties->Cargo) / $tR1->Size );
			if ($toGather > $tR1->Amount)
			$toGather = $tR1->Amount;
			$canGather = $shipProperties->Turns * $shipProperties->Gather;
			if ($toGather > $canGather)
			$toGather = $canGather;
			if ($canGather > 0 && $toGather > 0) {
				$turnsRequired = ceil ( $toGather / $shipProperties->Gather );
				$tContent .= \General\Controls::renderImgButton ( 'gather', "Playpulsar.gameplay.execute('gather','product',null,'{$tR1->CargoID}',null);", TranslateController::getDefault()->get ( 'gather' ) . " (" . $turnsRequired ."am)" );
			}
			$tContent .= "</td>";
			$tContent .= "</tr>";
		}
		if ($tContent != '') {
			$this->retVal .= "<h1>" . TranslateController::getDefault()->get ( 'products' ) . "</h1>";
			$this->retVal .= "<center>";
			$this->retVal .= "<table class='sectorResourceTable' cellspacing='0'>";
			$this->retVal .= $tContent;
			$this->retVal .= "</table>";
			$this->retVal .= "</center>";
		}

		/*
		 * Pobierz uzbrojenie
		 */
		$tWeapons = $sectorCargo->getList('weapon');
		$tContent = '';
		foreach($tWeapons as $tR1) {
			if ($tR1->Amount < 1) {
				continue;
			}
			$tContent .= "<tr>";
			$tContent .= "<td>" . $tR1->{$nameField} . "</td>";
			$tContent .= "<td class='amount'>" . $tR1->Amount . "</td>";
			$tContent .= "<td class='opers'>";
			if ($shipProperties->Cargo < $shipProperties->CargoMax && $shipProperties->Turns >= $itemPickCost) {
				$tContent .= \General\Controls::renderImgButton ( 'gather', "Playpulsar.gameplay.execute('gather','weapon',null,'{$tR1->CargoID}',null);", TranslateController::getDefault()->get ( 'pick1' ) . " (" . $itemPickCost ."am)" );
			}
			$tContent .= "</td>";
			$tContent .= "</tr>";
		}
		if ($tContent != '') {
			$this->retVal .= "<h1>" . TranslateController::getDefault()->get ( 'weapons' ) . "</h1>";
			$this->retVal .= "<center>";
			$this->retVal .= "<table class='sectorResourceTable' cellspacing='0'>";
			$this->retVal .= $tContent;
			$this->retVal .= "</table>";
			$this->retVal .= "</center>";
		}

		$tEquipments = $sectorCargo->getList('equipment');
		$tContent = '';
		foreach ($tEquipments as $tR1) {
			if ($tR1->Amount < 1) {
				continue;
			}
			$tContent .= "<tr>";
			$tContent .= "<td>" . $tR1->{$nameField} . "</td>";
			$tContent .= "<td class='amount'>" . $tR1->Amount . "</td>";
			$tContent .= "<td class='opers'>";
			if ($shipProperties->Cargo < $shipProperties->CargoMax && $shipProperties->Turns >= $itemPickCost) {
				$tContent .= \General\Controls::renderImgButton ( 'gather', "Playpulsar.gameplay.execute('gather','equipment',null,'{$tR1->CargoID}',null);", TranslateController::getDefault()->get ( 'pick1' ) . " (" . $itemPickCost ."am)" );
			}
			$tContent .= "</td>";
			$tContent .= "</tr>";
		}
		if ($tContent != '') {
			$this->retVal .= "<h1>" . TranslateController::getDefault()->get ( 'equipment' ) . "</h1>";
			$this->retVal .= "<center>";
			$this->retVal .= "<table class='sectorResourceTable' cellspacing='0'>";
			$this->retVal .= $tContent;
			$this->retVal .= "</table>";
			$this->retVal .= "</center>";
		}

		$tItems = $sectorCargo->getList('item');

		$tContent = '';
		foreach ($tItems as $tR1) {
			if ($tR1->Amount < 1) {
				continue;
			}
			$tContent .= "<tr>";
			$tContent .= "<td>" . $tR1->{$nameField} . "</td>";
			$tContent .= "<td class='amount'>" . $tR1->Amount . "</td>";
			$tContent .= "<td class=\"opers\">";
			if ($shipProperties->Cargo < $shipProperties->CargoMax && $shipProperties->Turns >= $itemPickCost) {
				$tContent .= \General\Controls::renderImgButton ( 'gather', "Playpulsar.gameplay.execute('gather','item',null,'{$tR1->CargoID}',null);", TranslateController::getDefault()->get ( 'pick1' ) . " (" . $itemPickCost ."am)" );
			}
			$tContent .= "</td>";
			$tContent .= "</tr>";
		}
		if ($tContent != '') {
			$this->retVal .= "<h1>" . TranslateController::getDefault()->get ( 'item' ) . "</h1>";
			$this->retVal .= "<center>";
			$this->retVal .= "<table class='sectorResourceTable' cellspacing='0'>";
			$this->retVal .= $tContent;
			$this->retVal .= "</table>";
			$this->retVal .= "</center>";
		}

		return true;
	}

}
