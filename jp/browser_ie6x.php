<?php
/*
  browser.php,v1.0 2007/01/13

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

	require('includes/application_top.php');

	//�־���̾�פˤĤ��ƤΤ��䤤��碌
	define('HEADING_TITLE', 'Internet Explorer6������ˤĤ���');
	define('NAVBAR_TITLE', '�֥饦��������');
	
	$breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_BROWSER_IE6X));
?>
<?php page_head();?>
</head>
<body>
	<div class="body_shadow" align="center">
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<!-- header_eof //-->
		<!-- body //-->
		<table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
			<tr>
				<td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
					<!-- left_navigation //-->
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					<!-- left_navigation_eof //-->
				</td> 
				<!-- body_text //--> 
				<td valign="top" id="contents">
					<h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1> 
					<div>
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top" class="contents">
									<p>������åԥ󥰥����ƥ�ϡ��֥饦���ν������Τޤޤ�ư���褦�˺���Ƥ���ޤ���<br>
									����åԥ󥰤��Ǥ��ʤ����ϡ��ʲ��μ����������Ƥ򤪳Τ��᤯��������</p>
									<div class="dot">&nbsp;</div>
									<img src="images/browser/browser01.gif" width="500" height="190" alt="���󥿡��ͥåȥ��ץ����">
									<p>Internet&nbsp;Explorer�ξ��ʥ�˥塼����Υġ���Ϥ����򤷡����ˡΥ��󥿡��ͥåȥ��ץ����Ϥ����򤷤Ƥ���������</p>
									<div class="dot">&nbsp;</div>
									<img src="images/browser/browser02.gif" width="471" height="425" alt="�ץ饤�Х�������">
									<p>���󥿡��ͥåȥ��ץ������̤������ޤ��ΤǡΥץ饤�Х����Ϥ򥯥�å����ץ饤�Х���������̤򳫤��ޤ���<br>
									���饤���ΤĤޤߤ��ư���ơ���Ϥ����򤷡���Ŭ�ѡϤ򥯥�å����ޤ���<br>
									<span class="red">��</span>&nbsp;������Ρδ���ϥܥ��󤬲�����褦�Ǥ����饯��å����ơ���Ŭ�ѡϤ򥯥�å����Ƥ���������</p>
									<div class="dot">&nbsp;</div>
									<p class="red"><b>�嵭������Ǥ��褷�ʤ����ϡ�³���Ʋ����������ԤäƤ���������</b></p>
									<div class="dot">&nbsp;</div>
									<img src="images/browser/browser03.gif" width="471" height="425" alt="�ץ饤�Х����ܺ�����">
									<p>���ˡξܺ�����Ϥ򥯥�å������ץ饤�Х�������ξܺٲ��̤򳫤��ޤ���</p>
									<div class="dot">&nbsp;</div>
									<img src="images/browser/browser04.gif" width="440" height="281" alt="cookie������">
									<p>�μ�ưCookie�������񤭤���Ϥȡξ�˥��å����Cookie����Ĥ���Ϥ˥����å�����Cookie��ͭ���ˤ��ޤ������θ��OK�ϥܥ���򥯥�å����Ƥ���������</p>
									<div class="dot">&nbsp;</div>
									<img src="images/browser/browser05.gif" width="471" height="426" alt="��٥�Υ������ޥ���">
									<p>���󥿡��ͥåȥ��ץ������̤ΡΥ������ƥ��Ϥ򥯥�å����Υ�٥�Υ������ޥ����Ϥ򥯥�å����Ƥ���������</p>
									<div class="dot">&nbsp;</div>
									<img src="images/browser/browser06.gif" width="410" height="383" alt="�����ƥ��֡�������ץ�">
									<p>�������ƥ���������̤������ޤ����Υ����ƥ���&nbsp;������ץȡϤ��ͭ���ˤ���Ϥ˥����å�����JavaScript��ͭ���ˤ��ޤ������θ��OK�ϥܥ���򥯥�å����Ƥ���������</p>
									<div class="dot">&nbsp;</div>
									<p>�ʾ������ϴ�λ�Ǥ�������åԥ󥰤򤪳ڤ��ߤ���������</p>
								</td>
							</tr>
						</table>
					</div>
				</td>
				<!-- body_text_eof //-->
				<td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
					<!-- right_navigation //-->
					<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
					<!-- right_navigation_eof //-->
				</td>
			</tr>
		</table>
		<!-- body_eof //-->
		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
	</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
