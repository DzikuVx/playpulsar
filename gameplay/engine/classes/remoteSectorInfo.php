<?php
/**
 * Informacje o zdalnym sektorze
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class remoteSectorInfo extends sectorPanel {
	protected $panelTag = "remoteSectorInfo";
	protected $renderCloser = true;

	public function render($sectorProperties, $systemProperties, $shipPosition = null) {

		global $userProperties;

		parent::render ( $sectorProperties, $systemProperties, $shipPosition );

		$item = new portProperties ( );
		$portProperties = $item->load ( $shipPosition, true, true );
		unset($item);

		$jumpNodeObject = new jumpNode ( );
		$jumpNode = $jumpNodeObject->load ( $shipPosition, true, true );

		unset($jumpNodeObject);

		if ($portProperties->PortID != null || ! empty ( $jumpNode )) {

			$shipProperties->RookieTurns = 1;

			$item = new portInfoPanel ( );
			$item->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );
			$this->retVal .= $item->getRetVal ();

			if (!empty($portProperties->PortID)) {
					
				//Sprzedaż portu
				$item = new portCargo ( 0, $portProperties, $userProperties->Language );
				$tQuery = $item->getProductsSell ();

				$this->retVal .= "<h2 style=\"text-align: center;\">" . TranslateController::getDefault()->get ( 'sell' ) . "</h2>";

				$tString = "";
				while ( $tRow = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
					$tString .= ", " . $tRow->Name;
				}

				$this->retVal .= mb_substr ( $tString, 2 );

				$tQuery = $item->getProductsBuy ();

				$this->retVal .= "<h2 style=\"text-align: center;\">" . TranslateController::getDefault()->get ( 'buy' ) . "</h2>";

				$tString = "";
				while ( $tRow = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
					$tString .= ", " . $tRow->Name;
				}

				$this->retVal .= mb_substr ( $tString, 2 );
			}
		}

		if ($portProperties->PortID != null  && $portProperties->Type == 'station') {
			/*
			 * Lista broni
			 */
			$this->retVal .= "<h2 style='text-align: center;'>" . TranslateController::getDefault()->get ( 'weapons' ) . "</h2>";
			$tArray = explode(',', $portProperties->Weapons);

			$tString = '';
			foreach ($tArray as $tItem) {
				$tData = weapon::quickLoad($tItem);
				if ($userProperties->Language == 'pl') {
					$tString .= ', '.$tData->NamePL;
				}else {
					$tString .= ', '.$tData->NameEN;
				}
			}
			$this->retVal .= mb_substr ( $tString, 2 );

			/*
			 * Lista wyposażenia
			 */
			$this->retVal .= "<h2 style='text-align: center;'>" . TranslateController::getDefault()->get ( 'equipment' ) . "</h2>";
			$tArray = explode(',', $portProperties->Equipment);

			$tString = '';
			foreach ($tArray as $tItem) {
				$tData = equipment::quickLoad($tItem);
				if ($userProperties->Language == 'pl') {
					$tString .= ', '.$tData->NamePL;
				}else {
					$tString .= ', '.$tData->NameEN;
				}
			}
			$this->retVal .= mb_substr ( $tString, 2 );
				
			/*
			 * Lista okrętów
			 */
			$this->retVal .= "<h2 style='text-align: center;'>" . TranslateController::getDefault()->get ( 'ships' ) . "</h2>";
			$tArray = explode(',', $portProperties->Ships);

			$tString = '';
			foreach ($tArray as $tItem) {
				$tData = ship::quickLoad($tItem);
				if ($userProperties->Language == 'pl') {
					$tString .= ', '.$tData->NamePL;
				}else {
					$tString .= ', '.$tData->NameEN;
				}
			}
			$this->retVal .= mb_substr ( $tString, 2 );
		}

		$this->retVal .= "<div style='margin-top: 4px; text-align: center;'>";
		$this->retVal .= "<input onclick=\"systemMap.plot('{$shipPosition->System}', '{$shipPosition->X}', '{$shipPosition->Y}')\" type=\"button\" class=\"smallButton\" value=\"" . TranslateController::getDefault()->get ( 'setNavPoint' ) . "\" />";
		$this->retVal .= "</div>";

		return true;
	}
}