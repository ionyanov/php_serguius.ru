<?php
/*
    Name: phpMysqlConnection
    Author: Justin Koivisto
    Version: 1.5.9
    Date: 11/5/2002 12:13PM GMT-6:00
*/
	Error_Reporting(E_ALL & ~E_NOTICE);

if(isset($PHPMYSQLCONNECTION_INC)) return;
$PHPMYSQLCONNECTION_INC=TRUE;

class phpMysqlConnection{
    var $result='';
    var $data='';
    var $rows='';
    var $user='';
    var $pass='';
    var $host='';
    var $port='';
    var $socket='';
    var $id='';
    var $a_rows='';

    function phpMysqlConnection($user="root",$pass="",$host="localhost",$port='',$socket_path=''){
        $this->user=$user;
        $this->pass=$pass;
        $this->host=$host;
        $this->port=$port;
        $this->socket=$socket_path;
        if($this->port) $host.=":".$this->port;
        if($this->socket) $host.=":".$this->socket;
        if($this->id=mysql_connect($this->host,$this->user,$this->pass))
            return TRUE;
        else
            return $this->errorMessage("Unable to connect to mysql server: ".$this->host);
    }

    function GetDatabases(){
        if($this->result=mysql_list_dbs()){
            $i=0;
            while($i<mysql_num_rows($this->result)){
                $db_names[$i]=mysql_tablename($this->result,$i);
                $i++;
            }
            return($db_names);
        }else
            return $this->errorMessage("Unable to find a database on server: ".$this->host);
    }

    function CreateDB($database){
        if($this->result=mysql_create_db($database)){
            return TRUE;
        }else
            return $this->errorMessage("Unable to create database: $database");
    }

    function DropDB($database){
        if($this->result=mysql_drop_db($database)){
            return TRUE;
        }else
            return $this->errorMessage("Unable to drop database: $database");
    }

    function CopyDB($database,$dest_db='',$drop_tables=0,$dest_host="localhost",$dest_user="root",
            $dest_pass="",$dest_port='',$dest_socket_path=''){
        set_time_limit(300);    // set time limit to 5 minutes (may not work!)
        // define the second server connection
        if($dest_port) $host.=":".$dest_port;
        if($dest_socket_path) $host.=":".$dest_socket_path;

        // Let's connect to the other server now
        $conn_id=mysql_connect($host,$dest_user,$dest_pass) or
            $retVal=$this->errorMessage("Unable to connect to mysql server: $dest_host");
        if(isset($retVal)) return $retVal;

        $dest_dbs=mysql_list_dbs($conn_id) or
            $retVal=$this->errorMessage("Unable to find a database on server: $dest_host");

        if(isset($retVal)) return $retVal;

        // check if the database exists on the destination server
        $EXISTS=FALSE;
        while($i<mysql_num_rows($dest_dbs)){
            if(mysql_tablename($dest_dbs,$i)==$dest_db)
                $EXISTS=TRUE;
            $i++;
        }
        if(!$EXISTS){
            // if the database doesn't exist, create it
            $result=mysql_create_db($dest_db,$conn_id)  or
                $retVal=$this->errorMessage("Unable to create database: $dest_db");
            if(isset($retVal)) return $retVal;
        }
        // at this point the remote database exists

        // get a list of the available tables on the source database
        $tables=mysql_list_tables($database,$this->id);
        $num_tables=mysql_num_rows($tables) or
            $retVal=$this->errorMessage("No tables found on database: $database, host: $this->host");
        if(isset($retVal)) return $retVal;


        // dump the data
        for($i=0;$i<$num_tables;$i++){    // for each table
            set_time_limit(60);
            $table=mysql_tablename($tables,$i);    // get the name
                "$dest_user,$dest_pass,$dest_port,$dest_socket_path);<br>";
            // copy this table
            $this->CopyTable($table,$table,$database,$dest_db,$drop_tables,$conn_id,$dest_host,
                $dest_user,$dest_pass,$dest_port,$dest_socket_path);
        }
        return TRUE;
    }

    function CopyTable($table,$dest_table,$database,$dest_db,$drop_table='',$conn_id='',$dest_host='',
            $dest_user='',$dest_pass='',$dest_port='',$dest_socket_path=''){
        // first, let's check to see if we are connected to a server already, if not, connect
        if(empty($conn_id)){
            $host=$dest_host;
            if($dest_port) $host.=":".$dest_port;
            if($dest_socket_path) $host.=":".$dest_socket_path;
            $conn_id=mysql_connect($dest_host,$dest_user,$dest_pass)  or
                $retVal=$this->errorMessage("Unable to connect to mysql server: ".$dest_host);
            if(isset($retVal)) return $retVal;

        }

        if($drop_table){
            $drop="DROP TABLE IF EXISTS $dest_table;";
            mysql_select_db($dest_db);
            $result=mysql_query($drop,$conn_id)  or
                $retVal=$this->errorMessage("Unable to perform query (drop $table) on database: $dest_host:".
                    "$dest_db<br><br>$drop<br><br>");
            if(isset($retVal)) return $retVal;
        }

        $struc="CREATE TABLE ".$dest_table." (\n";
        mysql_select_db($database);
        $result=mysql_query("SHOW FIELDS FROM $table",$this->id)  or
            $retVal=$this->errorMessage("Unable to copy table (fields): $table from database $database");

        if(isset($retVal)) return $retVal;

        while($row=mysql_fetch_array($result)){
            $struc.=" $row[Field] $row[Type]";
            if(isset($row['Default']) && (!empty($row['Default']) || $row['Default']=="0"))
                $struc.=" DEFAULT '$row[Default]'";
            if($row['Null']!="YES")
                $struc.=" NOT NULL";
            if($row['Extra']!="")
                $struc.=" $row[Extra]";
            $struc.=",\n";
        }
        // remove the last comma
        $struc=ereg_replace(",\n$","",$struc);

        mysql_select_db($database);
        $result=mysql_query("SHOW KEYS FROM $table",$this->id)  or
            $retVal=$this->errorMessage("Unable to copy table (keys): $table from database $database");
        if(isset($retVal)) return $retVal;

        while($row=mysql_fetch_array($result)){
            $key_name=$row['Key_name'];
            if(($key_name!='PRIMARY') && ($row['Non_unique']==0))
                $key_name="UNIQUE|$key_name";
            if(!isset($index[$key_name]))
                $index[$key_name]=array();
            $index[$key_name][]=$row['Column_name'];
        }

        while(list($x,$columns)=@each($index)){
            $struc.=",\n";
            if($x=="PRIMARY")
                $struc.=" PRIMARY KEY (".implode($columns,", ").")";
            else if(substr($x,0,6)=="UNIQUE")
                $struc.=" UNIQUE ".substr($x,7)." (".implode($columns, ", ").")";
            else
                $struc.=" KEY $x (".implode($columns, ", ").")";
        }

        $struc.="\n);";
        $struc=stripslashes(trim($struc));

        // do structure query
        mysql_select_db($databse);
        $result=mysql_query($dest_db,$struc,$conn_id)  or
            $retVal=$this->errorMessage("Unable to create $dest_host: ".$dest_db.".".$dest_table);
        if(isset($retVal)) return $retVal;


        // get data
        mysql_select_db($database);
        $result=mysql_query("SELECT * FROM $table",$this->id)  or
            $retVal=$this->errorMessage("Unable to perform query: $query");
        if(isset($retVal)) return $retVal;

        $num_rows=mysql_num_rows($result);
        $insert_data=array();
        for($i=0;$i<$num_rows;$i++){
            mysql_data_seek($result,$i) or $retVal=$this->errorMessage("Unable to seek data row: $row");
            if(isset($retVal)) return $retVal;

            $data=mysql_fetch_array($result) or $retVal=$this->errorMessage("Unable to fetch row: $row");
            if(isset($retVal)) return $retVal;

            $values='';
            while(list($key,$val)=@each($data)){
                if(!is_int($key)){
                    $values.="$key = ";
                    $values.="'".addslashes($val)."', ";
                }
            }
            $values=ereg_replace(", $","",$values);
            $insert_data[]="INSERT INTO $dest_table set $values";
        }

        while(list($k,$query)=@each($insert_data)){
            mysql_select_db($dest_db);
            $result=mysql_query($query,$conn_id)  or
                $retVal=$this->errorMessage("Unable to perform query (data $table) on database: $dest_host: $dest_db");
            if(isset($retVal)) return $retVal;
        }
        return TRUE;
    }

    function SelectDB($db){
        $this->db=$db;
        if(mysql_select_db($db,$this->id))
            return TRUE;
        else
            return $this->errorMessage("Unable to select database: $db");
    }

    function GetTableList(){
        if($this->result=mysql_list_tables($this->db,$this->id)){
            $i=0;
            while($i < mysql_num_rows($this->result)){
                $tb_names[$i]=mysql_tablename($this->result,$i);
                $i++;
            }
            return($tb_names);
        }else
            return $this->errorMessage("Unable to find any tables in database: $this->db");
    }

    function GetFieldList($tbl_name){
        if($this->result=mysql_list_fields($this->db,$tbl_name,$this->id)){
            $i=0;
            while($i < mysql_num_fields($this->result)){
                $fd_names[$i]=mysql_field_name($this->result,$i);
                $i++;
            }
            return($fd_names);
        }else
            return $this->errorMessage("Unable to find any field list in table: $tbl_name");
    }

    function Delete($query){
        if($this->result=mysql_query($query,$this->id)){
            $this->a_rows=mysql_affected_rows($this->id);
            return TRUE;
        }else
            return $this->errorMessage("Unable to perform Delete: $query");
    }

    function Update($query){
        if($this->result=mysql_query($query,$this->id)){
            $this->a_rows=mysql_affected_rows($this->id);
            return TRUE;
        }else
            return $this->errorMessage("Unable to perform update: $query");
    }

    function Insert($query){
        if($this->result=mysql_query($query,$this->id)){
            $this->a_rows=mysql_affected_rows($this->id);
            return TRUE;
        }else
            return $this->errorMessage("Unable to perform insert: $query");
    }

    function InsertID(){
        if($this->result=mysql_insert_id($this->id)){
            return($this->result);
        }else
            return $this->errorMessage("Cannot retrieve auto_increment value: $this->id");
    }

    function Query($query){
        if($this->result=mysql_query($query,$this->id)){
            if(@mysql_num_rows($this->result))
                $this->rows=mysql_num_rows($this->result);
            else
                $this->rows=0;
            return TRUE;
        }else
            return $this->errorMessage("Unable to perform query: $query");
    }

    function GetRow($row){
        if(mysql_data_seek($this->result,$row)){
            if($this->data=mysql_fetch_array($this->result))
                return TRUE;
            else
                return $this->errorMessage("Unable to fetch row: $row");
        }else
            return $this->errorMessage("Unable to seek data row: $row");
    }

    function QueryRow($query){
        if($this->result=mysql_query($query,$this->id)){
            $this->rows=mysql_num_rows($this->result);
            if($this->data=mysql_fetch_array($this->result))
                return($this->data);
            else
                return $this->errorMessage("Unable to fetch data from query: $query");
        }else
            return $this->errorMessage("Unable to perform query: $query");
    }

    function QueryItem($query){
        if($this->result=mysql_query($query,$this->id)){
            $this->rows=mysql_num_rows($this->result);
            if($this->data=mysql_fetch_array($this->result))
                return($this->data[0]);
            else
                return $this->errorMessage("Unable to fetch data from query: $query");
        }else
            return $this->errorMessage("Unable to perform query: $query");
    }

    // returns XML-formatted record rows from query results.
    // $this->data holds the XML only, and the function returns the XML header + the data
    //  example:
    //  header("Content-type: text/xml");
    //  echo $sql->XML_Output("SELECT * FROM yourTable");
    // Thanks to René Moser <r.moser@meesly.ch>
    function XML_DataOutput($query, $tags = array('dataset','record')) {
        if($this->result=mysql_query($query,$this->id)) {
            $this->fields=mysql_num_fields($this->result);
            $xmlheader='<?xml version="1.0" ?>'."\n";
            $this->data.='<'.$tags[0].'>'."\n";
            while($this->rows = mysql_fetch_array($this->result)) {
                $this->data.= "\t".'<'.$tags[1].'>'."\n";
                for($i=0; $i<$this->fields; $i++) {
                    $tag = mysql_field_name($this->result,$i);
                    $this->data.="\t\t".'<'.$tag.'>'. preg_replace("/([\r\n])/", '', strip_tags($this->rows[$i])). '</'.$tag.'>'."\n";
                }
                $this->data.= "\t".'</'.$tags[1].'>'."\n";
             }
            $this->data.= '</'.$tags[0].'>';
            return $xmlheader.$this->data;
        }else
            return $this->errorMessage("Unable to perform query: $query; in XML output");
    }

    // Thanks to Andrew Collington <amnuts@talker.com> for the update
    function Exists($query){
        if ($this->result=mysql_query($query,$this->id)){
            if(mysql_num_rows($this->result))
                return TRUE;
            else
                return FALSE;
        }else
            return $this->errorMessage("Unable to perform query: $query");
    }

    // Thanks to Andrew Collington <amnuts@talker.com>
    function GetSetList($table,$field){
        $query="SHOW COLUMNS FROM $table LIKE '$field'";
        if($this->result=mysql_query($query,$this->id)){
            if($this->data=mysql_fetch_array($this->result)){
                while(list($key,$val)=@each($this->data)){
                    $this->data[$key]=stripslashes($val);
                }
                $mySet=split("','",substr($this->data[1],5,-2));
                return $mySet;
            }else
                return $this->errorMessage("Unable to fetch data set from: $query");
        }else
            return $this->errorMessage("Unable to perform set fetch: $query");
    }

    // Thanks to Andrew Collington <amnuts@talker.com>
    function GetEnumList($table,$field){
        $query="SHOW COLUMNS FROM $table LIKE '$field'";
        if($this->result=mysql_query($query,$this->id)){
            if($this->data=mysql_fetch_array($this->result)){
                while(list($key,$val)=@each($this->data)){
                    $this->data[$key]=stripslashes($val);
                }
                $myEnum=split("','",substr($this->data[1],6,-2));
                return $myEnum;
            }else
                return $this->errorMessage("Unable to fetch data enum from: $query");
        }else
            return $this->errorMessage("Unable to perform enum fetch: $query");
    }

    // Use this function to insert the binary image data of a picrutre into a database
    function InsertImage($image,$table,$blob_field,$num_att_cols,$atts1,$atts2='',$atts3='',$where_clause){
        // Examples of using this function
        // $sql->InsertImage($_FILES['userfile'],'my_table','my_blob_field',1,'attributes',0,"id = '1'");
        // $sql->InsertImage($_FILES['userfile'],'my_table','my_blob_field',2,'image_width','image_height',"id = '1'");
        $size=getimagesize($image['tmp_name']);
        switch($size[2]){
            case 1:  $type = 'GIF'; break;
            case 2:  $type = 'JPG'; break;
            case 3:  $type = 'PNG'; break;
            case 4:  $type = 'SWF'; break;
            case 5:  $type = 'PSD'; break;
            case 6:  $type = 'BMP'; break;
            case 7:  $type = 'TIFF(intel byte order)'; break;
            case 8:  $type = 'TIFF(motorola byte order)'; break;
            case 9:  $type = 'JPC'; break;
            case 10: $type = 'JP2'; break;
            case 11: $type = 'JPX'; break;
        }

                $fd=fopen($image['tmp_name'],"r");
                $data=addslashes(fread($fd,$image['size']));
                fclose($fd);

        $q="UPDATE `".$table."` SET `".$blob_field."` = '".$data."'";
        switch($num_att_cols){
            case 1: $q.=", `".$atts1."` = '".$size[3]."'";
                    break; // width and height HTML attribute string
            case 2: if($atts2)
                        $q.=", `".$atts1."` = '".$size[0]."', `".$atts2."` = '".$size[1]."'"; // width integer, height integer
                    else if($atts3){
                        $q.=", `".$atts1."` = '".$size[0]."', `".$atts3."` = '".$type."'"; // HTML attribute string, image type
                    }
                    break;
            case 3: $q.=", `".$atts1."` = '".$size[0]."', `".$atts2."` = '".$size[1]."', `".$atts3."` = '".$size[1]."'";
                    break; // width integer, height integer, image type
        }
        $q.=" WHERE ".$where_clause;

        return $this->Update($q);
    }

    // use this function to get the binary data from the database
    function GetImage($table,$blob_field,$num_att_cols,$atts1,$atts2='',$atts3='',$where_clause){
        $q="select `".$blob_field."`";
        switch($num_att_cols){
            case 1: $q.=", `".$atts1."`";
                    break; // width and height HTML attribute string
            case 2: if($atts2)
                        $q.=", `".$atts1."`, `".$atts2."`"; // width integer, height integer
                    else if($atts3){
                        $q.=", `".$atts1."`, `".$atts3."`"; // HTML attribute string, image type
                    }
                    break;
            case 3: $q.=", `".$atts1."`, `".$atts2."`, `".$atts3."`";
                    break; // width integer, height integer, image type
        }
        $q.=" from `".$table."` where ".$where_clause;
        if(!$this->Query($q))
            return $this->errorMessage('Error selecting image');
        if($this->rows < 2){
            if(!$this->GetRow(0))
                return $this->errorMessage('Error selecting image');
         }
        return $this->data;
    }

    function errorMessage($msg){
        return "Error: $msg : ".mysql_error();
    }
}
?>