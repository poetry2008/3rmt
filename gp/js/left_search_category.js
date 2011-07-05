var http_request = false;
function send_request(url){
  http_request = false;
  if(window.XMLHttpRequest){
    http_request = new XMLHttpRequest();
    if(http_request.overrideMimeType){
      http_request.overrideMimeType("text/xml");
    }
  } else if(window.ActiveXObject){
    try{
      http_request = new ActiveXObject("Msxml2.XMLHttp");
    }catch(e){
      try{
        http_request = new ActiveXobject("Microsoft.XMLHttp");
      }catch(e){}
    }
  }
  
  if(!http_request){
    return false;
  }
   
  http_request.onreadystatechange = process_request;
  http_request.open("POST", url, true);
  http_request.send(null);
}

function process_request(){
  if(http_request.readyState == 4){
    if(http_request.status == 200){
       document.getElementById("leftca").innerHTML = http_request.responseText;    
       document.getElementById("leftca").style.display = "block";    
    } else{
      return false; 
    }
  }
}

function left_search_category(url){
  send_request(url);
}

function close_left_category() {
  document.getElementById("leftca").style.display = "none";    
}
