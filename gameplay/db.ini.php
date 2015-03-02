<?php

if (empty ( $deploymentMode )) {
	/*
	 * Tryb lokalny
	 */
	$dbConfig ['handle'] = null;
	$dbConfig ['host'] = "localhost";
	$dbConfig ['login'] = "pulsar";
	$dbConfig ['password'] = "pulsar";
	$dbConfig ['database'] = "pulsar_gameplay";
	$dbConfig ['persistent'] = false;
	$dbConfig ['enconding'] = "UTF-8";

	$chatDbConfig ['handle'] = null;
	$chatDbConfig ['host'] = "localhost";
	$chatDbConfig ['login'] = "pulsar";
	$chatDbConfig ['password'] = "pulsar";
	$chatDbConfig ['database'] = "pulsar_chat";
	$chatDbConfig ['persistent'] = false;
	$chatDbConfig ['enconding'] = "UTF-8";

	$backendDbConfig ['handle'] = null;
	$backendDbConfig ['host'] = "localhost";
	$backendDbConfig ['login'] = "pulsar";
	$backendDbConfig ['password'] = "pulsar";
	$backendDbConfig ['database'] = "pulsar_backend";
	$backendDbConfig ['persistent'] = false;
	$backendDbConfig ['enconding'] = "UTF-8";

	$portalDbConfig ['handle'] = null;
	$portalDbConfig ['host'] = "localhost";
	$portalDbConfig ['login'] = "pulsar";
	$portalDbConfig ['password'] = "pulsar";
	$portalDbConfig ['database'] = "pulsar_portal";
	$portalDbConfig ['persistent'] = false;
	$portalDbConfig ['enconding'] = "UTF-8";

}
else {
	/*
	 * MySQL configuration in production configuration
	 */
	$dbConfig ['handle'] = null;
	$dbConfig ['host'] = "";
	$dbConfig ['login'] = "";
	$dbConfig ['password'] = "";
	$dbConfig ['database'] = "playpulsar";
	$dbConfig ['persistent'] = false;
	$dbConfig ['enconding'] = "UTF-8";

	$chatDbConfig ['handle'] = null;
	$chatDbConfig ['host'] = "";
	$chatDbConfig ['login'] = "";
	$chatDbConfig ['password'] = "";
	$chatDbConfig ['database'] = "playpulsar_chat";
	$chatDbConfig ['persistent'] = false;
	$chatDbConfig ['enconding'] = "UTF-8";

	$backendDbConfig ['handle'] = null;
	$backendDbConfig ['host'] = "";
	$backendDbConfig ['login'] = "";
	$backendDbConfig ['password'] = "";
	$backendDbConfig ['database'] = "playpulsar_backend";
	$backendDbConfig ['persistent'] = false;
	$backendDbConfig ['enconding'] = "UTF-8";

	$portalDbConfig ['handle'] = null;
	$portalDbConfig ['host'] = "";
	$portalDbConfig ['login'] = "";
	$portalDbConfig ['password'] = "";
	$portalDbConfig ['database'] = "playpulsar_portal";
	$portalDbConfig ['persistent'] = false;
	$portalDbConfig ['enconding'] = "UTF-8";

}