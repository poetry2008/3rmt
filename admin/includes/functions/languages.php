<?php
/*
  $Id: languages.php,v 1.1.1.1 2003/02/20 01:03:53 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  function tep_get_languages_directory($code) {
    global $languages_id;

    $language_query = tep_db_query("select languages_id, directory from " . TABLE_LANGUAGES . " where code = '" . $code . "'");
    if (tep_db_num_rows($language_query)) {
      $language = tep_db_fetch_array($language_query);
      $languages_id = $language['languages_id'];
      return $language['directory'];
    } else {
      return false;
    }
  }
?>