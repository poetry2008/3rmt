<?php
class user_certify {
    // 用户权限
    var $apermissions = array('read'=>0, 'write'=>0, 'config'=>0, 'users'=>0);
    var $npermission = 0;
    // 登陆日志的数据保存期间(日)
    var $login_log_span = 14;
    // 日期格式
    var $date_format = 'Y-m-d H:i:s';

    // 初次登陆标签
    var $isFirstTime = FALSE;
    // 登陆用户标签
    var $isErr = FALSE;
    // 登陆完了标签
    var $flg = FALSE;

    // 用户ID
    var $auth_user = '';

/* -------------------------------------
    功  能 : 构造函数
    参  数 : $s_sid             - (i) sessionID
    返回值 : TRUE/FALSE
    说  明 : 进行用户的认证
 ------------------------------------ */
    function user_certify($s_sid) {
        $this->user_admin_entry();           // 管理员(admin)注册

        // 获取超时时刻
        $actime = $this->time_out_time();
        //error_log('USER ' . date($this->date_format) . ' user_certify start. timeout='.$actime . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);// DEBUG

        // 经过一定时间将访问日志的状态更新
        $this->logoutCertifyLog($actime,$s_sid);

        $user = '';
        // 登陆页面用户ID被输入的时候
        if ($GLOBALS['_POST']['execute_login']) {
            $user = trim($GLOBALS['_POST']['loginuid']);
        }
        // 通过sessionID获取用户登陆信息
        $oresult = tep_db_query("select * from login where sessionid='" . $s_sid . "'");
        if (!$oresult) {                     // DB错误的时候
            $this->putCertifyLog($s_sid,'e',$user);
            $this->isErr = TRUE;
            die('<br>'.TEXT_ERRINFO_DBERROR);
        }

        $nrow = tep_db_num_rows($oresult);   // 获取记录件数
        if ($nrow == 1) {                    // 登陆日志被注册的时候
            $this->flg = TRUE;
            $arec = tep_db_fetch_array($oresult);  // 获取记录
			// UID在登陆页面被输入的时候检查是否和表里的值相同
            if ($user && $user != $arec['account']) {
                $this->putCertifyLog($s_sid,'n',$user);
                $this->isErr = TRUE;
                return;
            } elseif ($arec['loginstatus'] != 'a') { // 之前的登陆错了吗?
                $this->isFirstTime = TRUE;
                return;
            } elseif ($arec['logoutstatus'] != 'i') {// 错误,退出登录,超时
                $this->isFirstTime = TRUE;
                return;
            } elseif (strcmp($arec['lastaccesstime'], $actime) < 0) {// 超时
                //error_log('USER ' . date($this->date_format) . ' timeout lastaccesstime[' . $arec['lastaccesstime'] . '] limit=[' . $actime . "]\n", 3, STORE_PAGE_PARSE_TIME_LOG);// DEBUG
                $this->putTimeOut($s_sid);
                $this->isFirstTime = TRUE;
                return;
            } else {
                $user = $arec['account'];
            }
		}

        if (!$user) {       // 退出初次登陆的处理
            $this->isFirstTime = TRUE;
        } else {
            // 用户ID检查
            $oresult = tep_db_query("select * from users where userid = '" . $user . "'");
            if (!$oresult) {                 // DB错误的时候
                $this->putCertifyLog($s_sid,'e',$user);
                $this->isErr = TRUE;
                die('<br>'.TEXT_ERRINFO_DBERROR);
            }

            $nrow = tep_db_num_rows($oresult); // 获取记录件数
            if ($nrow == 1) {  // 输入的UID的用户被注册的时候
                $arec = tep_db_fetch_array($oresult); // 获取记录
                $pret = $this->password_check($s_sid,$arec['password'],$user); // 检查密码
                $aret = $this->user_parmission($s_sid,$user); // 获取用户权限
                if ($pret && $aret) {
                    $this->putCertifyLog($s_sid,'a',$user);
                    $this->auth_user = $user;
                } else {
                    $this->isErr = TRUE;
                }
            } else {  // 没有注册的用户
                $this->putCertifyLog($s_sid,'n',$user);
                $this->isErr = TRUE;
            }
        }
    }

/* -------------------------------------
    功  能 : 检查密码
    参  数 : $s_sid             - (i) sessionID
             $pwd               - (i) 密码
             $auth_user         - (i) 用户ID
    返回值 : TRUE/FALSE
 ------------------------------------ */
    function password_check($s_sid,$pwd,$auth_user) {
        if ($GLOBALS['_POST']['execute_login']) {
            //error_log('USER ' . date($this->date_format) . ' password_check user='. $auth_user . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);// DEBUG
            if ($GLOBALS['_POST']['loginpwd']) {
                // 输入的密码用DES进行加密
                //(转换成和表里注册的密码相同的状态)
                $sLogin_pwd = crypt($GLOBALS['_POST']['loginpwd'], $pwd);
                $n_max = 64;                        // 限制字段长度
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
    功  能 : 获取超时时刻
    参  数 : 没有
    返回值 : 超时时刻
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
    功  能 : 获取用户权限
    参  数 : $s_sid             - (i) sessionID
             $auth_user         - (i) 用户ID
    返回值 : 认证完毕:空字符串,异常终了:错误信息
    说  明 : 将获取的用户权限放到类变量里
 ------------------------------------ */
    function user_parmission($s_sid,$auth_user) {
        // 获取用户权限
        $oresult = tep_db_query("select permission from permissions where userid = '" . $auth_user . "'");
        if (!$oresult) {                                        // 错误的时候
            $this->putCertifyLog($s_sid,'n',$auth_user);
            die('<br>'.TEXT_ERRINFO_DBERROR);
        }

        $nrow = tep_db_num_rows($oresult);      // 获取记录件数
        if ($nrow == 1) {                       // 输入的UID的用户被注册的时候
            $arec = tep_db_fetch_array($oresult, DB_FETCHMODE_ASSOC); // 获取记录
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
    功  能 : 管理员(admin)注册
    参  数 : 没有
    返回值 : 没有
    说  明 : 用户一个都没有注册的时候,注册管理员(admin)
 ------------------------------------ */
    function user_admin_entry() {
        // 管理员(admin)注册
        $oresult = tep_db_query("select * from users");
        $nrow = tep_db_num_rows($oresult); // 获取记录件数
        if ($nrow == 0) {      // 用户一个都没有注册的时候,注册管理员
            $s_pwd = crypt('admin');
            $result = tep_db_query("insert into users values ('admin','$s_pwd','システム管理者','')");
            $result = tep_db_query("insert into permissions values ('admin',15)");
        }
    }

/* -------------------------------------
    功  能 : 注册&#35748;&#35777;日志
    参  数 : $s_sid             - (i) sessionID
             $s_status          - (i) 状态
             $auth_user         - (i) 用户ID
    返回值 : 没有
 ------------------------------------ */
    function putCertifyLog($s_sid,$s_status,$auth_user) {
        $this->deleteCertifyLog();  // 经过一定时间删除旧的认证日志
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
            // 将IP地址弄成4字节的整数
            $n_ip4 = 0;
            while (list($n_key, $s_byte) = each($as_ip)) {
                $n_ip4 = ($n_ip4 << 8) | (int)$s_byte;
            }

            $status_out_c = ''; $status_out = '';
            if ($s_status == 'a') {
                $status_out_c = ',logoutstatus';
                $status_out = ",'i'";
            }

            // 记录
            $result = tep_db_query("insert into login(sessionid,logintime,lastaccesstime,account,loginstatus,address$status_out_c) values('$s_sid','" . $time_ . "','" . $time_ . "','" . $auth_user . "','$s_status',$n_ip4$status_out)");
            if (!$result) {
                $this->isErr = TRUE;
                die('<br>'.TEXT_ERRINFO_DBERROR);
            }
        }
        return TRUE;
    }

/* -------------------------------------
    功  能 : 记录超时
    参  数 : $s_sid             - (i) sessionID
             $auth_user         - (i) 用户ID
    返回值 : 没有
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
    功  能 : 经过一定时间将访问日志的状态更新为退出登陆
    参  数 : $actime            - (i) 超时时刻
             $s_sid             - (i) 状态
    返回值 : 没有
 ------------------------------------ */
    function logoutCertifyLog($actime,$s_sid) {
        // 不是现在的sessionID，最终访问时刻表比超时时刻还早的话，即使是正常的登陆也强制退出登陆
        $result = tep_db_query("update login set logoutstatus='o' where sessionid!='$s_sid' and lastaccesstime<'$actime' and logoutstatus='i'");
    }

/* -------------------------------------
    功  能 : 经过一定时间删除旧的访问日志
    参  数 : 没有
    返回值 : 没有
 ------------------------------------ */
    function deleteCertifyLog() {
        if ( 0 < $this->login_log_span) {
            $sspan_date = date($this->date_format, mktime() - $this->login_log_span * 3600 * 24);
            $result = tep_db_query("delete from login where lastaccesstime < '$sspan_date'");
        }
    }
}

/* -------------------------------------
    功  能 : 退出登陆
    参  数 : $erf               - (i) 错误标签
             $s_status          - (i) 状态
    返回值 : 没有
 ------------------------------------ */
function logout_user($erf='',$s_status='') {
    if ($s_status) {    // 记录退出登陆
        $s_sid = session_id();
        $result = tep_db_query("update login set logoutstatus='$s_status' where sessionid='$s_sid'");
    }
    //for redirect to admin.php after login
    tep_redirect('admin_login.php' . ($erf ? ('?erf='.$erf) : ''));
}

/* =====================================
    主要
 ===================================== */
if ($GLOBALS['_GET']['execute_logout_user']) { logout_user(FALSE,'o'); } 

if (file_exists(DIR_WS_LANGUAGES . $language . '/user_certify.php')) {
    include(DIR_WS_LANGUAGES . $language . '/user_certify.php');
}
$ocertify = new user_certify(session_id());     // 认证
if ($ocertify->isErr) { logout_user(1); }
elseif ($ocertify->isFirstTime) { logout_user(); }

?>
