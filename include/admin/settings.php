<?php
	if(isset($_POST['submit'])){
		while(list($name, $val)=@each($_POST))
			if($name!='newname' && $name!='newvalue') $ar[$name] = $val;
		if(!empty($_POST['newname']))
			$ar[$_POST['newname']] = $_POST['newvalue'];
		if(isset($ar))
			$sec_sys->updateSettings($ar);
	}
	if(isset($ERR))
		echo '<span class="error">'.$ERR.'</span>';
?>
<form id="myForm" method="post" action="">
	<table class="inner">
		<tr>
			<th><label>№</label></th>
			<th><label>Название</label></th>
			<th><label>Значение</label></th>
		</tr>
<?php
		$settings=$sec_sys->getSettings();
		$i=1;
		while(list($name,$value)=@each($settings)){
			echo '		<tr>'."\n";
			echo '			<td><label>'.$i.'</label></td>'."\n";
			echo '			<td><label>'.$name.'</label></td>'."\n";
			echo '			<td><input type=text name='.$name.' size=60 value=\''.$value.'\'></td>'."\n";
			echo '		</tr>'."\n";
			$i++;
		}
		if($ADMINMMODE){
			echo '		<tr>'."\n";
		    echo '			<td><label>*</label></td>'."\n";
			echo '			<td><input type=text name=newname size=15 value=""></td>'."\n";
			echo '			<td><input type=text name=newvalue size=60 value=""></td>'."\n";
			echo '		</tr>'."\n";
		}
?>
		<tr>
			<td colspan=3 align=right><input type=submit name=submit value="Сохранить" /></td>
		</tr>
	</table>
</form>