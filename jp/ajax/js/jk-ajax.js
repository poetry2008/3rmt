//----------------------------------------
//Content            :Communication
//geturi             :Address URL
//execution_f        :After generating xmlHttpObject, if the state is changed, then use this Function(When null values, background processing)
//did                :The ID of Changed text
//----------------------------------------
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
  
  //After adding execution_f, do the following
  if(xmlHttpObject){
	if(execution_f == 'displaychange'){
	  xmlHttpObject.onreadystatechange = displaychange;
	}else if(execution_f == 'moneydisplay'){
	  xmlHttpObject.onreadystatechange = moneydisplay;
	}
  }
  
//----------------------------------------------------------------------------------------------------
//Start adding Function

  //The display is changed (add to cart）
  function displaychange() {  
    if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){
      document.getElementById(did).innerHTML = "カートに追加しました";
	  sendData('ajax.php?id=jk-shoppingcart','moneydisplay','jk-shoppingcart');
    } else {
      document.getElementById(did).innerHTML = "<b>NowLoading........</b>";
    }
  }
  
  //The display is changed (amount)
  function moneydisplay() {
    if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){  
      document.getElementById(did).innerHTML = xmlHttpObject.responseText+'円';
	  cart_view('ajax.php?id=jk-list','in_cart','dis_clist');
    } else {
      document.getElementById(did).innerHTML = "------";
    }
  }

//End adding Function
//----------------------------------------------------------------------------------------------------  
  
  if(xmlHttpObject){
    xmlHttpObject.open("GET",geturi,true);
    xmlHttpObject.send(null);
  }
}
