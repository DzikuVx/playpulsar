<?php

namespace Gameplay\Panel;

use Gameplay\Model\ShipPosition;
use Gameplay\Model\ShipProperties;
use Interfaces\Singleton;

use TranslateController as Translate;

class SectorResources extends Renderable implements Singleton {

	protected $onEmpty = "clearAndHide";
	protected $panelTag = "SectorResources";

    /**
     * @var SectorResources
     */
    private static $instance = null;

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
     * @param ShipPosition $shipPosition
     * @param ShipProperties $shipProperties
     * @param \stdClass $sectorProperties
     * @return bool
     */
    function render(ShipPosition $shipPosition, ShipProperties $shipProperties, $sectorProperties) {

		global $itemPickCost;

		if ($shipPosition->Docked == "yes") {
			$this->hide ();
			return false;
		}

		$this->rendered = true;

		$sectorCargo = new \sectorCargo($shipPosition);

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


			$tContent .= "<div class='well resource'>";

			$toGather = floor ( ($shipProperties->CargoMax - $shipProperties->Cargo) / $tR1->Size );
			if ($toGather > $tR1->Amount)
				$toGather = $tR1->Amount;
			$canGather = $shipProperties->Turns * $shipProperties->Gather;
			if ($toGather > $canGather)
				$toGather = $canGather;
			if ($canGather > 0 && $toGather > 0) {
				$turnsRequired = ceil ( $toGather / $shipProperties->Gather );
				$tContent .= \General\Controls::bootstrapIconButton( "{T:gather} ({$turnsRequired}am)", "Playpulsar.gameplay.execute('gather','product',null,'{$tR1->CargoID}',null);", 'btn-mini pull-right', 'icon-download icon-white' );
			}
			$tContent .= '<label class="green">'.$tR1->{$nameField}.' ('.$tR1->Amount.')</label>';

			$tContent .= "</div>";
		}

		$tWeapons = $sectorCargo->getList('weapon');
		foreach($tWeapons as $tR1) {
			if ($tR1->Amount < 1) {
				continue;
			}

			$tContent .= "<div class='well resource'>";
			if ($shipProperties->Cargo < $shipProperties->CargoMax && $shipProperties->Turns >= $itemPickCost) {
				$tContent .= \General\Controls::bootstrapIconButton( "{T:pick1} ({$itemPickCost}am)", "Playpulsar.gameplay.execute('gather','weapon',null,'{$tR1->CargoID}',null);", 'btn-mini pull-right', 'icon-download icon-white' );
			}
			$tContent .= '<label class="red">'.$tR1->{$nameField}.' ('.$tR1->Amount.')</label>';
			$tContent .= "</div>";

		}

		$tEquipments = $sectorCargo->getList('equipment');
		foreach ($tEquipments as $tR1) {
			if ($tR1->Amount < 1) {
				continue;
			}

			$tContent .= "<div class='well resource'>";
			if ($shipProperties->Cargo < $shipProperties->CargoMax && $shipProperties->Turns >= $itemPickCost) {
				$tContent .= \General\Controls::bootstrapIconButton( "{T:pick1} ({$itemPickCost}am)", "Playpulsar.gameplay.execute('gather','equipment',null,'{$tR1->CargoID}',null);", 'btn-mini pull-right', 'icon-download icon-white' );
			}
			$tContent .= '<label>'.$tR1->{$nameField}.' ('.$tR1->Amount.')</label>';
			$tContent .= "</div>";

		}

		$tItems = $sectorCargo->getList('item');
		foreach ($tItems as $tR1) {
			if ($tR1->Amount < 1) {
				continue;
			}

			$tContent .= "<div class='well resource'>";
			if ($shipProperties->Cargo < $shipProperties->CargoMax && $shipProperties->Turns >= $itemPickCost) {
				$tContent .= \General\Controls::bootstrapIconButton( "{T:pick1} ({$itemPickCost}am)", "Playpulsar.gameplay.execute('gather','item',null,'{$tR1->CargoID}',null);", 'btn-mini pull-right', 'icon-download icon-white' );
			}
			$tContent .= '<label class="yellow">'.$tR1->{$nameField}.' ('.$tR1->Amount.')</label>';
			$tContent .= "</div>";
		}

		if ($tContent != '') {
			$this->retVal .= "<h2 style='clear: both;'>{T:Resources}</h2>";
			$this->retVal .= "<div>";
			$this->retVal .= $tContent;
			$this->retVal .= "</div>";
		}

		return true;
	}

}
