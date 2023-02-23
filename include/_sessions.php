<?php // 6/6/02 4:28PM
/*
    This file contains all the function definitions for the custom session
    handler that uses a mysql database. The variables below this comment block
    are the only things you should need to change.

    You will need to create a table in the database that you wish to use with the
    following structure:

    CREATE TABLE sessions(
     sesskey char(32) not null,
     expiry int(11) unsigned not null,
     value text not null,
     PRIMARY KEY(sesskey)
    );
*/

if(isset($MYSQL_SESSION_INC)) return;
$MYSQL_SESSION_INC=TRUE;

    // let the server know that you want to set your session handling yourself.
    // The manual is kind of funny with this. According to the ini_set page, this shouldn't
    // work, but it does (and a user has posted that fact). However, I've also noticed this
    // script working with session.save_handler = files. According to the
    // session_set_save_handler page, it shouldn't - I added a note on this.
    //!!!ini_set('session.save_handler','user');

    // I don't like the idea of having stale sessions around. Having them removed may even
    // enhance performance on the database table if it starts getting large. However, I don't
    // think it is necessary to run it at 100 - unless you are _very_ paranoid.
    //!!!ini_set('session.gc_probability','100');

    require_once dirname(__FILE__).'/class.phpMysqlConnection.php';
    $SESS_SQL=FALSE;            // MySQL object to be used by sessions

    // How long the sessions last. Defaults to the value in the php.ini file.
    $SESS_LIFE=ini_get('session.gc_maxlifetime');

    function sess_open(){
        global $PHPSECURITYADMIN_PATH,$SQL_HOST,$SQL_DB,$SQL_USER,$SQL_PASS,$SESS_SQL,$_SERVER;

        // Create the object to use in the sessions
        $SESS_SQL=new phpMysqlConnection($SQL_USER,$SQL_PASS,$SQL_HOST);

        // Select the correct database on the server
        $SESS_SQL->SelectDB($SQL_DB);
        return TRUE;
    }

    function sess_close(){
        return TRUE;
    }

    function sess_read($key){
    	global $PHPSECURITYADMIN_PATH,$SESS_SQL,$SESS_LIFE;

    	$query="select value from sessions where sesskey='$key' and expiry > ".time();
    	if($SESS_SQL->Exists($query))
    	    // If the requested session exists, get the data
    	    $retVal=$SESS_SQL->QueryItem($query);
    	if(isset($retVal)) return $retVal;
    	else return '';
    }

    function sess_write($key,$val){
    	global $PHPSECURITYADMIN_PATH,$SESS_SQL,$SESS_LIFE;

        // Calculate the session end time
    	$expiry=time()+$SESS_LIFE;
    	$value=addslashes($val);
        $q="select sesskey from sessions where sesskey='$key'";
        if($SESS_SQL->Exists($q)){
            // if the session exists, update it
            $query="update sessions set expiry=$expiry, value='$value' where sesskey='$key' and expiry > ".time();
            $SESS_SQL->Update($query);
        }else{
            // if the session doesn't exist, create it
    	    $query="insert into sessions values('$key',$expiry,'$value')";
            $SESS_SQL->Insert($query);
        }
    	return TRUE;
    }

    function sess_destroy($sess_id){
    	global $PHPSECURITYADMIN_PATH,$SESS_SQL;

    	// delete the existing session
    	$query="DELETE from sessions where sesskey='$sess_id'";
    	$SESS_SQL->Delete($query);
    	return TRUE;
    }

    function sess_gc(){
    	global $PHPSECURITYADMIN_PATH,$SESS_SQL;

    	// delete all expired sessions
    	$query='DELETE from sessions where expiry < '.time();
    	$SESS_SQL->Delete($query);
    	return $SESS_SQL->a_rows;
    }

    session_set_save_handler('sess_open','sess_close','sess_read','sess_write','sess_destroy','sess_gc');
?>