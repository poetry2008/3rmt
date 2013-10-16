//----------------------------------------  
//The display of window when scrolling   
//----------------------------------------
  
  var vs = "0";
  window.onscroll = myMove;
  
  //----------Function for IE----------
  function ietruebody(){
    return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
  }
  //----------Put div in the bottom left display----------
  function myMove(){
	var offSY = ietruebody().scrollTop; //Scroll position (y coordinates on a screen)
	
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
	  

        //----------The Function to set the cross browser opacity----------
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
  
    //----------The first display ?----------
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
//execution_f        :The Function will be performed if a state changes after generation of xmlHttpObject (when it is empty, it is processed by a backend)
//did                : Character change ID
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
  
    //----------The following processings will be added if execution_f is added. ----------
    if(xmlHttpObject){
	  if(execution_f == 'in_cart'){
	    xmlHttpObject.onreadystatechange = in_cart;
	  }
    }
  
    //----------------------------------------------------------------------------------------------------
    //Start to add the Function

    //----------The display of the list in a cart ----------
    function in_cart(){
	  var offSY = ietruebody().scrollTop; //Scroll position (y coordinates on a screen) 
	
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
	  
          //----------The setting to perform the function when  click on scroll ----------
	  window.onscroll = myMove;
	  
          //----------The setting to display window (cart) when  click  on it ----------
	  document.getElementById("dis_clist").onclick = null;

          //----------The Function to set the opacity of  crossing the browser----------
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
//The display of mini window
//----------------------------------------
  function cart_non(did) {
    //----------Didplay  mini_div in the left bottom----------
    var miniSY = ietruebody().scrollTop; // Scroll position (Y coordinates on the screen） 
	
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
	
        //----------The setting to perform the function when scrolling ----------
	window.onscroll = miniMove;
  }

//----------------------------------------  
//To display window (mini) when scrolling
//----------------------------------------
  function miniMove(){
    //----------Display div in the left bottom----------
	var moveSY = ietruebody().scrollTop; // Scroll position (Y coordinates on the screen）
	
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
	
        //----------The setting to display window (cart) when click on it----------
	document.getElementById("dis_clist").onclick = function(){cart_view('ajax.php?id=jk-list','in_cart','dis_clist')};
  }
