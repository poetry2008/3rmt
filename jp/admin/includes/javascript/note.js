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
      $(this).css("border","1px solid #369");
      $(this).siblings().css("border","1px solid #cccccc");
      });

    $("#addbtn").live('click',function(e){
        var txt = $("#note_txt").val();
        var title = $("#title").val();
        var attribute= $("#attribute").val();
        var author= $("#author").val();
        var belong= $("#belong").val();
        var color = $("#mycolor").val();
        if(txt==""){
        $("#msg_txt").html(notice_input_info_str);
        $("#note_txt").focus();
        return false;
        }
        if(title==""){
        $("#msg_title").html(notice_input_title_str);
        $("#title").focus();
        return false;
        }

        var data = {
        'zIndex': ++zIndex,
        'content': txt,
        'title': title,
        'attribute': attribute,
	'author':author,
        'belong':belong,
        'color': color
        };

        $.post('posts.php',data,function(msg){
          zIndex = zIndex++;
          msg = msg.split('||');
          if(parseInt(msg[0])){
          var str = "<div id='note_"+msg[0]+"' class='note "+color+"' style='left:0;top:0;z-index:"+zIndex+",width:150px,height:150px'><div class='note_head' ><div class='title'><input type='button' onclick=\"note_save_text(\'"+msg[0]+"\')\" value='"+notice_save_str+"'>"+title+"&nbsp;&nbsp;"+msg[1]+"</div><div class='note_clost'><input type='hidden' value='"+msg[0]+"' class='hidden'><input type='image' onclick=\"note_desplay_none(\'"+msg[0]+"\')\" alt='close' src='images/icons/note_close.gif'></div></div><div id='note_text_"+msg[0]+"' class='note_textarea' style='height:120px'><textarea style='resize: none;overflow;auto;font-size:11px;'>"+txt+"</textarea></div></div>";
          $(".box_warp").append(str);
          make_draggable($('.note'));
          $.fancybox.close();
          window.location.reload();
          }else{
          $("#msg").html(msg[0]);
          }
          });
        e.preventDefault();
    });	
});

var zIndex = 0;
//drag
function make_draggable(elements){
  elements.mousedown(function(e){
    $("#manual_info_id").show();
    if(e.which == 1){
      var box_warp_height = $('.box_warp').height();
      var leftmenu_height = $(".leftmenu").height();
      if(leftmenu_height > box_warp_height){
        $('.box_warp').height(leftmenu_height);
      }
    }
  });
  /*elements.click(function(){
    $(this).css('z-index',++zIndex);
    });*/
  elements.draggable({
opacity: 0.8,
containment:'parent',
start:function(e,ui){ ui.helper.css('z-index',++zIndex)},
stop:function(e,ui){
z_index = zIndex+1;
$.get('update_position.php',{
x		: ui.position.left,
y		: ui.position.top,
z		: zIndex,
id	: parseInt(ui.helper.find('input.hidden').val())
});
}
});
}
//hide
function note_desplay_none(id)
{
  if(confirm(notice_input_is_del_str)){
    document.getElementById('note_'+id).style.display = "none";
    $.get('update_position.php?del_note=true&id='+id,{});
  }
}
//save
function note_save_text(id)
{
  text = $("#note_textarea_"+id).val();
  $.ajax({
url: 'update_position.php',
type: 'POST',
async: false,
data:
'action=save_text&text='+text+'&id='+id,
success: function(date){
var res_arr = date.split("|||");
var image_id = document.getElementById("image_id_"+id);
image_id = image_id.src;
if(res_arr[0]=='true'){
  title = '<input type="button" onclick="note_save_text(\''+id+'\')" value="'+notice_save_str+'">&nbsp;<image id="image_id_'+id+'" src="'+image_id+'">&nbsp;&nbsp;'+res_arr[1]+'&nbsp;&nbsp;'+res_arr[2];
  content = res_arr[3];
  $('#note_title_'+id).html(title);
  $('#note_textarea_'+id).val(content);
  alert(notice_info_save_str);
}
}
});
}

//min window
function note_min_window(n_id)
{
  $.ajax({
    type: 'POST',
    data:'note_id='+n_id,
    async:false,
    url: 'ajax_orders.php?action=hide_note',
    success: function(msg) {
      msg_note_info = msg.split('|||');  
      $('#note_'+n_id).css('display', 'none'); 
      note_add_str = '<li><a href="javascript:void(0);" onclick="note_revert_window(this, \''+n_id+'\');"><img src="images/icons/note_'+msg_note_info[0]+'_window.gif" title="'+msg_note_info[1]+'" alt="'+msg_note_info[1]+'"></a></li>'; 
      $('.note_hide_list').append(note_add_str); 
    }
  });
}

//revert window
function note_revert_window(n_obj, n_id)
{
  $.ajax({
    type: 'POST',
    data:'note_id='+n_id,
    async:false,
    url: 'ajax_orders.php?action=show_note',
    success: function(msg) {
      $(n_obj).remove();
      $('#note_'+n_id).css('display', 'block');
    } 
  });
}

//change layer
function changeLayer(obj) {
  arr = new Array(); 
  var i = 0 
  $('.note').each(function(i) {
    arr[i] = $(this).css("z-index");  
    i++; 
  });
  arr.sort();
  max = arr[arr.length-1]+1;
  $(obj).css('z-index', max);
}
