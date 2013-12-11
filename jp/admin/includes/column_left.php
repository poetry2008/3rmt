<?php
/*
  $Id$
*/
?>
<script language="javascript" src="js2php.php?path=includes|javascript&name=left&type=js&other=<?php echo urlencode($_SERVER['PHP_SELF']);?>&v=<?php echo $back_rand_info?>"></script>
<?php
$l_select_box_arr = array();
if (isset($_SESSION['l_select_box'])) {
  $l_select_box_arr = explode(',', $_SESSION['l_select_box']);
}
require(DIR_WS_BOXES . 'configuration.php');
require(DIR_WS_BOXES . 'catalog.php');
require(DIR_WS_BOXES . 'modules.php');
require(DIR_WS_BOXES . 'customers.php');
require(DIR_WS_BOXES . 'localization.php');
require(DIR_WS_BOXES . 'reports.php');
require(DIR_WS_BOXES . 'tools.php');
require(DIR_WS_BOXES . 'users.php');
?>
