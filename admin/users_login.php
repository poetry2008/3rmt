<?php
/* *********************************************************
   $Id$

  モジュール名: users_log.php
 * 2002-05-13
 * Naomi Suzukawa
 * suzukawa@bitscope.co.jp
  ----------------------------------------------------------
ユーザアクセスログ

  ■変更履歴
********************************************************* */

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
  tep_session_name('osCAdminsID');

// lets start our session
  tep_session_start();


  // セッションID を削除する
  // PHPSESSIDのクッキー名で記録されている
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

// language
  require(DIR_WS_FUNCTIONS . 'languages.php');
  $language = tep_get_languages_directory(DEFAULT_LANGUAGE);

// include the language translations
  require(DIR_WS_LANGUAGES . $language . '.php');

// define our general functions used application-wide
  require(DIR_WS_FUNCTIONS . 'general.php');
  require(DIR_WS_FUNCTIONS . 'html_output.php');

if (file_exists(DIR_WS_LANGUAGES . $language . '/user_certify.php')) {
	include(DIR_WS_LANGUAGES . $language . '/user_certify.php');
}
/* -------------------------------------
	ログイン画面表示
 ------------------------------------ */

// エラーメッセージ
$msg = (isset($erf) && $erg ? '<div align="center"><font color="#FF0000">'.TEXT_ERRINFO_LOGIN.'</font></div>' : '');

echo '<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">' . "\n";
echo '<html ' . HTML_PARAMS . '>' . "\n";
echo '<head>' . "\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=' . CHARSET . '">' . "\n";
echo '<title>' . TITLE . '</title>' . "\n";
echo '<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">' . "\n";
echo '</head>' . "\n";
echo '<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">' . "\n";

echo tep_draw_form('defaultpage', FILENAME_DEFAULT . "?SID=" . session_id());		// <form>タグの出力

echo '<!-- body_text //-->' . "\n";
echo '<table border="0" cellspacing="0" cellpadding="2">' . "\n";
/*
echo '<tr>' . "\n";
echo '<td>';
echo tep_image(DIR_WS_IMAGES . 'oscommerce.gif', 'osCommerce', '204', '50');
echo '</td>' . "\n";
echo '</tr>' . "\n";
*/
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
echo tep_draw_input_field("execute_login", BUTTON_LOGIN, "", FALSE, "submit", FALSE);	// ログイン
echo '</td>' . "\n";
echo '</tr>' . "\n";
echo '</table>' . "\n";

echo $msg;

echo '</td>' . "\n";
echo '</tr>' . "\n";
echo '</table>' . "\n";

echo "</form>\n";						// フォームのフッター

echo "</body>\n";
echo "</html>\n";
?>
