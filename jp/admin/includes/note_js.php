<?php
$notes = '';  
$left='';  
$top='';  
$zindex='';  
$mode_array = array(FILENAME_ORDERS_EDIT,FILENAME_FINAL_PREORDERS,FILENAME_CUSTOMERS.'\?cID=');
$mode_flag = false;
$mode_belong_value = '';
foreach($mode_array as $mode_value){
  preg_match_all('/'.$mode_value.'/',$belong,$mode_belong_array);
  $mode_flag = $mode_belong_array[0][0] != '' ? true : false;
  if($mode_flag){
    $mode_belong_value = $mode_value; 
    break; 
  }
}
if($mode_flag){
  $query = tep_db_query("select * from notes where (belong='".$belong."' or belong='".$mode_belong_value."') and (attribute='1' or (attribute='0' and author='".$ocertify->auth_user."')) order by id desc");
}else{
  $query = tep_db_query("select * from notes where belong='".$belong."' and (attribute='1' or (attribute='0' and author='".$ocertify->auth_user."')) order by id desc");
}
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
<script type="text/javascript" >
<?php tep_get_javascript('global','includes');?>
</script>
<script type='text/javascript' src='includes/javascript/ui/jquery-ui-1.8.16.custom.min.js'></script>
<script type='text/javascript' src='includes/javascript/ui/jquery.ui.resizable.js'></script>
<link rel="stylesheet" type="text/css" href="includes/note_style.css" />
<link rel="stylesheet" type="text/css" href="includes/fancybox.css" />
<link rel="stylesheet" type="text/css" href="includes/base/jquery.ui.all.css" />
<?php if(!empty($height_arr)){?>
<script language="javascript">
$(document).ready(function() { 
var scroll_height = document.body.scrollHeight;
scroll_height = parseInt(scroll_height);
var max_height = '<?php echo max($height_arr);?>';
max_height = parseInt(max_height);
head_height = $(".compatible_head").height();
if(max_height > scroll_height-head_height){
  $('.box_warp').height(<?php echo max($height_arr);?>);
}
<?php
foreach($note_arr as $note_row){
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
          if($('.box_warp').height()<(Number(ylen)+Number(top.substring(0,top.length-2))+10)){
              $('.box_warp').height(Number(ylen)+Number(top.substring(0,top.length-2))+10);
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

