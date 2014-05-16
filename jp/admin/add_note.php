<?php
  require('includes/application_top.php');
  if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest"){
    echo '<html '.HTML_PARAMS.'>'."\n";
    echo '<head>'."\n";
    echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'>'."\n";
    echo '<title>'.TEXT_ADD_NOTE.'</title>'."\n";
    echo '</head>'."\n";
    echo '<body>'."\n";
    if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){
      echo "<script language='javascript'>"."\n";
      echo 'one_time_pwd('.$page_name.');'."\n";
      echo '</script>'."\n";
    }
  }
  $notes_sql = "select * from `notes`";
  $notes_query = tep_db_query($notes_sql);
  $i = 0;
  while($notes_row = tep_db_fetch_array($notes_query)){
       $notes_array_z[$i][0] = $notes_row['id'];
       $notes_array = explode('|',$notes_row['xyz']);
       $notes_array_z[$i][1] = $notes_array[2];
       $notes_array_z[$i][2] = $notes_array[0];
       $notes_array_z[$i][3] = $notes_array[1];
       $notes_array_z[$i][4] = $notes_array[3];
       $notes_array_z[$i][5] = $notes_array[4];
       $i++;
  }
$zindex = 1000;
foreach($notes_array_z as $key => $value){
    if($value[1] > $zindex){
      if(100+$key < $zindex){
        $z = 100+$key; 
      }else{
        $z = (100+$key)/2;
      }
      echo "<script language='javascript'>";
      echo "$('#note_".$value[0]."').css('z-index',".$z.")";
      echo "</script>";
      $xyz = $value[2].'|'.$value[3].'|'.$z.'|'.$value[4].'|'.$value[5];
      tep_db_query("update `notes` set `xyz`='".$xyz."' where `id`=".$value[0]); 
    }
}
?>
<script language='javascript'>
//   alert($('#fancybox-wrap').css('z-index'));
</script>
<div class="popwin" style="width:420px">
<h3 class="popup_title"><?php echo TEXT_ADD_NOTE;?></h3>
<div id="note_form">
<form id="add_form" action="#" method="post">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="posttable">
<tr>
<td ><?php echo TEXT_TITLE_NOTE;?>&nbsp;&nbsp;&nbsp;<span id="msg_title"></span></td>
</tr>
<tr>
<td><input type="text" class="input" name="title" id="title"/></td>
</tr>
<tr>
<td ><?php echo TEXT_ATTRIBUTE;?>&nbsp;&nbsp;&nbsp;<span id="msg_title"></span></td>
</tr>
<tr>
<tr>
<td>
<select name="attribute" id="attribute">
<option value="1"><?php echo TEXT_ATTRIBUTE_PUBLIC;?></option>
<option value="0"><?php echo TEXT_ATTRIBUTE_PRIVATE;?></option>
</select>
</td>
</tr>
<tr>
<td ><?php echo TEXT_COMMENT_NOTE;?>&nbsp;&nbsp;&nbsp;<span id="msg_txt"></span></td>
</tr>
<tr>
<td ><textarea name="note_txt" id="note_txt" class="input" style="width:98%; height:80px"></textarea></td>
</tr>
<tr>
<td><?php echo TEXT_COLOR;?></td>
</tr>
<tr>
<td><ul id="color">
<li class="white"></li>
<li class="gray"></li>
<li class="red"></li>
<li class="blue"></li>
<li class="yellow"></li>
</ul><input type="hidden" id="mycolor" value="white" />
<input type="hidden" id="author" value="<?php echo $_GET['author']?>" />
<input type="hidden" id="belong" value="<?php echo $_GET['belong']?>" />
</td>
</tr>
<tr>
<td height="36" colspan="2" align="center"><input type="submit" id="addbtn"
class="btn" value="<?php echo IMAGE_SAVE?>" /> <input type="button" class="btn"
value="<?php echo IMAGE_CANCEL?>" id="cancel" onclick="$.fancybox.close()" /></td>
</tr>
</table>
</form>
</div>
</div>
<?php 
  if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest"){
    echo '</body></html>';
  }
?>
