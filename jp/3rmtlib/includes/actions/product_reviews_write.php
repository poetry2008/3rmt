<?php
  $product_query = tep_db_query("
      select pd.products_name 
      from " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
      where p.products_id  = '" . (int)$_GET['products_id'] . "' 
        and pd.products_id = p.products_id 
        and pd.language_id = '" . $languages_id . "' 
        and (pd.site_id = '0' or pd.site_id = '".SITE_ID."')
      order by pd.site_id DESC
    ");
  $valid_product = (tep_db_num_rows($product_query) > 0);
  //forward 404
  forward404Unless($valid_product);
  if (isset($_GET['action']) && $_GET['action'] == 'process') {
    $form_error = false;
    if ($valid_product == true) { 
      // We got to the process but it is an illegal product, don't write
      $customer = tep_db_query("
          SELECT customers_firstname, 
                 customers_lastname 
          FROM " . TABLE_CUSTOMERS . " 
          WHERE customers_id = '" . $customer_id . "' 
            AND site_id      = '".SITE_ID."'
      ");
      $customer_values = tep_db_fetch_array($customer);
      $date_now = date('Ymd');
    
    $ban_list_array = explode(',', REVIEWS_BAN_CHARACTER);
    if (!empty($ban_list_array)) {
      foreach($ban_list_array as $b_key => $b_value) {
        $check_name_pos = strpos($_POST['reviews_name'], $b_value); 
        $check_content_pos = strpos($_POST['review'], $b_value); 
        if (($check_name_pos !== false) || ($check_content_pos !== false)) {
          $form_error = true;
          $error_message .= JS_REVIEW_BAN_CHARACTER;
          break;
        }
      }
    }
    if($_POST['reviews_name'] && tep_not_null($_POST['reviews_name'])) {
      //评论的名字是否为空 
      $reviews_name = $_POST['reviews_name'];
    } else {
      require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_REVIEWS_WRITE);
      $reviews_name = REVIEWS_NO_NAMES;
    }
    if (mb_strlen($_POST['review'], 'UTF-8') < REVIEW_TEXT_MIN_LENGTH) {
      //评论的内容是否小于指定字符 
      $form_error = true;
      $error_message .= JS_REVIEW_TEXT;
    }
    if (!in_array($_POST['rating'], array('1','2','3','4','5'))) {
      //是否选择评论等级 
      $form_error = true;
      $error_message .= JS_REVIEW_RATING;
    }
    if (tep_db_num_rows(tep_db_query("
      select * 
      from ".TABLE_REVIEWS." 
      where reviews_ip = '".$_SERVER['REMOTE_ADDR']."' 
        and date_added>'".date('Y-m-d H:i:s', time()-86400)."'
        and site_id = '" . SITE_ID . "'
      ")) > REVIEWS_DAY_LIMIT) {
      $form_error = true;
      $error_message .= '※ 今日あまりにも多いコメントを送りました。明日送ってください。\n';
    }
    $last_reviews_query = tep_db_query("
      select * from ".TABLE_REVIEWS." where reviews_ip = '".$_SERVER['REMOTE_ADDR']."' order by date_added DESC
    ");
    $last_reviews = tep_db_fetch_array($last_reviews_query);
    if ($last_reviews && time() - strtotime($last_reviews['date_added']) < REVIEWS_TIME_LIMIT) {
      $form_error = true;
      $error_message .= '※ 投稿が制限されています。時間をおいてお試しください。\n';
    }
    if ($form_error === false) {
      tep_db_query("
          INSERT INTO " . TABLE_REVIEWS . " (
            products_id, 
            customers_id, 
            customers_name, 
            reviews_rating, 
            date_added, 
            last_modified,
            reviews_status,
            reviews_ip,
            site_id,
            user_added,
            user_update
          ) values (
            '" . $_GET['products_id'] . "', 
            '" . $customer_id . "', 
            '" . addslashes($reviews_name) . "', 
            '" . $_POST['rating'] . "',
            now(), 
            now(),
            '0', 
            '".$_SERVER['REMOTE_ADDR']."',
            '".SITE_ID."',
            '".$_SESSION['customer_last_name'].$_SESSION['customer_first_name']."',
            '".$_SESSION['customer_last_name'].$_SESSION['customer_first_name']."'
          )");
        $insert_id = tep_db_insert_id();
        tep_db_query("
            insert into " . TABLE_REVIEWS_DESCRIPTION . " (
              reviews_id, 
              languages_id, 
              reviews_text
            ) values (
              '" . $insert_id . "', 
              '" . $languages_id . "', 
              '" . mysql_real_escape_string($_POST['review']) . "'
            )
        ");
      }
    }
    if ($form_error === false) {
    tep_redirect(tep_href_link(FILENAME_PRODUCT_INFO, $_POST['get_params']));
    }
  }

// lets retrieve all $_GET keys and values..
  $get_params      = tep_get_all_get_params(array('action'));
  $get_params_back = tep_get_all_get_params(array('reviews_id', 'action')); // for back button
  $get_params      = substr($get_params, 0, -1); //remove trailing &
  if (tep_not_null($get_params_back)) {
    $get_params_back = substr($get_params_back, 0, -1); //remove trailing &
  } else {
    $get_params_back = $get_params;
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_REVIEWS_WRITE);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_PRODUCT_REVIEWS, $get_params));
  $customer_info_query = tep_db_query("
      select customers_firstname, 
             customers_lastname 
      from " . TABLE_CUSTOMERS . " 
      where customers_id = '" .  $customer_id . "' 
        and site_id = ".SITE_ID
  );
  $customer_info = tep_db_fetch_array($customer_info_query);
