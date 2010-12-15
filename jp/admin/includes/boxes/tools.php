<?php
/*
  $Id$
*/
?>
<!-- tools //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_TOOLS,
                     'link'  => tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=tools'));

  if ($selected_box == 'tools') {
    $contents[] = array('text'  =>
                   //'<a href="' . tep_href_link(FILENAME_BACKUP) . '" class="menuBoxContentLink">' . BOX_TOOLS_BACKUP . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_BANNER_MANAGER) . '" class="menuBoxContentLink">' . BOX_TOOLS_BANNER_MANAGER . '</a><br>' .
                   //'<a href="' . tep_href_link(FILENAME_CL) . '" class="menuBoxContentLink">' . BOX_TOOLS_CL . '</a><br>' .
                   '<a href="' . tep_href_link('bank_cl.php') . '" class="menuBoxContentLink">銀行営業日</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_CACHE) . '" class="menuBoxContentLink">' . BOX_TOOLS_CACHE . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_DEFINE_LANGUAGE) . '" class="menuBoxContentLink">' . BOX_TOOLS_DEFINE_LANGUAGE . '</a><br>' .
                   // '<a href="' . tep_href_link(FILENAME_FILE_MANAGER) . '" class="menuBoxContentLink">' . BOX_TOOLS_FILE_MANAGER . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_MAIL) . '" class="menuBoxContentLink">' . BOX_TOOLS_MAIL . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_NEWSLETTERS) . '" class="menuBoxContentLink">' . BOX_TOOLS_NEWSLETTER_MANAGER . '</a><br>' .
                   //'<a href="' . tep_href_link(FILENAME_SERVER_INFO) . '" class="menuBoxContentLink">' . BOX_TOOLS_SERVER_INFO . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_WHOS_ONLINE) . '" class="menuBoxContentLink">' . BOX_TOOLS_WHOS_ONLINE . '</a><br>' . 
                   '<a href="' . tep_href_link('referer.php') . '" class="menuBoxContentLink">アクセスランキング</a><br>' . 
                   '<a href="' . tep_href_link('keywords.php') . '" class="menuBoxContentLink">キーワードランキング</a><br>' . 
                   '<a href="' . tep_href_link('adsense.php') . '" class="menuBoxContentLink">Adsense Logs</a><br>' . 
                   '<a href="' . tep_href_link(FILENAME_COMPUTERS) . '" class="menuBoxContentLink">PC管理</a><br>' . 
                   
                   '<a href="' . tep_href_link(FILENAME_LATEST_NEWS) . '" class="menuBoxContentLink">' . BOX_TOOLS_LATEST_NEWS . '</a><br>' . 
                   //'<a href="' . tep_href_link(FILENAME_CONTENTS) . '" class="menuBoxContentLink">' . BOX_TOOLS_CONTENTS . '</a>');
                   '<a href="' . tep_href_link(FILENAME_CONTENTS) . '" class="menuBoxContentLink">' . BOX_TOOLS_CONTENTS . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_PRESENT) . '" class="menuBoxContentLink">' . BOX_TOOLS_PRESENT . '</a><br>' . 
                   '<a href="' . tep_href_link(FILENAME_FAQ) . '" class="menuBoxContentLink">' . BOX_TOOLS_FAQ . '</a>' . 
                     
                   '<hr size="1">' . 
                   '<a href="' . tep_href_link('mag_up.php', '', 'NONSSL') . '" class="menuBoxContentLink">メールマガジン一括登録</a><br>' . 
                   '<a href="' . tep_href_link('mag_dl.php', '', 'NONSSL') . '" class="menuBoxContentLink">メールマガジンデータDL</a>');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- tools_eof //-->
