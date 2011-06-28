<?php
/* *********************************************************
  モジュール名: users.php
 * 2001/5/29
 *   modi 2002-05-10
 * Naomi Suzukawa
 * suzukawa@bitscope.co.jp
  ----------------------------------------------------------
ナビゲーションボックス（ユーザ）  既存のシステムに組み込む

  ■変更履歴
  2003-04-16 それぞれの言語に対応させる（言語ファイル作成）
********************************************************* */

// ファイル名
define('FILENAME_USERS', 'users.php');
define('FILENAME_USERS_LOGINLOG', 'users_log.php');
define('FILENAME_ONCE_PWD_LOG', 'pwd_log.php');

// 2003-04-16 modi -s
// ここで定数宣言をしていたのを、言語ファイルを作成して移動する
  if (file_exists(DIR_WS_LANGUAGES . $language . '/boxes_users.php')) {
    include(DIR_WS_LANGUAGES . $language . '/boxes_users.php');
  }
// 2003-04-16 modi -e

?>

<!-- users //-->
          <tr>
            <td>
<?php
  /* 
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_USER,
                     'link'  => tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=users'));

  if ($selected_box == 'users') {
    if ($ocertify->npermission == 15) $loginlog = '<a href="' . tep_href_link(FILENAME_USERS_LOGINLOG, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_USER_LOG . '</a>';
	else $loginlog = '';
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_USERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_USER_ADMIN . '</a><br>' .
                                   '<a href="' . tep_href_link(basename($PHP_SELF), '', 'NONSSL') . '?execute_logout_user=1" class="menuBoxContent_Link">' . BOX_USER_LOGOUT . '</a><br>' .
                                   $loginlog);
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
  */
?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
              <td onmouseover="this.style.cursor='hand'" class="menuBoxHeading">
              &nbsp; 
              <a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col8');"><?php echo BOX_HEADING_USER;?></a> 
              &nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if (in_array('col8', $l_select_box_arr)) {
            ?>
            <div id="col8" style="display:block"> 
            <?php
            } else {
            ?>
            <div id="col8" style="display:none"> 
            <?php
            }
            ?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
    <?php  
        if ($ocertify->npermission == 15) $loginlog = '<a href="' . tep_href_link(FILENAME_USERS_LOGINLOG, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_USER_LOG . '</a>';
	else $loginlog = '';
        echo '<a href="' . tep_href_link(FILENAME_USERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_USER_ADMIN . '</a><br>' .  '<a href="' . tep_href_link(basename($PHP_SELF), '', 'NONSSL') . '?execute_logout_user=1" class="menuBoxContent_Link">' . BOX_USER_LOGOUT . '</a><br>' . 
        $loginlog."<br>".
       '<a href="'.tep_href_link(FILENAME_ONCE_PWD_LOG).'"
       class="menuBoxContent_Link">'.BOX_ONCE_PWD_LOG."</a>"; 
    ?>
                </td>
              </tr>
            </table> 
            </div> 
            </td>
          </tr>
<!-- users //-->
