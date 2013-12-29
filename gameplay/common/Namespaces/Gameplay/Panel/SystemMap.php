<?php

namespace Gameplay\Panel;

use Gameplay\Model\ShipPosition;
use Interfaces\Singleton;

//FIXME spearate view (Panel) from rendering

class SystemMap extends MiniMap implements Singleton{

	protected $panelTag = "SystemMap";
	protected $sectorClass = "systemMap";
	protected $useBorder = true;
	protected $onClick = "Playpulsar.gameplay.sectorInfo";

    /**
     * @param string $userID
     * @param int $system
     * @param ShipPosition $shipPosition
     * @param bool $getShips
     * @param bool $getStacks
     */
    public function __construct($userID, $system, ShipPosition $shipPosition = null, $getShips = false, $getStacks = false) {
		$this->load ( $userID, $system, $shipPosition, $getShips, $getStacks );
	}

	protected function getShips() {

	}

	protected function getCacheProperty() {
		return $this->system->SystemID;
	}

    /**
     * @param $currentSystem
     * @param null $attr
     * @param bool $displayEmpty
     * @param bool $displayName
     * @return string
     */
    static public function sRenderAvaibleSystemsSelect($currentSystem, $attr = null, $displayEmpty = true, $displayName = false) {
		global $userID, $shipRouting;

		if (!isset($attr['class'])) {
			$attr['class'] = 'plot span1';
		}

		if (!isset($attr['id'])) {
			$attr['id'] = 'plotSystem';
		}

		$retVal = "<select";

		foreach ($attr as $tKey=>$tValue) {
			$retVal .= ' '.$tKey.'="'.$tValue.'"';
		}

		$retVal .= ">";
		if ($displayEmpty) {
			$retVal .= "<option value=\"null\">-</option>";
		}
		$tQuery = \Database\Controller::getInstance()->execute ( "SELECT
        systems.SystemID,
        systems.Number,
        systems.Name
      FROM
        systems LEFT JOIN usermaps ON usermaps.SystemID=systems.SystemID
      WHERE
        systems.Enabled = 'yes' AND
        (systems.MapAvaible = 'yes' OR usermaps.UserID='$userID')
      ORDER BY
        systems.SystemID;
        " );
		while ( $resultRow = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			if ($currentSystem == $resultRow->SystemID) {
				$tString = "selected";
			} else {
				$tString = "";
			}
			if ($displayName) {
				$retVal .= "<option value='{$resultRow->SystemID}' {$tString}>" . $resultRow->Number . " - {$resultRow->Name}</option>";
			}else {
				$retVal .= "<option value=\"" . $resultRow->SystemID . "\" $tString >" . $resultRow->Number . "</option>";
			}
		}

		$retVal .= "</select>";

		return $retVal;
	}

	protected function getLimits() {

		$this->X ['start'] = 1;
		$this->X ['stop'] = $this->system->Width;
		$this->Y ['start'] = 1;
		$this->Y ['stop'] = $this->system->Height;
	}

	public function renderHeader() {

		$retVal = "<h1>{T:system}: " . $this->system->Name . "</h1>";

		$retVal .= '<div style="float: left; margin-right: 1em;">';
		$tArray = array();
		$tArray['onchange'] = 'Playpulsar.gameplay.systemMap($(this).val());';
		$tArray['class'] = 'input-small';
		$retVal .= self::sRenderAvaibleSystemsSelect($this->system->SystemID, $tArray, false, true);
		$retVal .= '</div>';

		$retVal .= parent::renderHeader ();
		return $retVal;
	}

}