<?php
/**
 * Informacje o zdalnym sektorze
 *
 * @version $Rev: 460 $
 * @package Engine
 */
use General\Controls;

class remoteSectorInfo extends \Gameplay\Panel\Sector {
	protected $panelTag = "remoteSectorInfo";
	protected $renderCloser = true;

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

	/**
	 *
	 * @param string $language
	 * @param int $localUserID
	 */
	static public function initiateInstance($language = 'pl', $localUserID = null) {
		self::$instance = new self($language, $localUserID);
	}

    /**
     * @param stdClass $sectorProperties
     * @param \Gameplay\Model\SystemProperties $systemProperties
     * @param \Gameplay\Model\ShipPosition $shipPosition
     * @return $this|bool
     */
    public function render($sectorProperties, \Gameplay\Model\SystemProperties $systemProperties, \Gameplay\Model\ShipPosition $shipPosition = null) {

		global $userProperties;

		parent::render ( $sectorProperties, $systemProperties, $shipPosition );

		$this->retVal = '<button onclick="$(this).parent().hide();" class="close" title="{T:close}"><i class="icon-white icon-remove"></i></button>'.$this->retVal;

		$this->retVal .= '<div style="clear: both;"></div>';

        $portProperties = new \Gameplay\Model\PortEntity($shipPosition);

		$jumpNodeObject = new jumpNode ( );
		$jumpNode = $jumpNodeObject->load ( $shipPosition, true, true );

		unset($jumpNodeObject);

		if ($portProperties->PortID != null || ! empty ( $jumpNode )) {

			$shipProperties = new \Gameplay\Model\ShipProperties();
			$shipProperties->RookieTurns = 1;

			\Gameplay\Panel\Port::initiateInstance($userProperties->Language);
			$item = \Gameplay\Panel\Port::getInstance();
			$item->render ( $shipPosition, $portProperties, $shipProperties, $jumpNode );
			$this->retVal .= $item->getRetVal ();
			$this->retVal .= '<div style="clear: both;"></div>';
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
				while ( $tRow = \Database\Controller::getInstance()->fetch($tQuery)) {
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
		$this->retVal .= Controls::bootstrapButton('{T:setNavPoint}', "Playpulsar.gameplay.plot('{$shipPosition->System}', '{$shipPosition->X}', '{$shipPosition->Y}')",'btn-mini','icon-forward');
		$this->retVal .= "</div>";

		return $this;
	}
}