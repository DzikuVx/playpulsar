<?php

/*
 * Inicjacja
 */
try {

	require_once '../../common.php';

	//	header ( 'Content-Type: text/xml' );
	header ( 'Content-Type: text/html' );

	if (empty ( $HTTP_RAW_POST_DATA )) {
		throw new Exception ( 'No input XML', 0 );
	}

	$sXml = new SimpleXMLElement ( $HTTP_RAW_POST_DATA );

	$tClass = ( string ) $sXml->methodName;

	if (empty ( $tClass )) {
		throw new Exception ( 'No method string specified', 1 );
	}

	$tClass = explode ( '::', $tClass );

	if (empty ( $tClass [1] )) {
		throw new Exception ( 'No method specified', 3 );
	}

	if (empty ( $tClass [0] )) {
		throw new Exception ( 'No class specified', 2 );
	}

	$tMethod = $tClass [1];
	$tClass = $tClass [0];

	if (! class_exists ( $tClass )) {
		throw new Exception ( 'Illegal class', 4 );
	}

	$tObject = new $tClass ( );

	if (! method_exists ( $tObject, $tMethod )) {
		throw new Exception ( 'Unknown method', 5 );
	}

	/*
	 * Pobierz parametry do tablicy
	 */
	$tParams = $sXml->params;
	if (empty ( $tParams )) {
		throw new Exception ( 'No params specified', 6 );
	}
	$tParamsArray = array ();
	foreach ( $sXml->params->param as $tParam ) {
		$tParamsArray [] = ( string ) $tParam->value;
	}

	$tResponse = $tObject->{$tMethod} ( $tParamsArray );

	$retVal = '<?xml version="1.0"?>';
	$retVal .= '<methodResponse>';
	$retVal .= '<params>';
	$retVal .= '<value>';
	$retVal .= $tResponse;
	$retVal .= '</value>';
	$retVal .= '</params>';
	$retVal .= '</methodResponse>';

	//@todo: XML-PRC przerobić na wersję zwracającą rzeczywisty XML


} catch ( Exception $e ) {

	$retVal = '<?xml version="1.0"?>';
	$retVal .= '<methodResponse>';
	$retVal .= '<fault>';
	$retVal .= '<value>';
	$retVal .= '<faultCode>';
	$retVal .= $e->getCode ();
	$retVal .= '</faultCode>';
	$retVal .= '<faultString>';
	$retVal .= trim ( strip_tags ( $e->getMessage () ) );
	$retVal .= '</faultString>';
	$retVal .= '</value>';
	$retVal .= '</fault>';
	$retVal .= '</methodResponse>';

}

echo $retVal;

?>