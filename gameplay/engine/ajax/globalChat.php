<?php

try {

    /** @noinspection PhpIncludeInspection */
    require_once '../../common.php';

	$chatDb = \Database\Controller::getChatInstance();
	
	$retVal = new jsonChatData ( );
	$retVal->State = 1;

	if (empty ( $_SESSION ['userID'] )) {
		throw new customException ( 'Security Error' );
	}

	/**
	 * Pobierz wspisy z bazy
	 */
	if (! empty ( $_REQUEST ['lastID'] )) {
		$tQuery = "SELECT * FROM chatglobal WHERE ChatID>'{$_REQUEST['lastID']}' ORDER BY ChatID ASC LIMIT 30";
	} else {
		$tQuery = "SELECT * FROM chatglobal ORDER BY ChatID DESC LIMIT 30";
	}
	$tQuery = $chatDb->executeAndRetryOnDeadlock ( $tQuery );
	while ( $tResult = $chatDb->fetch ( $tQuery ) ) {
		$tObject = unserialize ( $tResult->Data );
		$retVal->push ( $tObject->render (), $tResult->ChatID );
	}

	if (empty ( $_REQUEST ['lastID'] )) {
		$retVal->reverse ();
	}

} catch ( customException $e ) {
	$retVal->State = 0;
	$retVal->Data = $e->getMessage ();
} catch ( Exception $e ) {
	$retVal->State = 0;
	$retVal->Data = 'Unexpected Error';
	psDebug::cThrow ( null, $e, array ('display' => false, 'send' => true ) );
}

echo json_encode ( $retVal );