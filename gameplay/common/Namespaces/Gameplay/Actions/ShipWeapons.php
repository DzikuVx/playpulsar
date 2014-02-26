<?php

namespace Gameplay\Actions;

use Gameplay\Framework\ContentTransport;
use Gameplay\Model\WeaponType;
use Gameplay\PlayerModelProvider;

class ShipWeapons {

    private function __construct() {

    }

    /**
     * @param int $shipWeaponID
     * @return bool
     * @throws \securityException
     */
    static public function sMoveUp($shipWeaponID) {

        global $error;

        $shipWeapons = PlayerModelProvider::getInstance()->get('ShipWeapons');

        $tData = $shipWeapons->getSingle($shipWeaponID);

        /*
         * Warunki bezpieczeństwa
         */
        if (empty ($tData)) {
            throw new \securityException();
        }

        if (! $error) {

            $tOtherWeapon = $shipWeapons->getPrevSequence($tData->Sequence);

            if (empty ( $tOtherWeapon->Sequence )) {
                return false;
            }

            $shipWeapons->setSequence ( $shipWeaponID, $tOtherWeapon->Sequence );
            $shipWeapons->setSequence ( $tOtherWeapon->ShipWeaponID, $tData->Sequence );

            \shipWeaponsRegistry::sRender ();
        }
        return true;
    }

    /**
     * @param int $shipWeaponID
     * @return bool
     * @throws \securityException
     */
    static public function sMoveDown($shipWeaponID) {

        global $error, $shipWeapons;

        $shipWeapons = PlayerModelProvider::getInstance()->get('ShipWeapons');

        $tData = $shipWeapons->getSingle($shipWeaponID);

        /*
         * Warunki bezpieczeństwa
         */
        if (empty ( $tData )) {
            throw new \securityException ( );
        }

        if (! $error) {

            $tOtherWeapon = $shipWeapons->getNextSequence ( $tData->Sequence );

            if (empty ( $tOtherWeapon->Sequence )) {
                return false;
            }

            $shipWeapons->setSequence ( $shipWeaponID, $tOtherWeapon->Sequence );
            $shipWeapons->setSequence ( $tOtherWeapon->ShipWeaponID, $tData->Sequence );

            \shipWeaponsRegistry::sRender ();
        }
        return true;
    }

    /**
     * @param int $weaponID
     * @throws \securityException
     */
    static public function sSell($weaponID) {

        global $userStats, $shipProperties, $shipWeapons, $error;

        $shipPosition = PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = PlayerModelProvider::getInstance()->get('PortEntity');
        $shipWeapons = PlayerModelProvider::getInstance()->get('ShipWeapons');

        $shipProperties->updateWeaponsCount();

        if ($shipPosition->Docked == 'no') {
            throw new \securityException ( );
        }

        if ($portProperties->Type != 'station') {
            throw new \securityException ( );
        }

        if (! $shipWeapons->checkExists ( $weaponID )) {
            throw new \securityException ( );
        }

        if (! $error) {

            /**
             * Pobierz parametry
             */
            $tData = $shipWeapons->getSingle ( $weaponID );

            if ($tData->Damaged == 0) {
                $tPrice = floor ( $tData->Price / 2 );
            } else {
                $tPrice = floor ( $tData->Price / 8 );
            }

            $shipWeapons->remove ( $weaponID, $shipProperties );
            $userStats->incCash($tPrice);

            $portProperties->Cash -= $tPrice;
            if ($portProperties->Cash < 0) {
                $portProperties->Cash = 0;
            }

            \Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:weaponSold}' . $tPrice . '$' );
            \shipWeaponsRegistry::sRender ();
            $shipWeapons->computeOffensiveRating ( $shipProperties );
        }
    }

    /**
     * @param $weaponID
     * @throws \securityException
     */
    static public function sSellFromCargo($weaponID) {

        global $shipCargo, $userID, $userStats, $shipProperties;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = \Gameplay\PlayerModelProvider::getInstance()->get('PortEntity');

        if ($shipPosition->Docked == 'no') {
            throw new \securityException ( );
        }

        if ($portProperties->Type != 'station') {
            throw new \securityException ( );
        }

        if ($shipCargo->getWeaponAmount($weaponID) < 1) {
            throw new \securityException ( );
        }

        /**
         * Pobierz parametry
         */
        $tData = new \Gameplay\Model\WeaponType($weaponID);

        $tPrice = floor ( $tData->Price / 2 );

        $shipCargo->decAmount($weaponID, 'weapon', 1);
        $userStats->incCash($tPrice);

        $portProperties->Cash -= $tPrice;
        if ($portProperties->Cash < 0) {
            $portProperties->Cash = 0;
        }

        \Gameplay\Model\ShipProperties::updateUsedCargo ( $shipProperties );

        \shipCargo::management ( $userID );
        \Gameplay\Panel\SectorShips::getInstance()->hide ();
        \Gameplay\Panel\SectorResources::getInstance()->hide ();
        \Gameplay\Panel\PortAction::getInstance()->clear();
        \Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:weaponSold}' . $tPrice . '$' );
    }

    /**
     * @param int $shipWeaponID
     * @throws \securityException
     */
    static public function sReload($shipWeaponID) {

        global $error, $shipWeapons, $userStats;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = \Gameplay\PlayerModelProvider::getInstance()->get('PortEntity');

        $tData = $shipWeapons->getSingle($shipWeaponID);

        /*
         * Warunki bezpieczeństwa
         */

        if ($shipPosition->Docked == 'no') {
            throw new \securityException ( );
        }

        if ($portProperties->Type != 'station') {
            throw new \securityException ( );
        }

        $tReloadPrice = \Gameplay\Model\WeaponType::sGetReloadPrice($tData->WeaponID, $tData->Ammo);

        if ($userStats->Cash < $tReloadPrice) {
            throw new \securityException();
        }

        if (empty ( $tData->MaxAmmo )) {
            throw new \securityException();
        }

        if ($tData->Ammo == $tData->MaxAmmo) {
            throw new \securityException();
        }

        /**
         * Od 2011-05-24 broń można przeładować w dowolnej stacji
         */

        if (! $error) {

            $shipWeapons->reload ( $shipWeaponID, $tData->MaxAmmo );

            $userStats->decCash($tReloadPrice);
            $portProperties->Cash += $tReloadPrice;

            \Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:weaponReloadedFor}' . $tReloadPrice . '$' );
            \shipWeaponsRegistry::sRender ();
        }

    }

    /**
     * @param int $shipWeaponID
     * @throws \securityException
     */
    static public function sRepair($shipWeaponID) {

        global $error, $shipWeapons, $userStats;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = \Gameplay\PlayerModelProvider::getInstance()->get('PortEntity');

        $tData = $shipWeapons->getSingle ( $shipWeaponID );

        /*
         * Warunki bezpieczeństwa
         */

        if ($shipPosition->Docked == 'no') {
            throw new \securityException ( );
        }

        $oObject = new WeaponType($tData->WeaponID);
        $tRepairPrice = $oObject->getRepairPrice();

        if ($userStats->Cash < $tRepairPrice) {
            throw new \securityException ( );
        }

        if (!$error) {
            $shipWeapons->repair ( $shipWeaponID );

            $userStats->decCash($tRepairPrice);
            $portProperties->Cash += $tRepairPrice;

            \Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:weaponRepairedFor}' . $tRepairPrice . '$' );
            \shipWeaponsRegistry::sRender ();
        }
    }

    /**
     * @param int $weaponID
     * @throws \securityException
     */
    static public function sBuy($weaponID) {

        global $action, $userStats, $shipProperties, $shipWeapons;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = \Gameplay\PlayerModelProvider::getInstance()->get('PortEntity');

        $shipProperties->updateWeaponsCount();

        if ($shipPosition->Docked == 'no') {
            throw new \securityException ( );
        }

        if ($portProperties->Type != 'station') {
            throw new \securityException ( );
        }

        $tWeapon = new \Gameplay\Model\WeaponType($weaponID);

        if ($userStats->Cash < $tWeapon->Price) {
            throw new \securityException ( );
        }

        if ($userStats->Fame < $tWeapon->Fame) {
            throw new \securityException ( );
        }

        /**
         * czy port sprzedaje
         */
        $tString = ',' . $portProperties->Weapons . ',';
        if (mb_strpos ( $tString, ',' . $weaponID . ',' ) === false) {
            throw new \securityException ( );
        }

        $shipWeapons->insert($tWeapon, $shipProperties);
        $userStats->decCash($tWeapon->Price);
        $userStats->decFame($tWeapon->Fame);
        $portProperties->Cash += $tWeapon->Price;

        ContentTransport::getInstance()->addNotification( 'success', '{T:weaponBought}' . $tWeapon->Price . '$' );
        $action = "portHangar";
        $shipWeapons->computeOffensiveRating ( $shipProperties );
    }

} 