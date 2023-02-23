<?php
	if(isset($_REQUEST['cat']))
		$id = $_REQUEST['cat'];
	if(isset($_POST['submit'])){
		$category = array('name' => $_POST['name'],
						'title' => $_POST['title'],
						'is_active' => (isset($_POST['is_active'])?1:0),
						'active_image' => $_POST['active'],
						'halfactive_image' => $_POST['halfactive'],
						'inactive_image' => $_POST['inactive'],
						'order' => intval($_POST['order']));
		$sec_sys->setCategory($category);
	}
	elseif(isset($_POST['up'])){
		$sec_sys->moveCategoryUp($id);
	}
	elseif(isset($_POST['down'])){
		$sec_sys->moveCategoryDown($id);
	}
?>
<table class="content">
	<tr>
		<th><label>Операции</label></th>
		<th><label>Описание</label></th>
		<th><label>Основная</label></th>
		<th><label>Текущая</label></th>
		<th><label>Запасная</label></th>
	</tr>
<?php
	$menuitems = $sec_sys->getCategories('1=1');
	while(list($cid,$detail)=@each($menuitems)){
		echo '	<form method="post" action="?cat='.$cid.'"><tr>'."\n";
		echo '		<td class="dotted"><input name="is_active" type="checkbox" '.(($detail['is_active'])?' checked':'').'></td>'."\n";
		echo '		<td class="dotted"><label>'.$cid.'<input name="name" type="hidden" value="'.$cid.'"></label></td>'."\n";
		echo '		<TD rowspan=3 class="dotted"><input id="active'.$cid.'" name="active" type="hidden" value="'.$detail['active_image'].'">'."\n";
		echo '			<img id="active_image'.$cid.'" class="menu" src="'.$sec_sys->getImgLink($detail['active_image']).'" '
			.'onclick="openImgSelector(\'active'.$cid.'\',\'active_image'.$cid.'\');"></TD>'."\n";
		echo '		<TD rowspan=3 class="dotted"><input id="inactive'.$cid.'" name="inactive" type="hidden" value="'.$detail['inactive_image'].'">'."\n";
		echo '			<img id="inactive_image'.$cid.'" class="menu" src="'.$sec_sys->getImgLink($detail['inactive_image']).'"  '
			.'onclick="openImgSelector(\'inactive'.$cid.'\',\'inactive_image'.$cid.'\');"></TD>'."\n";
		echo '		<TD rowspan=3 class="dotted"><input id="halfactive'.$cid.'" name="halfactive" type="hidden" value="'.$detail['halfactive_image'].'">'."\n";
		echo '			<img id="halfactive_image'.$cid.'" class="menu" src="'.$sec_sys->getImgLink($detail['halfactive_image']).'"  '
			.'onclick="openImgSelector(\'halfactive'.$cid.'\',\'halfactive_image'.$cid.'\');"></TD>'."\n";
		echo '	</tr>'."\n";
		echo '	<tr>'."\n";
		echo '		<td><input id="order" name="order" type="hidden" value="'.$detail['order'].'">'."\n";
		echo '			<input type="submit" name=up value="Выше"></td>'."\n";
		echo '		<td><input type="text" name="title" value="'.$detail['title'].'"></td>'."\n";
		echo '	</tr>'."\n";
		echo '	<tr>'."\n";
		echo '		<td><input type="submit" name=down value="Ниже"></td>'."\n";
		echo '		<td><input type="submit" name=submit value="Сохранить"></td>'."\n";
		echo '	</tr></form>'."\n";
	}
	$detail = $sec_sys->getNewCategory();
	echo '	<form method="post" action=""><tr>'."\n";
	echo '		<td class="dotted"><input name="is_active" type="checkbox" '.(($detail['is_active'])?' checked':'').'></td>'."\n";
	echo '		<td class="dotted"><input name="name" type="text" value="'.$detail['name'].'" required></td>'."\n";
	echo '		<TD rowspan=3 class="dotted"><input id="active" name="active" type="hidden" value="'.$detail['active_image'].'">'."\n";
	echo '			<img id="active_image" class="menu" src="'.$sec_sys->getImgLink($detail['active_image']).'" onclick="openImgSelector(\'active\',\'active_image\');"></TD>'."\n";
	echo '		<TD rowspan=3 class="dotted"><input id="inactive" name="inactive" type="hidden" value="'.$detail['inactive_image'].'">'."\n";
	echo '			<img id="inactive_image" class="menu" src="'.$sec_sys->getImgLink($detail['inactive_image']).'" onclick="openImgSelector(\'inactive\',\'inactive_image\');"></TD>'."\n";
	echo '		<TD rowspan=3 class="dotted"><input id="halfactive" name="halfactive" type="hidden" value="'.$detail['halfactive_image'].'">'."\n";
	echo '			<img id="halfactive_image" class="menu" src="'.$sec_sys->getImgLink($detail['halfactive_image']).'" onclick="openImgSelector(\'halfactive\',\'halfactive_image\');"></TD>'."\n";
	echo '	</tr>'."\n";
	echo '	<tr>'."\n";
	echo '		<td><input id="order" name="order" type="hidden" value="'.$detail['order'].'"></td>'."\n";
	echo '		<td><input name="title" type="text" value="'.$detail['title'].'"></td>'."\n";
	echo '	</tr>'."\n";
	echo '	<tr>'."\n";
	echo '		<td>&nbsp;</td>'."\n";
	echo '		<td><input type="submit" name=submit value="Добавить"></td>'."\n";
	echo '	</tr></form>'."\n";
?>
</table>