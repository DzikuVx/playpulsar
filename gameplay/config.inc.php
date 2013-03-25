<?php
$actualTime = time ();

ini_set ( 'date.timezone', 'Europe/Warsaw' );
ini_set ( 'date.default_latitude', '31.7667' );
ini_set ( 'date.default_longitude', '35.2333' );
ini_set ( 'date.sunrise_zenith', '90.583333' );
ini_set ( 'date.sunset_zenith', '90.583333' );
date_default_timezone_set ( "Europe/Warsaw" );

$config['useApc'] = true;

/**
 * CDN url prefix
 * @example http://pulsar.mycdn.com/
 */
$config['general']['cdn'] = '';

$portalNews2Page = 1;

$config ['general'] ['enableLogin'] = true;
$config ['general'] ['enableRegister'] = true;
$config ['general'] ['enableGameplay'] = true;
$config ['general'] ['url'] = 'playpulsar.com';
$config ['general'] ['pageTitle'] = 'Pulsar Online';

/*
 * Needs to be implemeted
*/
$config['raiding']['enabled'] = false;

$config['activeScanner']['powerUsage'] = 75;
$config['activeScanner']['amUsage'] = 20;

/**
 * @since 2011-05-06
 */
$config ['debug'] ['db'] = true;
$config ['debug'] ['script'] = true;
$config ['debug'] ['gameplayDebugOutput'] = true;

/*
 * Experience gained for each move
*/
$config ['general'] ['expForMove'] = 2;

/*
 * Experience gained for each jump node jump
*/
$config ['general'] ['expForWarpJump'] = 100;

/**
 * New player default values
 */
$config ['userDefault'] ['ship'] = 1; //Default ship: clipper
$config ['userDefault'] ['weapons'] = "1,1"; //Default weapons, 2 small lasers
$config ['userDefault'] ['equipment'] = "";
$config ['userDefault'] ['system'] = "1,2";
$config ['userDefault'] ['specialization'] = 1;
$config ['userDefault'] ['rookie'] = 200;
$config ['userDefault'] ['turns'] = 400;
$config ['userDefault'] ['cash'] = 2000;
$config ['userDefault'] ['experience'] = 0;

/*
 * Traxium (previously "fame") configuration
*/
$config ['fame'] ['multiplier'] = 1;
$config ['fame'] ['cap'] = 5; //Max Traxium amount that can be adden at once for passive income
$config ['fame'] ['resetThreshold'] = 86400; //Number of seconds that passes between passive Traxium gaining

/*
 * Alliances
*/
$config ['alliance']['createFameCost'] = 20; //Traxium cost for alliance creation

/*
 * Time thresholds
* Do not modify, very fragile
*/
$config ['timeThresholds'] ['npcReset'] = 1800; // Number of second between player's NPC reset triggers
$config ['timeThresholds'] ['npcMove'] = 5; // Number of second between player's NPC move triggers
$config ['timeThresholds'] ['portReset'] = 1800; //Number of second between player's Port reset triggers
$config ['timeThresholds'] ['sectorReset'] = 1800; // Number of second between player's Sector reset triggers
$config ['timeThresholds'] ['shipRepair'] = 1; //Number of second between player's ship (auto)repair triggers
$config ['timeThresholds'] ['turnsReset'] = 3600; //Number of second between player's Antimatter generator triggers

/*
 * NPC Settings
*/

/*
 * number of NPCs that can me moved at once
* lower this value in case of overlapping move attempts
*/
$config ['npc'] ['simulaneousMoveLimit'] = 25;

/**
 * Probability, that playe-npc contact will be strored for further use
 * in percents [%]
 */
$config ['npc'] ['contactProbablity'] = 25;

/**
 * Do not change
 * Max probability of player-npc contact store
 */
$config ['npc'] ['contactProbablityMax'] = 100;

/*
 * NPC behavior radius
* Used by protective NPC's for combat assists
*/
$config ['npc'] ['behaviorRadius'] = 3;

/*
 * Probability that defending ship will call for protective NPCs (summon)
*/
$config ['npc'] ['protectiveSummonProbability'] = 60;

/*
 * Combat mechanics setup
*/
$config ['combat'] ['autoDisengageThreshold'] = 30;
$config ['combat'] ['salvoInterval'] = 5;
$config ['combat'] ['shdPenetrationProbability'] = 10;
$config ['combat'] ['npcSimultanousCombat'] = 5;
$config ['combat'] ['weaponToSpaceProbability'] = 10;
$config ['combat'] ['equipmentToSpaceProbability'] = 10;
$config ['combat'] ['weaponDestroyProbability'] = 50;
$config ['combat'] ['equipmentDestroyProbability'] = 50;
$config ['combat'] ['itemToSpaceProbability'] = 50;
$config ['combat'] ['killRookieTurns'] = 200;
$config ['combat'] ['weaponDamageProbability'] = 10;
$config ['combat'] ['equipmentDamageProbability'] = 10;

/**
 * how many protective NPCs can attack a player
 */
$config ['combat'] ['protectiveNpcSummonLimit'] = 3;

/*
 * Number of second after last player's action that removes player from "online players" registruy
*/
$config ['user'] ['onlineThreshold'] = 900;

/**
 * How many top players are listed in "Top players" registry
 */
$config ['user'] ['topPlayers'] = 20;

$config ['equipment'] ['repairCost'] ['cash'] = 0.5;
$config ['weapon'] ['repairCost'] ['cash'] = 0.5;


/**
 * EMP auto repair ratio 
 * (how many emp units are repaired every second)
 */
$config ['emp'] ['repairRatio'] = 5;

/*
 * repair cost
*/
$config ['repairCost'] ['shield'] = 100;
$config ['repairCost'] ['armor'] = 200;
$config ['repairCost'] ['power'] = 100;
$config ['repairCost'] ['emp'] = 200;

$turnsToEquip = 10;

$config ['node'] ['jumpCostTurns'] = 20;
$config ['node'] ['jumpCostPower'] = 25;

$itemPickCost = 10;
$itemJettisonCost = 1;

/*
 * Animatter (Am) (former turns) regeneration settings
*/
$config ['turns'] ['capLimit'] = 200; //Max Am amount = Speed * Cap
$config ['turns'] ['multiplier'] = 1; //Am regeneration multilier


/*
 * Port configuration
*/
$config ['port'] ['maxCargoAmount'] = 5000;
$config ['port'] ['cargoThresholdHigh'] = 4800;
$config ['port'] ['cargoThresholdLow'] = 200;
$config ['port'] ['cargoChangeRatio'] = 20;
$config ['port'] ['storageSpace'] = 100;
$config ['port'] ['storageSpacePrice'] = 100000;
$config ['port'] ['levelMin'] = 5;
$config ['port'] ['levelMax'] = 30;
$config ['port'] ['mapCreateCount'] = 3;

/*
 * System map price
*/
$config ['port'] ['mapPrice'] = 85000;


/*
 * Sector configuration
*/
$config ['sector'] ['maxResources'] = 5000;
$config ['sector'] ['resourceDivider'] = 20;

/**
 * item casting settings
 */
$itemCastProbablity = 2; //1-1000
$itemCastMaxProbablity = 1000;

$icons ['MoveUp'] = $config['general']['cdn']."gfx/moveup2.png";
$icons ['MoveDown'] = $config['general']['cdn']."gfx/movedown2.png";
$icons ['MoveLeft'] = $config['general']['cdn']."gfx/moveleft2.png";
$icons ['MoveRight'] = $config['general']['cdn']."gfx/moveright2.png";
$icons ['Refresh'] = $config['general']['cdn']."gfx/refresh2.png";
$icons ['Dock'] = $config['general']['cdn']."gfx/dock.png";
$icons ['Undock'] = $config['general']['cdn']."gfx/undock.png";

$defaultPortProperties ['PortID'] = null;
$defaultPortProperties ['PortTypeID'] = null;
$defaultPortProperties ['PortTypeName'] = null;
$defaultPortProperties ['Name'] = null;
$defaultPortProperties ['ResetTime'] = null;
$defaultPortProperties ['Type'] = null;
$defaultPortProperties ['SpecialBuy'] = '';
$defaultPortProperties ['SpecialSell'] = '';
$defaultPortProperties ['NoBuy'] = '';
$defaultPortProperties ['NoSell'] = '';
$defaultPortProperties ['Items'] = null;
$defaultPortProperties ['Weapons'] = null;
$defaultPortProperties ['Equipment'] = null;
$defaultPortProperties ['Ships'] = null;
$defaultPortProperties ['Shield'] = 0;
$defaultPortProperties ['Armor'] = 0;
$defaultPortProperties ['OffRating'] = 0;
$defaultPortProperties ['DefRating'] = 0;
$defaultPortProperties ['Image'] = '';
$defaultPortProperties ['Cash'] = 0;
$defaultPortProperties ['Experience'] = 0;
$defaultPortProperties ['Level'] = 0;
$defaultPortProperties ['State'] = null;

$defaultSectorProperties ['SectorID'] = null;
$defaultSectorProperties ['Name'] = 'deepspace';
$defaultSectorProperties ['Color'] = '#000000';
$defaultSectorProperties ['Image'] = 'gfx/sectors/ds.gif';
$defaultSectorProperties ['MoveCost'] = 2;
$defaultSectorProperties ['Visibility'] = '100';
$defaultSectorProperties ['Accuracy'] = '100';
$defaultSectorProperties ['Resources'] = '';
$defaultSectorProperties ['ResetTime'] = null;

//Inicjacja sesji
if (! isset ( $_SESSION ['userLanguage'] )) {
	$_SESSION ['userLanguage'] = 'en';
}

$maxBackgroundCount = 4;

/**
 * psDebug configuration
 * @see https://sourceforge.net/projects/psdebug/
 */
psDebug::$displayErrors = true;
psDebug::$displayTrace = true;
psDebug::$sendTrace = true;
psDebug::$writeTrace = false;
psDebug::$errorHoldsExecution = true;
psDebug::$standardErrorText = 'Unexpected error!';
psDebug::$additionalErrorText = 'Help Pulsar development and report this error <a href="http://board.playpulsar.com/forum-4.html">here</a>';
psDebug::$senderConfig ['sender'] = 'Pulsar';
psDebug::create ();

if (empty ( $deploymentMode )) {
	psDebug::$senderConfig ['url'] = 'localhost';
	psDebug::$senderConfig ['path'] = '/pulsar_public/gameplay/errorCatcher.php';
}else {
	psDebug::$senderConfig ['url'] = 'localhost';
	psDebug::$senderConfig ['path'] = '/errorCatcher.php';
}

if (empty ( $HTTP_RAW_POST_DATA )) {
	$inputSocket = fopen ( 'php://input', 'rb' );
	$HTTP_RAW_POST_DATA = stream_get_contents ( $inputSocket );
	fclose ( $inputSocket );
}

/**
 * facebook app config
 * @since 2011-03-20
 */
/**
 * Pulsar localhost app
 */
$config['facebook']['appId'] = '200311930013717';
$config['facebook']['secret'] = '60d01bbf63521d029b22c4fc27d4d8b6';
$config['facebook']['cookie'] = true;

/*
 * Google Analytics settings
 * leave empty to disable
 */
$config['analytics']['id'] = 'UA-7536599-7';
$config['analytics']['domain'] = '.playpulsar.com';

/**
 *
 * SMTP stuff goes here
 * @since 2011-05-25
 */
define ( "SASL_INTERACT", 2 );
define ( "SASL_CONTINUE", 1 );
define ( "SASL_OK", 0 );
define ( "SASL_FAIL", - 1 );
define ( "SASL_NOMECH", - 4 );

/*
 * To enable mailing fill those fields
*/
$config ['smtp'] = array ();
$config ['smtp'] ['enable'] = false;
$config ['smtp'] ['host'] = "";
$config ['smtp'] ['port'] = 465;
$config ['smtp'] ['ssl'] = 'ssl';
$config ['smtp'] ['user'] = "";
$config ['smtp'] ['pass'] = "";
$config ['smtp'] ['from'] ['email'] = 'support@playpulsar.com';
$config ['smtp'] ['from'] ['name'] = 'Pulsar Online Support';
$config ['smtp'] ['reply'] ['email'] = 'support@playpulsar.com';
$config ['smtp'] ['reply'] ['name'] = 'Pulsar Online Support';