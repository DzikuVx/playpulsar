<?php

/**
 * Przyjęcie i wstawienie do bazy danych wpisu czatu globalnego
 * @author Paweł Spychalski
 */
$retVal = 'OK';

try {

    /** @noinspection PhpIncludeInspection */
    require_once '../../common.php';

	if (empty ( $_SESSION ['userID'] )) {
		throw new customException ( 'Security Error' );
	}

	if (empty ( $_REQUEST ['text'] )) {
		throw new customException ( 'No data' );
	}

	$tObject = new chatEntry ( time (), $_SESSION ['userName'], $_REQUEST ['text'] );

	$tObject->save ();

} catch ( customException $e ) {
	$retVal = $e->getMessage ();
} catch ( Exception $e ) {
	$retVal = 'Unexpected Error';
	psDebug::cThrow ( null, $e, array ('display' => false, 'send' => true ) );
}

echo $retVal;