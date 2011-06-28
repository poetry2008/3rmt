<?php
/*
  $Id$
*/

require('includes/application_top.php');

//年月日の取得
  $today = getdate();
  $yyyy = $today['year'];
  $mm = $today['mon'];
  $dd = $today['mday'];
  $pd = $dd + 1;

//ファイルの拡張子を取得
  function GetExt($filepath){
    $f = strrev($filepath);
    $ext = substr($f,0,strpos($f,"."));
    return strrev($ext);
  }

//登録処理
  if(isset($_GET['action']) && $_GET['action'] == 'insert'){

    $site_id  = tep_db_prepare_input($_POST['site_id']);
    $ins_title = tep_db_prepare_input($_POST['title']);

    if($_FILES['file']['tmp_name'] != ""){
      $filepath = tep_get_upload_dir($site_id) ."present/"."image".".".date("YmdHis").".".GetExt($_FILES['file']['name']);
      $filepath2 = "image".".".date("YmdHis").".".GetExt($_FILES['file']['name']);
      move_uploaded_file($_FILES['file']['tmp_name'],$filepath);
    }else{
      $filepath2 = "";
    }

    $ins_ht   = tep_db_prepare_input($_POST['ht']);
    $ins_text = addslashes($_POST['text']);
    $ins_period1 = tep_db_prepare_input($_POST['start_y']).tep_db_prepare_input($_POST['start_m']).tep_db_prepare_input($_POST['start_d']);
    $ins_period2 = tep_db_prepare_input($_POST['limit_y']).tep_db_prepare_input($_POST['limit_m']).tep_db_prepare_input($_POST['limit_d']);

    $ins = "insert into ".TABLE_PRESENT_GOODS."(
              title, 
              html_check, 
              image, 
              text, 
              start_date, 
              limit_date,
              site_id
            ) values (
              '".$ins_title."',
              '".$ins_ht."',
              '".$filepath2."',
              '".$ins_text."',
              '".$ins_period1."',
              '".$ins_period2."',
              '".$site_id."')";

          
    $mess = mysql_query($ins) or die("データ追加エラー");
    if($mess == true){
    }
    header("location: present.php");
  }

//更新処理
  if(isset($_GET['action']) && $_GET['action'] == 'update'){
    $up_id = tep_db_prepare_input($_GET['cID']);
    $up_ht = tep_db_prepare_input($_POST['ht']);
    $up_title = tep_db_prepare_input($_POST['title']);
    $present = tep_get_present_by_id($_GET['cID']);
  $site_id=$present['site_id'];
    if(!$site_id) $site_id=0;
   if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];//权限判断
         else $site_arr="";
   forward401Unless(editPermission($site_arr, $site_id));
    if($_FILES['file']['tmp_name'] != ""){
      $filepath = tep_get_upload_dir($present['site_id'])."present/"."image".".".date("YmdHis").".".GetExt($_FILES['file']['name']);
      $filepath2 = "image".".".date("YmdHis").".".GetExt($_FILES['file']['name']);
      move_uploaded_file($_FILES['file']['tmp_name'],$filepath);
    }else{
      $filepath2 = "";
    }

    $up_text = addslashes($_POST['text']);
    $up_period1 = tep_db_prepare_input($_POST['start_y']).tep_db_prepare_input($_POST['start_m']).tep_db_prepare_input($_POST['start_d']);
    $up_period2 = tep_db_prepare_input($_POST['limit_y']).tep_db_prepare_input($_POST['limit_m']).tep_db_prepare_input($_POST['limit_d']);

      if($filepath2 == ""){
      $up = "update ".TABLE_PRESENT_GOODS." set title='".$up_title."', html_check='".$up_ht."', text='".$up_text."',start_date='".$up_period1."',limit_date='".$up_period2."' where goods_id='".$up_id."'";
      }else{
      $up = "update ".TABLE_PRESENT_GOODS." set title='".$up_title."', html_check='".$up_ht."', image='".$filepath2."',text='".$up_text."',start_date='".$up_period1."',limit_date='".$up_period2."' where goods_id='".$up_id."'";
      }
    $mess = mysql_query($up) or die("データ追加エラー");
      if($mess == true){
      }
    header("location: present.php");
  }

//削除処理
  if(isset($_GET['action']) && $_GET['action'] == 'delete'){
    $dele_id = tep_db_prepare_input($_GET['cID']);

    $dele = "delete from ".TABLE_PRESENT_GOODS." where goods_id = '".$dele_id."'";
    $mess = mysql_query($dele) or die("データ追加エラー");
      if($mess == true){
      }
    header("location: present.php");
  }
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<script language="javascript">
function msg(){
  if(document.apply.title.value == ""){
    alert("タイトルを入力して下さい");
    document.apply.title.focus();
    return false;
  }
  if(document.apply.text.value == ""){
    alert("本文を入力して下さい");
    document.apply.text.focus();
    return false;
  }
  if((document.apply.start_y.value + document.apply.start_m.value + document.apply.start_d.value) > (document.apply.limit_y.value + document.apply.limit_m.value +document.apply.limit_d.value)){
    alert("開始日＞終了日になっています");
    document.apply.start_y.focus();
    return false;
  }
}

function msg2(){
  if(document.view.title.value == ""){
    alert("タイトルを入力して下さい");
    document.view.title.focus();
    return false;
  }
  if(document.view.text.value == ""){
    alert("本文を入力して下さい");
    document.view.text.focus();
    return false;
  }
  if((document.view.start_y.value + document.view.start_m.value + document.view.start_d.value) > (document.view.limit_y.value + document.view.limit_m.value +document.view.limit_d.value)){
    alert("開始日＞終了日になっています");
    document.view.start_y.focus();
    return false;
  }
}
</script>
</head>
<body>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </table></td>
    <!-- body_text //-->
    <td width="100%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
        <tr>
          <td><!-- insert -->
            <?php
switch(isset($_GET['action'])?$_GET['action']:''){
case 'input' :
?>
            <form onSubmit="return msg()" name="apply" action="present.php?action=insert" method="post" enctype="multipart/form-data">
              <table width="100%" align="center" border="0" cellspacing="0" cellpadding="8">
                <tr>
                  <td valign="middle" class="pageHeading" height="10"><?php echo PRESENT_CREATE_TITLE;?></td>
                  <td align="right" valign="top">&nbsp;</td>
                </tr>
                <tr>
                  <td colspan="2"><p>&nbsp;</p>
                    <table width="780" cellpadding="0" cellspacing="0">
                      <tr>
                        <td><table width="100%"  border="0" cellspacing="1" cellpadding="3">
                            <tr>
                              <td class="main" width="150" bgcolor="#FFFFFF"><?php echo ENTRY_SITE;?></td>
                              <td class="main" width="630" bgcolor="#FFFFFF"><?php echo tep_site_pull_down_menu();?></td>
                            <tr>
                              <td class="main" width="150" bgcolor="#FFFFFF"><?php echo PRESENT_NAME_TEXT;?></td>
                              <td class="main" width="630" bgcolor="#FFFFFF"><input name="title" type="text"></td>
                            </tr>
                            <tr>
                              <td class="main" width="150" bgcolor="#FFFFFF"><?php echo PRESENT_PIC_TEXT;?></td>
                              <td class="main" width="630" bgcolor="#FFFFFF"><input type="file" name="file"></td>
                            </tr>
                            <tr>
                              <td class="main" valign="top" bgcolor="#FFFFFF"><?php echo PRESENT_COMMENT_TEXT;?></td>
                              <td class="main" bgcolor="#FFFFFF">
                <input type="checkbox" name="ht" value="1"><?php echo PRESENT_HTML_READ;?>
                                <textarea name="text" style="width:95%; height:100px;"></textarea></td>
                            </tr>
                            <tr>
                              <td class="main" valign="top" bgcolor="#FFFFFF"><?php echo PRESENT_DATE_TEXT;?></td>
                              <td class="main" bgcolor="#FFFFFF">
                              <?php echo KEYWORDS_SEARCH_START_TEXT;?>   
                              <select name="start_y">
                                  <?php 
          for($y=0;$y<=5;$y++){
            $nen = $yyyy + $y;
            if($yyyy == $nen){
              echo "<option value=\"$nen\" selected>$nen</option>"."\n";
            }else{
              echo "<option value=\"$nen\">$nen</option>"."\n";
            }
          }
          ?>
                                </select>
                                <?php echo YEAR_TEXT;?> 
                                <select name="start_m">
                                  <?php
          for($m=1;$m<=9;$m++){
            if($mm == $m){
              echo "<option value=\"0$m\" selected>$m</option>"."\n";
            }else{
              echo "<option value=\"0$m\">$m</option>"."\n";
            }
          }
          for($m=10;$m<=12;$m++){
            if($mm == $m){
              echo "<option value=\"$m\" selected>$m</option>"."\n";
            }else{
              echo "<option value=\"$m\">$m</option>"."\n";
            }
          }
          ?>
                                </select>
                                <?php echo MONTH_TEXT;?> 
                                <select name="start_d">
                                  <?php
          for($d=1;$d<=9;$d++){
            if($dd == $d){
              echo "<option value=\"0$d\" selected>$d</option>"."\n";
            }else{
              echo "<option value=\"0$d\">$d</option>"."\n";
            }
          }
          for($d=10;$d<=31;$d++){
            if($dd == $d){
              echo "<option value=\"$d\" selected>$d</option>"."\n";
            }else{
              echo "<option value=\"$d\">$d</option>"."\n";
            }
          }
          ?>
                                </select>
                                <?php echo DAY_TEXT;?> 
                                <br>
                                <?php echo KEYWORDS_SEARCH_END_TEXT;?>   
                                <select name="limit_y">
                                  <?php
          for($y=0;$y<=5;$y++){
            $nen = $yyyy + $y;
            if($yyyy == $nen){
              echo "<option value=\"$nen\" selected>$nen</option>"."\n";
            }else{
              echo "<option value=\"$nen\">$nen</option>"."\n";
            }
          }
          ?>
                                </select>
                                <?php echo YEAR_TEXT;?> 
                                <select name="limit_m">
                                  <?php
          for($m=1;$m<=9;$m++){
            if($mm == $m){
              echo "<option value=\"0$m\" selected>$m</option>"."\n";
            }else{
              echo "<option value=\"0$m\">$m</option>"."\n";
            }
          }
          for($m=10;$m<=12;$m++){
            if($mm == $m){
              echo "<option value=\"$m\" selected>$m</option>"."\n";
            }else{
              echo "<option value=\"$m\">$m</option>"."\n";
            }
          }
          ?>
                                </select>
                                <?php echo MONTH_TEXT;?> 
                                <select name="limit_d">
                                  <?php
          for($d=1;$d<=9;$d++){
            if($pd == $d){
              echo "<option value=\"0$d\" selected>$d</option>"."\n";
            }else{
              echo "<option value=\"0$d\">$d</option>"."\n";
            }
          }
          for($d=10;$d<=31;$d++){
            if($pd == $d){
              echo "<option value=\"$d\" selected>$d</option>"."\n";
            }else{
              echo "<option value=\"$d\">$d</option>"."\n";
            }
          }
          ?>
                                </select>
                                <?php echo DAY_TEXT;?> 
                                </td>
                            </tr>
                          </table></td>
                      </tr>
                    </table>
                    <div align="right">
            <a class="new_product_reset" href=
            <?php echo tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('action'))); ?>
            ><?php echo tep_html_element_button('戻る'); ?></a>
            <?php echo tep_html_element_submit('保存'); ?>
                    </div></td>
                </tr>
              </table>
            </form>
            <?php
break;
case 'view' :
$sele1_id = (int)$_GET['cID'];
$sele1 = tep_db_query("
    select g.goods_id,
           g.html_check,
           g.title,
           g.image,
           g.text,
           g.start_date,
           g.limit_date,
           g.site_id,
           s.romaji,
           s.name as site_name
    from ".TABLE_PRESENT_GOODS." g, ".TABLE_SITES." s
    where g.goods_id = '".$sele1_id."'
      and g.site_id = s.id
    ");
$sql1 = tep_db_fetch_array($sele1);
//画像
//$pic = HTTP_CATALOG_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . "present/".$sql1['image'];
//期間
$sele_sty = substr($sql1['start_date'],0,4);
$sele_stm = substr($sql1['start_date'],5,2);
$sele_std = substr($sql1['start_date'],8,2);
$sele_liy = substr($sql1['limit_date'],0,4);
$sele_lim = substr($sql1['limit_date'],5,2);
$sele_lid = substr($sql1['limit_date'],8,2);

?>
            <form onSubmit="return msg2()" name="view" method="post"
            action="present.php?action=update&cID=<?php echo $sele1_id;?>" enctype="multipart/form-data">
              <table width="100%" align="center" border="0" cellspacing="0" cellpadding="8">
                <tr>
                  <td valign="middle" class="pageHeading" height="10"><?php echo PRESENT_UPDATE_TITLE;?></td>
                  <td align="right" valign="top">&nbsp;</td>
                </tr>
                <tr>
                  <td colspan="2"><p>&nbsp;</p>
                    <table width="780" cellpadding="0" cellspacing="0">
                      <tr>
                        <td><table width="100%"  border="0" cellspacing="1" cellpadding="3">
                            <tr>
                              <td class="main" width="150" bgcolor="#FFFFFF"><?php echo ENTRY_SITE;?></td>
                              <td class="main" width="630" bgcolor="#FFFFFF"><?php echo $sql1['site_name'] ;?></td>
                            </tr>
                            <tr>
                              <td class="main" width="150" bgcolor="#FFFFFF"><?php echo PRESENT_NAME_TEXT;?></td>
                              <td class="main" width="630" bgcolor="#FFFFFF"><input name="title" type="text" value="<?php echo $sql1['title'] ;?>"></td>
                            </tr>
                            <tr>
                              <td class="main" width="150" bgcolor="#FFFFFF"><?php echo PRESENT_PIC_TEXT;?></td>
                              <td class="main" width="630" bgcolor="#FFFFFF">
                <?php 
                if($sql1['image']){
                ?>
                  <?php echo tep_info_image('present/'.$sql1['image'], $sql1['title'],'','',$sql1['site_id']) ; ?> <br>
                <?php
                }
                ?>
                                <input type="file" name="file"></td>
                            </tr>
                            <tr>
                              <td class="main" valign="top" bgcolor="#FFFFFF"><?php echo PRESENT_COMMENT_TEXT;?></td>
                              <td class="main" bgcolor="#FFFFFF">
                <?php
                if($sql1['html_check'] == 1){
                  echo '<input type="checkbox" name="ht" value="1" checked>HTMLを許可する'."\n";
                  echo '<textarea name="text" style="width:95%; height:300px;">'.stripslashes($sql1['text']).'</textarea>'."\n";
                }else{
                  echo '<input type="checkbox" name="ht" value="1">HTMLを許可する'."\n";
                  echo '<textarea name="text" style="width:95%; height:300px;">'.stripslashes($sql1['text']).'</textarea>'."\n";
                }
                ?>
                </td>
                            </tr>
                            <tr>
                              <td class="main" valign="top" bgcolor="#FFFFFF"><?php echo PRESENT_DATE_TEXT;?></td>
                              <td class="main" bgcolor="#FFFFFF">
                              <?php echo KEYWORDS_SEARCH_START_TEXT;?>   
                              <select name="start_y">
                                  <?php
          for($y=0;$y<=5;$y++){
            $nen = $yyyy + $y;
            if($sele_sty == $nen){
              echo "<option value=\"$nen\" selected>$nen</option>"."\n";
            }else{
              echo "<option value=\"$nen\">$nen</option>"."\n";
            }
          }
          ?>
                                </select>
                                <?php echo YEAR_TEXT;?> 
                                <select name="start_m">
                                  <?php
          for($m=1;$m<=9;$m++){
            if($sele_stm == $m){
              echo "<option value=\"0$m\" selected>$m</option>"."\n";
            }else{
              echo "<option value=\"0$m\">$m</option>"."\n";
            }
          }
          for($m=10;$m<=12;$m++){
            if($sele_stm == $m){
              echo "<option value=\"$m\" selected>$m</option>"."\n";
            }else{
              echo "<option value=\"$m\">$m</option>"."\n";
            }
          }
          ?>
                                </select>
                                <?php echo MONTH_TEXT;?> 
                                <select name="start_d">
                                  <?php
          for($d=1;$d<=9;$d++){
            if($sele_std == $d){
              echo "<option value=\"0$d\" selected>$d</option>"."\n";
            }else{
              echo "<option value=\"0$d\">$d</option>"."\n";
            }
          }
          for($d=10;$d<=31;$d++){
            if($sele_std == $d){
              echo "<option value=\"$d\" selected>$d</option>"."\n";
            }else{
              echo "<option value=\"$d\">$d</option>"."\n";
            }
          }
          ?>
                                </select>
                                <?php echo DAY_TEXT;?> 
                <br>
                              <?php echo KEYWORDS_SEARCH_END_TEXT;?>   
                                <select name="limit_y">
                                  <?php
          for($y=0;$y<=5;$y++){
            $nen = $yyyy + $y;
            if($sele_liy == $nen){
              echo "<option value=\"$nen\" selected>$nen</option>"."\n";
            }else{
              echo "<option value=\"$nen\">$nen</option>"."\n";
            }
          }
          ?>
                                </select>
                                <?php echo YEAR_TEXT;?> 
                                <select name="limit_m">
                                  <?php
          for($m=1;$m<=9;$m++){
            if($sele_lim == $m){
              echo "<option value=\"0$m\" selected>$m</option>"."\n";
            }else{
              echo "<option value=\"0$m\">$m</option>"."\n";
            }
          }
          for($m=10;$m<=12;$m++){
            if($sele_lim == $m){
              echo "<option value=\"$m\" selected>$m</option>"."\n";
            }else{
              echo "<option value=\"$m\">$m</option>"."\n";
            }
          }
          ?>
                                </select>
                                <?php echo MONTH_TEXT;?> 
                                <select name="limit_d">
                                  <?php
          for($d=1;$d<=9;$d++){
            if($sele_lid == $d){
              echo "<option value=\"0$d\" selected>$d</option>"."\n";
            }else{
              echo "<option value=\"0$d\">$d</option>"."\n";
            }
          }
          for($d=10;$d<=31;$d++){
            if($sele_lid == $d){
              echo "<option value=\"$d\" selected>$d</option>"."\n";
            }else{
              echo "<option value=\"$d\">$d</option>"."\n";
            }
          }
          ?>
                                </select>
                                <?php echo DAY_TEXT;?> 
                                </td>
                            </tr>
                          </table></td>
                      </tr>
                    </table>
                    <div align="right">
            <a href=
            <?php echo tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('action'))); ?>
            ><?php echo tep_html_element_button('戻る'); ?></a>
            <?php echo tep_html_element_submit('更新'); ?>
                    </div></td>
                </tr>
              </table>
            </form>
            <?php
break;
case 'listview' :
$lv_id = tep_db_prepare_input($_GET['list_id']);
$sele2 = tep_db_query("
    select * 
    from ".TABLE_PRESENT_APPLICANT." 
    where id = '".$lv_id."'");
$sql2 = tep_db_fetch_array($sele2);

?>
            <form name="listview" method="post">
              <table width="100%" align="center" border="0" cellspacing="0" cellpadding="8">
                <tr>
                  <td valign="middle" class="pageHeading" height="10"><?php echo PRESENT_CUSTOMER_TITLE?></td>
                  <td align="right" valign="top">&nbsp;</td>
                </tr>
                <tr>
                  <td colspan="2"><p>&nbsp;</p>
                    <table width="780" cellpadding="0" cellspacing="0">
                      <tr>
                        <td><table width="100%"  border="1" cellspacing="1" cellpadding="3">
                            <tr>
                              <td class="main" width="150" bgcolor="#FFFFFF">customer_id</td>
                              <td class="main" width="630" bgcolor="#FFFFFF"><?php echo $sql2['customer_id'] ;?></td>
                            </tr>
                            <tr>
                              <td class="main" width="150" bgcolor="#FFFFFF"><?php echo PRESENT_CUSTOMER_NAME;?></td>
                              <td class="main" width="630" bgcolor="#FFFFFF"><?php echo $sql2['family_name'] ;?><?php echo $sql2['first_name'] ;?></td>
                            </tr>
                            <tr>
                              <td class="main" width="150" bgcolor="#FFFFFF">e-mail</td>
                              <td class="main" width="630" bgcolor="#FFFFFF"><?php echo $sql2['mail'] ;?></td>
                            </tr>
                            <tr>
                              <td class="main" width="150" bgcolor="#FFFFFF"><?php echo PRESENT_CUSTOMER_ADDRESS;?></td>
                              <td class="main" width="630" bgcolor="#FFFFFF">〒<?php echo $sql2['postcode'] ;?><br>
                                <?php echo $sql2['prefectures'] ;?><?php echo $sql2['cities'] ;?><br>
                                <?php echo $sql2['address1'] ;?><br>
                                <?php echo $sql2['address2'] ;?></td>
                            </tr>
                            <tr>
                              <td class="main" width="150" bgcolor="#FFFFFF"><?php echo PRESENT_CUSTOMER_TEL;?></td>
                              <td class="main" width="630" bgcolor="#FFFFFF"><?php echo $sql2['phone'] ;?></td>
                            </tr>
                            <tr>
                              <td class="main" width="150" bgcolor="#FFFFFF"><?php echo PRESENT_CUSTOMER_APPLYDAY;?></td>
                              <td class="main" width="630" bgcolor="#FFFFFF"><?php echo $sql2['tourokubi'] ;?></td>
                            </tr>
                          </table>
              </td>
                      </tr>
                    </table>
          <div align="right">
            <a href=
            <?php echo tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('action')). '&action=list'); ?>
            ><?php echo tep_html_element_button('戻る'); ?></a>
                    </div>
          </td>
                </tr>
              </table>
            </form>
            <?php
break;
case 'list' :
$c_id = tep_db_prepare_input($_GET['cID']);
?>
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading"><?php echo HEADING_TITLE2; ?></td>
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr class="dataTableHeadingRow">
                      <td class="dataTableHeadingContent" align="center"><?php echo PRESENT_CUSTOMER_TABLE_SURNAME;?></td>
                      <td class="dataTableHeadingContent" align="center"><?php echo PRESENT_CUSTOMER_TABLE_NAME;?></td>
                      <td class="dataTableHeadingContent" align="center"><?php echo PRESENT_CUSTOMER_TABLE_APPLYDAY;?></td>
                      <td class="dataTableHeadingContent" align="center"><?php echo PRESENT_CUSTOMER_TABLE_OPERATE;?></td>
                    </tr>
                    <?php
    $search = '';
    $count = 0;
    $list_query_raw = "
      select p.id ,
             p.goods_id, 
             p.customer_id, 
             p.family_name, 
             p.first_name, 
             p.mail, 
             p.postcode, 
             p.prefectures, 
             p.cities, 
             p.address1, 
             p.address2, 
             p.phone, 
             p.tourokubi, 
             s.romaji
      from ".TABLE_PRESENT_APPLICANT." p , ".TABLE_SITES." s, ".TABLE_PRESENT_GOODS." g
      where p.goods_id='".$c_id."' 
        and g.goods_id = p.goods_id
        and s.id = g.site_id
      order by id";
    $list_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $list_query_raw, $list_query_numrows);
    $list_query = tep_db_query($list_query_raw);
    while ($list = tep_db_fetch_array($list_query)) {
      $count++;
      if ( ((isset($_GET['list_id']) && $list['id'] == $_GET['list_id']) || ((!isset($_GET['list_id']) || !$_GET['list_id']) && $count == 1)) ) {
      echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('list_id', 'action')) . 'list_id=' . $list['id'] . '&action=listview') . '\'">' . "\n";
      if(!isset($_GET['list_id']) || !$_GET['list_id']) {

        $list_id = $list['id'];
      }
      } else {
      echo '          <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('list_id')) . 'list_id=' . $list['id']) . '\'">' . "\n";
      }
  ?>
                    <td class="dataTableContent" align="center"><?php echo htmlspecialchars($list['family_name']); ?></td>
                      <td class="dataTableContent" align="center"><?php echo htmlspecialchars($list['first_name']); ?> </td>
                      <td class="dataTableContent" align="center"><?php echo htmlspecialchars($list['tourokubi']); ?> </td>
                      <td class="dataTableContent" align="right"><?php if ($list['id'] == $list_id) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_PRESENT, 'list_id=' . $list['id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>
&nbsp;</td>
                    </tr>
                    <?php
    }
  //While終了
  ?>
                    <tr>
                      <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                          <tr>
                            <td class="smallText" valign="top"><?php echo $list_split->display_count($list_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CONTENS); ?></td>
                            <td class="smallText" align="right"><?php echo $list_split->display_links($list_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                          </tr>
                        </table></td>
                    </tr>
                  </table></td>
                <?php
          if(isset($list_id) && $list_id && tep_not_null($list_id)) {
          $cquery = tep_db_query("
              select * 
              from ".TABLE_PRESENT_APPLICANT." 
              where id = '".$list_id."'");
          $cresult = tep_db_fetch_array($cquery);
          $c_title = $cresult['family_name'].$cresult['first_name'];
          } else {
          $c_title = '&nbsp;';
          }
          
          $heading = array();
          $present = array();
          switch ($_GET['action']) {
          default:
            if (isset($list_id) && $list_id && tep_not_null($list_id)) {
            $heading[] = array('text' => '<b>' . $c_title . '</b>');
        
            $present[] = array('align' => 'center', 'text' => '<br><br><a href="' .  tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('action','list_id')) . 'list_id=' .  $list_id  .'&action=listview') . '">' .  tep_html_element_button('表示') .'</a> <a href="' .  tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('action'))) . '">' . tep_html_element_button('戻る') . '</a>');
            }
            break;
          }
        
          if ( (tep_not_null($heading)) && (tep_not_null($present)) ) {
          echo '            <td width="25%" valign="top">' . "\n";
        
          $box = new box;
          echo $box->infoBox($heading, $present);
        
          echo '            </td>' . "\n";
          }
?>
                <?php
break;
default:

?>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                      <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                      <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
                    </tr>
                  </table></td>
              </tr>
              <tr>
                <td>
                <?php tep_site_filter(FILENAME_PRESENT);?>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                      <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                          <tr class="dataTableHeadingRow"  width="30">
                            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SITE;?></td>
                            <td class="dataTableHeadingContent">TITLE</td>
                            <td class="dataTableHeadingContent" align="center"><?php echo PRESENT_DATE_TEXT;?></td>
                            <td class="dataTableHeadingContent" align="center"><?php echo PRESENT_CUSTOMER_TABLE_OPERATE;?></td>
                          </tr>
                          <?php
    $search = '';
    $count = 0;
    $present_query_raw = "
      select g.goods_id,
             g.html_check,
             g.title,
             g.title,
             g.image,
             g.text,
             g.start_date,
             g.limit_date,
             s.romaji
      from ".TABLE_PRESENT_GOODS." g , ".TABLE_SITES." s
      where s.id = g.site_id
        " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and s.id = '" . intval($_GET['site_id']) . "' " : '') . "
      order by goods_id";
    $present_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $present_query_raw, $present_query_numrows);
    $present_query = tep_db_query($present_query_raw);
    while ($present = tep_db_fetch_array($present_query)) {

      $count++;
      if ( ((isset($cID) && $present['goods_id'] == $cID) || ((!isset($_GET['cID']) || !$_GET['cID']) && $count == 1)) ) {
        echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $present['goods_id'] . '&action=view') . '\'">' . "\n";
        
        if(!isset($_GET['cID']) || !$_GET['cID']) {
          $cID = $present['goods_id'];
        }
      } else {
        echo '          <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('cID')) . 'cID=' . $present['goods_id']) . '\'">' . "\n";
      }
?>
                          <td class="dataTableContent"><?php echo $present['romaji']; ?></td>
                          <td class="dataTableContent"><?php echo htmlspecialchars($present['title']); ?></td>
                            <td class="dataTableContent" align="center"><?php
            $st_date = htmlspecialchars($present['start_date']);
            $st_ymd = substr($st_date,0,4)."/".substr($st_date,5,2)."/".substr($st_date,8,2);
            $li_date = htmlspecialchars($present['limit_date']);
            $li_ymd = substr($li_date,0,4)."/".substr($li_date,5,2)."/".substr($li_date,8,2);
            
            echo "$st_ymd - $li_ymd"; 
            ?>
                            </td>
                            <td class="dataTableContent" align="right"><?php if ($present['goods_id'] == $cID) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_PRESENT, 'cID=' . $present['goods_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>                              &nbsp;</td>
                          </tr>
                          <?php
    }
//While終了
?>
                          <tr>
                            <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                                <tr>
                                  <td class="smallText" valign="top"><?php echo $present_split->display_count($present_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CONTENS); ?></td>
                                  <td class="smallText" align="right"><?php echo $present_split->display_links($present_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></td>
                                </tr>
                              </table></td>
                          </tr>
                          <tr>
                            <td><a href="present.php?action=input"><?php echo tep_html_element_button('新規'); ?></a></td>
                          </tr>
                        </table></td>
                      <?php
          if(isset($cID) && $cID && tep_not_null($cID)) {
          $cquery = tep_db_query("select * from ".TABLE_PRESENT_GOODS." where goods_id = '".$cID."'");
          $cresult = tep_db_fetch_array($cquery);
          $c_title = $cresult['title'];
          } else {
          $c_title = '&nbsp;';
          }
          
          $heading = array();
          $present = array();
          switch (isset($_GET['action'])?$_GET['action']:'') {
          case 'deleform':
            $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PRESENT . '</b>');
        
            $present = array('form' => tep_draw_form('present', FILENAME_PRESENT, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cID . '&action=delete'));
            $present[] = array('text' => TEXT_DELETE_INTRO . '<br><br><b>' . $c_title . '</b>');
            $present[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_submit(IMAGE_DELETE) . ' <a href="' .  tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cID) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
            break;
          default:
            if (isset($cID) && $cID && tep_not_null($cID)) {
            $heading[] = array('text' => '<b>' . $c_title . '</b>');
        
            $present[] = array('align' => 'center', 'text' => '<br><br><a href="' .  tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cID . '&action=view') . '">' .  tep_html_element_button(IMAGE_EDIT) . '</a> <a href="' .  tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cID . '&action=deleform') . '">' .  tep_html_element_button(IMAGE_DELETE) . '</a> <a href="' .  tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('cID', 'action','page')) . 'cID=' . $cID . '&action=list') . '">' .  tep_html_element_button('リスト') . '</a>' );
            }
            break;
          }
        
          if ( (tep_not_null($heading)) && (tep_not_null($present)) ) {
          echo '            <td width="25%" valign="top">' . "\n";
        
          $box = new box;
          echo $box->infoBox($heading, $present);
        
          echo '            </td>' . "\n";
          }
        ?>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
    <!-- body_text_eof //-->
  </tr>
</table>
<?php
break;
}
?>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
</body>
</html>
