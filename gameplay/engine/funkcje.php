<?php

function routingSort($a, $b) {

	if ($a->value == $b->value) {
		return 0;
	}
	return ($a->value < $b->value) ? 1 : - 1;
}

function shipExamine($id, $userID) {

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

	\Gameplay\Panel\SectorResources::getInstance()->hide ();
	\Gameplay\Panel\SectorShips::getInstance()->hide ();

	$sRetVal = "";
	$sRetVal .= \General\Controls::bootstrapIconButton('{T:close}',"Playpulsar.gameplay.execute('shipRefresh',null,null,null,null)",'pull-right close','icon-remove');
	$sRetVal .= "<div>";
	$sRetVal .= "<table class='table table-striped'>";
	$sRetVal .= "<tr><th>{T:playername}</th><td>" . $otheruserParameters->Name . "</td>";
	$sRetVal .= "<td rowspan='3' colspan='2' class='center'><img src=\"" . $avatar . "\" class='avatar' style='width: 100px; height: 100px;' /></td></tr>";
	$sRetVal .= "<tr><th>{T:playerid}</th><td> " . $id . "</td></tr>";
	$sRetVal .= "<tr><th>{T:specialization}</th><td>" . $othershipParameters->SpecializationName . "</td></tr>";
	$sRetVal .= "<tr><th>{T:alliance}</th><td>" . $othershipAlliance->Name . "</td>";
	$sRetVal .= "<th>{T:lastaction}</th><td>" . date ( "Y-m-d H:i", $otheruserTimes->LastAction ) . "</td></tr>";
	$sRetVal .= "<tr><th>{T:experiencefull}</th><td>" . $otheruserStats->Experience . "</td>";
	$sRetVal .= "<th>{T:level}</th><td>" . $otheruserStats->Level . "</td></tr>";
	$sRetVal .= "<tr><th>{T:kills}</th><td>" . $otheruserStats->Kills . "</td>";
	$sRetVal .= "<th>{T:deaths}</th><td>" . $otheruserStats->Deaths . "</td></tr>";
	// 	$sRetVal .= "<tr><th>{T:pirates}</th><td>" . $otheruserStats->Pirates . "</td>";
	// 	$sRetVal .= "<th>{T:raids}</th><td>" . $otheruserStats->Raids . "</td></tr>";
	$sRetVal .= "</table>";

	if ($otheruserParameters->Type == 'player' && $id != $userID) {

		$sRetVal .= \General\Controls::bootstrapButton('{T:sendMessage}',"Playpulsar.gameplay.execute('sendMessage',null,null,'{$id}');",null,'icon-white icon-envelope');

		if (!buddyList::sCheckEntry($userID, $id)) {
			$sRetVal .= \General\Controls::bootstrapButton('{T:Send buddy request}',"Playpulsar.gameplay.execute('addToFiends',null,null,'{$id}',null);",null,'icon-white icon-heart');
		}

		$sRetVal .= \General\Controls::bootstrapButton('{T:Report abusement}',"Playpulsar.gameplay.execute('reportAbusement',null,null,'{$id}',null);",null,'icon-white icon-flag');

	} elseif ($id == $userID) {
		$sRetVal .= \General\Controls::bootstrapButton('{T:Account settings}',"Playpulsar.gameplay.execute('accountSettings',null,null,null,null);",null,'icon-white icon-edit');

		if ($othershipParameters->RookieTurns > 0) {
			$sRetVal .= \General\Controls::bootstrapButton('{T:Drop rookie turns}',"Playpulsar.gameplay.execute('dropRookie',null,null,null,null);",null,'icon-white icon-remove');
		}

		//@todo Reset konta
	}

	$sRetVal .= "</div>";

	\Gameplay\Panel\Action::getInstance()->add($sRetVal);

}

/**
 * @deprecated
 * @param unknown_type $current
 * @param unknown_type $max
 */
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
