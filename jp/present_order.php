<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRESENT_ORDER);
  require(DIR_FS_3RMTLIB.'address_info/AD_Option.php');
  require(DIR_FS_3RMTLIB.'address_info/AD_Option_Group.php');
  $hm_option = new AD_Option();

  
  if($_GET['goods_id']) {
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
  
  $breadcrumb->add(NAVBAR_TITLE1, tep_href_link(FILENAME_PRESENT));
  $breadcrumb->add(NAVBAR_TITLE2, tep_href_link(FILENAME_PRESENT,'good_id='.$_GET['goods_id']));
  $breadcrumb->add(NAVBAR_TITLE3, tep_href_link(FILENAME_PRESENT_ORDER));

?>
<?php page_head();?>
<?php require('includes/present_form_check.js.php'); ?>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript"><!--
$(document).ready(function(){
$("#"+country_fee_id).change(function(){
    country_check($("#"+country_fee_id).val());
    country_area_check($("#"+country_area_id).val());
    $("#error_"+country_fee_id_one).html('');
    $("#prompt_"+country_fee_id_one).html('');
    $("#error_"+country_area_id_one).html('');
    $("#prompt_"+country_area_id_one).html('');
    $("#error_"+country_city_id_one).html('');
    $("#prompt_"+country_city_id_one).html('');
}); 
$("#"+country_area_id).change(function(){
    country_area_check($("#"+country_area_id).val());
    $("#error_"+country_fee_id_one).html('');
    $("#prompt_"+country_fee_id_one).html('');
    $("#error_"+country_area_id_one).html('');
    $("#prompt_"+country_area_id_one).html('');
    $("#error_"+country_city_id_one).html('');
    $("#prompt_"+country_city_id_one).html('');
});
address_option_show();
});
<?php
  $address_fixed_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where fixed_option!='0' and status='0'");
  while($address_fixed_array = tep_db_fetch_array($address_fixed_query)){

    switch($address_fixed_array['fixed_option']){

    case '1':
      echo 'var country_fee_id = "op_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_fee_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_fee_id = 'op_'.$address_fixed_array['name_flag'];
      break;
    case '2':
      echo 'var country_area_id = "op_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_area_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_area_id = 'op_'.$address_fixed_array['name_flag'];
      break;
      break;
    case '3':
      echo 'var country_city_id = "op_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_city_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_city_id = 'op_'.$address_fixed_array['name_flag'];
      break;
      break;
    }
  }
?>
function address_option_show(){

  arr_new = new Array();
  arr_color = new Array(); 
<?php 
  $address_new_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type!='text' and status='0' order by sort");
  while($address_new_array = tep_db_fetch_array($address_new_query)){
    $address_new_arr = unserialize($address_new_array['type_comment']);
    if($address_new_array['type'] == 'textarea'){
      if($address_new_arr['set_value'] != ''){
        echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "'. $address_new_arr['set_value'] .'";';
        echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#000";';
      }else{
        echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "'. $address_new_array['comment'] .'";';
        echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#999";';
      }
    }elseif($address_new_array['type'] == 'option' && $address_new_arr['select_value'] !=''){
      echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "'. $address_new_arr['select_value'] .'";';
      echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#000";';
    }else{

      echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "";';
      echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#000";';


    }
  }
  tep_db_free_result($address_new_query);
?> 
    check('<?php echo $_POST[$country_fee_id];?>');
  country_check($("#"+country_fee_id).val(),'<?php echo $_POST[$country_area_id];?>');
  country_area_check($("#"+country_area_id).val(),'<?php echo $_POST[$country_city_id];?>');
    <?php
    if($_GET['action'] != 'process'){
    ?>
    for(x in arr_new){
     
      $("#op_"+x).val(arr_new[x]);
      $("#op_"+x).css('color',arr_color[x]);
      $("#error_"+x).html('');
      if($("#l_"+x).val() == 'true'){
          $("#r_"+x).html('&nbsp;*<?php echo TEXT_REQUIRED;?>');
      }
    } 
    $("#error_"+country_fee_id_one).html('');
    $("#prompt_"+country_fee_id_one).html('');
    $("#error_"+country_area_id_one).html('');
    $("#prompt_"+country_area_id_one).html('');
    $("#error_"+country_city_id_one).html('');
    $("#prompt_"+country_city_id_one).html('');   
    <?php
    }
    if(isset($_SESSION['address_present'])){
   ?>
   session_value();
   <?php
   }
   ?>

}

function check(select_value){

  var arr = new Array();
  <?php 
    $country_fee_query = tep_db_query("select id,name from ". TABLE_COUNTRY_FEE ." where status='0' order by id");
    while($country_fee_array = tep_db_fetch_array($country_fee_query)){

      echo 'arr["'.$country_fee_array['name'].'"] = "'. $country_fee_array['name'] .'";'."\n";
    }
    tep_db_free_result($country_fee_query);
  ?>
    var i = 0;
    var selected_value = '';
    $("#"+country_fee_id).empty();
    for(x in arr){
      if(x==select_value){

        selected_value = ' selected';
      } 
      $("#"+country_fee_id).append( "<option value=\""+arr[x]+"\""+selected_value+">"+x+"</option>" );
      selected_value = '';
      i++;
    } 
    if(i == 0){ 
      $("#td_"+country_fee_id_one).hide();
    }else{

      $("#td_"+country_fee_id_one).show();
    }
}
function country_check(value,select_value){
   
   var arr = new Array();
  <?php 
    $country_array = array();
    $country_area_query = tep_db_query("select id,fid,name from ". TABLE_COUNTRY_AREA ." where status='0' order by sort");
    while($country_area_array = tep_db_fetch_array($country_area_query)){
      
      $country_fee_fid_query = tep_db_query("select name from ". TABLE_COUNTRY_FEE ." where id='".$country_area_array['fid']."'"); 
      $country_fee_fid_array = tep_db_fetch_array($country_fee_fid_query);
      tep_db_free_result($country_fee_fid_query);
      $country_array[$country_fee_fid_array['name']][$country_area_array['name']] = $country_area_array['name'];
      
    }
    tep_db_free_result($country_area_query);
    foreach($country_array as $country_key=>$country_value){
      
      echo 'arr["'.$country_key.'"] = new Array();'."\n";
      foreach($country_value as $c_key=>$c_value){
      
        echo 'arr["'.$country_key.'"]["'.$c_key.'"] = "'. $c_value .'";'."\n";

      }

    }
   ?>
    var i = 0;
    var selected_value = '';
    $("#"+country_area_id).empty();
    for(x in arr[value]){
      if(x==select_value){

        selected_value = ' selected';
      }
      $("#"+country_area_id).append( "<option value=\""+arr[value][x]+"\""+selected_value+">"+x+"</option>" );
      selected_value = '';
      i++;
    }
    if(i == 0){ 
      $("#td_"+country_area_id_one).hide();
    }else{
      
      $("#td_"+country_area_id_one).show();
    }
}

function country_area_check(value,select_value){
   
   var arr = new Array();
  <?php
    $country_array = array();
    $country_city_query = tep_db_query("select id,fid,name from ". TABLE_COUNTRY_CITY ." where status='0' order by sort");
    while($country_city_array = tep_db_fetch_array($country_city_query)){
      
      $country_area_fid_query = tep_db_query("select name from ". TABLE_COUNTRY_AREA ." where id='".$country_city_array['fid']."'"); 
      $country_area_fid_array = tep_db_fetch_array($country_area_fid_query);
      tep_db_free_result($country_area_fid_query); 
      $country_array[$country_area_fid_array['name']][$country_city_array['name']] = $country_city_array['name'];
      
    }
    tep_db_free_result($country_city_query);
    foreach($country_array as $country_key=>$country_value){
      
      echo 'arr["'.$country_key.'"] = new Array();'."\n";
      foreach($country_value as $c_key=>$c_value){
      
        echo 'arr["'.$country_key.'"]["'.$c_key.'"] = "'. $c_value .'";'."\n";

      }

    }
  ?>

  var i = 0;
    var selected_value = '';
    $("#"+country_city_id).empty();
    for(x in arr[value]){
      if(x==select_value){

        selected_value = ' selected';
      }
      $("#"+country_city_id).append( "<option value=\""+arr[value][x]+"\""+selected_value+">"+x+"</option>" );
      selected_value = '';
      i++;
    }
    if(i == 0){ 
      $("#td_"+country_city_id_one).hide();
    }else{
      
      $("#td_"+country_city_id_one).show();

    }
}

function session_value(){
  var session_array = new Array();
<?php
  foreach($_SESSION['address_present'] as $see_key=>$see_value){
    $see_value[1] = str_replace("\n","",$see_value[1]);
    $see_value[1] = str_replace("\r","",$see_value[1]);
    echo 'session_array["'. $see_key .'"] = "'. $see_value[1] .'";';
  }
?>
  for(x in session_array){
    
    if(country_fee_id == 'op_'+x){
      check(session_array[x]);
    }else if(country_area_id == 'op_'+x){
      country_check($("#"+country_fee_id).val(),session_array[x]);
     
    }else if(country_city_id == 'op_'+x){
      country_area_check($("#"+country_area_id).val(),session_array[x]);
    }else{

      $("#op_"+x).css("color","#000000");
      $("#op_"+x).val(session_array[x]);
    }
  }
}

function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
--></script>
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
              <td><?php
  if(isset($_POST['goods_id']) && $_POST['goods_id']) {
    $present_query = tep_db_query("
        select * 
        from ".TABLE_PRESENT_GOODS." 
        where goods_id = '".(int)$_GET['goods_id']."'
          and site_id  = '" . SITE_ID . "'
    ") ;
    $present = tep_db_fetch_array($present_query) ;
  } 
?>
                  <table border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td> 
                  <table width="100%" cellpadding="1" cellspacing="0" class="infoBox" border="0">
                  <tr>
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContents">
                        <tr <?php echo $_class?"class='".$_class."'":'' ; ?>>
                          <td class="main" width="<?php echo SMALL_IMAGE_WIDTH ; ?>">
<script type="text/javascript" language="javascript"><!--
  document.write('<?php echo '<a href="'.tep_href_link(FILENAME_PRESENT, 'goods_id=' . (int)$_GET['goods_id']).'">' . tep_image(DIR_WS_IMAGES.'present/'.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT, 'align="right" hspace="5" vspace="5"') . '<\'+\'/a>'; ?>');
--></script>
<noscript>
<?php echo tep_image(DIR_WS_IMAGES.'present/'.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT, 'align="right"'); ?>
</noscript>
                          </td>
                          <td class="main"><b><?php echo $present['title'] ; ?></b> &nbsp;&nbsp; 応募期間:<?php echo tep_date_long($present['start_date']) .'～'. tep_date_long($present['limit_date']); ?> </td>
                        </tr>
                      </table></td>
                  </tr>
                </table></td></tr></table></td>
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
if(!tep_session_is_registered('customer_id')) {
?>
            <tr><td><?php echo tep_draw_form('login', tep_href_link(FILENAME_PRESENT_ORDER, 'goods_id='.$_GET['goods_id'].'&action=login', 'SSL')); ?>
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr><td class="formAreaTitle"><b><?php echo HEADING_RETURNING_CUSTOMER; ?></b></td></tr>
            <tr>
              <td class="main">
               <table width="100%" cellspacing="0" cellpadding="1" border="0" class="infoBox"> 
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
                                <td class="main" width="104" valign="top"><b><?php echo ENTRY_EMAIL_ADDRESS; ?></b></td>
                                <td class="main"><?php echo tep_draw_input_field('email_address','','class="input_text"'); ?></td>
                              </tr>
                              <tr>
                                <td class="main" width="104" valign="top"><b><?php echo ENTRY_PASSWORD; ?></b></td>
                                <td class="main"><?php echo tep_draw_password_field('password','','class="input_text"'); ?></td>
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
                            </table>
                      </td>
                  </tr> 
                </table>
                </td>
            </tr>
            <tr>
            <td align="right"><?php echo tep_image_submit('button_login.gif', IMAGE_BUTTON_LOGIN); ?></td>
            </tr>
            </table></form>
            </td></tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
<?php
}
?>
            <tr>
              <td class="main"><?php echo TEXT1 ; ?> </td>
            </tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
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
      <!-- body_text_eof --> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof --> </td> 
  </table> 
  <!-- body_eof --> 
  <!-- footer --> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof --> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
