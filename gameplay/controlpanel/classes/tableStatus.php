<?php

/**
 * Status tabel bazy danych
 *
 * @package ControlPanel
 * @version $Rev: 454 $
 */
class tableStatus extends \Controlpanel\BaseItem{

	/**
	 * Indeksy pojedynczej tabeli
	 *
	 * @param user $user
	 * @param array $params
	 * @return string
	 */
	public function getIndexes(user $user, $params) {
		$retVal = '';

		if ($user->sGetRole () != 'admin') {
			throw new customException ( 'No rights to perform selected operation' );
		}

		$tQuery = \Database\Controller::getInstance()->execute ( "SHOW KEYS FROM {$params['id']}" );

		$retVal .= "<table class='table table-striped table-bordered table-condensed'>";

		$tCount = 0;
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			if ($tCount == 0) {

				$retVal .= '<thead>';
				$retVal .= '<tr>';

				foreach ( $tResult as $tKey => $tValue ) {

					if ($tKey == 'Table' || $tKey == 'Collation' || $tKey == 'Comment' || $tKey == 'Index_type') {
						continue;
					}

					$retVal .= '<th>' . $tKey . '</th>';
				}

				$retVal .= '</tr>';
				$retVal .= '</thead>';
				$retVal .= '<tbody>';

			}

			$retVal .= '<tr>';
			foreach ( $tResult as $tKey => $tValue ) {
				if ($tKey == 'Table' || $tKey == 'Collation' || $tKey == 'Comment' || $tKey == 'Index_type') {
					continue;
				}
				$retVal .= '<td>' . $tValue . '</td>';
			}
			$retVal .= '</tr>';

			$tCount ++;
		}
		$retVal .= "</tbody>";
		$retVal .= '</table>';

		return $retVal;
	}

	/**
	 * Parametry pojedynczej tabeli
	 *
	 * @param user $user
	 * @param array $params
	 * @return string
	 * @throws customException
	 */
	public function getStatus(user $user, $params) {
		$retVal = '';

		if ($user->sGetRole () != 'admin') {
			throw new customException ( 'No rights to perform selected operation' );
		}
		
		$tQuery = \Database\Controller::getInstance()->execute ( "SHOW TABLE STATUS WHERE Name='{$params['id']}'" );

		$retVal .= "<table class='table table-striped table-bordered table-condensed'>";

		$retVal .= "<thead>";
		$retVal .= "<tr>";
		$retVal .= "<th>Key</th>";
		$retVal .= "<th>Value</th>";
		$retVal .= "</tr>";
		$retVal .= "</thead>";

		$retVal .= "<tbody>";
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			foreach ( $tResult as $tKey => $tValue ) {

				$retVal .= "<tr>";
				$retVal .= "<td>" . $tKey . "</td>";
				$retVal .= "<td>" . $tValue . "</td>";
				$retVal .= "</tr>";
			}
		}
		$retVal .= "</tbody>";
		$retVal .= '</table>';

		return $retVal;
	}

	/**
	 * Szczegóły tabeli
	 *
	 * @param user $user
	 * @param array $params
	 * @return string
	 */
	public function detail(user $user, $params) {
		$retVal = '';

		if ($user->sGetRole () != 'admin') {
			throw new customException ( 'No rights to perform selected operation' );
		}

		$retVal .= $this->renderTitle ( "Table status: " . $params ['id'] );
		
		$retVal .= '<p>';
		$retVal .= \General\Controls::bootstrapButton ( 'Optimize', "document.location='?class=".get_class($this)."&amp;method=optimize&amp;id={$params['id']}&amp;returnToDetail=true'", 'btn-success',"icon-cog");
		$retVal .= \General\Controls::bootstrapButton ( 'Check', "document.location='?class=".get_class($this)."&amp;method=check&amp;id={$params['id']}&amp;returnToDetail=true'", 'btn-info',"icon-search" );
		$retVal .= \General\Controls::bootstrapButton ( 'Repair', "document.location='?class=".get_class($this)."&amp;method=repair&amp;id={$params['id']}&amp;returnToDetail=true'",'btn-success','icon-fire' );
		$retVal .= \General\Controls::bootstrapButton ( 'Analyze', "document.location='?class=".get_class($this)."&amp;method=analyze&amp;id={$params['id']}&amp;returnToDetail=true'",'btn-success','icon-folder-open' );
		$retVal .= '</p>';

		$retVal .= $this->getStatus ( $user, $params );
		
		$retVal .= $this->renderTitle ( "Table indexes: " . $params ['id'] );
		$retVal .= $this->getIndexes ( $user, $params );

		$retVal .= "<div style='text-align: center;'>";
		$retVal .= \General\Controls::bootstrapButton ( "Close", "window.history.back();",'btn-inverse','icon-off' );
		$retVal .= "</div>";

		return $retVal;
	}

	/**
	 * Sprawdzenie pojecynczej tabeli
	 *
	 * @param string $name
	 * @return string
	 */
	static public function sQuickCheck($name) {
		$retVal = '';
		
		$tQuery = "CHECK TABLE " . $name;
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tSeconResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			$retVal = $tSeconResult->Msg_text;
		}

		return $retVal;
	}

	/**
	 * Optymalizacja tabeli
	 *
	 * @param user $user
	 * @param array $params
	 * @return string
	 */
	public function optimize(user $user, $params) {

		$retVal = '';

		if ($user->sGetRole () != 'admin') {
			throw new customException ( 'No rights to perform selected operation' );
		}

		$tQuery = \Database\Controller::getInstance()->execute ( 'OPTIMIZE TABLE ' . $params ['id'] );

		$retVal .= $this->renderTitle ( "Table optimize: " . $params ['id'] );

		$retVal .= "<table class='template' border='0'>";

		$retVal .= "<thead>";
		$retVal .= "<tr>";
		$retVal .= "<th>Type</th>";
		$retVal .= "<th>Message</th>";
		$retVal .= "</tr>";
		$retVal .= "</thead>";
		$retVal .= "<tbody>";
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			$retVal .= "<tr>";
			$retVal .= "<td>" . $tResult->Msg_type . "</td>";
			$retVal .= "<td>" . $tResult->Msg_text . "</td>";
			$retVal .= "</tr>";
		}
		$retVal .= "</tbody>";
		$retVal .= '</table>';

		$retVal .= "<p>";
		$retVal .= \General\Controls::bootstrapButton ( "Close", "window.history.back();", 'btn-inverse','icon-off' );
		$retVal .= "</p>";

		return $retVal;

	}

	/**
	 * Sprawdzenie tabeli
	 *
	 * @param user $user
	 * @param array $params
	 * @return string
	 */
	public function check(user $user, $params) {

		$retVal = '';

		if ($user->sGetRole () != 'admin') {
			throw new customException ( 'No rights to perform selected operation' );
		}

		$tQuery = \Database\Controller::getInstance()->execute ( 'CHECK TABLE ' . $params ['id'] . ' EXTENDED' );

		$retVal .= $this->renderTitle ( "Table check: " . $params ['id'] );

		$retVal .= "<table class='template' border='0'>";

		$retVal .= "<thead>";
		$retVal .= "<tr>";
		$retVal .= "<th>Type</th>";
		$retVal .= "<th>Message</th>";
		$retVal .= "</tr>";
		$retVal .= "</thead>";
		$retVal .= "<tbody>";
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			$retVal .= "<tr>";
			$retVal .= "<td>" . $tResult->Msg_type . "</td>";
			$retVal .= "<td>" . $tResult->Msg_text . "</td>";
			$retVal .= "</tr>";
		}
		$retVal .= "</tbody>";
		$retVal .= '</table>';

		$retVal .= "<div style='text-align: center;'>";
		$retVal .= \General\Controls::bootstrapButton ( "Close", "window.history.back();", 'btn-inverse','icon-off'  );
		$retVal .= "</div>";

		return $retVal;
	}

	/**
	 * Naprawa tabeli
	 *
	 * @param user $user
	 * @param array $params
	 * @return string
	 */
	public function repair(user $user, $params) {

		$retVal = '';

		if ($user->sGetRole () != 'admin') {
			throw new customException ( 'No rights to perform selected operation' );
		}

		$tQuery = \Database\Controller::getInstance()->execute ( 'REPAIR TABLE ' . $params ['id'] . ' EXTENDED' );

		$retVal .= $this->renderTitle ( "Table repair: " . $params ['id'] );

		$retVal .= "<table class='template' border='0'>";

		$retVal .= "<thead>";
		$retVal .= "<tr>";
		$retVal .= "<th>Type</th>";
		$retVal .= "<th>Message</th>";
		$retVal .= "</tr>";
		$retVal .= "</thead>";
		$retVal .= "<tbody>";
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			$retVal .= "<tr>";
			$retVal .= "<td>" . $tResult->Msg_type . "</td>";
			$retVal .= "<td>" . $tResult->Msg_text . "</td>";
			$retVal .= "</tr>";
		}
		$retVal .= "</tbody>";
		$retVal .= '</table>';

		$retVal .= "<div style='text-align: center;'>";
		$retVal .= \General\Controls::bootstrapButton ( "Close", "window.history.back();", 'btn-inverse','icon-off'  );
		$retVal .= "</div>";

		return $retVal;
	}

	/**
	 * ANALYZE table
	 *
	 * @param user $user
	 * @param array $params
	 * @return string
	 */
	public function analyze(user $user, $params) {

		$retVal = '';

		if ($user->sGetRole () != 'admin') {
			throw new customException ( 'No rights to perform selected operation' );
		}

		$tQuery = \Database\Controller::getInstance()->execute ( 'ANALYZE TABLE ' . $params ['id'] );

		$retVal .= $this->renderTitle ( "Table analyze: " . $params ['id'] );

		$retVal .= "<table class='template' border='0'>";

		$retVal .= "<thead>";
		$retVal .= "<tr>";
		$retVal .= "<th>Type</th>";
		$retVal .= "<th>Message</th>";
		$retVal .= "</tr>";
		$retVal .= "</thead>";
		$retVal .= "<tbody>";
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			$retVal .= "<tr>";
			$retVal .= "<td>" . $tResult->Msg_type . "</td>";
			$retVal .= "<td>" . $tResult->Msg_text . "</td>";
			$retVal .= "</tr>";
		}
		$retVal .= "</tbody>";
		$retVal .= '</table>';

		$retVal .= "<div style='text-align: center;'>";
		$retVal .= \General\Controls::bootstrapButton ( "Close", "window.history.back();", 'btn-inverse','icon-off'  );
		$retVal .= "</div>";

		return $retVal;
	}

}