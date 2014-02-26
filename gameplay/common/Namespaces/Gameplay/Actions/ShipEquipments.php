<?php

namespace Gameplay\Actions;

use Gameplay\Model\EquipmentType;
use Gameplay\PlayerModelProvider;

class ShipEquipments {

    private function __construct() {

    }

    /**
     * @param $equipmentID
     * @throws \securityException
     */
    static public function sBuy($equipmentID) {

        global $action, $userStats, $shipProperties, $error;

        $shipPosition = PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = PlayerModelProvider::getInstance()->get('PortEntity');
        $shipEquipment = PlayerModelProvider::getInstance()->get('ShipEquipments');

        if ($shipPosition->Docked == 'no') {
            throw new \securityException ( );
        }

        if ($portProperties->Type != 'station') {
            throw new \securityException ( );
        }

        $tEquipment = new EquipmentType($equipmentID);

        if ($userStats->Cash < $tEquipment->Price) {
            throw new \securityException ( );
        }

        if ($userStats->Fame < $tEquipment->Fame) {
            throw new \securityException ( );
        }

        if ($tEquipment->Type == 'equipment' && $shipEquipment->checkExists ( $tEquipment )) {
            throw new \securityException ( );
        }

        /**
         * czy port sprzedaje
         */
        $tString = ',' . $portProperties->Equipment . ',';
        if (mb_strpos ( $tString, ',' . $equipmentID . ',' ) === false) {
            throw new \securityException ( );
        }

        if (! $error) {
            $shipEquipment->insert($tEquipment, $shipProperties);
            $userStats->decCash($tEquipment->Price);
            $userStats->decFame($tEquipment->Fame);
            $portProperties->Cash += $tEquipment->Price;

            \Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:equipmentBought}' . $tEquipment->Price . '$' );
            $shipProperties->computeMaxValues();
            $action = "portHangar";
        }
    }

    /**
     * @param $equipmentID
     * @throws \securityException
     */
    static public function sStationRepair($equipmentID) {

        global $error;

        $oProvider = PlayerModelProvider::getInstance();
        $portProperties = $oProvider->get('PortEntity');
        $shipPosition = $oProvider->get('ShipPosition');
        $shipEquipment = $oProvider->get('ShipEquipments');
        $shipProperties = $oProvider->get('ShipProperties');
        $userStats = $oProvider->get('UserStatistics');

        if ($shipPosition->Docked == 'no') {
            throw new \securityException ( );
        }

        $tEquipment = $shipEquipment->getSingle ( $equipmentID );

        $oObject = new EquipmentType($tEquipment->EquipmentID);
        $tRepairPrice = $oObject->getRepairPrice();

        if ($userStats->Cash < $tRepairPrice) {
            throw new \securityException ( );
        }

        if (! $error) {
            $shipEquipment->repair ( $equipmentID );
            $userStats->decCash($tRepairPrice);
            $portProperties->Cash += $tRepairPrice;

            \Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:equipmentRepaired}' . $tRepairPrice . '$' );
            $shipProperties->computeMaxValues();
            \shipEquipmentRegistry::sRender ();
        }
    }

    /**
     * @param int $equipmentTypeId
     * @throws \securityException
     */
    static public function sSellFromCargo($equipmentTypeId) {

        global $shipCargo, $userID, $userStats, $shipProperties;

        $shipPosition = \Gameplay\PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = \Gameplay\PlayerModelProvider::getInstance()->get('PortEntity');

        if ($shipPosition->Docked == 'no') {
            throw new \securityException ( );
        }

        if ($portProperties->Type != 'station') {
            throw new \securityException ( );
        }

        if ($shipCargo->getEquipmentAmount($equipmentTypeId) < 1) {
            throw new \securityException ( );
        }

        $tData = new EquipmentType($equipmentTypeId);

        $tPrice = floor ( $tData->Price / 2 );

        $shipCargo->decAmount($equipmentTypeId, 'equipment', 1);
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
        \Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:equipmentSold}' . $tPrice . '$' );
    }

    /**
     * @param int $equipmentID
     * @throws \securityException
     */
    static public function sSell($equipmentID) {

        global $userStats, $shipProperties, $error;

        $shipPosition = PlayerModelProvider::getInstance()->get('ShipPosition');
        $portProperties = PlayerModelProvider::getInstance()->get('PortEntity');
        $shipEquipment = PlayerModelProvider::getInstance()->get('ShipEquipments');

        if ($shipPosition->Docked == 'no') {
            throw new \securityException ( );
        }

        if ($portProperties->Type != 'station') {
            throw new \securityException ( );
        }

        if (! $shipEquipment->checkExists ( $equipmentID )) {
            throw new \securityException ( );
        }

        if (! $error) {

            $tData = $shipEquipment->getSingle ( $equipmentID );

            if ($tData->Damaged == 0) {
                $tPrice = floor ( $tData->Price / 2 );
            } else {
                $tPrice = floor ( $tData->Price / 8 );
            }

            $shipEquipment->remove ( $equipmentID, $shipProperties );

            $userStats->incCash($tPrice);

            $portProperties->Cash -= $tPrice;
            if ($portProperties->Cash < 0) {
                $portProperties->Cash = 0;
            }

            \Gameplay\Framework\ContentTransport::getInstance()->addNotification( 'success', '{T:equipmentSold}' . $tPrice . '$' );
            $shipProperties->computeMaxValues();
            \shipEquipmentRegistry::sRender ();
        }
    }
}