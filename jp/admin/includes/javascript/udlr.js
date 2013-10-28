function getPos(pos,xoy){
  var _pos = pos.indexOf('_');
  if(xoy == 'x'){
  var posx =  pos.substr(0,_pos);
  return parseInt( posx,10);
  }else{
  var posy =  pos.substr(_pos+1,pos.length);
  return parseInt( posy,10);
  }
}
$.fn.extend({
    udlr: function(){
	//function udlr start
	var selectString = $(this).selector;
    var ymax   = 0;
    var ymin   = 999;
    $(this).each(function(){
        var pos = $(this).attr('pos');
        ymax = Math.max(ymax,getPos(pos,'y'));
        ymin = Math.min(ymin,getPos(pos,'y'));
    });
	
	
	var startpos = ymin;
    var endpos = ymax;
    $(this).bind('focus',function(event){
        $(this).select();
    });
   // $(this).each(function(){
        $(this).bind('keydown',function(e){
		    e = (e) ? e : ((window.event) ? window.event : "") //兼容IE和Firefox获得keyBoardEvent对象
		    var key = e.keyCode?e.keyCode:e.which; //兼容IE和Firefox获得keyBoardEvent对象的键值
            //
            var  pos = $(this).attr('pos');
            //
            var  posx = getPos(pos,'x');
            var  posy = getPos(pos,'y');
            switch(key) {
            case 37: 
                //left
                var npos =$(selectString+"[pos='"+String(posx)+'_'+String(posy-1)+"']");

                if(npos.length > 0)  {
                    npos.focus();
                }
                
                
                break;
            case 38:   
                //up
                i = 1;
                while(1) {
                  npos =$(selectString+"[pos='"+String(posx-i)+'_'+String(posy)+"']")
                  if (!npos.length) {
                    return false;
                    break;
                  }
                  if (!npos.attr('disabled')){
                    npos.focus();
                    return false;
                    break;
                  }
                  i++;
                }

                break;

            case 39:
                //right
                var npos =$(selectString+"[pos='"+String(posx)+'_'+String(posy+1)+"']");
                if(npos.length > 0)  {
                    npos.focus();
                }
                break;
            case 13:
            case 40:
                //down
                i = 1;
                while(1) {
                  npos =$(selectString+"[pos='"+String(posx+i)+'_'+String(posy)+"']")
                  if (!npos.length) {
                    return false;
                    break;
                  }
                  if (!npos.attr('disabled')){
                    npos.focus();
                    return false;
                    break;
                  }
                  i++;
                }
                break;
                
                
            }
        });
  //  });
	//  function udlr
}

})







