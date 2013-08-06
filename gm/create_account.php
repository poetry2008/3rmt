<?php
/*
  $Id$

*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CREATE_ACCOUNT);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CREATE_ACCOUNT));
?>
<?php page_head();?>
<?php require('includes/form_check.js.php'); ?>
<script type="text/javascript">
<!--
function pass_hidd(CI){
  if(CI == '0'){
    document.getElementById('trpass1').style.display = "";
    document.getElementById('trpass2').style.display = "";
    if( document.getElementById('trpass3')){
      document.getElementById('trpass3').style.display = "";
    }
    if( document.getElementById('trpass4')){
      document.getElementById('trpass4').style.display = "";
    }
    if( document.getElementById('trpass5')){
      document.getElementById('trpass5').style.display = "";
    }
  }else{
    document.getElementById('trpass1').style.display = "none";
    document.getElementById('trpass2').style.display = "none";
    if( document.getElementById('trpass3')){
      document.getElementById('trpass3').style.display = "none";
    }
    if( document.getElementById('trpass4')){
      document.getElementById('trpass4').style.display = "none";
    }
    if( document.getElementById('trpass5')){
      document.getElementById('trpass5').style.display = "none";
    }
  }
}
-->
</script>
</head>
<body>
<!-- header --> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof --> 
<!-- body --> 
<div id="main">
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<?php include('includes/search_include.php');?>
<!-- body_text -->
<div id="main-content">

<?php echo tep_draw_form('account_edit', tep_href_link(FILENAME_CREATE_ACCOUNT_PROCESS, '', 'SSL'), 'post', 'onSubmit="return check_form();"') . tep_draw_hidden_field('action', 'process'); ?> 
<h2><?php echo HEADING_TITLE ; ?></h2>
          <table width="100%" cellspacing="0" cellpadding="0" border="0" class="content_account"> 
            <?php
  if (sizeof($navigation->snapshot) > 0) {
?> 
            <tr> 
              <td colspan="2">
<?php echo sprintf(TEXT_ORIGIN_LOGIN, tep_href_link(FILENAME_LOGIN, tep_get_all_get_params(), 'SSL')); ?></td> 
            </tr> 
            <?php
  }
?> 
            <?php
  if (isset($_GET['email_address'])) $email_address = tep_db_prepare_input($_GET['email_address']);
  $account['entry_country_id'] = STORE_COUNTRY;

  require(DIR_WS_MODULES . 'account_details.php');
?>           
          </table>
		   <div align="right" class="botton-continue"><?php echo
                   tep_image_submit('button_continue.gif',
                       IMAGE_BUTTON_CONTINUE,'onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_hover.gif\'"
                       onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue.gif\'"'); ?></div>
        </div>          </div></form>
 <?php include('includes/float-box.php');?>
		</div>
      <!-- body_text_eof --> 
  <!-- body_eof -->  
  <!-- footer --> 
   <!-- footer_eof -->
  <script>
  document.onreadystatechange=function(){
  var obj = document.getElementsByName("guestchk"); 
  for(i = 0;i < obj.length;i++)    { 
    if(obj[i].checked){ 
      CI = obj[i].value; 
    } 
  }      
  pass_hidd(CI);  
  }
  </script>
</div>

 <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
