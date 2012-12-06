//----------------------------------------
//Content            :Communication
//
//geturi             :URL destination
//execution_f        :After generating the xmlHttpObject, Function to run state has changed (if empty, the back-end processing)
//did                :ID character change
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
  
  //If added a execution_f,add the following process
  if(xmlHttpObject){
	if(execution_f == 'displaychange'){
	  xmlHttpObject.onreadystatechange = displaychange;
	}else if(execution_f == 'moneydisplay'){
	  xmlHttpObject.onreadystatechange = moneydisplay;
	}
  }
  
//----------------------------------------------------------------------------------------------------
//Add Function from here

  //Display change (Add to cart)
  function displaychange() {  
    if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){
      document.getElementById(did).innerHTML = "カートに追加しました";
	  sendData('ajax.php?id=jk-shoppingcart','moneydisplay','jk-shoppingcart');
    } else {
      document.getElementById(did).innerHTML = "<b>NowLoading........</b>";
    }
  }
  
  //Display change (Price)
  function moneydisplay() {
    if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){  
      document.getElementById(did).innerHTML = xmlHttpObject.responseText+'円';
	  cart_view('ajax.php?id=jk-list','in_cart','dis_clist');
    } else {
      document.getElementById(did).innerHTML = "------";
    }
  }

//Add function stop here
//----------------------------------------------------------------------------------------------------  
  
  if(xmlHttpObject){
    xmlHttpObject.open("GET",geturi,true);
    xmlHttpObject.send(null);
  }
}
