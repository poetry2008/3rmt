//----------------------------------------  
//����������˥�����ɥ���ɽ���ʥ����ȡ�
//----------------------------------------
  
  var vs = "0";
  window.onscroll = myMove;
  
  //----------ie��function----------
  function ietruebody(){
    return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
  }
   
  //----------div�򺸲���ɽ��----------
  function myMove(){
	var offSY = ietruebody().scrollTop; //����������֡ʲ��̾��y��ɸ��
	
	if(document.all){
	  var offMY = ietruebody().clientHeight; //������ɥ�������
	}else{
	  var offMY = innerHeight;
	}
	
	var dsize = 250; //������������y��ɸ��
	var offTop = offSY + offMY - dsize;
	
    document.getElementById("dis_clist").style.top = offTop + "px";
	document.getElementById("dis_clist").style.left = "30px";
    document.getElementById("dis_clist").style.position = "absolute";
    document.getElementById("dis_clist").style.zindex = "1";
    document.getElementById("dis_clist").style.width = "500px";
    document.getElementById("dis_clist").style.height = dsize + "px";
	document.getElementById("dis_clist").style.backgroundImage = "url(" + "./ajax/images/clist.gif" + ")";
	document.getElementById("dis_clist").style.fontSize = "10px";
	document.getElementById("dis_clist").style.visibility = "visible";
	  

    //----------�������饦����Ʃ��������ؿ�----------
	if(window.opera){
      return;
    }
  
    var ua =navigator.userAgent;
    var arg = 0.89;
  
    if(ua.indexOf('Safari') != -1 || ua.indexOf('KHTML') != -1){
      document.getElementById("dis_clist").style.opacity = arg
    }else if(document.all){ //win-e4,win-e5,win-e6
      document.all("dis_clist").style.filter = "alpha(opacity=0)";
	  document.all("dis_clist").filters.alpha.Opacity = (arg * 100);
    }else if(ua.indexOf('Gecko') != -1){  //n6,n7,m1
      document.getElementById("dis_clist").style.MozOpacity = arg;
    }
  
    //----------�����ܤ�ɽ����----------
	if(vs == 0){
	  vs = "1";
	  cart_view('ajax.php?id=jk-list','in_cart','dis_clist');
	}else{
	}
  
  }



//----------------------------------------
//Content            :Communication
//
//geturi             :������URL
//execution_f        :xmlHttpObject�������塢���֤��Ѥ�ä���¹Ԥ���Function(���ξ��ϡ��Хå�����ɤǤν���)
//did                :ʸ���ѹ�ID
//----------------------------------------
  function cart_view(geturi,execution_f,did) {
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
  
    //----------execution_f���ɲä�����ʲ��ν������ɲä���----------
    if(xmlHttpObject){
	  if(execution_f == 'in_cart'){
	    xmlHttpObject.onreadystatechange = in_cart;
	  }
    }
  
    //----------------------------------------------------------------------------------------------------
    //�������鲼���ɲ�Function

    //----------��������ꥹ�Ȥ�ɽ��----------
    function in_cart(){
	  var offSY = ietruebody().scrollTop; //����������֡ʲ��̾��y��ɸ��
	
	  if(document.all){
	    var offMY = ietruebody().clientHeight; //������ɥ�������
	  }else{
	    var offMY = innerHeight;
	  }
	
	  var dsize = 250; //������������y��ɸ��
	  var offTop = offSY + offMY - dsize;
	
      document.getElementById("dis_clist").style.top = offTop + "px";
	  document.getElementById("dis_clist").style.left = "30px";
      document.getElementById("dis_clist").style.position = "absolute";
      document.getElementById("dis_clist").style.zindex = "1";
      document.getElementById("dis_clist").style.width = "500px";
      document.getElementById("dis_clist").style.height = dsize + "px";
	  document.getElementById("dis_clist").style.backgroundImage = "url(" + "./ajax/images/clist.gif" + ")";
	  document.getElementById("dis_clist").style.fontSize = "10px";
	  document.getElementById("dis_clist").style.visibility = "visible";
	  
	  //----------�����������ư����function������----------
	  window.onscroll = myMove;
	  
	  //----------����å����˥�����ɥ���ɽ���ʥ����ȡ�����----------
	  document.getElementById("dis_clist").onclick = null;

      //----------�������饦����Ʃ��������ؿ�----------
	  if(window.opera){
        return;
      }
  
      var ua =navigator.userAgent;
      var arg = 0.89;
  
      if(ua.indexOf('Safari') != -1 || ua.indexOf('KHTML') != -1){
        document.getElementById("dis_clist").style.opacity = arg
      }else if(document.all){ //win-e4,win-e5,win-e6
        document.all("dis_clist").style.filter = "alpha(opacity=0)";
	    document.all("dis_clist").filters.alpha.Opacity = (arg * 100);
      }else if(ua.indexOf('Gecko') != -1){  //n6,n7,m1
        document.getElementById("dis_clist").style.MozOpacity = arg;
      }
	  
	  //----------������----------
	  if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){
        document.getElementById(did).innerHTML = xmlHttpObject.responseText;
      } else {
        document.getElementById(did).innerHTML = "<p>&nbsp;</p><div align=\"center\" class=\"main\"><img src=\"images/design/loadingcircle.gif\" align=\"absmiddle\"> NowLoading........</div>"; 
      }
    }
  
    //�����ޤǤ��ɲ�Function
    //----------------------------------------------------------------------------------------------------  
  
    if(xmlHttpObject){
      xmlHttpObject.open("GET",geturi,true);
      xmlHttpObject.send(null);
    }
  }



//----------------------------------------  
//������ɥ�(mini)��ɽ��
//----------------------------------------
  function cart_non(did) {
    //----------mini_div�򺸲���ɽ��----------
    var miniSY = ietruebody().scrollTop; //����������֡ʲ��̾��y��ɸ��
	
	if(document.all){
	  var miniMY = ietruebody().clientHeight; //������ɥ�������
	}else{
	  var miniMY = innerHeight;
	}
	
	var minisize = 40; //������������y��ɸ��
	var miniTop = miniSY + miniMY - minisize;
	
    document.getElementById(did).style.top = miniTop + "px";
	document.getElementById(did).style.left = "30px";
    document.getElementById(did).style.position = "absolute";
    document.getElementById(did).style.zindex = "1";
    document.getElementById(did).style.width = "180px";
    document.getElementById(did).style.height = minisize + "px";
	document.getElementById(did).style.backgroundImage = "url(" + "./ajax/images/min_clist.gif" + ")";
	document.getElementById(did).innerHTML = "";
	document.getElementById(did).style.visibility = "visible";
	
	//----------�����������ư����function������----------
	window.onscroll = miniMove;
  }

//----------------------------------------  
//����������˥�����ɥ���ɽ����mini��
//----------------------------------------
  function miniMove(){
    //----------div�򺸲���ɽ��----------
	var moveSY = ietruebody().scrollTop; //����������֡ʲ��̾��y��ɸ��
	
	if(document.all){
	  var moveMY = ietruebody().clientHeight; //������ɥ�������
	}else{
	  var moveMY = innerHeight;
	}
	
	var movesize = 40; //������������y��ɸ��
	var moveTop = moveSY + moveMY - movesize;
	
    document.getElementById("dis_clist").style.top = moveTop + "px";
	document.getElementById("dis_clist").style.left = "30px";
    document.getElementById("dis_clist").style.position = "absolute";
    document.getElementById("dis_clist").style.zindex = "1";
    document.getElementById("dis_clist").style.width = "180px";
    document.getElementById("dis_clist").style.height = movesize + "px";
	document.getElementById("dis_clist").style.fontSize = "10px";
	document.getElementById("dis_clist").style.visibility = "visible";
	
	//----------����å����˥�����ɥ���ɽ���ʥ����ȡ�����----------
	document.getElementById("dis_clist").onclick = function(){cart_view('ajax.php?id=jk-list','in_cart','dis_clist')};
  }