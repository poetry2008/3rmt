<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');



  //notes
$notes = '';  
$left='';  
$top='';  
$zindex='';  
$query = tep_db_query("select * from notes order by id desc");
$note_arr = array();
$height_arr = array();
while($row=tep_db_fetch_array($query)){
  list($left,$top,$zindex,$xlen,$ylen) = explode('|',$row['xyz']); 
  $note_arr[] = $row['id'];
  $height_arr[] = $ylen+$top+10;
  $time = strtotime($row['addtime']);
  $notes.= '
    <div id="note_'.$row['id'].'" class="note '.$row['color'].'" 
    style="left:'.$left.'px;top:'.$top.'px;z-index:'.$zindex.';height:'.$ylen.'px;width:'.$xlen.'px">
    <div class="note_close">
    <input type="hidden" value="'.$row['id'].'" class="hidden">
    <input type="image" onclick="note_save_text(\''.$row['id'].'\')" alt="save" 
    src="images/icons/note_save.gif">
    <input type="image" onclick="note_desplay_none(\''.$row['id'].'\')" alt="close"
    src="images/icons/note_close.gif">
    </div><div id="note_text_'.$row['id'].'" class="note_textarea"
    style="height:'.($ylen-30).'px">'
    .'<textarea style="resize: none;" id="note_textarea_'.$row['id'].'">'
    .htmlspecialchars($row['content']).'
    </textarea></div>
    </div>';
}
//end nodes
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script type="text/javascript" src="includes/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="includes/global.js"></script>
<script type='text/javascript' src='includes/javascript/ui/jquery-ui-1.8.16.custom.min.js'></script>
<script type='text/javascript' src='includes/javascript/ui/jquery.ui.resizable.js'></script>
<link rel="stylesheet" type="text/css" href="includes/note_style.css" />
<link rel="stylesheet" type="text/css" href="includes/fancybox.css" />
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />
<link rel="stylesheet" type="text/css" href="includes/base/jquery.ui.all.css" />
<script language="javascript">
$().ready(function() { 
$('.demo').height(<?php echo max($height_arr);?>);
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
</script>
<title><?php echo TITLE; ?></title>
</head>
<body>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    //one_time_pwd('<?php echo $page_name;?>');
    </script>
    <?php }?>
    <div class='header'>
    <?php
    require(DIR_WS_INCLUDES . 'header.php');
    ?>
    </div>
    <table border="0" width="100%" cellspacing="2" cellpadding="2">
    <tr>
    <?php
    if ($ocertify->npermission >= 10) {
      echo '<td width="' . BOX_WIDTH . '" valign="top">';
      echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
      require(DIR_WS_INCLUDES . 'column_left.php');
      echo '</table>';
      echo '</td>';
    } else {
      echo '<td>&nbsp;</td>';
    }
?>
<td width="100%" valign="top">
<table width="100%"><tr>
<td align="rignt" height="20px">
<div id="add"><a href="add_note.php" id="fancy">
<?php echo "<input type='button' value='".TEXT_ADD_NOTE."'>";?></a></div>
</td>
</tr>
<tr><td>
<div class="demo">
<?php echo $notes;?>
</div>
</td></tr>
</table>
</td>
</tr>
</table>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<br>
</body>
</html>
