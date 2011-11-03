// JavaScript Document
$(function(){
    var tmp;

    $('.note').each(function(){
      tmp = $(this).css('z-index');
      if(tmp>zIndex) zIndex = tmp;
      })

    make_draggable($('.note'));

    $("#fancy").fancybox({
      'type':'ajax',
      'modal':true,
      'titleShow':false,
      });

    $("#color li").live('click',function(){
      var color = $(this).attr("class");
      $("#mycolor").val(color);
      //alert(color);
      $(this).css("border","1px solid #369");
      $(this).siblings().css("border","1px solid #fff");
      });

    $("#addbtn").live('click',function(e){
        var txt = $("#note_txt").val();
        var user = $("#user").val();
        var color = $("#mycolor").val();
        if(txt==""){
        $("#msg").html("内容不能为空");
        $("#note_txt").focus();
        return false;
        }

        var data = {
        'zIndex': ++zIndex,
        'content': txt,
        'user': user,
        'color': color
        };

        $.post('posts.php',data,function(msg){
          zIndex = zIndex++;
          //alert(zIndex);
          if(parseInt(msg)){
          var str = "<div id='note_"+msg+"' class='note "+color+"' style='left:0;top:0;z-index:"+zIndex+",width:150px,height:150px'><div class='note_close' ><input type='hidden' value='"+msg+"' class='hidden'><input type='image' onclick=\"note_save_text(\'"+msg+"\')\" alt='save' src='images/icons/note_save.gif'><input type='image' onclick=\"note_desplay_none(\'"+msg+"\')\" alt='close' src='images/icons/note_close.gif'></div><div id='note_text_"+msg+"' class='note_textarea' style='height:120px'><textarea style='resize: none;'>"+txt+"</textarea></div></div>";
          $(".demo").append(str);
          make_draggable($('.note'));
          $.fancybox.close();
          window.location.reload();
          }else{
          $("#msg").html(msg);
          }
          });
        e.preventDefault();
    });	
});

var zIndex = 0;
function make_draggable(elements){
  /*elements.click(function(){
    $(this).css('z-index',++zIndex);
    });*/
  elements.draggable({
opacity: 0.8,
containment:'parent',
start:function(e,ui){ ui.helper.css('z-index',++zIndex)},
stop:function(e,ui){
$.get('update_position.php',{
x		: ui.position.left,
y		: ui.position.top,
z		: zIndex,
id	: parseInt(ui.helper.find('input.hidden').val())
});
}
});
}
function note_desplay_none(id)
{
  if(confirm("削除を実行しますか？")){
    document.getElementById('note_'+id).style.display = "none";
    $.get('update_position.php?del_note=true&id='+id,{});
  }
}
function note_save_text(id)
{
  text = $("#note_textarea_"+id).html();
  $.ajax({
url: 'update_position.php',
type: 'POST',
async: false,
data:
'action=save_text&text='+text+'&id='+id,
success: function(date){
if(date==1){
  alert("内容を保存しました。");
}
}
});

}
