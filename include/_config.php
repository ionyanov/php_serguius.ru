<?php // 6/13/02 8:26AM

	Error_Reporting(E_ALL & ~E_NOTICE);
	// If you use Apache to set these variables (RECOMMENDED), comment out the 4 lines below
/*	$SQL_HOST='localhost';			// the sql server name or IP address
	$SQL_USER='admin';				// the username to use for mysql
	$SQL_PASS='123';				// the password for above
	$SQL_DB='serguius';				// the database name on the above server to use
	$PSA_SITE_NAME = '/serguius/';	//
*/
	$SQL_HOST='localhost';			// the sql server name or IP address
	$SQL_USER='onyano_serguius';	// the username to use for mysql
	$SQL_PASS='fkbcfabab';			// the password for above
	$SQL_DB='onyano_serguius';		// the database name on the above server to use
	$PSA_SITE_NAME = 'http://serguius.ru/';	//

	$ADMINMMODE=FALSE;
/**************** SHOULD NOT NEED TO EDIT ANYTHING BELOW THIS LINE ****************/

	session_start();
	require_once dirname(__FILE__).'/_sessions.php';
	require_once dirname(__FILE__).'/class.phpMysqlConnection.php';
	require_once dirname(__FILE__).'/class.phpSecurityAdmin.php';

	$sec_sys=new phpSecurityAdmin($SQL_USER,$SQL_PASS,$SQL_DB,$SQL_HOST,$PSA_SITE_NAME);
?>