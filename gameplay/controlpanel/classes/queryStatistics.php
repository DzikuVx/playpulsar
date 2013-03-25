<?php
class queryStatistics extends \Controlpanel\BaseItem{

	static private function sGetScriptRunCount() {

		$tQuery = \Database\Controller::getBackendInstance()->execute("SELECT SUM(Count) AS ile FROM st_scriptruns");

		return \Database\Controller::getBackendInstance()->fetch($tQuery)->ile;
	}

	static private function sGetScriptRunTime() {

		$tQuery = \Database\Controller::getBackendInstance()->execute("SELECT SUM(Time) AS ile FROM st_scriptruns");

		return \Database\Controller::getBackendInstance()->fetch($tQuery)->ile;
	}

	static private function sGetAllCount() {

		$tQuery = \Database\Controller::getBackendInstance()->execute("SELECT SUM(Count) AS ile FROM st_queries");

		return \Database\Controller::getBackendInstance()->fetch($tQuery)->ile;
	}

	static private function sGetAllTime() {

		$tQuery = \Database\Controller::getBackendInstance()->execute("SELECT SUM(Time) AS ile FROM st_queries");

		return \Database\Controller::getBackendInstance()->fetch($tQuery)->ile;
	}

	static private function sGetTypeCount($type) {

		$type = mb_strtoupper($type);

		$tQuery = \Database\Controller::getBackendInstance()->execute("SELECT SUM(Count) AS ile FROM st_queries WHERE UPPER(Query) LIKE '{$type}%'");

		$retVal = \Database\Controller::getBackendInstance()->fetch($tQuery)->ile;

		if (empty($retVal)) {
			$retVal = 0;
		}

		return $retVal;
	}

	static private function sGetTypeTime($type) {

		$type = mb_strtoupper($type);

		$tQuery = \Database\Controller::getBackendInstance()->execute("SELECT SUM(Time) AS ile FROM st_queries WHERE UPPER(Query) LIKE '{$type}%'");
		$retVal = \Database\Controller::getBackendInstance()->fetch($tQuery)->ile;

		if (empty($retVal)) {
			$retVal = 0;
		}

		return $retVal;
	}

	public function detail(user $user, $params) {

		global $config;

		if ($user->sGetRole () != 'admin') {
			throw new customException ( 'No rights to perform selected operation' );
		}

		$retVal = $this->renderTitle ( "Statistics" );

		$retVal .= "<table class='table table-striped table-bordered table-condensed'>";

		$retVal .= "<thead>";
		$retVal .= "<tr>";
		$retVal .= "<th>Type</th>";
		$retVal .= "<th>#</th>";
		$retVal .= "<th>#%</th>";
		$retVal .= "<th>Time</th>";
		$retVal .= "<th>Time %</th>";
		$retVal .= "</tr>";
		$retVal .= "</thead>";
		$retVal .= "<tbody>";

		$tAll = self::sGetAllCount();
		$tInsert = self::sGetTypeCount('INSERT');
		$tUpdate = self::sGetTypeCount('UPDATE');
		$tDelete = self::sGetTypeCount('DELETE');
		$tSet = self::sGetTypeCount('SET');
		$tAlter = self::sGetTypeCount('ALTER');
		$tDrop = self::sGetTypeCount('DROP');
		$tTruncate = self::sGetTypeCount('TRUNCATE');

		$tSelect = $tAll - $tInsert - $tUpdate - $tDelete - $tSet - $tAlter - $tDrop - $tTruncate;

		$tAllTime = self::sGetAllTime();
		$tInsertTime = self::sGetTypeTime('INSERT');
		$tUpdateTime = self::sGetTypeTime('UPDATE');
		$tDeleteTime = self::sGetTypeTime('DELETE');
		$tSetTime = self::sGetTypeTime('SET');
		$tAlterTime = self::sGetTypeTime('ALTER');
		$tDropTime = self::sGetTypeTime('DROP');
		$tTruncateTime = self::sGetTypeTime('TRUNCATE');

		$tScriptRuns = self::sGetScriptRunCount();
		$tScriptRunsTime = self::sGetScriptRunTime();

		if (empty($tScriptRuns)) {
			$tScriptRuns = 0.001;
		}
		if (empty($tScriptRunsTime)) {
			$tScriptRunsTime = 0.001;
		}

		$tSelectTime = $tAllTime - $tInsertTime - $tUpdateTime - $tDeleteTime - $tSetTime - $tAlterTime - $tDropTime - $tTruncateTime;

		$retVal .= "<tr>";
		$retVal .= "<td>All</td>";
		$retVal .= "<td>{$tAll}</td>";
		$retVal .= "<td>100%</td>";
		$retVal .= "<td>{$tAllTime}</td>";
		$retVal .= "<td>100%</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>SELECT</td>";
		$retVal .= "<td>{$tSelect}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tSelect, $tAll)."%</td>";
		$retVal .= "<td>{$tSelectTime}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tSelectTime, $tAllTime)."%</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>INSERT</td>";
		$retVal .= "<td>{$tInsert}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tInsert, $tAll)."%</td>";
		$retVal .= "<td>{$tInsertTime}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tInsertTime, $tAllTime)."%</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>UPDATE</td>";
		$retVal .= "<td>{$tUpdate}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tUpdate, $tAll)."%</td>";
		$retVal .= "<td>{$tUpdateTime}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tUpdateTime, $tAllTime)."%</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>DELETE</td>";
		$retVal .= "<td>{$tDelete}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tDelete, $tAll)."%</td>";
		$retVal .= "<td>{$tDeleteTime}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tDeleteTime, $tAllTime)."%</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>SET</td>";
		$retVal .= "<td>{$tSet}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tSet, $tAll)."%</td>";
		$retVal .= "<td>{$tSetTime}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tSetTime, $tAllTime)."%</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>ALTER</td>";
		$retVal .= "<td>{$tAlter}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tAlter, $tAll)."%</td>";
		$retVal .= "<td>{$tAlterTime}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tAlterTime, $tAllTime)."%</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>DROP</td>";
		$retVal .= "<td>{$tDrop}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tDrop, $tAll)."%</td>";
		$retVal .= "<td>{$tDropTime}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tDropTime, $tAllTime)."%</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>TRUNCATE</td>";
		$retVal .= "<td>{$tTruncate}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tTruncate, $tAll)."%</td>";
		$retVal .= "<td>{$tTruncateTime}</td>";
		$retVal .= "<td>".\General\Formater::sGetPercentage($tTruncateTime, $tAllTime)."%</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td colspan='5'>&nbsp;</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>Script Runs</td>";
		$retVal .= "<td>{$tScriptRuns}</td>";
		$retVal .= "<td>&nbsp;</td>";
		$retVal .= "<td>{$tScriptRunsTime}</td>";
		$retVal .= "<td>&nbsp;</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>Queries / Script Runs</td>";
		$retVal .= "<td>".($tAll / $tScriptRuns)."</td>";
		$retVal .= "<td colspan='3'>&nbsp;</td>";
		$retVal .= "</tr>";

		$tPhpTime = $tScriptRunsTime - $tAllTime;

		$tPhpPercent = ($tPhpTime/$tScriptRunsTime)*100;
		$tMysqlPercent = ($tAllTime/$tScriptRunsTime)*100;

		$retVal .= "<tr>";
		$retVal .= "<td>PHP Time</td>";
		$retVal .= "<td colspan='2'>&nbsp;</td>";
		$retVal .= "<td>{$tPhpTime}</td>";
		$retVal .= "<td>".\General\Formater::formatInt($tPhpPercent,'%')."</td>";
		$retVal .= "</tr>";

		$retVal .= "<tr>";
		$retVal .= "<td>MySQL Time</td>";
		$retVal .= "<td colspan='2'>&nbsp;</td>";
		$retVal .= "<td>{$tAllTime}</td>";
		$retVal .= "<td>".\General\Formater::formatInt($tMysqlPercent,'%')."</td>";
		$retVal .= "</tr>";

		$retVal .= "</tbody>";
		$retVal .= "</table>";

		return $retVal;
	}
}