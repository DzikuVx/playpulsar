<?php
/**
 * rejestr ulubionych sektorów
 *
 * @version $Rev: 460 $
 * @package Engine
 */
class favSectorsRegistry extends simpleRegistry {

	/**
	 * Wyrenderowanie rejestru ulubionych sektorów
	 *
	 * @param int $userID
	 * @return string
	 */
	public function get() {

		$retVal = '';

		$retVal .= "<h1>" . TranslateController::getDefault()->get ( 'favSectors' ) . "</h1>";

		$retVal .= "<table class=\"transactionList\" cellspacing=\"2\" cellpadding=\"0\">";

		$retVal .= "<tr>";
		$retVal .= "<th>" . TranslateController::getDefault()->get ( 'sector' ) . "</th>";
		$retVal .= "<th style=\"width: 6em;\">&nbsp;</th>";
		$retVal .= "</tr>";

		$tQuery = "SELECT System, X, Y FROM favouritesectors WHERE UserID='{$this->userID}' ORDER BY System, X, Y";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			$retVal .= '<tr>';
			$retVal .= '<td >' . $tResult->System . '/' . $tResult->X . '/' . $tResult->Y . '</td>';

			$tString = '';
			$tString .= \General\Controls::renderImgButton ( 'delete', "executeAction('deleteFavSector','',null,'{$tResult->System}/{$tResult->X}/{$tResult->Y}');", TranslateController::getDefault()->get('delete') );
			$tString .= \General\Controls::renderImgButton ( 'rightFar', "systemMap.plot('{$tResult->System}','{$tResult->X}','{$tResult->Y}');", TranslateController::getDefault()->get ( 'setNavPoint' ));

			if (empty ( $tString )) {
				$tString = '&nbsp;';
			}

			$retVal .= '<td>' . $tString . '</td>';

			$retVal .= '</tr>';
		}
		$retVal .= "</table>";

		return $retVal;
	}

}