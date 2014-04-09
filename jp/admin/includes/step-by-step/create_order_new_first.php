<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo CREATE_ORDER_TITLE_TEXT; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/jquery.autocomplete.css">
<?php require('includes/step-by-step/form_check.js.php'); ?>
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language="javascript" src="includes/jquery.form.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $(document).keyup(function(event) {
    if (event.which == 13) {
      search_email_check();
    } 
  });
});
<?php //检测相应网站下的电子邮箱是否存在?>
function search_email_check(){

  var email = $("#keyword").val(); 
  email = email.replace(/\s/g,"");
  if(email == ''){

    alert('<?php echo TEXT_MUST_ENTER;?>');
    $("#keyword").focus();
  }else{
    var site_id = document.getElementsByName("site_id")[0];
    site_id = site_id.value;
    $.ajax({
        url: 'ajax.php?action=check_email_exists',      
        data: 'email='+email+'&site_id='+site_id,
        type: 'POST',
        dataType: 'text',
        async:false,
        success: function (data) {

          if(data == '1'){
            alert("<?php echo TEXT_EMAIL_ADDRESS_ERROR;?>");
          }else if(data == '0'){

            if(confirm('<?php echo TEXT_CREATE_CUSTOMERS_CONFIRM;?>')){

              location.href="<?php echo FILENAME_CUSTOMERS;?>?email_address="+email+"&sid="+site_id;
            }
          }else{
            document.email_check.action = '<?php echo FILENAME_CREATE_ORDER;?>';
            document.email_check.submit();
          }  
        }
    });
  }
}

<?php //检查提交  ?>
function check_submit(){
  var options = {
    url: 'ajax_orders_weight.php?action=create_orders',
    type:  'POST',
    success: function(data) {
      if(data != ''){
        if(confirm(data)){
         
           var fax = document.getElementById("fax");
           var fax_value = document.getElementById("fax_value");
           fax_value.value = fax.value;
           document.create_order_form_1.submit();
        }
      }else{
        var fax = document.getElementById("fax");
        var fax_value = document.getElementById("fax_value");
        fax_value.value = fax.value;
        document.create_order_form_1.submit();
      }
    }
  };
  $('#create_order_form_1').ajaxSubmit(options); 
}

$(function() {
      function format(group) {
          return group.name;
      }
      $("#keyword").autocomplete('ajax_create_order.php?action=search_email', {
        multipleSeparator: '',
        dataType: "json",
        parse: function(data) {
        return $.map(data, function(row) {
            return {
             data: row,
             value: row.name,
             result: row.name
            }
          });
        },
        formatItem: function(item) {
          return format(item);
        }
      }).result(function(e, item) {
      });
});
</script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
if($belong == FILENAME_CREATE_ORDER_PROCESS){
  $belong = FILENAME_CREATE_ORDER;
}
require("includes/note_js.php");
?>
<script language="javascript" src="includes/javascript/jquery.autocomplete.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>', '<?php echo JS_TEXT_INPUT_ONETIME_PWD?>', '<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<?php
if($order_exists == true){
?>
<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
<td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
  <div class="compatible">
  <table border='0' bgcolor='#7c6bce' width='100%'>
      <tr>
        <td class="main"><font color="#ffffff"><?php echo CREATE_ORDER_STEP_ONE;?></font></td>
      </tr>
    </table>
  <p class="pageHeading"><?php echo CREATE_ORDER_TITLE_TEXT;?></p>
<?php
                                                                //显示用户查询表单
  echo '<form name="email_check" action="' . "create_order.php" . '" method="GET">' . "\n";
  if(isset($_GET['oID']) && $_GET['oID'] != ''){
    echo '<input type="hidden" name="oID" value="'.$_GET['oID'].'">';
  }
  echo '<p class=main>'.CREATE_ORDER_SEARCH_TEXT.'<br>'.CREATE_ORDER_EMAIL_TEXT.'&nbsp;<input type="text" value="'.$lastemail.'" id="keyword" name="Customer_mail" size="40"><input type="text" style="display:none;" name="email_submit">'.tep_site_pull_down_menu('', false).'&nbsp;&nbsp;<input type="button" value="'.CREATE_ORDER_SEARCH_BUTTON_TEXT.'" onclick="search_email_check();"></p>' . "\n";
  echo '</form>' . "\n";
?>
  <br>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading">
      <?php 
      if ($_SERVER['PHP_SELF'] == '/admin/create_order_process.php') {
        echo '<font color="red">'.CREATE_ORDER_RED_TITLE_TEXT.'</font>'; 
      } else {
        echo HEADING_CREATE; 
      }
      ?>
      </td>
    </tr>
    <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
  </table>

<?php
  //插入变量
    require(DIR_WS_INCLUDES . 'step-by-step/create_order_details.php');
?>
  <br>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'SSL') . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td> <td class="main" align="right"><input type="button" value="<?php echo IMAGE_CONFIRM_NEXT; ?>" onclick="check_submit();"></td>
      </tr>
    </table>
    </div>
    </div>
  </td>
  </tr>
</table>
<?php
}
?>
<!-- body_eof -->

<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->

<br>
</body>
</html>
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
?>
