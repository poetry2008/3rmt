<?php
/* *********************************************************
  �⥸�塼��̾: users.php
 * 2001/5/29
 *   modi 2002-05-10
 * Naomi Suzukawa
 * suzukawa@bitscope.co.jp
  ----------------------------------------------------------
�ʥӥ��������ܥå����ʥ桼����  ��¸�Υ����ƥ���Ȥ߹���

  ���ѹ�����
  2003-04-16 ���줾��θ�����б�������ʸ���ե����������
********************************************************* */

// �ե�����̾
define('FILENAME_USERS', 'users.php');
define('FILENAME_USERS_LOGINLOG', 'users_log.php');

// 2003-04-16 modi -s
// �������������򤷤Ƥ����Τ򡢸���ե������������ư�ư����
  if (file_exists(DIR_WS_LANGUAGES . $language . '/boxes_users.php')) {
    include(DIR_WS_LANGUAGES . $language . '/boxes_users.php');
  }
// 2003-04-16 modi -e

?>

<!-- users //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_USER,
                     'link'  => tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=users'));

  if ($selected_box == 'users') {
    if ($ocertify->npermission == 15) $loginlog = '<a href="' . tep_href_link(FILENAME_USERS_LOGINLOG, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_USER_LOG . '</a>';
	else $loginlog = '';
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_USERS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_USER_ADMIN . '</a><br>' .
                                   '<a href="' . tep_href_link(basename($PHP_SELF), '', 'NONSSL') . '?execute_logout_user=1" class="menuBoxContentLink">' . BOX_USER_LOGOUT . '</a><br>' .
                                   $loginlog);
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- users //-->
