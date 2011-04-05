<?php
/*
  $Id$
*/
?>
<script type="text/javascript" src="includes/jquery-1.3.2.min.js"></script>
<script>
$(document).ready(function(){
  $('.columnLeft').parent().after('<td valign="top" style="padding-top:50px; *padding-top:48px;"><a href="javascript:void(0);" class="leftright" onclick="toggle_leftColumn();"><img src="includes/languages/japanese/images/boult.gif" alt="img"></a></td>');
});
function toggle_leftColumn()
{
  $('.columnLeft').toggle();
  if ($('.leftright').children().attr('src').indexOf('boult.gif') == -1) {
     $('.leftright').children().attr('src', 'includes/languages/japanese/images/boult.gif')
  } else {
     $('.leftright').children().attr('src', 'includes/languages/japanese/images/boult_back.gif')
  }
}
function toggle_lan(sobj)
{
   
  $('#'+sobj).slideToggle(); 
}
</script>
<?php
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
