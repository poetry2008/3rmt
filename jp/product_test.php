<?php 
require('includes/application_top.php');
require('option/HM_Option.php');
require('option/HM_Option_Group.php');
$hm_option = new HM_Option();
if ($_GET['action'] == 'process') {
  $hm_option->check();
}
page_head();
?>
<head>
<body>
<form action="product_test.php?action=process" method="post">
<?
$hm_option->render();
?>
<input type="submit" value="submit">
</form>
</body>
</html>
