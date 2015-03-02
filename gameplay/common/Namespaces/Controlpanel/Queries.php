<?php

namespace Controlpanel;

class Queries extends \Controlpanel\BaseItem{

	protected function assignDbObject() {
		$this->db = \Database\Controller::getBackendInstance();
	}

	public function explain($user, $params) {

		$tObject = $this->getDataObject ( $params ['id'] );

		$retVal = '';
		
		$retVal .= "<p>";
		$retVal .= \General\Controls::bootstrapButton ( "Close", "window.history.back()",'btn-inverse', 'icon-off' );
		$retVal .= "</p>";
		
		$retVal .= "<table class='table table-striped'>";

		$retVal .= "<tr>";
		$retVal .= "<th style='width: 20%'>Hash: </th>";
		$retVal .= "<td>" . $tObject->Hash . "</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<th>Zapytanie: </th>";
		$retVal .= "<td>" . $tObject->Query . "</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<th>Liczba: </th>";
		$retVal .= "<td>" . $tObject->Count . "</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<th>Czas: </th>";
		$retVal .= "<td>" . $tObject->Time . "</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<th>Średni czas: </th>";
		$retVal .= "<td>" . $tObject->Avg . "</td>";
		$retVal .= "</tr>";

		$retVal .= "</table>";

		$retVal .= "<table class='table table-striped'>";

		$tCount = 0;

		$tQuery = 'EXPLAIN ' . $tObject->Query;
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $tResult = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			if ($tCount == 0) {

				$retVal .= '<thead>';
				$retVal .= '<tr>';

				foreach ( $tResult as $tKey => $tValue ) {
					$retVal .= '<th>' . $tKey . '</th>';
				}

				$retVal .= '</tr>';
				$retVal .= '</thead>';
				$retVal .= '<tbody>';
					
			}

			$retVal .= '<tr>';
			foreach ( $tResult as $tValue ) {
				$retVal .= '<td>' . $tValue . '</td>';
			}
			$retVal .= '</tr>';

			$tCount ++;
		}

		$retVal .= '</tbody>';
		$retVal .= "</table>";

		return $retVal;
	}

	public function detail($user, $params) {

		$retVal = '';

		$tObject = $this->getDataObject ( $params ['id'] );

		$retVal .= "<p>";
		$retVal .= \General\Controls::bootstrapButton ( "Close", "document.location='index.php?class=" . get_class ( $this ) . "&amp;method=browse'",'btn-inverse', 'icon-off' );
		if (get_class ( $this ) == 'Controlpanel\Queries') {
			$retVal .= \General\Controls::bootstrapButton ( "Explain", "document.location='index.php?class=" . get_class ( $this ) . "&amp;method=explain&amp;id={$params['id']}'",'btn-success', 'icon-asterisk');
		}
		$retVal .= "</p>";

		$retVal .= $this->renderTitle ( "Query details" );

		$retVal .= "<table class='table table-striped'>";

		$retVal .= "<tr>";
		$retVal .= "<th style='width: 20%'>Hash: </th>";
		$retVal .= "<td>" . $tObject->Hash . "</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<th>Query: </th>";
		$retVal .= "<td>" . $tObject->Query . "</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<th>Count: </th>";
		$retVal .= "<td>" . $tObject->Count . "</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<th>Time: </th>";
		$retVal .= "<td>" . $tObject->Time . "</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<th>Average time: </th>";
		$retVal .= "<td>" . $tObject->Avg . "</td>";
		$retVal .= "</tr>";

		$retVal .= "</table>";


		return $retVal;
	}

	protected function getDataObject($itemID) {

		$tQuery = $this->db->execute ( "SELECT st_queries.*, (Time/Count) AS Avg FROM st_queries WHERE Hash='{$itemID}' LIMIT 1" );
		return $this->db->fetch ( $tQuery );
	}

	public function clear($user, $params) {

		$tQuery = "TRUNCATE TABLE st_queries";
		$this->db->execute ( $tQuery );

		$host = $_SERVER ['HTTP_HOST'];
		$uri = rtrim ( dirname ( $_SERVER ['PHP_SELF'] ), '/\\' );
		$extra = '?class=queries&method=browse';
		header ( "Location: http://$host$uri/$extra" );

	}

}