<?php

namespace Controlpanel;
use Gameplay\Model\PortEntity;
use \General\Controls as Controls;
use \General\Formater as Formater;
use \Database\Controller as Database;
use \stdClass as stdClass;

class Maintenance extends BaseItem {

	final private static function sResetAllAccounts() {

		if (\user::sGetRole () != 'admin') {
			throw new \customException ( 'No rights to perform selected operation' );
		}

		$tQuery = "SELECT UserID FROM users WHERE Type='player'";
		$tQuery = Database::getInstance()->execute($tQuery);
		while ($tResult = Database::getInstance()->fetch($tQuery)) {
			\user::sAccountReset($tResult->UserID);
		}

        \phpCache\Factory::getInstance()->create()->clearAll();
	}

    /**
     * @param \user $user
     * @param array $params
     * @return string
     * @throws \customException
     */
    final public function resetAll($user, $params) {

		if ($user->sGetRole () != 'admin') {
			throw new \customException ( 'No rights to perform selected operation' );
		}

		global $config;

		return Controls::dialog( "Confirm", "Do you want to <strong>reset all player accounts</strong>?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=resetAllExe'", "window.history.back();", 'Yes','No' );
	}

    /**
     * @param \user $user
     * @param array $params
     * @throws \customException
     */
    final public function resetAllExe($user, /** @noinspection PhpUnusedParameterInspection */
                                      $params) {

		if ($user->sGetRole () != 'admin') {
			throw new \customException ( 'No rights to perform selected operation' );
		}

		global $config;

		self::sResetAllAccounts();

		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail", "Operation completed");
	}

    /**
     * @param \user $user
     * @param array $params
     * @return string
     * @throws \customException
     */
    final public function flushAll($user, $params) {

		if ($user->sGetRole () != 'admin') {
			throw new \customException ( 'No rights to perform selected operation' );
		}

		global $config;

		return Controls::dialog( "Confirm", "Do you want to <strong>flush shared cache</strong>?", "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=flushAllExe'", "window.history.back();", 'Yes','No' );
	}

    /**
     * @param \user $user
     * @param $params
     * @throws \customException
     */
    final public function flushAllExe($user, $params) {

		if ($user->sGetRole () != 'admin') {
			throw new \customException ( 'No rights to perform selected operation' );
		}

		global $config;

        \phpCache\Factory::getInstance()->create()->clearAll();

		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail", "Operation completed");
	}

    /**
     * @param \user $user
     * @param $params
     * @return string
     * @throws \customException
     */
    final public function giveTraxium($user, $params) {

		if ($user->sGetRole () != 'admin') {
			throw new \customException ( 'No rights to perform selected operation' );
		}

		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';
		$text .= '<input masked="int10" class="span1" value="0" name="value"/>';
		$text .= '<input type="hidden" name="class" value="'.get_class($this).'">';
		$text .= '<input type="hidden" name="method" value="giveTraxiumExe">';
		$text .= '</form>';

		$retVal = Controls::dialog( "Give Traxium", $text, "player.giveTraxium()", "window.history.back();", 'OK','Cancel', 'height: 100px;' );

		$retVal .= '<script type="text/javascript">';
		$retVal .= '$("#dialog-message").css("height",60)';
		$retVal .= '</script>';

		return $retVal;
	}

    /**
     * @param \user $user
     * @param array $params
     * @throws \customException
     */
    final public function giveTraxiumExe($user, $params) {

		if ($user->sGetRole () != 'admin') {
			throw new \customException ( 'No rights to perform selected operation' );
		}

		global $config;

		if (!is_numeric($params['value'])) {
			throw new \customException('Security Error');
		}

		Database::getInstance()->execute("UPDATE userstats SET Fame=Fame+'{$params['value']}'");
        \phpCache\Factory::getInstance()->create()->clearAll();

		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail", "<strong>Operation completed</strong>");
	}

    /**
     * @param \user $user
     * @param array $params
     * @return string
     * @throws \customException
     */
    final public function giveAntimatter($user, /** @noinspection PhpUnusedParameterInspection */
                                         $params) {

		if ($user->sGetRole () != 'admin') {
			throw new \customException ( 'No rights to perform selected operation' );
		}

		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';
		$text .= '<input masked="int10" class="span2" value="0" name="value" />';
		$text .= '<input type="hidden" name="class" value="'.get_class($this).'">';
		$text .= '<input type="hidden" name="method" value="giveAntimatterExe">';
		$text .= '</form>';

		$retVal = Controls::dialog( "Give Antimatter", $text, "player.giveTraxium()", "window.history.back();", 'OK','Cancel', 'height: 100px;' );

		$retVal .= '<script type="text/javascript">';
		$retVal .= '$("#dialog-message").css("height",60)';
		$retVal .= '</script>';

		return $retVal;
	}

    /**
     * @param \user $user
     * @param array $params
     * @return string
     * @throws \customException
     */
    final public function gameplayMessage($user, $params) {

		if ($user->sGetRole () != 'admin') {
			throw new \customException ( 'No rights to perform selected operation' );
		}

		$text = '<form method="post" name="myForm" style="margin: 0; padding: 0;">';

		$tData = \gameplayMessage::getRaw();

		$values = array('info'=>'Info','warning'=>'Warning','error'=>'Error');

		$text .= Controls::renderSelect('type', $tData['type'], $values,array('class'=>'span2'));

		$text .= '<p><textarea name="text" class="span4">';
		$text .= $tData['text'];
		$text .= '</textarea></p>';

		$text .= '<input type="hidden" name="class" value="'.get_class($this).'">';
		$text .= '<input type="hidden" name="method" value="gameplayMessageExe">';
		$text .= '</form>';

		$retVal = Controls::dialog( "In-game message", $text, "player.gameplayMessage()", "window.history.back();", 'OK','Cancel', 'height: 100px;' );

		$retVal .= '<script type="text/javascript">';
		$retVal .= '$("#dialog-message").css("height",140);';
		$retVal .= '$("[role=dialog]").css("width",420);';
		$retVal .= '</script>';

		return $retVal;
	}

    /**
     * @param \user $user
     * @param $params
     * @throws \customException
     */
    final public function gameplayMessageExe($user, $params) {

		if ($user->sGetRole () != 'admin') {
			throw new \customException ( 'No rights to perform selected operation' );
		}

		global $config;

		\gameplayMessage::write($_REQUEST['type'], $_REQUEST['text']);

		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail", "Operation completed");
	}

    /**
     * @param \user $user
     * @param array $params
     * @throws \customException
     */
    final public function giveAntimatterExe($user, $params) {

		if ($user->sGetRole () != 'admin') {
			throw new \customException ( 'No rights to perform selected operation' );
		}

		global $config;

		if (!is_numeric($params['value'])) {
			throw new \customException('Security Error');
		}

		Database::getInstance()->execute("UPDATE userships SET Turns=Turns+'{$params['value']}'");
        \phpCache\Factory::getInstance()->create()->clearAll();
		\General\Controls::reloadWithMessage("{$config['backend']['fileName']}?class=".get_class($this)."&method=detail", "Operation completed");
	}

    /**
     * @param \user $user
     * @param array $params
     * @return string
     * @throws \customException
     */
    public function detail($user, $params) {

		global $config;

		if ($user->sGetRole () != 'admin') {
			throw new \customException ( 'No rights to perform selected operation' );
		}

		$retVal = $this->renderTitle ( "Global administration" );

		$retVal .= Controls::bootstrapButton('Reset all accounts', "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=resetAll'", 'btn-danger','icon-fire' );
		$retVal .= Controls::bootstrapButton('Flush all shared cache', "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=flushAll'", '','icon-trash' );
		$retVal .= Controls::bootstrapButton('Give Traxium', "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=giveTraxium'", '','icon-plus' );
		$retVal .= Controls::bootstrapButton('Give Antimatter', "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=giveAntimatter'", '','icon-plus' );
		$retVal .= Controls::bootstrapButton('Set in-game message', "document.location='{$config['backend']['fileName']}?class=".get_class($this)."&method=gameplayMessage'", '','icon-envelope' );

		if (! empty ( $params ['execute'] )) {

			switch ($params ['execute']) {

				case 'clearMessages' :
					self::sDeleteOldMessages ();
					break;

				case 'clearNewsAgency' :
					self::sDeleteOldNewsAgency ();
					break;

				case 'clearOffensiveReports' :
					self::sDeleteOldOffensiveReports ();
					break;

				case 'clearDefensiveReports' :
					self::sDeleteOldDefensiveReports ();
					break;

				case 'clearNpcContact' :
					self::sDeleteOldNpcContact ();
					break;

				case 'clearNpcCache' :
					self::sDeleteNpcClearCache ();
					break;

				case 'clearCacheClear' :
					self::sDeleteOldClearCache ();
					break;

				case 'optimize' :
					self::sOptimizeTables ();
					break;

				case 'optimizeAll' :
					self::sOptimizeTables (true);
					break;

				case 'resetOldPorts' :
					self::sResetOldPorts ();
					break;

				case 'resetSharedCache' :
                    \phpCache\Factory::getInstance()->create()->clearAll();
					break;

				case 'cleanupWeapons' :
					self::sDeleteHalfWeaponsInSpace();
					break;

				case 'cleanupEquipments' :
					self::sDeleteHalfEquipmentsInSpace();
					break;

				case 'cleanupItems' :
					self::sDeleteHalfItemsInSpace();
					break;

				case 'dropWeapons' :
					self::sDeleteAllWeaponsInSpace();
					break;

				case 'dropEquipments' :
					self::sDeleteAllEquipmentsInSpace();
					break;

				case 'dropItems' :
					self::sDeleteAllItemsInSpace();
					break;

			}

		}

		$retVal .= $this->renderTitle ( "Old entries maintenance" );

		$retVal .= "<table class='table table-striped table-bordered table-condensed'>";

		$retVal .= "<thead>";
		$retVal .= "<tr>";
		$retVal .= "<th>Module</th>";
		$retVal .= "<th>All</th>";
		$retVal .= "<th>Old</th>";
		$retVal .= "<th>Old [%]</th>";
		$retVal .= "<th>Operation</th>";
		$retVal .= "</tr>";
		$retVal .= "</thead>";

		$retVal .= "<tbody>";

		$tAll = self::sGetTableEntries ( 'messages' );
		$tOld = self::sGetOldMessages ();
		if ($tAll != 0) {
			$tPercent = ($tOld / $tAll) * 100;
		}
		else {
			$tPercent = 0;
		}
		$retVal .= "<tr>";
		$retVal .= "<td>Messages</td>";
		$retVal .= "<td>" . $tAll . "</td>";
		$retVal .= "<td>" . $tOld . "</td>";
		$retVal .= "<td>" . Formater::formatValue($tPercent,'') . "</td>";
		$retVal .= "<td>" . Controls::bootstrapButton ( 'Clear', "document.location='?class=".get_class($this)."&method=detail&execute=clearMessages'",'btn-danger','icon-trash' ) . "</td>";
		$retVal .= "</tr>";

		$tAll = self::sGetTableEntries ( 'newsagency' );
		$tOld = self::sGetOldNewsAgency ();
		if ($tAll != 0) {
			$tPercent = ($tOld / $tAll) * 100;
		}
		else {
			$tPercent = 0;
		}
		$retVal .= "<tr>";
		$retVal .= "<td>News Agency</td>";
		$retVal .= "<td>" . $tAll . "</td>";
		$retVal .= "<td>" . $tOld . "</td>";
		$retVal .= "<td>" . Formater::formatValue($tPercent,'') . "</td>";
		$retVal .= "<td>" . Controls::bootstrapButton ( 'Clear', "document.location='?class=".get_class($this)."&method=detail&execute=clearNewsAgency'",'btn-warning','icon-trash' ) . "</td>";
		$retVal .= "</tr>";

		$tAll = self::sGetTableEntries ( 'combatmessages' );
		$tOld = self::sGetOldOffensiveReports ();
		if ($tAll != 0) {
			$tPercent = ($tOld / $tAll) * 100;
		}
		else {
			$tPercent = 0;
		}
		$retVal .= "<tr>";
		$retVal .= "<td>Offensive combat messages</td>";
		$retVal .= "<td>" . $tAll . "</td>";
		$retVal .= "<td>" . $tOld . "</td>";
		$retVal .= "<td>" . Formater::formatValue($tPercent,'') . "</td>";
		$retVal .= "<td>" . Controls::bootstrapButton ( 'Clear', "document.location='?class=".get_class($this)."&method=detail&execute=clearOffensiveReports'",'btn-warning','icon-trash' ) . "</td>";
		$retVal .= "</tr>";

		$tAll = self::sGetTableEntries ( 'combatmessages' );
		$tOld = self::sGetOldDefensiveReports ();
		if ($tAll != 0) {
			$tPercent = ($tOld / $tAll) * 100;
		}
		else {
			$tPercent = 0;
		}
		$retVal .= "<tr>";
		$retVal .= "<td>Defensive combat messages</td>";
		$retVal .= "<td>" . $tAll . "</td>";
		$retVal .= "<td>" . $tOld . "</td>";
		$retVal .= "<td>" . Formater::formatValue($tPercent,'') . "</td>";
		$retVal .= "<td>" . Controls::bootstrapButton ( 'Clear', "document.location='?class=".get_class($this)."&method=detail&execute=clearDefensiveReports'",'btn-warning','icon-trash' ) . "</td>";
		$retVal .= "</tr>";

		$tAll = self::sGetTableEntries ( 'npccontact' );
		$tOld = self::sGetOldNpcContact ();
		if ($tAll != 0) {
			$tPercent = ($tOld / $tAll) * 100;
		}
		else {
			$tPercent = 0;
		}
		$retVal .= "<tr>";
		$retVal .= "<td>Npc Contact</td>";
		$retVal .= "<td>" . $tAll . "</td>";
		$retVal .= "<td>" . $tOld . "</td>";
		$retVal .= "<td>" . Formater::formatValue($tPercent,'') . "</td>";
		$retVal .= "<td>" . Controls::bootstrapButton ( 'Clear', "document.location='?class=".get_class($this)."&method=detail&execute=clearNpcContact'",'btn-warning','icon-trash' ) . "</td>";
		$retVal .= "</tr>";

		$tAll = self::sGetTableEntries ( 'cacheclear' );
		$tOld = self::sGetNpcClearCache ();
		if ($tAll != 0) {
			$tPercent = ($tOld / $tAll) * 100;
		}
		else {
			$tPercent = 0;
		}
		$retVal .= "<tr>";
		$retVal .= "<td>Cache Clear For NPC</td>";
		$retVal .= "<td>" . $tAll . "</td>";
		$retVal .= "<td>" . $tOld . "</td>";
		$retVal .= "<td>" . Formater::formatValue($tPercent,'') . "</td>";
		$retVal .= "<td>" . Controls::bootstrapButton ( 'Clear', "document.location='?class=".get_class($this)."&method=detail&execute=clearNpcCache'",'btn-warning','icon-trash' ) . "</td>";
		$retVal .= "</tr>";

		$tAll = self::sGetTableEntries ( 'cacheclear' );
		$tOld = self::sGetOldCacheClear ();
		if ($tAll != 0) {
			$tPercent = ($tOld / $tAll) * 100;
		}
		else {
			$tPercent = 0;
		}
		$retVal .= "<tr>";
		$retVal .= "<td>Old cache clear</td>";
		$retVal .= "<td>" . $tAll . "</td>";
		$retVal .= "<td>" . $tOld . "</td>";
		$retVal .= "<td>" . Formater::formatValue($tPercent,'') . "</td>";
		$retVal .= "<td>" . Controls::bootstrapButton ( 'Clear', "document.location='?class=".get_class($this)."&method=detail&execute=clearCacheClear'",'btn-warning','icon-trash' ) . "</td>";
		$retVal .= "</tr>";

		$tAll = self::sGetTablesCount ();
		$tOld = self::sGetOverheadTablesCount ();
		if ($tAll != 0) {
			$tPercent = ($tOld / $tAll) * 100;
		}
		else {
			$tPercent = 0;
		}
		$retVal .= "<tr>";
		$retVal .= "<td>Tables with overhead</td>";
		$retVal .= "<td>" . $tAll . "</td>";
		$retVal .= "<td>" . $tOld . "</td>";
		$retVal .= "<td>" . Formater::formatValue($tPercent,'') . "</td>";
		$retVal .= "<td>" . Controls::bootstrapButton ( 'Optimize Overhead', "document.location='?class=".get_class($this)."&method=detail&execute=optimize'",'btn-success','icon-cog' );
		$retVal .= Controls::bootstrapButton ( 'Optimize All', "document.location='?class=".get_class($this)."&method=detail&execute=optimizeAll'",'btn-success','icon-cog' ) . "</td>";
		$retVal .= "</tr>";

		$tAll = self::sGetTableEntries ( 'ports' );
		$tOld = self::sGetOldPortReset ();
		if ($tAll != 0) {
			$tPercent = ($tOld / $tAll) * 100;
		}
		else {
			$tPercent = 0;
		}
		$retVal .= "<tr>";
		$retVal .= "<td>Unreseted ports</td>";
		$retVal .= "<td>" . $tAll . "</td>";
		$retVal .= "<td>" . $tOld . "</td>";
		$retVal .= "<td>" . Formater::formatValue($tPercent,'') . "</td>";
		$retVal .= "<td>" . Controls::bootstrapButton ( 'Reset', "document.location='?class=".get_class($this)."&method=detail&execute=resetOldPorts'",'btn-success','icon-refresh' ) . "</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>Weapons in space</td>";
		$retVal .= "<td>" . self::sGetWeaponsInSpace() . "</td>";
		$retVal .= "<td>-</td>";
		$retVal .= "<td>-</td>";
		$retVal .= "<td>" . Controls::bootstrapButton ( 'Clear [50%]', "document.location='?class=".get_class($this)."&method=detail&execute=cleanupWeapons'",'btn-warning','icon-trash' );
		$retVal .= Controls::bootstrapButton ( 'Drop All', "document.location='?class=".get_class($this)."&method=detail&execute=dropWeapons'",'btn-danger','icon-trash' ) . "</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>Equipment in space</td>";
		$retVal .= "<td>" . self::sGetEquipmentsInSpace() . "</td>";
		$retVal .= "<td>-</td>";
		$retVal .= "<td>-</td>";
		$retVal .= "<td>" . Controls::bootstrapButton ( 'Clear [50%]', "document.location='?class=".get_class($this)."&method=detail&execute=cleanupEquipments'",'btn-warning','icon-trash' );
		$retVal .= Controls::bootstrapButton ( 'Drop All', "document.location='?class=".get_class($this)."&method=detail&execute=dropEquipments'",'btn-danger','icon-trash' ) . "</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>Items in space</td>";
		$retVal .= "<td>" . self::sGetItemsInSpace() . "</td>";
		$retVal .= "<td>-</td>";
		$retVal .= "<td>-</td>";
		$retVal .= "<td>" . Controls::bootstrapButton ( 'Clear [50%]', "document.location='?class=".get_class($this)."&method=detail&execute=cleanupItems'",'btn-warning','icon-trash' );
		$retVal .= Controls::bootstrapButton ( 'Drop All', "document.location='?class=".get_class($this)."&method=detail&execute=dropItems'",'btn-danger','icon-trash' ) . "</td>";
		$retVal .= "</tr>";

		$retVal .= "</tbody>";
		$retVal .= "</table>";

		return $retVal;
	}

	/**
	 * Optymalizacja tabel
	 *
	 *	@param bool $forceAll Wymuszenie optymalizacji i analizy nawet jeśli nie ma overhead
	 * @return boolean
	 */
	static private function sOptimizeTables($forceAll = false) {

		$tQuery = "SHOW TABLE STATUS";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		while ( $tResult = Database::getInstance()->fetch ( $tQuery ) ) {

			if ($forceAll || $tResult->Data_free != 0) {
				Database::getInstance()->execute ( 'OPTIMIZE TABLE ' . $tResult->Name );
				Database::getInstance()->execute ( 'ANALYZE TABLE ' . $tResult->Name );
			}

		}
        \phpCache\Factory::getInstance()->create()->clear(new \phpCache\CacheKey('maintenance', 'sGetOverheadTablesCount'));
		return true;
	}

	/**
	 * Pobranie liczby wszystkich tabel
	 *
	 * @return int
	 */
	static private function sGetTablesCount() {

		$retVal = 0;

		$oCacheKey = new \phpCache\CacheKey('maintenance', 'sGetTablesCount');
        $oCache    = \phpCache\Factory::getInstance()->create();

		if (! $oCache->check ( $oCacheKey )) {

			$tQuery = "SHOW TABLE STATUS";
			$tQuery = Database::getInstance()->execute ( $tQuery );
			while ( $tResult = Database::getInstance()->fetch ( $tQuery ) ) {
				if (! empty ( $tResult )) {
					$retVal ++;
				}
			}
			$oCache->set ( $oCacheKey, $retVal, 3600 );
		} else {
			$retVal = $oCache->get ( $oCacheKey );
		}

		return $retVal;
	}

	/**
	 * Pobranie liczby tabel z overhead
	 *
	 * @return int
	 */
	static private function sGetOverheadTablesCount() {

		$retVal = 0;

		$oCacheKey = new \phpCache\CacheKey('maintenance', 'sGetOverheadTablesCount');
        $oCache    = \phpCache\Factory::getInstance()->create();
		
		if (! $oCache->check ( $oCacheKey )) {

			$tQuery = "SHOW TABLE STATUS";
			$tQuery = Database::getInstance()->execute ( $tQuery );
			while ( $tResult = Database::getInstance()->fetch ( $tQuery ) ) {
				if ($tResult->Data_free != 0) {
					$retVal ++;
				}
			}
			$oCache->set ( $oCacheKey, $retVal, 300 );
		} else {
			$retVal = $oCache->get ( $oCacheKey );
		}

		return $retVal;
	}

	/**
	 * Liczba portów nie resetowanych
	 *
	 * @return int
	 */
	static private function sGetOldPortReset() {

		global $maintenance;

		$tTime = time () - $maintenance ['portResetThreshold'];

		$tQuery = "SELECT COUNT(*) AS ILE FROM ports WHERE ResetTime<'{$tTime}'";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		$retVal = Database::getInstance()->fetch ( $tQuery )->ILE;

		return $retVal;
	}

	/**
	 * reset nieresetowanych portów
	 *
	 */
	static private function sResetOldPorts() {

		global $maintenance;

		$tTime = time () - $maintenance ['portResetThreshold'];

		$tQuery = "SELECT PortID FROM ports WHERE ResetTime<'{$tTime}'";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		while ( $tResult = Database::getInstance()->fetch ( $tQuery ) ) {

			$tPort = new PortEntity($tResult->PortID, false);
            PortEntity::sReset($tPort);
			unset ( $tPort );

		}

		$tQuery = "UPDATE ports SET ResetTime='" . time () . "' WHERE ResetTime<'{$tTime}'";
		Database::getInstance()->execute ( $tQuery );

	}

	/**
	 * Liczba przeterminowanych cache clear
	 *
	 * @return int
	 */
	static private function sGetOldCacheClear() {

		global $maintenance;

		$tTime = time () - ($maintenance ['cacheClearValid'] * 3600);

		$tQuery = "SELECT COUNT(*) AS ILE FROM cacheclear JOIN usertimes USING (UserID) WHERE LastAction<'{$tTime}'";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		$retVal = Database::getInstance()->fetch ( $tQuery )->ILE;

		return $retVal;
	}

	/**
	 * Usunięcie przeterminowanych cacheclear
	 *
	 * @return bool
	 */
	static private function sDeleteOldClearCache() {

		global $maintenance;

		$tTime = time () - ($maintenance ['cacheClearValid'] * 3600);

		$tQuery = "DELETE FROM cacheclear WHERE (SELECT usertimes.LastAction FROM usertimes WHERE usertimes.UserID=cacheclear.UserID) < '{$tTime}'";
		Database::getInstance()->execute ( $tQuery );
		return true;
	}

	/**
	 * @return int
	 */
	static private function sGetNpcClearCache() {

		$tQuery = "SELECT COUNT(*) AS ILE FROM cacheclear JOIN users USING (UserID) WHERE users.Type!='player'";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		return Database::getInstance()->fetch ( $tQuery )->ILE;
	}

	/**
	 * Usunięcie cache clear dla NPC
	 *
	 * @return boolean
	 */
	static private function sDeleteNpcClearCache() {
		Database::getInstance()->execute("DELETE FROM cacheclear WHERE (SELECT users.Type FROM users WHERE users.UserID=cacheclear.UserID) != 'player'");
		return true;
	}

	/**
	 * Pobranie liczby wszystkich wpisów w tabeli
	 *
	 * @param string $tableName
	 * @return int
	 */
	static private function sGetTableEntries($tableName) {

		$tQuery = "SELECT COUNT(*) AS ILE FROM {$tableName} WHERE 1";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		$retVal = Database::getInstance()->fetch ( $tQuery )->ILE;

		return $retVal;
	}

	/**
	 * Pobranie liczby przestarzałych npc contact
	 *
	 * @return int
	 */
	static private function sGetOldNpcContact() {

		global $maintenance;

		$tTime = time () - ($maintenance ['npcContactValid'] * 3600);

		$tQuery = "SELECT COUNT(*) AS ILE FROM npccontact WHERE ContactTime<'{$tTime}'";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		$retVal = Database::getInstance()->fetch ( $tQuery )->ILE;

		return $retVal;
	}

	/**
	 * Usunięcie starych npc contact
	 *
	 * @return bool
	 */
	static private function sDeleteOldNpcContact() {

		global $maintenance;

		$tTime = time () - ($maintenance ['npcContactValid'] * 3600);

		$tQuery = "DELETE FROM npccontact WHERE ContactTime<'{$tTime}'";
		Database::getInstance()->execute ( $tQuery );
		return true;
	}


	/**
	 * Get number of old messages
	 *
	 * @return int
	 */
	static private function sGetOldMessages() {

		global $maintenance;

		$tTime = time () - ($maintenance ['messagesValid'] * 3600);

		$tQuery = "SELECT COUNT(*) AS ILE FROM messages WHERE CreateTime<'{$tTime}'";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		$retVal = Database::getInstance()->fetch ( $tQuery )->ILE;

		return $retVal;
	}

	/**
	 * Get count of old newsagency entries
	 *
	 * @return int
	 */
	static private function sGetOldNewsAgency() {

		global $maintenance;

		$tTime = time () - ($maintenance ['newsAgencyValid'] * 3600);

		$tQuery = "SELECT COUNT(*) AS ILE FROM newsagency WHERE Date<'{$tTime}'";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		$retVal = Database::getInstance()->fetch ( $tQuery )->ILE;

		return $retVal;
	}

	/**
	 * Get count of old offensive combat messages
	 *
	 * @return int
	 */
	static private function sGetOldOffensiveReports() {

		global $maintenance;

		$tTime = time () - ($maintenance ['offensiveReportsValid'] * 3600);

		$tQuery = "SELECT COUNT(*) AS ILE FROM combatmessages WHERE Type='offensive' AND CreateTime<'{$tTime}'";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		$retVal = Database::getInstance()->fetch ( $tQuery )->ILE;

		return $retVal;
	}

	/**
	 * Usunięcie przeterminowanych combatmessages typu offensive
	 *
	 * @return boolean
	 */
	static private function sDeleteOldOffensiveReports() {

		global $maintenance;

		$tTime = time () - ($maintenance ['offensiveReportsValid'] * 3600);

		$tQuery = "DELETE FROM combatmessages WHERE Type='offensive' AND CreateTime<'{$tTime}'";
		Database::getInstance()->execute ( $tQuery );
		return true;
	}

	/**
	 * Liczba przeterminowanych combatmessages typu defensive
	 *
	 * @return int
	 */
	static private function sGetOldDefensiveReports() {

		global $maintenance;

		$tTime = time () - ($maintenance ['defensiveReportsValid'] * 3600);

		$tQuery = "SELECT COUNT(*) AS ILE FROM combatmessages WHERE Type='defensive' AND CreateTime<'{$tTime}'";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		$retVal = Database::getInstance()->fetch ( $tQuery )->ILE;

		return $retVal;
	}

	/**
	 * Usunięcie przeterminowanych combatmessages typu defensive
	 *
	 * @return boolean
	 */
	static private function sDeleteOldDefensiveReports() {

		global $maintenance;

		$tTime = time () - ($maintenance ['defensiveReportsValid'] * 3600);

		Database::getInstance()->execute("DELETE FROM combatmessages WHERE Type='defensive' AND CreateTime<'{$tTime}'");
		return true;
	}

	/**
	 * Usunięcie przeterminowanych newsagency
	 *
	 * @return boolean
	 */
	static private function sDeleteOldNewsAgency() {

		global $maintenance;

		$tTime = time () - ($maintenance ['newsAgencyValid'] * 3600);

		$tQuery = "DELETE FROM newsagency WHERE Date<'{$tTime}'";
		$tQuery = Database::getInstance()->execute ( $tQuery );

		return true;
	}

	/**
	 * @return bool
	 */
	static private function sDeleteOldMessages() {

		global $maintenance;

		$tTime = time () - ($maintenance ['messagesValid'] * 3600);

		$tQuery = "DELETE FROM messages WHERE CreateTime<'{$tTime}'";
		Database::getInstance()->execute ( $tQuery );

		return true;
	}

	static private function sGetWeaponsInSpace() {

		$tQuery = "SELECT COUNT(*) AS ILE FROM sectorcargo WHERE Type='weapon'";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		$retVal = Database::getInstance()->fetch ( $tQuery )->ILE;

		return $retVal;
	}

	static private function sSectorCleanup($type, $count) {

		Database::getInstance()->disableAutocommit();

		$tQuery = "SELECT
                sectorcargo.System,
                sectorcargo.X,
                sectorcargo.Y,
                sectors.System AS System2,
                sectors.X AS X2,
                sectors.Y AS Y2
            FROM
                sectorcargo LEFT JOIN sectors ON sectors.SectorID=sectorcargo.SectorID
            WHERE
                sectorcargo.Type='{$type}' LIMIT {$count}";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		while ($tResult = Database::getInstance()->fetch($tQuery)) {

			$tPosition = new stdClass();

			if (!empty($tResult->System)) {
				$tPosition->System = $tResult->System;
				$tPosition->X = $tResult->X;
				$tPosition->Y = $tResult->Y;
			}else {
				$tPosition->System = $tResult->System2;
				$tPosition->X = $tResult->X2;
				$tPosition->Y = $tResult->Y2;
			}

			$tObject = new \sectorCargo($tPosition);
			$tObject->drop($type);
			unset($tObject);
		}

		Database::getInstance()->commit();
		Database::getInstance()->enableAutocommit();
	}

	static private function sDeleteHalfWeaponsInSpace() {

		$tCount = self::sGetWeaponsInSpace();
		$tCount = ceil($tCount/2);

		self::sSectorCleanup('weapon', $tCount);

		return true;
	}

	static private function sDeleteHalfEquipmentsInSpace() {

		$tCount = self::sGetEquipmentsInSpace();
		$tCount = ceil($tCount/2);

		self::sSectorCleanup('equipment', $tCount);

		return true;
	}

	static private function sDeleteHalfItemsInSpace() {

		$tCount = self::sGetItemsInSpace();
		$tCount = ceil($tCount/2);

		self::sSectorCleanup('item', $tCount);

		return true;
	}

	static private function sDeleteAllWeaponsInSpace() {

		$tCount = self::sGetWeaponsInSpace();

		self::sSectorCleanup('weapon', $tCount);

		return true;
	}

	static private function sDeleteAllEquipmentsInSpace() {

		$tCount = self::sGetEquipmentsInSpace();

		self::sSectorCleanup('equipment', $tCount);

		return true;
	}

	static private function sDeleteAllItemsInSpace() {

		$tCount = self::sGetItemsInSpace();

		self::sSectorCleanup('item', $tCount);

		return true;
	}

	static private function sGetEquipmentsInSpace() {

		$tQuery = "SELECT COUNT(*) AS ILE FROM sectorcargo WHERE Type='equipment'";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		$retVal = Database::getInstance()->fetch ( $tQuery )->ILE;

		return $retVal;
	}

	static private function sGetItemsInSpace() {

		$tQuery = "SELECT COUNT(*) AS ILE FROM sectorcargo WHERE Type='item'";
		$tQuery = Database::getInstance()->execute ( $tQuery );
		$retVal = Database::getInstance()->fetch ( $tQuery )->ILE;

		return $retVal;
	}

}