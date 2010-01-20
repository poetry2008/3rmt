<?php
/* *********************************************************
  �⥸�塼��̾: users_log.php
 * 2002-05-13
 * Naomi Suzukawa
 * suzukawa@bitscope.co.jp
  ----------------------------------------------------------
�桼������������

  ���ѹ�����
	2003-04-07 $HTTP_POST_VERS ���б��������PHP �����ѡ������Х��ѿ�[$_POST]�ؤ��б��ϼ���Ȥ����
	2003-04-16 ������֤���Ť�ǧ�ڥ��������뵡ǽ������������ꤵ�줿���֤Υ���ä��Ƥ��ʤ��ä���
********************************************************* */

/* ===============================================
	global ���
 ============================================== */
// �ơ��֥�̾
  define('TABLE_LOGIN', 'login');
// ����ɽ���Կ�
  define('LOGIN_LOG_MAX_LINE', '30');
// MySQL �� limit ��¾��DB�Ǥϰۤʤ�ʸˡ�ʤΤǽ�����ɬ�ס�
  define('TABLE_LIMIT_OFFSET', 'limit %s,' . LOGIN_LOG_MAX_LINE);

/* ===============================================
	global �ѿ�
 ============================================== */
	$TableBorder = 'border="0"';				// �ơ��֥롧��������
	$TableCellspacing = 'cellspacing="3"';		// �ơ��֥롧����δֳ�
	$TableCellpadding = 'cellpadding="3"';		// �ơ��֥롧����Υޡ�����
	$TableBgcolor = 'bgcolor="#FFFFFF"';		// �ơ��֥롧�طʿ�

	$ThBgcolor = 'bgcolor="Gainsboro"';			// �إå����롧�طʿ�
	$TdnBgcolor = 'bgcolor="WhiteSmoke"';		// ���롧����̾�طʿ�

	$FontColor = 'color="#009900"';				// �ե���ȡ��ޡ�����

/* --------------------------------
2003-04-07 add 
$HTTP_POST_VERS ���б�������
��PHP �����ѡ������Х��ѿ�[$_POST]�ؤ��б��ϼ���Ȥ����
-------------------------------- */
	if (isset($HTTP_POST_VERS['lm'])) { $lm = $HTTP_POST_VERS['lm']; }
	if (isset($HTTP_POST_VERS['jp'])) { $jp = $HTTP_POST_VERS['jp']; }
	if (isset($HTTP_POST_VERS['pp'])) { $pp = $HTTP_POST_VERS['pp']; }
	if (isset($HTTP_POST_VERS['np'])) { $np = $HTTP_POST_VERS['np']; }
	if (isset($HTTP_POST_VERS['aval'])) { $aval = $HTTP_POST_VERS['aval']; }
//2003-07-16 hiroshi_sato add 2 line
        if (isset($HTTP_POST_VARS['sp'])) { $sp = $HTTP_POST_VARS['sp']; }
        if (isset($HTTP_POST_VARS['execute_delete'])) { $execute_delete = $HTTP_POST_VARS['execute_delete']; }

/* ===============================================
	�쥳���ɼ��� sql ʸ���������ؿ���Select��
 ============================================== */
/*--------------------------------------
	��  ǽ : ��������������ΰ���ɽ��
	��  �� : $oresult		- (i) �쥳���ɥ��֥�������
	����� : �ʤ�
 --------------------------------------*/
function show_loginlog_list($oresult) {

	// �ǡ��������ɽ������
	$rec_c = 1;
	while ($arec = tep_db_fetch_array($oresult)) {			// �쥳���ɤ����
		$naddress = (int)$arec['address'];		// IP���ɥ쥹����
		$saddress = '';
		for ($i=0; $i<4; $i++) {
			if ($i) $saddress = ($naddress & 0xff) . '.' . $saddress;
			else $saddress = (string)($naddress & 0xff);
			$naddress >>= 8;
		}

		if ($rec_c % 2) echo "<tr " . $GLOBALS['TdnBgcolor'] . ">\n";
		else echo "<tr>\n";
//		echo '<td class="main">' . $arec['sessionid'] . "</td>\n";		// Session ID

		// �桼��
		echo '<td class="main" >' . $arec['account'] . "</td>\n";
		// ����������
		echo '<td class="main" >' . $arec['logintime'] . "</td>\n";
		// �����󥹥ơ�����
		echo '<td class="main" >' . $arec['loginstatus'] . ' <small>[' . $GLOBALS['a_sts_in'][$arec['loginstatus']] . ']</small>' . "</td>\n";
		// �ǽ�������������
		echo '<td class="main" >' . $arec['lastaccesstime'] . "</td>\n";
		// �������ȥ��ơ�����
		if ($arec['logoutstatus']) {
			echo '<td class="main" >' . $arec['logoutstatus'] . ' <small>[' . $GLOBALS['a_sts_out'][$arec['logoutstatus']] . ']</small>' . "</td>\n";
		}
		else {
			echo '<td class="main" >&nbsp;</small>' . "</td>\n";
		}
		// ���ɥ쥹
		echo '<td class="main" >' . $saddress . "</td>\n";

		echo "</tr>\n";
		$rec_c++;
	}
}

/*--------------------------------------
	��  ǽ : �ڡ�������ܥ���ɽ��
	��  �� : $nrow		- (i) �쥳���ɷ���ʰ����Կ���
	����� : �쥳���ɷ��
 --------------------------------------*/
function show_page_ctl($nrow) {

	// ��쥳���ɷ������
	$ssql = "select count(*) as rc from " . TABLE_LOGIN;
	@$oresult = tep_db_query($ssql);
	if (!$oresult) {											// ���顼���ä��Ȥ�
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;
	}

	$arec = tep_db_fetch_array($oresult);						// �쥳���ɤμ���
	echo tep_draw_hidden_field("lm", $GLOBALS['lm']);			// ���ߤΥڡ����򱣤����ܤ˥��åȤ���

	// �ܥ���ɽ��
	if ($GLOBALS['lm'] >= LOGIN_LOG_MAX_LINE) {
		echo tep_draw_input_field("pp", BUTTON_PREVIOUS_PAGE, '', FALSE, "submit", FALSE);	// ���ڡ���
	}
	if ($GLOBALS['lm'] + LOGIN_LOG_MAX_LINE < $arec['rc']) {
		echo tep_draw_input_field("np", BUTTON_NEXT_PAGE, '', FALSE, "submit", FALSE);		// ���ڡ���
	}

	$page_count = ceil($arec['rc'] / LOGIN_LOG_MAX_LINE);
	for ($i=1; $i<=$page_count; $i++) {
		$lm_ = ($i-1) * LOGIN_LOG_MAX_LINE;
		$asp[$i-1]['id'] = $lm_;
		$asp[$i-1]['text'] = $i;
	}
	echo '&nbsp;&nbsp;';
	$GLOBALS['sp'] = $GLOBALS['lm'];							// �ץ�������˥塼�������ͥ��å�
	echo tep_draw_pull_down_menu("sp", $asp, $GLOBALS['lm']);	// �ץ�������˥塼��ɽ��
	echo tep_draw_input_field("jp", BUTTON_JUMP_PAGE, '', FALSE, "submit", FALSE);		// �ڡ����إ�����

	if ($GLOBALS['lm']) $c_page = ceil((int)$GLOBALS['lm'] / LOGIN_LOG_MAX_LINE);
	$c_page++;
	echo '<font class="main">&nbsp;&nbsp;' . sprintf(TEXT_PAGE, $c_page,$page_count,$nrow,$arec['rc']) . '</font>' . "\n";
	echo "<br>\n";

}

/*--------------------------------------
	��  ǽ : ����������������� sql ʸ��������
	��  �� : �ʤ�
	����� : select ��ʸ����
 --------------------------------------*/
function makeSelectLoginLog() {

	$s_select = "select * from " . TABLE_LOGIN;
	$s_select .= " order by logintime desc";		// �������������εս��֤˥ǡ������������
	if (!isset($GLOBALS['lm'])) $GLOBALS['lm'] = 0;
	$s_select .= " " . sprintf(TABLE_LIMIT_OFFSET,$GLOBALS['lm']);

	return $s_select;

}

/* ==============================================
	����ɽ���ؿ��ʥᥤ���
 ============================================= */
/*--------------------------------------
	��  ǽ : �����������������ɽ��
	��  �� : �ʤ�
	����� : �ʤ�
 --------------------------------------*/
function UserLoginLog_list() {

	global $ocertify;						// �桼��ǧ�ڥ��֥�������

	PageBody('t', PAGE_TITLE_MENU_USER);	// �桼���������̤Υ����ȥ���ɽ���ʥ桼��������˥塼��

	// ���ߤΥڡ����ʥ쥳���ɼ������ϰ��֡�
	if ($GLOBALS['jp']) $GLOBALS['lm'] = (int)$GLOBALS['sp'];
	if ($GLOBALS['pp']) (int)$GLOBALS['lm'] -= LOGIN_LOG_MAX_LINE;
	if ($GLOBALS['np']) (int)$GLOBALS['lm'] += LOGIN_LOG_MAX_LINE;

	// �����������������
	$ssql = makeSelectLoginLog();
	@$oresult = tep_db_query($ssql);
	if (!$oresult) {											// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_NO_LOGINFO;						// ��å�����ɽ��
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;
	}

	$nrow = tep_db_num_rows($oresult);							// �쥳���ɷ���μ���
	if ($nrow > 0) {											// �쥳���ɤ������Ǥ����Ȥ�

		// �ơ��֥륿���γ���
		echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
		echo "<tr>\n";
//		echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_LOGINID . '</td>' . "\n";			// Session ID
		echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_USER . '</td>' . "\n";				// �桼��
		echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_LOGINTIME . '</td>' . "\n";			// ����������
		echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_STATUS . '</td>' . "\n";				// ���ơ�����
		echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_LAST_ACCESSTIME . '</td>' . "\n";	// �ǽ�������������
		echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_STATUS . '</td>' . "\n";				// ���ơ�����
		echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_ADDRESS . '</td>' . "\n";			// ���ɥ쥹
		echo "</tr>\n";
		show_loginlog_list($oresult);		// ��������������ΰ���ɽ��
		echo "</table>\n";

		echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));		// <form>�����ν���
		echo "<br>\n";
		show_page_ctl($nrow);				// �ڡ�������ܥ����ɽ��
		echo "<br>\n";

		// �ơ��֥륿���γ���
		echo '<table border="0" cellspacing="1" cellpadding="1">' . "\n";
		echo "<tr>\n";

		// ���κ��
		echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TEXT_INFO_DELETE_DAY . "</td>\n";
		echo '<td class="main" colspan="2">';
		echo tep_draw_input_field("aval[span]", $ocertify->login_log_span, 'size="1" maxlength="3"', FALSE, 'text', FALSE);
		echo TEXT_INFO_DELETE_FORMER_DAY . "</td>\n";

		echo '<td class="main">';
		// �ܥ���ɽ��
		echo tep_draw_input_field("execute_delete", BUTTON_DELETE_LOGINLOG, "onClick=\"return formConfirm('delete')\"", FALSE, "submit", FALSE);	// ���κ��
		echo "</td></tr></table>\n";
		echo "</form>\n";						// �ե�����Υեå���
	}
	if ($oresult) @tep_db_free_result($oresult);					// ��̥��֥������Ȥ�������

	return TRUE;
}

/* ==============================================
	�����¹Դؿ�
 ============================================= */
/*--------------------------------------
	��  ǽ : ������֤���Ť�ǧ�ڥ���������
	��  �� : �ʤ�
	����� : true/false
 --------------------------------------*/
function LoginLogDelete_execute() {

	if ( 0 < $GLOBALS['aval']['span']) {
		$sspan_date = date ("Y-m-d H:i:s", mktime (date(H), date(i), date(s),date(m), date(d) - (int)$GLOBALS['aval']['span'], date(Y)));
		$result = tep_db_query("delete from login where logintime < '$sspan_date'");
	}
	if ($oresult) @tep_db_free_result($oresult);		// ��̥��֥������Ȥ�������

	return TRUE;

}

/*--------------------------------------
	��  ǽ : ��ǧ��å������Τ���� JavaScript
	��  �� : �ʤ�
	����� : true/false
 --------------------------------------*/
function putJavaScript_ConfirmMsg() {

echo '
<script language="JavaScript1.1">
<!--
function formConfirm(type) {
  if (type == "delete") {
      rtn = confirm("'. JAVA_SCRIPT_INFO_DELETE . '");
  }
  if (rtn) return true;
  else return false;
}
//-->
</script>
';

}

/*--------------------------------------
	��  ǽ : �ڡ����إå���ɽ��
	��  �� : �ʤ�
	����� : �ʤ�
 --------------------------------------*/
function PageHeader() {
	echo '<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">' . "\n";
	echo '<html ' . HTML_PARAMS . '>' . "\n";
	echo '<head>' . "\n";
	echo '<meta http-equiv="Content-Type" content="text/html; charset=' . CHARSET . '">' . "\n";
	echo '<title>' . TITLE . '</title>' . "\n";
	echo '<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">' . "\n";
	putJavaScript_ConfirmMsg();						// ��ǧ��å�������ɽ������ JavaScript
	echo '</head>' . "\n";
	echo '<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">' . "\n";
	echo '<!-- header //-->' . "\n";
	require(DIR_WS_INCLUDES . 'header.php');
	echo '<!-- header_eof //-->' . "\n";
}

/*--------------------------------------
	��  ǽ : �ڡ����Υ쥤�����ȥơ��֥�ɽ��
	��  �� : $mode		-(i)	ʸ���󡧥⡼�ɡ�t:�塢u:����
	����� : �ʤ�
 --------------------------------------*/
function PageBodyTable($mode='t') {
	switch ($mode) {
	case 't':
		echo '<!-- body //-->' . "\n";
		echo '<table border="0" width="100%" cellspacing="2" cellpadding="2">' . "\n";
		echo '  <tr>' . "\n";
		echo '    <td width="' . BOX_WIDTH . '" valign="top"><table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">' . "\n";
		break;
	case 'u':
		echo '  </tr>' . "\n";
		echo '</table>' . "\n";
		echo '<!-- body_eof //-->' . "\n";
		break;
	} 
}

/*--------------------------------------
	��  ǽ : �ڡ����ܥǥ���ɽ��
	��  �� : $mode		-(i)	ʸ���󡧥⡼�ɡ�t:�塢u:����
			 $stitle	-(i)	ʸ���󡧥ܥǥ��Υ����ȥ�
	����� : �ʤ�
 --------------------------------------*/
function PageBody($mode='t', $stitle = "") {
	switch ($mode) {
	case 't':
		echo '<!-- body_text //-->' . "\n";
		echo '    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n";
		echo '      <tr>' . "\n";
		echo '        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">' . "\n";
		echo '          <tr>' . "\n";
		echo '            <td class="pageHeading">' . HEADING_TITLE . '</td>' . "\n";
		echo '            <td class="pageHeading" align="right">';
		echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT);
		echo '</td>' . "\n";
		echo '          </tr>' . "\n";
		echo '        </table></td>' . "\n";
		echo '      </tr>' . "\n";
		echo '      <tr>' . "\n";
		echo '        <td>' . "\n";
		break;
	case 'u':
		echo '        </td>' . "\n";
		echo '      </tr>' . "\n";
		echo '    </table></td>' . "\n";
		echo '<!-- body_text_eof //-->' . "\n";
		break;
	} 
}

/*--------------------------------------
	��  ǽ : �ڡ����եå���ɽ��
	��  �� : �ʤ�
	����� : �ʤ�
 --------------------------------------*/
function PageFooter() {
	echo "<!-- footer //-->\n";
	require(DIR_WS_INCLUDES . 'footer.php');
	echo "\n<!-- footer_eof //-->\n";
	echo "<br>\n";
	echo "</body>\n";
	echo "</html>\n";
}

/* *************************************

   �桼�������ݼ���̤Υץ��������ʥᥤ���

 ************************************* */

  require('includes/application_top.php');

	if ($execute_delete) {		// ��������������κ��
		LoginLogDelete_execute();
		$lm = 0;
	}

	PageHeader();				// �ڡ������إå���ɽ��
	PageBodyTable('t');			// �ڡ����Υ쥤�����ȥơ��֥롧���ϡʥʥӥ��������ܥå�������礹��ơ��֥볫�ϡ�

	// ���ʥӥ��������ܥå�����ɽ��
	echo "<!-- left_navigation //-->\n";		// 
	include_once(DIR_WS_INCLUDES . 'column_left.php');
	echo "\n<!-- left_navigation_eof //-->\n";
	echo "    </table></td>\n";

	// �����󥹥ơ��������󥻥å�
	$aval = explode(',',TEXT_INFO_STATUS_IN);
	if (is_array($aval)) {
		while (list($key,$val) = each($aval)) {
			$sts = explode(':',$val);
			$a_sts_in[$sts[0]] = $sts[1];
		}
	}
	// �������ȥ��ơ��������󥻥å�
	$aval = explode(',',TEXT_INFO_STATUS_OUT);
	if (is_array($aval)) {
		while (list($key,$val) = each($aval)) {
			$sts = explode(':',$val);
			$a_sts_out[$sts[0]] = $sts[1];
		}
	}

	// ����ɽ��
	UserLoginLog_list();		// ����������ɽ��

	PageBody('u');				// �ڡ����ܥǥ��ν�λ
	PageBodyTable('u');			// �ڡ����Υ쥤�����ȥơ��֥롧��λ
	PageFooter();				// �ڡ����եå���ɽ��

	require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
