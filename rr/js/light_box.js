<script>
function fnCreate(src){
        
            var ClassName = "thumbviewbox";
        
            if(src == '')
            {
                return false;
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
            element_ground.setAttribute('onclick',closefunction);
            var element_boder = document.createElement('div');
            element_boder.appendChild(img);
            element_boder.setAttribute('class',ClassName);
            element_boder.setAttribute('style','margin: 0 auto; line-height: 1.4em; overflow: auto; width: 100%; padding: 0 5px 0;');
            element_boder.setAttribute('onclick',closefunction);
            var element = document.createElement('div');
            element.appendChild(element_boder);
            element.setAttribute('class',ClassName);
            element.setAttribute('style','width:100%;position:absolute;z-index:151;text-align:center;line-height:0;top:100px;z-index:151;');
                
            document.body.appendChild(element_ground);
            document.body.appendChild(element);
    }
    </script>
