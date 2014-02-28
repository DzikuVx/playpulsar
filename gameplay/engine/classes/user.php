<?php
/**
 * Klasa gracza
 *
 * @version $Rev: 460 $
 * @package Engine
 *
 */
class user {

	/**
	 * Wykonanie restetu statku, wersja bez czyszczenia cache
	 * @param int $playerID
	 */
	static public function sAccountReset($playerID) {

		global $config;

		//@todo połączyć to w jedno z metodą player::sDrop
		\Database\Controller::getInstance()->execute ( "DELETE FROM portcargo WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM shipcargo WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM shipequipment WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM shiprouting WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM shippositions WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM shipweapons WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM usermaps WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM userportcargo WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM userships WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM userstats WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM userfasttimes WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM usertimes WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM alliancemembers WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM alliancerequests WHERE UserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM alliancefinance WHERE UserID='{$playerID}' OR ForUserID='{$playerID}'" );
		\Database\Controller::getInstance()->execute ( "DELETE FROM newsagency WHERE UserID='{$playerID}' OR ByUserID='{$playerID}'" );

		$tUserParams = new \Gameplay\Model\UserEntity($playerID, false);

		/*
		 * System
		*/
		$position = new \Gameplay\Model\ShipPosition();
		$position->System = additional::randFormList ( $config ['userDefault'] ['system'] );

		$tPosition = \Gameplay\Model\SystemProperties::randomPort ( $position );
		$position->X = $tPosition->X;
		$position->Y = $tPosition->Y;
		$position->Docked = 'yes';

		/*
		 * Wstaw pozycję statku
		*/
		$tQuery2 = "INSERT INTO
        shippositions(
          UserID,
          System,
          X,
          Y,
          Docked)
        VALUES(
          '$playerID',
          '{$position->System}',
          '{$position->X}',
          '{$position->Y}',
          '{$position->Docked}'
        )
    ";
		\Database\Controller::getInstance()->execute ( $tQuery2 );

		$tUserParams->Name = \Database\Controller::getInstance()->quote($tUserParams->Name);

		/*
		 * Wstaw tabelę userships
		*/
		$tQuery2 = "INSERT INTO
        userships(
          UserID,
          SpecializationID,
          RookieTurns,
          ShipID,
          ShipName,
          Turns
          )
        VALUES(
          '$playerID',
          '{$config ['userDefault'] ['specialization']}',
          '{$config ['userDefault'] ['rookie']}',
          '{$config ['userDefault'] ['ship']}',
          '{$tUserParams->Name}',
          '{$config ['userDefault'] ['turns']}'
        )
    ";
		\Database\Controller::getInstance()->execute ( $tQuery2 );

		/*
		 * Wstaw tablelę userstats
		*/
		$tQuery2 = "INSERT INTO
        userstats(
          UserID,
          Cash,
          Experience,
          Level
          )
        VALUES(
          '$playerID',
          '" . $config ['userDefault'] ['cash'] . "',
          '" . $config ['userDefault'] ['experience'] . "',
          '" . \Gameplay\Model\UserStatistics::computeLevel ( $config ['userDefault'] ['experience'] ) . "'
        )
    ";
		\Database\Controller::getInstance()->execute ( $tQuery2 );

		/*
		 * Wstaw tablelę usertimes
		*/
		$tQuery2 = "INSERT INTO
        usertimes(
          UserID,
          TurnReset
          )
        VALUES(
          '$playerID',
          '0'
        )
    ";
		\Database\Controller::getInstance()->execute ( $tQuery2 );

		/*
		 * Wygeneruj uzbrojenie
		*/
		$weaponsCount = self::sInsertWeaponsSet($playerID, $config ['userDefault'] ['weapons']);

		/*
		 * Wygeneruj equipment
		*/
		$equipmentCount = self::sInsertEquipmentSet($playerID, $config ['userDefault'] ['equipment']);

		/*
		 * Załadowanie danych okrętu
		*/
		$shipProperties = new \Gameplay\Model\ShipProperties($playerID, false);

		$shipProperties->CurrentWeapons = $weaponsCount;
		$shipProperties->CurrentEquipment = $equipmentCount;

		/**
		 * Przelicz OFF RATING
		 */
		$shipWeapons = new \Gameplay\Model\ShipWeapons($playerID, $tUserParams->Language);
		$shipWeapons->computeOffensiveRating ( $shipProperties );

		/**
		 * Uaktualnij wartości maksymalne okrętu
		 */
        $shipProperties->computeMaxValues();

		/**
		 * Ustaw aktualne maksymalne jako aktualne
		 */
        $shipProperties->setFromFull();
        $shipProperties->computeDefensiveRating();
		$shipProperties->synchronize();
	}

	/**
	 * Pobranie liczby zalogowanych graczy
	 * @return int
	 */
	static public function sGetOnlineCount() {

		global $config;

		try {

			$oCacheKey = new \phpCache\CacheKey('user::sGetOnlineCount', '');
            $oCache    = \phpCache\Factory::getInstance()->create();

			if (!$oCache->check($oCacheKey)) {

				$tQuery = "SELECT COUNT(users.UserID) AS ILE FROM usertimes JOIN users USING(UserID) JOIN userstats USING(UserID) WHERE users.Type='player' AND usertimes.LastAction>'" . (time () - $config ['user'] ['onlineThreshold']) . "'";
				$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
				$retVal = \Database\Controller::getInstance()->fetch($tQuery)->ILE;

				$oCache->set($oCacheKey, $retVal, 120);

			}else {
				$retVal = $oCache->get($oCacheKey);
			}

		}catch (Exception $e) {
			psDebug::cThrow(null, $e, array('display'=>false));
			$retVal = 0;
		}

		return $retVal;
	}

	/**
	 * Edycja własnych danych
	 * @param string $xml
	 * @throws securityException
	 */
	static public function sEditOwnExe($xml) {
        $userProperties = \Gameplay\PlayerModelProvider::getInstance()->get('UserEntity');

		$pA = xml::sGetValue($xml,'<passwordA>','</passwordA>' );
		$pB = xml::sGetValue($xml,'<passwordB>','</passwordB>' );

		if ($pA != '' && $pB != '') {

			if ($pA != $pB) {
				throw new securityException();
			}

			if (mb_strlen($pA) < 6) {
				throw new securityException();
			}

			$userProperties->Password = self::sPasswordHash($userProperties->Login, $pA);
		}

		$userProperties->Name = \Database\Controller::getInstance()->quote(xml::sGetValue($xml,'<userName>','</userName>' ));
		$userProperties->Country = \Database\Controller::getInstance()->quote(xml::sGetValue($xml,'<userCountry>','</userCountry>' ));

		if (\Database\Controller::getInstance()->quote(xml::sGetValue($xml,'<spamCheckbox>','</spamCheckbox>' )) == 'true') {
			$userProperties->AllowSpam = 'yes';
		}else{
			$userProperties->AllowSpam = 'no';
		}

		if (\Database\Controller::getInstance()->quote(xml::sGetValue($xml,'<userLanguage>','</userLanguage>' )) == 'pl') {
			$userProperties->Language = 'pl';
		}else{
			$userProperties->Language = 'en';
		}

        $userProperties->synchronize();
		\Gameplay\Model\UserStatistics::sExamineMe();
		\Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', '{T:opSuccess}');
	}

	static public function sEditOwnDialog() {
        $userProperties = \Gameplay\PlayerModelProvider::getInstance()->get('UserEntity');

		$template = new \General\Templater('../templates/userDataForm.html');

		$template->add ( 'countryInput', \General\Controls::renderInput ( 'text', $userProperties->Country, 'userCountry', 'userCountry', 32 ) );
		$template->add ( 'nameInput', \General\Controls::renderInput ( 'text', $userProperties->Name, 'userName', 'userName', 32 ) );

		if (!empty($userProperties->FacebookID)) {
			$tValue = '-';
		} else {
			$tValue = $userProperties->Login;
		}

		$template->add ( 'loginInput', $tValue );
		$template->add ( 'emailInput',  $userProperties->Email);
		$template->add ( 'passwordAInput', \General\Controls::renderInput ( 'password', '', 'passwordA', 'passwordA', 32 ) );
		$template->add ( 'passwordBInput', \General\Controls::renderInput ( 'password', '', 'passwordB', 'passwordB', 32 ) );
		$tParams = array ();
		$tParams ['pl'] = 'Polski';
		$tParams ['en'] = 'English';
		$template->add ( 'languageInput', \General\Controls::renderSelect ( 'userLanguage', $userProperties->Language, $tParams, array('id' =>'userLanguage', 'class'=>'ui-corner-all', 'style'=>'background-color: #323232; color: #999;') ) );

		if ($userProperties->AllowSpam == 'yes') {
			$tValue = true;
		}else {
			$tValue = false;
		}

		$template->add ( 'spamInput', \General\Controls::renderInput ( 'checkbox', $tValue, 'spamCheckbox', 'spamCheckbox' ) );
		$template->add ( 'saveButton', \General\Controls::renderButton ( TranslateController::getDefault()->get ( 'Save' ), 'user.editExecute();', null, 'closeButton' ) );

		\Gameplay\Panel\Action::getInstance()->add((string) $template);
		\Gameplay\Panel\SectorShips::getInstance()->hide ();
		\Gameplay\Panel\SectorResources::getInstance()->hide ();
		\Gameplay\Panel\PortAction::getInstance()->clear();
	}

	/**
	 * Wstawienie equipu dla użytkownika
	 * @param int $playerID
	 * @param array|string $equipmentSet tablica equipementID lub string oddzielony przecinkami
	 * @return int liczba equipu
	 */
	static public function sInsertEquipmentSet($playerID, $equipmentSet = '') {

		$equipmentCount = 0;

		if (! empty ( $equipmentSet )) {

			if (is_array($equipmentSet)) {
				$tEquipments = $equipmentSet;
			}else {
				$tEquipments = explode ( ",", $equipmentSet );
			}

			\Database\Controller::getInstance()->disableAutocommit();

			foreach ( $tEquipments as $value ) {
				//@todo dodać prepared query
				$tQuery2 = "INSERT INTO
           shipequipment(
             UserID,
             EquipmentID
             )
          VALUES(
             '$playerID',
             '$value'
          )";
				\Database\Controller::getInstance()->execute ( $tQuery2 );
				$equipmentCount ++;
			}

			\Database\Controller::getInstance()->commit();
			\Database\Controller::getInstance()->enableAutocommit();

		}

		return $equipmentCount;
	}

    /**
     * @param $playerID
     * @param Array|string $weaponsSet
     * @return int
     */
    static public function sInsertWeaponsSet($playerID, $weaponsSet = '') {

		$weaponsCount = 0;

		if (! empty ( $weaponsSet )) {

			if (is_array($weaponsSet)) {
				$tWeapons = $weaponsSet;
			}else {
				$tWeapons = explode ( ",", $weaponsSet );
			}

			$sequence = 0;

			\Database\Controller::getInstance()->disableAutocommit();

			foreach($tWeapons as $value) {
                $weapon = new \Gameplay\Model\WeaponType($value);

				if ($weapon->Ammo == null) {
					$weapon->Ammo = "null";
				} else {
					$weapon->Ammo = "'" . $weapon->Ammo . "'";
				}
				//FIXME dodać prepared query
				$sequence ++;
				$tQuery2 = "INSERT INTO
                       shipweapons(
                         UserID,
                         WeaponID,
                         Ammo,
                         Sequence)
                      VALUES(
                         '$playerID',
                         '$value',
                         " . $weapon->Ammo . ",
                         '$sequence'
                      )
                    ";
				\Database\Controller::getInstance()->execute ( $tQuery2 );
				$weaponsCount ++;
			}

			\Database\Controller::getInstance()->commit();
			\Database\Controller::getInstance()->enableAutocommit();

		}

		return $weaponsCount;
	}

	/**
	 * hashowanie hasła
	 * @param string $login
	 * @param string $password
	 * @return string
	 */
	static public function sPasswordHash($login, $password) {
		return sha1($login.':'.$password);
	}

	/**
	 * wstawienie wpisów do tabel
	 * @param array $params
	 * @param \Gameplay\Model\UserEntity $tUsers
	 * @since 2011-03-20
	 */
	static private final function sInsert($params, \Gameplay\Model\UserEntity $tUsers) {

		global $config;

		/*
		 * Dokonaj wstawienia do tabeli users
		*/
		$playerID = $tUsers->insert();

		/*
		 * System
		*/
		$position = new \Gameplay\Model\ShipPosition();
		$position->System = additional::randFormList ( $config ['userDefault'] ['system'] );

		$tPosition = \Gameplay\Model\SystemProperties::randomPort ( $position );
		$position->X = $tPosition->X;
		$position->Y = $tPosition->Y;
		$position->Docked = 'yes';

		unset ( $tPosition );

		/*
		 * Wstaw pozycję statku
		*/
		$tQuery2 = "INSERT INTO
                shippositions(
                    UserID,
                    System,
                    X,
                    Y,
                    Docked)
                VALUES(
                    '$playerID',
                    '{$position->System}',
                    '{$position->X}',
                    '{$position->Y}',
                    '{$position->Docked}'
                )";
		\Database\Controller::getInstance()->execute ( $tQuery2 );

		/*
		 * Wstaw tabelę userships
		*/
		$tQuery2 = "INSERT INTO
                userships(
                  UserID,
                  SpecializationID,
                  RookieTurns,
                  ShipID,
                  ShipName,
                  Turns
                  )
                VALUES(
                  '$playerID',
                  '{$config ['userDefault'] ['specialization']}',
                  '{$config ['userDefault'] ['rookie']}',
                  '{$config ['userDefault'] ['ship']}',
                  '{$params['name']}',
                  '{$config ['userDefault'] ['turns']}'
                )
            ";
		\Database\Controller::getInstance()->execute ( $tQuery2 );

		/*
		 * Wstaw tablelę userstats
		*/
		$tQuery2 = "INSERT INTO
                userstats(
                  UserID,
                  Cash,
                  Experience,
                  Level
                  )
                VALUES(
                  '$playerID',
                  '" . $config ['userDefault'] ['cash'] . "',
                  '" . $config ['userDefault'] ['experience'] . "',
                  '" . \Gameplay\Model\UserStatistics::computeLevel ( $config ['userDefault'] ['experience'] ) . "'
                )
            ";
		\Database\Controller::getInstance()->execute ( $tQuery2 );

		/*
		 * Wstaw tablelę usertimes
		*/
		$tQuery2 = "INSERT INTO
        usertimes(
          UserID,
          TurnReset
          )
        VALUES(
          '$playerID',
          '".time()."'
        )
    ";
		\Database\Controller::getInstance()->execute ( $tQuery2 );

		/*
		 * Wygeneruj uzbrojenie
		*/
		$weaponsCount = self::sInsertWeaponsSet($playerID, $config ['userDefault'] ['weapons']);

		/*
		 * Wygeneruj equipment
		*/
		$equipmentCount = self::sInsertEquipmentSet($playerID, $config ['userDefault'] ['equipment']);

		/*
		 * Załadowanie danych okrętu
		*/
		$shipProperties = new \Gameplay\Model\ShipProperties($playerID);

		$shipProperties->CurrentWeapons   = $weaponsCount;
		$shipProperties->CurrentEquipment = $equipmentCount;

		/**
		 * Przelicz OFF RATING
		 */
		$shipWeapons = new \Gameplay\Model\ShipWeapons( $playerID, $params ['language'] );
		$shipWeapons->computeOffensiveRating($shipProperties);

		/**
		 * Uaktualnij wartości maksymalne okrętu
		 */
        $shipProperties->computeMaxValues();

		/**
		 * Ustaw aktualne maksymalne jako aktualne
		 */
        $shipProperties->setFromFull();
        $shipProperties->computeDefensiveRating();
		$shipProperties->synchronize();
	}

	/**
	 * @param string $name
	 * @param array $fbMe
	 * @throws Exception
	 * @return string
	 * @since 2011-03-20
	 */
	static public function sCreateAccountFromFb($name, $fbMe) {

		try {

			/*
			 * Wstaw gracza
			*/

			$params = array();

			$params['name'] = $name;

			$tUsers = new \Gameplay\Model\UserEntity();
			$tUsers->Password = self::sPasswordHash(uniqid(), uniqid());
			$tUsers->Login = uniqid();
			$tUsers->Email = $fbMe ['email'];
			$tUsers->Name = $params['name'];
			$tUsers->UserLocked = 'no';
			$tUsers->UserActivated = 'yes';
			$tUsers->Country = ' ';
			if ($fbMe['locale'] == 'pl_PL') {
				$params ['language'] = 'pl';
			}else {
				$params ['language'] = 'en';
			}
			$tUsers->Language = $params ['language'];
			$tUsers->About = ' ';
			$tUsers->AllowSpam = 'yes';
			$tUsers->Type = 'player';
			$tUsers->NPCTypeID = null;
			$tUsers->FacebookID = $fbMe['id'];

			self::sInsert($params, $tUsers);

		} catch ( customException $e ) {
			$retVal = $e->getMessage ();
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage (), $e->getCode () );
		}

		$retVal = 'OK';
		return $retVal;
	}

	static public function sRenderFbForm(\General\Templater $template, $fbMe) {
		$formTemplate = new \General\Templater('templates/fbAccountForm.html');
		$formTemplate->add('fbName', $fbMe['name']);
		$template->add('title',TranslateController::getDefault()->get('fbNewAccount'));
		$template->add('text',(string) $formTemplate);
		$template->add('action',\General\Controls::bootstrapButton(TranslateController::getDefault()->get('continue'), "document.forms['fbCreate'].submit();",'btn-primary','icon-ok'));
	}

	/**
	 * Pobranie URL do zdjęcia gracza zalogowanego z FB
	 * @param int $facebookID
	 * @return string
	 */
	static public function sGetFbPictureUrl($facebookID) {

		global $config;

		try {

			$oCacheKey = new \phpCache\CacheKey('user::sGetFbPictureUrl', $facebookID);
            $oCache    = \phpCache\Factory::getInstance()->create();

			if (!$oCache->check($oCacheKey)) {

				$fb=new Facebook(array(
                    'appId'  => $config['facebook']['appId'],
                    'secret' => $config['facebook']['secret'],
                    'cookie' => $config['facebook']['cookie']
				));

				$data = $fb->api($facebookID, array(
                    'fields' => 'picture',
                    'type' => 'large'
				));

				$retVal = $data['picture'];

				$oCache->set($oCacheKey, $retVal, 86400);

			}else {
				$retVal = $oCache->get($oCacheKey);
			}
		}catch (Exception $e) {
			psDebug::cThrow(null, $e, array('display'=>false));
			$retVal = null;
		}

		return $retVal;
	}

	/**
	 * Połączenie z facebookiem
	 */
	static public function sFbConnect() {

		global $config;

		$fb = new Facebook(array(
          'appId'  => $config['facebook']['appId'],
          'secret' => $config['facebook']['secret'],
          'cookie' => $config['facebook']['cookie']
		));

		$user = $fb->getUser();

        $fbMe = null;

		if ($user) {
			try {
				$fbMe = $fb->api('/me');
			} catch (FacebookApiException $e) {
				$fbMe = null;
			}
		}else {
			psDebug::halt('Unable to obtain Facebook data', null, array('send'=>false, 'display'=>false));
		}

		if (empty($fbMe)) {
			psDebug::halt('Unable to obtain Facebook data', null, array('send'=>false, 'display'=>false));
		}

		return $fbMe;
	}

	static public function sGetRole() {
		return $_SESSION ['cpLoggedUserRole'];
	}

	/**
	 * Listener logowania użytkownika
	 *
	 * @param array $params
	 * @return string
	 */
	static function sLoginListener(&$params) {

		$retVal = '';

		if (! empty ( $params ['doLogin'] ) && $params ['doLogin'] == 'true') {

			try {

				$params ['doLogout'] = null;

				/*
				 * Procedura logowania
				*/
				$tResult = null;
				$tQuery = "SELECT users.UserID, users.Name, userpermissions.Type FROM users JOIN userpermissions USING(UserID) WHERE users.UserLocked='no' AND userpermissions.Type!='player' AND users.Login='" . \Database\Controller::getInstance()->quote ( $params ['loginName'] ) . "' AND users.Password='" . self::sPasswordHash(\Database\Controller::getInstance()->quote ( $params ['loginName'] ), \Database\Controller::getInstance()->quote ( $params ['loginPassword'] ))  . "'";
				$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
				$tResult = \Database\Controller::getInstance()->fetch ( $tQuery );

				if (empty ( $tResult )) {
					throw new customException ( 'Unknown login od bad password' );
				}

				$_SESSION ['cpLoggedUserID'] = $tResult->UserID;
				$_SESSION ['cpLoggedUserName'] = $tResult->Name;
				$_SESSION ['cpLoggedUserRole'] = $tResult->Type;

			} catch ( customException $e ) {
				$retVal = \General\Controls::displayErrorDialog ( $e->getMessage () );
			} catch ( Exception $e ) {
				$retVal = psDebug::cThrow ( null, $e, array ('display' => true ) );
			}
		}

		return $retVal;
	}

    /**
     * @param $params
     * @return string
     */
    static function sLogoutListener($params) {

		$retVal = '';

		if (! empty ( $params ['doLogout'] ) && $params ['doLogout'] == 'true') {

			$_SESSION ['cpLoggedUserID'] = null;
			$_SESSION ['cpLoggedUserName'] = '';
			$_SESSION ['cpLoggedUserRole'] = null;
		}

		return $retVal;
	}

	/**
	 * Rejestracja gracza, formularz rejestracji
	 *
	 * @param array $params
	 * @return string
	 */
	public function register(/** @noinspection PhpUnusedParameterInspection */
        $params) {

		$template = new \General\Templater ( 'templates/register.html' );
		return ( string ) $template;
	}

    /**
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function registerExecute($params) {

        $template = new \General\Templater ( 'templates/general.html' );

        $oDb = \Database\Controller::getInstance();

        try {
            $oDb->quoteAll($params);

            /*
             * Sprawdź, czy login nie jest już wykorzystywany
            */
            $tUserCount = 0;
            $tQuery = "SELECT COUNT(*) AS ILE FROM users WHERE Login='{$params['login']}'";
            $tQuery = $oDb->execute ( $tQuery );
            while ( $tResult = $oDb->fetch ( $tQuery ) ) {
                $tUserCount = $tResult->ILE;
            }

			if ($tUserCount != 0) {
				throw new customException ( TranslateController::getDefault()->get ( 'loginAlreadyUsed' ) );
			}

			$tQuery = "SELECT COUNT(*) AS ILE FROM users WHERE Email='{$params['email']}'";
			$tQuery = $oDb->execute ( $tQuery );
			while ( $tResult = $oDb->fetch ( $tQuery ) ) {
				$tUserCount = $tResult->ILE;
			}

			if ($tUserCount != 0) {
				throw new customException ( TranslateController::getDefault()->get ( 'emailAlreadyUsed' ) );
			}

			/*
			 * Dokonaj wstawienia do tabeli users
			*/
			$tUsers = new \Gameplay\Model\UserEntity();
			$tUsers->Password = self::sPasswordHash($params ['login'], $params ['passwordA']);
			$tUsers->Login = $params ['login'];
			$tUsers->Email = $params ['email'];
			$tUsers->Name = $params ['name'];
			$tUsers->UserLocked = 'no';
			$tUsers->UserActivated = 'yes';
			$tUsers->Country = $params ['country'];
			$tUsers->Language = $params ['language'];
			$tUsers->About = ' ';

			if (! empty ( $params ['spam'] )) {
				$params ['spam'] = 'yes';
			} else {
				$params ['spam'] = 'no';
			}

			$tUsers->AllowSpam = $params ['spam'];
			$tUsers->Type = 'player';
			$tUsers->NPCTypeID = null;

			self::sInsert($params, $tUsers);

		} catch ( customException $e ) {
			$template->reset();
			$template->add('text', $e->getMessage());
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage (), $e->getCode () );
		}

		$template->add ( 'text', TranslateController::getDefault()->get ( 'accountCreated' ) );

		$retVal = ( string ) $template;

		return $retVal;
	}

}