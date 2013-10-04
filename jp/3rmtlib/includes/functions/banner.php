<?php
/*
  $Id$
*/

/* -------------------------------------
    功能: 设置banner的状态 
    参数: $banners_id(int) banner id   
    参数: $status(string) 状态   
    返回值: 是否成功设置(resource/int) 
------------------------------------ */
  function tep_set_banner_status($banners_id, $status) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_BANNERS . " set status = '1', date_status_change = now(), date_scheduled = NULL where banners_id = '" .  $banners_id . "' and site_id = '".SITE_ID."'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_BANNERS . " set status = '0', date_status_change = now() where banners_id = '" . $banners_id . "' and site_id = '".SITE_ID."'");
    } else {
      return -1;
    }
  }

/* -------------------------------------
    功能: 激活banner的状态 
    参数: 无  
    返回值: 无 
------------------------------------ */
  function tep_activate_banners() {
    $banners_query = tep_db_query("select banners_id, date_scheduled from " .  TABLE_BANNERS . " where date_scheduled != '' and date_scheduled != null and site_id = ".SITE_ID);
    if (tep_db_num_rows($banners_query)) {
      while ($banners = tep_db_fetch_array($banners_query)) {
        if (date('Y-m-d H:i:s') >= $banners['date_scheduled']) {
          tep_set_banner_status($banners['banners_id'], '1');
        }
      }
    }
  }

/* -------------------------------------
    功能: 设置过期的banner 
    参数: 无  
    返回值: 无 
------------------------------------ */
  function tep_expire_banners() {
    $banners_query = tep_db_query("select b.banners_id, b.expires_date, b.expires_impressions, sum(bh.banners_shown) as banners_shown from " .  TABLE_BANNERS . " b, " . TABLE_BANNERS_HISTORY . " bh where b.status = '1' and b.banners_id = bh.banners_id and b.site_id = ".SITE_ID."group by b.banners_id");
    if (tep_db_num_rows($banners_query)) {
      while ($banners = tep_db_fetch_array($banners_query)) {
        if (tep_not_null($banners['expires_date'])) {
          if (date('Y-m-d H:i:s') >= $banners['expires_date']) {
            tep_set_banner_status($banners['banners_id'], '0');
          }
        } elseif (tep_not_null($banners['expires_impressions'])) {
          if ($banners['banners_shown'] >= $banners['expires_impressions']) {
            tep_set_banner_status($banners['banners_id'], '0');
          }
        }
      }
    }
  }

/* -------------------------------------
    功能: 显示banner 
    参数: $action(string) 动作类型  
    参数: $identifier(array) banner组的信息 
    参数: $width(int) 宽度 
    参数: $height(int) 高度  
    参数: $jump_single(bool) 跳转URL  
    返回值: 显示的html(string) 
------------------------------------ */
  function tep_display_banner($action, $identifier, $width = null, $height = null,$jump_single=false) {
    if ($action == 'dynamic') {
      $banners_query = tep_db_query("select count(*) as count from " . TABLE_BANNERS . " where status = '1' and banners_group = '" . $identifier . "' and site_id = '".SITE_ID."'");
      $banners = tep_db_fetch_array($banners_query);
      if ($banners['count'] > 0) {
        $banner = tep_random_select("select banners_id, banners_title, banners_url,banners_image, banners_html_text from " . TABLE_BANNERS . " where status = '1' and banners_group = '" . $identifier . "' and site_id = '".SITE_ID."'");
      } else {
        return '<b>TEP ERROR! (tep_display_banner(' . $action . ', ' . $identifier . ') -> No banners with group \'' . $identifier . '\' found!</b>';
      }
    } elseif ($action == 'static') {
      if (is_array($identifier)) {
        $banner = $identifier;
      } else {
        $banner_query = tep_db_query("select banners_id, banners_title, banners_url,banners_image, banners_html_text from " . TABLE_BANNERS . " where status = '1' and banners_id = '" . $identifier . "' and site_id = '".SITE_ID."'");
        if (tep_db_num_rows($banner_query)) {
          $banner = tep_db_fetch_array($banner_query);
        } else {
          return '<b>TEP ERROR! (tep_display_banner(' . $action . ', ' . $identifier . ') -> Banner with ID \'' . $identifier . '\' not found, or status inactive</b>';
        }
      }
    } else {
      return '<b>TEP ERROR! (tep_display_banner(' . $action . ', ' . $identifier . ') -> Unknown $action parameter value - it must be either \'dynamic\' or \'static\'</b>';
    }

    if (tep_not_null($banner['banners_html_text'])) {
      $banner_string = $banner['banners_html_text'];
    } else {
      if($banner['banners_url'] != '') { 
         if($width && $height) {
        $banner_string = '<a href="' . tep_href_link(FILENAME_REDIRECT, 'action=banner&goto=' . $banner['banners_id']) . '" '.($jump_single?'':'class="blank"').'>' . tep_image(DIR_WS_IMAGES . $banner['banners_image'], $banner['banners_title'], $width, $height) . '</a>';
         } else {
          $banner_string = '<a href="' . tep_href_link(FILENAME_REDIRECT, 'action=banner&goto=' . $banner['banners_id']) . '" '.($jump_single?'':'class="blank"').'>' . tep_image(DIR_WS_IMAGES . $banner['banners_image'], $banner['banners_title']) . '</a>';
         }
        } else {
        if($width && $height) {
        $banner_string = tep_image(DIR_WS_IMAGES . $banner['banners_image'], $banner['banners_title'], $width, $height);
      } else {
        $banner_string = tep_image(DIR_WS_IMAGES . $banner['banners_image'], $banner['banners_title']);
      }
    }
  }

    tep_update_banner_display_count($banner['banners_id']);

    return $banner_string;
  }

/* -------------------------------------
    功能: 检查是否有banner存在 
    参数: $action(string) 动作类型  
    参数: $identifier(array) banner组的信息 
    返回值: 是否存在(resource/boolean) 
------------------------------------ */
  function tep_banner_exists($action, $identifier) {
    if ($action == 'dynamic') {
      $banner_query = tep_db_query("select banners_id, banners_title, banners_image,banners_url, banners_html_text from " . TABLE_BANNERS . " where status = '1' and banners_group = '" . $identifier . "' and site_id = '".SITE_ID."'");
      return tep_db_fetch_array($banner_query);
    } else 
    if ($action == 'static') {
      $banner_query = tep_db_query("select banners_id, banners_title, banners_image,banners_url, banners_html_text from " . TABLE_BANNERS . " where status = '1' and banners_id = '" . $identifier . "' and site_id = '".SITE_ID."'");
      return tep_db_fetch_array($banner_query);
    } else {
      return false;
    }
  }

/* -------------------------------------
    功能: 更新该banner的历史记录 
    参数: $banner_id(int) banner id  
    返回值: 无
------------------------------------ */
  function tep_update_banner_display_count($banner_id) {
    $banner_check_query = tep_db_query("select count(*) as count from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . $banner_id . "' and date_format(banners_history_date, '%Y%m%d') = date_format(now(), '%Y%m%d')");
    $banner_check = tep_db_fetch_array($banner_check_query);

    if ($banner_check['count'] > 0) {
      tep_db_query("update " . TABLE_BANNERS_HISTORY . " set banners_shown = banners_shown + 1 where banners_id = '" . $banner_id . "' and date_format(banners_history_date, '%Y%m%d') = date_format(now(), '%Y%m%d')");
    } else {
      tep_db_query("insert into " . TABLE_BANNERS_HISTORY . " (banners_id, banners_shown, banners_history_date) values ('" . $banner_id . "', 1, now())");
    }
  }

/* -------------------------------------
    功能: 更新该banner被点击的次数 
    参数: $banner_id(int) banner id  
    返回值: 无
------------------------------------ */
  function tep_update_banner_click_count($banner_id) {
    tep_db_query("update " . TABLE_BANNERS_HISTORY . " set banners_clicked = banners_clicked + 1 where banners_id = '" . $banner_id . "' and date_format(banners_history_date, '%Y%m%d') = date_format(now(), '%Y%m%d')");
  }
?>
