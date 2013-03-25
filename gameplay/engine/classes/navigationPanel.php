<?php
/**
 * Panel nawigacyjny
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class navigationPanel extends basePanel {
	protected $panelTag = "navigationPanel";

	private static $instance = null;

	/**
	 * Konstruktor statyczny
	 * @return navigationPanel
	 */
	public static function getInstance(){
		if (empty(self::$instance)) {
			$className = __CLASS__;

			global $userProperties;

			self::$instance = new $className($userProperties->Language);
		}
		return self::$instance;
	}

	/**
	 * Wyrenderowanie panelu
	 *
	 * @param stdClass $shipPosition
	 * @param stdClass $shipRouting
	 * @param stdClass $shipProperties
	 */
	public function render($shipPosition, $shipRouting, $shipProperties) {
		global $userID;
		$this->rendered = true;

		$this->retVal = "<label>";
		$this->retVal .= TranslateController::getDefault()->get ( 'navigation' );
		$this->retVal .= "</label>";

		$this->retVal .= "<div>";
		$this->retVal .= "<div class='column50'>";

		$this->retVal .= systemMap::sRenderAvaibleSystemsSelect();

		$this->retVal .= "/";
		$this->retVal .= "<input onkeyup=\"javascript:return maskPlot(this.value,this)\" onblur=\"javascript:return maskPlot(this.value,this)\" type=\"text\" value=\"{$shipRouting->X}\" class=\"plot\" id=\"plotX\" />";
		$this->retVal .= "/";
		$this->retVal .= "<input onkeyup=\"javascript:return maskPlot(this.value,this)\" onblur=\"javascript:return maskPlot(this.value,this)\" type=\"text\" value=\"{$shipRouting->Y}\" class=\"plot\" id=\"plotY\" />";

		$this->retVal .= "</div>";

		$this->retVal .= "<div class='column50'>";
		$this->retVal .= "<div class='btn-group'>";
		$this->retVal .= "<button class='btn btn-success btn-mini' onclick=\"executeAction('plotSet',null,null,null,null);\" title='".TranslateController::getDefault()->get ( 'Set autopilot destination sector' )."'><i class='icon-white icon-check'></i></button>";

		if ($shipRouting->System != null) {
			$this->retVal .= " <button class='btn btn-danger btn-mini' onclick=\"executeAction('plotReset',null,null,null,null);\" title='".TranslateController::getDefault()->get ( 'Clear destination sector' )."'><i class='icon-white icon-trash'></i></button>";

			if ($shipPosition->Docked == 'no') {
				$this->retVal .= " <button class='btn btn-info btn-mini' onclick=\"executeAction('nextWaypoint',null,null,null,null);\" title='".TranslateController::getDefault()->get ( 'Next waypoint' )."'><i class='icon-white icon-step-forward'></i></button>";
			}

		}

		$this->retVal .= "</div>";
		$this->retVal .= "</div>";

		$this->retVal .= "</div>";
		$this->retVal .= "<div style='clear: both;'></div>";

		$this->retVal .= "<div class='btn-toolbar'>";
		$this->retVal .= "<div class='btn-group'>";
		$this->retVal .= "<button class='btn btn-small' onclick=\"systemMap.show();\" title='".TranslateController::getDefault()->get ( 'systemMap' )."'><i class='icon-list-alt icon-white'></i>".TranslateController::getDefault()->get ( 'systemMap' )."</button>";
		$this->retVal .= "</div>";

		$this->retVal .= '<div class="btn-group">';
		$this->retVal .= '<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">';
		$this->retVal .= TranslateController::getDefault()->get ( 'Menu' );
		$this->retVal .= '<span class="caret"></span>';
		$this->retVal .= '</a>';
		$this->retVal .= '<ul class="dropdown-menu">';
		$this->retVal .= "<li><a href='#' onclick=\"executeAction('showMyMaps',null, null, null);\"><i class='icon-th-list icon-white'></i> " . TranslateController::getDefault()->get ( 'My system maps' ) . '</a></li>';
		$this->retVal .= "<li><a href='gfx/maps/galaxymap.jpg' target='_blank'><i class='icon-list-alt icon-white'></i> " . TranslateController::getDefault()->get ( 'Galaxy map' ) . '</a></li>';
		$this->retVal .= "<li><a href='#' onclick=\"executeAction('showFavSectors',null, null, null);\"><i class='icon-plus icon-white'></i> " . TranslateController::getDefault()->get ( 'favSectors' ) . '</a></li>';
		$this->retVal .= "<li><a href='#' onclick=\"executeAction('addToFavSectors',null, null, null);\"><i class='icon-heart icon-white'></i> " . TranslateController::getDefault()->get ( 'addCurrentFavSectors' ) . '</a></li>';

		$this->retVal .= "</ul></div>";

		$this->retVal .= "</div>";
		


		return true;
	}

}