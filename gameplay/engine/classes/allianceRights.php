<?php
/**
 * Klasa uprawnień gracza do sojuszu
 * @author Paweł
 * @version $Rev: 460 $
 * @package Engine
 */
class allianceRights extends baseItem {

	protected $tableName = "alliancerights";
	protected $defaultCacheExpire = 120;

	/**
	 * Ustawienie uprawnień gracza w sojuszu
	 * @param int $id
	 * @param string $xml
	 * @throws securityException
	 */
	static public function sPlayerSet($id, $xml) {

		global $userID, $userAlliance;

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (!allianceRights::sCheck($userID, $userAlliance->AllianceID, 'rank')) {
			throw new securityException();
		}

		$tSecondAlliance = userAlliance::quickLoad($id);
		if (empty($tSecondAlliance->AllianceID)) {
			throw new securityException();
		}

		if ($userAlliance->AllianceID != $tSecondAlliance->AllianceID) {
			throw new securityException();
		}

		if (xml::sGetValue($xml, '<editValue>', '</editValue>') == 'true') {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'edit', true);
		}else {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'edit', false);
		}

		if (xml::sGetValue($xml, '<acceptValue>', '</acceptValue>') == 'true') {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'accept', true);
		}else {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'accept', false);
		}

		if (xml::sGetValue($xml, '<kickValue>', '</kickValue>') == 'true') {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'kick', true);
		}else {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'kick', false);
		}

		if (xml::sGetValue($xml, '<cashValue>', '</cashValue>') == 'true') {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'cash', true);
		}else {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'cash', false);
		}

		if (xml::sGetValue($xml, '<relationsValue>', '</relationsValue>') == 'true') {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'relations', true);
		}else {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'relations', false);
		}

		if (xml::sGetValue($xml, '<postValue>', '</postValue>') == 'true') {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'post', true);
		}else {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'post', false);
		}

		if (xml::sGetValue($xml, '<rankValue>', '</rankValue>') == 'true') {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'rank', true);
		}else {
			allianceRights::sSet($id, $tSecondAlliance->AllianceID, 'rank', false);
		}

		allianceRights::sRender();

		\Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'info', TranslateController::getDefault()->get ( 'saved' ) );
	}

	static public function sRenderForm($id) {
		global $userID, $userAlliance;

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (!allianceRights::sCheck($userID, $userAlliance->AllianceID, 'rank')) {
			throw new securityException();
		}

		$tSecondAlliance = userAlliance::quickLoad($id);
		if (empty($tSecondAlliance->AllianceID)) {
			throw new securityException();
		}

		if ($userAlliance->AllianceID != $tSecondAlliance->AllianceID) {
			throw new securityException();
		}

		$item = new userStats ( );
		$otheruserStats = $item->load ( $id, true, true );
		unset($item);

		$item = new userProperties ( );
		$otheruserParameters = $item->load ( $id, true, true );
		unset($item);

		$item = new shipProperties ( );
		$othershipParameters = $item->load ( $id, true, true );
		unset($item);

		$item = new userAlliance ( );
		$othershipAlliance = $item->load ( $id, true, true );
		unset($item);

		$template = new \General\Templater('../templates/allianceRightsForm.html');

		$template->add($otheruserParameters);

		if (allianceRights::sCheck($id, $userAlliance->AllianceID, 'edit')) {
			$template->add('editValue', 'checked');
		}else {
			$template->add('editValue', ' ');
		}
		if (allianceRights::sCheck($id, $userAlliance->AllianceID, 'post')) {
			$template->add('postValue', 'checked');
		}else {
			$template->add('postValue', ' ');
		}
		if (allianceRights::sCheck($id, $userAlliance->AllianceID, 'rank')) {
			$template->add('rankValue', 'checked');
		}else {
			$template->add('rankValue', ' ');
		}
		if (allianceRights::sCheck($id, $userAlliance->AllianceID, 'cash')) {
			$template->add('cashValue', 'checked');
		}else {
			$template->add('cashValue', ' ');
		}
		if (allianceRights::sCheck($id, $userAlliance->AllianceID, 'accept')) {
			$template->add('acceptValue', 'checked');
		}else {
			$template->add('acceptValue', ' ');
		}
		if (allianceRights::sCheck($id, $userAlliance->AllianceID, 'kick')) {
			$template->add('kickValue', 'checked');
		}else {
			$template->add('kickValue', ' ');
		}
		if (allianceRights::sCheck($id, $userAlliance->AllianceID, 'relations')) {
			$template->add('relationsValue', 'checked');
		}else {
			$template->add('relationsValue', ' ');
		}

		$template->add('action',"alliance.setMemberRights('{$id}');");

		\Gameplay\Panel\Action::getInstance()->add((string) $template);

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();

	}

	/**
	 *
	 * Renderowanie listy członków na których można ustawić prawa do sojuszu
	 * @throws securityException
	 */
	static public function sRender() {

		global $userID, $userAlliance;

		if (empty($userAlliance->AllianceID)) {
			throw new securityException();
		}

		if (!allianceRights::sCheck($userID, $userAlliance->AllianceID, 'rank')) {
			throw new securityException();
		}

		/*
		 * Wyrenderowanie sojuszu
		 */
		$registry = new allianceRightsRegistry( $userID );
		$registry->setDisableCache(true);

		\Gameplay\Panel\Action::getInstance()->add($registry->get ($userAlliance->AllianceID));
		\Gameplay\Panel\Action::getInstance()->add("<div style=\"text-align: center;\">" . \General\Controls::bootstrapButton ( '{T:close}', "Playpulsar.gameplay.execute('allianceDetail',null,null,'{$userAlliance->AllianceID}');" ) . "</div>");

		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();

	}

	/**
	 *
	 * Nadanie wszystkich praw usera do sojuszu
	 * @param int $userID
	 * @param int $allianceID
	 */
	static public function sGiveAll($userID, $allianceID) {
		allianceRights::sSet($userID, $allianceID, 'edit', true);
		allianceRights::sSet($userID, $allianceID, 'accept', true);
		allianceRights::sSet($userID, $allianceID, 'kick', true);
		allianceRights::sSet($userID, $allianceID, 'cash', true);
		allianceRights::sSet($userID, $allianceID, 'relations', true);
		allianceRights::sSet($userID, $allianceID, 'post', true);
		allianceRights::sSet($userID, $allianceID, 'rank', true);
	}

	/**
	 * Zabranie wszystkich praw do sojuszu
	 * @param int $userID
	 * @param int $allianceID
	 */
	static public function sGiveNone($userID, $allianceID) {
		allianceRights::sSet($userID, $allianceID, 'edit', false);
		allianceRights::sSet($userID, $allianceID, 'accept', false);
		allianceRights::sSet($userID, $allianceID, 'kick', false);
		allianceRights::sSet($userID, $allianceID, 'cash', false);
		allianceRights::sSet($userID, $allianceID, 'relations', false);
		allianceRights::sSet($userID, $allianceID, 'post', false);
		allianceRights::sSet($userID, $allianceID, 'rank', false);
	}

	/**
	 * Sprawdzenie prawa
	 * @param int $userID
	 * @param int $allianceID
	 * @param string $module
	 * @return boolean
	 */
	static public function sCheck($userID, $allianceID, $module) {

		if (empty($allianceID)) {
			return false;
		}

		$tObject = new allianceRights();
		$retVal = $tObject->load(array('UserID'=>$userID,'AllianceID'=>$allianceID,'Module'=>$module), true, true);
		unset($tObject);
		return $retVal;
	}

	/**
	 * Ustawienie prawa
	 * @param int $userID
	 * @param int $allianceID
	 * @param string $module
	 * @param string $value
	 */
	static public function sSet($userID, $allianceID, $module, $value) {

		$tObject = new allianceRights();
		$retVal = $tObject->load(array('UserID'=>$userID,'AllianceID'=>$allianceID,'Module'=>$module), true, true);
		$tObject->synchronize($value, true, true);
		unset($tObject);

		/*
		 * Wyczyść moduł
		 */
		\Cache\Controller::forceClear($userID,'allianceRights');
	}

	/**
	 * Zapis do cache
	 * @see engine/classes/baseItem::toCache()
	 * @param boolean $useSession
	 * @return boolean
	 */
	function toCache($useSession = false) {

		\Cache\Session::getInstance()->set ( get_class ( $this ), md5(serialize($this->ID)), $this->dataObject, $useSession, $this->defaultCacheExpire );

		return true;
	}

	/**
	 * Utworzenie unkalanego identyfikatora uprawnienia
	 * @param array $data
	 */
	public function createUniqueID(array $data) {

		$retVal = $data['UserID'] . '|'.$data['AllianceID']. '|'.$data['Module'];
		return md5 ( $retVal );
	}

	protected function parseCacheID($ID) {

		return $this->createUniqueID ( $ID );
	}

	/**
	 * Kontruktor
	 * @param array $position
	 */
	function __construct(array $data = null) {

		if (!empty($data)) {
			$this->get ( $data );
		}
	}

	/**
	 * Pobranie obiektu uprawnień
	 * @see engine/classes/baseItem::get()
	 * @param array $data
	 * @throws Exception
	 */
	function get(array $data) {

		$this->dataObject = false;

		if (empty($data['UserID'])) {
			throw new Exception('No user provided');
		}

		if (empty($data['AllianceID'])) {
			throw new Exception('No alliance provided');
		}

		if (empty($data['Module'])) {
			throw new Exception('No module provided');
		}

		$tResult = \Database\Controller::getInstance()->execute ( "
      SELECT
        COUNT(*) AS Ile
      FROM
        alliancerights
      WHERE
        UserID = '{$data['UserID']}' AND
        AllianceID = '{$data['AllianceID']}' AND
        Module = '{$data['Module']}'
      LIMIT 1" );
		if (\Database\Controller::getInstance()->fetch ( $tResult )->Ile > 0 ) {
			$this->dataObject = true;
		}
		$this->ID = $data;
		return true;
	}

	/**
	 * Funkcja parsująca obiekt do zapytania typu UPDATE do bazy danych
	 *
	 * @param boolean $object
	 * @param int $ID - wymuszona nazwa identyfikatora klucza głównego
	 * @return string
	 */
	protected function formatUpdateQuery($object, $ID = null) {

		if ($ID == null) {
			$ID = $this->ID;
		}

		if (!empty($object)) {
			/*
			 * Jest prawo
			 */
			$retVal = "INSERT INTO alliancerights(UserID, AllianceID, Module) VALUES('{$ID['UserID']}','{$ID['AllianceID']}','{$ID['Module']}')";
		}else {
			/*
			 * Brak prawa
			 */
			$retVal = "DELETE FROM alliancerights WHERE UserID='{$ID['UserID']}' AND AllianceID='{$ID['AllianceID']}' AND Module='{$ID['Module']}'";
		}

		return $retVal;
	}

	/**
	 * (non-PHPdoc)
	 * @see engine/classes/baseItem::give()
	 * @return boolean
	 */
	public function give() {
		return $this->dataObject;
	}

	/**
	 *
	 * Pobranie wszystkich członków sojuszu posiadających określone prawo
	 * @param int $allianceID
	 * @param string $module
	 * @return array
	 * @since 2010-07-27
	 */
	static public function sGetMembersWithRight($allianceID, $module) {

		//@todo wykorzystać cache

		$retVal = array();

		$tQuery = "SELECT
					am.UserID
				FROM
					alliancemembers AS am JOIN alliancerights AS ar ON am.UserID=ar.UserID AND am.AllianceID=ar.UserID
				WHERE
					ar.AllianceID='{$allianceID}' AND
					ar.Module='{$module}'
				GROUP BY
					UserID
				";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);
		while ($tResult = \Database\Controller::getInstance()->fetch($tQuery)) {
			array_push($retVal, $tResult->UserID);
		}

		return $retVal;
	}

}