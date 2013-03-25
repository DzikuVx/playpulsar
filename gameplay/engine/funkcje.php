<?php

function routingSort($a, $b) {

	if ($a->value == $b->value) {
		return 0;
	}
	return ($a->value < $b->value) ? 1 : - 1;
}

function clearActionPanel() {

	global $actionPanel;
	$actionPanel = "&nbsp;";
}

function shipExamine($id, $userID) {

	global $actionPanel;

	$item = new userStats ( );
	$otheruserStats = $item->load ( $id, true, true );
	unset($item);

	$item = new userProperties ( );
	$otheruserParameters = $item->load ( $id, true, true );
	unset($item);

	$item = new shipProperties ( );
	$othershipParameters = $item->load ( $id, true, true );
	unset($item);

	$item = new userAlliance ( );
	$othershipAlliance = $item->load ( $id, true, true );
	unset($item);

	$otheruserTimes = new userTimes ($id );

	if($otheruserParameters->FacebookID) {
		$avatar = user::sGetFbPictureUrl($otheruserParameters->FacebookID);
	}elseif (file_exists ( "../avatars/user_" . $id . ".jpg" )) {
		$avatar = "avatars/user_" . $id . ".jpg";
	}

	if (empty($avatar)) {
		$avatar = "avatars/unknown.jpg";
	}

	sectorResourcePanel::getInstance()->hide ();
	sectorShipsPanel::getInstance()->hide ();

	$actionPanel = "<div align=\"center\">";
	if ($otheruserParameters->Type == 'player' && $id != $userID) {
		$actionPanel .= '<div class="panel" style="width: 150px; float: right; text-align: center;">';
		$actionPanel .= General\Controls::renderButton ( TranslateController::getDefault()->get ( 'sendMessage' ), "executeAction('sendMessage',null,null,'{$id}',null);", "width: 140px; margin: 2px;" );

		if (!buddyList::sCheckEntry($userID, $id)) {
			$actionPanel .= General\Controls::renderButton ( TranslateController::getDefault()->get ( 'Send buddy request' ), "executeAction('addToFiends',null,null,'{$id}',null);", "width: 140px; margin: 2px;" );
		}

		$actionPanel .= General\Controls::renderButton ( TranslateController::getDefault()->get ( 'Report abusement' ), "executeAction('reportAbusement',null,null,'{$id}',null);", "width: 140px; margin: 2px;" );

		$actionPanel .= "</div>";
	}elseif ($id == $userID) {
		$actionPanel .= '<div class="panel" style="width: 150px; float: right; text-align: center;">';
		$actionPanel .= General\Controls::renderButton ( TranslateController::getDefault()->get ( 'Account settings' ), "executeAction('accountSettings',null,null,null,null);", "width: 140px; margin: 2px;" );

		if ($othershipParameters->RookieTurns > 0) {
			$actionPanel .= General\Controls::renderButton ( TranslateController::getDefault()->get ( 'Drop rookie turns' ), "executeAction('dropRookie',null,null,null,null);", "width: 140px; margin: 2px;" );
		}

		//@todo Reset konta
		$actionPanel .= "</div>";
	}
	$actionPanel .= "<div class=\"panel infoLine\">";
	$actionPanel .= "<div class=\"avatar\"><img src=\"" . $avatar . "\" class=\"avatar\" style='width: 100px; height: 100px;' /></div>";
	$actionPanel .= "<div><b>" . TranslateController::getDefault()->get ( 'playername' ) . ": </b> " . $otheruserParameters->Name . "</div>";
	$actionPanel .= "<div><b>" . TranslateController::getDefault()->get ( 'playerid' ) . ": </b> " . $id . "</div>";
	$actionPanel .= "<div><b>" . TranslateController::getDefault()->get ( 'specialization' ) . ": </b> " . $othershipParameters->SpecializationName . "</div>";
	$actionPanel .= "<div><b>" . TranslateController::getDefault()->get ( 'alliance' ) . ": </b> " . $othershipAlliance->Name . "</div>";
	$actionPanel .= "<div><b>" . TranslateController::getDefault()->get ( 'lastaction' ) . ": </b> " . date ( "Y-m-d H:i", $otheruserTimes->LastAction ) . "</div>";
	$actionPanel .= "<div><b>" . TranslateController::getDefault()->get ( 'experiencefull' ) . ": </b> " . $otheruserStats->Experience . "</div>";
	$actionPanel .= "<div><b>" . TranslateController::getDefault()->get ( 'level' ) . ": </b> " . $otheruserStats->Level . "</div>";
	$actionPanel .= "<div><b>" . TranslateController::getDefault()->get ( 'kills' ) . ": </b> " . $otheruserStats->Kills . "</div>";
	$actionPanel .= "<div><b>" . TranslateController::getDefault()->get ( 'pirates' ) . ": </b> " . $otheruserStats->Pirates . "</div>";
	$actionPanel .= "<div><b>" . TranslateController::getDefault()->get ( 'raids' ) . ": </b> " . $otheruserStats->Raids . "</div>";
	$actionPanel .= "<div><b>" . TranslateController::getDefault()->get ( 'deaths' ) . ": </b> " . $otheruserStats->Deaths . "</div>";
	$actionPanel .= "</div>";
	$actionPanel .= "<div class=\"closeButton\" onClick=\"executeAction('shipRefresh',null,null,null,null);\">" . TranslateController::getDefault()->get ( 'close' ) . "</div>";
	$actionPanel .= "</div>";
}

function writeDebug($text) {

	global $debug;
	$debug .= $text . "<br />";
}

function getParameterColor($current, $max) {

	global $colorTable;

	if (empty ( $max )) {
		return "style=\"color: " . $colorTable ['red'] . ";\"";
	}

	$out = "style=\"color: ";

	$temp = $current / $max;

	if ($temp > 0.66)
	$out .= $colorTable ['green'];
	if (($temp <= 0.66) and ($temp >= 0.33))
	$out .= $colorTable ['yellow'];
	if ($temp < 0.33)
	$out .= $colorTable ['red'];

	$out .= ";\"";
	return $out;
}
