<?php

namespace Gameplay\Panel;

use Gameplay\Model\ShipPosition;
use Gameplay\Model\ShipProperties;
use Interfaces\Singleton;

class Navigation extends Renderable implements Singleton {

	protected $panelTag = "Navigation";

	/**
	 * @var \Gameplay\Panel\Navigation
	 */
	private static $instance = null;

	/**
	 *
	 * @return \Gameplay\Panel\Navigation
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			$className = __CLASS__;

            $userProperties = \Gameplay\PlayerModelProvider::getInstance()->get('UserEntity');
			self::$instance = new $className($userProperties->Language);
		}
		return self::$instance;
	}

	/**
	 * @param ShipPosition $shipPosition
	 * @param \stdClass $shipRouting
	 * @param ShipProperties $shipProperties
     * @return string
	 */
	public function render(ShipPosition $shipPosition, $shipRouting, ShipProperties $shipProperties) {
		$this->rendered = true;

		$this->retVal = "<label>{T:navigation}</label>";

		$this->retVal .= "<div>";
		$this->retVal .= "<div class='column50'>";

		$this->retVal .= SystemMap::sRenderAvaibleSystemsSelect($shipRouting->System);

		$this->retVal .= "/";
		$this->retVal .= "<input onkeyup=\"return maskPlot(this.value,this)\" onblur=\"return maskPlot(this.value,this)\" type=\"text\" value=\"{$shipRouting->X}\" class=\"plot\" id=\"plotX\" />";
		$this->retVal .= "/";
		$this->retVal .= "<input onkeyup=\"return maskPlot(this.value,this)\" onblur=\"return maskPlot(this.value,this)\" type=\"text\" value=\"{$shipRouting->Y}\" class=\"plot\" id=\"plotY\" />";

		$this->retVal .= "</div>";

		$this->retVal .= "<div class='column50'>";
		$this->retVal .= "<div class='btn-group'>";
		$this->retVal .= "<button class='btn btn-success btn-mini' onclick=\"Playpulsar.gameplay.execute('plotSet');\" title='{T:Set autopilot destination sector}'><i class='icon-white icon-check'></i></button>";

		if ($shipRouting->System != null) {
			$this->retVal .= " <button class='btn btn-danger btn-mini' onclick=\"Playpulsar.gameplay.execute('plotReset');\" title='{T:Clear destination sector}'><i class='icon-white icon-trash'></i></button>";

			if ($shipPosition->Docked == 'no') {
				$this->retVal .= " <button class='btn btn-info btn-mini' onclick=\"Playpulsar.gameplay.execute('nextWaypoint');\" title='{T:Next waypoint}'><i class='icon-white icon-step-forward'></i></button>";
			}

		}

		$this->retVal .= "</div>";
		$this->retVal .= "</div>";

		$this->retVal .= "</div>";
		$this->retVal .= "<div style='clear: both;'></div>";

		$this->retVal .= "<div class='btn-toolbar'>";
		$this->retVal .= "<div class='btn-group'>";
		$this->retVal .= "<button class='btn btn-small' onclick=\"Playpulsar.gameplay.systemMap();\" title='{T:systemMap}'><i class='icon-list-alt icon-white'></i>{T:systemMap}</button>";
		$this->retVal .= "</div>";

		$this->retVal .= '<div class="btn-group">';
		$this->retVal .= '<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">';
		$this->retVal .= '{T:Menu}';
		$this->retVal .= '<span class="caret"></span>';
		$this->retVal .= '</a>';
		$this->retVal .= '<ul class="dropdown-menu">';
		$this->retVal .= "<li><a href='#' onclick=\"Playpulsar.gameplay.execute('showMyMaps');\"><i class='icon-th-list icon-white'></i> {T:My system maps}</a></li>";
		$this->retVal .= "<li><a href='gfx/maps/galaxymap.jpg' target='_blank'><i class='icon-list-alt icon-white'></i> {T:Galaxy map}</a></li>";
		$this->retVal .= "<li><a href='#' onclick=\"Playpulsar.gameplay.execute('showFavSectors');\"><i class='icon-plus icon-white'></i> {T:favSectors}</a></li>";
		$this->retVal .= "<li><a href='#' onclick=\"Playpulsar.gameplay.execute('addToFavSectors');\"><i class='icon-heart icon-white'></i> {T:addCurrentFavSectors}</a></li>";
		$this->retVal .= "</ul></div>";
		$this->retVal .= "</div>";

		return true;
	}

}