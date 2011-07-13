<?php
/* *********************************************************
 * クラス名: user_certify.php
 * 管理サイトのユーザ認証を行い、アクセスログに記録する。
 * Naomi Suzukawa <suzukawa@bitscope.co.jp>
 *
 * 2001/05/29 作成
 * 2002/05/10 osCommers 用に変更
 * 2002/05/21 osCommers IE5.01で動作が不正になるため大幅に変更
 *            PHP HTTP(Basic)認証 を PHPセッション管理+パスワード認証に変更
 * 2004/03/19 replace get_cfg_var() with ini_get()
********************************************************* */
class user_certify {
    // ユーザ権限
    var $apermissions = array('read'=>0, 'write'=>0, 'config'=>0, 'users'=>0);
    var $npermission = 0;
    // ログインログのデータ保持期間（日）
    var $login_log_span = 14;
    // 日付形式
    var $date_format = 'Y-m-d H:i:s';

    // 初回ログインフラグ
    var $isFirstTime = FALSE;
    // ログインエラーフラグ
    var $isErr = FALSE;
    // ログイン済みフラグ
    var $flg = FALSE;

    // ユーザID
    var $auth_user = '';
    
    var $ipLimitErr = FALSE;
/* -------------------------------------
    機  能 : コンストラクタ
    引  数 : $s_sid             - (i) セッションID
    戻り値 : TRUE/FALSE
    説  明 : ユーザの認証を行う
 ------------------------------------ */
    function user_certify($s_sid) {
        $this->user_admin_entry();           // 管理者（admin）登録

        // タイムアウト時刻を取得
        $actime = $this->time_out_time();
        //error_log('USER ' . date($this->date_format) . ' user_certify start. timeout='.$actime . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);// DEBUG

        // 一定期間経過したアクセスログのステータスをログアウトに更新する
        $this->logoutCertifyLog($actime,$s_sid);

        $user = '';
        // ログインページでユーザＩＤが入力されているとき
        if (isset($GLOBALS['_POST']['execute_login']) && $GLOBALS['_POST']['execute_login']) {
            $user = trim($GLOBALS['_POST']['loginuid']);
        }
        // セッションＩＤにより、ユーザログイン情報取得
        $oresult = tep_db_query("select * from login where sessionid='" . $s_sid . "'");
        if (!$oresult) {                     // DBエラーだったとき
            $this->putCertifyLog($s_sid,'e',$user);
            $this->isErr = TRUE;
            die('<br>'.TEXT_ERRINFO_DBERROR);
        }

        $nrow = tep_db_num_rows($oresult);   // レコード件数の取得
        if ($nrow == 1) {                    // ログインログが登録されているとき
            $this->flg = TRUE;
            $arec = tep_db_fetch_array($oresult);  // レコードを取得
      // UIDがログインページで入力されているときテーブルの値と等しいかチェック
            if ($user && $user != $arec['account']) {
                $this->putCertifyLog($s_sid,'n',$user);
                $this->isErr = TRUE;
                return;
            } elseif ($arec['loginstatus'] != 'a') { // 前のログインがエラーでないか?
                $this->isFirstTime = TRUE;
                return;
            } elseif ($arec['logoutstatus'] != 'i') {// エラー,ログアウト,タイムアウト?
                $this->isFirstTime = TRUE;
                return;
            } elseif (strcmp($arec['lastaccesstime'], $actime) < 0) {// タイムアウト?
                //error_log('USER ' . date($this->date_format) . ' timeout lastaccesstime[' . $arec['lastaccesstime'] . '] limit=[' . $actime . "]\n", 3, STORE_PAGE_PARSE_TIME_LOG);// DEBUG
                $this->putTimeOut($s_sid);
                $this->isFirstTime = TRUE;
                return;
            } else {
                $user = $arec['account'];
            }
    }
        if (!$user) {       // 初回ログインのとき処理を抜ける
            $this->isFirstTime = TRUE;
        } else {
            // ユーザＩＤチェック
            $oresult = tep_db_query("select * from users where userid = '" . $user . "'");
            if (!$oresult) {                 // DBエラーだったとき
                $this->putCertifyLog($s_sid,'e',$user);
                $this->isErr = TRUE;
                die('<br>'.TEXT_ERRINFO_DBERROR);
            }
            $nrow = tep_db_num_rows($oresult); // レコード件数の取得
            if ($nrow == 1) {  // 入力された UID のユーザが登録されているとき
                $arec = tep_db_fetch_array($oresult); // レコードを取得
                $pret = $this->password_check($s_sid,$arec['password'],$user); // パスワード検査
                $aret = $this->user_parmission($s_sid,$user); // ユーザ権限を取得
                if ($pret && $aret) {
                    $login_ip = $_SERVER['REMOTE_ADDR']; 
                    $ip_limit_query = tep_db_query("select * from user_ip where userid = '".$user."'"); 
                    $ip_limit_num = tep_db_num_rows($ip_limit_query);
                    if ($ip_limit_num > 0) {
                      $ip_limit_arr = array();
                      $login_ip_check = false; 
                      while ($ip_limit_res = tep_db_fetch_array($ip_limit_query)) {
                        $ip_limit_arr = explode('.', $ip_limit_res['limit_ip']);
                        $reg_str = ''; 
                        if (trim($ip_limit_arr[0]) == '*') {
                          $reg_str .= '(\d)+\.'; 
                        } else {
                          $reg_str .= trim($ip_limit_arr[0]).'\.'; 
                        }
                        if (trim($ip_limit_arr[1]) == '*') {
                          $reg_str .= '(\d)+\.'; 
                        } else {
                          $reg_str .= trim($ip_limit_arr[1]).'\.'; 
                        }
                        if (trim($ip_limit_arr[2]) == '*') {
                          $reg_str .= '(\d)+\.'; 
                        } else {
                          $reg_str .= trim($ip_limit_arr[2]).'\.'; 
                        }
                        if (trim($ip_limit_arr[3]) == '*') {
                          $reg_str .= '(\d)+'; 
                        } else {
                          $reg_str .= trim($ip_limit_arr[3]); 
                        }
                        $reg_str = '/'.$reg_str.'/'; 
                        if (preg_match($reg_str, $login_ip)) {
                          $login_ip_check = true; 
                        }
                      }
                      if (!$login_ip_check) {
                        $this->isErr = TRUE;
                        $this->ipLimitErr = TRUE;
                      } else {
                        $this->putCertifyLog($s_sid,'a',$user);
                        $this->auth_user = $user;
                      }
                    } else {
                      $this->putCertifyLog($s_sid,'a',$user);
                      $this->auth_user = $user;
                    }
                } else {
                    $this->isErr = TRUE;
                }
            } else {  // 登録されていないユーザ
                $this->putCertifyLog($s_sid,'n',$user);
                $this->isErr = TRUE;
            }
        }
    }

/* -------------------------------------
    機  能 : パスワードチェック
    引  数 : $s_sid             - (i) セッションID
             $pwd               - (i) パスワード
             $auth_user         - (i) ユーザID
    戻り値 : TRUE/FALSE
 ------------------------------------ */
    function password_check($s_sid,$pwd,$auth_user) {
        if (isset($GLOBALS['_POST']['execute_login']) && $GLOBALS['_POST']['execute_login']) {
            if (isset($GLOBALS['_POST']['loginpwd']) && $GLOBALS['_POST']['loginpwd']) {
                // 入力されたパスワードを DES 暗号化法により暗号化する
                //（テーブルに登録されているパスワードと同じ状態に変換）
                $sLogin_pwd = crypt($GLOBALS['_POST']['loginpwd'], $pwd);
                $n_max = 64;                        // フィールド長の制限
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
    機  能 : タイムアウト時刻取得
    引  数 : なし
    戻り値 : タイムアウト時刻
 ------------------------------------ */
    function time_out_time() {
        if (isset($GLOBALS['SESS_LIFE']) && $GLOBALS['SESS_LIFE']) {
            $life_time = $GLOBALS['SESS_LIFE'];
        } else {
            $life_time = ini_get('session.gc_maxlifetime'); // replace get_cfg_var() with ini_get()
        }
        $life_time = max($life_time,600);
        return date($this->date_format, mktime() - $life_time);
    }

/* -------------------------------------
    機  能 : ユーザ権限取得
    引  数 : $s_sid             - (i) セッションID
             $auth_user         - (i) ユーザID
    戻り値 : 認証完了：空白文字列、異常終了：エラーメッセージ
    説  明 : 取得したユーザ権限をクラス変数にセットする
 ------------------------------------ */
    function user_parmission($s_sid,$auth_user) {
        // ユーザ権限取得
        $oresult = tep_db_query("select permission from permissions where userid = '" . $auth_user . "'");
        if (!$oresult) {                                        // エラーだったとき
            $this->putCertifyLog($s_sid,'n',$auth_user);
            die('<br>'.TEXT_ERRINFO_DBERROR);
        }

        $nrow = tep_db_num_rows($oresult);      // レコード件数の取得
        if ($nrow == 1) {                       // 入力された UID のユーザが登録されているとき
            $arec = tep_db_fetch_array($oresult); // レコードを取得
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
    機  能 : 管理者（admin）登録
    引  数 : なし
    戻り値 : なし
    説  明 : ユーザが一人も登録されていないとき、管理者（admin）を登録する
 ------------------------------------ */
    function user_admin_entry() {
        // 管理者（admin）登録
        $oresult = tep_db_query("select * from users");
        $nrow = tep_db_num_rows($oresult); // レコード件数の取得
        if ($nrow == 0) {      // ユーザが一人も登録されていないとき、管理者登録
            $s_pwd = crypt('admin');
            $result = tep_db_query("insert into users values ('admin','$s_pwd','システム管理者','')");
            $result = tep_db_query("insert into permissions values ('admin',15)");
        }
    }

/* -------------------------------------
    機  能 : 認証ログを記録する
    引  数 : $s_sid             - (i) セッションID
             $s_status          - (i) ステータス
             $auth_user         - (i) ユーザID
    戻り値 : なし
 ------------------------------------ */
    function putCertifyLog($s_sid,$s_status,$auth_user) {
        $this->deleteCertifyLog();  // 一定期間よりも古い認証ログを削除する
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
            // IPアドレスを4バイト整数にパックする
            $n_ip4 = 0;
            while (list($n_key, $s_byte) = each($as_ip)) {
                $n_ip4 = ($n_ip4 << 8) | (int)$s_byte;
            }

            $status_out_c = ''; $status_out = '';
            if ($s_status == 'a') {
                $status_out_c = ',logoutstatus';
                $status_out = ",'i'";
            }

            // 記録
            $result = tep_db_query("insert into login(sessionid,logintime,lastaccesstime,account,loginstatus,address$status_out_c) values('$s_sid','" . $time_ . "','" . $time_ . "','" . $auth_user . "','$s_status',$n_ip4$status_out)");
            if (!$result) {
                $this->isErr = TRUE;
                die('<br>'.TEXT_ERRINFO_DBERROR);
            }
        }
        return TRUE;
    }

/* -------------------------------------
    機  能 : タイムアウトを記録する
    引  数 : $s_sid             - (i) セッションID
             $auth_user         - (i) ユーザID
    戻り値 : なし
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
    機  能 : 一定期間経過したアクセスログのステータスをログアウトに更新する
    引  数 : $actime            - (i) タイムアウト時刻
             $s_sid             - (i) ステータス
    戻り値 : なし
 ------------------------------------ */
    function logoutCertifyLog($actime,$s_sid) {
        // 現在のセッションIDではなく、最終アクセス時刻がタイムアウト時刻よりも前で、正常ログインしているレコードをログアウトさせる
        $result = tep_db_query("update login set logoutstatus='o' where sessionid!='$s_sid' and lastaccesstime<'$actime' and logoutstatus='i'");
    }

/* -------------------------------------
    機  能 : 一定期間よりも古いアクセスログを削除する
    引  数 : なし
    戻り値 : なし
 ------------------------------------ */
    function deleteCertifyLog() {
        if ( 0 < $this->login_log_span) {
            $sspan_date = date($this->date_format, mktime() - $this->login_log_span * 3600 * 24);
            $result = tep_db_query("delete from login where lastaccesstime < '$sspan_date'");
        }
    }
}

/* -------------------------------------
    機  能 : ログアウト
    引  数 : $erf               - (i) エラーブラグ
             $s_status          - (i) ステータス
    戻り値 : なし
 ------------------------------------ */
function logout_user($erf='',$s_status='',$url='') {
    if ($s_status) {    // ログアウトを記録する
        $s_sid = session_id();
        $result = tep_db_query("update login set logoutstatus='$s_status' where sessionid='$s_sid'");
    }
    session_regenerate_id(); 
    if($url){
      $check_login_pos = strpos($_SERVER['REQUEST_URI'], 'users_login.php'); 
      if ($check_login_pos === false) {
        tep_redirect('users_login.php' . ($erf ? ('?erf='.$erf.'&his_url='.$url) : '?his_url='.$rul));
      } else {
        tep_redirect('users_login.php' . ($erf ? ('?erf='.$erf) : ''));
      }
    }else{
    tep_redirect('users_login.php' . ($erf ? ('?erf='.$erf) : ''));
    }
}

/* =====================================
    メイン
 ===================================== */
if (isset($GLOBALS['HTTP_GET_VARS']['execute_logout_user']) && $GLOBALS['HTTP_GET_VARS']['execute_logout_user']) { logout_user(FALSE,'o'); } 

if (file_exists(DIR_WS_LANGUAGES . $language . '/user_certify.php')) {
    include(DIR_WS_LANGUAGES . $language . '/user_certify.php');
}
if (!tep_session_is_registered('user_permission')) {
  $check_login_pos = strpos($_SERVER['REQUEST_URI'], 'users_login.php'); 
  if ($check_login_pos === false) {
    tep_redirect('users_login.php?his_url='.$_SERVER['REQUEST_URI']);
  } else {
    tep_redirect('users_login.php');
  }
}
$ocertify = new user_certify(session_id());     // 認証
if ($ocertify->isErr) { 
  if ($ocertify->ipLimitErr) {
    logout_user(2,'',$_GET['his_url']); 
  } else {
    logout_user(1,'',$_GET['his_url']); 
  }
} elseif ($ocertify->isFirstTime) { logout_user(); }

if (isset($GLOBALS['HTTP_GET_VARS']['action']) &&
  $GLOBALS['HTTP_GET_VARS']['action']== 're_login') { 
  session_regenerate_id(); 
  $check_login_pos = strpos($_SERVER['REQUEST_URI'], 'users_login.php'); 
  if ($check_login_pos === false) {
    tep_redirect('users_login.php?his_url='.$PHP_SELF);
  } else {
    tep_redirect('users_login.php');
  }
} 

?>
