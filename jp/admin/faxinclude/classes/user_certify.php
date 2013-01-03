<?php
class user_certify {
    // �û�Ȩ��
    var $apermissions = array('read'=>0, 'write'=>0, 'config'=>0, 'users'=>0);
    var $npermission = 0;
    // ��½��־�����ݱ����ڼ�(��)
    var $login_log_span = 14;
    // ���ڸ�ʽ
    var $date_format = 'Y-m-d H:i:s';

    // ���ε�½��ǩ
    var $isFirstTime = FALSE;
    // ��½�û���ǩ
    var $isErr = FALSE;
    // ��½���˱�ǩ
    var $flg = FALSE;

    // �û�ID
    var $auth_user = '';

/* -------------------------------------
    ��  �� : ���캯��
    ��  �� : $s_sid             - (i) sessionID
    ����ֵ : TRUE/FALSE
    ˵  �� : �����û�����֤
 ------------------------------------ */
    function user_certify($s_sid) {
        $this->user_admin_entry();           // ����Ա(admin)ע��

        // ��ȡ��ʱʱ��
        $actime = $this->time_out_time();
        //error_log('USER ' . date($this->date_format) . ' user_certify start. timeout='.$actime . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);// DEBUG

        // ����һ��ʱ�佫������־��״̬����
        $this->logoutCertifyLog($actime,$s_sid);

        $user = '';
        // ��½ҳ���û�ID�������ʱ��
        if ($GLOBALS['_POST']['execute_login']) {
            $user = trim($GLOBALS['_POST']['loginuid']);
        }
        // ͨ��sessionID��ȡ�û���½��Ϣ
        $oresult = tep_db_query("select * from login where sessionid='" . $s_sid . "'");
        if (!$oresult) {                     // DB�����ʱ��
            $this->putCertifyLog($s_sid,'e',$user);
            $this->isErr = TRUE;
            die('<br>'.TEXT_ERRINFO_DBERROR);
        }

        $nrow = tep_db_num_rows($oresult);   // ��ȡ��¼����
        if ($nrow == 1) {                    // ��½��־��ע���ʱ��
            $this->flg = TRUE;
            $arec = tep_db_fetch_array($oresult);  // ��ȡ��¼
			// UID�ڵ�½ҳ�汻�����ʱ�����Ƿ�ͱ����ֵ��ͬ
            if ($user && $user != $arec['account']) {
                $this->putCertifyLog($s_sid,'n',$user);
                $this->isErr = TRUE;
                return;
            } elseif ($arec['loginstatus'] != 'a') { // ֮ǰ�ĵ�½������?
                $this->isFirstTime = TRUE;
                return;
            } elseif ($arec['logoutstatus'] != 'i') {// ����,�˳���¼,��ʱ
                $this->isFirstTime = TRUE;
                return;
            } elseif (strcmp($arec['lastaccesstime'], $actime) < 0) {// ��ʱ
                //error_log('USER ' . date($this->date_format) . ' timeout lastaccesstime[' . $arec['lastaccesstime'] . '] limit=[' . $actime . "]\n", 3, STORE_PAGE_PARSE_TIME_LOG);// DEBUG
                $this->putTimeOut($s_sid);
                $this->isFirstTime = TRUE;
                return;
            } else {
                $user = $arec['account'];
            }
		}

        if (!$user) {       // �˳����ε�½�Ĵ���
            $this->isFirstTime = TRUE;
        } else {
            // �û�ID���
            $oresult = tep_db_query("select * from users where userid = '" . $user . "'");
            if (!$oresult) {                 // DB�����ʱ��
                $this->putCertifyLog($s_sid,'e',$user);
                $this->isErr = TRUE;
                die('<br>'.TEXT_ERRINFO_DBERROR);
            }

            $nrow = tep_db_num_rows($oresult); // ��ȡ��¼����
            if ($nrow == 1) {  // �����UID���û���ע���ʱ��
                $arec = tep_db_fetch_array($oresult); // ��ȡ��¼
                $pret = $this->password_check($s_sid,$arec['password'],$user); // �������
                $aret = $this->user_parmission($s_sid,$user); // ��ȡ�û�Ȩ��
                if ($pret && $aret) {
                    $this->putCertifyLog($s_sid,'a',$user);
                    $this->auth_user = $user;
                } else {
                    $this->isErr = TRUE;
                }
            } else {  // û��ע����û�
                $this->putCertifyLog($s_sid,'n',$user);
                $this->isErr = TRUE;
            }
        }
    }

/* -------------------------------------
    ��  �� : �������
    ��  �� : $s_sid             - (i) sessionID
             $pwd               - (i) ����
             $auth_user         - (i) �û�ID
    ����ֵ : TRUE/FALSE
 ------------------------------------ */
    function password_check($s_sid,$pwd,$auth_user) {
        if ($GLOBALS['_POST']['execute_login']) {
            //error_log('USER ' . date($this->date_format) . ' password_check user='. $auth_user . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);// DEBUG
            if ($GLOBALS['_POST']['loginpwd']) {
                // �����������DES���м���
                //(ת���ɺͱ���ע���������ͬ��״̬)
                $sLogin_pwd = crypt($GLOBALS['_POST']['loginpwd'], $pwd);
                $n_max = 64;                        // �����ֶγ���
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
    ��  �� : ��ȡ��ʱʱ��
    ��  �� : û��
    ����ֵ : ��ʱʱ��
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
    ��  �� : ��ȡ�û�Ȩ��
    ��  �� : $s_sid             - (i) sessionID
             $auth_user         - (i) �û�ID
    ����ֵ : ��֤���:���ַ���,�쳣����:������Ϣ
    ˵  �� : ����ȡ���û�Ȩ�޷ŵ��������
 ------------------------------------ */
    function user_parmission($s_sid,$auth_user) {
        // ��ȡ�û�Ȩ��
        $oresult = tep_db_query("select permission from permissions where userid = '" . $auth_user . "'");
        if (!$oresult) {                                        // �����ʱ��
            $this->putCertifyLog($s_sid,'n',$auth_user);
            die('<br>'.TEXT_ERRINFO_DBERROR);
        }

        $nrow = tep_db_num_rows($oresult);      // ��ȡ��¼����
        if ($nrow == 1) {                       // �����UID���û���ע���ʱ��
            $arec = tep_db_fetch_array($oresult, DB_FETCHMODE_ASSOC); // ��ȡ��¼
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
    ��  �� : ����Ա(admin)ע��
    ��  �� : û��
    ����ֵ : û��
    ˵  �� : �û�һ����û��ע���ʱ��,ע�����Ա(admin)
 ------------------------------------ */
    function user_admin_entry() {
        // ����Ա(admin)ע��
        $oresult = tep_db_query("select * from users");
        $nrow = tep_db_num_rows($oresult); // ��ȡ��¼����
        if ($nrow == 0) {      // �û�һ����û��ע���ʱ��,ע�����Ա
            $s_pwd = crypt('admin');
            $result = tep_db_query("insert into users values ('admin','$s_pwd','�����ƥ������','')");
            $result = tep_db_query("insert into permissions values ('admin',15)");
        }
    }

/* -------------------------------------
    ��  �� : ע��&#35748;&#35777;��־
    ��  �� : $s_sid             - (i) sessionID
             $s_status          - (i) ״̬
             $auth_user         - (i) �û�ID
    ����ֵ : û��
 ------------------------------------ */
    function putCertifyLog($s_sid,$s_status,$auth_user) {
        $this->deleteCertifyLog();  // ����һ��ʱ��ɾ���ɵ���֤��־
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
            // ��IP��ַŪ��4�ֽڵ�����
            $n_ip4 = 0;
            while (list($n_key, $s_byte) = each($as_ip)) {
                $n_ip4 = ($n_ip4 << 8) | (int)$s_byte;
            }

            $status_out_c = ''; $status_out = '';
            if ($s_status == 'a') {
                $status_out_c = ',logoutstatus';
                $status_out = ",'i'";
            }

            // ��¼
            $result = tep_db_query("insert into login(sessionid,logintime,lastaccesstime,account,loginstatus,address$status_out_c) values('$s_sid','" . $time_ . "','" . $time_ . "','" . $auth_user . "','$s_status',$n_ip4$status_out)");
            if (!$result) {
                $this->isErr = TRUE;
                die('<br>'.TEXT_ERRINFO_DBERROR);
            }
        }
        return TRUE;
    }

/* -------------------------------------
    ��  �� : ��¼��ʱ
    ��  �� : $s_sid             - (i) sessionID
             $auth_user         - (i) �û�ID
    ����ֵ : û��
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
    ��  �� : ����һ��ʱ�佫������־��״̬����Ϊ�˳���½
    ��  �� : $actime            - (i) ��ʱʱ��
             $s_sid             - (i) ״̬
    ����ֵ : û��
 ------------------------------------ */
    function logoutCertifyLog($actime,$s_sid) {
        // �������ڵ�sessionID�����շ���ʱ�̱�ȳ�ʱʱ�̻���Ļ�����ʹ�������ĵ�½Ҳǿ���˳���½
        $result = tep_db_query("update login set logoutstatus='o' where sessionid!='$s_sid' and lastaccesstime<'$actime' and logoutstatus='i'");
    }

/* -------------------------------------
    ��  �� : ����һ��ʱ��ɾ���ɵķ�����־
    ��  �� : û��
    ����ֵ : û��
 ------------------------------------ */
    function deleteCertifyLog() {
        if ( 0 < $this->login_log_span) {
            $sspan_date = date($this->date_format, mktime() - $this->login_log_span * 3600 * 24);
            $result = tep_db_query("delete from login where lastaccesstime < '$sspan_date'");
        }
    }
}

/* -------------------------------------
    ��  �� : �˳���½
    ��  �� : $erf               - (i) �����ǩ
             $s_status          - (i) ״̬
    ����ֵ : û��
 ------------------------------------ */
function logout_user($erf='',$s_status='') {
    if ($s_status) {    // ��¼�˳���½
        $s_sid = session_id();
        $result = tep_db_query("update login set logoutstatus='$s_status' where sessionid='$s_sid'");
    }
    //for redirect to admin.php after login
    tep_redirect('admin_login.php' . ($erf ? ('?erf='.$erf) : ''));
}

/* =====================================
    ��Ҫ
 ===================================== */
if ($GLOBALS['_GET']['execute_logout_user']) { logout_user(FALSE,'o'); } 

if (file_exists(DIR_WS_LANGUAGES . $language . '/user_certify.php')) {
    include(DIR_WS_LANGUAGES . $language . '/user_certify.php');
}
$ocertify = new user_certify(session_id());     // ��֤
if ($ocertify->isErr) { logout_user(1); }
elseif ($ocertify->isFirstTime) { logout_user(); }

?>
