<?php

namespace Controlpanel;

class Error extends BaseItem {

	protected $tableName = 'st_errormessages';
	protected $tableIdField = 'MessageID';
	
	protected function assignDbObject() {
		$this->db = \Database\Controller::getBackendInstance();
	}

	public function clear(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {

		$tQuery = "TRUNCATE TABLE st_errormessages";
		$this->db->execute ( $tQuery );

		$host = $_SERVER ['HTTP_HOST'];
		$uri = rtrim ( dirname ( $_SERVER ['PHP_SELF'] ), '/\\' );
		$extra = '?class=\Controlpanel\Error&method=browse';
		header ( "Location: http://$host$uri/$extra" );

	}

	/**
	 * Pobranie obiektu z bazy danych
	 *
	 * @param int $itemID
	 * @return \stdClass
	 */
	protected function getDataObject($itemID) {

		$tQuery = $this->db->execute ( "SELECT * FROM st_errormessages WHERE MessageID='{$itemID}' LIMIT 1" );
		return $this->db->fetch ( $tQuery );
	}

	public function detail(/** @noinspection PhpUnusedParameterInspection */
        $user, $params) {

		$retVal = '';

		$tObject = $this->getDataObject ( $params ['id'] );

		$template = new \General\Templater('templates/errorDetail.html');

		$tObject->CreateTime = \General\Formater::formatDateTime($tObject->CreateTime);
		$tObject->Text = urldecode($tObject->Text);

		$tText = $tObject->Parameters;
		$tParams = unserialize ( $tText );
		ob_start ();
		print_r ( $tParams );
		$tText = ob_get_contents ();
		$tText = htmlentities($tText);
		//    $tObject->Parameters = '<pre>'.$tText.'</pre>';
		$tObject->Parameters = $tText;
		ob_end_clean ();

		$template->add($tObject);

		$retVal .= $template;

		$retVal .= "<div style='text-align: center;'>";
		$retVal .= \General\Controls::bootstrapButton ( "Close", "document.location='index.php?class=" . get_class ( $this ) . "&amp;method=browse'",'btn-inverse', 'icon-off' );
		$retVal .= "</div>";

		return $retVal;
	}

}