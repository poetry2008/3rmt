<?php
$notes = '';  
$left='';  
$top='';  
$zindex='';  
$query = tep_db_query("select * from notes where belong='".$belong."' and (attribute='1' or (attribute='0' and author='".$ocertify->auth_user."')) order by id desc");
$note_arr = array();
$height_arr = array();
while($row=tep_db_fetch_array($query)){
  list($left,$top,$zindex,$xlen,$ylen) = explode('|',$row['xyz']); 
  $note_arr[] = $row['id'];
  $height_arr[] = $ylen+$top+10;
  $time = strtotime($row['addtime']);
  $attribute = $row['attribute'];
  $attribute_image = $attribute == 1 ? '<image id="image_id_'.$row['id'].'" alt="'.TEXT_ATTRIBUTE_PUBLIC.'" src="images/icons/public.gif">' : '<image id="image_id_'.$row['id'].'" alt="'.TEXT_ATTRIBUTE_PRIVATE.'" src="images/icons/private.gif">';
  $notes.= '
    <div id="note_'.$row['id'].'" ondblclick="changeLayer(this);" class="note '.$row['color'].'" 
    style="left:'.$left.'px;top:'.$top.'px;z-index:'.$zindex.';height:'.$ylen.'px;width:'.$xlen.'px">
    <div class="note_head">
    <div id="note_title_'.$row['id'].'" class="note_title">
    <input type="button" onclick="note_save_text(\''.$row['id'].'\')"
     value=" '.IMAGE_SAVE.'" >&nbsp;'.$attribute_image.'&nbsp;'.$row['title'].'&nbsp;&nbsp;
    '.substr($row['addtime'],0,strlen($row['addtime'])-3).'
    </div><div class="note_close">
    <input type="hidden" value="'.$row['id'].'" class="hidden">
    <a href="javascript:void(0);" onclick="note_desplay_none(\''.$row['id'].'\')"><image title="close" alt="close"
    src="images/icons/note_close.gif"></a></div>
    </div><div id="note_text_'.$row['id'].'" class="note_textarea"
    style="height:'.($ylen-37).'px">'
    .'<textarea style="overflow:auto;resize: none;font-size:11px;" id="note_textarea_'.$row['id'].'">'
    .trim(htmlspecialchars($row['content'])).'</textarea></div>
    </div>';
}
?>
<script type="text/javascript" src="includes/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="js2php.php?path=includes&name=global&type=js"></script>
<script type='text/javascript' src='includes/javascript/ui/jquery-ui-1.8.16.custom.min.js'></script>
<script type='text/javascript' src='includes/javascript/ui/jquery.ui.resizable.js'></script>
<link rel="stylesheet" type="text/css" href="includes/note_style.css" />
<link rel="stylesheet" type="text/css" href="includes/fancybox.css" />
<link rel="stylesheet" type="text/css" href="includes/base/jquery.ui.all.css" />
<?php if(!empty($height_arr)){?>
<script language="javascript">
$(document).ready(function() { 
var success = $(".messageStackSuccess").html();
var head_success = 17;
var scroll_width = document.body.scrollWidth;  
var note_width;
var note_left;
var head_notice = 0;
var left_status_flag = $(".columnLeft").css("display");
var left_status = '';
if(left_status_flag){
  left_status = '<?php echo $_COOKIE['tarrow'];?>';
}
if(document.getElementById("show_head_notice")){

  head_notice = $("#show_head_notice").height();
}
$('.demo').height($('#main_table').height());
<?php
foreach($note_arr as $note_row){
?>
note_width = $("#note_<?php echo $note_row;?>").width();
note_width = parseInt(note_width);
note_top = $("#note_<?php echo $note_row;?>").css("top");
note_top = parseInt(note_top);
note_left = $("#note_<?php echo $note_row;?>").css("left");
note_left = note_left.replace('px','');
note_left = parseInt(note_left);
if((note_left+note_width) > scroll_width){
  $("#note_<?php echo $note_row;?>").css("left",scroll_width-note_width);
}
if(note_top < head_notice+93){

  $("#note_<?php echo $note_row;?>").css("top",note_top+head_notice); 
}
if(note_left < 180){
  if(left_status == 'open'){
    $("#note_<?php echo $note_row;?>").css("left",180);
  }else if(left_status == 'close'){
    $("#note_<?php echo $note_row;?>").css("left",20);
  }else if(left_status == ''){
    if(note_left < 0){
      $("#note_<?php echo $note_row;?>").css("left",0);
    }
  }
}
if(success != null){

  $("#note_<?php echo $note_row;?>").css("top",note_top+head_success);
}
if(head_notice != null && success != null){
  $("#note_<?php echo $note_row;?>").css("top",note_top+head_success+head_notice);
}
<?php
  echo "$('#note_".$note_row."').resizable({ 
    alsoResize: '#note_text_".$note_row."',
    stop: function(e) {
      var xlen=$(\"#note_".$note_row."\").width();
      var ylen=$(\"#note_".$note_row."\").height();
      var top=$(\"#note_".$note_row."\").css('top');
      $.ajax({
        url: 'update_position.php',
        type: 'POST',
        async: false,
        data:
        'action=change_move&xlen='+xlen+'&ylen='+ylen+'&id=".$note_row."',
        success: function(){
          if($('.demo').height()<(Number(ylen)+Number(top.substring(0,top.length-2))+10)){
              $('.demo').height(Number(ylen)+Number(top.substring(0,top.length-2))+10);
            }
          }
          });
      }
    });\n";
}
?>
});
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
</script>
<?php }?>

