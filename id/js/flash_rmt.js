function flashChecker()
{
var hasFlash=0;        
var flashVersion=0;
var isIE=/*@cc_on!@*/0;

if(isIE)
{
var swf = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
if(swf) {
hasFlash=1;
VSwf=swf.GetVariable("$version");
flashVersion=parseInt(VSwf.split(" ")[1].split(",")[0]);
}
}else{
if (navigator.plugins && navigator.plugins.length > 0)
{
var swf=navigator.plugins["Shockwave Flash"];
    if (swf)
     {
hasFlash=1;
        var words = swf.description.split(" ");
        for (var i = 0; i < words.length; ++i)
{
            if (isNaN(parseInt(words[i]))) continue;
            flashVersion = parseInt(words[i]);
}
    }
}
}
return {f:hasFlash,v:flashVersion};
}


fls = flashChecker()




if (fls.v < 9){
	document.write('<a href="index.php"><img src="images/id_picture.gif" width="900" height="120" alt="RMT" ></a>');
}else {

fflag=0;
if (document.layers || document.all || document.getElementById) {
 if (!fflag && document.all && !window.opera && navigator.userAgent.indexOf('Win')>-1) {
 document.write('<scr' + 'ipt type="text/vbscript"\> \n');
 document.write('on error resume next \n');
 document.write('fflag=( IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.6")))\n');
 document.write('<' + '/scr' + 'ipt> \n');
 }
 else if (navigator.plugins['Shockwave Flash']) fflag=1;
 }
if (fflag==0)
{
 document.write('<a href="index.php"><img alt="RMT" src="images/design/oc/header.jpg" width="900" height="120" border="0"><'+'/a>');
}else {
	 if (document.all && !window.opera)
		 document.write('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="900" height="120">'+
		 '<param name="movie" value="images/design/header_flash.swf" />'+
		 '<param name="wmode" value="transparent" />'+
		 '<param name="quality" value="high" />'+
		 '<'+'/object>'+
		 '');
 else
	 document.write('<object type="application/x-shockwave-flash" data="images/design/header_flash.swf" width="900" height="120">'+
		 '<param name="wmode" value="transparent" />'+
		 '<param name="quality" value="high" />'+
		 '<'+'/object>'+
		 '');
	 }
	}
	
