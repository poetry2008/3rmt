<?php
/*
  $Id$
*/

  require('includes/application_top.php');

//notes
$notes = '';  
$left='';  
$top='';  
$zindex='';  
$belong = str_replace('/admin/','',$_SERVER['PHP_SELF']);
$query = tep_db_query("select * from notes where belong='".$belong."' and (attribute='1' or (attribute='0' and (author='".$ocertify->auth_user."' or author=''))) order by id desc");
$note_arr = array();
$height_arr = array();
//首页该用户的memo列表
while($row=tep_db_fetch_array($query)){
  list($left,$top,$zindex,$xlen,$ylen) = explode('|',$row['xyz']); 
  $note_arr[] = $row['id'];
  $height_arr[] = $ylen+$top+10;
}
//end nodes
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<script language="javascript" src="includes/javascript/jquery_include.js?v=<?php echo $back_rand_info?>"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js&v=<?php echo $back_rand_info?>"></script>
<script type="text/javascript" src="includes/jquery.fancybox-1.3.1.pack.js?v=<?php echo $back_rand_info?>"></script>
<script language="javascript" src="js2php.php?path=includes&name=global&type=js&v=<?php echo $back_rand_info?>"></script>
<script type='text/javascript' src='includes/javascript/ui/jquery-ui-1.8.16.custom.min.js?v=<?php echo $back_rand_info?>'></script>
<script type='text/javascript' src='includes/javascript/ui/jquery.ui.resizable.js?v=<?php echo $back_rand_info?>'></script>
<link rel="stylesheet" type="text/css" href="includes/note_style.css?v=<?php echo $back_rand_info?>" />
<link rel="stylesheet" type="text/css" href="includes/fancybox.css?v=<?php echo $back_rand_info?>" />
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css?v=<?php echo $back_rand_info?>" />
<link rel="stylesheet" type="text/css" href="includes/base/jquery.ui.all.css?v=<?php echo $back_rand_info?>" />
<script type="text/javascript" src='includes/javascript/admin_index.js?v=<?php echo $back_rand_info?>'></script>
<?php if(!empty($height_arr)){?>
<script language="javascript">
function add_notes(page_self){
  $.ajax({
    url: 'ajax_orders.php?action=get_index_notes',
    data:'page_name='+page_self ,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(data){
      $('.box_warp').html(data);
    }
  });
}
$(document).ready(function() { 
$('.box_warp').height(<?php echo max($height_arr);?>);
add_notes('<?php echo $belong;?>');
<?php
//监听memo的缩放
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
</script>
<?php }?>
<title><?php echo TITLE; ?></title>
</head>
<body>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
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
      echo '<td width="' . BOX_WIDTH . '" valign="top">';
      echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
      require(DIR_WS_INCLUDES . 'column_left.php');
      echo '</table>';
      echo '</td>';
    ?>
<td width="100%" valign="top">
<table width="100%"><tr><td>
<div class="box_warp">
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
