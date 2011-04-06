<?php
/*
  $Id$
*/
if ((strpos($_SERVER['PHP_SELF'], 'history.php') === false)  && 
    (strpos($_SERVER['PHP_SELF'], 'cleate_oroshi.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'list_display.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'edit_orders.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'edit_new_orders.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'categories.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'micro_log.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'products_tags.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'categories_admin.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'pw_manager_log.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'cleate_dougyousya.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'customers_products.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'pw_manager.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'edit_new_orders2.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'telecom_unknow.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'cleate_list.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'orders.php') === false)) {
?>
<script type="text/javascript" src="includes/javascript/jquery.js"></script>
<?php }?>
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
