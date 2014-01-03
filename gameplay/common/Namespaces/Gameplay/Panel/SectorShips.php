<?php

namespace Gameplay\Panel;

use Gameplay\Model\SectorEntity;
use Gameplay\Model\ShipPosition;
use Gameplay\Model\ShipProperties;
use Gameplay\Model\SystemProperties;
use Gameplay\Model\UserStatistics;
use Interfaces\Singleton;

use \TranslateController as Translate;
use \Database\Controller as Database;
use \General\Controls as Controls;

class SectorShips extends Renderable implements Singleton
{
    protected $onEmpty = "clearAndHide";
    protected $panelTag = "SectorShips";

    /**
     * @var SectorShips
     */
    private static $instance = null;

    /**
     * @return SectorShips
     * @throws \Exception
     */
    static public function getInstance()
    {
        if (empty(self::$instance)) {
            throw new \Exception('Panel not initialized');
        } else {
            return self::$instance;
        }
    }

    static public function initiateInstance($language = 'pl', $localUserID = null)
    {
        self::$instance = new self($language, $localUserID);
    }

    /**
     * @param int $userID
     * @param SectorEntity $sectorProperties
     * @param SystemProperties $systemProperties
     * @param ShipPosition $shipPosition
     * @param ShipProperties $shipProperties
     * @return bool
     */
    public function render($userID, SectorEntity $sectorProperties, /** @noinspection PhpUnusedParameterInspection */
                           SystemProperties $systemProperties, ShipPosition $shipPosition, ShipProperties $shipProperties)
    {

        global $config, $userStats, $userAlliance;

        $oDb = \Database\Controller::getInstance();

        $this->rendered = true;

        $this->retVal = '';
        $nameField = "Name" . $this->language;

        $tQuery = "SELECT
                userships.UserID AS UserID,
                userships.RookieTurns AS RookieTurns,
                users.Name AS PlayerName,
                users.Type AS UserType,
                userstats.Level AS Level,
                specializations.$nameField AS SpecializationName,
                shiptypes.$nameField AS ShipTypeName,
                userships.OffRating AS OffRating,
                userships.DefRating AS DefRating,
                userships.Cloak AS Cloak,
                alliances.Name As AllianceName,
                alliances.AllianceID As AllianceID,
                npctypes.Behavior,
                usertimes.LastAction
            FROM
                shippositions JOIN userships ON userships.UserID=shippositions.UserID
                JOIN shiptypes ON shiptypes.ShipID = userships.ShipID
                JOIN users ON users.UserID = shippositions.UserID
                JOIN userstats ON userstats.UserID=shippositions.UserID
                LEFT JOIN specializations ON specializations.SpecializationID = userships.SpecializationID
                LEFT JOIN alliancemembers ON alliancemembers.UserID=shippositions.UserID
                LEFT JOIN alliances ON alliances.AllianceID = alliancemembers.AllianceID
                LEFT JOIN npctypes ON npctypes.NPCTypeID=users.NPCTypeID
                LEFT JOIN usertimes ON usertimes.UserID=users.UserID
            WHERE
                shippositions.System={$shipPosition->System} AND
                shippositions.X={$shipPosition->X} AND
                shippositions.Y={$shipPosition->Y} AND
                shippositions.Docked='{$shipPosition->Docked}' AND
                shippositions.UserID != '$userID' AND
                userstats.Experience > 0";
        $tQuery = $oDb->execute($tQuery);

        if ($oDb->count($tQuery) > 0) {
            $this->retVal .= "<h1>{T:ships}</h1>";
        }

        $shipDisplayed = false;

        while ($tR1 = $oDb->fetch($tQuery)) {

            /*
             * players from the same alliance sees each other every time
            */
            if (empty($userAlliance->AllianceID) || $userAlliance->AllianceID != $tR1->AllianceName) {

                /**
                 * sprawdz widzialność
                 */
                if ($shipPosition->Docked == 'no' && !ShipProperties::sGetVisibility($shipProperties, $userStats, new ShipProperties($tR1->UserID), new UserStatistics($tR1->UserID), $sectorProperties)) {
                    continue;
                }
            }

            $shipDisplayed = true;

            if ($tR1->AllianceName == null) {
                $tR1->AllianceName = Translate::getDefault()->get('noalliance');
            }

            $tDisplay = false;
            if ($tR1->UserType == 'npc') {
                $tDisplay = true;
            } else {

                if (empty($tR1->LastAction)) {
                    $tR1->LastAction = 0;
                }

                if (time() - $tR1->LastAction < $config ['user'] ['onlineThreshold']) {
                    $tDisplay = true;
                }
            }

            $this->retVal .= "<div class='well ship'>";

            $this->retVal .= "<div class='pull-right'>";
            if ($tDisplay) {
                $this->retVal .= "<img style='margin-right: 4px; margin-top: 2px;' src='{$config['general']['cdn']}gfx/pplonline.png' title='" . Translate::getDefault()->get('Online') . "' />";
            }
            if ($tR1->RookieTurns > 0) {
                $this->retVal .= "<img style='margin-right: 4px; margin-top: 2px;' src='{$config['general']['cdn']}gfx/hasrookie.png' title='" . Translate::getDefault()->get('Rookie protected') . "' />";
            }
            $this->retVal .= "</div>";

            $this->retVal .= "<div class='strong em12'>{$tR1->PlayerName}</div>";
            $this->retVal .= "<div class='strong'>{$tR1->AllianceName}</div>";
            $this->retVal .= "<div style='clear: both;'>";
            $this->retVal .= "<div class='column50 strong'>{$tR1->SpecializationName}&nbsp;</div>";
            $this->retVal .= "<div class='column50 yellow'>{T:level} {$tR1->Level}</div>";
            $this->retVal .= "</div>";
            $this->retVal .= "<div style='clear: both;'>";
            $this->retVal .= "<div class='column50 strong'>{$tR1->ShipTypeName}</div>";
            $this->retVal .= "<div class='column50 green'>{$tR1->OffRating}/{$tR1->DefRating}</div>";
            $this->retVal .= "</div>";

            $this->retVal .= "<div style='clear: both; padding-top: 0.5em;'>";
            $this->retVal .= Controls::bootstrapIconButton('{T:examine}', "Playpulsar.gameplay.execute('shipExamine','',null,{$tR1->UserID});", 'btn-info', 'icon-search');

            if ($shipPosition->Docked == "no" && $tR1->RookieTurns < 1 && $shipProperties->RookieTurns < 1 && ($tR1->AllianceID != $userAlliance->AllianceID || empty($userAlliance->AllianceID))) {

                $this->retVal .= Controls::bootstrapIconButton('{T:attack}', "Playpulsar.gameplay.execute('shipAttack',null,null,{$tR1->UserID},null);", 'btn-danger', 'icon-fire');

                /*
                 * trigger aggressive NPC behavior
                */
                if ($tR1->Behavior == 'aggresive') {

                    \npc::sAggresiveController($userID, $tR1->UserID, $userStats->Level, $tR1->Level, $sectorProperties->Visibility);

                }

            }
            $this->retVal .= "</div>";

            $this->retVal .= "</div>";


            /*
             * Insert NPC Contact
            */
            if ($tR1->UserType == 'npc' && \additional::checkRand($config ['npc'] ['contactProbablity'], $config ['npc'] ['contactProbablityMax'])) {
                \npc::sInsertContact($userID, $tR1->UserID, $shipPosition);
            }
        }

        /*
         * Jeśli żaden statek nie był wyświetlony, ukryj panel
        */
        if (!$shipDisplayed) {
            $this->retVal = '';
        } else {
            $this->retVal .= "<div style='clear: both;'></div>";
        }

        return true;
    }
}