<?php
require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();
switch ($_GET['action']){
/* -----------------------------------------------------
   case 'ajax' 获取该分类的计算设定信息   
------------------------------------------------------*/
    case 'ajax':
      $cPath=cpathPart($_GET['cPath'],2);
      $res=tep_db_query("select * from set_auto_calc where parent_id='".$cPath."'");
      
      $col=tep_db_fetch_array($res);
      if ($col) {
        echo json_encode($col);
      } else {
        echo json_encode(array(
          'parent_id' => $cPath,
          'bairitu'   => 1.1,
          'keisan'    => 0,
          'hikaku'    => '',
          'shisoku'   => '',
          'percent'   => ''
        ));
      }
      break;
   }

?>
