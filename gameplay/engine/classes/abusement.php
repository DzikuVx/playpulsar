<?php
class abusement extends baseItem {

	protected $tableName = "abusements";
	protected $tableID = "AbusementID";
	protected $tableUseFields = array ("UserID", "ByUserID", "CreateTime", "Text", "Status");

	/**
	 * Wstawienie zgłoszenia
	 * @param array $data
	 * @return boolean
	 * @deprecated
	 */
	static private function sInsert($data) {

		$retVal = true;

		try {

			\Database\Controller::getInstance()->quoteAll($data);

			$tQuery = "INSERT INTO abusements(UserID, ByUserID, CreateTime, Text) VALUES(
						'{$data['UserID']}',
						'{$data['ByUserID']}',
						'".time()."',
						'{$data['Text']}'
				)";

			\Database\Controller::getInstance()->execute($tQuery);

		}catch (Exception $e) {
			psDebug::cThrow(null, $e, array('display'=>false));
			$retVal = false;
		}

		return $retVal;
	}

	static public function sNew($id) {

		global $portPanel, $userID;

		if (empty($id)) {
			throw new securityException();
		}

		$template  = new \General\Templater('../templates/newAbusement.html');

		$tData = userProperties::quickLoad($id);

		$template->add('playerName', $tData->Name);
		$template->add('FormName', TranslateController::getDefault()->get('Report abusement'));
		$template->add('action', 'user.newAbusement('.$id.');');

		\Gameplay\Panel\Action::getInstance()->add((string) $template);

		\Gameplay\Panel\SectorShips::getInstance()->hide();
		\Gameplay\Panel\SectorResources::getInstance()->hide();
		$portPanel = "&nbsp;";
	}

	static public function sNewExe($id, $text) {

		global $portPanel, $userID;

		if (empty($id)) {
			throw new securityException();
		}

		$data = new stdClass();
		$data->UserID = $id;
		$data->ByUserID = $userID;
		$data->CreateTime = time();
		$data->Text = $text;
		$data->Status = 'new';

		\Database\Controller::getInstance()->quoteAll($data);

		$item = new self();
		$item->insert($data);
		unset ($item);

		shipExamine ( $id, $userID );

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		$portPanel = "&nbsp;";

		announcementPanel::getInstance()->write('info', TranslateController::getDefault()->get('opSuccess'));

	}

}