<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  $site_id = isset($_GET['site_id'])?$_GET['site_id']:1;
  $dir_fs_cache = get_configuration_by_site_id('DIR_FS_CACHE', $site_id);


  if (isset($_GET['action']) && $_GET['action']) {
    if ($_GET['action'] == 'reset') {
      tep_reset_cache_block($_GET['block']);
    }
if(isset($_GET['action']) && $_GET['action']=="update_css_rand"){
$css_rand_query = tep_db_query("select value from other_config where keyword='css_random_string'");
$css_rand_array = tep_db_fetch_array($css_rand_query);
$rand_num = $css_rand_array['value'];
$rand_num = (int)$rand_num+1;
$rand_num = (string)$rand_num;
while(strlen($rand_num)<4){
$rand_num ="0".$rand_num;
}
if($rand_num=="9999"){
$sql = "update other_config set value='0000' where keyword='css_random_string'";
tep_db_query($sql);
}else{
$sql = "update other_config set value='".$rand_num."' where keyword='css_random_string'";
tep_db_query($sql);
}
}
    tep_redirect(tep_href_link(FILENAME_CACHE));
  }

// check if the cache directory exists
  if (is_dir($dir_fs_cache)) {
    if (!is_writeable($dir_fs_cache)) {
      @chmod($dir_fs_cache, 0777);
      if (!is_writeable($dir_fs_cache)) {
        $messageStack->add(ERROR_CACHE_DIRECTORY_NOT_WRITEABLE, 'error');
      }
    }
  } else {
    @mkdir($dir_fs_cache, 0777); 
    @chmod($dir_fs_cache, 0777);
    if (!is_dir($dir_fs_cache)){
      $messageStack->add(ERROR_CACHE_DIRECTORY_DOES_NOT_EXIST, 'error');
    }
  }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
    
              <div id="tep_site_filter">
                  <?php foreach(tep_get_sites() as $k => $s) {?>
                        <span <?php if ($site_id == $s['id']) {?>class="site_filter_selected"<?php }?>><a href="cache.php?site_id=<?php echo $s['id'];?>"><?php echo $s['romaji'];?></a></span>
                  <?php }?>
              </div>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CACHE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_DATE_CREATED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  if ($messageStack->size < 1) {
    $languages = tep_get_languages();
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      if ($languages[$i]['code'] == DEFAULT_LANGUAGE) {
        $language = $languages[$i]['directory'];
      }
    }
    for ($i = 0, $n = sizeof($cache_blocks); $i < $n; $i++) {
      $cached_file = ereg_replace('-language', '-' . $language, $cache_blocks[$i]['file']);
      if (file_exists($dir_fs_cache . $cached_file)) {
        $cache_mtime = strftime(DATE_TIME_FORMAT, filemtime($dir_fs_cache . $cached_file));
      } else {
        $cache_mtime = TEXT_FILE_DOES_NOT_EXIST;
        $dir = dir($dir_fs_cache);
        while ($cache_file = $dir->read()) {
          $cached_file = ereg_replace('-language', '-' . $language, $cache_blocks[$i]['file']);
          if (ereg('^' . $cached_file, $cache_file)) {
            $cache_mtime = strftime(DATE_TIME_FORMAT, filemtime($dir_fs_cache . $cache_file));
            break;
          }
        }
        $dir->close();
      }
      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
?>
              <tr class="<?php echo $nowColor;?>" onmouseover="this.className='dataTableRowOver'" onmouseout="this.className='<?php echo $nowColor;?>'">
                <td class="dataTableContent"><?php echo $cache_blocks[$i]['title']; ?></td>
                <td class="dataTableContent" align="right"><?php echo $cache_mtime; ?></td>
                <td class="dataTableContent" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_CACHE, 'action=reset&block=' . $cache_blocks[$i]['code'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . 'icon_reset.gif', 'Reset', 13, 13) . '</a>'; ?>&nbsp;</td>
              </tr>
<?php
    }
  }
?>
              <tr>
                <td class="smallText" colspan="3"><?php echo TEXT_CACHE_DIRECTORY . ' ' . $dir_fs_cache; ?></td>
              </tr>
<?php 
$css_rand_query = tep_db_query("select id,value from other_config where keyword='css_random_string'");

$css_rand_array = tep_db_fetch_array($css_rand_query)
	?>
<tr>
<form name="" method="post" action="cache.php?action=update_css_rand">
<td class="smallText" colspan="3" align="right">
<?php echo CSS_RANDOM_STRING;?>
<?php echo $css_rand_array['value']; ?>
&nbsp;&nbsp;
<?php echo CSS_RANDOM_INFO; ?>
<input type="submit" value="<?php echo CSS_UPDATE_BUTTON?>">
</td>
</form>
</tr>
<tr>
<td class="smallText" colspan="3" align="right">
<?php echo CSS_EXAMPLE."(".NOW_RANDOM_VALUE."jp.css?v=".$css_rand_array['value'].")"?>
</td>
</tr>

            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
