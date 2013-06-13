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
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<?php
  if (isset($_GET['action']) && $_GET['action'] == 'upload'){
    /*
    $dat[0] => ID
    $dat[1] => 邮件地址
    $dat[2] => 姓名
  */
    // CSV文件检查
    $chk_csv = true;
    $filename = isset($HTTP_POST_FILES['products_csv']['name'])?$HTTP_POST_FILES['products_csv']['name']:'';
    if(substr($filename, strrpos($filename,".")+1)!="csv") $chk_csv = false;
     
    // 文件名参考检查
    if(isset($HTTP_POST_FILES['products_csv']['tmp_name']) && $HTTP_POST_FILES['products_csv']['tmp_name']!="" && $chk_csv){
    $file = fopen($products_csv,"r");
  
  //SQL弄成空
  mysql_query("delete from ".TABLE_MAIL_MAGAZINE." where site_id = '".(int)$_POST['site_id']."'");
  
  $cnt = "0"; 
  $chk_input = true;
  $cnt_insert=0;
  
  echo '<P>';
  while($dat = fgetcsv($file,10000,',')){
    // 第一行是字段名的时候，从第二行开始读取
    if(!ereg("@", $dat[1])) $dat = fgetcsv($file,10000,',');
    
    // 转换成EUC
    for($e=0;$e<count($dat);$e++){
      $dat[$e] = addslashes(jcodeconvert($dat[$e],"0","1"));
    }
    
    if($chk_input){
      
      //插入
      if(!empty($dat[1])) {
            
        $dat0 = tep_db_prepare_input($dat[0]);
        $dat1 = tep_db_prepare_input($dat[1]);
        $dat2 = tep_db_prepare_input($dat[2]);
        
        $updated = false;
        
        //参考顾客信息表
        $ccnt_query = tep_db_query("select count(*) as cnt from customers where customers_email_address = '".$dat1."' and site_id = '".$_POST['site_id']."'");
        $ccnt = tep_db_fetch_array($ccnt_query);
        
        if($ccnt['cnt'] > 0) {
          //Update
        tep_db_query("update customers set customers_newsletter = '1' where customers_email_address = '".$dat1."'");
        $updated = true;
        }
        
        //mail_magazine Update
        if($updated == false) {
          //重复检查
          $jcnt_query = tep_db_query("select count(*) as cnt from mail_magazine where mag_email = '".$dat1."' and site_id = '".(int)$_POST['site_id']."'");
          $jcnt = tep_db_fetch_array($jcnt_query);
        
          //插入（不重复）
          if($jcnt['cnt'] == 0) {
          tep_db_query("insert into mail_magazine (mag_email, mag_name, site_id) values ('".$dat1."', '".$dat2."', '".(int)$_POST['site_id']."')");
          } 
        
          //更新（有重复）
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
    echo "<font color='#CC0000' size='2'>".$cnt.NUMBERS_UP."</font>";
  }else{ 
    $image_upload_error = "<font color='#CC0000' size='2'>".UNABLE_UP."<br>".REFERENCE_CSV."</font>";
    ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="0">
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
                <td colspan="2"><input type=file name=products_csv size=50></td>
              </tr>
              <tr>
                <td><?php echo $image_upload_error; ?></td>
              </tr>
              <tr>
	      <td colspan="2" align="left"><input type=submit name=download value="<?php echo BUTTON_MAG_UP;?>"></td>
              </tr>
            </table></td>
      <input type="hidden" name="max_file_size" value="1000000">
          </form></tr>
<!-- body_text_eof -->
        </table></td>
      </tr>
    </table>
    </div> 
    </div>
    </td>
  </tr>
</table>
<!-- body_eof -->
<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>

<?php 
   }
  } else {
?>
<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="0">
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
                <td colspan="2"><input type=file name=products_csv size=50></td>
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


<!-- body_text_eof -->
        </table></td>
      </tr>
    </table>
    </div> 
    </div>
    </td>
  </tr>
</table>
<!-- body_eof -->


<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php 
  }
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
