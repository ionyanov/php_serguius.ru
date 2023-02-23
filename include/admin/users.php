<?php
    $action = $_REQUEST['a'];
    $id = $_REQUEST['id'];
    if(isset($action)){
		switch($action){			case "delete":
				if($id != $_SESSION['psaun']) $sec_sys->deleteUser($id);
				else $ERR="Собственный логин удалить нельзя";
				break;
			case "add":
				if($sec_sys->isSystemUser($_POST['login']))
					$ERR='Пользователь с таким именем уже существует'."\n";
	            else{
	            	$ar = array('pass' => $_POST['password'],
                 				'id' => $_POST['login'],
                 				'active' => ($_POST['active']==TRUE?1:0));
	                $sec_sys->addUser($ar);
	            }
				break;
			case "update":
				while(list($n,$v)=@each($_POST))
		        	if(substr($n,0,6) == "submit")
		        		$uid = substr($n,6);
		   		if(isset($uid)){
					$ar = array('uid' => $_POST['uid'.$uid],
	               				'id' => $_POST['login'.$uid],
	               				'pass' => $_POST['password'.$uid],
	               				'active' => ($_POST['active'.$uid]==TRUE?1:0));
		            $sec_sys->editUser($ar);
	            }
				break;		}
		if(isset($ERR))
			echo '<span class="error">'.$ERR.'</span>';
    }
?>
<table cellpadding="4" align="center" cellspacing="0" border="1">
	<form id="myForm" method="post" action="?a=update">
	<tr>
		<th><label>№<label></th>
		<th><label>Логин<label></th>
		<th><label>Пароль<label></th>
		<th><label>Включен<label></th>
		<th><label>Удалить<label></th>
		<th><label>Сохранить<label></th>
	</tr>
<?php
	$users=$sec_sys->getUsers();
	$i=1;
	while(list($username,$details)=@each($users)){		$uid = $details['uid'];
		echo '	<tr>'."\n";
		echo '		<td><input type=hidden name=uid'.$uid.' value="'.$details['uid'].'">'.$i.'</td>'."\n";
		echo '		<td><input type=text name=login'.$uid.' size=10 value="'.$username.'"></td>'."\n";
		echo '		<td><input type=password name=password'.$uid.' size=10 value=""></td>'."\n";
		echo '		<td align="center"><input type=checkbox name=active'.$uid.''.(($details['active'])?' checked':'').'></td>'."\n";
		echo '		<td align="center"><input type=submit name=delete'.$uid.' value="Удалить" onclick="document.forms.myForm.action = \'?a=delete&id='.$username.'\'"/></td>'."\n";
		echo '		<td align="center"><input type=submit name=submit'.$uid.' value="Сохранить" /></td>'."\n";
		echo '	</tr>'."\n";
		$i++;
	}
?>
	</form>
	<form method="post" action="?a=add">
<?php
	echo '	<tr>'."\n";
    echo '		<td>*</td>'."\n";
	echo '		<td><input type=text name=login size=10 value=""></td>'."\n";
	echo '		<td><input type=password name=password size=10 value=""></td>'."\n";
	echo '		<td align="center"><input type=checkbox name=active></td>'."\n";
	echo '		<td>&nbsp;</td>'."\n";
	echo '		<td align="center"><input type=submit name=submit value="Добавить" /></td>'."\n";
	echo '	</tr>'."\n";
?>
	</form>
</table>
