<?php

if (!isset($userID)) {
    $userID = null;
}

if (!isset($userStats)) {
    $userStats = new stdClass();
}

if (!isset($config)) {
    $config = array();
}

if (!isset($shipPosition)) {
    $shipPosition = new \Gameplay\Model\ShipPosition();
}

if (!isset($shipProperties)) {
    $shipProperties = new \Gameplay\Model\ShipProperties();
}

if (!isset($sectorProperties)) {
    $sectorProperties = new \Gameplay\Model\SectorEntity();
}

if (!isset($portProperties)) {
    $portProperties = new \Gameplay\Model\PortEntity();
}

if (!isset($shipEquipment)) {
    $shipEquipment = new \Gameplay\Model\ShipEquipments($userID);
}

if (!isset($shipWeapons)) {
    $shipWeapons = new \Gameplay\Model\ShipWeapons($userID);
}

if ($action == "equipFromCargo") {

	if ($shipProperties->Turns < $turnsToEquip) {
		$error = true;
		throw new warningException ( TranslateController::getDefault()->get ( 'notEnoughTurns' ) . "" );
	}

	$onBoard = false;

	if ($subaction == "weapon") {
        $tItem = new \Gameplay\Model\WeaponType($id);

		if ($shipProperties->WeaponSize < $tItem->Size ) {
			throw new securityException();
		}

		$onBoard = $shipCargo->checkExists ( $id, 'weapon' );
	}

	if ($subaction == "equipment") {
        $tItem = new \Gameplay\Model\EquipmentType($id);
		$onBoard = $shipCargo->checkExists ( $id, 'equipment' );
	}

	if (empty($tItem)) {
		throw new securityException ( );
	}

	if (empty($onBoard)) {
		throw new securityException ( );
	}

	//Zmniejsz tury
	$shipProperties->Turns -= $turnsToEquip;

	if ($shipProperties->RookieTurns > 0) {
		$shipProperties->RookieTurns -= $turnsToEquip;
		if ($shipProperties->RookieTurns < 0) {
			$shipProperties->RookieTurns = 0;
		}
	}

	$shipCargo->decAmount ( $id, $subaction, 1 );

	if ($subaction == "weapon") {
		$shipWeapons->insert ( $tItem, $shipProperties );
	}

	if ($subaction == "equipment") {
		$shipEquipment->insert ( $tItem, $shipProperties );
	}

    $shipProperties->computeMaxValues();
    \Gameplay\Model\ShipProperties::updateUsedCargo($shipProperties);

	/**
	 * Odświerz panele
	 */

	$action = "cargoManagement";

    \Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', '{T:opSuccess}');
}

if ($action == "cargoManagement") {
	shipCargo::management ( $userID );
	\Gameplay\Panel\SectorShips::getInstance()->hide ();
	\Gameplay\Panel\SectorResources::getInstance()->hide ();
	\Gameplay\Panel\PortAction::getInstance()->clear();
}

/*
 * Zakup przestrzeni magazynowej w portach
*/
if ($action == "buyStorageRoom") {

	if ($shipPosition->Docked == 'no') {
		throw new securityException ( );
	}

	if ($userStats->Cash < $config ['port'] ['storageSpacePrice']) {
		throw new securityException (TranslateController::getDefault()->get('notEnoughCash'));
	}

	//Pobierz aktualne miejsce w magazynie
	$totalStorageRoom = storageCargo::sGetTotalUserSpace( $userID, $portProperties->PortID );

	//Zwiększ przestrzeń magazynową
	if ($totalStorageRoom == 0) {
		$tQuery = "INSERT INTO userportcargo(PortID, UserID, Size) VALUES('{$portProperties->PortID}', '$userID', '{$config ['port'] ['storageSpace']}')";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
	} else {
		$tQuery = "UPDATE userportcargo SET Size=Size+'{$config ['port'] ['storageSpace']}' WHERE PortID='{$portProperties->PortID}' AND UserID='$userID'";
		$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
	}
	//Zmiejsz gotówkę usera
	$userStats->Cash -= $config ['port'] ['storageSpacePrice'];

	$portProperties->Cash += $config ['port'] ['storageSpacePrice'];

    $portProperties->reset();
	\Gameplay\Panel\SectorShips::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
	\Gameplay\Panel\SectorResources::getInstance()->hide ();
	$action = "portStorehouse";
	\Gameplay\Model\PortEntity::sPopulatePanel ( $userID, $shipPosition, $portProperties, $action, $subaction, $value, $id );
	\Gameplay\Panel\PortAction::getInstance()->clear ();
    \Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', '{T:opSuccess}');
}

/*
 * Jettison towarów
*/
if ($action == "jettison") {

	//Sprawdz, czy zadokowany
	if ($shipPosition->Docked == 'yes') {
		throw new securityException ( );
	}

	$inCargo = $shipCargo->getAmount ( $id, $subaction );

	if ($inCargo == 0) {
		throw new securityException ( );
	}

	if ($value == 'all') {
		$toJettison = $inCargo;
	} else {
		$toJettison = $value;
	}

	if ($toJettison < 1) {
		throw new securityException();
	}

	if ($toJettison > $inCargo) {
		throw new securityException ( );
	}

	if (! $error) {
		$shipCargo->setAmount ( $id, $subaction, $inCargo - $toJettison );

		if (empty($sectorCargo)) {
			$sectorCargo = new sectorCargo($shipPosition);
		}

		$sectorCargo->insert($subaction, $id, $toJettison);

		//zmiejsz liczbe tur usera
		$shipProperties->Turns -= $itemJettisonCost;

		if ($shipProperties->RookieTurns > 0) {
			$shipProperties->RookieTurns -= $itemJettisonCost;
			if ($shipProperties->RookieTurns < 0) {
				$shipProperties->RookieTurns = 0;
			}
		}

        \Gameplay\Model\ShipProperties::updateUsedCargo($shipProperties);

		//Jesli wyrzucałem towar, zmniejsz expa o max wartość
		if ($subaction == 'product') {
            $productData = new \Gameplay\Model\ProductType($id);
			$userStats->Experience -= $productData->getExperienceForJettison() * $toJettison;
			$userStats->Level = \Gameplay\Model\UserStatistics::computeLevel ( $userStats->Experience );
		}
	}

    $sectorProperties->resetResources();
    $portProperties->reset();
	shipCargo::management ( $userID );
	\Gameplay\Panel\PortAction::getInstance()->clear();
    \Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', '{T:opSuccess}');
}

/*
 * Zbieranie
*/
if ($action == "gather") {

	//Sprawdz, czy w sektorze jest towar który chcesz zebrać

    $productExp = 0;

	$sectorCargo = new sectorCargo($shipPosition);
	$sectorAmount = $sectorCargo->getAmount($subaction, $id);

	if ($sectorAmount === null) {
		throw new securityException();
	}

	$productSize = 1;
	$toGather = 1;
	$turnsRequired = $itemPickCost;

	if ($shipProperties->CargoMax < $shipProperties->Cargo) {
		throw new securityException ( );
	}

	if ($subaction == 'product') {
        $productData = new \Gameplay\Model\ProductType($id);

		$productSize = $productData->Size;
		$productExp = $productData->getExperienceForGather($sectorAmount);

		if (empty($shipProperties->Gather)) {
			throw new securityException();
		}

		$toGather = floor ( ($shipProperties->CargoMax - $shipProperties->Cargo) / $productSize );
		if ($toGather > $sectorAmount) {
			$toGather = $sectorAmount;
		}
		$canGather = $shipProperties->Turns * $shipProperties->Gather;
		if ($toGather > $canGather) {
		    $toGather = $canGather;
        }
		$turnsRequired = ceil ( $toGather / $shipProperties->Gather );
	}

	if ($subaction == 'weapon') {
        $productData = new \Gameplay\Model\WeaponType($id);
		$productSize = $productData->Size;
		$productExp = 0;

		if ($toGather > $sectorAmount) {
		    $toGather = $sectorAmount;
        }

		$canGather = floor ( $shipProperties->Turns / $turnsRequired );
		if ($toGather > $canGather) {
		    $toGather = $canGather;
        }
	}

	if ($subaction == 'equipment') {
        $productData = new \Gameplay\Model\EquipmentType($id);
		$productExp = 0;
		$productSize = $productData->Size;

		if ($toGather > $sectorAmount) {
		    $toGather = $sectorAmount;
        }
		$canGather = floor ( $shipProperties->Turns / $turnsRequired );
		if ($toGather > $canGather) {
		    $toGather = $canGather;
        }
	}

	if ($subaction == 'item') {
        $productData = new \Gameplay\Model\ItemType($id);
		$productExp = $productData->Experience;
		$productSize = $productData->Size;

		//Oblicz ile jesteś w stanie zebrać
		if ($toGather > $sectorAmount) {
		    $toGather = $sectorAmount;
        }

        $canGather = floor ( $shipProperties->Turns / $turnsRequired );

        if ($toGather > $canGather) {
		    $toGather = $canGather;
        }
	}

	if (! $error) {

		/*
		 * Ustal aktualną liczbę w sektorze
		*/
		$sectorCargo->update($subaction, $id, ($sectorAmount-$toGather));

		$shipCargo->incAmount ( $id, $subaction, $toGather );
        $userStats->incExperience($productExp * $toGather);

		$shipProperties->Turns -= $turnsRequired;

		if ($shipProperties->RookieTurns > 0) {
			$shipProperties->RookieTurns -= $turnsRequired;
			if ($shipProperties->RookieTurns < 0) {
				$shipProperties->RookieTurns = 0;
			}
		}

        \Gameplay\Model\ShipProperties::updateUsedCargo($shipProperties);

        $sectorProperties->resetResources();
        $portProperties->reset();
		\Gameplay\Panel\SectorShips::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
		\Gameplay\Panel\SectorResources::getInstance()->render ( $shipPosition, $shipProperties, $sectorProperties );
		\Gameplay\Panel\PortAction::getInstance()->clear();
        \Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', '{T:opSuccess}');
	}
}

if ($action == 'toCargohold') {

    $productSize = 1;

	if ($portProperties->PortID == null) {
		throw new securityException ( );
	}
	if ($shipPosition->Docked == 'no') {
		throw new securityException ( );
	}
	$storageCargo = new storageCargo ( $userID, $portProperties->PortID );

	if ($value < 0) {
		throw new securityException ( );
	}

	if ($value == 'all') {
		/*
		 * PObierz stan magazynu
		*/
		$originalValue = $storageCargo->getAmount ( $id, $subaction );
	} else {
		$originalValue = $value;
	}

	if ($originalValue < 1) {
		throw new securityException ( );
	}

	if ($subaction == 'product') {
        $productData = new \Gameplay\Model\ProductType($id);
		$productSize = $productData->Size;
	}

	if ($subaction == 'weapon') {
        $productData = new \Gameplay\Model\WeaponType($id);
		$productSize = $productData->Size;
	}

	if ($subaction == 'equipment') {
        $productData = new \Gameplay\Model\EquipmentType($id);
		$productSize = $productData->Size;

	}

	if ($subaction == 'item') {
        $productData = new \Gameplay\Model\ItemType($id);
		$productSize = $productData->Size;
	}

	$avaibleCargo = $shipProperties->CargoMax - $shipProperties->Cargo;
	$usedByMove = $originalValue * $productSize;

	/*
	 * Zabezpieczenie na brak miejsca w ładowni
	*/
	if ($avaibleCargo < 1) {
		throw new securityException ( );
	}

	if ($usedByMove > $avaibleCargo) {
		$toMove = floor ( $avaibleCargo / $productSize );
	} else {
		$toMove = $originalValue;
	}

	if (! $error) {
		/*
		 * Dodaj do ładowni
		*/
		$shipCargo->incAmount ( $id, $subaction, $toMove );
		$storageCargo->decAmount ( $id, $subaction, $toMove );

        \Gameplay\Model\ShipProperties::updateUsedCargo ( $shipProperties );

		$action = 'portStorehouse';

		\Gameplay\Model\PortEntity::sPopulatePanel ( $userID, $shipPosition, $portProperties, $action, $subaction, $value, $id );

        $sectorProperties->resetResources();
        $portProperties->reset();
		\Gameplay\Panel\PortAction::getInstance()->clear();
        \Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', '{T:opSuccess}');
	}
}

if ($action == 'toStorehouse') {

    $productSize = 1;

	if ($portProperties->PortID == null) {
		throw new securityException ();
	}

	if ($shipPosition->Docked == 'no') {
		throw new securityException ();
	}
	$storageCargo = new storageCargo ( $userID, $portProperties->PortID );

	if ($value == 'all') {
		/*
		 * PObierz stan magazynu
		*/
		$originalValue = $shipCargo->getAmount ( $id, $subaction );
	} else {
		$originalValue = $value;
	}

	if ($originalValue < 1) {
		throw new securityException();
	}

	if ($subaction == 'product') {
        $productData = new \Gameplay\Model\ProductType($id);
		if (empty($productData->Size)) {
			throw new securityException();
		}
		$productSize = $productData->Size;
	}

	if ($subaction == 'weapon') {
        $productData = new \Gameplay\Model\WeaponType($id);
		if (empty($productData->Size)) {
			throw new securityException();
		}
		$productSize = $productData->Size;
	}

	if ($subaction == 'equipment') {
		$productData = new \Gameplay\Model\EquipmentType($id);
		if (empty($productData->Size)) {
			throw new securityException();
		}
		$productSize = $productData->Size;
	}

	if ($subaction == 'item') {
        $productData = new \Gameplay\Model\ItemType($id);
		if (empty($productData->Size)) {
			throw new securityException();
		}
		$productSize = $productData->Size;
	}

	$avaibleCargo = storageCargo::sGetTotalUserSpace ( $userID, $portProperties->PortID ) - $storageCargo->getUsage ();
	$usedByMove = $originalValue * $productSize;

	if ($usedByMove > $avaibleCargo) {
		$toMove = floor ( $avaibleCargo / $productSize );
	} else {
		$toMove = $originalValue;
	}

	if ($originalValue > $shipCargo->getAmount ( $id, $subaction )) {
		$originalValue = $shipCargo->getAmount ( $id, $subaction );
	}

	if (! is_numeric ( $originalValue )) {
		throw new securityException ( );
	}

	if ($originalValue < 0) {
		throw new securityException ( );
	}

	if (! $error) {
		/*
		 * Dodaj do ładowni
		*/
		$shipCargo->decAmount ( $id, $subaction, $toMove );
		$storageCargo->incAmount ( $id, $subaction, $toMove );

        \Gameplay\Model\ShipProperties::updateUsedCargo ( $shipProperties );

		shipCargo::management ( $userID );

        $sectorProperties->resetResources();
        $portProperties->reset();
        \Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', '{T:opSuccess}');
	}
}

//---------------------------------------------------------------------------------------------------
//                                                     Sprzedaż itemów do portu
//---------------------------------------------------------------------------------------------------
if ($action == "itemSell") {

	$cargoAmount = $shipCargo->getItemAmount ( $id );

	//Sprawdz, czy port skupuje tego typu itemy
	$tItems = $portProperties->Items;
	$tItems = "," . $tItems;

	if (strpos ( $tItems, "," . $id ) === false) {
		throw new securityException ( );
	}

	if ($value < 1) {
		throw new securityException ( );
	}

	if (! is_numeric ( $value )) {
		throw new securityException ( );
	}

	if ($cargoAmount < $value) {
		throw new securityException ( );
	}

    $productData = new \Gameplay\Model\ItemType($id);

	$shipCargo->setAmount ( $id, 'item', $cargoAmount - $value );

	//Zwiększyć kaskę usera
	$userStats->Cash += $productData->Price * $value;
    $userStats->incExperience($productData->Experience * $value);

	\Gameplay\Model\ShipProperties::updateUsedCargo ( $shipProperties );

	//Update portu po zakończeniu handlu
	$portProperties->Experience += $productData->Experience * $value;
	$portProperties->Cash -= floor ( ($productData->Price * $value) / 2 );

	\Gameplay\Model\PortEntity::sCheckNewLevel($portProperties);

    $portProperties->reset();

    \Gameplay\Panel\SectorShips::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
	\Gameplay\Panel\SectorResources::getInstance()->hide ();
	$action = "portMarketplace";
	\Gameplay\Model\PortEntity::sPopulatePanel ( $userID, $shipPosition, $portProperties, $action, $subaction, $value, $id );
	\Gameplay\Panel\PortAction::getInstance()->clear ();
    \Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', '{T:opSuccess}');
}

//---------------------------------------------------------------------------------------------------
//                                                     Sprzedaż towarów do portu
//---------------------------------------------------------------------------------------------------
if ($action == "productSell") {

	$portAmount = \Gameplay\Model\ProductType::sGetAmountInPort ( $portProperties->PortID, $id, 'product', 'sell' );
	$cargoAmount = $shipCargo->getProductAmount ( $id );

	if ($value < 1) {
		throw new securityException ( );
	}

	if (! is_numeric ( $value )) {
		throw new securityException ( );
	}

	if ($cargoAmount < $value) {
		throw new securityException ( );
	}

	//Oblicz cenę towaru
    $productData = new \Gameplay\Model\ProductType($id);
	$productPrice = $productData->getPrice ( $portAmount );
	$productExperience = $productData->getExperienceForSell( $portAmount );

	//Zmniejsz wartość magazynu portu
	$tQuery = "UPDATE portcargo SET Amount=Amount+'$value' WHERE PortID='{$portProperties->PortID}' AND CargoID='$id' AND Type='product' AND portcargo.UserID IS NULL";
	$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );

	//Zwiększ zawartość ładowni
	$shipCargo->setAmount ( $id, 'product', $cargoAmount - $value );

	//Zwiększyć kaskę usera
	$userStats->Cash += $productPrice * $value;
    $userStats->incExperience($productExperience * $value);

	\Gameplay\Model\ShipProperties::updateUsedCargo ( $shipProperties );

	//Update portu po zakończeniu handlu
	$portProperties->Experience += $productExperience * $value;
	$portProperties->Cash -= floor ( ($productPrice * $value) / 2 );

	\Gameplay\Model\PortEntity::sCheckNewLevel($portProperties);

    $portProperties->reset();
	\Gameplay\Panel\SectorShips::getInstance()->render ( $userID, $sectorProperties, $systemProperties, $shipPosition, $shipProperties );
	\Gameplay\Panel\SectorResources::getInstance()->hide ();
	$action = "portMarketplace";
	\Gameplay\Model\PortEntity::sPopulatePanel ( $userID, $shipPosition, $portProperties, $action, $subaction, $value, $id );
	\Gameplay\Panel\PortAction::getInstance()->clear ();
    \Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', '{T:opSuccess}');
}

//---------------------------------------------------------------------------------------------------
//                                                     Kupno towarów z portu
//---------------------------------------------------------------------------------------------------
if ($action == "productBuy") {

	$portAmount = \Gameplay\Model\ProductType::sGetAmountInPort ( $portProperties->PortID, $id, 'product', 'sell' );

	if (! is_numeric ( $value )) {
		throw new securityException ( );
	}

	if ($value < 1) {
		throw new securityException ( );
	}

	if ($portAmount < $value) {
		throw new securityException ( );
	}

    $productData = new \Gameplay\Model\ProductType($id);
	$productPrice = $productData->getPrice ( $portAmount );
	$productExperience = $productData->getExperienceForBuy ( $portAmount );

	//Sprawdz dostepnosc srodkow do zakupu
	if ($userStats->Cash < ($productPrice * $value)) {
		throw new warningException ( TranslateController::getDefault()->get ( 'notEnoughCash' ) );
	}

	//Sprawdz, czy w ladowni jest miejsce do zakupu
	if (($shipProperties->CargoMax - $shipProperties->Cargo) < ($productData->Size * $value)) {
		throw new warningException ( TranslateController::getDefault()->get ( 'notEnoughSpace' ) );
	}

	if ($shipProperties->Cargo > $shipProperties->CargoMax) {
		throw new warningException ( TranslateController::getDefault()->get ( 'notEnoughSpace' ) );
	}

	//Zmniejsz wartość magazynu portu
	$tQuery = "UPDATE portcargo SET Amount=Amount-'$value' WHERE PortID='{$portProperties->PortID}' AND CargoID='$id' AND Type='product' AND portcargo.UserID IS NULL";
	$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );

	$shipCargo->incAmount ( $id, 'product', $value );

	//Zmniejsz kasę usera
	$userStats->Cash -= $productPrice * $value;
    $userStats->incExperience ($productExperience * $value);

	\Gameplay\Model\ShipProperties::updateUsedCargo ( $shipProperties );

	//Update portu po zakończeniu handlu
	$portProperties->Experience += $productExperience * $value;
	$portProperties->Cash += $productPrice * $value;

	\Gameplay\Model\PortEntity::sCheckNewLevel($portProperties);

    $portProperties->reset();
	\Gameplay\Panel\SectorResources::getInstance()->hide ();
	$action = "portMarketplace";
	\Gameplay\Model\PortEntity::sPopulatePanel ($userID, $shipPosition, $portProperties, $action, $subaction, $value, $id );
	\Gameplay\Panel\PortAction::getInstance()->clear ();
    \Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', '{T:opSuccess}');
}

/**
 *
 */
if ($action == "mapBuy") {

	$portAmount = \Gameplay\Model\ProductType::sGetAmountInPort ( $portProperties->PortID, $id, 'map', 'buy' );

	$value = 1;

	if ($portAmount < $value) {
		throw new securityException ( );
	}

	$productPrice = $config['port']['mapPrice'];

	if ($userStats->Cash < $productPrice * $value) {
		throw new warningException ( TranslateController::getDefault()->get ( 'notEnoughCash' ) );
	}

	//Zmniejsz wartość magazynu portu
	$tQuery = "UPDATE portcargo SET Amount=Amount-'$value' WHERE PortID='{$portProperties->PortID}' AND CargoID='$id' AND Type='map' AND portcargo.UserID IS NULL";
	$tQuery = \Database\Controller::getInstance()->execute ( $tQuery );

	userMap::sInsert($userID, $id);

	//Zmniejsz kasę usera
	$userStats->Cash -= $productPrice * $value;

	//Update portu po zakończeniu handlu
	$portProperties->Cash += $productPrice * $value;

    $portProperties->reset();
	\Gameplay\Panel\SectorResources::getInstance()->hide ();
	$action = "portMarketplace";
	\Gameplay\Model\PortEntity::sPopulatePanel ( $userID, $shipPosition, $portProperties, $action, $subaction, $value, $id );
	\Gameplay\Panel\Navigation::getInstance()->render($shipPosition, $shipRouting, $shipProperties);
	\Gameplay\Panel\PortAction::getInstance()->clear ();
    \Gameplay\Framework\ContentTransport::getInstance()->addNotification('success', '{T:opSuccess}');
}