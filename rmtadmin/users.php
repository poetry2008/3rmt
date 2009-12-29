<?php
/* *********************************************************
  �⥸�塼��̾: users.php
 * 2001/5/29
 *   modi 2002-05-10
 * Naomi Suzukawa
 * suzukawa@bitscope.co.jp
  ----------------------------------------------------------
�桼������

  ���ѹ�����
	2003-04-07 add 	$HTTP_POST_VERS ���б��������PHP �����ѡ������Х��ѿ�[$_POST]�ؤ��б��ϼ���Ȥ����
********************************************************* */

/* ===============================================
	global ���
 ============================================== */
// �ơ��֥�̾
  define('TABLE_USERS', 'users');
  define('TABLE_PERMISSIONS', 'permissions');

/* ===============================================
	global �ѿ�
 ============================================== */
	$TableBorder = 'border="0"';				// �ơ��֥롧��������
	$TableCellspacing = 'cellspacing="1"';		// �ơ��֥롧����δֳ�
	$TableCellpadding = 'cellpadding="0"';		// �ơ��֥롧����Υޡ�����
	$TableBgcolor = 'bgcolor="#FFFFFF"';		// �ơ��֥롧�طʿ�

	$ThBgcolor = 'bgcolor="Gainsboro"';			// �إå����롧�طʿ�
	$TdnBgcolor = 'bgcolor="WhiteSmoke"';		// ���롧����̾�طʿ�

/* --------------------------------
2003-04-07 add 
$HTTP_POST_VERS ���б�������
��PHP �����ѡ������Х��ѿ�[$_POST]�ؤ��б��ϼ���Ȥ����
-------------------------------- */
	if (isset($HTTP_POST_VARS['userid'])) { $userid = $HTTP_POST_VARS['userid']; }
	if (isset($HTTP_POST_VARS['aval'])) { $aval = $HTTP_POST_VARS['aval']; }
	if (isset($HTTP_POST_VARS['userslist'])) { $userslist = $HTTP_POST_VARS['userslist']; }
	if (isset($HTTP_POST_VARS['no_permission_list'])) { $no_permission_list = $HTTP_POST_VARS['no_permission_list']; }
	if (isset($HTTP_POST_VARS['permission_list'])) { $permission_list = $HTTP_POST_VARS['permission_list']; }
	if (isset($HTTP_POST_VARS['execute_user'])) { $execute_user = $HTTP_POST_VARS['execute_user']; }
	if (isset($HTTP_POST_VARS['execute_password'])) { $execute_password = $HTTP_POST_VARS['execute_password']; }
	if (isset($HTTP_POST_VARS['execute_permission'])) { $execute_permission = $HTTP_POST_VARS['execute_permission']; }
//2003-07-16 hiroshi_sato add 6 lines
        if (isset($HTTP_POST_VARS['execute_new'])) { $execute_new = $HTTP_POST_VARS['execute_new']; }
        if (isset($HTTP_POST_VARS['execute_insert'])) { $execute_insert = $HTTP_POST_VARS['execute_insert']; }
        if (isset($HTTP_POST_VARS['execute_update'])) { $execute_update = $HTTP_POST_VARS['execute_update']; }
        if (isset($HTTP_POST_VARS['execute_delete'])) { $execute_delete = $HTTP_POST_VARS['execute_delete']; }
        if (isset($HTTP_POST_VARS['execute_grant'])) { $execute_grant = $HTTP_POST_VARS['execute_grant']; }
        if (isset($HTTP_POST_VARS['execute_reset'])) { $execute_reset = $HTTP_POST_VARS['execute_reset']; }

/* ===============================================
	���ϥ����å��ؿ�
 ============================================== */

/*--------------------------------------
	��  ǽ : ̤���ϥ����å�
	��  �� : $s_val - (i) ��
	����� : "":�ϣ�,���顼��å�����:�Σ�
 --------------------------------------*/
function checkNotnull($s_val) {

	// �ͤ����Ϥ���Ƥ���Ȥ������å���Ԥ�
	if ($s_val == "") {
		return TEXT_ERRINFO_INPUT_NOINPUT;
	}
	return '';				// �����
}

/*--------------------------------------
	��  ǽ : ʸ������ܤΥ����å�������ɽ����
			 ����ɽ���ѥ�����Ȥ����ϥ����å�����Ⱦ�Ѻ��ߡ�
	��  �� : $s_val		-(i)	ʸ����. ʸ����
			 $s_ereg	-(i)	ʸ����. ����ɽ���ѥ�����ʾ�ά��:����ɽ�������å��򤷤ʤ���
	����� : "":�ϣ�,���顼��å�����:�Σ�
 -------------------------------------*/
function checkStringEreg($s_val, $s_ereg = "") {

	// �ͤ�̤���ϤΤȤ�������λ
	if ($s_val == "") return '';

	// ���顼Ƚ��
	if ($s_ereg && (ereg($s_ereg,$s_val) == false)) {
		return TEXT_ERRINFO_INPUT_ERR;
	}

	return '';						// �����
}

/*--------------------------------------
	��  ǽ : ʸ���������å�
	��  �� : $s_val			-(i)	ʸ����. ʸ����
			 $n_len			-(i)	����. �Х��ȿ��ʾ�ά��:��ʸ����
	����� : "":�ϣ�,���顼��å�����:�Σ�
 -------------------------------------*/
function checkLength_ge($s_val, $n_len) {

	// �ͤ�̤���ϤΤȤ�������λ
	if ($s_val == "") return '';

	// ���顼Ƚ��
	$n_val_len = strlen($s_val);
	if ($n_len > 0 && $n_len > $n_val_len) {
		return sprintf(TEXT_ERRINFO_INPUT_LENGTH, $n_len);
	}

	return '';						// �����
}

/*--------------------------------------
	��  ǽ : ���顼��å�����ɽ��
	��  �� : $a_error -(i) ���顼��å�����
	����� : �ʤ�
 --------------------------------------*/
function print_err_message($a_error) {

	$stable_bgcolor = 'bgcolor="#FFFFFF"';		// �ơ��֥��طʿ�
	$sfont_color = 'color="#FF0000"';			// �ե���ȥ��顼�ʥ��顼����

	echo '<font class="main" ' . $sfont_color . '">';
	echo TABLE_HEADING_ERRINFO;		// ���顼��å�����ɽ�������ȥ�
	echo "<br>\n";

	//-- ���顼ɽ�� --
	for ($i = 0 ; $i < count($a_error) ; $i++) {
		echo $a_error[$i];
		echo "<br>\n";
	}

	echo "</font>\n";

}

/* -------------------------------------
	��  ǽ : ���顼��å���������˥��顼��å��������å�
	��  �� : $a_error - (o) ���󥨥顼��å�����
			 $s_errmsg - (i) ���顼��å�����
	����� : �ʤ�
 ------------------------------------ */
function set_errmsg_array(&$a_error,$s_errmsg) {

	$a_error[] = $s_errmsg;
}

/* ===============================================
	�쥳���ɼ��� sql ʸ���������ؿ���Select��
 ============================================== */
/*--------------------------------------
	��  ǽ : �桼��������� sql ʸ��������
	��  �� : $s_user_ID - (i) �桼���ɣġʾ�ά�ġ�
	����� : select ��ʸ����
 --------------------------------------*/
function makeSelectUserInfo($s_user_ID = "") {

	$s_select = "select * from " . TABLE_USERS;
	$s_select .= ($s_user_ID == "" ? "" : " where userid = '$s_user_ID'");
	$s_select .= " order by userid;";			// �桼���ɣĤν��֤˥ǡ������������
	return $s_select;

}

/*--------------------------------------
	��  ǽ : �桼�����¤�ޤ������� sql ʸ��������
	��  �� : $nmode 	- (i) �����������⡼�ɡ�0:���̥桼������[������]��1:�����Լ�����
	����� : select ��ʸ����
 --------------------------------------*/
function makeSelectUserParmission($nmode=0) {

	// �桼�����¤�ޤ�������
	$s_select = "select u.userid as userid, u.name as name";
	$s_select .= " from " . TABLE_USERS . " u, " . TABLE_PERMISSIONS . " p";
	$s_select .= " where u.userid = p.userid";
	if ($nmode == 0) $s_select .= " and p.permission < 15";		// �����⡼�ɤˤ�� where ��ξ����Խ�����
	else $s_select .= " and p.permission = 15";
	$s_select .= " order by u.userid";							// �桼���ɣĤν��֤˥ǡ������������

	return $s_select;

}

/* ==============================================
	�ơ��֥빹�� sql ʸ���������ؿ���Insert��Update��Delete��
 ============================================= */
/*--------------------------------------
	��  ǽ : �����桼������Ͽ�ʥ桼���������桼�����¥ơ��֥���ɲ���Ͽ��
	��  �� : $aval		-(i)	Ϣ�������ɲä���ǡ���
			 $nmode 	-(i)	�����������⡼�ɡ�0:�桼�������ơ��֥��ɲ�sql[������]��1:�桼�����¥ơ��֥��ɲ�sql��
	����� : �ʤ�
 --------------------------------------*/
function makeInsertUser($aval, $nmode=0) {

	$ssql = "insert into ";
	if ($nmode == 0) {
		// DES �ǰŹ沽����
		$cryot_password = (string) crypt($aval['password']);
		// �桼�������ơ��֥�ؤ��ɲ� sql ʸ��������
		$ssql .= TABLE_USERS . " values (";
		$ssql .= "'" . $aval['userid'] . "'";
		$ssql .= ",'$cryot_password'";
		$ssql .= ",'" . $aval['name'] . "'";
		$ssql .= ",'" . $aval['email'] . "'";
		$ssql .= ")";
	} else {
		// �桼�����¥ơ��֥�ؤ��ɲ� sql ʸ��������
		$ssql .= TABLE_PERMISSIONS . " values (";
		$ssql .= "'" . $aval['userid'] . "'";
		$ssql .= ",7";
		$ssql .= ")";
	}

	return $ssql;
}

/*--------------------------------------
	��  ǽ : �桼������ơ��֥�ι���
	��  �� : $aval		-(i)	Ϣ�����󡧹�������ǡ���
			 $nmode		-(i)	�����⡼�ɡ�0:��̾��e-mail��1:�ѥ���ɡ�
	����� : �ʤ�
 --------------------------------------*/
function makeUpdateUser($aval, $nmode=0) {

	$ssql = "update " . TABLE_USERS . " set";
	if ($nmode == 0) {
		$ssql .= " name='" . $aval['name'] . "'";
		$ssql .= ", email='" . $aval['email'] . "'";
	} else {
		// DES �ǰŹ沽����
		$cryot_password = (string) crypt($aval['password']);
		$ssql .= " password='$cryot_password'";
	}
	$ssql .= " where userid='" . $GLOBALS['userid'] . "'";

	return $ssql;
}

/*--------------------------------------
	��  ǽ : �桼���κ�����ʥ桼���������桼�����¥ơ��֥뤫������
	��  �� :  $nmode 	-(i)	�����������⡼�ɡ�0:�桼�������ơ��֥���sql[������]��1:�桼�����¥ơ��֥���sql��
	����� : �ʤ�
 --------------------------------------*/
function makeDeleteUser($nmode=0) {

	$ssql = "delete from ";
	if ($nmode == 0) {
		// DES �ǰŹ沽����
		$cryot_password = (string) crypt($aval['password']);
		// �桼�������ơ��֥�ؤ��ɲ� sql ʸ��������
		$ssql .= TABLE_USERS;
	} else {
		// �桼�����¥ơ��֥�ؤ��ɲ� sql ʸ��������
		$ssql .= TABLE_PERMISSIONS;
	}
	$ssql .= " where userid='" . $GLOBALS['userid'] . "'";

	return $ssql;
}

/*--------------------------------------
	��  ǽ : �桼�����¥ơ��֥�ι���
	��  �� : $nmode		-(i) �����⡼�ɡ�0:grant��1:revoke��
			 $susers	-(i) �桼��ID
	����� : �ʤ�
 --------------------------------------*/
function makeUpdatePermission($nmode=0, $susers) {

	$ssql = "update " . TABLE_PERMISSIONS . " set";
	if ($nmode == 0) 						// ���¤�Ϳ����
		$ssql .= " permission=15";
	else $ssql .= " permission=7";			// ���¤��ä�
	$ssql .= " where userid='$susers'";

	return $ssql;

}

/* ==============================================
	����ɽ���ؿ��ʥᥤ���
 ============================================= */
/*--------------------------------------
	��  ǽ : �桼��������˥塼��ɽ����
	��  �� : �ʤ�
	����� : �ʤ�
 --------------------------------------*/
function UserManu_preview() {

	global $ocertify;						// �桼��ǧ�ڥ��֥�������

	PageBody('t', PAGE_TITLE_MENU_USER);			// �桼���������̤Υ����ȥ���ɽ���ʥ桼��������˥塼��

	// �桼���������
	if ($ocertify->npermission < 15) $ssql = makeSelectUserInfo($ocertify->auth_user);		// ���̥桼���ΤȤ�
	if ($ocertify->npermission == 15) $ssql = makeSelectUserInfo();		// �����ԤΤȤ�

	@$oresult = tep_db_query($ssql);
	if (!$oresult) {											// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_NO_USERINFO;						// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));						// <form>�����ν���
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";										// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;
	}

	$nrow = tep_db_num_rows($oresult);									// �쥳���ɷ���μ���
	// �ơ��֥륿���γ���
	echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
	echo "<tr>\n";
	echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));		// <form>�����ν���

	if ($nrow == 1) {													// �оݥǡ�����1����ä��Ȥ�
		// ���ܥ����ȥ��1����ˤν���
		echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_USER . '</td>' . "\n";		// �桼��
		$nLsize = 'size="1"';											// �ꥹ�ȤΥ������ѿ���1�򥻥å�
	} elseif ($nrow > 1) {												// �оݥǡ�����1��ʾ���ä��Ȥ�
		// ���ܥ����ȥ��1����ˤν���
		echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_USER_LIST . '</td>' . "\n";	// �桼������
		$nLsize = 'size="5"';											// �ꥹ�ȤΥ������ѿ���5�򥻥å�
	}
	echo "</tr>\n";

	// �ꥹ�ȥܥå�����ɽ������ǡ���������˥��åȤ���
	$i=0;
	while ($arec = tep_db_fetch_array($oresult)) {			// �쥳���ɤ����
		$ausers[$i]['id'] = $arec['userid'];
		$ausers[$i]['text'] = $arec['name'];
		$i++;
	}

	echo '<tr><td>';													// �ǡ�������
	echo tep_draw_pull_down_menu("userslist", $ausers, $ocertify->auth_user, $nLsize);	// �ꥹ�ȥܥå�����ɽ��
	echo "</td></tr>\n";
	echo "</table>\n";

	echo '<br>';

	// �ܥ���ɽ��
	if ($ocertify->npermission == 15) {			// �����ԤΤȤ�
		echo tep_draw_input_field("execute_new", BUTTON_INSERT_USER, '', FALSE, "submit", FALSE);	// �桼�����ɲ�
		echo tep_draw_input_field("execute_user", BUTTON_INFO_USER, '', FALSE, "submit", FALSE);	// �桼������
		echo tep_draw_input_field("execute_password", BUTTON_CHANGE_PASSWORD, '', FALSE, "submit", FALSE);	// �ѥ�����ѹ�
		echo tep_draw_input_field("execute_permission", BUTTON_PERMISSION, '', FALSE, "submit", FALSE);	// �����Ը���
		echo "\n";
	} else {
		echo tep_draw_input_field("execute_user", BUTTON_INFO_USER, '', FALSE, "submit", FALSE);	// �桼������
		echo tep_draw_input_field("execute_password", BUTTON_CHANGE_PASSWORD, '', FALSE, "submit", FALSE);	// �ѥ�����ѹ�
	}

	echo "</form>\n";						// �ե�����Υեå���

	if ($oresult) @tep_db_free_result($oresult);					// ��̥��֥������Ȥ�������

	return TRUE;
}

/*--------------------------------------
	��  ǽ : �桼�����ɲá�ɽ���ᥤ���
	��  �� : �ʤ�
	����� : �ʤ�
 --------------------------------------*/
function UserInsert_preview() {

	PageBody('t', BUTTON_INSERT_USER);		// �桼���������̤Υ����ȥ���ɽ���ʥ桼�����ɲá�

	echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));							// <form>�����ν���

	// �ơ��֥륿���γ���
	echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor']. '>' . "\n";
	echo "<tr>\n";
	// ���ܥ����ȥ��1����ˤν���
	echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_COLUMN . '</td>' . "\n";	// �����
	echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_DATA . '</td>' . "\n";	// �ǡ���
	echo "</tr>\n";

	echo "<tr>\n";
	echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_USER_ID . '</td>';		// �桼��ID
	// ���Ϲ��ܽ���
	echo '<td>';
	echo tep_draw_input_field("aval[userid]", '', 'size="18" maxlength="16"', TRUE, 'text', FALSE);
	echo '</td>';
	echo "</tr>\n";

	echo "<tr>\n";
	echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_PASSWORD . '</td>';		// �ѥ����
	// ���Ϲ��ܽ���
	echo '<td>';
	echo tep_draw_password_field("aval[password]", '', TRUE);
	echo '</td>';
	echo "</tr>\n";

	echo "<tr>\n";
	echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_NAME . '</td>';		// ��̾
	// ���Ϲ��ܽ���
	echo '<td>';
	echo tep_draw_input_field("aval[name]", '', 'size="32" maxlength="64"', TRUE, 'text', FALSE);
	echo '</td>';
	echo "</tr>\n";

	echo "<tr>\n";
	echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_EMAIL . '</td>';		// E-Mail
	// ���Ϲ��ܽ���
	echo '<td>';
	echo tep_draw_input_field("aval[email]", '', 'size="32" maxlength="96"', FALSE, 'text', FALSE);
	echo '</td>';
	echo "</tr>\n";

	echo "</table>\n";

	echo '<br>';

	echo tep_draw_hidden_field("execute_new");				// �����⡼�ɤ򱣤����ܤ˥��åȤ���

	// �ܥ���ɽ��
	echo tep_draw_input_field("execute_insert", BUTTON_INSERT, '', FALSE, "submit", FALSE);		// �ɲ�
	echo tep_draw_input_field("clear", BUTTON_CLEAR, '', FALSE, "reset", FALSE);				// ���ꥢ

	echo "</form>\n";						// �ե�����Υեå���

	// �桼��������˥塼�����
	echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) . '">&laquo;&nbsp;' . BUTTON_BACK_MENU . '</a>';	// �桼��������˥塼�����

	return TRUE;

}

/*--------------------------------------
	��  ǽ : �桼�������ݼ��ɽ���ᥤ���
	��  �� : �ʤ�
	����� : �ʤ�

2000.04.20 �оݥ桼����¸�ߤ��ʤ��Ȥ�����å�����ɽ������褦���ѹ����롣

 --------------------------------------*/
function UserInfo_preview() {

	global $ocertify;						// �桼��ǧ�ڥ��֥�������

	PageBody('t', BUTTON_INFO_USER);		// �桼���������̤Υ����ȥ���ɽ���ʥ桼�������

	echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));				// <form>�����ν���

	$ssql = makeSelectUserInfo($GLOBALS['userslist']);			// �桼���������
	@$oresult = tep_db_query($ssql);
	if (!$oresult) {											// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_NO_USERINFO;						// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));						// <form>�����ν���
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";										// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;
	}

	$nrow = tep_db_num_rows($oresult);							// �쥳���ɷ���μ���
	if ($nrow != 1) {											// ���������쥳���ɷ��1��Ǥʤ��Ȥ�
		echo TEXT_ERRINFO_DB_NO_USER;							// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));						// <form>�����ν���
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";										// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;											// ������ȴ����
	}

	$arec = tep_db_fetch_array($oresult);
	if ($oresult) @tep_db_free_result($oresult);				// ��̥��֥������Ȥ�������

	// �ơ��֥륿���γ���
	echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
	echo "<tr>\n";
	// �桼��̾�Ρʥ桼��ID��
	echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . ' colspan="2" nowrap>' . $arec['name'] . "��" . $GLOBALS['userslist'] . '��</td>' . "\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_NAME . '</td>';		// ��̾
	echo '<td>';
	echo tep_draw_input_field("aval[name]", $arec['name'], 'size="32" maxlength="64"', TRUE, 'text', FALSE);
	echo '</td>';
	echo "</tr>\n";

	echo "<tr>\n";
	echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_EMAIL . '</td>';		// E-Mail
	// ���Ϲ��ܽ���
	echo '<td>';
	echo tep_draw_input_field("aval[email]", $arec['email'], 'size="32" maxlength="96"', FALSE, 'text', FALSE);
	echo '</td>';
	echo "</tr>\n";

	echo "</table>\n";

	echo tep_draw_hidden_field("execute_user");						// �����⡼�ɤ򱣤����ܤ˥��åȤ���
	echo tep_draw_hidden_field("userid", $GLOBALS['userslist']);		// �桼���ɣĤ򱣤����ܤ˥��åȤ���

	echo '<br>';

	// �ܥ���ɽ��
	echo tep_draw_input_field("execute_update", BUTTON_UPDATE, "onClick=\"return formConfirm('update')\"", FALSE, "submit", FALSE);	// ����

	// �����ԤΤȤ�������ܥ����ɽ������
	if ($ocertify->npermission == 15) 
		echo tep_draw_input_field("execute_delete", BUTTON_DELETE, "onClick=\"return formConfirm('delete')\"", FALSE, "submit", FALSE);	// ���

	echo tep_draw_input_field("reset", BUTTON_RESET, '', FALSE, "reset", FALSE);	// �����ͤ��᤹
	echo "\n";

	echo "</form>\n";									// �ե�����Υեå���

	echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) . '">&laquo;&nbsp;' . BUTTON_BACK_MENU . '</a>';	// �桼��������˥塼�����

	return TRUE;
}

/*--------------------------------------
	��  ǽ : �ѥ�����ѹ���ɽ���ᥤ���
	��  �� : �ʤ�
	����� : �ʤ�
 --------------------------------------*/
function UserPassword_preview() {

	PageBody('t', PAGE_TITLE_PASSWORD);		// �桼���������̤Υ����ȥ���ɽ���ʥѥ�����ѹ���

	echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));							// <form>�����ν���

	$ssql = makeSelectUserInfo($GLOBALS['userslist']);			// �桼���������
	@$oresult = tep_db_query($ssql);
	if (!$oresult) {											// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_NO_USERINFO;						// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));						// <form>�����ν���
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";										// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;
	}

	$nrow = tep_db_num_rows($oresult);							// �쥳���ɷ���μ���
	if ($nrow != 1) {											// ���������쥳���ɷ��1��Ǥʤ��Ȥ�
		echo TEXT_ERRINFO_DB_NO_USER;							// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));						// <form>�����ν���
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";										// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;											// ������ȴ����
	}

	$arec = tep_db_fetch_array($oresult);
	if ($oresult) @tep_db_free_result($oresult);		// ��̥��֥������Ȥ�������

	// �ơ��֥륿���γ���
	echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
	echo "<tr>\n";
	// �桼��̾�Ρʥ桼��ID��
	echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . ' colspan="2" nowrap>' . $arec['name'] . "��" . $GLOBALS['userslist'] . '��</td>' . "\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_NEW_PASSWORD . '</td>';		// �������ѥ����
	// ���Ϲ��ܽ���
	echo '<td>';
	echo tep_draw_password_field("aval[password]", '', TRUE);
	echo '</td>';
	echo "</tr>\n";

	echo "<tr>\n";
	echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_CONFIRM_PASSWORD . '</td>';	// ��ǧ�Τ��������
	// ���Ϲ��ܽ���
	echo '<td>';
	echo tep_draw_password_field("aval[chk_password]", '', TRUE);
	echo '</td>';
	echo "</tr>\n";

	echo "</table>\n";

	echo '<br>';

	echo tep_draw_hidden_field("execute_password");					// �����⡼�ɤ򱣤����ܤ˥��åȤ���
	echo tep_draw_hidden_field("userid", $GLOBALS['userslist']);		// �桼���ɣĤ򱣤����ܤ˥��åȤ���

	// �ܥ���ɽ��
	echo tep_draw_input_field("execute_update", BUTTON_CHANGE, "onClick=\"return formConfirm('password')\"", FALSE, "submit", FALSE);	// �ѹ�
	echo tep_draw_input_field("clear", BUTTON_CLEAR, '', FALSE, "reset", FALSE);	// ���ꥢ
	echo "\n";

	echo "</form>\n";									// �ե�����Υեå���

	echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) . '">&laquo;&nbsp;' . BUTTON_BACK_MENU . '</a>';	// �桼��������˥塼�����

	return TRUE;
}

/*--------------------------------------
	��  ǽ : �����Ը��¡�ɽ���ᥤ���
	��  �� : �ʤ�
	����� : �ʤ�
 --------------------------------------*/
function UserPermission_preview() {

	PageBody('t', PAGE_TITLE_PERMISSION);		// �桼���������̤Υ����ȥ���ɽ���ʴ����Ը��¡�

	echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));	// <form>�����ν���

	// ���̥桼���������
	$ssql = makeSelectUserParmission();							// ���̥桼���Υǡ������������ sql ʸ��������

	@$oresult = tep_db_query($ssql);
	if (!$oresult) {											// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_NO_USERINFO;						// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));			// <form>�����ν���
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";										// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;
	}

	$nrow = tep_db_num_rows($oresult);							// �쥳���ɷ���μ���
	if ($nrow > 0) {
		// �ꥹ�ȥܥå�����ɽ������ǡ���������˥��åȤ���
		$i=0;
		while ($arec = tep_db_fetch_array($oresult)) {			// �쥳���ɤ����
			$ausers[$i]['id'] = $arec['userid'];
			$ausers[$i]['text'] = $arec['name'];
			$i++;
		}
	}

	if ($oresult) @tep_db_free_result($oresult);				// ��̥��֥������Ȥ�������

	// �����Ը��¤���ĥ桼���������
	$ssql = makeSelectUserParmission(1);						// �����Ը��¤���ĥǡ������������ sql ʸ��������

	@$oresult = tep_db_query($ssql);
	if (!$oresult) {											// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_NO_USERINFO;						// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));			// <form>�����ν���
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";										// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;
	}

	$nrow = tep_db_num_rows($oresult);							// �쥳���ɷ���μ���
	if ($nrow > 0) {
		// �ꥹ�ȥܥå�����ɽ������ǡ���������˥��åȤ���
		$i=0;
		while ($arec = tep_db_fetch_array($oresult)) {		// �쥳���ɤ����
			$ausers_admin[$i]['id'] = $arec['userid'];
			$ausers_admin[$i]['text'] = $arec['name'];
			$i++;
		}
	}

	if ($oresult) @tep_db_free_result($oresult);					// ��̥��֥������Ȥ�������

	// �ơ��֥륿���γ���
	echo '<table border="0" gbcolor="#FFFFFF" cellpadding="5" cellspacing="0">' . "\n";
	echo "<tr>\n";
	echo "<td>\n";									// �ǡ�������

		// �ơ��֥륿���γ��ϡʰ��̥桼���Υꥹ�ȥܥå�����
		echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
		echo "<tr>\n";
		echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_USER . '</td>' . "\n";	// ���̥桼��
		echo "</tr>\n";

		echo "<td>\n";									// �ǡ�������
		echo tep_draw_pull_down_menu("no_permission_list", $ausers, '', 'size="5"');	// �ꥹ�ȥܥå�����ɽ��
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo '<td align="center" valign="middle">' . "\n";									// �ǡ�������

		echo '<br>';
		echo tep_draw_input_field("execute_grant", BUTTON_GRANT, "onClick=\"return formConfirm('grant')\"", FALSE, "submit", FALSE);	// ���¤�Ϳ���� >>
		echo '<br>';
		echo tep_draw_input_field("execute_revoke", BUTTON_REVOKE, "onClick=\"return formConfirm('revoket')\"", FALSE, "submit", FALSE);	// << ���¤��ä�

	echo "</td>\n";
	echo "<td>\n";									// �ǡ�������

		// �ơ��֥륿���γ��ϡʴ������¤���äƤ���桼���Υꥹ�ȥܥå�����
		echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
		echo "<tr>\n";
		echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_ADMIN . '</td>' . "\n";		// �����ȴ�����
		echo "</tr>\n";

		echo "<td>\n";									// �ǡ�������
		echo tep_draw_pull_down_menu("permission_list", $ausers_admin, '', 'size="5"');	// �ꥹ�ȥܥå�����ɽ��
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo tep_draw_hidden_field("execute_permission");				// �����⡼�ɤ򱣤����ܤ˥��åȤ���

	echo "</form>\n";						// �ե�����Υեå���

	echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) . '">&laquo;&nbsp;' . BUTTON_BACK_MENU . '</a>';	// �桼��������˥塼�����

	return TRUE;
}

/* ==============================================
	�����¹Դؿ�
 ============================================= */
/*--------------------------------------
	��  ǽ : �桼�����ɲý����¹�
	��  �� : �ʤ�
	����� : true/false
	��  ­ : [:print:] ������ǽ�ʥ���饯��(=����ʸ���ʳ��Υ���饯��) 
 --------------------------------------*/
function UserInsert_execute() {

	PageBody('t', PAGE_TITLE_INSERT_USER);		// �桼���������̤Υ����ȥ���ɽ���ʥ桼�����ɲá�

	// �桼��ID �����ϥ����å�
	$aerror = "";
	$ret_err = checkLength_ge($GLOBALS['aval']['userid'], 2);
	if ($ret_err == "") $ret_err = checkNotnull($GLOBALS['aval']['userid']);
	if ($ret_err == "") $ret_err = checkStringEreg($GLOBALS['aval']['userid'], "[[:print:]]");
	if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_USER_ID . '</b>:' . $ret_err);	// �桼��ID

	// �ѥ���ɤ����ϥ����å�
	$ret_err = checkLength_ge($GLOBALS['aval']['password'], 2);
	if ($ret_err == "") $ret_err = checkNotnull($GLOBALS['aval']['password']);
	if ($ret_err == "") $ret_err = checkStringEreg($GLOBALS['aval']['password'], "[[:print:]]");
	if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_PASSWORD . '</b>:' . $ret_err);	// �ѥ����

	// ��̾ �����ϥ����å�
	$ret_err = checkNotnull($GLOBALS['aval']['name']);
	if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NAME . '</b>:' . $ret_err);		// ��̾

	echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));			// <form>�����ν���

	if (is_array($aerror)) {			// ���ϥ��顼�ΤȤ�
		print_err_message($aerror);		// ���顼��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";				// �ե�����Υեå���
		return FALSE;
	}

	// �ɲä���ǡ�������Ͽ����Ƥ��ʤ��������å�����
	$ssql = makeSelectUserInfo($GLOBALS['aval']['userid']);		// �桼���������
	@$oresult = tep_db_query($ssql);
	if (!$oresult) {											// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_USERCHACK;							// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";										// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;
	}

	$nrow = tep_db_num_rows($oresult);							// �쥳���ɷ���μ���
	if ($nrow >= 1) {											// ���������쥳���ɷ��0��Ǥʤ��Ȥ�
		echo TEXT_ERRINFO_DB_EXISTING_USER;						// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";										// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;											// ������ȴ����
	}

	$ssql = makeInsertUser($GLOBALS['aval']);					// �桼�������ơ��֥���ɲ�sqlʸ������������
	@$oresult = tep_db_query($ssql);
	if (!$oresult) {											// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_INSERT_USER;						// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";										// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;
	}

	$ssql = makeInsertUser($GLOBALS['aval'], 1);				// �桼�����¥ơ��֥���ɲ�sqlʸ������������
	@$oresult = tep_db_query($ssql);
	if (!$oresult) {											// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_INSERT_PERMISSION;					// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";										// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;
	}

	echo "<br>\n";
	echo TEXT_SUCCESSINFO_INSERT_USER;		// ��λ��å�����
	echo '<br><br>';
	echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
	echo "</form>\n";						// �ե�����Υեå���

	if ($oresult) @tep_db_free_result($oresult);		// ��̥��֥������Ȥ�������

	return TRUE;
}
/*--------------------------------------
	��  ǽ : �桼������ι��������¹�
	��  �� : �ʤ�
	����� : true/false
	��  ­ : [:print:] ������ǽ�ʥ���饯��(=����ʸ���ʳ��Υ���饯��) 
 --------------------------------------*/
function UserInfor_execute() {

	PageBody('t', PAGE_TITLE_USERINFO);		// �桼���������̤Υ����ȥ���ɽ���ʥ桼�������

	// ��̾ �����ϥ����å�
	$ret_err = checkNotnull($GLOBALS['aval']['name']);
	if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NAME . '</b>:' . $ret_err);		// ��̾

	echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));			// <form>�����ν���

	if (is_array($aerror)) {			// ���ϥ��顼�ΤȤ�
		print_err_message($aerror);		// ���顼��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);						// �桼���ɣĤ򱣤����ܤ˥��åȤ���
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";				// �ե�����Υեå���
		return FALSE;
	}

	$ssql = makeUpdateUser($GLOBALS['aval']);					// �桼�������ơ��֥�λ�̾��E-Mai�򹹿����� sqlʸ������������
	@$oresult = tep_db_query($ssql);
	if (!$oresult) {											// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_UPDATE_USER;						// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";										// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);			// ��̥��֥������Ȥ�������
		return FALSE;
	}

	echo "<br>\n";
	echo TEXT_SUCCESSINFO_UPDATE_USER;		// ��λ��å�����
	echo "<br><br>\n";
	echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
	echo "</form>\n";						// �ե�����Υեå���

	if ($oresult) @tep_db_free_result($oresult);		// ��̥��֥������Ȥ�������

	return TRUE;
}

/*--------------------------------------
	��  ǽ : �桼����������å�
	��  �� : �ʤ�
	����� : true/false
 --------------------------------------*/
function UserDelete_execute() {

	global $ocertify;						// �桼��ǧ�ڥ��֥�������

	PageBody('t', PAGE_TITLE_USERINFO);		// �桼���������̤Υ����ȥ���ɽ���ʥ桼�������

	if (strcmp($GLOBALS['userid'],$ocertify->auth_user) == 0)
		set_errmsg_array($aerror, TEXT_ERRINFO_USER_DELETE);			// �ܿͤξ�������ϥ��顼

	echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));		// <form>�����ν���

	if (is_array($aerror)) {			// ���ϥ��顼�ΤȤ�
		print_err_message($aerror);		// ���顼��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);	// �桼���ɣĤ򱣤����ܤ˥��åȤ���
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";				// �ե�����Υեå���
		return FALSE;
	}

	$ssql = makeDeleteUser(1);							// �桼�����¥ơ��֥뤫���оݥ桼���������� sqlʸ������������
	@$oresult = tep_db_query($ssql);
	if (!$oresult) {									// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_DELETE_USER;				// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";								// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);	// ��̥��֥������Ȥ�������
		return FALSE;
	}

	$ssql = makeDeleteUser();							// �桼�������ơ��֥뤫���оݥ桼���������� sqlʸ������������
	@$oresult = tep_db_query($ssql);
	if (!$oresult) {									// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_DELETE_USER;				// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";								// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);	// ��̥��֥������Ȥ�������
		return FALSE;
	}

	echo "<br>\n";
	echo TEXT_SUCCESSINFO_DELETE_USER;					// ��λ��å�����
	echo "<br><br>\n";
	echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);		// �桼��������˥塼�����
	echo "</form>\n";									// �ե�����Υեå���

	if ($oresult) @tep_db_free_result($oresult);		// ��̥��֥������Ȥ�������

	return TRUE;

}

/*--------------------------------------
	��  ǽ : �ѥ�����ѹ������¹�
	��  �� : �ʤ�
	����� : true/false
	��  ­ : [:print:] ������ǽ�ʥ���饯��(=����ʸ���ʳ��Υ���饯��) 
 --------------------------------------*/
function UserPassword_execute() {

	PageBody('t', PAGE_TITLE_PASSWORD);		// �桼���������̤Υ����ȥ���ɽ���ʥѥ�����ѹ���

	// �������ѥ���ɤ����ϥ����å�
	$ret_err = checkNotnull($GLOBALS['aval']['password']);
	if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NEW_PASSWORD . '</b>:' . $ret_err);
	$ret_err = checkLength_ge($GLOBALS['aval']['password'], 2);
	if ($ret_err == "") $ret_err = checkStringEreg($GLOBALS['aval']['password'], "[[:print:]]");
	if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NEW_PASSWORD . '</b>:' . $ret_err);
	// ��ǧ�Τ�������Ϥ����ϥ����å�
	if (strcmp($GLOBALS['aval']['password'],$GLOBALS['aval']['chk_password']) != 0)
		set_errmsg_array($aerror, TEXT_ERRINFO_CONFIRM_PASSWORD);

	echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));			// <form>�����ν���

	if (is_array($aerror)) {			// ���ϥ��顼�ΤȤ�
		print_err_message($aerror);		// ���顼��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);		// �桼���ɣĤ򱣤����ܤ˥��åȤ���
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";				// �ե�����Υեå���
		return FALSE;
	}

	$ssql = makeUpdateUser($GLOBALS['aval'], 1);		// �桼�������ơ��֥�Υѥ���ɤ򹹿����� sqlʸ������������
	@$oresult = tep_db_query($ssql);
	if (!$oresult) {									// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_CHANGE_PASSWORD;			// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";								// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);	// ��̥��֥������Ȥ�������
		return FALSE;
	}

	echo "<br>\n";
	echo TEXT_SUCCESSINFO_CHANGE_PASSWORD;		// ��λ��å�����
	echo "<br><br>\n";
	echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
	echo "</form>\n";						// �ե�����Υեå���

	if ($oresult) @tep_db_free_result($oresult);		// ��̥��֥������Ȥ�������

	return TRUE;

}

/*--------------------------------------
	��  ǽ : �桼��������������å�
	��  �� : $nmode - (i) �����⡼�ɡ�0:grant��1:revoke��
	����� : true/false
 --------------------------------------*/
function UserPermission_execute($nmode=0) {

	global $ocertify;						// �桼��ǧ�ڥ��֥�������

	PageBody('t', PAGE_TITLE_PERMISSION);		// �桼���������̤Υ����ȥ���ɽ���ʴ����Ը��¡�

	if ($nmode == 0) {		// ���¤�Ϳ����������桼�������򤵤�Ƥ��ʤ�
		$suserid = $GLOBALS['no_permission_list'];
		if ($suserid == "") set_errmsg_array($aerror, TEXT_ERRINFO_USER_GRANT);
	} else {				// ���¤��ä��������桼�������򤵤�Ƥ��ʤ�
		$suserid = $GLOBALS['permission_list'];
		if ($suserid == "") set_errmsg_array($aerror, TEXT_ERRINFO_USER_REVOKE);
	}
	// ���¤��ä��������桼���ܿͤΤȤ�
	if ($nmode == 1 && strcmp($suserid,$ocertify->auth_user) == 0) 
			set_errmsg_array($aerror, TEXT_ERRINFO_USER_REVOKE_ONESELF);

	echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));	// <form>�����ν���

	if (is_array($aerror)) {										// ���ϥ��顼�ΤȤ�
		print_err_message($aerror);									// ���顼��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);						// �桼���ɣĤ򱣤����ܤ˥��åȤ���
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";											// �ե�����Υեå���
		return FALSE;
	}

	$ssql = makeUpdatePermission($nmode, $suserid);					// �桼�����¥ơ��֥�θ��¤򹹿����� sqlʸ������������
	@$oresult = tep_db_query($ssql);
	if (!$oresult) {												// ���顼���ä��Ȥ�
		echo TEXT_ERRINFO_DB_CHANGE_USER;							// ��å�����ɽ��
		echo "<br>\n";
		echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);	// �桼��������˥塼�����
		echo "</form>\n";											// �ե�����Υեå���
		if ($oresult) @tep_db_free_result($oresult);				// ��̥��֥������Ȥ�������
		return FALSE;
	}

	printf(TEXT_SUCCESSINFO_PERMISSION, ($nmode == 0 ? 'Ϳ��' : '��ä�'));
	echo "<br><br>\n";
	echo tep_draw_input_field("execute_permission", BUTTON_BACK_PERMISSION, '', FALSE, "submit", FALSE);	// �����Ը��¤����
	echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);						// �桼��������˥塼�����
	echo "</form>\n";									// �ե�����Υեå���

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
  switch (type) {
    case "update":
      rtn = confirm("'. JAVA_SCRIPT_INFO_CHANGE . '");
      break;
    case "delete":
      rtn = confirm("'. JAVA_SCRIPT_INFO_DELETE . '");
      break;
    case "password":
      rtn = confirm("'. JAVA_SCRIPT_INFO_PASSWORD . '");
      break;
    case "grant":
      rtn = confirm("'. JAVA_SCRIPT_INFO_GRANT . '");
      break;
    case "revoket":
      rtn = confirm("'. JAVA_SCRIPT_INFO_REVOKE . '");
      break;
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

	// �桼�����󡢥ѥ�����ѹ��������Ը��¤ΤȤ���ǧ��å����� JavaScript ����
	if ($GLOBALS['execute_user'] || $GLOBALS['execute_password'] || $GLOBALS['execute_permission'] ) {
		putJavaScript_ConfirmMsg();						// ��ǧ��å�������ɽ������ JavaScript
	}

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
		echo '            <td class="pageHeading">' . HEADING_TITLE . ' (' . $stitle . ')</td>' . "\n";
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

	PageHeader();				// �ڡ������إå���ɽ��
	PageBodyTable('t');			// �ڡ����Υ쥤�����ȥơ��֥롧���ϡʥʥӥ��������ܥå�������礹��ơ��֥볫�ϡ�

	// ���ʥӥ��������ܥå�����ɽ��
	echo "<!-- left_navigation //-->\n";		// 
	include_once(DIR_WS_INCLUDES . 'column_left.php');
	echo "\n<!-- left_navigation_eof //-->\n";
	echo "    </table></td>\n";

// ����ɽ�������ϥ����å��ģ�ȿ��
	if ($ocertify->auth_user) {
		// �桼��������˥塼
		if ($execute_menu) {
			UserManu_preview();								// ���ɽ��

		// �桼�����ɲ�
		} elseif ($execute_new) {
			if ($execute_insert) UserInsert_execute();		// �桼�����ɲý����¹�
			else UserInsert_preview();						// �桼�����ɲåڡ���ɽ��

		// �桼�������ݼ�
		} elseif ($execute_user) {
			if ($execute_update) UserInfor_execute();		// �桼�����󹹿������¹�
			elseif ($execute_delete) UserDelete_execute();	// �桼�������������¹�
			else UserInfo_preview();						// �桼������ڡ���ɽ��

		// �ѥ�����ѹ�
		} elseif ($execute_password) {
			if ($execute_update) UserPassword_execute();	// �ѥ�����ѹ������¹�
			else UserPassword_preview();					// �ѥ�����ѹ��ڡ���ɽ��

		// �����Ը���
		} elseif ($execute_permission) {
			if ($execute_grant) UserPermission_execute(0);				// �����Ը��¤�Ϳ��������¹�
			elseif ($execute_revoke)  UserPermission_execute(1);		// �����Ը��¤��ä������¹�
			else UserPermission_preview();								// �����Ը��¥ڡ���ɽ��

		// �桼��������˥塼
		} else {
			UserManu_preview();								// ���ɽ��
		}
	}

	PageBody('u');				// �ڡ����ܥǥ��ν�λ
	PageBodyTable('u');			// �ڡ����Υ쥤�����ȥơ��֥롧��λ
	PageFooter();				// �ڡ����եå���ɽ��

	require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
