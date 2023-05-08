<?php
	if(isset($_POST['submit'])){
		$item = array('src' => $_POST['src'],
				'preview' => $_POST['preview'],
				'title' => $_POST['title'],
				'description' => $_POST['description'],
				'order' => $sec_sys->ITEM,
				'category' => $sec_sys->CATEGORY);
		$sec_sys->setItem($item);
	}
	elseif(isset($_POST['delete'])) {
		$sec_sys->deleteItem($sec_sys->ITEM);
		$sec_sys->ITEM = null;
	}
	if(isset($ERR))
		echo '<span class="error">'.$ERR.'</span>';
?>
<TABLE class="content">
	<form id="myForm" method="post" action="">
<?php
if(isset($sec_sys->ITEM)) {
	$item = $sec_sys->getItem($sec_sys->ITEM);
	if(!isset($item)) $item = $sec_sys->getNewItem($sec_sys->ITEM);
	echo '	<TR height="400px">'."\n";
	echo '		<TD colspan=3><input id="src" name="src" type="hidden" value="'.$item['src'].'">'."\n";
	echo '			<img id="srcimg" class="fullsize" src="'.$item['src'].'" onclick="openImgSelector(\'src\',\'srcimg\');"></TD>'."\n";
	echo '	</TR>'."\n";
	echo '	<TR>'."\n";
	echo '		<TD class="left_descr"><textarea name="title" rows=3 cols=25>'.$item['title'].'</textarea></TD>'."\n";
	echo '		<TD rowspan=2><input id="preview" name="preview" type="hidden" value="'.$item['preview'].'">'."\n";
	echo '			<img id="previewimg" class="preview" src="'.$item['preview'].'" onclick="openImgSelector(\'preview\',\'previewimg\');"></TD>'."\n";
	echo '		<TD class="right_descr"><textarea name="description" rows=3 cols=25>'.$item['description'].'</textarea></TD>'."\n";
	echo '	</TR>'."\n";
	echo '	<TR>'."\n";
	echo '		<TD class="left_descr"><input type="submit" name="delete" value="Удалить"></TD>'."\n";
	echo '		<TD class="right_descr"><input type="submit" name="submit" value="Сохранить"></TD>'."\n";
	echo '	</TR>'."\n";
}
else {
	$item_id = 1;
	for ($i=0; $i<$sec_sys->ROWS; $i++){
		echo '	<TR>'."\n";
		for ($j=1; $j<=$sec_sys->COLS; $j++){
			$item = $sec_sys->getItem($item_id);
			echo '		<TD class="adminpreview" id="'.$item_id.'" onclick="window.location.href = \'?item=\'+this.id;"> '."\n";
			if (isset($item))
				echo '			<A title="'.$item['title'].'<br>'.$item['description'].'" href="?item='.$item_id.'">
					<IMG class="preview" src="'.$item['preview'].'" title="'.$item['title'].'" alt="'.$item['description'].'"></A>';
			echo '		</TD>'."\n";
			if($j==intval($sec_sys->COLS/2))
				echo '		<TD class="emptypreview"></TD>'."\n";
			$item_id++;
		}
		echo '	</TR>'."\n";
	}
}
?>
	</form>
</TABLE>