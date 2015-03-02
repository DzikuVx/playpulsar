<?php
psDebug::$displayErrors = true;
psDebug::$displayTrace = true;
psDebug::$sendTrace = true;
psDebug::$errorHoldsExecution = true;
psDebug::$standardErrorText = 'Unexpected error!';
psDebug::$additionalErrorText = '';
psDebug::$senderConfig ['url'] = 'playpulsar.localhost';
psDebug::$senderConfig ['path'] = '/errorCatcher.php';
psDebug::$senderConfig ['sender'] = 'Pulsar Control Panel';

$config['backend']['fileName'] = '/controlpanel/';

/**
 * Okres ważności wpisów w tabelach [h]
 * @var int
 */
$maintenance['messagesValid'] = 720;
$maintenance['newsAgencyValid'] = 720;
$maintenance['offensiveReportsValid'] = 8;
$maintenance['defensiveReportsValid'] = 144;
$maintenance['npcContactValid'] = 144;
$maintenance['cacheClearValid'] = 8;
$maintenance['portResetThreshold'] = 900;