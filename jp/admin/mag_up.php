<?php
  require('includes/application_top.php');
  require("includes/jcode.phps");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title><?php echo MAG_UP_TITLE_TEXT; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
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
    $filename = isset($HTTP_POST_FILES['products_csv']['name'])?$HTTP_POST_FILES['products_csv']['name']:'';
    if(substr($filename, strrpos($filename,".")+1)!="csv") $chk_csv = false;
     
    // ファイル名の参照チェック
    if(isset($HTTP_POST_FILES['products_csv']['tmp_name']) && $HTTP_POST_FILES['products_csv']['tmp_name']!="" && $chk_csv){
    $file = fopen($products_csv,"r");
  
  //SQLを空にする
  //mysql_query("TRUNCATE TABLE mail_magazine");
  mysql_query("delete from ".TABLE_MAIL_MAGAZINE." where site_id = '".(int)$_POST['site_id']."'");
  
  $cnt = "0"; 
  $chk_input = true;
  $cnt_insert=0;
  
  echo '<P>';
  while($dat = fgetcsv($file,10000,',')){
    // １行目がフィールド名のとき、２行目から読む
    if(!ereg("@", $dat[1])) $dat = fgetcsv($file,10000,',');
    
    // EUCに変換
    for($e=0;$e<count($dat);$e++){
      $dat[$e] = addslashes(jcodeconvert($dat[$e],"0","1"));
    }
    
    if($chk_input){
      
      //インサート
      //if(!empty($dat[1]) && !empty($dat[2])) {
      if(!empty($dat[1])) {
            
        $dat0 = tep_db_prepare_input($dat[0]);
        $dat1 = tep_db_prepare_input($dat[1]);
        $dat2 = tep_db_prepare_input($dat[2]);
        
        $updated = false;
        
        //顧客情報のテーブル参照
        $ccnt_query = tep_db_query("select count(*) as cnt from customers where customers_email_address = '".$dat1."' and site_id = '".$_POST['site_id']."'");
        $ccnt = tep_db_fetch_array($ccnt_query);
        
        if($ccnt['cnt'] > 0) {
          //Update
        tep_db_query("update customers set customers_newsletter = '1' where customers_email_address = '".$dat1."'");
        $updated = true;
        }
        
        //--------------------------------------
        //mail_magazine Update
        if($updated == false) {
          //重複チェック
          $jcnt_query = tep_db_query("select count(*) as cnt from mail_magazine where mag_email = '".$dat1."' and site_id = '".(int)$_POST['site_id']."'");
          $jcnt = tep_db_fetch_array($jcnt_query);
        
          //インサート（重複なし）
          if($jcnt['cnt'] == 0) {
          tep_db_query("insert into mail_magazine (mag_email, mag_name, site_id) values ('".$dat1."', '".$dat2."', '".(int)$_POST['site_id']."')");
          } 
        
          //アップデート（重複有り）
          else {
          tep_db_query("update mail_magazine set mag_name = '".$dat2."' where mag_email = '".$dat1."' where site_id = '".(int)$_POST['site_id']."'");
          }
        }
        $cnt++;
      }
      
        
      if( ($cnt % 200) == 0 ){
        echo "・";
        Flush();
      }
      }
  }
    echo '</P>';
    fclose($file);
    echo "<font color='#CC0000'><b>".$cnt.NUMBERS_UP."</b></font>";
  }else{
    echo "<font color='#CC0000'><b>".UNABLE_UP."<br>".REFERENCE_CSV."</b></font>";
  }
  
  echo '<br><br><br><a href="mag_up.php">'.BUTTON_BACK.'</a>';
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
<td width="100%" valign="top"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="0">
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
          <tr><FORM method="POST" action="mag_up.php?action=upload" enctype="multipart/form-data">
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
	      <td colspan="2" align="left"><input type=submit name=download value="<?php echo BUTTON_MAG_UP;?>"></td>
              </tr>
            </table></td>
      <input type="hidden" name="max_file_size" value="1000000">
          </form></tr>


<!-- body_text_eof //-->
        </table></td>
      </tr>
    </table>
    </div> 
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
<?php 
  }
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
