function fn(value){
  var gameid = document.getElementById("gameid");
  if(gameid.style.display == 'none'){  
   $(value).css("background","url('../images/seach_right_visit.png') no-repeat scroll 0 0 transparent");
   $(value).css("height","75px");
   $("#gameid").slideDown("slow");
   $("#out_id").slideUp("slow");
   }else{
     $("#gameid").slideUp(500);               
     setTimeout('search_out()',510);
     //$("#game-preview").css("background","url('../images/seach_right.png') no-repeat scroll 0 0 transparent");
   }
}
function search_over()
{
       var gameid = document.getElementById("gameid");
       document.getElementById("game-preview").style.background='url("../images/seach_right_hover.png")';
}
function search_out()
{     
       var gameid = document.getElementById("gameid");
       if(gameid.style.display == 'none')
        {
          document.getElementById("game-preview").style.background='url("../images/seach_right.png")';
        }
        else if(gameid.style.display != 'none'){
          document.getElementById("game-preview").style.background='url("../images/seach_right_visit.png")';
        }
  }


function search_close(){
  $("#game-preview").css("background","url('../images/seach_right.png') no-repeat scroll 0 0 transparent");
  $("#gameid").slideUp("slow");
} 
 function lg(){
      var out_id = document.getElementById("out_id");
      if(out_id.style.display == 'none'){       
       $("#out_id").slideDown("slow");
      // $("#gameid").slideUp("slow");
      // $("#gameid").css("background","url('../image/seach_bottom03_visit.png') no-repeat scroll 0 0 transparent");
    setTimeout("search_close()") 
    }else{
      $("#out_id").slideUp("slow");               
    }     
}
function search_close_header(){
   $("#out_id").slideUp("slow");   
}
