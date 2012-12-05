//----------------------------------------  
//When scrolling window display (cart)
//----------------------------------------
  
  var vs = "0";
  window.onscroll = myMove;
  
  //----------function for ie----------
  function ietruebody(){
    return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
  }
   
  //----------a div displayed in the lower left corner----------
  function myMove(){
	var offSY = ietruebody().scrollTop; //(Y coordinates on the screen) scroll position
	
	if(document.all){
	  var offMY = ietruebody().clientHeight; //Window size
	}else{
	  var offMY = innerHeight;
	}
	
	var dsize = 250; //Image size (Y coordinates)
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
	  

    //----------Cross browser opacity setting function----------
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
  
    //----------Display for the first time？----------
	if(vs == 0){
	  vs = "1";

	  cart_view('ajax.php?id=jk-list','in_cart','dis_clist');
	}else{
	}
  
  }



//----------------------------------------
//Content            :Communication
//
//geturi             :URL destination
//execution_f        :After generating the xmlHttpObject, Function to run state has changed (if empty, the back-end processing)
//did                :ID character change
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
  
    //----------If added a execution_f,add the following process----------
    if(xmlHttpObject){
	  if(execution_f == 'in_cart'){
	    xmlHttpObject.onreadystatechange = in_cart;
	  }
    }
  
    //----------------------------------------------------------------------------------------------------
    //Add Function from here

    //----------View a list of the cart----------
    function in_cart(){
	  var offSY = ietruebody().scrollTop; //(Y coordinates on the screen) scroll position
	
	  if(document.all){
	    var offMY = ietruebody().clientHeight; //Window size
	  }else{
	    var offMY = innerHeight;
	  }
	
	  var dsize = 250; //Image size (Y coordinates)
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
	  
	  //----------Setting the function to move when scrolling----------
	  window.onscroll = myMove;
	  
	  //----------When you click Display Settings window (cart)----------
	  document.getElementById("dis_clist").onclick = null;

      //----------Cross browser opacity setting function----------
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
	  
	  //----------Send and receive----------
	  if((xmlHttpObject.readyState == 4) && (xmlHttpObject.status == 200)){
        document.getElementById(did).innerHTML = xmlHttpObject.responseText;
      } else {
        document.getElementById(did).innerHTML = "<p>&nbsp;</p><div align=\"center\" class=\"main\"><img src=\"images/design/loadingcircle.gif\" align=\"absmiddle\"> NowLoading........</div>"; 
      }
    }
  
    //Add function stop here
    //----------------------------------------------------------------------------------------------------  
  
    if(xmlHttpObject){
      xmlHttpObject.open("GET",geturi,true);
      xmlHttpObject.send(null);
    }
  }



//----------------------------------------  
//Window display (mini)
//----------------------------------------
  function cart_non(did) {
    //----------mini_div displayed in the lower left----------
    var miniSY = ietruebody().scrollTop; //(Y coordinates on the screen) scroll position
	
	if(document.all){
	  var miniMY = ietruebody().clientHeight; //Window size
	}else{
	  var miniMY = innerHeight;
	}
	
	var minisize = 40; //Image size (Y coordinates)
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
	
	//----------Setting the function to move when scrolling----------
	window.onscroll = miniMove;
  }

//----------------------------------------  
//When scrolling window display (mini)
//----------------------------------------
  function miniMove(){
    //----------a div displayed in the lower left corner----------
	var moveSY = ietruebody().scrollTop; //(Y coordinates on the screen) scroll position
	
	if(document.all){
	  var moveMY = ietruebody().clientHeight; //Window size
	}else{
	  var moveMY = innerHeight;
	}
	
	var movesize = 40; //Image size (Y coordinates)
	var moveTop = moveSY + moveMY - movesize;
	
    document.getElementById("dis_clist").style.top = moveTop + "px";
	document.getElementById("dis_clist").style.left = "30px";
    document.getElementById("dis_clist").style.position = "absolute";
    document.getElementById("dis_clist").style.zindex = "1";
    document.getElementById("dis_clist").style.width = "180px";
    document.getElementById("dis_clist").style.height = movesize + "px";
	document.getElementById("dis_clist").style.fontSize = "10px";
	document.getElementById("dis_clist").style.visibility = "visible";
	
	//----------When you click Display Settings window (cart)----------
	document.getElementById("dis_clist").onclick = function(){cart_view('ajax.php?id=jk-list','in_cart','dis_clist')};
  }