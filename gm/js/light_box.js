var image_list;
var title_list;
var now_index=0;
//create images
function fnCreate(src){
        
            image_list = getClass('input','large_image_input');
            title_list = getClass('img','carousel-image');
            var image_lenght = image_list.length;
            var show_title = 'image';
            var ClassName = "thumbviewbox";
        
            if(src == '')
            {
                return false;
            }
            
            for(var i=0;i<image_list.length;i++){
              if(image_list[i].value == src){
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
            
            em = document.getElementsByClassName(ClassName)
            
            for(var i=em.length-1; i>=0; i--){
                var p = em[i];
                p.parentNode.removeChild(p);
            }    
            
            var htmlWidth = window.innerWidth;        
            var htmlHeight = window.innerHeight;       
            var divleft = 0;                            
            var divtop =0;                              
            var allheight = document.body.scrollHeight 
            var closefunction = 'em=document.getElementsByClassName("'+ClassName+'");for(var i=em.length-1;i>=0;i--){var p=em[i];p.parentNode.removeChild(p);}';
            
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
            element_ground.setAttribute('style','position: absolute; top: 0; left: 0; z-index: 150;background-color: rgb(0, 0, 0); opacity: 0.8; width:100%; height: '+allheight+'px;');
            var element_boder = document.createElement('div');
            var img_parents = document.createElement('div');
            var img_parents_top = document.createElement('div');
            var img_parents_footer = document.createElement('div');
            var img_parents_center = document.createElement('div');
            var img_parents_loading = document.createElement('div');
            var img_parents_center_left = document.createElement('div');
            var img_parents_center_right = document.createElement('div');
            var img_parents_center_left_href = document.createElement('a');
            var img_parents_center_right_href = document.createElement('a');
            // set Attribute
            img_parents.setAttribute('id','img_parents_border');
            img_parents_loading.setAttribute('id','loading');
            img_parents_center_left_href.setAttribute('id','prevLink');
            img_parents_center_right_href.setAttribute('id','nextLink');
            img_parents_center_left.setAttribute('id','img_parents_border_left');
            img_parents_center_right.setAttribute('id','img_parents_border_right');
            img_parents_center_left_href.setAttribute('onclick','PrevImg();');
            img_parents_center_right_href.setAttribute('onclick','NextImg();');
            img_parents.setAttribute('style','width:'+(img.width+20)+'px;height:100%;margin: 0px auto; z-index: 149;background-color: rgb(255, 255, 255);');
            img_parents_top.setAttribute('style','width:'+(img.width+20)+'px;height:10px;margin: 0px auto; z-index: 149;background-color: rgb(255, 255, 255);');
            img_parents_footer.setAttribute('style','width:'+(img.width+20)+'px;height:10px;margin: 0px auto; z-index: 149;background-color: rgb(255, 255, 255);');
            img_parents_center.setAttribute('style','position:absolute;top:0;width:'+(img.width+20)+'px;height:100%;margin: 0px auto;');
            img_parents_center_left_href.setAttribute('style','height:'+(img.height+10)+'px;');
            img_parents_center_right_href.setAttribute('style','height:'+(img.height+10)+'px;');
            //create element
            element_boder.appendChild(img_parents_top);
            element_boder.appendChild(img_parents);
            element_boder.appendChild(img_parents_footer);
            img_parents.appendChild(img);
            img_parents.appendChild(img_parents_center);
            img_parents.appendChild(img_parents_loading);
            img_parents_center.appendChild(img_parents_center_left);
            img_parents_center.appendChild(img_parents_center_right);
            img_parents_center_left.appendChild(img_parents_center_left_href);
            img_parents_center_right.appendChild(img_parents_center_right_href);  
            element_boder.setAttribute('class',ClassName);
            element_boder.setAttribute('style','margin: 0 auto; line-height: 1.4em; overflow: auto; width: 100%;');
            var element = document.createElement('div');
            element.appendChild(element_boder);
            element.setAttribute('class',ClassName);
            element.setAttribute('style','width:100%;position:absolute;z-index:151;text-align:center;line-height:0;top:100px;');
            var element_title = document.createElement('div');
            var title =  document.createElement('div');
            var title_div = document.createElement('div');
            var title_boder = document.createElement('div');
            //page div
            title_page = '<div id="lightbox_title" style="float:left;margin-left:10px;"><small>page '+now_index+' of '+image_lenght+'</small></div>'
            //title div
            title_close = '<div style="float:right;margin-right:10px;" onclick=\'em=document.getElementsByClassName("'+ClassName+'");for(var i=em.length-1;i>=0;i--){var p=em[i];p.parentNode.removeChild(p);}\'><a href="javascript:void(0);"><img src="images/closelabel.gif"></a></div>';
            title_text = '<div id="lightbox_title_text" style="width:'+(imgwd+20)+'px;background-color: rgb(255, 255, 255);"><br><small style="margin-left:10px;margin-right:10px;">'+show_title+'</small></div>';
            //close div
            title.innerHTML = title_page+title_close+title_text;
            title_boder.appendChild(title);
            title_boder.setAttribute('class',ClassName);
            title_boder.setAttribute('style','width:'+(imgwd+20)+'px;height:22px;margin: 0px auto; z-index: 151;background-color: rgb(255, 255, 255);');
            title_div.appendChild(title_boder);
            title_div.setAttribute('class',ClassName);
            title_div.setAttribute('style','width:100%;position:absolute;z-index:152;text-align:center;');
            element_title.setAttribute('onclick','void(0)');

            element_title.appendChild(title_div);
            element_title.setAttribute('class',ClassName);
            element_title.setAttribute('id','light_box_title_boder');
            element_title.setAttribute('style','width:100%;z-index:151;text-align:center;');
                
            element_boder.appendChild(element_title);
            document.body.appendChild(element_ground);
            document.body.appendChild(element);
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
    light_box_img = document.getElementById('large_image_show');
    light_box_img.src = image_list[now_index].value;
    light_box_title_boder = document.getElementById('light_box_title_boder');
    light_box_title_boder.style.top = '';
    light_box_title_boder.style.top = (light_box_img.height + 100)+'px';
    light_box_title_text = document.getElementById('lightbox_title_text');
    light_box_title_text.innerHTML = '<br><small style="margin-left:10px;margin-right:10px;">'+title_list[now_index].alt+'</small>';
    light_box_title = document.getElementById('lightbox_title');
    light_box_title.innerHTML = '<small>page '+(now_index+1)+' of '+image_list.length+'</small>';
    now_index = now_index+1;
    image_lenght = image_list.length;
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
    document.getElementById("large_image_show").style.opacity = 0;
    document.getElementById("loading").style.display = 'block';
    setTimeout(function(){
      document.getElementById("large_image_show").style.opacity = 1;
      document.getElementById("loading").style.display = 'none'; 
    },500);
  }
}
//prev images
function PrevImg(){
  if(now_index>1){
    light_box_img = document.getElementById('large_image_show');
    light_box_img.src = image_list[now_index-2].value;
    light_box_title_boder = document.getElementById('light_box_title_boder');
    light_box_title_boder.style.top = '';
    light_box_title_boder.style.top = (light_box_img.height + 100)+'px';
    light_box_title_text = document.getElementById('lightbox_title_text');
    light_box_title_text.innerHTML = '<br><small style="margin-left:10px;margin-right:10px;">'+title_list[now_index-2].alt+'</small>';
    light_box_title = document.getElementById('lightbox_title');
    light_box_title.innerHTML = '<small>page '+(now_index-1)+' of '+image_list.length+'</small>';
    now_index = now_index-1;
    image_lenght = image_list.length;
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
    document.getElementById("large_image_show").style.opacity = 0;
    document.getElementById("loading").style.display = 'block';
    setTimeout(function(){
      document.getElementById("large_image_show").style.opacity = 1;
      document.getElementById("loading").style.display = 'none'; 
    },500);
  }
}
//get class
function getClass(tagname, className) { 
if (document.getElementsByClassName) {
return document.getElementsByClassName(className);
}
else {    
var tagname = document.getElementsByTagName_r(tagname);  
var tagnameAll = [];     
for (var i = 0; i < tagname.length; i++) {     
if (tagname[i].className == className) {     
tagnameAll[tagnameAll.length] = tagname[i];
}
}
return tagnameAll;
}
}
