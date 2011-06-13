// JavaScript Document
function sendData(geturi,execution_f,did) {
  xmlHttpObject = null;
  if(window.XMLHttpRequest) {
    xmlHttpObject = new XMLHttpRequest();
  }else if(window.ActiveXObject) {
    try{
	  xmlHttpObject = new ActiveXObject("Msxml2.XMLHTTP");
	}catch(e){
	  try{
	    xmlHttpObject = new ActiveXObject("Microsoft.XMLHTTPD");
	  }catch(e){
	    return null;
	  }
	}
  }
  
  //execution_fを追加したら以下の処理を追加する
  if(xmlHttpObject){
	if(execution_f == 'tabmenu2'){
	  xmlHttpObject.onreadystatechange = tabmenu2;
	}
  }
  
  
//----------------------------------------------------------------------------------------------------
//ここから下は追加Function

  //表示変更（tab_menu2）
  function tabmenu2() {
    if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){  
      document.getElementById(did).innerHTML = xmlHttpObject.responseText;
    } else {
      document.getElementById(did).innerHTML = "<div align=\"center\" class=\"main\"><img src=\"images/design/loadingcircle.gif\" align=\"absmiddle\"> NowLoading........</div>";
    }
  }

//ここまでが追加Function
//----------------------------------------------------------------------------------------------------  
  
  if(xmlHttpObject){
    xmlHttpObject.open("GET",geturi,true);
    xmlHttpObject.send(null);
  }
}





function btchange(tid){
  if(tid == 't1'){
    document.getElementById('t_oimg1').style.display = "block";
    document.getElementById('t_img1').style.display = "none";	
	document.getElementById('t_oimg2').style.display = "none";
    document.getElementById('t_img2').style.display = "block";	
	document.getElementById('t_oimg3').style.display = "none";
    document.getElementById('t_img3').style.display = "block";	
	document.getElementById('t_oimg4').style.display = "none";
    document.getElementById('t_img4').style.display = "block";
	  
	sendData('ajax.php?id=tab_ln','tabmenu2','tabtable');
	return false;
	  
  }else if(tid == 't2'){
	document.getElementById('t_oimg1').style.display = "none";
    document.getElementById('t_img1').style.display = "block";	
	document.getElementById('t_oimg2').style.display = "block";
    document.getElementById('t_img2').style.display = "none";	
	document.getElementById('t_oimg3').style.display = "none";
    document.getElementById('t_img3').style.display = "block";	
	document.getElementById('t_oimg4').style.display = "none";
    document.getElementById('t_img4').style.display = "block";
	  
	sendData('ajax.php?id=tab_wn','tabmenu2','tabtable');
	return false;
	  
  }else if(tid == 't3'){
	document.getElementById('t_oimg1').style.display = "none";
    document.getElementById('t_img1').style.display = "block";	
	document.getElementById('t_oimg2').style.display = "none";
    document.getElementById('t_img2').style.display = "block";	
	document.getElementById('t_oimg3').style.display = "block";
    document.getElementById('t_img3').style.display = "none";	
	document.getElementById('t_oimg4').style.display = "none";
    document.getElementById('t_img4').style.display = "block";
	  
	sendData('ajax.php?id=tab_rv','tabmenu2','tabtable');
	return false;
	
  }else if(tid == 't4'){
	document.getElementById('t_oimg1').style.display = "none";
    document.getElementById('t_img1').style.display = "block";	
	document.getElementById('t_oimg2').style.display = "none";
    document.getElementById('t_img2').style.display = "block";	
	document.getElementById('t_oimg3').style.display = "none";
    document.getElementById('t_img3').style.display = "block";	
	document.getElementById('t_oimg4').style.display = "block";
    document.getElementById('t_img4').style.display = "none";
	
	sendData('ajax.php?id=tab_rc','tabmenu2','tabtable');
	return false;

  }else if(tid == 't5'){	
	sendData('ajax.php?id=po_m','tabmenu2','tabtable');
	return false;
  }

}

function out_display(){
  document.getElementById('category_list').style.display = "none";
  return true ;
}
