//----------------------------------------  
//When the scroll bar appears, window display (cart)
//----------------------------------------
  
  var vs = "0";
  window.onscroll = myMove;
  
  //----------For IE function----------
  function ietruebody(){
    return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
  }
  //----------Put div in the bottom left display----------
  function myMove(){
	var offSY = ietruebody().scrollTop; //The scroll position (Y coordinate on the screen）
	
	if(document.all){
	  var offMY = ietruebody().clientHeight; //Window size
	}else{
	  var offMY = innerHeight;
	}
	
	var dsize = 250; //Image size(Y coordinate)
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
	  

        //----------Cross browserOpacity set the function----------
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
  
    //----------The first display----------
	if(vs == 0){
	  vs = "1";
	  cart_view('ajax.php?id=jk-list','in_cart','dis_clist');
	}else{
	}
  
  }



//----------------------------------------
//Content            :Communication
//
//geturi             :Address URL
//execution_f        :After generating xmlHttpObject, if the state is changed, then use this Function(When null values, background processing)
//did                :The ID of Changed text
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
  
    //----------After adding execution_f, do the following----------
    if(xmlHttpObject){
	  if(execution_f == 'in_cart'){
	    xmlHttpObject.onreadystatechange = in_cart;
	  }
    }
  
    //----------------------------------------------------------------------------------------------------
    //Start adding Function

    //----------In the shopping cart display list----------
    function in_cart(){
	  var offSY = ietruebody().scrollTop; //The scroll position (Y coordinates on the screen）
	
	  if(document.all){
	    var offMY = ietruebody().clientHeight; //Window size
	  }else{
	    var offMY = innerHeight;
	  }
	
	  var dsize = 250; //Image size(Y coordinate)
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
	  
	  //----------Set function when the scroll bar appears----------
	  window.onscroll = myMove;
	  
	  //----------When you click on it，window display (cart)----------
	  document.getElementById("dis_clist").onclick = null;

      //----------Cross browserOpacity set the function----------
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
	  
	  //----------Send/Receive mail----------
	  if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){
        document.getElementById(did).innerHTML = xmlHttpObject.responseText;
      } else {
        document.getElementById(did).innerHTML = "<p>&nbsp;</p><div align=\"center\" class=\"main\"><img src=\"images/design/loadingcircle.gif\" align=\"absmiddle\"> NowLoading........</div>"; 
      }
    }
  
    //End adding Function
    //----------------------------------------------------------------------------------------------------  
  
    if(xmlHttpObject){
      xmlHttpObject.open("GET",geturi,true);
      xmlHttpObject.send(null);
    }
  }



//----------------------------------------  
//Mini window display
//----------------------------------------
  function cart_non(did) {
    //----------Put mini_div in the bottom left display----------
    var miniSY = ietruebody().scrollTop; //The scroll position (Y coordinates on the screen）
	
	if(document.all){
	  var miniMY = ietruebody().clientHeight; //Window size
	}else{
	  var miniMY = innerHeight;
	}
	
	var minisize = 40; //Image size(Y coordinate)
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
	
	//----------Set function when the scroll bar appears----------
	window.onscroll = miniMove;
  }

//----------------------------------------  
//When the scroll bar appears, window display(mini)
//----------------------------------------
  function miniMove(){
    //----------In the left display div----------
	var moveSY = ietruebody().scrollTop; //The scroll position (Y coordinates on the screen）
	
	if(document.all){
	  var moveMY = ietruebody().clientHeight; //Window size
	}else{
	  var moveMY = innerHeight;
	}
	
	var movesize = 40; //Image size(Y coordinate)
	var moveTop = moveSY + moveMY - movesize;
	
    document.getElementById("dis_clist").style.top = moveTop + "px";
	document.getElementById("dis_clist").style.left = "30px";
    document.getElementById("dis_clist").style.position = "absolute";
    document.getElementById("dis_clist").style.zindex = "1";
    document.getElementById("dis_clist").style.width = "180px";
    document.getElementById("dis_clist").style.height = movesize + "px";
	document.getElementById("dis_clist").style.fontSize = "10px";
	document.getElementById("dis_clist").style.visibility = "visible";
	
	//----------When you click on it，window display (cart)----------
	document.getElementById("dis_clist").onclick = function(){cart_view('ajax.php?id=jk-list','in_cart','dis_clist')};
  }
