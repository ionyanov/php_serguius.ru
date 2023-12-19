<?php

	Error_Reporting(E_ALL & ~E_NOTICE);

	$SQL_HOST='db';	// the sql server name or IP address
	$SQL_USER='serguius';	// the username to use for mysql
	$SQL_PASS='serguius';	// the password for above
	$SQL_DB='serguius';		// the database name on the above server to use
	$PSA_SITE_NAME = 'http://localhost:8000/';	//

	$ADMINMMODE=FALSE;
/**************** SHOULD NOT NEED TO EDIT ANYTHING BELOW THIS LINE ****************/

	session_start();
	require_once dirname(__FILE__).'/_sessions.php';
	require_once dirname(__FILE__).'/class.phpMysqlConnection.php';
	require_once dirname(__FILE__).'/class.phpSecurityAdmin.php';

	$sec_sys=new phpSecurityAdmin($SQL_USER,$SQL_PASS,$SQL_DB,$SQL_HOST,$PSA_SITE_NAME);
?>