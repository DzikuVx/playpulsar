<?php
/**
 * Klasa npc
 *
 * @version $Rev: 456 $
 * @package Engine
 */
class npc extends baseItem {

	protected $tableName = "npctypes";
	protected $tableID = "NPCTypeID";
	protected $tableUseFields = null;
	protected $defaultCacheExpire = 86400;
	protected $useMemcached = true;

	/**
	 * Równanie ataku NPC na gracza
	 * @param int $userLevel
	 * @param int $npcLevel
	 * @param int $sectorVisibility
	 * @return int
	 */
	static private function sComputeAttackProbabilty($userLevel, $npcLevel, $sectorVisibility) {

		$retVal = floor(((($npcLevel-$userLevel) * 2) + $sectorVisibility) / 2);

		if ($retVal > 50) {
			$retVal = 50;
		}

		if ($retVal < 5) {
			$retVal = 5;
		}

		return $retVal;
	}

	/**
	 * Kontroler zachowanie AGGRESIVE NPCów
	 * @param int $userID
	 * @param int $npcID
	 * @param int $userLevel
	 * @param int $npcLevel
	 * @param int $sectorVisibility
	 */
	static public function sAggresiveController($userID, $npcID, $userLevel, $npcLevel, $sectorVisibility) {

		$Prop = self::sComputeAttackProbabilty($userLevel, $npcLevel, $sectorVisibility);

		if (additional::checkRand($Prop, 100)) {

			try {
				combat::sSetCombatLock ( $npcID, $userID, false );
			} catch (Exception $e) {
				// A tutaj rób nic, bo widać był jakiś błąd przy zakładaniu combat locka
			}
		}

	}

	/**
	 * Pobranie listy NPC typu protective znajdujących się określonej pozycji
	 * @param stdClass $position
	 * @return array
	 */
	static public function sGetProtectiveAtPosition($position, $defenderAlliance) {

		global $config;
		
		$retVal = array();
		$tQuery = "SELECT
					shippositions.UserID
				FROM
					shippositions JOIN users USING(UserID) 
					JOIN npctypes USING(NPCTypeID)
					JOIN userships USING(UserID)
					LEFT JOIN combatlock ON combatlock.UserID=shippositions.UserID
					JOIN alliancemembers ON alliancemembers.UserID=shippositions.UserID
				WHERE 
					userships.RookieTurns = '0' AND 
					combatlock.Active IS NULL AND
					(npctypes.Behavior='protect' OR (npctypes.Behavior='protect_own' AND alliancemembers.AllianceID='{$defenderAlliance}')) AND
					shippositions.System='{$position->System}' AND
					shippositions.X = '{$position->X}' AND
					shippositions.Y = '{$position->Y}' AND
					shippositions.Docked = 'no'
				LIMIT {$config ['combat'] ['protectiveNpcSummonLimit']}";
		$tQuery = \Database\Controller::getInstance()->execute($tQuery);
		while ($tResult = \Database\Controller::getInstance()->fetch($tQuery)) {
			array_push($retVal, $tResult->UserID);
		}

		return $retVal;
	}

	/**
	 * Sprowadzenia na pozycje NPC typu protective zgodnie z założonymi globalnie warunkami
	 * @param stdClass $position
	 */
	static public function sSummonProtective($position, $defenderAlliance) {

		global $config;

		if (additional::checkRand($config ['npc'] ['protectiveSummonProbability'], 100)) {

			//@todo teraz będzie przyzywać wszystko... jak leci

			$minX = $position->X - $config ['npc'] ['behaviorRadius'];
			$minY = $position->Y - $config ['npc'] ['behaviorRadius'];
			$maxX = $position->X + $config ['npc'] ['behaviorRadius'];
			$maxY = $position->Y + $config ['npc'] ['behaviorRadius'];

			$tQuery = "UPDATE
				shippositions JOIN users USING(UserID) 
				JOIN npctypes USING(NPCTypeID)
				JOIN userships USING(UserID)
				LEFT JOIN combatlock ON combatlock.UserID=shippositions.UserID
				JOIN alliancemembers ON alliancemembers.UserID=shippositions.UserID
			SET
				shippositions.X = '{$position->X}', 
				shippositions.Y = '{$position->Y}'
			WHERE 
				userships.RookieTurns = '0' AND
				combatlock.Active IS NULL AND
				(npctypes.Behavior='protect' OR (npctypes.Behavior='protect_own' AND alliancemembers.AllianceID='{$defenderAlliance}')) AND 
				shippositions.System='{$position->System}' AND
				shippositions.X > '{$minX}' AND
				shippositions.X < '{$maxX}' AND
				shippositions.Y > '{$minY}' AND
				shippositions.Y < '{$maxY}' AND
				shippositions.Docked = 'no'
				";
			\Database\Controller::getInstance()->execute($tQuery);
		}
	}

	/**
	 * Reset konkretnego NPC
	 *
	 * @param int $npcID
	 */
	static public function sResetNpc($npcID) {

		$npcShipPropertiesObject = new shipProperties ( );
		$npcShipProperties = $npcShipPropertiesObject->load ( $npcID, true, true );

		$npcUserPropertiesObject = new userProperties ( );
		$npcUserProperties = $npcUserPropertiesObject->load ( $npcID, true, true );

		if ($npcShipProperties->RookieTurns != 0) {

			/*
			 * Pełny reset
			 */

			$npcType = static::quickLoad ( $npcUserProperties->NPCTypeID );

			/*
			 * Zainicjuj obiekty
			 */
			$npcWeapons = new shipWeapons ( $npcID, 'en' );
			$npcEquipment = new shipEquipment ( $npcID, 'en' );
			$npcCargo = new shipCargo ( $npcID, 'en' );

			/*
			 * Zrzuć ew. wyposażenie i uzbrojenie
			 */
			$npcWeapons->removeAll ( $npcShipProperties );
			$npcEquipment->removeAll ( $npcShipProperties );
			$npcCargo->removeAll ( $npcShipProperties );

			/*
			 * Wylosuj jego nowy system i pozycję
			 */
			$npcPosition = new shipPosition ($npcID, false);

			if ($npcType->Systems == "all") {
				$npcPosition->System = galaxy::sGetRandomSystem ();
			} else {
				$npcPosition->System = additional::randFormList ( $npcType->Systems );
			}
			$tPosition = systemProperties::randomPosition ( $npcPosition->System );
			$npcPosition->X = $tPosition->X;
			$npcPosition->Y = $tPosition->Y;
			$npcPosition->Docked = 'no';
			unset ( $tPosition );

			$npcPosition->synchronize ();

			/*
			 * Ustaw doświadczenie, kasę
			 */
			$npcUserStatsObject = new userStats ( );
			$npcUserStats = $npcUserStatsObject->load ( $npcID, true, true );

			$npcUserStats->Cash = additional::randomizeValue ( $npcType->Cash, 20, 1000 );
			$npcUserStats->Experience = additional::randomizeValue ( userStats::computeExperience($npcType->Level), 10, 1000 );
			$npcUserStats->Level = userStats::computeLevel ( $npcUserStats->Experience );

			$npcUserStatsObject->synchronize ( $npcUserStats, true, true);
			unset($npcUserStatsObject);

			/*
			 * ustaw nowy statek
			 */
			$npcShipProperties->ShipID = $npcType->ShipID;

			/*
			 * Wstaw uzbrojenie
			 */
			$weaponsCount = user::sInsertWeaponsSet($npcID, $npcType->Weapons);

			/*
			 * Wygeneruj equipment
			 */
			$equipmentCount = user::sInsertEquipmentSet($npcID, $npcType->Equipment);

			$npcShipProperties->CurrentWeapons = $weaponsCount;
			$npcShipProperties->CurrentEquipment = $equipmentCount;

			$npcWeapons->computeOffensiveRating ( $npcShipProperties );

			shipProperties::computeMaxValues ( $npcShipProperties );

			/**
			 * Ustaw aktualne maksymalne jako aktualne
			 */
			shipProperties::setFromFull ( $npcShipProperties );
			shipProperties::computeDefensiveRating ( $npcShipProperties );

			if ($npcType->HaveItems == 'yes') {
				$tItems = item::getRand ( additional::rand ( 0, 5 ) );

				foreach ( $tItems as $value ) {
					$npcCargo->setAmount ( $value, 'item', additional::rand ( 1, 2 ) );
				}

			}

			if ($npcType->HaveCargo == 'yes') {
				$tValue = additional::rand ( 1, 3 );
				$tItems = product::getRand ( $tValue );

				$tValue = floor ( $npcShipProperties->CargoMax / $tValue );

				foreach ( $tItems as $value ) {
					$npcCargo->setAmount ( $value, 'product', $tValue );
				}

			}

			if ($npcType->Moveable == "yes") {
				$npcPosition->UserID = $npcID;
				self::initMoveTable ( $npcUserProperties->NPCTypeID, $npcPosition );
			}

			$npcShipProperties->RookieTurns = 0;

		} else {

			/*
			 * Wyłącznie naprawa equipmentu
			 */

			//@todo: naprawa equipmentu dla NPC po resecie


		}

		$npcShipPropertiesObject->synchronize ( $npcShipProperties, true, true);
		unset($npcShipPropertiesObject);

		$npcUserPropertiesObject->synchronize ( $npcUserProperties, true, true );
		unset($npcUserPropertiesObject);
	}

	/**
	 * Szybkie pobranie danych NPC
	 *
	 * @param int $ID
	 * @return stdClass
	 */
	static public function quickLoad($ID) {

		$item = new npc ( );
		$retVal = $item->load ( $ID, true, true );
		unset($item);
		return $retVal;
	}

	/**
	 * Inicjacja tablicy npcmove
	 *
	 * @param int $ID - typNPC
	 * @param stdClass $shipPosition - aktualna pozycja NPC
	 */
	static function initMoveTable($typeID, $position) {

		$item = static::quickLoad ( $typeID );

		$moveCount = rand ( $item->MoveCountMin, $item->MoveCountMax );
		$moveTime = rand ( $item->MoveTimeMin, $item->MoveTimeMax ) + time ();

		if ($item->Dock == "yes") {
			$tPos = systemProperties::randomPort ( $position );
		} else {
			$tPos = systemProperties::randomPosition ( $position->System );
		}

		\Database\Controller::getInstance()->execute ( "DELETE FROM npcmove WHERE UserID = '{$position->UserID}'" );

		$t2Query = "INSERT INTO npcmove (
        UserID,    
		    Direction,
        MoveTime,
        MoveCount,
        NextMoveTimeMin,
        NextMoveTimeMax,
        SrcSystem,
        SrcX,
        SrcY,
        DstSystem,
        DstX,
        DstY,
        Dock
	    ) VALUES (
	      '{$position->UserID}',
	      'Src-Dst' ,
        '$moveTime',
        '$moveCount',
        '{$item->MoveTimeMin}',
        '{$item->MoveTimeMax}',
        '{$position->System}',
        '{$position->X}',
        '{$position->Y}',
        '{$tPos->System}',
        '{$tPos->X}',
        '{$tPos->Y}',
        '{$item->Dock}'
	   )";
		\Database\Controller::getInstance()->execute ( $t2Query );

	}

	/**
	 * Ruszenie npc
	 *
	 * @param int $userID
	 * @param int $actualTime
	 * @param int $shipPosition
	 */
	static public function sMove($userID, $actualTime, $shipPosition) {

		global $config;

		/*
		 * Sprawdz, czy upłynął Twój czas ruszania NPC
		 */
		if (time () - $_SESSION ['lastNPCMoveTime'] <= $config ['timeThresholds'] ['npcMove']) {
			return false;
		}

		$_SESSION ['lastNPCMoveTime'] = time ();

		/*
		 * Obejmij całą procedurę transakcją
		 */
		try {

			\Database\Controller::getInstance()->disableAutocommit();

			$tQuery = "UPDATE npcmove SET npcmove.Owner='$userID' WHERE npcmove.Owner IS NULL AND npcmove.MoveCount > '0' AND npcmove.SrcSystem='{$shipPosition->System}' AND  npcmove.MoveTime < '$actualTime' LIMIT {$config ['npc'] ['simulaneousMoveLimit']}";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );

			//Pobierz tych wszystkich NPC wraz z ich parametrami
			$tQuery = "
      SELECT
        npcmove.Dock AS Dock,
        npcmove.MoveCount AS MoveCount,
        npcmove.Direction AS Direction,
        npcmove.NextMoveTimeMin AS NextMoveTimeMin,
        npcmove.NextMoveTimeMax AS NextMoveTimeMax,

        shippositions.System AS CurrentSystem,
        shippositions.X AS CurrentX,
        shippositions.Y AS CurrentY,
        shippositions.Docked AS CurrentDocked,

        npcmove.SrcSystem AS SrcSystem,
        npcmove.SrcX AS SrcX,
        npcmove.SrcY AS SrcY,

        npcmove.DstSystem AS DstSystem,
        npcmove.DstX AS DstX,
        npcmove.DstY AS DstY,
        npcmove.UserID AS NpcID
      FROM
        ((npcmove JOIN userships ON userships.UserID = npcmove.UserID)
        JOIN shippositions ON shippositions.UserID = npcmove.UserID)
        LEFT JOIN combatlock ON combatlock.UserID=npcmove.UserID
      WHERE
        combatlock.Active IS NULL AND
        npcmove.Owner = '$userID' AND
        userships.RookieTurns < '1'
    ";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );

			if (\Database\Controller::getInstance()->count($tQuery) > 0) {
				/*
				 * Przygotuj zapytanie do ustawienia pozycji
				 */
				$sPreparedPosition = mysqli_prepare(\Database\Controller::getInstance()->getHandle(), "UPDATE shippositions SET X=?, Y=?, Docked=? WHERE UserID=?");
				$sPreparedNpcMove = mysqli_prepare(\Database\Controller::getInstance()->getHandle(), "UPDATE npcmove SET Direction=? , MoveTime=?, MoveCount=? WHERE UserID=?");

			}

			while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				//Okresl które pozycje sa docelowymi
				if ($tR1->Direction == "Src-Dst") {
					$DstX = $tR1->DstX;
					$DstY = $tR1->DstY;
				} else {
					$DstX = $tR1->SrcX;
					$DstY = $tR1->SrcY;
				}
				//Oblicz nową pozycję dla statku
				$arrived = false;
				if ($tR1->CurrentDocked == "no") {
					$xPosDiffer = $DstX - $tR1->CurrentX;
					$yPosDiffer = $DstY - $tR1->CurrentY;

					if ((abs ( $xPosDiffer ) > 0) and (abs ( $yPosDiffer ) > 0)) {
						$prc = rand ( 1, 100 );
						if ($prc <= 50) {
							if ($xPosDiffer > 0) {
								$tR1->CurrentX += 1;
							} else {
								$tR1->CurrentX -= 1;
							}
						} else {
							if ($yPosDiffer > 0) {
								$tR1->CurrentY += 1;
							} else {
								$tR1->CurrentY -= 1;
							}
						}
					} else {
						//Sprawdz, ktora os poruszyć


						if (abs ( $xPosDiffer ) > 0) {
							//Rusz w osi X
							if ($xPosDiffer > 0) {
								$tR1->CurrentX += 1;
							} else {
								$tR1->CurrentX -= 1;
							}
						}
						if (abs ( $yPosDiffer ) > 0) {
							//Rusz w osi Y
							if ($yPosDiffer > 0) {
								$tR1->CurrentY += 1;
							} else {
								$tR1->CurrentY -= 1;
							}
						}
						if ((abs ( $xPosDiffer ) == 0) and (abs ( $yPosDiffer ) == 0)) {
							//Jest juz na pozycji
							if ($tR1->CurrentDocked == "no") {
								$tR1->CurrentDocked = "yes";
								$arrived = true;
							}
						}
					}
				}

				//Jeśli przybył na miejsce
				if ($arrived) {
					$tR1->MoveCount -= 1;
					//Obróc kierunek
					if ($tR1->Direction == "Src-Dst") {
						$tR1->Direction = "Dst-Src";
					} else {
						$tR1->Direction = "Src-Dst";
					}
					if ($tR1->Dock == "yes") {
						$moveTime = rand ( $tR1->NextMoveTimeMin, $tR1->NextMoveTimeMax ) * 5;
						if (rand ( 0, 100 ) < 25) {
							$moveTime = $moveTime * 10;
						}
						$moveTime += $actualTime;
					} else {
						$moveTime = rand ( $tR1->NextMoveTimeMin, $tR1->NextMoveTimeMax ) + $actualTime;
					}
				} else {
					$tR1->CurrentDocked = "no";
					$moveTime = rand ( $tR1->NextMoveTimeMin, $tR1->NextMoveTimeMax ) + $actualTime;
				}
				//Jesli jest to NPC niedokujący
				if ($tR1->Dock == "no")
				$tR1->CurrentDocked = "no";

				/**
				 * Przygotowane zapytanie dla parametrów ruchu
				 * @since 2011-02-07
				 */
				mysqli_stmt_bind_param($sPreparedNpcMove, 'siii',$tR1->Direction, $moveTime, $tR1->MoveCount, $tR1->NpcID);
				mysqli_stmt_execute($sPreparedNpcMove);

				/**
				 * Wykonaj przygotowane zapytanie ustawiania pozycji statku
				 * @since 2011-02-07
				 */
				mysqli_stmt_bind_param($sPreparedPosition, 'iisi', $tR1->CurrentX, $tR1->CurrentY, $tR1->CurrentDocked, $tR1->NpcID);
				mysqli_stmt_execute($sPreparedPosition);

				/*
				 * Wyczyść cache jego pozycji
				 */
				\Cache\Controller::getInstance()->clear('shipPosition',$tR1->NpcID);

			}

			//Dokonaj resetu tych NPC którym skończył się czas i nie zostali zniszczeni
			$tQuery = "
      SELECT
        npcmove.UserID AS NpcID,
        npctypes.Dock AS Dock,
        npctypes.MoveCountMin AS MoveCountMin,
        npctypes.MoveCountMax AS MoveCountMax,
        npctypes.MoveTimeMin AS MoveTimeMin,
        npctypes.MoveTimeMax AS MoveTimeMax,
        shippositions.System AS PositionSystem,
        shippositions.X AS PositionX,
        shippositions.Y AS PositionY,
        shippositions.Docked AS Docked
      FROM
        (((npcmove JOIN users ON users.UserID = npcmove.UserID)
        JOIN shippositions ON shippositions.UserID = npcmove.UserID)
        JOIN npctypes ON npctypes.NPCTypeID = users.NPCTypeID)
        JOIN userships ON userships.UserID = npcmove.UserID
      WHERE
        npcmove.Owner = '$userID' AND
        npcmove.MoveCount < '1' AND
        userships.RookieTurns < 1
    ";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );

			//Jesli sa NPC do zresetowania, pobierz parametry systemu
			if (\Database\Controller::getInstance()->count ( $tQuery ) > 0) {
				$systemProperties = systemProperties::quickLoad ( $shipPosition->System );
			}

			while ( $tR1 = \Database\Controller::getInstance()->fetch ( $tQuery ) ) {
				//Pobierz dane defaultowe NPC
				$moveCount = rand ( $tR1->MoveCountMin, $tR1->MoveCountMax );
				$direction = "Src-Dst";
				$srcSystem = $tR1->PositionSystem;
				$srcX = $tR1->PositionX;
				$srcY = $tR1->PositionY;

				$moveTime = rand ( $tR1->MoveTimeMin, $tR1->MoveTimeMax ) + $actualTime;

				if ($tR1->Dock == 'no') {
					$dstSystem = $tR1->PositionSystem;
					$dstX = rand ( 1, $systemProperties->Width );
					$dstY = rand ( 1, $systemProperties->Height );
				} else {
					$tPos = new stdClass();
					$tPos->System = $tR1->PositionSystem;
					$tPos->X = $tR1->PositionX;
					$tPos->Y = $tR1->PositionY;
					$tPos = systemProperties::randomPort ( $tPos );

					$dstSystem = $tPos->System;
					$dstX = $tPos->X;
					$dstY = $tPos->Y;

				}
				//Zapisz nowe parametry tego NPC do bazy danych
				$t2Query = "UPDATE npcmove SET
        Direction='$direction' ,
        MoveTime='$moveTime',
        MoveCount='$moveCount',
        NextMoveTimeMin = '{$tR1->MoveTimeMin}',
        NextMoveTimeMax = '{$tR1->MoveTimeMax}',
        SrcSystem = '$srcSystem',
        SrcX = '$srcX',
        SrcY = '$srcY',
        DstSystem = '$dstSystem',
        DstX = '$dstX',
        DstY = '$dstY',
        Dock = '{$tR1->Dock}'
      WHERE
        UserID='{$tR1->NpcID}'";
				$t2Query = \Database\Controller::getInstance()->execute ( $t2Query );
			}

			//Zdejmij blokadę NPC i Nadpisz następny czas ruchu
			$tQuery = "UPDATE npcmove JOIN usertimes ON usertimes.UserID = npcmove.UserID SET npcmove.Owner=null, usertimes.LastAction='$actualTime' WHERE npcmove.Owner='$userID'";
			$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );

			\Database\Controller::getInstance()->commit();
			\Database\Controller::getInstance()->enableAutocommit();

		}catch (Exception $e) {
			\Database\Controller::getInstance()->rollback();
			\Database\Controller::getInstance()->enableAutocommit();
			psDebug::cThrow(null, $e, array('display'=>false));
		}

		return true;
	}

	/**
	 * Wstawienie do bazy danych kontaktu pomiędzy graczem i NPC
	 * @param int $userID
	 * @param int $npcID
	 * @param stdClass $shipPosition
	 */
	static public function sInsertContact($userID, $npcID, $shipPosition) {

		global $actualTime;

		try {
			$tQuery = "INSERT DELAYED INTO npccontact(NpcID, UserID, ContactTime, System, X, Y) Values('$npcID','$userID','$actualTime','{$shipPosition->System}','{$shipPosition->X}','{$shipPosition->Y}')";
			\Database\Controller::getInstance()->execute ( $tQuery );
		}catch (Exception $e) {
			psDebug::cThrow(null, $e, array('display'=>false));
			return false;
		}
		return true;
	}

}
