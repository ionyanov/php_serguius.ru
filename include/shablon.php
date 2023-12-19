<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<?php
echo $sec_sys->getHead();
?>
</HEAD>

<BODY>
<DIV id="container">
<TABLE class="outer"><TR><TD class="reklama"></TD><TD>
	<TABLE class="outer">
		<TR><TD>
<?php
if($sec_sys->isLoggedIn()){
	print "		<TABLE class=\"inner\"><TR>\n";
	print "			<TD align=center><a href=\"categories.html\">���������</a></TD>\n";
	if($ADMINMMODE)
		print "			<TD align=center><a href=\"users.html\">������������</a></TD>\n";
	print "			<TD align=center><a href=\"settings.html\">���������</a></TD>\n";
	print "		</TR></TABLE>\n";
}
else echo "&nbsp;";
?>
		</TD></TR>
		<TR><TD class="center">
			<TABLE class="inner">
				<TR>
					<TD class="menu">
						<TABLE class="menu">
<?php
$menuitems = $sec_sys->getActiveCategories();
while(list($cid,$detail)=@each($menuitems)){
	$link = $detail['link'];
	$src = $detail['active_image'];
	$desc = $detail['title'];
	if($sec_sys->CATEGORY==$cid) {
		if(isset($sec_sys->ITEM)){
			$src = $detail['halfactive_image'];
		}
		else {
			$desc = $sec_sys->TO_INDEX_TITLE;
			$src = $detail['inactive_image'];
		}
	}
	echo '	<TR><TD><A href="'.$link.'"; title="'.$desc.'">'.
		'<IMG class="menu" src="'.$sec_sys->getImgLink($src).'" alt="'.$desc.'"></A></TD></TR>'."\n";
}
?>
						</TABLE>
					</TD>
<?php
$page = '';
switch($sec_sys->CATEGORY){
	case $sec_sys->CATEGORY_MAIN: break;
	case $sec_sys->CATEGORY_CONTACT: $page='/include/contact.php'; break;
	case 'phpinfo': $page='/include/admin/phpinfo.php'; break;
	case 'login':  $page='/include/admin/login.php'; break;
	case 'categories': if($sec_sys->isLoggedIn()) $page='/include/admin/categories.php'; break;
	case 'users': if($sec_sys->isLoggedIn()) $page='/include/admin/users.php'; break;
	case 'settings': if($sec_sys->isLoggedIn()) $page='/include/admin/settings.php'; break;
	default: $page='/include/'.($sec_sys->isLoggedIn()?'admin/':'').'content.php'; break;
}
if($page!='') {
	echo '					<TD class="main">'."\n";
	include $PHPSECURITYADMIN_PATH.$page;
	echo '					</TD>'."\n";
}
else
	echo '					<TD class="emptymain">&nbsp;</TD>'."\n";
?>
					<TD class="contact">
<?php
$link = $sec_sys->getLink($sec_sys->CATEGORY_CONTACT);
if($sec_sys->CATEGORY==$sec_sys->CATEGORY_CONTACT) {
	$desc = $sec_sys->TO_INDEX_TITLE;
	$src = 'images/about_notactiv.jpg';
}
else {
	$desc = "Contacts. ��������";
	$src = 'images/about_activ.jpg';
}
echo '						<A href="'.$link.'"; title="'.$desc.'"><IMG class="contact" alt="'.$desc.'" src="'.$sec_sys->getImgLink($src).'"></A>'."\n";
?>
					</TD>
				</TR>
			</TABLE>
		</TD></TR>
		<TR><TD class="copyright"><A class="copyright" href="login.html">&copy; �������� ������</A></TD></TR>
	</TABLE>
</TD><TD class="reklama">
</TD></TR></TABLE>
</DIV>
</BODY>
</HTML>