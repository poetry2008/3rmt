<?php
//header("Content-Type: text/xml");
require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();
switch ($_GET['action']){

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
      
      //    $xmlbody .= "<data>";
      //    $xmlbody .= "<calc bai=\"".$col['bairitu']."\" kei=\"".$col['keisan']."\"  shisoku=\"".$col['shisoku']."\"></calc>";
      //    $xmlbody .= "</data>"; 
    
      //    echo $xmlbody;          
      break;
    
      /*case 'cleate_menu':
    $cID=$_GET['cid'];
    $proid=$_GET['pid'];
    $pname=$_GET['pname'];
    tep_db_query("insert into set_menu_list (categories_id,products_id,products_name) values ('".$cID."','".$proid."','".$pname."')");
    
    break;
      */
}

?>