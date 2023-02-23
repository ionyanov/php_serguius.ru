<?php
// make sure this file is only included once
if(isset($SECURITY_CLASS_INC)) return;
$SECURITY_CLASS_INC=TRUE;

require_once dirname(__FILE__).'/class.phpMysqlConnection.php';
class phpSecurityAdmin extends phpMysqlConnection{
	var $SQL_HOST='';
	var $SQL_DB='';
	var $SQL_USER='';
	var $SQL_PASS='';
	var $VERSION='2.0';
	var $PHPSECURITYADMIN_HOST='';

	var $CATEGORY = 'index';
	var $ITEM;
	var $FANCYBOX=false;
	// This is the class constructor
    function phpSecurityAdmin($user='phpuser',$pass='php',$db='security',$host='localhost',$site=''){
        global $PHPSECURITYADMIN_PATH;

        $this->SQL_HOST=$host;
        $this->SQL_DB=$db;
        $this->SQL_USER=$user;
        $this->SQL_PASS=$pass;
        if(!empty($site))
            $this->PHPSECURITYADMIN_HOST=$site;
        else
            $this->PHPSECURITYADMIN_HOST=$_SERVER['HTTP_HOST'];

        // connect to the database - errors are output by the phpMysqlConnection
        $this->phpMysqlConnection($user,$pass,$host);
        $this-> Query("SET character_set_results='cp1251'");
        $this-> Query("set character_set_client='cp1251'");
		$this-> Query("set collation_connection='cp1251_general_ci'");
        $this->SelectDB($db);
        $this->initSettings();
        $this->parsLink();
    }

//-----------------------------------------------------------------------------------------------------------
    function isLoggedIn(){
        // if session remote value doesn't equal the remote address, don't allow access.
        if(isset($_SESSION['psaun']) && isset($_SESSION['remote']) &&
                ($_SESSION['remote'] == $_SERVER['REMOTE_ADDR'])){
            return TRUE;
        }
        return FALSE;
    }
//-----------------------------------------------------------------------------------------------------------
    function Login($user='',$pass=''){
        if($user=='')
            return "Пустое имя пользователя!";
        if($pass=='')
            return sprintf("Неверный пароль для '<b>%s</b>'. Попробуйте снова.",$user);

        // I want to make sure that there are no sneaks trying to pass SQL queries!
        $user=str_replace(' ','',$user);
        // password is md5() encoded before comparison - no need to check value (might want spaces in password)

        // clear session variables
        session_unset();

        // check if username is in the system
        if($this->isSystemUser($user)){
            // get user info from the database
            $query="SELECT `pass`,`hash`,`active`,`lockcount`,`uid` FROM `users` WHERE `id`='".
                trim($user)."'";
            $this->QueryRow($query);
            $db_pass=$this->data['pass'];
            $db_hash=$this->data['hash'];
            $active=$this->data['active'];
            $psau=$this->data['uid'];
            $lockcount=$this->data['lockcount']+1;

            if($lockcount>$this->LOCKCOUNT){
                $query="UPDATE `users` SET `updated`='".date('Y-m-d H:i:s').
                    "', `active`='0',`lockcount`='0' WHERE `id`='".$user."'";
                $this->Update($query);
                return "Пользователь заблокирован.";
            }

            // check if user account is active
            if(!$active)
                return "Пользователь заблокирован.";

            // check the password
            $pass=trim($pass);
            if((strlen($pass)>0 && md5($pass.$db_hash)==$db_pass)){
                $query="UPDATE `users` SET `updated`='".date("Y-m-d H:i:s").
                    "', `lockcount`='0' WHERE `id`='".$user."'";
                $this->Update($query);
                $psaun=$user;
                $remote=$_SERVER['REMOTE_ADDR'];
                $_SESSION['psaun']=$psaun;
                $_SESSION['remote']=$remote;
                $_SESSION['psag']=$psag;
                $_SESSION['psau']=$psau;
                return FALSE;
            }else{
                $query="UPDATE `users` SET `updated`='".date("Y-m-d H:i:s")."', `lockcount`='".$lockcount.
                    "' WHERE `id`='".$user."'";
                $this->Update($query);
                return sprintf("Неверный пароль для '<b>%s</b>'. Попробуйте снова.",$user);
            }
        }else
            return sprintf("Пользователь '<b>%s</b>' в системе не зарегистрирован.",$user);
    }
//-----------------------------------------------------------------------------------------------------------
    function Logout(){
        if(isset($_SESSION['psaun'])){
            session_unset();
            session_destroy();
        }
    }
//-----------------------------------------------------------------------------------------------------------
    function isSystemUser($user){
        if($this->Exists("SELECT `id` FROM `users` WHERE `id`='".$user."'"))
            return TRUE;
        return FALSE;
    }

//Users------------------------------------------------------------------------------------------------------
    function deleteUser($user){
        $this->Delete("DELETE FROM `users` WHERE `id`='".$user."'");
    }
//-----------------------------------------------------------------------------------------------------------
    function getUsers(){
        $users=array();
        $query='SELECT `id`,`uid`,`active` FROM `users` ORDER BY `id`';
        $this->Query($query);
        for($i=0;$i<$this->rows;$i++){
            $this->GetRow($i);
            $id=$this->data['id'];
            $uid=$this->data['uid'];
            $active=$this->data['active'];
            $users[$id]=array('id'=>$id,'active'=>$active,'uid'=>$uid);
        }
        return $users;
    }
//-----------------------------------------------------------------------------------------------------------
    function editUser($ar){
        $query="UPDATE `users` SET `updated`=FROM_UNIXTIME('".time."')";
        if(isset($ar['pass']) && $ar['pass']!='')
            $this->changePassword($ar['id'],$ar['pass']);
        while(list($field,$value)=@each($ar)){
            if($field=='id' || $field=='active')
                $query.=",`".$field."`='".$value."'";
        }
        $query.=" WHERE `uid`='".$ar['uid']."'";
        $this->Update($query);
        return TRUE;
    }
//-----------------------------------------------------------------------------------------------------------
    function addUser($ar){
        $users=$this->getUsers();
        if(!isset($users[$ar['id']])){
            $query="INSERT INTO `users` SET `updated`='".date('Y-m-d H:i:s')."'";

            $HASH_VAR=date('YmdHis').$_SERVER['REMOTE_ADDR'];
            $hash=md5($HASH_VAR);
            $password=md5($ar['pass'].$hash);

            $query.=",`id`='".$ar['id']."',`pass`='".$password."'".
                ",`hash`='".$hash."',`active`='".$ar['active']."' ";
            $this->Insert($query);
            return TRUE;
        }else
            return FALSE;
    }
//-----------------------------------------------------------------------------------------------------------
    function changePassword($user,$pass){
        if($_SESSION['psaun']==$user || $this->isAdmin($_SESSION['psaun'])){
            $HASH_VAR=date('YmdHis').$_SERVER['REMOTE_ADDR'];
            $hash=md5($HASH_VAR);
            $password=md5($pass.$hash);
            $query="UPDATE `users` SET `updated`='".date('Y-m-d H:i:s')."', `pass`='".$password."', ".
                "`hash`='".$hash."', `lockcount`='0', `active`='1' WHERE `id`='".$user."'";
            $this->Update($query);
            return TRUE;
        }else
            return FALSE;    // could possibly log these requests to show violations
    }

//Settings---------------------------------------------------------------------------------------------------
    function getSettings(){
        $settings=array();
        $query='SELECT `id`,`name`,`value` FROM `settings` ORDER BY `id`';
        $this->Query($query);
        for($i=0;$i<$this->rows;$i++){
            $this->GetRow($i);
            $settings[$this->data['name']]=$this->data['value'];
        }
        return $settings;
    }
//-----------------------------------------------------------------------------------------------------------
    function updateSettings($ar){
        $settings = $this->getSettings();
        while(list($name,$val)=@each($ar)) {
        	if($name!='submit'){
        		if($val=='') {
        			$query="DELETE FROM `settings` WHERE `name`='".$name."'";
                	$this->Delete($query);
        		}
            	elseif(!isset($settings[$name])!=$val) {
	             	$query="UPDATE `settings` SET `value`='".$val."' WHERE `name`='".$name."'";
	                $this->Update($query);
            	}
            	elseif($settings[$name]!=$val) {
	             	$query="INSERT INTO `settings` SET `value`='".$val."', `name`='".$name."'";
	                $this->Insert($query);
            	}
            }
        }
    }

//Other------------------------------------------------------------------------------------------------------
	function convertString($str) {
         $str = str_replace('&amp;nbsp;','&nbsp;',str_replace('&','&amp;', $str));
         $str = html_entity_decode(htmlspecialchars_decode($str), ENT_QUOTES, 'cp1251');
         return htmlspecialchars_decode($str, ENT_QUOTES);
    }
//-----------------------------------------------------------------------------------------------------------
	function initSettings(){
		$this->TO_INDEX_TITLE = 'To the Main Page. На Главную Страницу';
		$this->CATEGORY_CONTACT = 'contact';
		$this->CATEGORY_MAIN = 'index';
		$this->COLS = 4;
		$this->ROWS = 6;
		$this->NEWIMAGE = 'images/new.gif';

		$this->LOCKCOUNT = 5;

		$this->TITLE = 'SERGUIUS - сайт "Творчество Сергея Агасаряна" - СЕРГИУС';
		$this->TITLE2 = 'Творчество Сергея Агасаряна Искусство Живопись Рисунки Роспись Пейзажи Фотографии Графика Портреты Натюрморты';
		$this->AUTHOR = 'Agasaryen Serguey, Email: serguiusaga@hotmail.com, serguius.ru';
		$this->KEYWORDS ="Куплю Картины Современные художники Московский художник Художники Рисунки Искусство Галереи Живопись Фреска Графика Фотографии Иконы Интерьеры Роспись Узоры Дизайн Ремонт Офорты Гравюры Орнаменты Витражи Копии Портреты Пейзажи Резьба по дереву Agasaryen Agasaryan";
		$this->KEYWORDS2 ="Куплю картины живопись графика фотографии копии росписи фрески выставки интерьеры дизайн дизайнеры резьба по дереву московские художники";
		$this->COPYRIGHT ="Serguey Agasaryen Сергей Агасарян";
		$this->CLASSIFICATION ="Изобразительное искусство. Картины. Творчество";
		$this->CATEGORY ="Fine arts,Paintings,Creation";

		$settings=$this->getSettings();
		while(list($name,$value)=@each($settings)) $this->$name=$value;
	}
//-----------------------------------------------------------------------------------------------------------
	function getHead() {
		return '	<TITLE>'.$this->TITLE.'</TITLE>
	<META http-equiv=Content-Type content="text/html; charset=windows-1251">
	<META NAME="title" CONTENT="'.$this->TITLE2.'">
	<META NAME="author" CONTENT="'.$this->AUTHOR.'">
	<META NAME="keywords" content="'.$this->KEYWORDS.'">
	<META HTTP-EQUIV="keywords" CONTENT="'.$this->KEYWORDS2.'">
	<META NAME="robots" CONTENT="INDEX,ALL">
	<META NAME="description" CONTENT="Росписи станковая живопись графика фрески фотографии дизайны интерьеры нарисовать московские российские художники гравюры копии иконы">
	<META NAME="copyright" CONTENT="'.$this->COPYRIGHT.'">
	<META NAME="document-state" Content="dynamic">
	<META NAME="revisit-after" Content="10days">
	<META NAME="classification" content="'.$this->CLASSIFICATION.'">
	<META NAME="CATEGORY" content="'.$this->CATEGORY.'">
	<META NAME="language" content="Russian,English">
	<META NAME="rating" content="general">
	<META NAME="distribution" content="global">
	<!-- Add fancybox library -->
	<SCRIPT type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></SCRIPT>
	<SCRIPT> !window.jQuery && document.write(\'<script src="'.$this->PHPSECURITYADMIN_HOST.'fancybox/jquery-1.4.3.min.js"><\/script>\');</SCRIPT>
	<SCRIPT type="text/javascript" src="'.$this->PHPSECURITYADMIN_HOST.'fancybox/jquery.mousewheel-3.0.4.pack.js"></SCRIPT>
	<SCRIPT type="text/javascript" src="'.$this->PHPSECURITYADMIN_HOST.'fancybox/jquery.fancybox-1.3.4.pack.js"></SCRIPT>
	<LINK rel="stylesheet" type="text/css" href="'.$this->PHPSECURITYADMIN_HOST.'fancybox/jquery.fancybox-1.3.4.css" media="screen" />

	<LINK rel="stylesheet" type="text/css" href="'.$this->PHPSECURITYADMIN_HOST.'style.css" media="screen" />
	<SCRIPT type="text/javascript" src="'.$this->PHPSECURITYADMIN_HOST.'scripts.js"></SCRIPT>';
	}

//-----------------------------------------------------------------------------------------------------------
	function getLink($category, $item = '') {
		$result = $this->CATEGORY_MAIN;
		if($category==$this->CATEGORY && $item!='')
			$result=$category.'/'.$item;
		elseif($category!=$this->CATEGORY || (isset($this->ITEM) && $category==$this->CATEGORY))
			$result = $category;
		return $this->PHPSECURITYADMIN_HOST.$result.'.html';
	}
//-----------------------------------------------------------------------------------------------------------
	function getImgLink($src){
		return $src;//str_replace("//","/",$this->PHPSECURITYADMIN_HOST.$src);
	}
//-----------------------------------------------------------------------------------------------------------
	function parsLink() {
		//while(list($n,$v)=@each($_REQUEST)) echo "::".$n."=".$v.";"."\n";
		$link = str_replace(".html","",str_replace($PSA_SITE_NAME,"",$_REQUEST['p']));
		$arr = explode("/",$link);
		if($arr[0]=='box') {
			$this->CATEGORY = $arr[1];
			$this->ITEM = $arr[2];
			$this->FANCYBOX = true;
		}
		else {
			$this->CATEGORY = !empty($arr[0])?$arr[0]: $this->CATEGORY_MAIN;
			$this->ITEM = $arr[1];
		}
		if(isset($_REQUEST['item']))
			$this->ITEM = intval($_REQUEST['item']);
	}

//Menu-------------------------------------------------------------------------------------------------------
	function getCategories($where) {
        $categories=array();
        $query='SELECT `title` , `active_image` , `halfactive_image` , `inactive_image` , `is_active` , `name` , `order` FROM `categories`';
        if(isset($query)) $query .= ' WHERE '.$where;
        $query.= ' ORDER BY `order`, `name`';
        $this->Query($query);
        for($i=0;$i<$this->rows;$i++){
            $this->GetRow($i);
            $id=$this->data['id_category'];
            $title=$this->data['title'];
            $name=$this->data['name'];
            $order=$this->data['order'];
            $active_image=$this->data['active_image'];
            $halfactive_image=$this->data['halfactive_image'];
            $inactive_image=$this->data['inactive_image'];
            $is_active=$this->data['is_active'];
            $categories[$name]=array('title'=>$title,'is_active'=>$is_active,'name'=>$name,'order'=>$order
            	,'active_image'=>$active_image,'halfactive_image'=>$halfactive_image
            	,'inactive_image'=>$inactive_image, 'link'=>$this->getLink($name));
        }
		return $categories;
	}
//-----------------------------------------------------------------------------------------------------------
	function getActiveCategories() {
        return $this->getCategories('`is_active`=1');
	}
//-----------------------------------------------------------------------------------------------------------
	function getNextCategoryOrder(){
		return intval($this->QueryItem("SELECT MAX(`order`)+1 FROM `categories`"));
	}
//-----------------------------------------------------------------------------------------------------------
	function getNewCategory() {
        return array('title'=>'Новая','is_active'=>1,'name'=>'new','order'=>$this->getNextCategoryOrder()
            	,'active_image'=>$this->NEWIMAGE,'halfactive_image'=>$this->NEWIMAGE
            	,'inactive_image'=>$this->NEWIMAGE);
	}
//-----------------------------------------------------------------------------------------------------------
	function setCategory($category) {
        while(list($field,$value)=@each($category)){
            if($field=='name' || $field=='title' || $field=='is_active' || $field=='order' ||
            	$field=='inactive_image' || $field=='active_image' || $field=='halfactive_image'){
            		if(isset($per))
            			$query.=",`".$field."`='".$value."'";
            		else{
            			$per = TRUE;
            			$query.="`".$field."`='".$value."'";
            		}
            	}
        }
        $where = " WHERE `name`='".$category['name']."'";
		if($this->Exists("SELECT `order` FROM `categories`".$where))
			$this->Update("UPDATE `categories` SET ".$query.$where);
		else
			$this->Insert("INSERT INTO `categories` SET ".$query);
	}
//-----------------------------------------------------------------------------------------------------------
	function moveCategoryUp($cid){
    	$curCategory = $this->getCategories("`name`='".$cid."'");
        $curCategory = $curCategory[$cid];
        $query='SELECT `name`, `order` FROM `categories` WHERE `order`<'.$curCategory['order'].' ORDER BY `order` DESC LIMIT 1';
        if($this->Exists($query)){
	        $this->QueryRow($query);
	        $nid=$this->data['name'];
	        $order=$this->data['order'];

	        $this->setCategory(array('name'=>$nid,'order'=>$curCategory['order']));
	        $this->setCategory(array('name'=>$cid,'order'=>$order));
        }
    }
//-----------------------------------------------------------------------------------------------------------
	function moveCategoryDown($cid){
    	$curCategory = $this->getCategories("`name`='".$cid."'");
        $curCategory = $curCategory[$cid];
        $query='SELECT `name`, `order` FROM `categories` WHERE `order`>'.$curCategory['order'].' ORDER BY `order` LIMIT 1';
        if($this->Exists($query)){
	        $this->QueryRow($query);
	        $nid=$this->data['name'];
	        $order=$this->data['order'];

	        $this->setCategory(array('name'=>$nid,'order'=>$curCategory['order']));
	        $this->setCategory(array('name'=>$cid,'order'=>$order));
        }
    }

//Item-------------------------------------------------------------------------------------------------------
	function getItems($where) {
        $items=array();
        $query='SELECT `id`, `src`, `preview`, `title`, `description`, `order`, `category`, `is_active` FROM `items`';
        if(isset($query)) $query .= ' WHERE '.$where;
        $query.= ' ORDER BY `order`';
        $this->Query($query);
        for($i=0;$i<$this->rows;$i++){
            $this->GetRow($i);
            $id=$this->data['id'];
            $src=$this->data['src'];
            $preview=$this->data['preview'];
            $title=$this->data['title'];
            $description=$this->data['description'];
            $order=$this->data['order'];
            $category=$this->data['category'];
            $is_active=$this->data['is_active'];
            $items[]=array('src'=>$src,'preview'=>$preview,'title'=>$title,
            	'description'=>$description,'order'=>$order,'category'=>$category
            	,'is_active'=>$is_active, 'link'=>$this->getLink($category, $order));
        }
        return $items;
	}
//-----------------------------------------------------------------------------------------------------------
	function getItem($order) {
        $items = $this->getItems('`is_active`=1 AND `order`='.intval($order).' AND `category`=\''.$this->CATEGORY.'\'');
        return $items[0];
	}
//-----------------------------------------------------------------------------------------------------------
	function getNewItem($order) {
		return array('src'=>$this->NEWIMAGE,'preview'=>$this->NEWIMAGE,'title'=>'',
			'description'=>'','order'=>$order,'category'=>$this->CATEGORY
			,'is_active'=>1, 'link'=>$this->getLink($this->CATEGORY, $order));
	}
//-----------------------------------------------------------------------------------------------------------
	function deleteItem($order) {
		$query = $this->Update('UPDATE `items` SET `is_active`=0 WHERE `order`='.intval($order).' AND `category`=\''.$this->CATEGORY.'\'');
		if ($query>0) {
			return '<p class="event">Страница удалена.</p>';
		} else {
			return '<p class="error">Страница не удалена.</p>';
		}
	}
//-----------------------------------------------------------------------------------------------------------
	function setItem($item) {
		while(list($field,$value)=@each($item)){
			if($field=='src' || $field=='preview' || $field=='title'
				|| $field=='description' || $field=='order' || $field=='category'){
					$query.=",`".$field."`='".$value."'";
			}
		}
		$where = " WHERE `order`=".intval($item['order'])." AND `category`='".$item['category']."'";
		if($this->Exists("SELECT `order` FROM `items`".$where))
			$this->Update("UPDATE `items` SET `is_active`=1".$query.$where);
		else
			$this->Insert("INSERT INTO `items` SET `is_active`=1".$query);
		return TRUE;
	}
}
?>