<?php
/*
  $Id$
*/
  require("includes/application_top.php");
  
  function Jcode_EUCtoSJIS(&$str_EUC) {
    return $str_EUC;
    /*
  $str_SJIS = '';
  $b = unpack('C*', $str_EUC);
  $n = count($b);

  for ($i = 1; $i <= $n; ++$i) {
    $b1 = $b[$i];
    if ($b1 > 0x8E) {
      $b2 = $b[++$i];
      if ($b1 & 0x01) {
        $b1 >>= 1;
        if ($b1 < 0x6F) $b1 += 0x31; else $b1 += 0x71;
        if ($b2 > 0xDF) $b2 -= 0x60; else $b2 -= 0x61;
      } else {
        $b1 >>= 1;
        if ($b1 <= 0x6F) $b1 += 0x30; else $b1 += 0x70;
        $b2 -= 0x02;
      }
      $str_SJIS .= chr($b1).chr($b2);
    } elseif ($b1 == 0x8E) {
      $str_SJIS .= chr($b[++$i]);
    } else {
      $str_SJIS .= chr($b1);
    }
  }

  return $str_SJIS;
  */
  }
  
  
  if(isset($_POST['act']) && $_POST['act'] == 'export') {
    //CSVファイルネーム指定
    $filename = 'customer_' . date("YmdHis") . '.csv';

  //エラー対策
  $start = "0000-00-00 00:00:00";
  $end   = "0000-00-00 00:00:01";
  
  //指定範囲の取得
  if(!empty($_POST['s_y']) && !empty($_POST['s_m']) && !empty($_POST['s_d'])) {
      $s_y = $_POST['s_y'] ; //開始日　年
      $s_m = $_POST['s_m'] ; //開始日　月
      $s_d = $_POST['s_d'] ; //開始日　日
      $start = $s_y.'-'.$s_m.'-'.$s_d . ' 00:00:00';
  }
  
    if(!empty($_POST['e_y']) && !empty($_POST['e_m']) && !empty($_POST['e_d'])) {
    $e_y = $_POST['e_y'] ; //終了日　年
      $e_m = $_POST['e_m'] ; //終了日　月
      $e_d = $_POST['e_d'] ; //終了日　日
      $end = $e_y.'-'.$e_m.'-'.$e_d . ' 00:00:00';
  }
  
    ### Download Start #######################################
    header("Content-Type: application/force-download");
    header('Pragma: public');
    header('Content-Disposition: attachment; filename='.$filename);
        
        
  //HEADER
  $csv_header = 'アカウント作成日,性別,姓,名,生年月日,メールアドレス,会社名,郵便番号,都道府県,市区町村,住所1,住所2,国名,電話番号,FAX番号,メルマガ購読,ポイント';
  $csv_header = Jcode_EUCtoSJIS($csv_header);
  print chr(0xEF).chr(0xBB).chr(0xBF);
  print $csv_header."\r\n";
  
  //DATA
  $customers_query_row = "select c.customers_id, c.customers_gender, c.customers_firstname, c.customers_lastname, c.customers_dob, c.customers_email_address, c.customers_default_address_id, c.customers_telephone, c.customers_fax, c.customers_newsletter, c.point, ci.customers_info_date_account_created from ".TABLE_CUSTOMERS." c, ".TABLE_CUSTOMERS_INFO." ci where c.customers_id = ci.customers_info_id";
  if($start != "0000-00-00 00:00:00") {
    $customers_query_row .= " and ci.customers_info_date_account_created >= '" . $start . "'";
  }
  if($end != "0000-00-00 00:00:01") {
    $customers_query_row .= " and ci.customers_info_date_account_created <= '" . $end . "'";
  }
  $customers_query_row .= " order by ci.customers_info_date_account_created";
  
  //DATA PUT DB = >CSV
  $customers_query = tep_db_query($customers_query_row);
  if(tep_db_num_rows($customers_query)) {
    while($customers = tep_db_fetch_array($customers_query)) {
      //Get Addressbood default data
    $addressbook_query = tep_db_query("select * from ".TABLE_ADDRESS_BOOK." where customers_id = '".$customers['customers_id']."' and address_book_id = '".$customers['customers_default_address_id']."'");
    $addressbook = tep_db_fetch_array($addressbook_query);
    //アカウント作成日
    $account_add = str_replace("-", "/", $customers['customers_info_date_account_created']);
    print Jcode_EUCtoSJIS($account_add) . ',';
    
    //性別
    if($customers['customers_gender'] == 'm') {
      $gender = '男性';
    } else {
      $gender = '女性';
    }
    print Jcode_EUCtoSJIS($gender) . ',';
    
    //姓
    print Jcode_EUCtoSJIS($customers['customers_lastname']) . ',';
    
    //名
    print Jcode_EUCtoSJIS($customers['customers_firstname']) . ',';
    
    //生年月日
    print Jcode_EUCtoSJIS($customers['customers_dob']) . ',';
    
    //メールアドレス
    print Jcode_EUCtoSJIS($customers['customers_email_address']) . ',';
    
    //会社名
    print Jcode_EUCtoSJIS($addressbook['entry_company']) . ',';
    
    //郵便番号
    print Jcode_EUCtoSJIS($addressbook['entry_postcode']) . ',';
    
    //都道府県
    $zone = tep_get_zone_name($addressbook['entry_zone_id']);
    print Jcode_EUCtoSJIS($zone) . ',';
    
    //市区町村
    print Jcode_EUCtoSJIS($addressbook['entry_city']) . ',';
    
    //住所1
    print Jcode_EUCtoSJIS($addressbook['entry_street_address']) . ',';
    
    //住所2
    print Jcode_EUCtoSJIS($addressbook['entry_suburb']) . ',';
    
    //国名
    $country = tep_get_country_name($addressbook['entry_country_id']);
    print Jcode_EUCtoSJIS($country) . ',';
    
    //電話番号
    print Jcode_EUCtoSJIS($customers['customers_telephone']) . ',';
    
    //FAX番号
    print Jcode_EUCtoSJIS($customers['customers_fax']) . ',';
    
    //メルマガ購読
    if($customers['customers_newsletter'] == '0') {
      $mag = "未購読";
    } else {
      $mag = "購読";
    }
    print Jcode_EUCtoSJIS($mag) . ',';
    
    //ポイント
    print Jcode_EUCtoSJIS($customers['point']);
    
    //改行
    print "\r\n";
    }
  }
  exit();
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
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
        <tr> 
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td class="pageHeading"><?php echo CUSTOMERS_CSVEXE_TITLE;?></td> 
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr> 
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td> 
        </tr> 
        <tr> 
          <td> <form action="<?php echo tep_href_link('customers_csv_exe.php','',SSL) ; ?>" method="post"> 
              <fieldset> 
              <legend class="smallText"><b><?php echo CUSTOMERS_CSVEXE_TITLE;?></b></legend> 
              <table  border="0" cellpadding="0" cellspacing="2"> 
                <tr> 
                  <td class="main" height="35" style="padding-left:20px; "> <p><?php echo CUSTOMERS_CSVEXE_READ_TEXT;?></p></td> 
                </tr> 
                <tr> 
                  <td class="main" style="padding-left:20px; " height="30">
                  <?php echo KEYWORDS_SEARCH_START_TEXT;?>   
                  <select name="s_y"> 
                      <?php
      for($i=2002; $i<2011; $i++) {
        if($i == date(Y)){
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ;
        }else{
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        } 
            }
      ?> 
                    </select> 
                    <?php echo YEAR_TEXT;?> 
                    <select name="s_m"> 
                      <?php
      for($i=1; $i<13; $i++) {
        if($i == date(m)-1){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
            }
      ?> 
                    </select> 
                    <?php echo MONTH_TEXT;?> 
                    <select name="s_d"> 
                      <?php
      for($i=1; $i<32; $i++) {
        if($i == date(d)){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
            }
      ?> 
                    </select> 
                    <?php echo DAY_TEXT;?> 
                    </td> 
                </tr> 
                <tr> 
                  <td class="main" style="padding-left:20px; " height="30">
                  <?php echo KEYWORDS_SEARCH_END_TEXT;?>   
                    <select name="e_y"> 
                      <?php
      for($i=2002; $i<2011; $i++) {
        if($i == date(Y)){
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ;
        }else{
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        } 
            }
      ?> 
                    </select> 
                    <?php echo YEAR_TEXT;?> 
                    <select name="e_m"> 
                      <?php
      for($i=1; $i<13; $i++) {
        if($i == date(m)){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
            }
      ?> 
                    </select> 
                    <?php echo MONTH_TEXT;?> 
                    <select name="e_d"> 
                      <?php
      for($i=1; $i<32; $i++) {
        if($i == date(d)){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
            }
      ?> 
                    </select> 
                    <?php echo DAY_TEXT;?> 
                    </td> 
                </tr> 
                <tr> 
                  <td style="padding-left:20px;" height="35"><input type="image" src="includes/languages/japanese/images/buttons/button_csv_exe.gif" alt="CSVエクスポート" width="105" height="22" border="0"></td> 
                </tr> 
              </table> 
              <input type="hidden" name="act" value="export"> 
              </fieldset> 
            </form></td> 
        </tr> 
        <tr> 
          <td> <p class="main"><?php echo CUSTOMERS_CSV_EXE_NOTICE_TITLE;?></p> 
            <table width="100%"  border="0" cellpadding="2" cellspacing="1" class="infoBoxHeading"> 
            <?php echo CUSTOMERS_CSV_EXE_READ_ONE;?> 
            <?php echo CUSTOMERS_CSV_EXE_READ_TWO;?> 
            </table>
            </td> 
        </tr> 
      </table></td> 
    <!-- body_text_eof //--> 
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
