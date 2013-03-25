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

		$this->retVal = "<h1>";
		$this->retVal .= TranslateController::getDefault()->get ( 'navigation' );
		$this->retVal .= "</h1>";

		$this->retVal .= "<div class=\"plot\">";

		$this->retVal .= systemMap::sRenderAvaibleSystemsSelect();

		$this->retVal .= "/";
		$this->retVal .= "<input onKeyUp=\"javascript:return maskPlot(this.value,this)\" onBlur=\"javascript:return maskPlot(this.value,this)\" type=\"text\" value=\"{$shipRouting->X}\" class=\"plot\" id=\"plotX\" />";
		$this->retVal .= "/";
		$this->retVal .= "<input onKeyUp=\"javascript:return maskPlot(this.value,this)\" onBlur=\"javascript:return maskPlot(this.value,this)\" type=\"text\" value=\"{$shipRouting->Y}\" class=\"plot\" id=\"plotY\" />";

		$this->retVal .= "<img src=\"gfx/right2s.png\" class=\"linkSmall\" onclick=\"executeAction('plotSet',null,null,null,null);\" />";

		if ($shipRouting->System != null) {
			$this->retVal .= "<img src=\"gfx/del2s.png\" class=\"linkSmall\" onclick=\"executeAction('plotReset',null,null,null,null);\" />";

			if ($shipPosition->Docked == 'no') {
				$this->retVal .= \General\Controls::renderImgButton('follow', "executeAction('nextWaypoint',null,null,null,null)", TranslateController::getDefault()->get ( 'Next waypoint' ), 'link', 'style="height: 12px; margin: 0px; margin-left: 6px;"');
			}
				
		}

		$this->retVal .= "</div>";

		/*
		 * Mapa systemu
		*/
		$this->retVal .= '<div style="cursor: pointer;" onclick="systemMap.show();"><img src="gfx/right2.png" />' . TranslateController::getDefault()->get ( 'systemMap' ) . '</div>';
		/**
		 * Moje mapy
		 * @since 2011-05-31
		 */
		$this->retVal .= "<div style=\"cursor: pointer;\" onclick=\"executeAction('showMyMaps',null, null, null);\"><img src=\"gfx/right2.png\" />" . TranslateController::getDefault()->get ( 'My system maps' ) . '</div>';

		/*
		 * Mapa galaktyki
		*/
		$this->retVal .= "<div><img src=\"gfx/right2.png\" /><a href='gfx/maps/galaxymap.jpg' target='_blank'>" . TranslateController::getDefault()->get ( 'Galaxy map' ) . '</a></div>';

		/**
		 * Ulubione sektory
		 */
		$this->retVal .= "<div style=\"cursor: pointer;\" onclick=\"executeAction('showFavSectors',null, null, null);\"><img src=\"gfx/right2.png\" />" . TranslateController::getDefault()->get ( 'favSectors' ) . '</div>';
		$this->retVal .= "<div style=\"cursor: pointer;\" onclick=\"executeAction('addToFavSectors',null, null, null);\"><img src=\"gfx/right2.png\" />" . TranslateController::getDefault()->get ( 'addCurrentFavSectors' ) . '</div>';


		return true;
	}

}