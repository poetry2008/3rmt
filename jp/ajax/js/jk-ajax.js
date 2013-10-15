//----------------------------------------
//Content            :Communication
//geturi             :Address URL
//execution_f        :The Function will be performed if a state changes after generation of xmlHttpObject (when it is empty, it is processed by backend)
//did                :To change the text value which is from ID
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
  
  //The following processings will be added if execution_f is added.
  if(xmlHttpObject){
	if(execution_f == 'displaychange'){
	  xmlHttpObject.onreadystatechange = displaychange;
	}else if(execution_f == 'moneydisplay'){
	  xmlHttpObject.onreadystatechange = moneydisplay;
	}
  }
  
//----------------------------------------------------------------------------------------------------
//Start adding Function

  //Display changing (add to cart）
  function displaychange() {  
    if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){
      document.getElementById(did).innerHTML = "カートに追加しました";
	  sendData('ajax.php?id=jk-shoppingcart','moneydisplay','jk-shoppingcart');
    } else {
      document.getElementById(did).innerHTML = "<b>NowLoading........</b>";
    }
  }
  
  //Display changing (amount)
  function moneydisplay() {
    if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){  
      document.getElementById(did).innerHTML = xmlHttpObject.responseText+'円';
	  cart_view('ajax.php?id=jk-list','in_cart','dis_clist');
    } else {
      document.getElementById(did).innerHTML = "------";
    }
  }

//The Ending of Function
//----------------------------------------------------------------------------------------------------  
  
  if(xmlHttpObject){
    xmlHttpObject.open("GET",geturi,true);
    xmlHttpObject.send(null);
  }
}
