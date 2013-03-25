<?php

namespace Controlpanel;

class Menu {

	static public function get() {

		$retVal = '';
		
		$retVal .= '<div class="nav-collapse">';
		$retVal .= '<ul class="nav">';

		$retVal .= '<li class="dropdown">';
		$retVal .= '<a class="dropdown-toggle" data-toggle="dropdown" href="#">';
		$retVal .= 'Players';
		$retVal .= '<b class="caret"></b>';
		$retVal .= '</a>';
		$retVal .= '<ul class="dropdown-menu">';
		$retVal .= "<li><a href='index.php?class=\Controlpanel\Player&amp;method=browse'>Browse</a></li>";
		$retVal .= "<li><a href='index.php?class=\Controlpanel\Abusement&amp;method=browse'>Abusements</a></li>";
		$retVal .= "<li><a href='index.php?class=\Controlpanel\Statistics&amp;method=detail'>Statistics</a></li>";
		$retVal .= '</ul>';
		$retVal .= '</li>';

		$retVal .= '<li class="dropdown">';
		$retVal .= '<a class="dropdown-toggle" data-toggle="dropdown" href="#">';
		$retVal .= 'NPC';
		$retVal .= '<b class="caret"></b>';
		$retVal .= '</a>';
		$retVal .= '<ul class="dropdown-menu">';
		$retVal .= "<li><a href='index.php?class=\Controlpanel\Npc&amp;method=browse'>Browse</a></li>";
		$retVal .= "<li><a href='index.php?class=\Controlpanel\NpcTypes&amp;method=browse'>NPC Types</a></li>";
		$retVal .= '</ul>';
		$retVal .= '</li>';
		
		$retVal .= '<li class="dropdown">';
		$retVal .= '<a class="dropdown-toggle" data-toggle="dropdown" href="#">';
		$retVal .= 'Gameplay';
		$retVal .= '<b class="caret"></b>';
		$retVal .= '</a>';
		$retVal .= '<ul class="dropdown-menu">';
		$retVal .= "<li><a href='index.php?class=\Controlpanel\WeaponTypes&amp;method=browse'>Weapon Types</a></li>";
		$retVal .= "<li><a href='index.php?class=\Controlpanel\EquipmentTypes&amp;method=browse'>Equipment Types</a></li>";
		$retVal .= "<li><a href='index.php?class=\Controlpanel\ShipTypes&amp;method=browse'>Ship Types</a></li>";
		$retVal .= "<li><a href='index.php?class=\Controlpanel\ProductTypes&amp;method=browse'>Goods Types</a></li>";
		$retVal .= "<li><a href='index.php?class=\Controlpanel\PortTypes&amp;method=browse'>Port Types</a></li>";
		$retVal .= "<li><a href='index.php?class=\Controlpanel\StationTypes&amp;method=browse'>Station Types</a></li>";
		$retVal .= "<li><a href='index.php?class=\Controlpanel\NpcName&amp;method=browse'>NPC Names</a></li>";
		$retVal .= '</ul>';
		$retVal .= '</li>';
		
		$retVal .= '<li class="dropdown">';
		$retVal .= '<a class="dropdown-toggle" data-toggle="dropdown" href="#">';
		$retVal .= 'Portal';
		$retVal .= '<b class="caret"></b>';
		$retVal .= '</a>';
		$retVal .= '<ul class="dropdown-menu">';
		$retVal .= "<li><a href='index.php?class=\Controlpanel\News&amp;method=add'>New portal message</a></li>";
		$retVal .= "<li><a href='index.php?class=\Controlpanel\News&amp;method=browse'>Browse messages</a></li>";
		$retVal .= "<li><a href='index.php?class=\Controlpanel\MainNews&amp;method=browse'>Main news (intro)</a></li>";
		$retVal .= "<li><a href='index.php?class=\Controlpanel\Article&amp;method=add'>New portal article</a></li>";
		$retVal .= "<li><a href='index.php?class=\Controlpanel\Article&amp;method=browse'>Browse articles</a></li>";
		$retVal .= '</ul>';
		$retVal .= '</li>';

		if (\user::sGetRole () == 'admin') {

			$retVal .= '<li class="dropdown">';
			$retVal .= '<a class="dropdown-toggle" data-toggle="dropdown" href="#">';
			$retVal .= 'Admin';
			$retVal .= '<b class="caret"></b>';
			$retVal .= '</a>';
			$retVal .= '<ul class="dropdown-menu">';
			$retVal .= "<li><a href='index.php?class=\Controlpanel\Maintenance&amp;method=detail'>Maintenance</a></li>";
			$retVal .= '<li class="divider"></li>';
			$retVal .= "<li><a href='index.php?class=tableStatus&amp;method=browse'>Tables Status</a></li>";
			$retVal .= "<li><a href='index.php?class=tableCheck&amp;method=browse'>Tables Check</a></li>";
			$retVal .= '<li class="divider"></li>';
			$retVal .= "<li><a href='index.php?class=\Controlpanel\Queries&amp;method=browse'>Queries</a></li>";
			$retVal .= "<li><a href='index.php?class=\Controlpanel\ParsedQueries&amp;method=browse'>Parsed Queries</a></li>";
			$retVal .= "<li><a href='index.php?class=\Controlpanel\ScriptRun&amp;method=browse'>Script Runs</a></li>";
			$retVal .= "<li><a href='index.php?class=queryStatistics&amp;method=detail'>Statistics</a></li>";
			$retVal .= '<li class="divider"></li>';
			$retVal .= "<li><a href='index.php?class=\Controlpanel\Error&amp;method=browse'\">Errors</a></li>";
			$retVal .= '</ul>';
			$retVal .= '</li>';
		}
		$retVal .= '</ul>';

		$retVal .= '<ul class="nav pull-right">';
		$retVal .= "<li><a href='index.php?doLogout=true'>Sign Out</a>";
		$retVal .= '</ul>';
		$retVal .= '</div>';
		
		return $retVal;
	}

}