<TABLE class="content" height="100%">
<?php
if(isset($sec_sys->ITEM)) {
	$item = $sec_sys->getItem($sec_sys->ITEM);
	echo '	<TR height="400px">'."\n";
	echo '		<TD colspan=2><img class="fullsize" src="'.$sec_sys->PHPSECURITYADMIN_HOST.$item['src'].'"></TD>'."\n";
	echo '	</TR>'."\n";
	echo '	<TR>'."\n";
	echo '		<TD class="left_descr">'.$item['title'].'</TD>'."\n";
	echo '		<TD class="right_descr">'.$item['description'].'</TD>'."\n";
	echo '	</TR>'."\n";
	echo '	<TR  height="18px"><TD class="left_descr">'."\n";
	$count = $sec_sys->ROWS*$sec_sys->COLS;
	$item_id = 1;
	$allitem = array();
	for ($i=0; $i<$count; $i++) {
		$item = $sec_sys->getItem($i);
		if (isset($item)){
			if($i==$sec_sys->ITEM)
				$allitem[] = '		<lable>'.$item_id.'</lable>'."\n";
			else
				$allitem[] = '		<A class="itemlink" href="'.$item['link'].'">'.$item_id.'</A>'."\n";
			$item_id++;
		}
	}
	$i=0;
	foreach($allitem as $item) {
		if($i==intval(count($allitem)/2))
			echo '	</TD><TD class="right_descr">'."\n";
		echo $item;
		$i++;
	}
	echo '	</TD></TR>'."\n";
}
else {
	$item_id = 1;
	for ($i=0; $i<$sec_sys->ROWS; $i++){
		echo '	<TR>'."\n";
		for ($j=1; $j<=$sec_sys->COLS; $j++){
			$item = $sec_sys->getItem($item_id);
			echo '		<TD class="preview"> '."\n";
			if (isset($item))
				echo '			<A class="fancybox" rel="group" title="'.$item['title'].'<br>'.$item['description'].'" href="'.$item['link'].'">
					<IMG class="preview" src="'.$PSA_SITE_NAME.$item['preview'].'" title="'.$item['title'].'" alt="'.$item['description'].'"></A>';
			echo '		</TD>'."\n";
			if($j==intval($sec_sys->COLS/2))
				echo '		<TD class="emptypreview"></TD>'."\n";
			$item_id++;
		}
		echo '	</TR>'."\n";
	}
}
?>
</TABLE>