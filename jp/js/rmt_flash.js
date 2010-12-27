fflag=0;
if (document.layers || document.all || document.getElementById) {
	if (!fflag && document.all && !window.opera && navigator.userAgent.indexOf('Win')>-1) {
		document.write('<scr' + 'ipt type="text/vbscript"\> \n');
		document.write('on error resume next \n');
		document.write('fflag=( IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.6")))\n');
		document.write('</scr' + 'ipt> \n');
		}
    else if (navigator.plugins['Shockwave Flash']) fflag=1;
	}

if (fflag==0)
	document.write('<a href="index.php"><img alt="RMT" src="images/rmt.gif" width="185" height="65" border="0" /></a>');
else {
	if (document.all && !window.opera)
		document.write('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="898" height="70">'+
			'<param name="movie" value="images/rmt.swf" />'+
			'<param name="wmode" value="transparent" />'+
			'<param name="quality" value="high" />'+
			'</object>'+
			'');
	else
		document.write('<object type="application/x-shockwave-flash" data="images/rmt.swf" width="898" height="70">'+
			'<param name="wmode" value="transparent" />'+
			'<param name="quality" value="high" />'+
			'</object>'+
			'');
	}
