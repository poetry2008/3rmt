<?php
/* *********************************************************
 * ���饹̾: user_certify.php
 * ���������ȤΥ桼��ǧ�ڤ�Ԥ��������������˵�Ͽ���롣
 * Naomi Suzukawa <suzukawa@bitscope.co.jp>
 *
 * 2001/05/29 ����
 * 2002/05/10 osCommers �Ѥ��ѹ�
 * 2002/05/21 osCommers IE5.01��ư������ˤʤ뤿���������ѹ�
 *            PHP HTTP(Basic)ǧ�� �� PHP���å�������+�ѥ����ǧ�ڤ��ѹ�
 * 2004/03/19 replace get_cfg_var() with ini_get()
********************************************************* */
class user_certify {
    // �桼������
    var $apermissions = array('read'=>0, 'write'=>0, 'config'=>0, 'users'=>0);
    var $npermission = 0;
    // ��������Υǡ����ݻ����֡�����
    var $login_log_span = 14;
    // ���շ���
    var $date_format = 'Y-m-d H:i:s';

    // ��������ե饰
    var $isFirstTime = FALSE;
    // �����󥨥顼�ե饰
    var $isErr = FALSE;
    // ������Ѥߥե饰
    var $flg = FALSE;

    // �桼��ID
    var $auth_user = '';

/* -------------------------------------
    ��  ǽ : ���󥹥ȥ饯��
    ��  �� : $s_sid             - (i) ���å����ID
    ����� : TRUE/FALSE
    ��  �� : �桼����ǧ�ڤ�Ԥ�
 ------------------------------------ */
    function user_certify($s_sid) {
        $this->user_admin_entry();           // �����ԡ�admin����Ͽ

        // �����ॢ���Ȼ�������
        $actime = $this->time_out_time();
        //error_log('USER ' . date($this->date_format) . ' user_certify start. timeout='.$actime . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);// DEBUG

        // ������ַвᤷ�������������Υ��ơ�������������Ȥ˹�������
        $this->logoutCertifyLog($actime,$s_sid);

        $user = '';
        // ������ڡ����ǥ桼���ɣĤ����Ϥ���Ƥ���Ȥ�
        if ($GLOBALS['HTTP_POST_VARS']['execute_login']) {
            $user = trim($GLOBALS['HTTP_POST_VARS']['loginuid']);
        }
        // ���å����ɣĤˤ�ꡢ�桼��������������
        $oresult = tep_db_query("select * from login where sessionid='" . $s_sid . "'");
        if (!$oresult) {                     // DB���顼���ä��Ȥ�
            $this->putCertifyLog($s_sid,'e',$user);
            $this->isErr = TRUE;
            die('<br>'.TEXT_ERRINFO_DBERROR);
        }

        $nrow = tep_db_num_rows($oresult);   // �쥳���ɷ���μ���
        if ($nrow == 1) {                    // �����������Ͽ����Ƥ���Ȥ�
            $this->flg = TRUE;
            $arec = tep_db_fetch_array($oresult);  // �쥳���ɤ����
			// UID��������ڡ��������Ϥ���Ƥ���Ȥ��ơ��֥���ͤ��������������å�
            if ($user && $user != $arec['account']) {
                $this->putCertifyLog($s_sid,'n',$user);
                $this->isErr = TRUE;
                return;
            } elseif ($arec['loginstatus'] != 'a') { // ���Υ����󤬥��顼�Ǥʤ���?
                $this->isFirstTime = TRUE;
                return;
            } elseif ($arec['logoutstatus'] != 'i') {// ���顼,��������,�����ॢ����?
                $this->isFirstTime = TRUE;
                return;
            } elseif (strcmp($arec['lastaccesstime'], $actime) < 0) {// �����ॢ����?
                //error_log('USER ' . date($this->date_format) . ' timeout lastaccesstime[' . $arec['lastaccesstime'] . '] limit=[' . $actime . "]\n", 3, STORE_PAGE_PARSE_TIME_LOG);// DEBUG
                $this->putTimeOut($s_sid);
                $this->isFirstTime = TRUE;
                return;
            } else {
                $user = $arec['account'];
            }
		}

        if (!$user) {       // ��������ΤȤ�������ȴ����
            $this->isFirstTime = TRUE;
        } else {
            // �桼���ɣĥ����å�
            $oresult = tep_db_query("select * from users where userid = '" . $user . "'");
            if (!$oresult) {                 // DB���顼���ä��Ȥ�
                $this->putCertifyLog($s_sid,'e',$user);
                $this->isErr = TRUE;
                die('<br>'.TEXT_ERRINFO_DBERROR);
            }

            $nrow = tep_db_num_rows($oresult); // �쥳���ɷ���μ���
            if ($nrow == 1) {  // ���Ϥ��줿 UID �Υ桼������Ͽ����Ƥ���Ȥ�
                $arec = tep_db_fetch_array($oresult); // �쥳���ɤ����
                $pret = $this->password_check($s_sid,$arec['password'],$user); // �ѥ���ɸ���
                $aret = $this->user_parmission($s_sid,$user); // �桼�����¤����
                if ($pret && $aret) {
                    $this->putCertifyLog($s_sid,'a',$user);
                    $this->auth_user = $user;
                } else {
                    $this->isErr = TRUE;
                }
            } else {  // ��Ͽ����Ƥ��ʤ��桼��
                $this->putCertifyLog($s_sid,'n',$user);
                $this->isErr = TRUE;
            }
        }
    }

/* -------------------------------------
    ��  ǽ : �ѥ���ɥ����å�
    ��  �� : $s_sid             - (i) ���å����ID
             $pwd               - (i) �ѥ����
             $auth_user         - (i) �桼��ID
    ����� : TRUE/FALSE
 ------------------------------------ */
    function password_check($s_sid,$pwd,$auth_user) {
        if ($GLOBALS['HTTP_POST_VARS']['execute_login']) {
            //error_log('USER ' . date($this->date_format) . ' password_check user='. $auth_user . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);// DEBUG
            if ($GLOBALS['HTTP_POST_VARS']['loginpwd']) {
                // ���Ϥ��줿�ѥ���ɤ� DES �Ź沽ˡ�ˤ��Ź沽����
                //�ʥơ��֥����Ͽ����Ƥ���ѥ���ɤ�Ʊ�����֤��Ѵ���
                $sLogin_pwd = crypt($GLOBALS['HTTP_POST_VARS']['loginpwd'], $pwd);
                $n_max = 64;                        // �ե������Ĺ������
                if (substr($pwd,0,$n_max) != substr($sLogin_pwd,0,$n_max)) {
                    $this->putCertifyLog($s_sid,'p',$auth_user);
                    return FALSE;
                }
            } else {
                $this->putCertifyLog($s_sid,'p',$auth_user);
                return FALSE;
            }
        }
        return TRUE;
    }

/* -------------------------------------
    ��  ǽ : �����ॢ���Ȼ������
    ��  �� : �ʤ�
    ����� : �����ॢ���Ȼ���
 ------------------------------------ */
    function time_out_time() {
        if ($GLOBALS['SESS_LIFE']) {
            $life_time = $GLOBALS['SESS_LIFE'];
        } else {
            $life_time = ini_get('session.gc_maxlifetime'); // replace get_cfg_var() with ini_get()
        }
        $life_time = max($life_time,600);
        return date($this->date_format, mktime() - $life_time);
    }

/* -------------------------------------
    ��  ǽ : �桼�����¼���
    ��  �� : $s_sid             - (i) ���å����ID
             $auth_user         - (i) �桼��ID
    ����� : ǧ�ڴ�λ������ʸ���󡢰۾ｪλ�����顼��å�����
    ��  �� : ���������桼�����¤򥯥饹�ѿ��˥��åȤ���
 ------------------------------------ */
    function user_parmission($s_sid,$auth_user) {
        // �桼�����¼���
        $oresult = tep_db_query("select permission from permissions where userid = '" . $auth_user . "'");
        if (!$oresult) {                                        // ���顼���ä��Ȥ�
            $this->putCertifyLog($s_sid,'n',$auth_user);
            die('<br>'.TEXT_ERRINFO_DBERROR);
        }

        $nrow = tep_db_num_rows($oresult);      // �쥳���ɷ���μ���
        if ($nrow == 1) {                       // ���Ϥ��줿 UID �Υ桼������Ͽ����Ƥ���Ȥ�
            $arec = tep_db_fetch_array($oresult, DB_FETCHMODE_ASSOC); // �쥳���ɤ����
            $this->npermission = $arec['permission'];
            $this->apermissions['read'] = ($this->npermission & 1);
            $this->apermissions['write'] = ($this->npermission & 2);
            $this->apermissions['config'] = ($this->npermission & 4);
            $this->apermissions['users'] = ($this->npermission & 8);
            return TRUE;
        }
        else {
            $this->putCertifyLog($s_sid,'n',$auth_user);
            return FALSE;
        }
        return FALSE;
    }
/* -------------------------------------
    ��  ǽ : �����ԡ�admin����Ͽ
    ��  �� : �ʤ�
    ����� : �ʤ�
    ��  �� : �桼������ͤ���Ͽ����Ƥ��ʤ��Ȥ��������ԡ�admin�ˤ���Ͽ����
 ------------------------------------ */
    function user_admin_entry() {
        // �����ԡ�admin����Ͽ
        $oresult = tep_db_query("select * from users");
        $nrow = tep_db_num_rows($oresult); // �쥳���ɷ���μ���
        if ($nrow == 0) {      // �桼������ͤ���Ͽ����Ƥ��ʤ��Ȥ�����������Ͽ
            $s_pwd = crypt('admin');
            $result = tep_db_query("insert into users values ('admin','$s_pwd','�����ƥ������','')");
            $result = tep_db_query("insert into permissions values ('admin',15)");
        }
    }

/* -------------------------------------
    ��  ǽ : ǧ�ڥ���Ͽ����
    ��  �� : $s_sid             - (i) ���å����ID
             $s_status          - (i) ���ơ�����
             $auth_user         - (i) �桼��ID
    ����� : �ʤ�
 ------------------------------------ */
    function putCertifyLog($s_sid,$s_status,$auth_user) {
        $this->deleteCertifyLog();  // ������֤���Ť�ǧ�ڥ���������
        $time_ = date($this->date_format);

        if ($this->flg) {
            $result = tep_db_query("update login set lastaccesstime='$time_' where sessionid='$s_sid'");
            if (!$result) {
                $this->isErr = TRUE;
                die('<br>'.TEXT_ERRINFO_DBERROR);
            }
        }
        else {
            // The IP address of the remote host making the request.
            $as_ip = explode('.', getenv('REMOTE_ADDR'));
            // IP���ɥ쥹��4�Х��������˥ѥå�����
            $n_ip4 = 0;
            while (list($n_key, $s_byte) = each($as_ip)) {
                $n_ip4 = ($n_ip4 << 8) | (int)$s_byte;
            }

            $status_out_c = ''; $status_out = '';
            if ($s_status == 'a') {
                $status_out_c = ',logoutstatus';
                $status_out = ",'i'";
            }

            // ��Ͽ
            $result = tep_db_query("insert into login(sessionid,logintime,lastaccesstime,account,loginstatus,address$status_out_c) values('$s_sid','" . $time_ . "','" . $time_ . "','" . $auth_user . "','$s_status',$n_ip4$status_out)");
            if (!$result) {
                $this->isErr = TRUE;
                die('<br>'.TEXT_ERRINFO_DBERROR);
            }
        }
        return TRUE;
    }

/* -------------------------------------
    ��  ǽ : �����ॢ���Ȥ�Ͽ����
    ��  �� : $s_sid             - (i) ���å����ID
             $auth_user         - (i) �桼��ID
    ����� : �ʤ�
 ------------------------------------ */
    function putTimeOut($s_sid) {
        if ($this->flg) {
            $time_ = date($this->date_format);
            $result = tep_db_query("update login set logoutstatus='t', lastaccesstime='$time_' where sessionid='$s_sid'");
            if (!$result) {
                $this->isErr = TRUE;
                die('<br>'.TEXT_ERRINFO_DBERROR);
            }
        }
        return TRUE;
    }

/* -------------------------------------
    ��  ǽ : ������ַвᤷ�������������Υ��ơ�������������Ȥ˹�������
    ��  �� : $actime            - (i) �����ॢ���Ȼ���
             $s_sid             - (i) ���ơ�����
    ����� : �ʤ�
 ------------------------------------ */
    function logoutCertifyLog($actime,$s_sid) {
        // ���ߤΥ��å����ID�ǤϤʤ����ǽ������������郎�����ॢ���Ȼ���������ǡ���������󤷤Ƥ���쥳���ɤ�������Ȥ�����
        $result = tep_db_query("update login set logoutstatus='o' where sessionid!='$s_sid' and lastaccesstime<'$actime' and logoutstatus='i'");
    }

/* -------------------------------------
    ��  ǽ : ������֤���Ť�������������������
    ��  �� : �ʤ�
    ����� : �ʤ�
 ------------------------------------ */
    function deleteCertifyLog() {
        if ( 0 < $this->login_log_span) {
            $sspan_date = date($this->date_format, mktime() - $this->login_log_span * 3600 * 24);
            $result = tep_db_query("delete from login where lastaccesstime < '$sspan_date'");
        }
    }
}

/* -------------------------------------
    ��  ǽ : ��������
    ��  �� : $erf               - (i) ���顼�֥饰
             $s_status          - (i) ���ơ�����
    ����� : �ʤ�
 ------------------------------------ */
function logout_user($erf='',$s_status='') {
    if ($s_status) {    // �������Ȥ�Ͽ����
        $s_sid = session_id();
        $result = tep_db_query("update login set logoutstatus='$s_status' where sessionid='$s_sid'");
    }
    //for redirect to admin.php after login
    tep_redirect('admin_login.php' . ($erf ? ('?erf='.$erf) : ''));
}

/* =====================================
    �ᥤ��
 ===================================== */
if ($GLOBALS['HTTP_GET_VARS']['execute_logout_user']) { logout_user(FALSE,'o'); } //2003-07-16 hiroshi_sato

if (file_exists(DIR_WS_LANGUAGES . $language . '/user_certify.php')) {
    include(DIR_WS_LANGUAGES . $language . '/user_certify.php');
}
$ocertify = new user_certify(session_id());     // ǧ��
if ($ocertify->isErr) { logout_user(1); }
elseif ($ocertify->isFirstTime) { logout_user(); }

?>
