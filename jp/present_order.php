<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  if($_GET['goods_id']) {
//ccdd
    $present_query = tep_db_query("
        select * 
        from ".TABLE_PRESENT_GOODS." 
        where goods_id = '".(int)$_GET['goods_id']."' 
          and site_id = '".SITE_ID."'
    ") ;
    $present       = tep_db_fetch_array($present_query) ;
    forward404Unless($present);
  }else{
    tep_redirect(tep_href_link(FILENAME_PRESENT, 'error_message='.urlencode(TEXT_PRESENT_ERROR_NOT_SELECTED), 'SSL'));  
  }

  //登录的情况下，跳转到确认页面
  if(tep_session_is_registered('customer_id')) {
    $pc_id = $customer_id;
    tep_session_register('pc_id');
    tep_redirect(tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.(int)$_GET['goods_id'], 'SSL'));
  }
  
  //session里有“pc_id”的时候，跳转到确认页面
  if(tep_session_is_registered('pc_id')) {
    tep_redirect(tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.(int)$_GET['goods_id'], 'SSL'));
  }
 
  if(isset($_GET['action'])){
    switch($_GET['action']) {
      //老会员登录
    case 'login':
      require(DIR_WS_ACTIONS.'present_login.php');
      break;
    
      //游客或者新会员
    case 'process':
      require(DIR_WS_ACTIONS.'present_process.php');
      break;
    }
  }
  
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRESENT_ORDER);

  $breadcrumb->add(NAVBAR_TITLE1, tep_href_link(FILENAME_PRESENT));
  $breadcrumb->add(NAVBAR_TITLE2, tep_href_link(FILENAME_PRESENT,'good_id='.$_GET['goods_id']));
  $breadcrumb->add(NAVBAR_TITLE3, tep_href_link(FILENAME_PRESENT_ORDER));

?>
<?php page_head();?>
<?php require('includes/present_form_check.js.php'); ?>
<script type="text/javascript"><!--
function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
</head>
<body> 
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> <h1 class="pageHeading"> 
          <?php if (isset($_GET['news_id']) && $_GET['news_id']) { echo $latest_news['headline']; } else { echo HEADING_TITLE; } ?> 
        </h1> 
        
        <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
          <tr> 
            <td class="contents"> <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="33%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="50%" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td>
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                        </tr>
                      </table></td>
                    <td width="33%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                    <td width="33%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
                        </tr>
                      </table></td>
                  </tr>
                  <tr>
                    <td align="center" width="33%" class="checkoutBarCurrent">応募者情報</td>
                    <td align="center" width="33%" class="checkoutBarFrom">確認画面</td>
                    <td align="center" width="33%" class="checkoutBarFrom">応募完了</td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <tr>
              <td class="main"><?php
  if(isset($_POST['goods_id']) && $_POST['goods_id']) {
//ccdd
    $present_query = tep_db_query("
        select * 
        from ".TABLE_PRESENT_GOODS." 
        where goods_id = '".(int)$_GET['goods_id']."'
          and site_id  = '" . SITE_ID . "'
    ") ;
    $present = tep_db_fetch_array($present_query) ;
  } 
?>
                <table width="100%" cellpadding="1" cellspacing="0" class="infoBox" border="0" summary="table">
                  <tr>
                    <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxContents" summary="table">
                        <tr <?php echo $_class?"class='".$_class."'":'' ; ?>>
                          <td class="main" width="<?php echo SMALL_IMAGE_WIDTH ; ?>">
<script type="text/javascript" language="javascript"><!--
  document.write('<?php echo '<a href="javascript:popupWindow(\\\'' . tep_href_link('present_popup_image.php', 'pID=' . (int)$_GET['goods_id']) . '\\\')">' . tep_image(DIR_WS_IMAGES.'present/'.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT, 'align="right" hspace="5" vspace="5"') . '<\'+\'/a>'; ?>');
--></script>
<noscript>
<?php echo tep_image(DIR_WS_IMAGES.'present/'.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT, 'align="right"'); ?>
</noscript>
                            <?php //echo '<a href="'.tep_href_link(FILENAME_PRESENT , 'goods_id='.$present['goods_id'],NONSSL).'">' . tep_image(DIR_WS_IMAGES.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT) . '</a>'; ?>
                          </td>
                          <td class="main"><b><?php echo $present['title'] ; ?></b> &nbsp;&nbsp; 応募期間:<?php echo tep_date_long($present['start_date']) .'～'. tep_date_long($present['limit_date']); ?> </td>
                        </tr>
                      </table></td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <?php
  if (isset($_GET['login']) && ($_GET['login'] == 'fail')) {
    $info_message = TEXT_LOGIN_ERROR;
  } elseif ($cart->count_contents()) {
    $info_message = TEXT_VISITORS_CART;
  }

  if (isset($info_message)) {
?>
            <tr>
              <td class="smallText"><?php echo $info_message; ?></td>
            </tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <?php
  }
?>
            <tr>
              <td class="main"><?php echo tep_draw_form('login', tep_href_link(FILENAME_PRESENT_ORDER, 'goods_id='.$_GET['goods_id'].'&action=login', 'SSL')); ?>
                <table width="100%"  border="0" cellspacing="0" cellpadding="2" summary="table">
                  <tr>
                    <td width="25%">&nbsp;</td>
                    <td class="main"><b><?php echo HEADING_RETURNING_CUSTOMER; ?></b>
                      <table border="0" width="100%" cellspacing="0" cellpadding="1" class="infoBox">
                        <tr>
                          <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContents">
                              <tr>
                                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                              </tr>
                              <tr>
                                <td class="main" colspan="2"><?php echo TEXT_RETURNING_CUSTOMER; ?></td>
                              </tr>
                              <tr>
                                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                              </tr>
                              <tr>
                                <td class="main"><b><?php echo ENTRY_EMAIL_ADDRESS; ?></b></td>
                                <td class="main"><?php echo tep_draw_input_field('email_address'); ?></td>
                              </tr>
                              <tr>
                                <td class="main"><b><?php echo ENTRY_PASSWORD; ?></b></td>
                                <td class="main"><?php echo tep_draw_password_field('password'); ?></td>
                              </tr>
                              <tr>
                                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                              </tr>
                              <tr>
                                <td class="smallText" colspan="2"><?php echo '<a href="' . tep_href_link(FILENAME_PASSWORD_FORGOTTEN, '', 'SSL') . '">' . TEXT_PASSWORD_FORGOTTEN . '</a>'; ?></td>
                              </tr>
                              <tr>
                                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                              </tr>
                            </table></td>
                        </tr>
                      </table></td>
                    <td width="25%">&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td align="right"><?php echo tep_image_submit('button_login.gif', IMAGE_BUTTON_LOGIN); ?></td>
                    <td>&nbsp;</td>
                  </tr>
                </table>
                </form></td>
            </tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <tr>
              <td class="main"><?php echo TEXT1 ; ?> </td>
            </tr>
            <tr>
              <td class="main"><?php
      
  if (isset($_GET['email_address'])) $email_address = tep_db_prepare_input($_GET['email_address']);
    $account['entry_country_id'] = STORE_COUNTRY;
    echo tep_draw_form('present_account', tep_href_link(FILENAME_PRESENT_ORDER, 'goods_id='.$_GET['goods_id'].'&action=process', 'SSL'), 'post', 'onSubmit="return check_form();"'); 
    require(DIR_WS_MODULES . 'present_account_details.php');
    echo '<div align="right">'. tep_draw_hidden_field('goods_id', $present['goods_id']) . tep_image_submit('button_continue.gif', '') .'</div>' . "\n";
    echo '</form>';

?></td>
            </tr>
          </table>
        </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </td> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
