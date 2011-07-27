<?php
/*
  $Id$
*/
if ($ocertify->npermission >= 10) {?>
<script>
$(document).ready(function(){
  <?php
  if ($_COOKIE['tarrow'] == 'open') {
  ?>
  $('.columnLeft').parent().after('<td valign="top" style="padding-top:46px; *padding-top:48px; padding-right:5px; *padding-left:0;"><a href="javascript:void(0);" class="leftright" onclick="toggle_leftColumn();"><img src="includes/languages/japanese/images/boult_back.gif" alt="img"></a></td>');
  $('.columnLeft').css('display', 'block'); 
  <?php
  } else {
  ?>
  $('.columnLeft').parent().after('<td valign="top" style="padding-top:46px; *padding-top:48px; padding-right:5px; *padding-left:0;"><a href="javascript:void(0);" class="leftright" onclick="toggle_leftColumn();"><img src="includes/languages/japanese/images/boult.gif" alt="img"></a></td>');
  <?php
  }
  ?>
  $('.columnLeft').parent().parent().parent().parent().attr('cellspacing', '0');
  $('.columnLeft').parent().parent().parent().parent().attr('cellpadding', '0');
  $('.columnLeft').parent().addClass('leftmenu');
});
function toggle_leftColumn()
{
  var arrow_status = $('.columnLeft').css('display');
  
  if (arrow_status == 'none') {
    document.cookie = 'tarrow=open'; 
  } else {
    document.cookie = 'tarrow=close'; 
  }
  
  $('.columnLeft').toggle();
  if ($('.leftright').children().attr('src').indexOf('boult.gif') == -1) {
     $('.leftright').children().attr('src', 'includes/languages/japanese/images/boult.gif')
  } else {
     $('.leftright').children().attr('src', 'includes/languages/japanese/images/boult_back.gif')
  }
}
function toggle_lan(sobj)
{
  var current_status = $('#'+sobj).css('display');
  if (current_status == 'none') {
    var action_str = 'insert'; 
  } else {
    var action_str = 'del'; 
  }
  $('#'+sobj).slideToggle(); 
  $.ajax({
    url: "<?php echo tep_href_link('toggle_left_menu.php');?>",
    data:"id="+sobj+"&action="+action_str, 
    type: "POST",
    success:function(msg) {
    }
  }); 
}
</script>
<?php }?>
<?php
$l_select_box_arr = array();
if (isset($_SESSION['l_select_box'])) {
  $l_select_box_arr = explode(',', $_SESSION['l_select_box']);
}
if ($ocertify->npermission >= 10) {
  require(DIR_WS_BOXES . 'configuration.php');
  require(DIR_WS_BOXES . 'catalog.php');
  require(DIR_WS_BOXES . 'modules.php');
  require(DIR_WS_BOXES . 'customers.php');
  // require(DIR_WS_BOXES . 'taxes.php');
  require(DIR_WS_BOXES . 'localization.php');
  require(DIR_WS_BOXES . 'reports.php');
  require(DIR_WS_BOXES . 'tools.php');
  require(DIR_WS_BOXES . 'users.php');
}
?>
