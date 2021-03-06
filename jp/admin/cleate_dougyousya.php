<?php
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');

$currencies = new currencies();
$cPath=$_POST['cpath'];
switch ($HTTP_GET_VARS['action']){
/*--------------------------
 case 'edit_oroshi' 编辑名称
 case 'set_oroshi'  设置名称
 case 'delete' 删除名称
 -------------------------*/
case 'edit_oroshi':
  $cpath = $HTTP_GET_VARS['cpath'];
  $orrshi_id = $HTTP_GET_VARS['id'];
  $name = $HTTP_GET_VARS['name'];
  $sql = "select * from set_dougyousya_categories where
    dougyousya_id='".$orrshi_id."'";
  $res = tep_db_query($sql);
  $ckstr = array();
  while($col = tep_db_fetch_array($res)){
  $ckstr[] = $col['categories_id'];   
  }
  break;
case 'set_oroshi':
  $orrshi_id = $_POST['orrshi_id'];

  if (isset($_POST['sort'])) {
    foreach($_POST['sort_order'] as $oid => $sort_order) {
      tep_db_perform('set_dougyousya_names', array('sort_order' => (int)$sort_order), 'update', "dougyousya_id='".$oid."'");
    }
  } else if(isset($_POST['set_oroshi'])){
  $ocid_arr = $_POST['ocid'];
  $oro_name = $_POST['set_oroshi'];
  $cot = count($oro_name);
  $j = 0 ;
  while ( $j < $cot) {
  $ocid = $ocid_arr[$j];
  $sql = 'insert into set_dougyousya_names (dougyousya_name,user_added,date_added,user_update,date_update) values
    ("'.$oro_name[$j].'","'.$_SESSION['user_name'].'","'.date('Y-m-d  H:i:s',time()).'","'.$_SESSION['user_name'].'","'.date('Y-m-d H:i:s',time()).'")';
    $j++;
  if(!$oro_name[$j-1]||!$ocid){
    continue;
  }
  tep_db_query($sql);
  $res = tep_db_query("select max(dougyousya_id) dougyousya_id from set_dougyousya_names");
  $col = tep_db_fetch_array($res);
  $cnt = count($ocid);
  $i = 0;
  $flag = false;
  while ( $i < $cnt ) {
       $i++;
    $sql = 'insert into set_dougyousya_categories (dougyousya_id,categories_id) values
      ("'.$col['dougyousya_id'].'","'.$ocid[$i-1].'")';
       tep_db_query($sql);
  }
  }
  }else if (isset($orrshi_id)){
  $ocid = $_POST['ocid'];
  $cnt = count($ocid);
  $i = 0;
  $flag = false;
  $res = tep_db_query("select categories_id from
    set_dougyousya_categories where dougyousya_id='".$orrshi_id."'");
  $douno = array();
  $j = 0; 
  while($col = tep_db_fetch_array($res)){
    $douno[$j] = $col['categories_id'];
    $j++;
  }
    $sql = 'delete FROM set_dougyousya_categories WHERE
      dougyousya_id="'.$orrshi_id.'"';
    tep_db_query($sql);
  foreach ($ocid as $diffval) {
    $sql = 'insert into set_dougyousya_categories (dougyousya_id,categories_id) values
      ("'.$orrshi_id.'","'.$diffval.'")';
       tep_db_query($sql);
  }
  $name = $_POST['up_oroshi'];
  $sql = 'select * FROM set_dougyousya_history 
          WHERE dougyousya_id = "'.$orrshi_id.'" AND categories_id
          NOT IN 
          (select c.categories_id 
           from categories c 
           where c.parent_id in
          (SELECT sdc.categories_id
          FROM set_dougyousya_categories sdc
          WHERE dougyousya_id = "'.$orrshi_id.'"
          ))';
  $sql = 'update set_dougyousya_names set dougyousya_name="'.$name[$orrshi_id].'",user_update="'.$_SESSION['user_name'].'",date_update="'.date('Y-m-d H:i:s',time()).'" where dougyousya_id="'.$orrshi_id.'"';
  tep_db_query($sql);
  }
  
  tep_redirect(tep_href_link('cleate_dougyousya.php','action=edit_oroshi&id='.$_POST['orrshi_id']));
  break;
  
case 'delete':
  $dougyousya_id=$_GET['id'];
  $sql = "delete from  set_dougyousya_names  where dougyousya_id ='".$dougyousya_id.
    "'";
  tep_db_query($sql);
  $sql = "delete from set_dougyousya_categories where dougyousya_id='".$dougyousya_id."'";
  tep_db_query($sql);
  $sql = "delete from set_dougyousya_history where dougyousya_id='".$dougyousya_id."'";
  tep_db_query($sql);
  tep_redirect('cleate_dougyousya.php');
  break;  
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>
<?php echo CLEATE_DOUGYOUSYA_TITLE;?>
</title>
  <link rel="stylesheet" type="text/css" href="includes/stylesheet.css?v=<?php echo $back_rand_info?>">
  <script type="text/javascript" src="includes/javascript/jquery.js?v=<?php echo $back_rand_info?>"></script>
  <script language="javascript" src="includes/javascript/jquery_include.js?v=<?php echo $back_rand_info?>"></script>
  <script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js&v=<?php echo $back_rand_info?>"></script>
  <script type="text/javascript">
  	var html = new Array();
	var i=0;
	var js_dougyousya_text_add = '<?php echo TEXT_ADD;?>';
	var js_dougyousya_update_sort = '<?php echo CLEATE_DOUGYOUSYA_UPDATE_SORT?>';
	var valmethod = true;
	var js_dougyousya_peer_name = '<?php echo PLEASE_INPUT_PEER_NAME; ?>';
	var js_dougyousya_already_exists = '<?php echo TEXT_ALREADY_EXISTS; ?>';
	var js_dougyousya_game_title = '<?php echo TEXT_PLEASE_GAME_TITLE; ?>';
	var js_dougyousya_must_same = '<?php echo TEXT_CONTENT_MUST_BE_THE_SAME; ?>';
	var js_dougyousya_alert = '<?php echo CLEATE_DOUGYOUSYA_ALERT;?>';
	var js_dougyousya_self = '<?php echo $_SERVER['PHP_SELF']?>';
	var js_onetime_pwd = '<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>';
	var js_onetime_error = '<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>';
	var js_dougyousya_ok_to_delete = '<?php echo TEXT_OK_TO_DELETE; ?>';
	var js_dougyousya_ws_admin = '<?php echo HTTP_SERVER.DIR_WS_ADMIN;?>';
</script>
<script type="text/javascript" src="includes/javascript/admin_cleate_dougyousya.js?v=<?php echo $back_rand_info?>"></script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
  <div id="spiffycalendar" class="text"></div>
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
     <tr>
        <td width="<?php echo BOX_WIDTH; ?>" valign="top">
           <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
              <tr>
                 <td>
                    <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
                 </td>
              </tr>
           </table>
        </td>
        <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
        <div class="compatible">
           <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                 <td class="pageHeading" height="40"><?php echo CLEATE_DOUGYOUSYA_TITLE;?></td>
              </tr>
              <tr>
                 <td>
  <form method="post" action="cleate_dougyousya.php?action=set_oroshi" name="d_form">
  <table width="100%" cellspacing="0" cellpadding="0">
     <tr>
        <td class="cleate_add" valign="top">
  <input type="button" value="<?php echo CLEATE_DOUGYOUSYA_ADD_BUTTON;?>" name='b1' onClick="input_add()">
        </td>
     </tr>
     <tr>
        <td class="cleate_main">
  <input type="hidden" value="<?php echo $cPath ?>" name="cpath">
  <?php if(isset($orrshi_id)){?>
    <input type="hidden" value="<?php echo $orrshi_id;?>" name="orrshi_id" id="orrshi_id">
  <?php }
  $start = 0;
  $categories_subtree = getSubcatergories($start);
  $res=tep_db_query("select * from set_dougyousya_names ORDER BY sort_order ,dougyousya_id ASC");
  $i = 0;
?>
<table width="100%" cellspacing="0" border="0" cellpadding="2">
<?php
while($col=tep_db_fetch_array($res)){
if(empty($HTTP_GET_VARS['id'])){
   $HTTP_GET_VARS['id'] = $col['dougyousya_id']; 
   echo '<tr class="dataTableRowSelected">';
 }else if($col['dougyousya_id'] == $HTTP_GET_VARS['id']){
   echo '<tr class="dataTableRowSelected">';
 } else{
   echo '<tr>';
 }
  ?>
    <td width="10"><?php if ($i) {?><a href="javascript:void(0);" onclick="ex(<?php echo $i;?>)">↑</a><?php }?></td>
    <td id="tr_<?php echo $i;?>_1">
      <input type="hidden" name="sort_order[<?php echo $col['dougyousya_id'];?>]" value="<?php echo $i;?>" class="sort_order_input">
      <?php echo CLEATE_DOUGYOUSYA_TONGYE;?><?php echo $col['dougyousya_name'];?>
      <input type="hidden" name="exist_name[]" value='<?php echo $col['dougyousya_name'];?>'>
    </td>
    <td id="tr_<?php echo $i;?>_2" width='50'><a href='cleate_dougyousya.php?action=edit_oroshi&id=<?php echo $col['dougyousya_id'];?>'><?php echo CLEATE_DOUGYOUSYA_EDIT;?></a></td>
    <?php
    if ($ocertify->npermission >= 15) {
    ?>
    <td id="tr_<?php echo $i;?>_3" width='50'><a href='javascript:void(0);' onclick="del_oroshi(<?php echo $col['dougyousya_id'];?>, '<?php echo $ocertify->npermission;?>')"><?php echo CLEATE_DOUGYOUSYA_DEL;?></a></td>
    <?php
    } else {
    ?>
    <td id="tr_<?php echo $i;?>_3" width='50'>&nbsp;</td>
    <?php
    }
    ?>
    <td id="tr_<?php echo $i;?>_4" width='50'><a href='history.php?action=dougyousya&dougyousya_id=<?php echo $col['dougyousya_id'];?>'><?php echo CLEATE_DOUGYOUSYA_HISTORY;?></a>
  <td width='50' align="right"><a href='cleate_dougyousya.php?action=select_oroshi&id=<?php echo
  $col['dougyousya_id'];?>'><?php if($col['dougyousya_id'] == $HTTP_GET_VARS['id']){echo tep_image(DIR_WS_IMAGES.
      'icon_arrow_right.gif');}else{ echo tep_image(DIR_WS_IMAGES . 'icon_info.gif');}?></a></td>
  </tr>
<?php if(isset($ckstr)&&$orrshi_id == $col['dougyousya_id']){?>
  <tr>
    <td colspan ='5'>
      <div id="change_one">
      <input type='text' id='name_<?php echo $col['dougyousya_id'];?>'name='up_oroshi[<?php echo $col['dougyousya_id'];?>]' value='<?php echo $col['dougyousya_name'];?>'>
      <input type="hidden" id="src_name_<?php echo $col['dougyousya_id'];?>" value='<?php echo $col['dougyousya_name'];?>'><br>
      <?php echo makeCheckbox($categories_subtree,$ckstr);?>

      </div>
      <a href="javascript:void(0);"><?php echo tep_html_element_button(IMAGE_UPDATE, 'onclick="w_close(\''.$ocertify->npermission.'\', \'0\');"');?></a>
      <input type = "button" value = "<?php echo IMAGE_CANCEL;?>" onclick="resset_cb()" style="margin:2px"><br />
    </td>
  </tr>
<?php 
  }
  $i++;
}
?>
<tr>
<td  colspan ='5'>
 <div id="o_input"></div> 
  </td>
</tr>
</table>
</td>
<td width="25%" valign="top" class="right_column_boxes">
<table width="100%" cellspacing="0" cellpadding="2" border="0">
 <tr class="infoBoxHeading">
    <td class="infoBoxHeading">
    <?php 
    $oroshi_query = tep_db_query("select * from set_dougyousya_names where   dougyousya_id = '".$HTTP_GET_VARS['id']."'");
    $oroshi = tep_db_fetch_array($oroshi_query);
    echo '<table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr><td class="infoBoxContent"
    align="left">'.$oroshi['dougyousya_name'].'</td></tr>';
    if(tep_not_null($oroshi['user_added'])){
    echo '<tr><td>'.TEXT_USER_ADDED.'&nbsp;'.$oroshi['user_added'].'</td></tr>';
    }else{
    echo '<tr><td>'.TEXT_USER_ADDED.'&nbsp;'.TEXT_UNSET_DATA.'</td></tr>';
    }if(tep_not_null(tep_datetime_short($oroshi['date_added']))){
    echo '<tr><td>'.TEXT_DATE_ADDED.'&nbsp;'.$oroshi['date_added'].'</td></tr>';
    }else{
    echo '<tr><td>'.TEXT_DATE_ADDED.'&nbsp;'.TEXT_UNSET_DATA.'</td></tr>';
    }if(tep_not_null($oroshi['user_update'])){
    echo '<tr><td>'.TEXT_USER_UPDATE.'&nbsp;'.$oroshi['user_update'].'</td></tr>';
    }else{
    echo '<tr><td>'.TEXT_USER_UPDATE.'&nbsp;'.TEXT_UNSET_DATA.'</td></tr>';
    }if(tep_not_null(tep_datetime_short($oroshi['date_update']))){
    echo '<tr><td>'.TEXT_DATE_UPDATE.'&nbsp;'.$oroshi['date_update'].'</td></tr>';
    }else{
    echo '<tr><td>'.TEXT_DATE_UPDATE.'&nbsp;'.TEXT_UNSET_DATA.'</td></tr>';
    }
    echo '</table>';
    ?></td>
  </tr>
</table>
</td>
</tr>
</table>
<div id="oo_input" style="display:none">
<?php
    echo makeCheckbox($categories_subtree); 
?>
</div>
<a href="javascript:void(0);"><?php echo tep_html_element_button(CLEATE_DOUGYOUSYA_LOGIN, 'onclick="w_close(\''.$ocertify->npermission.'\', \'0\');"');?></a>
<a href="javascript:void(0);"><?php echo tep_html_element_button(CLEATE_DOUGYOUSYA_UPDATE_SORT, 'onclick="w_close(\''.$ocertify->npermission.'\', \'1\');"');?></a>
<div id="h_sort"></div>
</form>
                          </td>
                       </tr>
                    </table>      
                 </td>
              </tr>
           </table>
           </div>
           </div>
        </td>
      </tr>
  </table>
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <br>
  </body>
  </html>
  <?php require(DIR_WS_INCLUDES . 'application_bottom.php');?>
