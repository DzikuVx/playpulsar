<?php
class allianceRightsRegistry extends allianceMembersRegistry {

	protected function renderActionButtons($tR, $tRights, $userID) {

		$tString = \General\Controls::renderImgButton ( 'repair', "executeAction('setAllianceRight','',null,{$tR->UserID});", TranslateController::getDefault()->get ( 'set' ) );
		
		return $tString;
	}

}
