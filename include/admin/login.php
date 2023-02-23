<?php // 6/6/02 4:29PM
if(isset($_POST['LOGIN'])){
	$user  = array_key_exists('user', $_REQUEST)  ? $_REQUEST['user']  : '';
	$pass  = array_key_exists('pass', $_REQUEST)  ? $_REQUEST['pass']  : '';
    $result=$sec_sys->Login($user,$pass);
    if($result){
    	echo '<h3 align=center>'.$result.'</h3>';
    }else{    	header('Location: index.html');
    	exit;
    }
}
if($sec_sys->isLoggedIn()){
	$sec_sys->Logout();
	header('Location: index.html');
	exit;
}else{
?>
<form id="myForm" method=post action="">
	<table border="0" align="center">
		<tr>
			<td align="right"><label for="user">Логин</label></td>
			<td><input type=text size=15 name=user id=user></td>
		</tr>
		<tr>
			<td align="right"><label for="pass">Пароль</label></td>
			<td><input type=password size=15 name=pass id=pass></td>
		</tr>
		<tr>
			<td></td>
			<td><input type=submit name=LOGIN value="Войти"></td>
		</tr>
	</table>
</form>
<?php
}
?>