<?php
  require('includes/application_top.php');
  $breadcrumb->add('�ե꡼�᡼��ǥ᡼�뤬�������ʤ�����', tep_href_link('email_trouble.php'));
?>
<?php page_head();?>
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<!-- left_navigation //-->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- left_navigation_eof //-->
<!-- body_text //-->
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading"><?php echo '�ե꡼�᡼��ǥ᡼�뤬�������ʤ�����'; ?></h1>
<div class="box">
<div class="content_email01">
  <div id="bgn_content">
    <div id="wrapper_kiyaku">
      <p>
        <span class="txt_blue">*****@yahoo.co.jp��*****@hotmail.com��*****@msn.com��AOL�ʤɤΥե꡼�᡼������Ѥˤʤ��Ƥ����硢</span><br>
        �嵭�Υɥᥤ��Ǥ���Ͽ���줿���ǡ����Ҥ���Υ᡼�뤬�Ϥ��ʤ��Ȥ���������ĺ���Ƥ���ޤ��� </p>
      <p>
        <br>
        ����ϡ�Yahoo!�᡼�롢hotmail��msn�᡼�롢AOL�μ�����³���ǡ����ǥ᡼��פȤ��ƽ����򤵤�Ƥ����ǽ�����������ޤ���<br>
        ���ν����ϳƥե꡼�᡼�뤬���ĵ�ǽ�ǡ�������¿���Υ����Ȥ���Υ᡼�������ǥ᡼��פȤ����ӽ�������Ŭ�˥᡼�����Ѥ��뤿��Τ�ΤǤ���<br>
        �嵭�Τ褦�ʥɥᥤ��򤴻��Ѥξ��ˤϡ�<span class="txt_blue">���Ҥ���Υ᡼�����̾�᡼��פȤ��Ƽ������Ĥ򤤤�����ɬ�פ��������ޤ���</span><br>
      </p>
      <p>
        <br>
        �ʲ��˳ƥե꡼�᡼�����˼������Ĥ�������򵭤��ޤ��� </p>
      <br>
      <br>
      <h3><span class="txt_bold">Yahoo!�᡼�롢Yahoo!BB�᡼��&nbsp;&nbsp;�������Ĥ�������</span></h3>
      <p>
      <ol>
        <li>Yahoo!�᡼��˥����󤷤�[�᡼�륪�ץ����]�򥯥�å�</li>
        <li>[�ե��륿���ȼ�����������] �� [��������]���˥���å�</li>
        <li>�����ʲ��Τ褦���ѹ����ޤ���<br>
          ��From�����ʤ�ޤ�ˡ�<span class="txt_blue"><?php echo STORE_DOMAIN;?></span>�ˡ�&nbsp;&nbsp;��ư��ե�����ּ���Ȣ�� </li>
      </ol>
      </p>
      <br>
      <h3><span class="txt_bold">hotmail��msnmail&nbsp;&nbsp;�������Ĥ�������</span></h3>
      <p>
      <ol>
        <li>hotmail �˥����󥤥󤷤�[���ץ����] �򥯥�å�</li>
        <li>���ǥ᡼�������[�����եꥹ��]�򥯥�å�</li>
        <li><?php echo STORE_NAME;?>��������Ҥ����Ѥ���ɥᥤ���<span class="txt_blue"><?php echo STORE_DOMAIN;?></span>�פ��ɲä��ޤ�</li>
        <li>�Ǹ�ˡ�OK�פ򥯥�å����ƽ�λ���ޤ�</li>
      </ol>
      </p>
      <br>
      <h3><span class="txt_bold">AOL&nbsp;&nbsp;�������Ĥ�������</span></h3>
      <p> �����ѴĶ��ˤ��AOL��³���եȡ�AOL Communicator�פ����꤬ɬ�פǤ���
      <ol>
        <li>�����å������ɤΥ᡼�륳��ȥ���򻲾Ȥ��ޤ�</li>
        <li>[���ǥ᡼��ե��륿]�ǡ�<span class="txt_blue"><?php echo STORE_DOMAIN;?></span>�פμ�������Ĥ�������ˤ��Ʋ�����</li>
      </ol>
      </p>
      <br>
      <h3><span class="txt_bold">�嵭�ʳ��Υ᡼�륢�ɥ쥹�����Ѥξ��</span></h3>
      <p> �嵭�ʳ��Υ᡼�륢�ɥ쥹�����Ѥ����ǡ�Ʊ�ͤ�<?php echo STORE_NAME;?>��������Ҥ���Υ᡼�뤬�Ϥ��ʤ��Ȥ������⡢Ʊ�ͤθ��������ǥ᡼�������ǽ�ˤ��ͤ����ޤ���<br>
        ������Ǥ�����������Υޥ˥奢���������ξ塢<?php echo STORE_NAME;?>��������Ҥ����Ѥ���ɥᥤ���<span class="txt_blue"><?php echo STORE_DOMAIN;?></span>�פ������������᡼��μ������Ĥ����ꤷ�Ʋ�������<br>
      </p>
      <br>
      <br>
      <br>
    </div>
    <!-- end of wrapper_mail_trouble -->
  </div>
  <!-- end of bgn_content -->
</div>

      </div></div>
      <!-- body_text_eof //--> 
<!-- right_navigation //--> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
<!-- right_navigation_eof //-->
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html>

