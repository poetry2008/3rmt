//----------------------------------------  
//スクロール時にウィンドウの表示（カート）
//----------------------------------------
  
  var vs = "0";
  window.onscroll = myMove;
  
  //----------ie用function----------
  function ietruebody(){
    return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
  }
   
  //----------divを左下に表示----------
  function myMove(){
	var offSY = ietruebody().scrollTop; //スクロール位置（画面上のy座標）
	
	if(document.all){
	  var offMY = ietruebody().clientHeight; //ウインドウサイズ
	}else{
	  var offMY = innerHeight;
	}
	
	var dsize = 250; //画像サイズ（y座標）
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
	  

    //----------クロスウラウザ不透明度設定関数----------
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
  
    //----------１回目の表示？----------
	if(vs == 0){
	  vs = "1";
	  cart_view('ajax.php?id=jk-list','in_cart','dis_clist');
	}else{
	}
  
  }



//----------------------------------------
//Content            :Communication
//
//geturi             :送信先URL
//execution_f        :xmlHttpObjectの生成後、状態が変わったら実行するFunction(空の場合は、バックエンドでの処理)
//did                :文字変更ID
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
  
    //----------execution_fを追加したら以下の処理を追加する----------
    if(xmlHttpObject){
	  if(execution_f == 'in_cart'){
	    xmlHttpObject.onreadystatechange = in_cart;
	  }
    }
  
    //----------------------------------------------------------------------------------------------------
    //ここから下は追加Function

    //----------カート内リストの表示----------
    function in_cart(){
	  var offSY = ietruebody().scrollTop; //スクロール位置（画面上のy座標）
	
	  if(document.all){
	    var offMY = ietruebody().clientHeight; //ウインドウサイズ
	  }else{
	    var offMY = innerHeight;
	  }
	
	  var dsize = 250; //画像サイズ（y座標）
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
	  
	  //----------スクロール時に動かすfunctionの設定----------
	  window.onscroll = myMove;
	  
	  //----------クリック時にウィンドウの表示（カート）設定----------
	  document.getElementById("dis_clist").onclick = null;

      //----------クロスウラウザ不透明度設定関数----------
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
	  
	  //----------送受信----------
	  if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){
        document.getElementById(did).innerHTML = xmlHttpObject.responseText;
      } else {
        document.getElementById(did).innerHTML = "<p>&nbsp;</p><div align=\"center\" class=\"main\"><img src=\"images/design/loadingcircle.gif\" align=\"absmiddle\"> NowLoading........</div>"; 
      }
    }
  
    //ここまでが追加Function
    //----------------------------------------------------------------------------------------------------  
  
    if(xmlHttpObject){
      xmlHttpObject.open("GET",geturi,true);
      xmlHttpObject.send(null);
    }
  }



//----------------------------------------  
//ウィンドウ(mini)の表示
//----------------------------------------
  function cart_non(did) {
    //----------mini_divを左下に表示----------
    var miniSY = ietruebody().scrollTop; //スクロール位置（画面上のy座標）
	
	if(document.all){
	  var miniMY = ietruebody().clientHeight; //ウインドウサイズ
	}else{
	  var miniMY = innerHeight;
	}
	
	var minisize = 40; //画像サイズ（y座標）
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
	
	//----------スクロール時に動かすfunctionの設定----------
	window.onscroll = miniMove;
  }

//----------------------------------------  
//スクロール時にウィンドウの表示（mini）
//----------------------------------------
  function miniMove(){
    //----------divを左下に表示----------
	var moveSY = ietruebody().scrollTop; //スクロール位置（画面上のy座標）
	
	if(document.all){
	  var moveMY = ietruebody().clientHeight; //ウインドウサイズ
	}else{
	  var moveMY = innerHeight;
	}
	
	var movesize = 40; //画像サイズ（y座標）
	var moveTop = moveSY + moveMY - movesize;
	
    document.getElementById("dis_clist").style.top = moveTop + "px";
	document.getElementById("dis_clist").style.left = "30px";
    document.getElementById("dis_clist").style.position = "absolute";
    document.getElementById("dis_clist").style.zindex = "1";
    document.getElementById("dis_clist").style.width = "180px";
    document.getElementById("dis_clist").style.height = movesize + "px";
	document.getElementById("dis_clist").style.fontSize = "10px";
	document.getElementById("dis_clist").style.visibility = "visible";
	
	//----------クリック時にウィンドウの表示（カート）設定----------
	document.getElementById("dis_clist").onclick = function(){cart_view('ajax.php?id=jk-list','in_cart','dis_clist')};
  }