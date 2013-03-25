<?php
/**
 * rejestr ulubionych sektorów
 *
 * @version $Rev: 283 $
 * @package Engine
 */
class userMapsRegistry extends simpleRegistry {

	/**
	 * Wyrenderowanie rejestru ulubionych sektorów
	 *
	 * @param int $userID
	 * @return string
	 */
	public function get() {

		global $t, $userID;

		$retVal = '';

		$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'My system maps' ) . "</h1>";

		$retVal .= "<table class='table table-striped table-condensed'>";

		$retVal .= "<tr>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'System' ) . "</th>";
		$retVal .= "<th style=\"width: 2em;\">&nbsp;</th>";
		$retVal .= "</tr>";

		$tQuery = "SELECT
        systems.SystemID,
        systems.Number,
        systems.Name
      FROM
        systems LEFT JOIN usermaps ON usermaps.SystemID=systems.SystemID
      WHERE
        systems.Enabled = 'yes' AND
        (systems.MapAvaible = 'yes' OR usermaps.UserID='$userID')
      ORDER BY
        systems.SystemID";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$retVal .= "<tr>";
			$retVal .= "<td>" . $tResult->Name . ' ['.$tResult->Number."]</td>";
			$retVal .= "<td>";
			$retVal .= \General\Controls::renderImgButton ( 'info', "systemMap.show('{$tResult->SystemID}');", TranslateController::getDefault()->get('Info') );
			$retVal .= "</td>";
			$retVal .= "</tr>";
		}
		$retVal .= "</table>";

		return $retVal;
	}

}