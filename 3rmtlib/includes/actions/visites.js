function pmv_plugMoz(pmv_pl) {
	if (pmv_tm.indexOf(pmv_pl) != -1 && (navigator.mimeTypes[pmv_pl].enabledPlugin != null))
		return '1';
	return '0';
}
function pmv_plugIE(pmv_plug){
	pmv_find = false;
	document.write('<SCR' + 'IPT LANGUAGE=VBScript>\n on error resume next \n pmv_find = IsObject(CreateObject("' + pmv_plug + '")) </SCR' + 'IPT>\n');
	if (pmv_find) return '1';
	return '0';
}
function getFlashVer() {
 var f="",n=navigator;
 if (n.plugins && n.plugins.length) {
  for (var ii=0;ii<n.plugins.length;ii++) {
   if (n.plugins[ii].name.indexOf('Shockwave Flash')!=-1) {
    f=n.plugins[ii].description.split('Shockwave Flash ')[1];
    break;
   }
  }
 } else if (window.ActiveXObject) {
  for (var ii=10;ii>=2;ii--) {
   try {  
    var fl=eval("new ActiveXObject('ShockwaveFlash.ShockwaveFlash."+ii+"');");
    if (fl) {f=ii + '.0'; break; }
   }
   catch(e) {}
  }
 }
 return f;
}

//ユーザーの言語環境
function getUserLanguage(){
	if (objChk(navigator.userLanguage))
		return navigator.userLanguage;
	else{return '';}
} 

//パソコンの言語環境
function getSystemLanguage(){
	if (objChk(navigator.systemLanguage))
		return navigator.systemLanguage;
	else{return '';}
} 

function objChk(pObj){
	if (pObj==null){return false;}
	if (pObj=="undefined"){return false;}
	if (pObj==""){return false;}
	return true;
} 

//check pdf install
function pdfChk(){
  //if ff
  if (navigator.plugins && navigator.plugins.length) {
    for (x=0; x<navigator.plugins.length;x++) {
      if (navigator.plugins[x].name== 'Adobe Acrobat')
        return true;
    }
  } else if (window.ActiveXObject) {
    for (x=2; x<10; x++)
    {
      try
      {
        oAcro=eval("new ActiveXObject('PDF.PdfCtrl."+x+"');");
        if (oAcro)
        return true;
      }
      catch(e) {}
    }
    try
    {
      oAcro4=new ActiveXObject('PDF.PdfCtrl.1');
      if (oAcro4)
        return true;
    }
    catch(e) {}
    try
    {
      oAcro7=new ActiveXObject('AcroPDF.PDF.1');
      if (oAcro7)
        return true;
    }
    catch(e) {}
  }
}

var pmv_jav='0'; if(navigator.javaEnabled()) pmv_jav='1';
var pmv_agent = navigator.userAgent.toLowerCase();
var pmv_moz = (navigator.appName.indexOf("Netscape") != -1);
var pmv_ie = (pmv_agent.indexOf("msie") != -1);
var pmv_win = ((pmv_agent.indexOf("win") != -1) || (pmv_agent.indexOf("32bit") != -1));
// Determine if cookie enabled
var pmv_cookie=(navigator.cookieEnabled)? '1' : '0';

//if not IE4+ nor NS6+
if ((typeof (navigator.cookieEnabled) =="undefined") && (pmv_cookie == '0')) { 
	document.cookie="pmv_testcookie"
	pmv_cookie=(document.cookie.indexOf("pmv_testcookie")!=-1)? '1' : '0';
}

var pmv_dir = '0'; 
var pmv_fla = '0'; 
var pmv_fla_ver = getFlashVer(); 
var pmv_pdf = '0'; 
var pmv_qt = '0'; 
var pmv_rea = '0'; 
var pmv_wma = '0'; 

if (!pmv_win || pmv_moz){
	var pmv_tm = '';
	for (var i=0; i < navigator.mimeTypes.length; i++)
		pmv_tm += navigator.mimeTypes[i].type.toLowerCase();
	pmv_dir = pmv_plugMoz("application/x-director");
	pmv_fla = pmv_plugMoz("application/x-shockwave-flash");
	pmv_pdf = pmv_plugMoz("application/pdf");
	pmv_qt = pmv_plugMoz("video/quicktime");
	pmv_rea = pmv_plugMoz("audio/x-pn-realaudio-plugin");
	pmv_wma = pmv_plugMoz("application/x-mplayer2");
} else if (pmv_win && pmv_ie){
	pmv_dir = pmv_plugIE("SWCtl.SWCtl.1");
	pmv_fla = pmv_plugIE("ShockwaveFlash.ShockwaveFlash.1");
	//if (pmv_plugIE("PDF.PdfCtrl.1") == '1' || pmv_plugIE('PDF.PdfCtrl.5') == '1' || pmv_plugIE('PDF.PdfCtrl.6') == '1' || pmv_plugIE('PDF.PdfCtrl.7') == '1') 
	//	pmv_pdf = '1';
	if (pdfChk()) pmv_pdf = '1';
	pmv_qt = pmv_plugIE("Quicktime.Quicktime"); // Old : "QuickTimeCheckObject.QuickTimeCheck.1"
	pmv_rea = pmv_plugIE("rmocx.RealPlayer G2 Control.1");
	pmv_wma = pmv_plugIE("wmplayer.ocx"); // Old : "MediaPlayer.MediaPlayer.1"

}
	
var pmv_do = document;
var pmv_rtu = '';
try {pmv_rtu = top.pmv_do.referrer;} catch(e) {
	if (parent) {
		if (parent.pmv_getReferer) {
			try {pmv_rtu = parent.pmv_getReferer;} catch(E3) {pmv_rtu = '';}
		}
		else  {
			try {pmv_rtu = parent.document.referrer;} catch(E) {
				try {pmv_rtu = document.referrer;} catch(E2) {pmv_rtu = '';}
			}
		}
		parent.pmv_getReferer = document.location.href;
	}
	else {
		try {pmv_rtu = document.referrer;} catch(E3) {pmv_rtu = '';}
	}
}
// Get the url to call phpmyvisites
function pmv_getUrlStat(pmv_urlPmv, pmv_site, pmv_urlDoc, pmv_pname, pmv_typeClick, pmv_vars)
{
	var pmv_getvars='';
	if (pmv_vars) {
		for (var i in pmv_vars){
			if (!Array.prototype[i]){
				pmv_getvars = pmv_getvars + '&a_vars['+ escape(i) + ']' + "=" + escape(pmv_vars[i]);
			}
		}
	}
	
	var pmv_da = new Date();
	var pmv_src = pmv_urlPmv;
	pmv_src += '?url='+escape(pmv_urlDoc)+'&pagename='+escape(pmv_pname)+pmv_getvars;
	pmv_src += '&id='+pmv_site+'&res='+screen.width+'x'+screen.height+'&col='+screen.colorDepth;
	pmv_src += '&h='+pmv_da.getHours()+'&m='+pmv_da.getMinutes()+'&s='+pmv_da.getSeconds();
	pmv_src += '&flash='+pmv_fla+'&director='+pmv_dir+'&quicktime='+pmv_qt+'&realplayer='+pmv_rea;
	pmv_src += '&pdf='+pmv_pdf+'&windowsmedia='+pmv_wma+'&java='+pmv_jav+'&cookie='+pmv_cookie;
	pmv_src += '&systemlanguage='+getSystemLanguage()+'&userlanguage='+getUserLanguage();
	if ((pmv_typeClick) && (pmv_typeClick != "")) pmv_src += '&type='+escape(pmv_typeClick);
	//pmv_src += '&ref='+escape(pmv_rtu);
	pmv_src += '&flashversion='+pmv_fla_ver;

	return pmv_src;
}
// log action : pmv_typeClick = empty like a page, FILE ans in the futur RSS, PODCAST
function pmv_click (pmv_urlPmv, pmv_site, pmv_urlDoc, pmv_pname, pmv_typeClick, pmv_vars)
{
	var pmv_src = pmv_getUrlStat(pmv_urlPmv, pmv_site, pmv_urlDoc, pmv_pname, pmv_typeClick, pmv_vars);
	var pmv_img = new Image();
	pmv_img.src = pmv_src;
}
// Log current page
function pmv_log(pmv_urlPmv, pmv_site, pmv_pname, pmv_vars)
{
	var pmv_urlCur = pmv_do.location.href;
	var pmv_pos = pmv_urlCur.indexOf("//");
	if (pmv_pos > 0) {
		pmv_urlCur = pmv_urlCur.substr(pmv_pos);
	}
	var pmv_src = pmv_getUrlStat(pmv_urlPmv, pmv_site, pmv_urlCur, pmv_pname, "", pmv_vars);
	pmv_do.writeln('<img src="'+pmv_src+'" alt="visites" style="display:none;border:0" />');
}
pmv_log(visitesURL, visitesSite, pagename, a_vars);