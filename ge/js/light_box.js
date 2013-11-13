var image_list;
var title_list;
var now_index=0;
var close_array = Array('element_ground','element_boder','title_boder','element','title_div','element_title');
var em_close;
//create images
function fnCreate(src,num){
       
            //get images info
            image_list = getClass('input','large_image_input');
            title_list = getClass('img','image_alt_list');
            var image_lenght = image_list.length;
            if(title_list[0]){
              var show_title = title_list[0].alt;
            }
            var ClassName = "thumbviewbox";
        
            if(src == '')
            {
                return false;
            }
            
            for(var i=0;i<image_list.length;i++){
              if(i == num){
                show_title = title_list[i].alt; 
                now_index = i+1;
              }
            }
            var img = document.createElement('img');
            img.setAttribute('src',src);
            var imgwd = img.width;
            var imghg = img.height;
            
            if(imgwd<1)
            {
                var timer = setTimeout("fnCreate('"+src+"')",100);
                return false;
            }else{
                clearInterval(timer);
            }
           
            if(document.getElementsByClassName){
              em = document.getElementsByClassName(ClassName)
            
              for(var i=em.length-1; i>=0; i--){
                var p = em[i];
                p.parentNode.removeChild(p);
              }    
            }
            
            var htmlWidth = window.innerWidth;        
            var htmlHeight = window.innerHeight;       
            var divleft = 0;                            
            var divtop =0;                              
            var allheight = document.body.scrollHeight  
            var closefunction = 'for(x in close_array){if(document.getElementById(close_array[x]+"_close")){var em_close=document.getElementById(close_array[x]+"_close");em_close.parentNode.removeChild(em_close);}}';
            
            img.setAttribute('id','large_image_show');
            
            if(imgwd>htmlWidth*0.8)
            {
                img.setAttribute('width',htmlWidth*0.8);
                divleft=htmlWidth*0.1;
                if(imghg>htmlHeight*0.8)
                {
                    divtop = htmlHeight*0.1;
                }else{
                    divtop = (htmlHeight-imgwd)/2;
                }
            }else{
                img.setAttribute('width',imgwd);
                divleft= (htmlWidth-imgwd)/2;
                if(imghg>htmlHeight*0.8)
                {
                    divtop = htmlHeight*0.1;
                }
                else
                {
                    divtop = (htmlHeight-imgwd)/2;
                }
            }
    
            var element_ground = document.createElement('div');
            element_ground.setAttribute('class',ClassName);
            element_ground.setAttribute('id','element_ground_close');
            element_ground.style.cssText = 'position: absolute; top: 0px; left: 0; z-index: 150;background-color: rgb(0, 0, 0); opacity: 0.8; width:100%; height: '+allheight+'px;';
            element_ground.style.filter="alpha(opacity=80)";
            element_ground.setAttribute('onclick',closefunction);
            var element_boder = document.createElement('div');
            var img_parents = document.createElement('div');
            var img_parents_top = document.createElement('div');
            var img_parents_footer = document.createElement('div');
            var img_parents_center = document.createElement('div');
            var img_parents_loading = document.createElement('div');
            var img_parents_center_left = document.createElement('div');
            var img_parents_center_right = document.createElement('div');
            // set Attribute
            img_parents.setAttribute('id','img_parents_border');
            img_parents_loading.setAttribute('id','loading');
            img_parents_center_left.setAttribute('id','img_parents_border_left');
            img_parents_center_right.setAttribute('id','img_parents_border_right');
            img_parents.style.cssText = 'width:'+(img.width+20)+'px;height:100%;margin: 0px auto; z-index: 149;background-color: rgb(255, 255, 255);';
            img_parents_top.style.cssText = 'width:'+(img.width+20)+'px;height:10px;margin: 0px auto; z-index: 149;background-color: rgb(255, 255, 255);';
            img_parents_footer.style.cssText = 'width:'+(img.width+20)+'px;height:10px;margin: 0px auto; z-index: 149;background-color: rgb(255, 255, 255);';
            img_parents_center.style.cssText = 'position:absolute;top:0;width:'+(img.width+20)+'px;height:100%;margin: 0px auto;';
            //create element
            element_boder.appendChild(img_parents_top);
            element_boder.appendChild(img_parents);
            element_boder.appendChild(img_parents_footer);
            img_parents.appendChild(img);
            img_parents.appendChild(img_parents_center);
            img_parents.appendChild(img_parents_loading);
            img_parents_center_right.innerHTML = '<a id="nextLink" onclick="NextImg();" href="javascript:void(0);"></a>';
            img_parents_center_left.innerHTML = '<a id="prevLink" onclick="PrevImg();" href="javascript:void(0);"></a>';
            img_parents_center.appendChild(img_parents_center_left);
            img_parents_center.appendChild(img_parents_center_right);
            element_boder.setAttribute('class',ClassName);
            element_boder.setAttribute('id','element_boder_close');
            element_boder.style.cssText = 'margin: 0 auto; line-height: 1.4em;width: 100%;';
            var element = document.createElement('div');
            element.appendChild(element_boder);
            element.setAttribute('class',ClassName);
            element.setAttribute('id','element_close');
            element.style.cssText = 'width:100%;position:absolute;z-index:151;text-align:center;line-height:0;top:100px;';
            var element_title = document.createElement('div');
            var title =  document.createElement('div');
            var title_div = document.createElement('div');
            var title_boder = document.createElement('div');
            //title div
            title_close = '<div style="position: relative;z-index: 150;height:26px;background-color: rgb(255, 255, 255);">&nbsp;<div style="float:right;background-color: rgb(255, 255, 255);" onclick=\''+closefunction+'\'><a href="javascript:void(0);"><img src="images/close.gif"></a></div></div>';
            title_text = '<div id="lightbox_title_text" style="width:'+(imgwd+20)+'px;background-color: rgb(255, 255, 255);"><font color="#656565"><b>'+show_title+'('+now_index+'/'+image_lenght+')</b></font></div>';

            if(now_index == 0){title_text = '';}
            //close div
            title.innerHTML = title_text+title_close;
            title_boder.appendChild(title);
            title_boder.setAttribute('class',ClassName);
            title_boder.setAttribute('id','title_boder_close');
            title_boder.style.cssText = 'width:'+(imgwd+20)+'px;height:22px;margin: 0px auto; z-index: 151;background-color: rgb(255, 255, 255);';
            title_div.appendChild(title_boder);
            title_div.setAttribute('class',ClassName);
            title_div.setAttribute('id','title_div_close');
            title_div.style.cssText = 'z-index:152;text-align:center;';
            element_title.setAttribute('onclick','void(0)');

            element_title.appendChild(title_div);
            element_title.setAttribute('class',ClassName);
            element_title.setAttribute('id','element_title_close');
            element_title.setAttribute('id','light_box_title_boder');
            element_title.style.cssText = 'width:100%;z-index:151;text-align:center;';
                
            element_boder.appendChild(element_title);
            document.body.appendChild(element_ground);
            document.body.appendChild(element);
            document.getElementById("nextLink").style.height = (img.height+20)+'px';
            document.getElementById("prevLink").style.height = (img.height+20)+'px';
            if(now_index > 1){
           
              document.getElementById("img_parents_border_left").style.display = "block";
            }else{
              
              document.getElementById("img_parents_border_left").style.display = "none";
            }
            if(now_index < image_lenght){
           
              document.getElementById("img_parents_border_right").style.display = "block";
            }else{
              
              document.getElementById("img_parents_border_right").style.display = "none";
            }
            //loading images
            document.getElementById("loading").innerHTML = '<img src="images/loading.gif">';
            document.getElementById("large_image_show").style.opacity = 0;
            setTimeout(function(){
              document.getElementById("large_image_show").style.opacity = 1;
              document.getElementById("loading").style.display = 'none'; 
            },1000);
            
    }
//next images
function NextImg(){
  if(now_index<image_list.length){
    var div_close = getClass("div","thumbviewbox");
    if(div_close.length == 0){ 
      for(x in close_array){
        if(document.getElementById(close_array[x]+"_close") && close_array[x] != 'element_ground'){
          var em_close=document.getElementById(close_array[x]+"_close");
          em_close.parentNode.removeChild(em_close);
        }
      }
    }
    fnCreate(image_list[now_index].value,now_index); 
    if(div_close.length == 0){
      var em_close=document.getElementById("element_ground_close");
      em_close.parentNode.removeChild(em_close); 
    }
  }
}
//prev images
function PrevImg(){
  if(now_index>1){
    var div_close = getClass("div","thumbviewbox");
    if(div_close.length == 0){ 
      for(x in close_array){
        if(document.getElementById(close_array[x]+"_close") && close_array[x] != 'element_ground'){
          var em_close=document.getElementById(close_array[x]+"_close");
          em_close.parentNode.removeChild(em_close);
        }
      }
    }  
    fnCreate(image_list[now_index-2].value,now_index-2); 
    if(div_close.length == 0){
      var em_close=document.getElementById("element_ground_close");
      em_close.parentNode.removeChild(em_close); 
    }
  }
}
//get class
function getClass(tagname, className) { 
  if (document.getElementsByClassName) {
    return document.getElementsByClassName(className);
  }else {    
    var tagname = document.getElementsByTagName(tagname);  
    var tagnameAll = [];     
    for (var i = 0; i < tagname.length; i++) {     
      if (tagname[i].className == className) {     
        tagnameAll[tagnameAll.length] = tagname[i];
      }
    }
    return tagnameAll;
  }
}
