<?php

namespace Controlpanel;

use Gameplay\Model\ShipPosition;
use Gameplay\Model\SystemProperties;

class NpcTypes extends GameplayItem{

	protected $detailTitle = 'NPC Type Data';
	protected $editTitle = '';
	protected $addTitle = '';

	protected $tableName = 'npctypes';
	protected $tableIdField = 'NPCTypeID';

	public function editExe($user, $params) {
		$retVal = '';

		if (empty($_SESSION['returnUser'])) {
			throw new customException('Security error');
		}

		$tData = $this->getDataObject($params['id']);

		$tFields = array();
		foreach ($tData as $tKey => $tValue) {
			if ($tKey != 'NPCTypeID' && $tKey!='AllianceName') {
				array_push($tFields, $tKey);
			}
		}

		$tQuery = BaseItem::sMakeUpdateQuery('npctypes', 'NPCTypeID', $tFields, $params);
		\Database\Controller::getInstance()->execute($tQuery);
        \npc::sFlushCache($params['id']);
		\General\Controls::reloadWithMessage(\General\Session::get('returnLink'), "Data has been <strong>set</strong>", 'success');

		return $retVal;
	}

	/**
	 * (non-PHPdoc)
	 * @see Controlpanel.BaseItem::deleteExe()
	 */
	public function deleteExe($user, $params) {

		self::sDropType($params ['id']);
		return parent::deleteExe($user, $params);
	}

	/**
	 * Zrzucenie wszystkich NPC typu autopopulate
	 */
	static private function sDropAll() {
			
		set_time_limit(3600);

		$tQuery = "SELECT NPCTypeID FROM npctypes WHERE AutoPopulate='yes'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $row1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {

			$tQuery2 = "SELECT UserID FROM users WHERE NPCTypeID='{$row1->NPCTypeID}'";
			$tQuery2 = \Database\Controller::getInstance()->execute ( $tQuery2 );
            while ($row2 = \Database\Controller::getInstance()->fetch ( $tQuery2 ) ) {
				Player::sDrop($row2->UserID);
			}

		}

		return true;
	}

	static private function sDropType($id) {
			
		set_time_limit(3600);

		$tQuery2 = "SELECT UserID FROM users WHERE NPCTypeID='{$id}'";
		$tQuery2 = \Database\Controller::getInstance()->execute ( $tQuery2 );
		while ( $row2 = \Database\Controller::getInstance()->fetch ( $tQuery2 ) ) {
			player::sDrop($row2->UserID);
		}

		return true;
	}


	final public function createAll($user, $params) {

		global $config;

		return \General\Controls::dialog( "Confirm", "Do you want to <strong>create all NPC</strong>?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=createAllExe'", "window.history.back();", 'Yes','No' );
	}

	final public function createAllExe($user, $params) {

		global $config;

		self::sCreate();

		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=browse", "Operation completed");
	}

	final public function dropAll($user, $params) {

		global $config;

		return \General\Controls::dialog( "Confirm", "Do you want to <strong>drop all NPC</strong>?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=dropAllExe'", "window.history.back();", 'Yes','No' );
	}

	final public function dropAllExe($user, $params) {

		global $config;

		self::sDropAll();

		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=browse", "Operation completed");
	}

	final public function drop($user, $params) {

		global $config;

		return \General\Controls::dialog( "Confirm", "Do you want to <strong>drop selected NPC</strong>?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=dropExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );
	}

	final public function dropExe($user, $params) {

		global $config;

		self::sDropType($params['id']);

		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}", "Operation completed");
	}

	final public function create($user, $params) {

		global $config;

		return \General\Controls::dialog( "Confirm", "Do you want to <strong>create selected NPC</strong>?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=createExe&id={$params['id']}'", "window.history.back();", 'Yes','No' );
	}

	final public function createExe($user, $params) {

		global $config;

		self::sCreate($params['id']);

		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail&id={$params['id']}", "Operation completed");
	}

	/**
	 * Pełne utworzenie NPC
	 */
	static private function sCreate($typeID = null) {

        //FIXME replace getInstance with single variable

		set_time_limit(3600);

		$out = "";

		$tHandle = \Database\Controller::getInstance()->getHandle();
		if (empty($tHandle)) {
			\Database\Controller::getInstance()->connect();
		}

		$sPreparedWeapons = mysqli_prepare(\Database\Controller::getInstance()->getHandle(), "INSERT INTO
		  	   shipweapons(
		  	     UserID,
		  	     WeaponID,
		  	     Ammo,
		  	     Sequence)
		  	  VALUES(
		  	     ?,
		  	     ?,
		  	     ?,
		  	     ?
		  	  )");

		$sPreparedEquipment = mysqli_prepare(\Database\Controller::getInstance()->getHandle(), "INSERT INTO
           shipequipment(
             UserID,
             EquipmentID
             )
          VALUES(
             ?,
             ?
          )");

		$userParameters ['Language'] = 'pl';

		$userProperties = new \stdClass();
		$userProperties->Language = 'pl';

		/*
		 * Zrzuć wszystkich NPC
		*/
		if (empty($typeID)) {
			self::sDropAll();
			$tCondition = '';
		}else {
			self::sDropType($typeID);
			$tCondition = " AND NPCTypeID='{$typeID}' ";
		}

		$tQuery = "SELECT * FROM npctypes WHERE AutoPopulate='yes' ".$tCondition;
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
		while ( $row1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
			/*
			 * Zrzuć wszystkich graczy tego typu
			*/

			if ($row1->AllianceID == null) {
				$row1->AllianceID = "null";
			} else {
				$row1->AllianceID = "'" . $row1->AllianceID . "'";
			}

			/*
			 * Rozpocznij pętlę
			*/
			for($tIndex = 1; $tIndex <= $row1->UniverseNumber; $tIndex ++) {

				/*
				 * Wylosuj system dla gracza
				*/
				$position = new ShipPosition();

				if ($row1->Systems == "all") {
					$position->System = \galaxy::sGetRandomSystem ();
				} else {
					$position->System = \additional::randFormList ( $row1->Systems );
				}

				$tPosition = SystemProperties::randomPosition ( $position->System );

                $position->X = $tPosition->X;
				$position->Y = $tPosition->Y;
				$position->Docked = 'no';
				unset ( $tPosition );

				if ($row1->RandomName == "yes") {
					$npcName = \additional::getRandomName ();
				} else {
					$npcName = $row1->Name;
				}

				/*
				 * Dokonaj wstawienia do tabeli users
				*/
				$tUsers = new \stdClass();
				$tUsers->Password = \user::sPasswordHash( uniqid () , uniqid () );
				$tUsers->Login = uniqid ();
				$tUsers->Email = ' ';
				$tUsers->Name = $npcName;
				$tUsers->UserLocked = 'no';
				$tUsers->UserActivated = 'yes';
				$tUsers->Country = 'Pulsar Universe';
				$tUsers->Language = 'en';
				$tUsers->About = ' ';
				$tUsers->AllowSpam = 'no';
				$tUsers->Type = 'npc';
				$tUsers->NPCTypeID = $row1->NPCTypeID;

				\Database\Controller::getInstance()->quoteAll($tUsers);

				$npcID = \userProperties::quickInsert ( $tUsers );

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
		      '$npcID',
		      '{$position->System}',
		      '{$position->X}',
		      '{$position->Y}',
		      'no'
		    )
		";
				\Database\Controller::getInstance()->execute ( $tQuery2 );

				/*
				 * Wstaw tabelę userships
				*/
				$tName = \Database\Controller::getInstance()->quote($row1->Name);
				$tQuery2 = "INSERT INTO
        userships(
          UserID,
          SpecializationID,
          RookieTurns,
          ShipID,
          ShipName
          )
        VALUES(
          '$npcID',
          null,
          '0',
          '{$row1->ShipID}',
          '{$tName}'
        )
    ";
				\Database\Controller::getInstance()->execute ( $tQuery2 );

				/*
				 * Zapisz od sojuszu
				*/
				if ($row1->AllianceID != "null") {
					$tQuery2 = "INSERT INTO
                        alliancemembers(
                          UserID,
                          AllianceID
                          )
                        VALUES(
                          '$npcID',
                          " . $row1->AllianceID . "
                        )
                        ";
					\Database\Controller::getInstance()->execute ( $tQuery2 );
				}
				/*
				 * Wstaw tablelę userstats
				*/
				$tExperience = \additional::randomizeValue ( \userStats::computeExperience($row1->Level), 10, 1000 );
				$tQuery2 = "INSERT INTO
                    userstats(
                      UserID,
                      Cash,
                      Experience,
                      Level
                      )
                    VALUES(
                      '$npcID',
                      '" . \additional::randomizeValue ( $row1->Cash, 20, 1000 ) . "',
                      '" . $tExperience . "',
                      '" . \userStats::computeLevel ( $tExperience ) . "'
                    )
                ";
				\Database\Controller::getInstance()->execute ( $tQuery2 );

				/*
				 * Wstaw tablelę usertimes
				*/
				$tQuery2 = "INSERT INTO
                    usertimes(
                      UserID
                      )
                    VALUES(
                      '$npcID'
                    )
                ";
				\Database\Controller::getInstance()->execute ( $tQuery2 );

				/*
				 * Wygeneruj uzbrojenie
				*/

				//@todo user::sInsertWeaponsSet
				$weaponsCount = 0;
				if ($row1->Weapons != "") {
					$tWeapons = explode ( ",", $row1->Weapons );

					$sequence = 0;
					$weaponsCount = 0;
					\Database\Controller::getInstance()->disableAutocommit();
					foreach ( $tWeapons as $value ) {

						$weapon = \weapon::quickLoad ( $value );

						$sequence ++;

						mysqli_stmt_bind_param($sPreparedWeapons, 'iiii', $npcID, $value, $weapon->Ammo, $sequence);
						$tResult = mysqli_stmt_execute($sPreparedWeapons);

						if (empty($tResult)) {
							throw new \Database\Exception ( mysqli_error (\Database\Controller::getInstance()->getHandle()), mysqli_errno (\Database\Controller::getInstance()->getHandle()) );
						}

						$weaponsCount ++;
					}
					\Database\Controller::getInstance()->commit();
					\Database\Controller::getInstance()->enableAutocommit();
				}

				/*
				 * Wygeneruj equipment
				*/
				//@todo user::sInsertEquipmentSet
				$equipmentCount = 0;
				if ($row1->Equipment != "") {
					$tEquipments = explode ( ",", $row1->Equipment );
					$equipmentCount = 0;
					\Database\Controller::getInstance()->disableAutocommit();
					foreach ( $tEquipments as $value ) {

						mysqli_stmt_bind_param($sPreparedEquipment, 'ii', $npcID, $value);
						$tResult = mysqli_stmt_execute($sPreparedEquipment);

						if (empty($tResult)) {
							
							var_dump($sPreparedEquipment, $npcID, $value);
							die();
							
							throw new \Database\Exception ( mysqli_error (\Database\Controller::getInstance()->getHandle()), mysqli_errno (\Database\Controller::getInstance()->getHandle()) );
						}

						$equipmentCount ++;
					}
					\Database\Controller::getInstance()->commit();
					\Database\Controller::getInstance()->enableAutocommit();

				}

				/*
				 * Załadowanie danych okrętu
				*/

				$shipPropertiesObject = new \shipProperties ( );
				$shipProperties = $shipPropertiesObject->load ( $npcID, true, true );

				$shipProperties->CurrentWeapons = $weaponsCount;
				$shipProperties->CurrentEquipment = $equipmentCount;

				/**
				 * Przelicz OFF RATING
				 */
				$shipWeapons = new \shipWeapons ( $npcID, 'pl' );
				$shipWeapons->computeOffensiveRating ( $shipProperties );
				unset($shipWeapons);

				$shipEquipment = new \shipEquipment ( $npcID, 'pl' );

				/**
				 * Uaktualnij wartości maksymalne okrętu
				 */
				\shipProperties::computeMaxValues ( $shipProperties );

				/**
				 * Ustaw aktualne maksymalne jako aktualne
				 */
				\shipProperties::setFromFull ( $shipProperties );

				\shipProperties::computeDefensiveRating ( $shipProperties );

				/*
				 * Jeśli NPC ma itemy ładowniach
				*/
				if ($row1->HaveItems == 'yes') {
					$tItems = \item::getRand ( \additional::rand ( 0, 5 ) );
					$tCargo = new \shipCargo ( $npcID, 'pl' );

					foreach ( $tItems as $value ) {
						$tCargo->setAmount ( $value, 'item', \additional::rand ( 1, 2 ) );
					}

					unset($tCargo);
				}

				if ($row1->HaveCargo == 'yes') {
					$tValue = \additional::rand ( 1, 3 );
					$tItems = \product::getRand ( $tValue );
					$tCargo = new \shipCargo ( $npcID, 'pl' );

					$tValue = floor ( $shipProperties->CargoMax / $tValue );

					foreach ( $tItems as $value ) {
						$tCargo->setAmount ( $value, 'product', $tValue );
					}

					unset($tCargo);
				}

				/**
				 * Zainicjuj tablicę ruchu
				 */
				if ($row1->Moveable == "yes") {
					\npc::initMoveTable ($npcID, $row1->NPCTypeID, $position );
				}

				$shipPropertiesObject->synchronize ( $shipProperties, true, true );
				unset($shipPropertiesObject);

			}
		}

		return $out;

	}

	/**
	 * (non-PHPdoc)
	 * @see Controlpanel.BaseItem::getDataObject()
	 */
	protected function getDataObject($itemID) {

		$tQuery = \Database\Controller::getInstance()->execute ( "
				SELECT 
					npctypes.*, 
					alliances.Name As AllianceName
				FROM 
					npctypes LEFT JOIN alliances USING(AllianceID) 
				WHERE 
					NPCTypeID='{$itemID}' LIMIT 1" );
		return \Database\Controller::getInstance()->fetch ( $tQuery );
	}

	public function detail($user, $params) {

		$_SESSION['returnLink'] = $_SERVER['REQUEST_URI'];
		$_SESSION['returnUser'] = $params['id'];

		$template = new \General\Templater('templates/npcTypeDetail.html');

		$template->add('Title',$this->detailTitle);

		$tData = $this->getDataObject($params['id']);

		$template->add('Data',\General\Controls::sBuilTable($tData,2));

		$tShipData = \ship::quickLoad($tData->ShipID);

		$tMaxStats = new \stdClass();
		$tMaxStats->Shield = $tShipData->Shield;
		$tMaxStats->Armor = $tShipData->Armor;
		$tMaxStats->Power = $tShipData->Power;
		$tMaxStats->Maneuver = $tShipData->Maneuver;
		$tMaxStats->Targetting = $tShipData->Targetting;
		$tMaxStats->ArmorStrength = $tShipData->ArmorStrength;
		$tMaxStats->ArmorPiercing = $tShipData->ArmorPiercing;

		/*
		 * Lista uzbrojenia
		*/
		$tString = '<ul>';
		$tArray = explode(',', $tData->Weapons);
		foreach ($tArray as $tWeaponID) {
			$tString .= '<li>'.\weapon::quickLoad($tWeaponID)->NameEN.'</li>';
		}
		$tString .= '</ul>';
		$template->add('WeaponsList', $tString);
		$template->add('currentWeapons', count($tArray));
		$template->add('maxWeapons', $tShipData->Weapons);

		/*
		 * Lista wyposażenia
		*/
		$tArray = explode(',', $tData->Equipment);
		$tString = '<ul>';
		foreach ($tArray as $tEquipmentID) {
			$tEquipment = \equipment::quickLoad($tEquipmentID);

			if (empty($tEquipment)) {
				continue;
			}

			foreach ($tEquipment as $tKey => $tValue) {
				if (isset($tMaxStats->{$tKey})) {
					$tMaxStats->{$tKey} += $tValue;
				}
			}

			$tString .= '<li>'.$tEquipment->NameEN.'</li>';
		}
		$tString .= '</ul>';
		$template->add('EquipmentList', $tString);
		$template->add('currentEquipment', count($tArray));
		$template->add('maxEquipment', $tShipData->Space);

		$template->add('theoreticalValues',\General\Controls::sBuilTable($tMaxStats));

		$template->add('CLOSE_BUTTON',\General\Controls::bootstrapButton ( "Close", "document.location='index.php?class=".get_class($this)."&method=browse'", 'btn-inverse','icon-off' ));
		
		if (\user::sGetRole() == 'admin') {
			$template->add('editButton',\General\Controls::bootstrapButton ( "Edit", "document.location='index.php?class=".get_class($this)."&method=edit&id={$params['id']}'",'btn-warning', 'icon-pencil' ));
			$template->add('deleteButton',\General\Controls::bootstrapButton ( "Delete Type", "document.location='index.php?class=".get_class($this)."&method=delete&id={$params['id']}'",'btn-danger', 'icon-trash' ));
			$template->add('dropButton',\General\Controls::bootstrapButton ( "Drop All NPC of this type", "document.location='index.php?class=".get_class($this)."&method=drop&id={$params['id']}'",'btn-danger', 'icon-trash' ));
			$template->add('createButton',\General\Controls::bootstrapButton ( "Create NPC of this type", "document.location='index.php?class=".get_class($this)."&method=create&id={$params['id']}'",'btn-danger', 'icon-plus' ));
		}
		else {
			$template->remove('operations');
		}

		return (string)$template;
	}

}