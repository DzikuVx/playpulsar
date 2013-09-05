<?php

namespace Gameplay\Panel;

use Interfaces\Singleton;

use \TranslateController as Translate;

class Move extends Renderable implements Singleton {
	protected $panelTag = "Move";

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
		$this->retVal .= "<table class='moveCell'>";
		if ($shipPosition->Docked == "no") {
			//Komórka ruchu
			$this->retVal .= "<tr>";

			if (!empty($portProperties->PortID)) {
				$this->retVal .= "<td><button class='btn' onclick=\"Playpulsar.gameplay.execute('shipDock');\" title='".Translate::getDefault()->get ( 'dock' )."'><i class='icon-white icon-resize-small'></i></button></td>";
			}else {
				$this->retVal .= "<td>&nbsp;</td>";
			}

			if ($shipPosition->Y > 1) {
				$this->retVal .= "<td><button class='btn' onclick=\"Playpulsar.gameplay.execute('shipMove','up',null,null,null);\" ><i class='icon-chevron-up'></i></button></td>";
			} else {
				$this->retVal .= "<td>&nbsp;</td>";
			}

			if ($shipRouting->System != null) {
				$this->retVal .= "<td><button class='btn' onclick=\"Playpulsar.gameplay.execute('nextWaypoint',null,null,null,null);\" title='{T:Next waypoint}' ><i class='icon-white icon-step-forward'></i></button></td>";
			}else {
				$this->retVal .= "<td>&nbsp;</td>";
			}

			$this->retVal .= "</tr>";

			$this->retVal .= "<tr>";
			if ($shipPosition->X > 1) {
				$this->retVal .= "<td><button class='btn' onclick=\"Playpulsar.gameplay.execute('shipMove','left',null,null,null);\" ><i class='icon-chevron-left'></i></button></td>";
			} else {
				$this->retVal .= "<td>&nbsp;</td>";
			}
			$this->retVal .= "<td><button class='btn' onclick=\"Playpulsar.gameplay.execute('shipRefresh');\" ><i class='icon-refresh icon-white'></i></button></td>";
			if ($shipPosition->X < $systemProperties->Width) {
				$this->retVal .= "<td><button class='btn' onclick=\"Playpulsar.gameplay.execute('shipMove','right',null,null,null);\" ><i class='icon-chevron-right'></i></button></td>";
			} else {
				$this->retVal .= "<td>&nbsp;</td>";
			}
			$this->retVal .= "</tr>";

			$this->retVal .= "<tr>";

			if (!empty($shipProperties->CanActiveScan)) {
				$this->retVal .= "<td><button class='btn btn-info' onclick=\"Playpulsar.gameplay.execute('engageActiveScanner','down',null,null,null);\" title='{T:engageActiveScanner}' ><i class='icon-search icon-white'></i></button></td>";
			}else {
				$this->retVal .= "<td>&nbsp;</td>";
			}

			if ($shipPosition->Y < $systemProperties->Height) {
				$this->retVal .= "<td><button class='btn' onclick=\"Playpulsar.gameplay.execute('shipMove','down',null,null,null);\" ><i class='icon-chevron-down'></i></button></td>";
			} else {
				$this->retVal .= "<td>&nbsp;</td>";
			}
			if (!empty($shipRouting->System) && !empty($shipProperties->CanWarpJump)) {
				$this->retVal .= "<td><button class='btn btn-warning' onclick=\"Playpulsar.gameplay.execute('engageFtl','down',null,null,null);\" title='{T:Engage FTL Jump Drive}' ><i class='icon-screenshot icon-white'></i></button></td>";
			}else {
				$this->retVal .= "<td>&nbsp;</td>";
			}
			$this->retVal .= "</tr>";
		} else {
			//Komórka bezruchu :)
			$this->retVal .= "<tr>";
				$this->retVal .= "<td><button class='btn' onclick=\"Playpulsar.gameplay.execute('shipUnDock');\" title='".Translate::getDefault()->get ( 'undock' )."'><i class='icon-white icon-resize-full'></i></button></td>";
			$this->retVal .= "<td>&nbsp;</td>";
			$this->retVal .= "<td>&nbsp;</td>";
			$this->retVal .= "</tr>";
			$this->retVal .= "<tr>";
			$this->retVal .= "<td>&nbsp;</td>";
			$this->retVal .= "<td><button class='btn' onclick=\"Playpulsar.gameplay.execute('shipRefresh');\" ><i class='icon-refresh icon-white'></i></button></td>";
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