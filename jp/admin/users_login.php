<?php
/* *********************************************************
   $Id$
********************************************************* */
  ini_set('display_errors', 'off'); 
  define('TABLE_CONFIGURATION', 'configuration');
  define('TABLE_LANGUAGES',     'languages');
  define('FILENAME_DEFAULT',    'index.php');

// Set the local configuration parameters - mainly for developers
  if (file_exists('includes/local/configure.php')) include('includes/local/configure.php');

// Include application configuration parameters
  require('includes/configure.php');

// initialize the logger class
  require(DIR_WS_CLASSES . 'logger.php');

  if (!function_exists('session_start')) {
    define('PHP_SESSION_NAME', 'sID');
    define('PHP_SESSION_SAVE_PATH', '/tmp');

    include(DIR_WS_CLASSES . 'sessions.php');
  }

  require(DIR_WS_FUNCTIONS . 'sessions.php');
  tep_session_name('XSID');

// lets start our session
  tep_session_start();

  //删除session的id
  //用php的session名作为cookie名来记录
  setcookie(session_name(), '', time() - 3600, '/');
  setcookie(session_name(), '', time() - 3600, substr(DIR_WS_ADMIN, 0, -1));

// include the database functions
  require(DIR_WS_FUNCTIONS . 'database.php');

// make a connection to the database... now
  tep_db_connect() or die('Unable to connect to database server!');

// set application wide parameters
  $configuration_query = mysql_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION . '');
  while ($configuration = tep_db_fetch_array($configuration_query)) {
    if (!defined($configuration['cfgKey'])) {
      define($configuration['cfgKey'], $configuration['cfgValue']);
    }
  }

/* -----------------------------------------------------
   功能: 获得默认语言id 
   参数: 无 
   返回值: 语言id(int) 
 -----------------------------------------------------*/
function tep_get_default_language_id(){
    $language_id_query = mysql_query("select languages_id, directory from 
        language where code = '" . DEFAULT_LANGUAGE . "'");
    if(@mysql_num_rows($language_id_query)){
      $lan_id_row = mysql_fetch_array($language_id_query);
      $languages_id = $lan_id_row['languages_id'];
    }
    return $languages_id;
}
  require(DIR_WS_FUNCTIONS . 'languages.php');
  $language = tep_get_languages_directory(DEFAULT_LANGUAGE);

// include the language translations
  require(DIR_WS_LANGUAGES . $language . '.php');

// define our general functions used application-wide
  require(DIR_WS_FUNCTIONS . 'general.php');
  require(DIR_WS_FUNCTIONS . 'html_output.php');

if (file_exists(DIR_WS_LANGUAGES . $language . '/user_certify.php')) {
  $_SESSION['PASSWORD_RULES'] = PASSWORD_RULES;
  include(DIR_WS_LANGUAGES . $language . '/user_certify.php');
}

  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  # 永远是改动过的
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  # HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  # HTTP/1.0
  header("Pragma: no-cache");
/* -------------------------------------
  显示登录页面
 ------------------------------------ */
//错误信息

$msg = (isset($erf) && $erf ? '<div align="center"><font color="#FF0000">'.TEXT_ERRINFO_LOGIN.'</font></div>' : '');

if (isset($erf)) {
 
  if ($erf == 2) {
    $msg = '<div align="center"><font color="#FF0000">'.TEXT_ERRINFO_IP.'</font></div>';
  } 
}
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n";
echo '<html ' . HTML_PARAMS . '>' . "\n";
echo '<head>' . "\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=' . CHARSET . '">' . "\n";
echo '<title>' . TITLE . '</title>' . "\n";
echo '<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">' . "\n";
echo '</head>' . "\n";
echo '<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">' . "\n";

if(isset($_GET['his_url'])&&$_GET['his_url']){

echo tep_draw_form('defaultpage', FILENAME_DEFAULT . "?SID=" .
    session_id()."&his_url=".$_GET['his_url']);  //<form>开始 
}else{
echo tep_draw_form('defaultpage', FILENAME_DEFAULT . "?SID=" . session_id()); //<form>开始 
}

echo '<!-- body_text -->' . "\n";
echo '<table border="0" cellspacing="0" cellpadding="2">' . "\n";
echo '<tr>' . "\n";
echo '<td align="center">';

echo '<table border="0" cellspacing="0" cellpadding="2">' . "\n";
echo '<tr>' . "\n";
echo '<td class="pageHeading" colspan="2">' . HEADING_TITLE_ . '<br><br></td>' . "\n";
echo '</tr>' . "\n";

echo '<tr>';
echo '<td>' . TABLE_HEADING_USER . "</td>\n";
echo '<td>';
echo tep_draw_input_field("loginuid", '', 'size="10" style="width:200px;"', FALSE, 'text', FALSE);
echo '</td>' . "\n";
echo '</tr>' . "\n";

echo '<tr>';
echo '<td>' . TABLE_HEADING_PASSWORD . "</td>\n";
echo '<td>';
echo tep_draw_input_field("loginpwd", '', 'size="10" style="width:200px;"', FALSE, 'password', false);
echo '</td>' . "\n";
echo '</tr>' . "\n";

echo '<tr>';
echo '<td>&nbsp;</td>' . "\n";
echo '<td align="left"><br>';
echo tep_draw_input_field("execute_login", BUTTON_LOGIN, "", FALSE, "submit", FALSE); //登录 
echo '</td>' . "\n";
echo '</tr>' . "\n";
echo '</table>' . "\n";

echo $msg;

echo '</td>' . "\n";
echo '</tr>' . "\n";
echo '</table>' . "\n";

echo "</form>\n";           //<form>结束

echo "</body>\n";
echo "</html>\n";
?>
