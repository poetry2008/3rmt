<?php
/*
  $Id$
*/
?>
<!-- tools //-->
          <tr>
            <td>
<?php
  /* 
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_TOOLS,
                     'link'  => tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=tools'));

  if ($selected_box == 'tools') {
    $contents[] = array('text'  =>
                   //'<a href="' . tep_href_link(FILENAME_BACKUP) . '" class="menuBoxContent_Link">' . BOX_TOOLS_BACKUP . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_BANNER_MANAGER) . '" class="menuBoxContent_Link">' . BOX_TOOLS_BANNER_MANAGER . '</a><br>' .
                   //'<a href="' . tep_href_link(FILENAME_CL) . '" class="menuBoxContent_Link">' . BOX_TOOLS_CL . '</a><br>' .
                   '<a href="' . tep_href_link('bank_cl.php') . '" class="menuBoxContent_Link">銀行営業日</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_CACHE) . '" class="menuBoxContent_Link">' . BOX_TOOLS_CACHE . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_PW_MANAGER) . '"
                   class="menuBoxContent_Link">' . 'パスワード管理' . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_DEFINE_LANGUAGE) . '" class="menuBoxContent_Link">' . BOX_TOOLS_DEFINE_LANGUAGE . '</a><br>' .
                   // '<a href="' . tep_href_link(FILENAME_FILE_MANAGER) . '" class="menuBoxContent_Link">' . BOX_TOOLS_FILE_MANAGER . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_MAIL) . '" class="menuBoxContent_Link">' . BOX_TOOLS_MAIL . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_NEWSLETTERS) . '" class="menuBoxContent_Link">' . BOX_TOOLS_NEWSLETTER_MANAGER . '</a><br>' .
                   //'<a href="' . tep_href_link(FILENAME_SERVER_INFO) . '" class="menuBoxContent_Link">' . BOX_TOOLS_SERVER_INFO . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_WHOS_ONLINE) . '" class="menuBoxContent_Link">' . BOX_TOOLS_WHOS_ONLINE . '</a><br>' . 
                   '<a href="' . tep_href_link(FILENAME_COMPUTERS) . '" class="menuBoxContent_Link">PC管理</a><br>' . 
                   
                   '<a href="' . tep_href_link(FILENAME_LATEST_NEWS) . '" class="menuBoxContent_Link">' . BOX_TOOLS_LATEST_NEWS . '</a><br>' . 
                   //'<a href="' . tep_href_link(FILENAME_CONTENTS) . '" class="menuBoxContent_Link">' . BOX_TOOLS_CONTENTS . '</a>');
                   '<a href="' . tep_href_link(FILENAME_CONTENTS) . '" class="menuBoxContent_Link">' . BOX_TOOLS_CONTENTS . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_PRESENT) . '" class="menuBoxContent_Link">' . BOX_TOOLS_PRESENT . '</a><br>' . 
                   '<a href="' . tep_href_link(FILENAME_FAQ) . '" class="menuBoxContent_Link">' . BOX_TOOLS_FAQ . '</a>' . 
                     
                   '<hr size="1">' . 
                   '<a href="' . tep_href_link('mag_up.php', '', 'NONSSL') . '" class="menuBoxContent_Link">メールマガジン一括登録</a><br>' . 
                   '<a href="' . tep_href_link('mag_dl.php', '', 'NONSSL') . '" class="menuBoxContent_Link">メールマガジンデータDL</a>');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
  */
?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
              <td onmouseover="this.style.cursor='hand'" class="menuBoxHeading">
              &nbsp; 
              <a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col7');"><?php echo BOX_HEADING_TOOLS;?></a> 
              &nbsp; 
              </td>
              </tr>
            </table> 
            <div id="col7" style="display:none"> 
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
    <?php 
    echo 
                   '<a href="' . tep_href_link(FILENAME_BANNER_MANAGER) . '" class="menuBoxContent_Link">' . BOX_TOOLS_BANNER_MANAGER . '</a><br>' .
                   '<a href="' . tep_href_link('bank_cl.php') . '" class="menuBoxContent_Link">銀行営業日</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_CACHE) . '" class="menuBoxContent_Link">' . BOX_TOOLS_CACHE . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_DEFINE_LANGUAGE) . '" class="menuBoxContent_Link">' . BOX_TOOLS_DEFINE_LANGUAGE . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_MAIL) . '" class="menuBoxContent_Link">' . BOX_TOOLS_MAIL . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_NEWSLETTERS) . '" class="menuBoxContent_Link">' . BOX_TOOLS_NEWSLETTER_MANAGER . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_WHOS_ONLINE) . '" class="menuBoxContent_Link">' . BOX_TOOLS_WHOS_ONLINE . '</a><br>' . 
                   '<a href="' . tep_href_link(FILENAME_COMPUTERS) . '" class="menuBoxContent_Link">PC管理</a><br>' . 
                   
                   '<a href="' . tep_href_link(FILENAME_LATEST_NEWS) . '" class="menuBoxContent_Link">' . BOX_TOOLS_LATEST_NEWS . '</a><br>' . 
                   '<a href="' . tep_href_link(FILENAME_CONTENTS) . '" class="menuBoxContent_Link">' . BOX_TOOLS_CONTENTS . '</a><br>' .
                   '<a href="' . tep_href_link(FILENAME_PRESENT) . '" class="menuBoxContent_Link">' . BOX_TOOLS_PRESENT . '</a><br>' . 
                   '<a href="' . tep_href_link(FILENAME_FAQ) . '" class="menuBoxContent_Link">' . BOX_TOOLS_FAQ . '</a>' . 
                     
                   '<hr size="1">' . 
                   '<a href="' . tep_href_link('mag_up.php', '', 'NONSSL') . '" class="menuBoxContent_Link">メールマガジン一括登録</a><br>' . 
                   '<a href="' . tep_href_link('mag_dl.php', '', 'NONSSL') . '" class="menuBoxContent_Link">メールマガジンデータDL</a>';
              ?> 
                </td>
              </tr>
            </table> 
            </div> 
            </td>
          </tr>
<!-- tools_eof //-->
