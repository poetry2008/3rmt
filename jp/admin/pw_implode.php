<?php
  require('includes/application_top.php');
  require("includes/jcode.phps");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<?php
  if (isset($_GET['action']) && $_GET['action'] == 'upload'){
    /*
    $dat[0] => ID
    $dat[1] => メールアドレス
    $dat[2] => 姓名
  */
    // CSVファイルのチェック
    $chk_csv = true;
    $filename = isset($_FILES['products_csv']['name'])?$_FILES['products_csv']['name']:'';
    if(substr($filename, strrpos($filename,".")+1)!="csv") $chk_csv = false;
     
    // ファイル名の参照チェック
    if(isset($_FILES['products_csv']['tmp_name']) && $_FILES['products_csv']['tmp_name']!="" && $chk_csv){
    $str = file_get_contents($products_csv);
    $row_arr = explode(";",$str);
  
  //SQLを空にする
  //mysql_query("TRUNCATE TABLE mail_magazine");
  
  $cnt = "0"; 
  $chk_input = true;
  $cnt_insert=0;
  echo '<P>';
  foreach($row_arr as $row){
    
    $dat = explode(',',$row);
    // EUCに変換
      //インサート
      //if(!empty($dat[1]) && !empty($dat[2])) {
        $dat0 = tep_db_prepare_input($dat[0])?tep_db_prepare_input($dat[0]):'1';
        $dat1 = tep_db_prepare_input($dat[1]);
        $dat2 = tep_db_prepare_input($dat[2]);
        $dat3 = tep_db_prepare_input($dat[3]);
        $dat4 = tep_db_prepare_input($dat[4]);
        $dat5 = tep_db_prepare_input($dat[5]);
        $dat6 = tep_db_prepare_input($dat[6]);
        $dat7 = tep_db_prepare_input($dat[7]);
  $sql_insert = "insert into ".TABLE_IDPW."(`id`, `title`, `priority`, `site_id`,
    `url`, `loginurl`, `username`, `password`, `comment`, `memo`, `nextdate`,
    `privilege`, `self`, `operator`, `update_user`, `created_at`, `updated_at`,
    `onoff`, `date_order`) VALUES (null,'".$dat3."','".$dat0."','0','".$dat6."','".
      $dat7."','".$dat1."','".$dat2."','".$dat4."','".$dat5.
      "',now(),'7','','岡川  美恵','岡川  美恵',now(),now(),'1',now())";
      tep_db_query($sql_insert);
        $cnt++;
      if( ($cnt % 200) == 0 ){
        echo "・";
        Flush();
      }
  }
    echo '</P>';
    echo "<font color='#CC0000'><b>".$cnt."件をアップロードしました。</b></font>";
  }else{
    echo "<font color='#CC0000'><b>アップロードできませんでした。<br>所定のCSVファイルを参照してください。</b></font>";
  }
  
  echo '<br><br><br><a href="mag_up.php">戻る</a>';
  } else {
?>
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>

            <td class="pageHeading"><?php echo MAG_UP_TITLE_TEXT;?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr><FORM method="POST" action="pw_implode.php?action=upload" enctype="multipart/form-data">
            <td><table border="0" cellpadding="0" cellspacing="2">
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_site_pull_down_menu('', false);?></td>
              </tr>
              <tr>
                <td colspan="2" align="right"><input type=file name=products_csv size=50></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="left"><input type=submit name=download value="アップロード"></td>
              </tr>
            </table></td>
      <input type="hidden" name="max_file_size" value="1000000">
          </form></tr>


<!-- body_text_eof //-->
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- body_eof //-->


<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php 
  }
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
