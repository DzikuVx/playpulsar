<?php

namespace Gameplay\Panel;
use Gameplay\Model\ShipPosition;
use Gameplay\Model\ShipProperties;
use Gameplay\Model\SystemProperties;
use Gameplay\PlayerModelProvider;
use Interfaces\Singleton;
use TranslateController;

//FIXME separate view from model!!

class ActiveScanner extends SystemMap implements Singleton {

	protected $panelTag = "ActiveScanner";
	protected $sectorClass = "systemMap";
	protected $onClick = "Playpulsar.gameplay.sectorInfo";

	/**
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
	 * @param ShipPosition $shipPosition
	 */
	public function setShipPosition(ShipPosition $shipPosition) {
		$this->shipPosition = $shipPosition;
		$this->system = SystemProperties::quickLoad ( $this->shipPosition->System );
	}

    /**
     * @return bool|MiniMap
     * @throws \securityException
     */
    public function render() {

		if (empty($this->shipPosition)) {
			throw new \securityException();
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
     * @param ShipProperties $shipProperties
     * @return mixed
     */
    static private function sGetPowerUsage(/** @noinspection PhpUnusedParameterInspection */
        ShipProperties $shipProperties) {
		global $config;

		return $config['activeScanner']['powerUsage'];
	}

    /**
     * @param \stdClass $shipRouting
     * @param ShipPosition $shipPosition
     * @return mixed
     */
    static private function sGetAmUsage(/** @noinspection PhpUnusedParameterInspection */
        $shipRouting, ShipPosition $shipPosition) {
		global $config;

		return $config['activeScanner']['amUsage'];
	}

	static public function sEngage() {
		global $userProperties, $userID, $shipRouting;

        $shipPosition   = PlayerModelProvider::getInstance()->get('ShipPosition');
        $shipProperties = PlayerModelProvider::getInstance()->get('ShipProperties');
		$activeScanner 	= new ActiveScanner($userProperties->Language, $userID);

		if (ShipProperties::sCheckMalfunction($shipProperties)) {
			\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'error', '{T:shipMalfunctionEmp}');
			return false;
		}

		if ($shipPosition->Docked != 'no') {
			throw new \securityException();
		}

		if (empty($shipProperties->CanActiveScan)) {
			throw new \securityException();
		}

		$tPowerUsage = self::sGetPowerUsage($shipProperties);
		$tAmUsage 	 = self::sGetAmUsage($shipRouting, $shipPosition);

		if ($shipProperties->Power < $tPowerUsage) {
			\Gameplay\Framework\ContentTransport::getInstance()->addNotification('warning', TranslateController::getDefault()->get('notEnoughPower'));
			return false;
		}

		if ($shipProperties->Turns < $tAmUsage) {
			\Gameplay\Framework\ContentTransport::getInstance()->addNotification('warning', TranslateController::getDefault()->get('notEnoughTurns'));
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

		\Gameplay\Panel\Overlay::getInstance()->add($activeScanner->getRetVal());

		throw new \Gameplay\Exception\Overlay();
	}

    protected function getShips() {

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
	 * @see Gameplay\Panel.SystemMap::getCacheProperty()
	 */
	protected function getCacheProperty() {
		return $this->system->SystemID;
	}

	/**
	 * (non-PHPdoc)
	 * @see Gameplay\Panel.SystemMap::renderHeader()
	 */
	public function renderHeader() {
		$retVal = "<h1>{T:System Scan}: " . $this->system->Name . "</h1>";
		$retVal .= MiniMap::renderHeader ();
		return $retVal;
	}

}