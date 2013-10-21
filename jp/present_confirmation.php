<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/present_confirmation.php');
// if the customer is not logged on, redirect them to the present page
  if (!tep_session_is_registered('pc_id')) {
   $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_PRESENT_ORDER,'goods_id='.$_GET['goods_id']));
  }


  if($_GET['goods_id']) {
    $present_query = tep_db_query("
        select * 
        from ".TABLE_PRESENT_GOODS." 
        where goods_id = '".(int)$_GET['goods_id']."' 
          and site_id  = '".SITE_ID."'
    ") ;
    $present = tep_db_fetch_array($present_query) ;
  }else{
    tep_redirect(tep_href_link(FILENAME_PRESENT, 'error_message='.urlencode(TEXT_NO_SELECT_PRESENT), 'SSL'));  
  }
  
  //process
if (!isset($_GET['action'])) $_GET['action'] = NULL;//delnotice
  switch($_GET['action']) {
    case 'process'://申请流程
    //现在的时间
    $now = date("Y/m/d H:i:s", time());

    //present address info 
    $present_address_info = '';
    $i = 0;
    $sum = count($_SESSION['address_present']);
    foreach($_SESSION['address_present'] as $op_key=>$op_value){
  
      if($_SESSION['present_type_array'][$op_key] == 'num'){
     
        $input_text_str = $op_value[1];
        $mode = array('/\s/','/－/','/－/','/-/');
        $replace = array('','','','');
        $mode_half = array('1','2','3','4','5','6','7','8','9','0');
        $mode_all = array('/１/','/２/','/３/','/４/','/５/','/６/','/７/','/８/','/９/','/０/');
        $input_text_str = preg_replace($mode,$replace,$input_text_str);
        $input_text_str = preg_replace($mode_all,$mode_half,$input_text_str);
        $op_value[1] = $input_text_str;
      } 
      $i++;
      if($i == $sum){
        if(trim($op_value[1]) != ''){
          $present_address_info .= $op_value[1];
        }
      }else{
        if(trim($op_value[1]) != ''){
          $present_address_info .= $op_value[1].'<br>'; 
        }
      }
    }
    
    //insert present_aplicant
    $sql_data_array = array(
                'goods_id'    => tep_db_prepare_input($_GET['goods_id']),
                'customer_id' => tep_db_prepare_input($pc_id),
                'family_name' => tep_db_prepare_input($lastname),
                'first_name'  => tep_db_prepare_input($firstname),
                'mail'        => tep_db_prepare_input($email_address),
                'tourokubi'   => tep_db_prepare_input($now),
                'address'     => tep_db_prepare_input($present_address_info)
                );

    tep_db_perform(TABLE_PRESENT_APPLICANT, $sql_data_array);

    //address history info
    $address_show_array = array(); 
    $address_show_list_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
    while($address_show_list_array = tep_db_fetch_array($address_show_list_query)){

      $address_show_array[$address_show_list_array['id']] = $address_show_list_array['name_flag'];
    }
    tep_db_free_result($address_show_list_query);
    $address_temp_str = '';
    foreach($_SESSION['address_present'] as $address_his_key=>$address_his_value){
    
      if(in_array($address_his_key,$address_show_array)){

         $address_temp_str .= $address_his_value[1];
      }
    }
  
    $address_error = false;
    $orders_id_temp = '';
    $address_sh_his_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id='$customer_id' group by orders_id");
    while($address_sh_his_array = tep_db_fetch_array($address_sh_his_query)){

      $address_sh_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where customers_id='$customer_id' and orders_id='". $address_sh_his_array['orders_id'] ."' order by id");
      $add_temp_str = '';
      while($address_sh_array = tep_db_fetch_array($address_sh_query)){
     
        if(in_array($address_sh_array['name'],$address_show_array)){

          $add_temp_str .= $address_sh_array['value'];
        }  
      }
      if($address_temp_str == $add_temp_str){

        $address_error = true;
        $orders_id_temp = $address_sh_his_array['orders_id'];
        break;
      }
      tep_db_free_result($address_sh_query);
    }
    tep_db_free_result($address_sh_his_query); 
    //update address info
    if($address_error == true && $orders_id_temp != ''){

      tep_db_query("update ". TABLE_ADDRESS_HISTORY ." set orders_id='".(date("Ymd") . '-' . date("His") . tep_get_order_end_num())."' where orders_id='".$orders_id_temp."'"); 
    }
    if($address_error == false && $_SESSION['guestchk'] == '0'){
      foreach($_SESSION['address_present'] as $address_history_key=>$address_history_value){
        $address_history_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where name_flag='". $address_history_key ."'");
        $address_history_array = tep_db_fetch_array($address_history_query);
        tep_db_free_result($address_history_query);
        $address_history_id = $address_history_array['id'];
        $address_history_add_query = tep_db_query("insert into ". TABLE_ADDRESS_HISTORY ." value(NULL,'".(date("Ymd") . '-' . date("His") . tep_get_order_end_num())."',{$customer_id},$address_history_id,'{$address_history_array['name_flag']}','$address_history_value[1]')");
        tep_db_free_result($address_history_add_query);
      }
    }
    
    //check pre insert - customers
    if($pc_id != '0') {
      $cmcnt_query = tep_db_query("
          select count(*) as cnt 
          from ".TABLE_CUSTOMERS." 
          where customers_email_address = '".tep_db_prepare_input($email_address)."' 
          and site_id = '".SITE_ID."'
      ");
      $cmcnt_result = tep_db_fetch_array($cmcnt_query);
    
      $cmcnt = $cmcnt_result['cnt'];

      //update mail_mag
      if($cmcnt != 0) {
        tep_db_query("
            update ".TABLE_CUSTOMERS." 
            set customers_newsletter = '1' 
            where customers_email_address = '".tep_db_prepare_input($email_address)."' 
              and site_id ='".SITE_ID."'
        ");
      }
    } else {
      $cmcnt = 0;
    }
    
    //check pre insert - main_magazine
    $mgcnt_query = tep_db_query("
        select count(*) as cnt 
        from ".TABLE_MAIL_MAGAZINE." 
        where mag_email = '".tep_db_prepare_input($email_address)."' 
          and site_id = '".SITE_ID."'
    ");
    $mgcnt_result = tep_db_fetch_array($mgcnt_query);
    
    //insert mail_magazine ** customers=0 & mail_magazine=0
    if($cmcnt == 0 && $mgcnt_result['cnt'] == 0) {
      $sql_data_array2 = array(
          'mag_email' => tep_db_prepare_input($email_address),
          'mag_name'  => tep_get_fullname($firstname, $lastname),
          'site_id'   => SITE_ID
          );
      tep_db_perform(TABLE_MAIL_MAGAZINE, $sql_data_array2);
    }
    
    tep_redirect(tep_href_link(FILENAME_PRESENT_SUCCESS,'goods_id='.$_GET['goods_id']));
    break;  
    default:
          if (!tep_session_is_registered('firstname'))
          {
          $account_query = tep_db_query("
              select c.customers_gender, 
                     c.customers_firstname, 
                     c.customers_lastname, 
                     c.customers_firstname_f, 
                     c.customers_lastname_f, 
                     c.customers_dob, 
                     c.customers_email_address, 
                     a.entry_company, 
                     a.entry_street_address, 
                     a.entry_suburb, 
                     a.entry_postcode, 
                     a.entry_city, 
                     a.entry_zone_id, 
                     a.entry_state, 
                     a.entry_country_id, 
                     c.customers_telephone, 
                     c.customers_fax, 
                     c.customers_newsletter 
              from " . TABLE_CUSTOMERS . " c, " .  TABLE_ADDRESS_BOOK . " a 
              where c.customers_id = '" . $customer_id . "' 
                and a.customers_id = c.customers_id 
                and a.address_book_id = '" .  $customer_default_address_id . "' 
                and c.site_id = '" . SITE_ID . "'
              ");
          $account        = tep_db_fetch_array($account_query);
          $firstname      = $account['customers_firstname'];
          $lastname       = $account['customers_lastname'];
          $email_address  = $account['customers_email_address'];
          
          tep_session_register('firstname');
          tep_session_register('lastname');
          tep_session_register('email_address'); 
      }
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRESENT_ORDER);

  $breadcrumb->add(NAVBAR_TITLE1, tep_href_link(FILENAME_PRESENT));
  $breadcrumb->add(NAVBAR_TITLE2, tep_href_link(FILENAME_PRESENT,'good_id='.$_GET['goods_id']));
  $breadcrumb->add(NAVBAR_TITLE3, tep_href_link(FILENAME_PRESENT_ORDER));

?>
<?php page_head();?>
</head>
<body> 
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof --> 
  <!-- body --> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof --> </td> 
      <!-- body_text --> 
      <td valign="top" id="contents"> <h1 class="pageHeading"> 
      <?php if (!isset($_GET['news_id'])) $_GET['news_id']=NULL;?>
          <?php if ($_GET['news_id']) { echo $latest_news['headline']; } else { echo HEADING_TITLE; } ?> 
        </h1> 
        
        <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
          <tr> 
            <td> <div class="contents"> 
              <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                <tr> 
                  <td width="33%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                      <tr> 
                        <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                      </tr> 
                    </table></td> 
                  <td width="33%" align="center"><table width="100%"  border="0" cellspacing="0" cellpadding="0"> 
                      <tr> 
                        <td><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                        <td width="11"><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td> 
                        <td><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                      </tr> 
                    </table></td> 
                  <td width="33%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                      <tr> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
                      </tr> 
                    </table></td> 
                </tr> 
                <tr> 
                  <td align="center" width="33%" class="checkoutBarFrom"><a href="<?php echo tep_href_link(FILENAME_PRESENT_ORDER,'goods_id='.$_GET['goods_id']);?>" class="checkoutBarFrom"><?php echo TEXT_EPRESNT_BAR_INFORMATION;?></a></td> 
                  <td align="center" width="33%" class="checkoutBarCurrent"><?php echo TEXT_PRESENT_BAR_CONFIRMATION;?></td> 
                  <td align="center" width="33%" class="checkoutBarFrom"><?php echo TEXT_PRESENT_BAR_SUCCESS;?></td> 
                </tr> 
              </table></td> 
          </tr> 
          <tr> 
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr> 
          <?php 
            echo tep_draw_form('process', tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.$_GET['goods_id'].'&action=process', 'SSL')); 
          ?>
          <tr> 
            <td class="main">
              <table border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td> 
              <table width="100%" cellpadding="1" cellspacing="0" class="infoBox" border="0"> 
                <tr> 
                  <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContents"> 
                      <tr class="<?php echo $_class ; ?>"> 
                      <td class="main"><b><?php echo TEXT_PRESENT_CON_NEXT;?></b></td> 
                        <td class="main" align="right"><?php echo tep_image_submit('button_present.gif', IMAGE_BUTTON_PRESENT); ?></td> 
                      </tr> 
                    </table></td> 
                </tr> 
              </table></td></tr></table></td> 
          </tr> 
          <tr> 
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr>
          <tr><td>
          <table border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td> 
              <table width="100%" cellpadding="1" cellspacing="0" class="infoBox" border="0"> 
                <tr> 
                  <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContents"> 
                  <tr><td class="main" colspan="3"><b><?php echo TEXT_PRESENT_CON_CONTENTS;?>&nbsp;</b><a href="<?php echo tep_href_link(FILENAME_PRESENT,'','SSL');?>"><span class="orderEdit"><?php echo TEXT_PRESENT_CON_EDIT;?></span></a></td></tr>
                      <tr class="<?php echo $_class ; ?>"> 
                        <td class="main" width="10">&nbsp;</td> 
                        <td class="main"><?php echo $present['title'] ; ?><br> 
                          <?php echo TEXT_PRESENT_CON_TIME.tep_date_long($present['start_date']) .'～'. tep_date_long($present['limit_date']); ?> </td> 
                      </tr> 
                    </table></td> 
                </tr> 
              </table></td></tr></table>
          </td></tr>
          <tr> 
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr>  
          <tr> 
            <td class="main"><table width="100%"  border="0" cellspacing="0" cellpadding="2"> 
                <tr> 
                  <td class="main"> 
                    <table border="0" width="100%" height="100%" cellspacing="0" cellpadding="1" class="infoBox"> 
                      <tr> 
                        <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContents"> 
                            <tr> 
                              <td class="main" colspan="3"><b><?php echo TEXT_PRESENT_CON_PERSONAL_INFO; ?>&nbsp;</b><a href="<?php echo tep_href_link(FILENAME_PRESENT_ORDER,'goods_id='.$_GET['goods_id'],'SSL');?>"><span class="orderEdit"><?php echo TEXT_PRESENT_CON_EDIT;?></span></a></td> 
                            </tr> 
                            <tr> 
                              <td class="main" width="10">&nbsp;</td>
                              <td class="main" width="30%"><?php echo TEXT_PRESENT_CON_FAM;?></td> 
                              <td class="main"><?php echo $lastname; ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main" width="10">&nbsp;</td>
                              <td class="main" width="30%"><?php echo TEXT_PRESENT_CON_NAME;?></td> 
                              <td class="main"><?php echo $firstname; ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main" width="10">&nbsp;</td>
                              <td class="main" width="30%"><?php echo TEXT_PRESENT_CON_EMAIL; ?></td> 
                              <td class="main"><?php echo $email_address; ?></td> 
                            </tr>   
                          </table>
                          </td> 
                      </tr> 
                    </table></td> 
                </tr>  
              </table></td> 
          </tr>  
          <tr> 
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr>  
          <tr> 
            <td class="main"><table width="100%"  border="0" cellspacing="0" cellpadding="2"> 
                <tr> 
                  <td class="main"> 
                    <table border="0" width="100%" height="100%" cellspacing="0" cellpadding="1" class="infoBox"> 
                      <tr> 
                        <td><table border="0" width="100%" height="100%" cellspacing="0" cellpadding="2" class="infoBoxContents"> 
                            <tr> 
                              <td class="main" colspan="3"><b><?php echo TEXT_PRESENT_CON_ADDRESS_INFO; ?>&nbsp;</b><a href="<?php echo tep_href_link(FILENAME_PRESENT_ORDER,'goods_id='.$_GET['goods_id'],'SSL');?>"><span class="orderEdit"><?php echo TEXT_PRESENT_CON_EDIT;?></span></a></td> 
                            </tr> 
                            <?php
                            foreach($_SESSION['address_present'] as $key=>$value){
                            ?>
                            <tr>
                              <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                              <td class="main" width="30%" valign="top"><?php echo $value[0]; ?>:</td>
                              <td class="main" width="70%"><?php echo $value[1]; ?><span id="<?php echo $key;?>"></span></td>
                            </tr>
                            <?php
                             }
                            ?>  
                          </table></td> 
                      </tr> 
                    </table></td> 
                </tr>  
              </table></td> 
          </tr> 
          <tr> 
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr>
          <tr> 
              <td>
              <table border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td> 
              <table width="100%" cellpadding="1" cellspacing="0" class="infoBox" border="0"> 
                <tr> 
                  <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContents"> 
                      <tr class="<?php echo $_class ; ?>"> 
                      <td class="main"><b><?php echo TEXT_PRESENT_CON_NEXT;?></b></td> 
                        <td class="main" align="right"><?php echo tep_image_submit('button_present.gif', IMAGE_BUTTON_PRESENT); ?></td> 
                      </tr> 
                    </table></td> 
                </tr> 
              </table></td></tr></table>
              </td> 
              </tr>
          </form> 
           </table></div></td> 
      <!-- body_text_eof --> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof --> </td> 
  </tr>
  </table> 
  <!-- body_eof --> 
  <!-- footer --> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof --> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
