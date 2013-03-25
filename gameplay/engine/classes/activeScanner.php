<?php
/**
 * Skaner aktywny
 *
 * @version $Rev: 423 $
 * @package Engine
 */
class activeScanner extends systemMap {

	protected $panelTag = "activeScanner";
	protected $sectorClass = "systemMap";
	protected $onClick = "systemMap.sectorInfo";
	protected $onEmpty = "hide";

	/**
	 *
	 * @param string $language
	 */
	function __construct($language = 'pl') {
		global $userID;

		if (empty ( $localUserID )) {
			$this->userID = $userID;
		} else {
			$this->userID = $localUserID;
		}

		$this->language = $language;
	}

	/**
	 * @param shipPosition $shipPosition
	 */
	public function setShipPosition($shipPosition) {
		$this->shipPosition = $shipPosition;

		$this->system = systemProperties::quickLoad ( $this->shipPosition->System );

	}

	/**
	 * (non-PHPdoc)
	 * @see miniMap::render()
	 */
	public function render() {

		if (empty($this->shipPosition)) {
			throw new securityException();
		}

		$this->rendered = true;

		$this->retVal = "";

		$this->retVal .= $this->renderHeader ();

		$this->getLimits ();
		$this->getSectors ();
		$this->getShips ();

		$this->retVal .= $this->renderSetors ();
		$this->retVal .= $this->renderFooter ();

		return true;
	}

	/**
	 * Zużycie energii przez skaner
	 */
	static private function sGetPowerUsage($shipProperties) {
		global $config;

		return $config['activeScanner']['powerUsage'];
	}

	/**
	 * Zużycie antymaterii przez skaner
	 * @param stdClass $shipRouting
	 * @param shipPosition $shipPosition
	 */
	static private function sGetAmUsage($shipRouting, $shipPosition) {
		global $config;

		return $config['activeScanner']['amUsage'];
	}

	static public function sEngage() {
		global $activeScanner, $userID, $shipProperties, $shipPosition, $shipRouting, $userStats, $config, $sectorProperties,$portProperties, $systemProperties, $jumpNode, $sectorPropertiesObject, $portPropertiesObject, $jumpNodeObject;

		if (shipProperties::sCheckMalfunction ( $shipProperties )) {
			announcementPanel::getInstance()->write ( 'error', TranslateController::getDefault()->get ( 'shipMalfunctionEmp' ) );
			return false;
		}

		if ($shipPosition->Docked != 'no') {
			throw new securityException();
		}

		if (empty($shipProperties->CanActiveScan)) {
			throw new securityException();
		}

		$tPowerUsage = self::sGetPowerUsage($shipProperties);
		$tAmUsage = self::sGetAmUsage($shipRouting, $shipPosition);

		if ($shipProperties->Power < $tPowerUsage) {
			announcementPanel::getInstance()->write ('warning', TranslateController::getDefault()->get('notEnoughPower'));
			return false;
		}

		if ($shipProperties->Turns < $tAmUsage) {
			announcementPanel::getInstance()->write ('warning', TranslateController::getDefault()->get('notEnoughTurns'));
			return false;
		}

		$shipProperties->Power -= $tPowerUsage;
		if ($shipProperties->Power < 0) {
			$shipProperties->Power = 0;
		}
		$shipProperties->Turns -= $tAmUsage;
		if ($shipProperties->Turns < 0) {
			$shipProperties->Turns = 0;
		}
		if ($shipProperties->RookieTurns > 0) {
			$shipProperties->RookieTurns -= $tAmUsage;
			if ($shipProperties->RookieTurns < 0) {
				$shipProperties->RookieTurns = 0;
			}
		}

		$activeScanner->setShipPosition($shipPosition);
		$activeScanner->render();

		return;
	}

	/**
	 * (non-PHPdoc)
	 * @see miniMap::getShips()
	 */
	protected function getShips() {

		global $shipProperties;

		$tQuery = "SELECT
		sp.UserID,
		us.Cloak,
		sp.X,
		sp.Y
		FROM
		shippositions AS sp JOIN userships AS us USING(UserID)
		WHERE
		sp.System = '{$this->system->SystemID}' AND
		sp.X >= '{$this->X['start']}' AND
		sp.X <= '{$this->X['stop']}' AND
		sp.Y >= '{$this->Y['start']}' AND
		sp.Y <= '{$this->Y['stop']}' AND
		sp.Docked='no'
		";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);
		while ($tResult = \Database\Controller::getInstance()->fetch($tQuery)) {
			if ($this->userID == $tResult->UserID) {
				continue;
			}

			if ($tResult->X == $this->shipPosition->X && $tResult->Y == $this->shipPosition->Y) {
				continue;
			}

			$this->sector [$tResult->X] [$tResult->Y]->shipCount += $this->sector [$tResult->X] [$tResult->Y]->visibility - $tResult->Cloak;
		}

		for($indexY = $this->Y ['start']; $indexY <= $this->Y ['stop']; $indexY ++) {
			for($indexX = $this->X ['start']; $indexX <= $this->X ['stop']; $indexX ++) {

				$obj = $this->sector [$indexX] [$indexY];
				$obj->showPercentage = $obj->shipCount;

				if ($obj->showPercentage > 90) {
					$obj->showPercentage = 90;
				}

				if ($obj->showPercentage < 1) {
					$obj->showPercentage = 0;
				}

			}
		}

	}

	/**
	 * (non-PHPdoc)
	 * @see miniMap::getCacheProperty()
	 */
	protected function getCacheProperty() {
		return $this->system->SystemID;
	}

	public function renderHeader() {

		$retVal = "<h1>{T:System Scan}: " . $this->system->Name . "</h1>";
		$retVal .= miniMap::renderHeader ();
		return $retVal;
	}

}