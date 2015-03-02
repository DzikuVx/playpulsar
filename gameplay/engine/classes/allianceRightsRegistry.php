<?php
class allianceRightsRegistry extends allianceMembersRegistry {

	protected function renderActionButtons($tR, $tRights, $userID) {

		$tString = \General\Controls::renderImgButton ( 'repair', "Playpulsar.gameplay.execute('setAllianceRight','',null,{$tR->UserID});", TranslateController::getDefault()->get ( 'set' ) );
		
		return $tString;
	}

}
