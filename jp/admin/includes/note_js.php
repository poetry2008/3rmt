<?php
$notes = '';  
$left='';  
$top='';  
$zindex='';  
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
$user_info = tep_get_user_info($ocertify->auth_user);
$query = tep_db_query("select * from notes where belong='".$belong."' and (attribute='1' or (attribute='0' and author='".$user_info['name']."'))  order by id desc");
$note_arr = array();
$height_arr = array();
while($row=tep_db_fetch_array($query)){
  list($left,$top,$zindex,$xlen,$ylen) = explode('|',$row['xyz']); 
  $note_arr[] = $row['id'];
  $height_arr[] = $ylen+$top+10;
  $time = strtotime($row['addtime']);
  $notes.= '
    <div id="note_'.$row['id'].'" ondblclick="changeLayer(this);" class="note '.$row['color'].'" 
    style="left:'.$left.'px;top:'.$top.'px;z-index:'.$zindex.';height:'.$ylen.'px;width:'.$xlen.'px">
    <div class="note_head">
    <div id="note_title_'.$row['id'].'" class="note_title">
    <input type="button" onclick="note_save_text(\''.$row['id'].'\')"
     value=" '.IMAGE_SAVE.'" >&nbsp;&nbsp;'.$row['title'].'&nbsp;&nbsp;
    '.substr($row['addtime'],0,strlen($row['addtime'])-3).'
    </div><div class="note_close">
    <input type="hidden" value="'.$row['id'].'" class="hidden">
    <input type="image" onclick="note_desplay_none(\''.$row['id'].'\')" alt="close"
    src="images/icons/note_close.gif"></div>
    </div><div id="note_text_'.$row['id'].'" class="note_textarea"
    style="height:'.($ylen-37).'px">'
    .'<textarea style="overflow:auto;resize: none;font-size:11px;" id="note_textarea_'.$row['id'].'">'
    .trim(htmlspecialchars($row['content'])).'</textarea></div>
    </div>';
}
?>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script type="text/javascript" src="includes/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="includes/global.js"></script>
<script type='text/javascript' src='includes/javascript/ui/jquery-ui-1.8.16.custom.min.js'></script>
<script type='text/javascript' src='includes/javascript/ui/jquery.ui.resizable.js'></script>
<link rel="stylesheet" type="text/css" href="includes/note_style.css" />
<link rel="stylesheet" type="text/css" href="includes/fancybox.css" />
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />
<link rel="stylesheet" type="text/css" href="includes/base/jquery.ui.all.css" />
<?php if(!empty($height_arr)){?>
<script language="javascript">
$(document).ready(function() { 
$('.demo').height($('#main_table').height());
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

