<?php
/*
  $Id: tools.php,v 1.1.1.1 2003/02/20 01:03:52 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
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
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_BACKUP) . '" class="menuBoxContentLink">' . BOX_TOOLS_BACKUP . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_BANNER_MANAGER) . '" class="menuBoxContentLink">' . BOX_TOOLS_BANNER_MANAGER . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_CL) . '" class="menuBoxContentLink">' . BOX_TOOLS_CL . '</a><br>' .
								   '<a href="' . tep_href_link(FILENAME_CACHE) . '" class="menuBoxContentLink">' . BOX_TOOLS_CACHE . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_DEFINE_LANGUAGE) . '" class="menuBoxContentLink">' . BOX_TOOLS_DEFINE_LANGUAGE . '</a><br>' .
                                  // '<a href="' . tep_href_link(FILENAME_FILE_MANAGER) . '" class="menuBoxContentLink">' . BOX_TOOLS_FILE_MANAGER . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_MAIL) . '" class="menuBoxContentLink">' . BOX_TOOLS_MAIL . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_NEWSLETTERS) . '" class="menuBoxContentLink">' . BOX_TOOLS_NEWSLETTER_MANAGER . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_SERVER_INFO) . '" class="menuBoxContentLink">' . BOX_TOOLS_SERVER_INFO . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_WHOS_ONLINE) . '" class="menuBoxContentLink">' . BOX_TOOLS_WHOS_ONLINE . '</a><br>' . 
								   '<a href="' . tep_href_link(FILENAME_LATEST_NEWS) . '" class="menuBoxContentLink">' . BOX_TOOLS_LATEST_NEWS . '</a><br>' . 
								   //'<a href="' . tep_href_link(FILENAME_CONTENTS) . '" class="menuBoxContentLink">' . BOX_TOOLS_CONTENTS . '</a>');
								   '<a href="' . tep_href_link(FILENAME_CONTENTS) . '" class="menuBoxContentLink">' . BOX_TOOLS_CONTENTS . '</a><br>' .
								   '<a href="' . tep_href_link(FILENAME_PRESENT) . '" class="menuBoxContentLink">' . BOX_TOOLS_PRESENT . '</a>' . 
								   '<hr size="1">' . 
								   '<a href="' . tep_href_link('mag_up.php', '', 'NONSSL') . '" class="menuBoxContentLink">�᡼��ޥ���������Ͽ</a><br>' . 
								   '<a href="' . tep_href_link('mag_dl.php', '', 'NONSSL') . '" class="menuBoxContentLink">�᡼��ޥ�����ǡ���DL</a>');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- tools_eof //-->
