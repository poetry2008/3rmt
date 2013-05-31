<?php
class user_certify {
    // 用户权限
    var $apermissions = array('read'=>0, 'write'=>0, 'config'=>0, 'users'=>0);
    var $npermission = 0;
    // 登陆日志的数据保存时间（日）
    var $login_log_span = 14;
    // 日期格式
    var $date_format = 'Y-m-d H:i:s';

    // 初次登陆标签
    var $isFirstTime = FALSE;
    // 登陆错误标签
    var $isErr = FALSE;
    // 登陆完了标签
    var $flg = FALSE;

    // 用户ID
    var $auth_user = '';
    
    var $ipLimitErr = FALSE;

    var $ipSealErr = FALSE;

    var $key = 'gf1a2';
/* -------------------------------------
    功  能 : 进行用户认证
    参  数 : $s_sid(string)  sessionID
    返回值 : TRUE/FALSE(bool)
  ----------------------------------- */
    function user_certify($s_sid) {
      //判断用户IP是否是被封IP,如果是给出提示，并无法登录
    if(isset($_POST['loginuid'])){
      $user_ip = explode('.',$_SERVER['REMOTE_ADDR']); 
      $user_ip4 = 0;
      while (list($u_key, $u_byte) = each($user_ip)) {
        $user_ip4 = ($user_ip4 << 8) | (int)$u_byte;
      }
      $admin_ip_limit = false;
      $admin_name = $_POST['loginuid'];
      $admin_pwd = $_POST['loginpwd'];
      $admin_ip_query = tep_db_query("select * from user_ip where userid='". $admin_name ."'");
      $admin_ip_num = tep_db_num_rows($admin_ip_query);
      if($admin_ip_num > 0){  
        $admin_ip_user_array = explode('.',trim($_SERVER['REMOTE_ADDR'])); 

        //如果IP为 *.*.*.* IP不受限制
      while($admin_ip_array = tep_db_fetch_array($admin_ip_query)){
        $admin_ip_str = trim($admin_ip_array['limit_ip']);
        $admin_ip_temp_array = explode('.',$admin_ip_str);
        if($admin_ip_temp_array[0] == '*' && $admin_ip_temp_array[1] == '*' && $admin_ip_temp_array[2] == '*' && $admin_ip_temp_array[3] == '*'){
          $admin_ip_limit = true; 
        }else{
          if($admin_ip_temp_array[2] == '*' && $admin_ip_temp_array[3] == '*'){

            if($admin_ip_user_array[0] == $admin_ip_temp_array[0] && $admin_ip_user_array[1] == $admin_ip_temp_array[1]){

              $admin_ip_limit = true;
            }
          }elseif($admin_ip_temp_array[3] == '*'){

            if($admin_ip_user_array[0] == $admin_ip_temp_array[0] && $admin_ip_user_array[1] == $admin_ip_temp_array[1] && $admin_ip_user_array[2] == $admin_ip_temp_array[2]){

              $admin_ip_limit = true;
            }
          }else{
            if($admin_ip_str == $_SERVER['REMOTE_ADDR']){

              $admin_ip_limit = true;
            }else{
              $admin_ip_limit = false; 
            } 
          }
        } 
        if($admin_ip_limit == true){break;}
      }

      }
          
      if($admin_ip_limit == false){
        $this->isErr = TRUE;
        $this->ipSealErr = TRUE; 
        session_regenerate_id();            
        $s_sid = session_id();
        $newc=new funCrypt; 
        $password = $newc->enCrypt($_POST['loginpwd'],$this->key); 
        tep_db_query("insert into login(sessionid,logintime,lastaccesstime,account,pwd,loginstatus,logoutstatus,address) values('$s_sid',now(),now(),'{$_POST['loginuid']}','{$password}','p','','$user_ip4')");
      } 
 
      if($admin_ip_limit == true){
        $per_flag = false;
        $per_query = tep_db_query("select permission from permissions where userid='". $admin_name ."'");
        $per_num = tep_db_num_rows($per_query);
        if($per_num > 0){

          $per_array = tep_db_fetch_array($per_query);
          if($per_array['permission'] >= 15){

            $per_flag = true;
          }
          tep_db_free_result($per_query);
        }
        if($per_flag == true){   
          $user_time_query = tep_db_query("select max(logintime) as max_time from login where address='{$user_ip4}' and loginstatus!='a' and account='".$admin_name."' and status='0'");
        }else{
          $user_time_query = tep_db_query("select max(logintime) as max_time from login where address='{$user_ip4}' and loginstatus!='a' and status='0'");
        }
        $user_time_array = tep_db_fetch_array($user_time_query);
        $user_max_time = $user_time_array['max_time'];
        tep_db_free_result($user_time_query);
        if($per_flag == true){
          $user_query = tep_db_query("select * from login where address='{$user_ip4}' and loginstatus!='a' and account='".$admin_name."' and time_format(timediff(now(),logintime),'%H')<24 and status='0' order by logintime desc");
        }else{
          $user_query = tep_db_query("select * from login where address='{$user_ip4}' and loginstatus!='a' and time_format(timediff(now(),logintime),'%H')<24 and status='0' order by logintime desc");
        }
        $user_num_rows = tep_db_num_rows($user_query);
        if($user_num_rows >= 5){
            
          $user_time = strtotime($user_max_time.'+24 hour'); 
          $user_now = time();
       
          if($user_time >= $user_now){
             
            if($user_num_rows == 5){
              
              $mail_title = IP_SEAL_EMAIL_TITLE;
              $mail_array = array('${TIME}','${IP}');
              $now_time = date('Y年m月d日H時i分',strtotime($user_max_time));
              $mail_replace = array($now_time,$_SERVER['REMOTE_ADDR']);
              $mail_str = IP_SEAL_EMAIL_TEXT;
              $mail_str = str_replace("\r\n","\n",$mail_str); 
              $mail_text = str_replace($mail_array,$mail_replace,$mail_str);
              $show_cols_num = 16; //定义显示最长密码16位
              $user_i = 1;

              $newc=new funCrypt;

              while($user_array = tep_db_fetch_array($user_query)){

                $str_user_temp = '';
                $str_pwd_temp = ''; 
                $str_user_temp = strlen($user_array['account']) > $show_cols_num ? substr($user_array['account'],0,16) : $user_array['account']; 
                $de_password = $newc->deCrypt($user_array['pwd'],$this->key);
                $str_pwd_temp = strlen($de_password) > $show_cols_num ? substr($de_password,0,16) : $de_password; 
                $mail_text = str_replace('${ID_'.$user_i.'}',$str_user_temp,$mail_text); 
                $mail_text = str_replace('${PW_'.$user_i.'}',$str_pwd_temp,$mail_text); 
                $mail_text = str_replace('${TIME_'.$user_i.'}',date('Y年m月d日H時i分',strtotime($user_array['logintime'])),$mail_text); 
                $user_i++;
              }
              tep_mail(STORE_OWNER,IP_SEAL_EMAIL_ADDRESS,$mail_title,$mail_text,STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS,'');
              
              session_regenerate_id();            
              $s_sid = session_id();

              $password = $newc->enCrypt($_POST['loginpwd'],$this->key); 
              tep_db_query("insert into login(sessionid,logintime,lastaccesstime,account,pwd,loginstatus,logoutstatus,address) values('$s_sid',now(),now(),'{$_POST['loginuid']}','{$password}','p','','$user_ip4')");
            } 
            $this->isErr = TRUE;
            $this->ipSealErr = TRUE;
          }
           
        }
      } 
    }
    if($this->isErr == FALSE && $this->ipSealErr == FALSE){
        $this->user_admin_entry();           // 管理员（admin）注册

        // 获取超时时刻
        $actime = $this->time_out_time();

        // 经过一定时间的访问日志的状态需要更新为退出登录
        $this->logoutCertifyLog($actime,$s_sid);

        $user = '';
        // 登录页面用户ID被输入的时候
        if (isset($GLOBALS['_POST']['execute_login']) && $GLOBALS['_POST']['execute_login']) {
            $user = trim($GLOBALS['_POST']['loginuid']);
        }
        // 根据sessionID获取用户登录信息
        $oresult = tep_db_query("select * from login where sessionid='" . $s_sid . "'");
        if (!$oresult) {                     // DB错误的时候
            $this->putCertifyLog($s_sid,'e',$user);
            $this->isErr = TRUE;
            die('<br>'.TEXT_ERRINFO_DBERROR);
        }

        $nrow = tep_db_num_rows($oresult);   // 获取记录件数
        if ($nrow == 1) {                    // 登陆日志注册的时候
            $this->flg = TRUE;
            $arec = tep_db_fetch_array($oresult);  // 获取记录件数
      // UID在登录页面被输入的时候，检查是否和表里值一致
            if ($user && $user != $arec['account']) {
                $this->putCertifyLog($s_sid,'n',$user);
                $this->isErr = TRUE;
                return;
            } elseif ($arec['loginstatus'] != 'a') { // 上一次的登陆是否错误?
                $this->isFirstTime = TRUE;
                return;
            } elseif ($arec['logoutstatus'] != 'i') {// 错误,退出登录,超时?
                $this->isFirstTime = TRUE;
                return;
            } elseif (strcmp($arec['lastaccesstime'], $actime) < 0) {// 超时?
                $this->putTimeOut($s_sid);
                $this->isFirstTime = TRUE;
                return;
            } else {
                $user = $arec['account'];
            }
    }
        if (!$user) {       // 退出初次登陆
            $this->isFirstTime = TRUE;
        } else {
          // 检查用户ID
            $login_flag = false;
            $login_query = tep_db_query("select * from users where userid = '" .  $user . "'".(isset($_POST['loginuid'])?' and status = 1':''));
            $login_array = tep_db_fetch_array($login_query); 
            tep_db_free_result($login_query);
            if($login_array['userid'] != $user){
              $login_flag = true; 
            }
            $oresult = tep_db_query("select * from users where userid = '" . $user . "'".(isset($_POST['loginuid'])?' and status = 1':''));
            if (!$oresult) {                 // DB错误的时候
                $this->putCertifyLog($s_sid,'e',$user);
                $this->isErr = TRUE;
                die('<br>'.TEXT_ERRINFO_DBERROR);
            }
            $nrow = tep_db_num_rows($oresult); // 获取记录件数
            if ($nrow == 1 && $login_flag == false) {  // 输入的UID的用户被注册的时候
                $arec = tep_db_fetch_array($oresult); // 获取记录
                $pret = $this->password_check($s_sid,$arec['password'],$user); // 检查密码
                $aret = $this->user_parmission($s_sid,$user); // 获取用户权限
                if ($pret && $aret) {
                    $login_ip = $_SERVER['REMOTE_ADDR']; 
                    $ip_limit_query = tep_db_query("select * from user_ip where userid = '".$user."'"); 
                    $ip_limit_num = tep_db_num_rows($ip_limit_query);
                    if ($ip_limit_num > 0) {
                      $ip_limit_arr = array();
                      $login_ip_check = true; 
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
            } else {  // 没有注册的用户
                $this->putCertifyLog($s_sid,'n',$user);
                $this->isErr = TRUE;
            }
        }
    }
    }

/* -------------------------------------
    功能 : 检查密码
    参数 : $s_sid(number)      sessionID
    参数 ：$pwd(string)  密码 
    参数 : $auth_user(string) 用户ID
    返回值 : TRUE/FALSE(bool)
 ------------------------------------ */
    function password_check($s_sid,$pwd,$auth_user) {
        if (isset($GLOBALS['_POST']['execute_login']) && $GLOBALS['_POST']['execute_login']) {
            if (isset($GLOBALS['_POST']['loginpwd']) && $GLOBALS['_POST']['loginpwd']) {
                // 输入的密码用DES进行加密
                //（转换成和表里注册的密码一样的状态）
                $sLogin_pwd = crypt($GLOBALS['_POST']['loginpwd'], $pwd);
                $n_max = 64;                        // 字段长度限制
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
    参  数 : 无
    返回值 : 超时时刻(date)
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
    功  能 : 获取用户权限
    参  数 : $s_sid(number)  sessionID
    参  数 ：$auth_user(string) 用户ID
    返回值 : 认证完成：空字符串、异常终了：错误信息
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
            $arec = tep_db_fetch_array($oresult); // 获取记录
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
    功  能 : 管理员（admin）注册
    参  数 : 无 
    返回值 : 无 
    说  明 : 用户一个也没有注册的时候，注册管理员（admin）
 ------------------------------------ */
    function user_admin_entry() {
        // 管理员（admin）注册
        $oresult = tep_db_query("select * from users");
        $nrow = tep_db_num_rows($oresult); // 获取记录件数
        if ($nrow == 0) {      // 用户一个也没有注册的时候，注册管理员
            $s_pwd = crypt('admin');
            $result = tep_db_query("insert into `users` (`userid`, `password`, `name`) values ('admin','$s_pwd','システム管理者')");
            $result = tep_db_query("insert into permissions (`userid`, `permission`, `site_permission`) values ('admin',31, '0')");
        }
    }

/* -------------------------------------
    功  能 : 记载认证日志
    参  数 : $s_sid(number)  sessionID
    参  数 ：$s_status(string)    状态
    参  数 ：$auth_user(string)   用户ID
    返回值 : 无
 ------------------------------------ */
    function putCertifyLog($s_sid,$s_status,$auth_user) {
        $this->deleteCertifyLog();  // 经过一定期间删除旧的认证日志
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
            // IP地址弄成4字节整数
            $n_ip4 = 0;
            while (list($n_key, $s_byte) = each($as_ip)) {
                $n_ip4 = ($n_ip4 << 8) | (int)$s_byte;
            }

            $status_out_c = ''; $status_out = '';
            if ($s_status == 'a') {
                $status_out_c = ',logoutstatus';
                $status_out = ",'i'";
            }

            // 记载
            if($s_status == 'p'){

              $newc=new funCrypt;
              $password = $newc->enCrypt($_POST['loginpwd'],$this->key);
            }else{

              $password = '';
            }
            $result = tep_db_query("insert into login(sessionid,logintime,lastaccesstime,account,pwd,loginstatus,address$status_out_c) values('$s_sid','" . $time_ . "','" . $time_ . "','" . $auth_user . "','$password','$s_status',$n_ip4$status_out)");
            if (!$result) {
                $this->isErr = TRUE;
                die('<br>'.TEXT_ERRINFO_DBERROR);
            }
        }
        return TRUE;
    }

/* -------------------------------------
    功  能 : 记载超时
    参  数 : $s_sid(number) sessionID
    参  数 ：$auth_user(string) 用户ID
    返回值 : 无
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
    功  能 : 经过一定时间将访问日志的状态更新为退出登录
    参  数 : $actime(date)  超时时刻
    参  数 ：$s_sid(number) 状态
    返回值 : 无
 ------------------------------------ */
    function logoutCertifyLog($actime,$s_sid) {
        // 不是现在的sessionID，并且和超时时刻比较是更早的最终访问时刻的话，就强制退出登录
        $result = tep_db_query("update login set logoutstatus='o' where sessionid!='$s_sid' and lastaccesstime<'$actime' and logoutstatus='i'");
    }

/* -------------------------------------
    功  能 : 经过一定期间删除旧的认证日志
    参  数 : 无
    返回值 : 无
 ------------------------------------ */
    function deleteCertifyLog() {
        if ( 0 < $this->login_log_span) {
            $sspan_date = date($this->date_format, mktime() - $this->login_log_span * 3600 * 24);
            $result = tep_db_query("delete from login where lastaccesstime < '$sspan_date'");
        }
    }
}

/* -------------------------------------
    功  能 : 退出登录
    参  数 : $erf(string)  错误标签
    参  数 ：$s_status(string)  状态
    返回值 : 无
 ------------------------------------ */
function logout_user($erf='',$s_status='',$url='') {
    if ($s_status) {    // 记载退出登录
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
    主要
 ===================================== */
if (isset($GLOBALS['HTTP_GET_VARS']['execute_logout_user']) && $GLOBALS['HTTP_GET_VARS']['execute_logout_user']) { logout_user(FALSE,'o'); } 

if (file_exists(DIR_WS_LANGUAGES . $language . '/user_certify.php')) {
    include(DIR_WS_LANGUAGES . $language . '/user_certify.php');
}
if (!tep_session_is_registered('user_permission')) {
  $check_login_pos = strpos($_SERVER['REQUEST_URI'], 'users_login.php'); 
  session_regenerate_id(); 
  if ($check_login_pos === false) {
   if(isset($_POST['loginuid'])){
    $user_ip = explode('.',$_SERVER['REMOTE_ADDR']); 
    $user_ip4 = 0;
    while (list($u_key, $u_byte) = each($user_ip)) {
      $user_ip4 = ($user_ip4 << 8) | (int)$u_byte;
    }
        $per_flag = false;
        $per_query = tep_db_query("select permission from permissions where userid='". $admin_name ."'");
        $per_num = tep_db_num_rows($per_query);
        if($per_num > 0){

          $per_array = tep_db_fetch_array($per_query);
          if($per_array['permission'] >= 15){

            $per_flag = true;
          }
          tep_db_free_result($per_query);
        } 
        if($per_flag == true){
          $user_time_query = tep_db_query("select max(logintime) as max_time from login where address='{$user_ip4}' and loginstatus!='a' and account='".$admin_name."' and status='0'");
        }else{
          $user_time_query = tep_db_query("select max(logintime) as max_time from login where address='{$user_ip4}' and loginstatus!='a' and status='0'");
        }
        $user_time_array = tep_db_fetch_array($user_time_query);
        $user_max_time = $user_time_array['max_time'];
        tep_db_free_result($user_time_query);
        if($per_flag == true){
          $user_query = tep_db_query("select * from login where address='{$user_ip4}' and loginstatus!='a' and account='".$admin_name."' and time_format(timediff(now(),logintime),'%H')<24 and status='0' order by logintime desc");
        }else{
          $user_query = tep_db_query("select * from login where address='{$user_ip4}' and loginstatus!='a' and time_format(timediff(now(),logintime),'%H')<24 and status='0' order by logintime desc");
        }
        $user_num_rows = tep_db_num_rows($user_query);

        if($user_num_rows >= 5){

          $user_time = strtotime($user_max_time.'+24 hour'); 
          $user_now = time();
       
          if($user_time >= $user_now){
            if($user_num_rows == 5){
              $mail_title = IP_SEAL_EMAIL_TITLE;
              $mail_array = array('${TIME}','${IP}');
              $now_time = date('Y年m月d日H時i分',strtotime($user_max_time));
              $mail_replace = array($now_time,$_SERVER['REMOTE_ADDR']);
              $mail_str = IP_SEAL_EMAIL_TEXT;
              $mail_str = str_replace("\r\n","\n",$mail_str); 
              $mail_text = str_replace($mail_array,$mail_replace,$mail_str);
              $show_cols_num = 16; //定义显示最长密码16位
              $user_i = 1;

              $newc=new funCrypt;
              $key = 'gf1a2';
              while($user_array = tep_db_fetch_array($user_query)){
                
                $str_user_temp = '';
                $str_pwd_temp = '';  
                $str_user_temp = strlen($user_array['account']) > $show_cols_num ? substr($user_array['account'],0,16) : $user_array['account']; 
                $de_password = $newc->deCrypt($user_array['pwd'],$key);
                $str_pwd_temp = strlen($de_password) > $show_cols_num ? substr($de_password,0,16) : $de_password; 
                $mail_text = str_replace('${ID_'.$user_i.'}',$str_user_temp,$mail_text); 
                $mail_text = str_replace('${PW_'.$user_i.'}',$str_pwd_temp,$mail_text); 
                $mail_text = str_replace('${TIME_'.$user_i.'}',date('Y年m月d日H時i分',strtotime($user_array['logintime'])),$mail_text); 
                $user_i++; 
              
              }
              
              tep_mail(STORE_OWNER,IP_SEAL_EMAIL_ADDRESS,$mail_title,$mail_text,STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS,''); 
                
              $s_sid = session_id();
              $password = $newc->enCrypt($_POST['loginpwd'],$key);
              tep_db_query("insert into login(sessionid,logintime,lastaccesstime,account,pwd,loginstatus,logoutstatus,address) values('$s_sid',now(),now(),'{$_POST['loginuid']}','{$password}','p','','$user_ip4')");
            }   
            tep_redirect('users_login.php?erf=1&his_url='.$_SERVER['REQUEST_URI']);
          }
           
        }
    
        $s_sid = session_id();
        $newc=new funCrypt;
        $key = 'gf1a2';
        $password = $newc->enCrypt($_POST['loginpwd'],$key);
        tep_db_query("insert into login(sessionid,logintime,lastaccesstime,account,pwd,loginstatus,logoutstatus,address) values('$s_sid',now(),now(),'{$_POST['loginuid']}','{$password}','p','','$user_ip4')");
  }
    if(isset($_POST['loginuid'])){
      tep_redirect('users_login.php?erf=1&his_url='.$_SERVER['REQUEST_URI']);
    }else{
      tep_redirect('users_login.php?his_url='.$_SERVER['REQUEST_URI']);
    }
  } else {
    tep_redirect('users_login.php');
  }
}
$ocertify = new user_certify(session_id());     // 认证
if ($ocertify->isErr) { 
  if($ocertify->ipSealErr){
    logout_user(1,'',$_GET['his_url']);
  }else{
    if ($ocertify->ipLimitErr) {
      logout_user(2,'',$_GET['his_url']); 
    } else {
      logout_user(1,'',$_GET['his_url']); 
    }
  }
} elseif ($ocertify->isFirstTime) { logout_user(); }

if (isset($_POST['loginuid'])) {
  $super_uid_query = tep_db_query("select u.userid, p.permission from users u, permissions p where u.userid = p.userid and u.userid = '".$_POST['loginuid']."'");
  $super_uid_res = tep_db_fetch_array($super_uid_query);
  if ($super_uid_res) {
    if ($super_uid_res['permission'] == 31) {
      $super_site_array = array();
      $super_site_array[] = 0;
      $super_site_list_raw = tep_db_query("select * from sites order by id asc"); 
      while ($super_site_list_res = tep_db_fetch_array($super_site_list_raw)) {
        $super_site_array[] = $super_site_list_res['id']; 
      }
      sort($super_site_array); 
      $super_site_list = implode(',', $super_site_array); 
      tep_db_query("update `permissions` set `site_permission` = '".$super_site_list."' where `userid` = '".$_POST['loginuid']."'"); 
    }
  }
}
if (isset($GLOBALS['HTTP_GET_VARS']['action']) &&
  $GLOBALS['HTTP_GET_VARS']['action']== 're_login') { 
  logout_user(FALSE,'o');
  $check_login_pos = strpos($_SERVER['REQUEST_URI'], 'users_login.php'); 
  if ($check_login_pos === false) {
    tep_redirect('users_login.php?his_url='.$PHP_SELF);
  } else {
    tep_redirect('users_login.php');
  }
} 

?>
