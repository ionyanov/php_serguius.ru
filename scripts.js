function formatTitle(title, currentArray, currentIndex, currentOpts) {
	return '<div id="title"><span><a href="javascript:;" onclick="$.fancybox.close();"><img src="fancybox/fancy_close.png" /></a></span>' + (title && title.length ? '<b>' + title + '</b>' : '' ) + 'Изображение ' + (currentIndex + 1) + ' из ' + currentArray.length + '</div>';
}
function openImgSelector(input, img) {	var URL = 'filebrowser/?editor=standalone&returnID='+input;
	NewWindow = window.open(URL,"_blank","toolbar=no,menubar=0,status=1,copyhistory=0,scrollbars=yes,resizable=1,location=0,Width=1500,Height=760");
	NewWindow.onunload = function(){ document.getElementById(img).src = document.getElementById(input).value; };
}

$(document).ready(function() {
	$("a.fancybox").each( function(index, Element) {		var arr = Element.href.split('/');
		Element.href=arr.slice(0, -2).concat('box', arr.slice(-2)).join('/');	})
	$("a.fancybox").fancybox({
		'hideOnContentClick': true,
		'showCloseButton'	: false,
		'titleFormat'		: formatTitle,
		'titlePosition'		: 'inside',
		'type'				: 'image'
	});
});