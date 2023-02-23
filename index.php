<?php
	if(!isset($PHPSECURITYADMIN_PATH))
		$PHPSECURITYADMIN_PATH=dirname(__FILE__);
	Error_Reporting (0 & ~E_NOTICE);

	include $PHPSECURITYADMIN_PATH.'/include/_config.php';
	// To use in a templated system, we may need to have output strings returned, do this with the ob_* functions
	if($sec_sys->FANCYBOX){
		$item = $sec_sys->getItem($sec_sys->ITEM);
		//echo "<HTML>\n<HEAD>\n";
		//echo $sec_sys->getHead();
		//echo "</HEAD>\n";
		//echo "<BODY onload(function(){ if(document.images){ var im=new Image; im.src='".$sec_sys->getImgLink($item['src'])."';}})>\n";
		//echo '<img src="'.$sec_sys->getImgLink($item['src']).'">'."\n";
		header('Content-type: image/jpeg');
		readfile($PHPSECURITYADMIN_PATH.$sec_sys->getImgLink($item['src']));
		exit;
		//echo "</BODY>\n</HTML>";
	}
	else {
		ob_start();
		include $PHPSECURITYADMIN_PATH.'/include/shablon.php';
		// save the page output.
		$PHPSECURITYADMIN_OUTPUT=ob_get_contents();
		// clear the buffer and turn of buffering
		ob_end_clean();
		// This line is for displaying the output buffer.
		echo $sec_sys->convertString($PHPSECURITYADMIN_OUTPUT);
	}
?>