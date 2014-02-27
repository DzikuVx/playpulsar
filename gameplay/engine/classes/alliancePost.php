<?php
class alliancePost extends baseItem {
	protected $tableName = "allianceposts";
	protected $tableID = "PostID";
	protected $tableUseFields = array ("UserID", "Date", "AllianceID", "Text");
	protected $defaultCacheExpire = 1800;
	protected $useMemcached = true;

	static private function sRemove($id) {
		\Database\Controller::getInstance()->execute("DELETE FROM allianceposts WHERE PostID='{$id}' LIMIT 1");
	}

    /**
     * @param $ID
     * @return stdClass
     * @deprecated
     */
    static public function quickLoad($ID) {
		$item = new self();
		$retVal = $item->load ( $ID, true, true );
		unset($item);
		return $retVal;
	}

	/**
	 * Usunięcie wiadomości na ścianie sojuszu, dialog
	 * @param int $id
	 * @throws securityException
	 */
	static public function sDelete($id) {

		global $userAlliance, $userID;

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (! allianceRights::sCheck($userID, $userAlliance->AllianceID, 'post')) {
			throw new securityException();
		}

		$tData = self::quickLoad($id);

		if ($tData->AllianceID != $userAlliance->AllianceID) {
			throw new securityException();
		}

		\Gameplay\Panel\Action::getInstance()->add(\General\Controls::sRenderDialog(TranslateController::getDefault()->get ( 'confirm' ), TranslateController::getDefault()->get('wantDeleteAlliancePost'),"Playpulsar.gameplay.execute('alliancePostDeleteExe',null,null,{$id})","Playpulsar.gameplay.execute('allianceDetail',null,null,'{$userAlliance->AllianceID}')"));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 * usunięcie wiadomości na ścianie sojuszu, wykonanie
	 * @param int $id
	 * @throws securityException
	 */
	static public function sDeleteExe($id) {

		global $userAlliance, $userID;

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (! allianceRights::sCheck($userID, $userAlliance->AllianceID, 'post')) {
			throw new securityException();
		}

		$tData = self::quickLoad($id);

		if ($tData->AllianceID != $userAlliance->AllianceID) {
			throw new securityException();
		}

		self::sRemove($id);

		\phpCache\Factory::getInstance()->create()->clear('alliancePostsRegistry::get',  md5($userAlliance->AllianceID.'|'.serialize(true)));
        \phpCache\Factory::getInstance()->create()->clear('alliancePostsRegistry::get',  md5($userAlliance->AllianceID.'|'.serialize(false)));

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('info', TranslateController::getDefault()->get('messageDeleted'));

		\Gameplay\Panel\Action::getInstance()->add(\Gameplay\Model\Alliance::sGetDetail($userAlliance->AllianceID));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 * @throws securityException
	 */
	static public function sNew() {

		global $userAlliance, $userID;

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (! allianceRights::sCheck($userID, $userAlliance->AllianceID, 'post')) {
			throw new securityException();
		}

		$template  = new \General\Templater('../templates/alliancePost.html');

		$template->add('FormName', TranslateController::getDefault()->get('newMessage'));
		$template->add('action', 'alliance.newPostExecute();');

		\Gameplay\Panel\Action::getInstance()->add((string) $template);
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 * @param string $values
	 * @throws securityException
	 * @since 2011-03-14
	 */
	static public function sNewExe($values) {

		global $userAlliance, $userID;

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (! allianceRights::sCheck($userID, $userAlliance->AllianceID, 'post')) {
			throw new securityException();
		}

		$data = new stdClass();
		$data->UserID = $userID;
		$data->Date = time();
		$data->AllianceID = $userAlliance->AllianceID;
		$data->Text = xml::sGetValue($values, '<postText>', '</postText>');

		\Database\Controller::getInstance()->quoteAll($data);

		$item = new alliancePost();
		$item->insert($data);

        \phpCache\Factory::getInstance()->create()->clear('alliancePostsRegistry::get',  md5($userAlliance->AllianceID.'|'.serialize(true)));
        \phpCache\Factory::getInstance()->create()->clear('alliancePostsRegistry::get',  md5($userAlliance->AllianceID.'|'.serialize(false)));

		\Gameplay\Panel\Action::getInstance()->add(\Gameplay\Model\Alliance::sGetDetail($userAlliance->AllianceID));
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

}
