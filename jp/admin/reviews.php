<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
      case 'new_preview':
        $sql_array = array(
          'reviews_id' => 'null',
          'products_id' => $_POST['products_id'],
          'customers_id' => '0',
          'customers_name' => $_POST['customers_name'],
          'reviews_rating' => $_POST['reviews_rating'],
          'date_added' => $_POST['year'].'-'.$_POST['m'].'-'.$_POST['d'].' '.$_POST['h'].':'.$_POST['i'].':'.$_POST['s'],
          'last_modified' => '',
          'reviews_read' => '0',
          'site_id' => $_POST['site_id'],
          'reviews_status' => $_POST['reviews_status'],
        );
        tep_db_perform(TABLE_REVIEWS, $sql_array);
        $insert_id = tep_db_insert_id();
        $sql_description_array = array(
          'reviews_id' => $insert_id,
          'languages_id' => '4',
          'reviews_text' => $_POST['reviews_text']
        );
        tep_db_perform(TABLE_REVIEWS_DESCRIPTION, $sql_description_array);
        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath='.$_POST['cPath']));
        break;
      case 'setflag':
        $site_id = isset($_GET['site_id']) ? $_GET['site_id'] :0;
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if ($_GET['pID']) {
            $pID = (int)$_GET['pID'];
            $flag = (int)$_GET['flag'];
            tep_db_query("UPDATE ".TABLE_REVIEWS." SET reviews_status = '".$flag."' WHERE reviews_id = '".$pID."'");
          }
        }
        tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'].'&site_id='.$site_id));
        break;
      case 'update':
        $reviews_id     = tep_db_prepare_input($_GET['rID']);
        $reviews_rating = tep_db_prepare_input($_POST['reviews_rating']);
        $last_modified  = tep_db_prepare_input($_POST['last_modified']);
        $reviews_text   = tep_db_prepare_input($_POST['reviews_text']);
        $reviews_status = tep_db_prepare_input($_POST['reviews_status']);

        tep_db_query("
            update " . TABLE_REVIEWS . " 
            set reviews_rating = '" . tep_db_input($reviews_rating) . "', 
                last_modified = now(), 
                reviews_status = '".$reviews_status."' 
            where reviews_id = '" . tep_db_input($reviews_id) . "'");
        
        tep_db_query("
            update " . TABLE_REVIEWS_DESCRIPTION . " 
            set reviews_text = '" . tep_db_input($reviews_text) . "' 
            where reviews_id = '" . tep_db_input($reviews_id) . "'");

        tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews_id.(isset($_GET['lsite_id'])?('&site_id='.$_GET['lsite_id']):'')));
        break;
      case 'deleteconfirm':
        $reviews_id = tep_db_prepare_input($_GET['rID']);

        tep_db_query("
            delete from " . TABLE_REVIEWS . " 
            where reviews_id = '" . tep_db_input($reviews_id) . "'");
        tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " 
            where reviews_id = '" . tep_db_input($reviews_id) . "'");

        tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')));
        break;
    }
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<script>
    function word_count(ele)
    {
      document.getElementById('count_box').innerHTML = ele.value.length;
    }
    function check_review()
    {
        if (document.getElementById('reviews_text').value.length < 50) {
            alert("レビューの文章は少なくても 50 文字以上必要です");
            return false
        } else {
            return true;
        }
    }
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();word_count(document.getElementById('reviews_text'))">
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
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
<?php
  if (isset($_GET['action']) && $_GET['action'] == 'new') {
    if (!isset($_GET['products_id'])) {
      tep_redirect('reviews.php');
    }
    $products_query = tep_db_query("
        select products_image 
        from " . TABLE_PRODUCTS . " 
        where products_id = '" . $_GET['products_id'] . "'");
    $products = tep_db_fetch_array($products_query);

    $products_name_query = tep_db_query("
        select *
        from " . TABLE_PRODUCTS_DESCRIPTION . " 
        where products_id = '" . $_GET['products_id'] . "' 
          and site_id = '0'
          and language_id = '" . $languages_id . "'");
    $products_name = tep_db_fetch_array($products_name_query);

    $rInfo_array = tep_array_merge($products, $products_name);
    $rInfo = new objectInfo($rInfo_array);
?>
      <tr><?php echo tep_draw_form('review', FILENAME_REVIEWS, 'action=new_preview', 'post', 'onsubmit="return check_review()"'); ?>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main" valign="top">
      <b><?php echo ENTRY_SITE; ?>:</b> <?php echo tep_site_pull_down_menu();?><br>
      <b><?php echo ENTRY_PRODUCT; ?></b> <?php echo $rInfo->products_name; ?><br>
      <b><?php echo ENTRY_FROM; ?></b> <input type="text" name="customers_name" value="" /> <br>
      <b><?php echo ENTRY_DATE; ?></b> 
  <select name='year'>
  <?php for ($i=0;$i<10;$i++) {?>
    <option value="<?php echo date('Y')-$i;?>" <?php $i==intval(date('Y')) && print('selected');?>><?php echo date('Y')-$i;?></option>
  <?php }?>
  </select>/<select name='m'>
  <?php for ($i=1;$i<13;$i++) {?>
    <option value="<?php echo $i;?>" <?php $i==intval(date('m')) && print('selected');?>><?php echo $i;?></option>
  <?php }?>
  </select>/<select name='d'>
  <?php for ($i=1;$i<31;$i++) {?>
    <option value="<?php echo $i;?>" <?php $i==intval(date('d')) && print('selected');?>><?php echo $i;?></option>
  <?php }?>
  </select> <select name='h'>
  <?php for ($i=0;$i<24;$i++) {?>
    <option value="<?php echo $i;?>" <?php $i==intval(date('H')) && print('selected');?>><?php printf('%02d', $i);?></option>
  <?php }?>
  </select>:<select name='i'>
  <?php for ($i=0;$i<60;$i++) {?>
    <option value="<?php echo $i;?>" <?php $i==intval(date('i')) && print('selected');?>><?php printf('%02d', $i);?></option>
  <?php }?>
  </select>:<select name='s'>
  <?php for ($i=0;$i<60;$i++) {?>
    <option value="<?php echo $i;?>" <?php $i==intval(date('s')) && print('selected');?>><?php printf('%02d', $i);?></option>
  <?php }?>
  </select>
  <br>
      <b><?php echo TEXT_PRODUCTS_STATUS; ?></b> <?php echo tep_draw_radio_field('reviews_status', '1') . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '&nbsp;' . tep_draw_radio_field('reviews_status', '0', 1) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE; ?>
      </td>
            <td class="main" align="right" valign="top"><?php //echo tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . $rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table witdh="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main" valign="top" colspan='2'><b><?php echo ENTRY_REVIEW; ?></b><br><br><?php echo tep_draw_textarea_field('reviews_text', 'soft', '60', '15', '', 'id="reviews_text" onkeypress="word_count(this)" onchange="word_count(this)"'); ?></td>
          </tr>
          <tr>
            <td class="smallText">漢字数:<span id="count_box"></span></td>
            <td class="smallText" align="right"><?php echo ENTRY_REVIEW_TEXT; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo ENTRY_RATING; ?></b>&nbsp;<?php echo TEXT_BAD; ?>&nbsp;<?php for ($i=1; $i<=5; $i++) echo tep_draw_radio_field('reviews_rating', $i, '', $i == 5) . '&nbsp;'; echo TEXT_GOOD; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td align="right" class="main"><?php echo tep_draw_hidden_field('cPath', $_GET['cPath']) . tep_draw_hidden_field('products_id', $rInfo->products_id) . tep_image_submit('button_save.gif', IMAGE_save) . ' <a href="' . tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
      </form></tr>
<?php

  } elseif (isset($_GET['action']) && $_GET['action'] == 'edit') {
    $rID = tep_db_prepare_input($_GET['rID']);

    $reviews_query = tep_db_query("
        select r.reviews_id, 
               r.products_id, 
               r.customers_name, 
               r.date_added, 
               r.last_modified, 
               r.reviews_read, 
               rd.reviews_text, 
               r.reviews_rating, 
               r.reviews_status,
               s.romaji,
               s.name as site_name,
               r.site_id
        from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd , ".TABLE_SITES." s
        where r.reviews_id = '" . tep_db_input($rID) . "' 
          and s.id = r.site_id
          and r.reviews_id = rd.reviews_id");
    $reviews = tep_db_fetch_array($reviews_query);
    $products_query = tep_db_query("
        select products_image 
        from " . TABLE_PRODUCTS . " 
        where products_id = '" . $reviews['products_id'] . "'");
    $products = tep_db_fetch_array($products_query);

    $products_name_query = tep_db_query("
        select *
        from " . TABLE_PRODUCTS_DESCRIPTION . " 
        where products_id = '" . $reviews['products_id'] . "' 
          and site_id = '0'
          and language_id = '" . $languages_id . "'");
    $products_name = tep_db_fetch_array($products_name_query);

    $rInfo_array = tep_array_merge($reviews, $products, $products_name);
    $rInfo = new objectInfo($rInfo_array);
  
    switch ($rInfo->reviews_status) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
    }
?>
      <tr><?php echo tep_draw_form('review', FILENAME_REVIEWS, 'page=' . $_GET['page'] . (isset($_GET['lsite_id'])?('&lsite_id='.$_GET['lsite_id']):'').'&rID=' . $_GET['rID'] . '&action=preview'); ?>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main" valign="top">
      <b><?php echo ENTRY_SITE; ?>:</b> <?php echo $reviews['site_name']; ?><br>
      <b><?php echo ENTRY_PRODUCT; ?></b> <?php echo $rInfo->products_name; ?><br>
      <b><?php echo ENTRY_FROM; ?></b> <?php echo tep_output_string_protected($rInfo->customers_name); ?><br>
      <b><?php echo ENTRY_DATE; ?></b> <?php echo tep_date_short($rInfo->date_added); ?><br>
      <b><?php echo TEXT_PRODUCTS_STATUS; ?></b> <?php echo tep_draw_radio_field('reviews_status', '1', $in_status) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '&nbsp;' . tep_draw_radio_field('reviews_status', '0', $out_status) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE; ?>

      </td>
            <td class="main" align="right" valign="top"><?php echo tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . $rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table witdh="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main" valign="top" colspan='2'><b><?php echo ENTRY_REVIEW; ?></b><br><br><?php echo tep_draw_textarea_field('reviews_text', 'soft', '60', '15', $rInfo->reviews_text, 'id="reviews_text" onkeypress="word_count(this)" onchange="word_count(this)"'); ?></td>
          </tr>
          <tr>
            <td class="smallText">漢字数:<span id="count_box"></span></td>
            <td class="smallText" align="right"><?php echo ENTRY_REVIEW_TEXT; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo ENTRY_RATING; ?></b>&nbsp;<?php echo TEXT_BAD; ?>&nbsp;<?php for ($i=1; $i<=5; $i++) echo tep_draw_radio_field('reviews_rating', $i, '', $rInfo->reviews_rating) . '&nbsp;'; echo TEXT_GOOD; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td align="right" class="main"><?php echo tep_draw_hidden_field('reviews_id', $rInfo->reviews_id) . tep_draw_hidden_field('products_id', $rInfo->products_id) . tep_draw_hidden_field('customers_name', tep_output_string_protected($rInfo->customers_name)) . tep_draw_hidden_field('products_name', $rInfo->products_name) . tep_draw_hidden_field('products_image', $rInfo->products_image) . tep_draw_hidden_field('date_added', $rInfo->date_added) . tep_image_submit('button_preview.gif', IMAGE_PREVIEW) . ' <a href="' . tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
      </form></tr>
<?php
  } elseif (isset($_GET['action']) && $_GET['action'] == 'preview') {
    if ($_POST) {
      $rInfo = new objectInfo($_POST);
    } else {
      $reviews_query = tep_db_query("
          select r.reviews_id, 
                 r.products_id, 
                 r.customers_name, 
                 r.date_added, 
                 r.last_modified, 
                 r.reviews_read, 
                 rd.reviews_text, 
                 r.reviews_rating ,
                 r.site_id
          from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd 
          where r.reviews_id = '" . $_GET['rID'] . "' 
            and r.reviews_id = rd.reviews_id");
      $reviews = tep_db_fetch_array($reviews_query);
      $products_query = tep_db_query("
          select products_image 
          from " . TABLE_PRODUCTS . " 
          where products_id = '" . $reviews['products_id'] . "'
      ");
      $products = tep_db_fetch_array($products_query);

      $products_name_query = tep_db_query("
          select products_name 
          from " . TABLE_PRODUCTS_DESCRIPTION . " 
          where products_id = '" . $reviews['products_id'] . "' 
            and site_id = '0'
            and language_id = '" . $languages_id . "'
      ");
      $products_name = tep_db_fetch_array($products_name_query);

      $rInfo_array = tep_array_merge($reviews, $products, $products_name);
      $rInfo = new objectInfo($rInfo_array);
    }
?>
      <tr><?php echo tep_draw_form('update', FILENAME_REVIEWS, 'page=' . $_GET['page'] . (isset($_GET['lsite_id'])?('&lsite_id='.$_GET['lsite_id']):'').'&rID=' . $_GET['rID'] . '&action=update', 'post', 'enctype="multipart/form-data"'); ?>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main" valign="top"><b><?php echo ENTRY_PRODUCT; ?></b> <?php echo $rInfo->products_name; ?><br><b><?php echo ENTRY_FROM; ?></b> <?php echo tep_output_string_protected($rInfo->customers_name); ?><br><br><b><?php echo ENTRY_DATE; ?></b> <?php echo tep_date_short($rInfo->date_added); ?></td>
            <td class="main" align="right" valign="top"><?php echo tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . $rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?></td>
          </tr>
        </table>
      </tr>
      <tr>
        <td><table witdh="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top" class="main"><b><?php echo ENTRY_REVIEW; ?></b><br><br><?php echo nl2br(tep_db_output(tep_break_string($rInfo->reviews_text, 15))); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo ENTRY_RATING; ?></b>&nbsp;<?php echo tep_image(DIR_WS_CATALOG_IMAGES . 'stars_' . $rInfo->reviews_rating . '.gif', sprintf(TEXT_OF_5_STARS, $rInfo->reviews_rating)); ?>&nbsp;<small>[<?php echo sprintf(TEXT_OF_5_STARS, $rInfo->reviews_rating); ?>]</small></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
<?php
    if ($_POST) {
/* Re-Post all POST'ed variables */
      reset($_POST);
      while(list($key, $value) = each($_POST)) echo '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars(stripslashes($value)) . '">';
?>
      <tr>
        <td align="right" class="smallText"><?php echo '<a href="' . tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=edit') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a> ' . tep_image_submit('button_update.gif', IMAGE_UPDATE) . ' <a href="' . tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
      </form></tr>
<?php
    } else {
      if (isset($_GET['origin']) && $_GET['origin']) {
        $back_url = $_GET['origin'];
        $back_url_params = '';
      } else {
        $back_url = FILENAME_REVIEWS;
        $back_url_params = 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id;
      }
?>
      <tr>
        <td align="right"><?php echo '<a href="' . tep_href_link($back_url, $back_url_params, 'NONSSL') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
      </tr>
<?php
    }
  } else {
?>
      <tr>
        <td>
        <?php echo tep_site_filter(FILENAME_REVIEWS);?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SITE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_RATING; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $reviews_query_raw = "
      select r.reviews_id, 
             r.products_id, 
             r.date_added, 
             r.last_modified, 
             r.reviews_rating, 
             r.reviews_status ,
             s.romaji,
             s.name as site_name,
             pd.products_name
     from " . TABLE_REVIEWS . " r, ".TABLE_SITES." s, ".TABLE_PRODUCTS_DESCRIPTION." pd
     where r.site_id = s.id
           and r.products_id = pd.products_id
           and pd.language_id = '".$languages_id."'
           and pd.site_id = 0
        " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and s.id = '" . intval($_GET['site_id']) . "' " : '') . "
     order by date_added DESC";
    $reviews_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $reviews_query_raw, $reviews_query_numrows);
    $reviews_query = tep_db_query($reviews_query_raw);
    while ($reviews = tep_db_fetch_array($reviews_query)) {
      if ( ((!isset($_GET['rID']) || !$_GET['rID']) || ($_GET['rID'] == $reviews['reviews_id'])) && (!isset($rInfo) || !$rInfo) ) {
        $reviews_text_query = tep_db_query("
            select r.reviews_read, 
                   r.customers_name, 
                   r.site_id,
                   length(rd.reviews_text) as reviews_text_size 
            from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd 
            where r.reviews_id = '" . $reviews['reviews_id'] . "' 
              and r.reviews_id = rd.reviews_id");
        $reviews_text = tep_db_fetch_array($reviews_text_query);

        $products_image_query = tep_db_query("
            select products_image 
            from " . TABLE_PRODUCTS . " 
            where products_id = '" . $reviews['products_id'] . "'
        ");
        $products_image = tep_db_fetch_array($products_image_query);

        $products_name_query = tep_db_query("
            select products_name 
            from " . TABLE_PRODUCTS_DESCRIPTION . " 
            where products_id = '" . $reviews['products_id'] . "' 
              and site_id ='0'
              and language_id = '" . $languages_id . "'");
        $products_name = tep_db_fetch_array($products_name_query);

        $reviews_average_query = tep_db_query("
            select (avg(reviews_rating) / 5 * 100) as average_rating 
            from " . TABLE_REVIEWS . " 
            where products_id = '" . $reviews['products_id'] . "'
          ");
        $reviews_average = tep_db_fetch_array($reviews_average_query);

        $review_info = tep_array_merge($reviews_text, $reviews_average, $products_name);
        $rInfo_array = tep_array_merge($reviews, $review_info, $products_image);
        $rInfo = new objectInfo($rInfo_array);
      }

      if ( isset($rInfo) && (is_object($rInfo)) && ($reviews['reviews_id'] == $rInfo->reviews_id) ) {
        echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&site_id=' . (isset($_GET['site_id'])?$_GET['site_id']:''). '&action=preview') . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id']. '&site_id=' . (isset($_GET['site_id'])?$_GET['site_id']:'')) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo $reviews['site_name'];?></td>
                <td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id'] . '&action=preview') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $reviews['products_name']; ?></td>
        <td class="dataTableContent" align="center">
<?php
      if ($reviews['reviews_status'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_REVIEWS, 'action=setflag&flag=0'.(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').'&page=' . (isset($_GET['page'])?$_GET['page']:'') . '&pID=' . $reviews['reviews_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_REVIEWS, 'action=setflag&flag=1'.(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').'&page=' . (isset($_GET['page'])?$_GET['page']:'') . '&pID=' . $reviews['reviews_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?>
                </td>
        <td class="dataTableContent" align="right"><?php echo tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif'); ?></td>
                <td class="dataTableContent" align="right"><?php echo tep_date_short($reviews['date_added']) . ' ' .date('H:i:s', strtotime($reviews['date_added'])); ?></td>
                <td class="dataTableContent" align="right"><?php if ( (isset($rInfo) && is_object($rInfo)) && ($reviews['reviews_id'] == $rInfo->reviews_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $reviews_split->display_count($reviews_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
                    <td class="smallText" align="right"><?php echo $reviews_split->display_links($reviews_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page'))); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
    $heading = array();
    $contents = array();
    switch (isset($_GET['action'])?$_GET['action']:'') {
      case 'delete':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_REVIEW . '</b>');

        $contents = array('form' => tep_draw_form('reviews', FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=deleteconfirm'.(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')));
        $contents[] = array('text' => TEXT_INFO_DELETE_REVIEW_INTRO);
        $contents[] = array('text' => '<br><b>' . $rInfo->products_name . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id) . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'') . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      default:
      if (isset($rInfo) && is_object($rInfo)) {
        $heading[] = array('text' => '<b>' . $rInfo->products_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => 
          '<a href="' . tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=edit' . (isset($_GET['site_id'])?('&lsite_id='.$_GET['site_id']):'')) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>' 
        . ($ocertify->npermission == 15 ? (' <a href="' . tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=delete' . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>'):'')
        );
        $contents[] = array('text' => '<br>' . TEXT_INFO_DATE_ADDED . ' ' . tep_date_short($rInfo->date_added));
        if (tep_not_null($rInfo->last_modified)) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . tep_date_short($rInfo->last_modified));
        $contents[] = array('text' => '<br>' . tep_info_image('products/'.$rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, $rInfo->site_id));
        $contents[] = array('text' => '<br>' . TEXT_INFO_REVIEW_AUTHOR . ' ' . tep_output_string_protected($rInfo->customers_name));
        $contents[] = array('text' => TEXT_INFO_REVIEW_RATING . ' ' . tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . 'stars_' . $rInfo->reviews_rating . '.gif'));
        $contents[] = array('text' => TEXT_INFO_REVIEW_READ . ' ' . $rInfo->reviews_read);
        $contents[] = array('text' => '<br>' . TEXT_INFO_REVIEW_SIZE . ' ' . $rInfo->reviews_text_size . ' bytes');
        $contents[] = array('text' => '<br>' . TEXT_INFO_PRODUCTS_AVERAGE_RATING . ' ' . number_format($rInfo->average_rating, 2) . '%');
      }
        break;
    }

    if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
      echo '            <td width="25%" valign="top">' . "\n";

      $box = new box;
      echo $box->infoBox($heading, $contents);

      echo '            </td>' . "\n";
    }
?>
          </tr>
        </table></td>
      </tr>
<?php
  }
?>
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
