<?php
/*
*/
require('includes/application_top.php');
//require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_MANUFAXTURERS);

define('HEADING_TITLE', '����ã����');
define('MINUTES', 30);

$breadcrumb->add('����ã�ե�����', tep_href_link('reorder2.php'));
?>
<?php page_head();?>
<script src='./js/order.js'></script>
</head>
<body>
<div id="main">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <div id="l_menu">
    <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
  </div>
  <!-- body_text //-->
  <div id="content">
    <div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; ');?></div>
    <h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
    <?php if($HTTP_POST_VARS){
					$date     = tep_db_prepare_input($HTTP_POST_VARS['date']);
					$hour     = tep_db_prepare_input($HTTP_POST_VARS['hour']);
					$minute   = tep_db_prepare_input($HTTP_POST_VARS['minute']);

					$name      = tep_db_prepare_input($HTTP_POST_VARS['name']);
					$character = tep_db_prepare_input($HTTP_POST_VARS['character']);
					$product   = tep_db_prepare_input($HTTP_POST_VARS['product']);
					$comment   = tep_db_prepare_input($HTTP_POST_VARS['comment']);

					$datetime = $date.' '.$hour.':'.$minute;
					$time     = strtotime($datetime);
					if ($date && $hour && $minute && ($time < (time() - MINUTES * 60) or $time > (time() + (7*86400)))) {
						// time error
						echo '<div><div class="comment">������֤�����äư���ְʾ�����ꤷ�Ƥ������� <div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" alt=""></a></div></div>';
					} else {
						echo '<div><div class="comment" style="width:95%">��ʸ���Ƥ��ѹ��򾵤�ޤ������Żҥ᡼��򤴳�ǧ���������� <div align="right"><a href="/"><img src="includes/languages/japanese/images/buttons/button_back_home.gif" alt="TOP�����" title="TOP�����"></a></div></div>';
						// sent mail to customer
						//$mail    = tep_db_fetch_array(tep_db_query("select * from iimy_orders_mail where orders_status_id=16"));
						//$mail_title   = $mail['orders_status_title'];
						//$mail_content = $mail['orders_status_mail'];

						$email_order = '';
						$email_order .= $name . "��\n";
						$email_order .= "\n";
						$email_order .= "�����٤ϡ���Ϣ�����������ˤ��꤬�Ȥ��������ޤ���\n";
						$email_order .= "�����ͤ��顢��������ʸ���Ƥˤƺ���ã�򾵤�ޤ�����\n";
						$email_order .= "\n";
						$email_order .= "=====================================\n";
						$email_order .= "\n";
						$email_order .= '������������������������������������������' . "\n";
						$email_order .= '����̾�� : ' . $name . "\n";
						$email_order .= '���᡼�륢�ɥ쥹 : ' . $email . "\n";
						$email_order .= '�������ॿ���ȥ� : ' . $product . "\n";
						$email_order .= '������饯����̾ : ' . $character . "\n";
						$email_order .= '���ѹ���˾������ : ' . $datetime . "\n";
						$email_order .= "������ : \n";
						$email_order .= $comment . "\n";
						$email_order .= '������������������������������������������' . "\n";
						$email_order .= "\n";
						$email_order .= "=====================================\n\n\n\n";
						
						$email_order .= "�������������������ޤ����顢��ʸ�ֹ�򤴳�ǧ�ξ塢\n";
						$email_order .= "���䤤��碌����������\n\n";

						$email_order .= "[��Ϣ�����䤤��碌��]������������������������\n";
						$email_order .= "������� iimy\n";
						$email_order .= SUPPORT_EMAIL_ADDRESS . "\n";
						$email_order .= HTTP_SERVER . "\n";
						//$email_order .= "��761-0445 ����⾾��������Į2925����\n";
						//$email_order .= "�����ֹ桧 087-862-1173\n";
						$email_order .= "����������������������������������������������\n";
						
						//echo nl2br($email_order);
						
						//$email_title = str_replace(array(), array(), $email_title);
						$mail_title = "����ã��ǧ�᡼���" . STORE_NAME . "��";
						//$email_order = str_replace(array('${NAME}', '${TIME}', '${CONTENT}'), array($name, date('Y-m-d H:i:s'), $email_order), $mail_content);
						
						tep_mail($name, $email, $mail_title, nl2br($email_order), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '');

						if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
						  tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, $mail_title, nl2br($email_order), $o->customer['name'], $o->customer['email_address'], '');
						}
					}
				 }else{?>
    <div class="comment">
      <form action="reorder2.php" method="post" name="order">
        <input type="hidden" name="dummy" value="��������������">
        <table class="information_table">
          <tr>
            <td width='120'>��̾��</td>
            <td>
              <input type='text'  name='name' value='' id='new_name' class="input_text" >
              <span id='name_error'></span></td>
          </tr>
          <tr>
            <td>�᡼�륢�ɥ쥹</td>
            <td>
              <input type='text'  name='email' value='' id='new_email' class="input_text" >
              <span id='email_error'></span></td>
          </tr>
          <tr>
            <td>�����ॿ���ȥ�</td>
            <td>
              <input type='text'  name='product' value='' id='new_product' class="input_text" >
              <span id='product_error'></span></td>
          </tr>
          <tr>
            <td>����饯����̾</td>
            <td>
              <input type='text'  name='character' value='' id='new_character' class="input_text" >
              <span id='character_error'></span></td>
          </tr>
          <tr>
            <td>�������</td>
            <td>
              <select name='date' id='new_date' onChange="selectDate('<?php echo date('H');?>', '<?php echo date('i');?>')">
                <option value=''>--</option>
                <?php for($i=0;$i<7;$i++){?>
                <option value='<?php echo date('Y-m-d', time()+($i*86400));?>'><?php echo strftime(DATE_FORMAT_LONG, time()+($i*86400));?></option>
                <?php }?>
              </select>
              <select name='hour' id='new_hour' onChange="selectHour('<?php echo date('H');?>', '<?php echo date('i');?>')">
                <option value=''>--</option>
              </select>
              :
              <select name='minute' id='new_minute'>
                <option value=''>--</option>
              </select>
              <span id='date_error'></span>
              <br >
              <font color="red">����˾�Τ����֤�ź���ʤ����ϡ����Ҥ��ּ�����֡פ�Ϣ�����Ƥ��������ޤ���</font>
            </td>
          </tr>
          <tr>
            <td>����</td>
            <td>
              <textarea name='comment' id='comment'></textarea>
            </td>
          </tr>
        </table>
        <br>
        <p align="center">
          <input type='image' src="includes/languages/japanese/images/buttons/button_submit2.gif" alt="���ꤹ��" title="���ꤹ��" onclick='return check()' >
          <input type='image' src="includes/languages/japanese/images/buttons/button_reset.gif" alt="���ꥢ" title="���ꥢ" onclick='javascript:document.order.reset();return false;' >
        </p>
      </form>
      <?php }?>
    </div>
    <script type="text/javascript">
function check(){
	commit = true;
	document.getElementById('name_error').innerHTML = '';
	document.getElementById('date_error').innerHTML = '';
	document.getElementById('product_error').innerHTML = '';
	//document.getElementById('comment_error').innerHTML = '';
	document.getElementById('email_error').innerHTML = '';
	document.getElementById('character_error').innerHTML = '';
	
	if((document.getElementById('new_date').selectedIndex != 0 || document.getElementById('new_hour').selectedIndex != 0 || document.getElementById('new_minute').selectedIndex != 0) && !(document.getElementById('new_date').selectedIndex != 0 && document.getElementById('new_hour').selectedIndex != 0 && document.getElementById('new_minute').selectedIndex != 0)){
		document.getElementById('date_error').innerHTML = "<br><font color='red'>�ڼ�������ۤ����򤷤Ƥ���������</font>";
		commit = false;
	}
	if(document.getElementById('new_date').selectedIndex == 0 && document.getElementById('new_hour').selectedIndex == 0 && document.getElementById('new_minute').selectedIndex == 0){
		document.getElementById('date_error').innerHTML = "<font color='red'>ɬ�ܹ���</font>";
		commit = false;
	}
	if(document.getElementById('new_name').value == ''){
		document.getElementById('name_error').innerHTML = "<font color='red'>ɬ�ܹ���</font>";
		commit = false;
	}
	if(document.getElementById('new_email').value == ''){
		document.getElementById('email_error').innerHTML = "<font color='red'>ɬ�ܹ���</font>";
		commit = false;
	}else{
		if(!/(\S)+[@]{1}(\S)+[.]{1}(\w)+/.test(document.getElementById('new_email').value)){
			document.getElementById('email_error').innerHTML = "<br><font color='red'>�᡼�륢�ɥ쥹�������������Ϥ�������</font>";
			commit = false;
		}
	}
	if(document.getElementById('new_product').value == ''){
		document.getElementById('product_error').innerHTML = "<font color='red'>ɬ�ܹ���</font>";
		commit = false;
	}
	if(document.getElementById('new_character').value == ''){
		document.getElementById('character_error').innerHTML = "<font color='red'>ɬ�ܹ���</font>";
		commit = false;
	}
	if (commit){
		return true;
	} else {
		return false
	}
}
</script>
    <p class="pageBottom"></p>
  </div>
  <!-- body_text_eof //-->
  <div id="r_menu">
    <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
    <!-- right_navigation_eof //-->
  </div>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
