<?php
/*
  $Id: reviews.php,v 1.3 2004/05/26 05:05:11 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  define('HEADING_TITLE','����ƥ�Ķ�������');
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();"> 
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
          <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td> 
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr> 
          <td>
            <table width="100%"  border="0" cellspacing="0" cellpadding="2"> 
              <tr> 
                <td colspan="2" class="formAreaTitle">����</td> 
              </tr>
              <tr>
                <td width="150" valign="top" class="main">�ۡ���ڡ��������ȥ�</td>
                <td class="smallText"><?php echo tep_draw_input_field('title',$title,'style="width:250px;"');?><br>
�֥饦���Υ����ȥ�Ȥʤ�ޤ���</td>
              </tr> 
              <tr> 
                <td valign="top" class="main">�������</td> 
                <td class="smallText"><?php echo tep_draw_input_field('keywords',$title,'style="width:400px;"');?><br>
                  �ۡ���ڡ����Υ�����ɤ򥫥�ޡ�,�׶��ڤ�����Ϥ��Ƥ���������META������ȿ�Ǥ���ޤ���</td> 
              </tr>
              <tr>
                <td valign="top" class="main">�ۡ���ڡ�������</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('description',$title,'','','','style="width:400px;height:50px;"');?><br>
                  �ۡ���ڡ��������������Ϥ��Ƥ���������META������ȿ�Ǥ���ޤ���</td>
              </tr> 
              <tr> 
                <td valign="top" class="main">��ܥå�</td> 
                <td class="smallText"><input name="robots" type="radio" value="yes">
                index,follow 
                <input name="robots" type="radio" value="no">
                no<br>
                �������󥸥�˥���ǥ������������index.follow�˥����å�������Ƥ���������</td> 
              </tr> 
              <tr> 
                <td valign="top" class="main">�����</td> 
                <td class="smallText"><?php echo tep_draw_input_field('copyright',$title);?><br>
                  �ۡ���ڡ���������Ԥ����Ϥ��Ƥ���������</td> 
              </tr> 
              <tr> 
                <td colspan="2" class="formAreaTitle">�᡼��</td> 
              </tr> 

              <tr> 
                <td valign="top" class="main">E�᡼���̾</td> 
                <td class="smallText"><?php echo tep_draw_input_field('email_footer',$title);?><br>
                  ������������ƤΥ᡼��Υեå�����ɽ������ޤ���</td> 
              </tr> 
              <tr> 
                <td valign="top" class="main">�����Ͽ�᡼��</td> 
                <td class="smallText"><?php echo tep_draw_textarea_field('email_creat_account',$title,'','','','style="width:400px;height:50px;"');?><br>
                  ��������Ⱥ����������������᡼��Ǥ���</td> 
              </tr>
              <tr>
                <td valign="top" class="main">��ʸ�᡼��</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('email_order',$title,'','','','style="width:400px;height:50px;"');?><br>
                  ��ʸ�������������᡼��Ǥ���</td>
              </tr>
              <tr>
                <td valign="top" class="main">������</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('email_cod_table',$title,'','','','style="width:400px;height:50px;"');?><br>
                ��ʸ��������������������Υ᡼��Ǥ���ͭ���ˤ���ˤϥ⥸�塼�����ꢪ��ѥ⥸�塼�뢪��������ON�ˤ��Ƥ���������</td>
              </tr>
              <tr>
                <td valign="top" class="main">��Կ���</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('email_bank',$title,'','','','style="width:400px;height:50px;"');?><br>
                ��ʸ��������������������Υ᡼��Ǥ���ͭ���ˤ���ˤϥ⥸�塼�����ꢪ��ѥ⥸�塼�뢪��Կ�����ߤ�ON�ˤ��Ƥ���������</td>
              </tr>
              <tr>
                <td valign="top" class="main">͹�ؿ���</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('email_postal',$title,'','','','style="width:400px;height:50px;"');?><br>
                ��ʸ�������������͹�ؿ��ؤΥ᡼��Ǥ���ͭ���ˤ���ˤϥ⥸�塼�����ꢪ��ѥ⥸�塼�뢪͹�ؿ��ؤ�ON�ˤ��Ƥ���������</td>
              </tr>
              <tr>
                <td valign="top" class="main">���쥸�åȥ�����</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('email_cc',$title,'','','','style="width:400px;height:50px;"');?><br>
                ��ʸ������������륯�쥸�åȥ����ɤΥ᡼��Ǥ���ͭ���ˤ���ˤϥ⥸�塼�����ꢪ��ѥ⥸�塼�뢪���쥸�åȥ����ɤ�ON�ˤ��Ƥ���������</td>
              </tr>
              <tr>
                <td valign="top" class="main">����åץ�</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('logo',$title,'','','','style="width:400px;height:50px;"');?><br>
                  Ǽ�ʽ�����ɼ���������ڡ����Υإå����������ڡ����Υإå�����ɽ���������Ǥ���</td>
              </tr>
              <tr>
                <td valign="top" class="main">&nbsp;</td>
                <td class="smallText">&nbsp;</td>
              </tr> 
          </table></td> 
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
