//----------------------------------------
//Content            :Communication
//
//geturi             :������URL
//execution_f        :xmlHttpObject�������塢���֤��Ѥ�ä���¹Ԥ���Function(���ξ��ϡ��Хå�����ɤǤν���)
//did                :ʸ���ѹ�ID
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
  
  //execution_f���ɲä�����ʲ��ν������ɲä���
  if(xmlHttpObject){
	if(execution_f == 'displaychange'){
	  xmlHttpObject.onreadystatechange = displaychange;
	}else if(execution_f == 'moneydisplay'){
	  xmlHttpObject.onreadystatechange = moneydisplay;
	}
  }
  
//----------------------------------------------------------------------------------------------------
//�������鲼���ɲ�Function

  //ɽ���ѹ��ʥ����Ȥ��ɲá�
  function displaychange() {  
    if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){
      document.getElementById(did).innerHTML = "�����Ȥ��ɲä��ޤ���";
	  sendData('ajax.php?id=jk-shoppingcart','moneydisplay','jk-shoppingcart');
    } else {
      document.getElementById(did).innerHTML = "<b>NowLoading........</b>";
    }
  }
  
  //ɽ���ѹ��ʶ�ۡ�
  function moneydisplay() {
    if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){  
      document.getElementById(did).innerHTML = xmlHttpObject.responseText+'��';
	  cart_view('ajax.php?id=jk-list','in_cart','dis_clist');
    } else {
      document.getElementById(did).innerHTML = "------";
    }
  }

//�����ޤǤ��ɲ�Function
//----------------------------------------------------------------------------------------------------  
  
  if(xmlHttpObject){
    xmlHttpObject.open("GET",geturi,true);
    xmlHttpObject.send(null);
  }
}
