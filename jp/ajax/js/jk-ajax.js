//----------------------------------------
//Content            :Communication
//
//geturi             :送信先URL
//execution_f        :xmlHttpObjectの生成後、状態が変わったら実行するFunction(空の場合は、バックエンドでの処理)
//did                :文字変更ID
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
  
  //execution_fを追加したら以下の処理を追加する
  if(xmlHttpObject){
	if(execution_f == 'displaychange'){
	  xmlHttpObject.onreadystatechange = displaychange;
	}else if(execution_f == 'moneydisplay'){
	  xmlHttpObject.onreadystatechange = moneydisplay;
	}
  }
  
//----------------------------------------------------------------------------------------------------
//ここから下は追加Function

  //表示変更（カートに追加）
  function displaychange() {  
    if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){
      document.getElementById(did).innerHTML = "カートに追加しました";
	  sendData('ajax.php?id=jk-shoppingcart','moneydisplay','jk-shoppingcart');
    } else {
      document.getElementById(did).innerHTML = "<b>NowLoading........</b>";
    }
  }
  
  //表示変更（金額）
  function moneydisplay() {
    if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){  
      document.getElementById(did).innerHTML = xmlHttpObject.responseText+'円';
	  cart_view('ajax.php?id=jk-list','in_cart','dis_clist');
    } else {
      document.getElementById(did).innerHTML = "------";
    }
  }

//ここまでが追加Function
//----------------------------------------------------------------------------------------------------  
  
  if(xmlHttpObject){
    xmlHttpObject.open("GET",geturi,true);
    xmlHttpObject.send(null);
  }
}
