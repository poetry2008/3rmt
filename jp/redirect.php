<?php
/*
  $Id$

  跳转

*/

  require('includes/application_top.php');

  switch ($HTTP_GET_VARS['action']) {
//ccdd
    case 'banner': $banner_query = tep_db_query("
                       select banners_url 
                       from " . TABLE_BANNERS . " 
                       where banners_id = '" . intval($HTTP_GET_VARS['goto']) . "' 
                         and site_id = '" . SITE_ID . "'
                   ");
                   if (tep_db_num_rows($banner_query)) {
                     $banner = tep_db_fetch_array($banner_query);
                     tep_update_banner_click_count($HTTP_GET_VARS['goto']);
                     tep_redirect($banner['banners_url']);
                   } else {
                     tep_redirect(tep_href_link(FILENAME_DEFAULT));
                   }
                   break;

    case 'url':    if (isset($HTTP_GET_VARS['goto'])) {
                     tep_redirect('http://' . $HTTP_GET_VARS['goto']);
                   } else {
                     tep_redirect(tep_href_link(FILENAME_DEFAULT));
                   }
                   break;

    case 'manufacturer' : if (isset($HTTP_GET_VARS['manufacturers_id'])) {
//ccdd
                            $manufacturer_query = tep_db_query("
                                select manufacturers_url 
                                from " . TABLE_MANUFACTURERS_INFO . " 
                                where manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' 
                                  and languages_id = '" . $languages_id . "'
                            ");
                            if (!tep_db_num_rows($manufacturer_query)) {
// no url exists for the selected language, lets use the default language then
//ccdd
                              $manufacturer_query = tep_db_query("
                                  select mi.languages_id, 
                                         mi.manufacturers_url 
                                  from " . TABLE_MANUFACTURERS_INFO . " mi, " . TABLE_LANGUAGES . " l 
                                  where mi.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' 
                                    and mi.languages_id = l.languages_id 
                                    and l.code = '" . DEFAULT_LANGUAGE . "'
                              ");
                              if (!tep_db_num_rows($manufacturer_query)) {
// no url exists, return to the site
                                tep_redirect(tep_href_link(FILENAME_DEFAULT));
                              } else {
                                $manufacturer = tep_db_fetch_array($manufacturer_query);
//todo: 这里不知道是否应该分开网站
//ccdd 
                                tep_db_query("
                                    update " . TABLE_MANUFACTURERS_INFO . " 
                                    set url_clicked = url_clicked+1, 
                                        date_last_click = now() 
                                    where manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' 
                                      and languages_id = '" . $manufacturer['languages_id'] . "'
                                ");
                              }
                            } else {
// url exists in selected language
                              $manufacturer = tep_db_fetch_array($manufacturer_query);
//ccdd
                              tep_db_query("
                                  update " . TABLE_MANUFACTURERS_INFO . " 
                                  set url_clicked = url_clicked+1, 
                                      date_last_click = now() 
                                  where manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' 
                                    and languages_id = '" . $languages_id . "'
                              ");
                            }

                            tep_redirect($manufacturer['manufacturers_url']);
                          } else {
                            tep_redirect(tep_href_link(FILENAME_DEFAULT));
                          }
                          break;
    default:       tep_redirect(tep_href_link(FILENAME_DEFAULT));
                   break;
  }
?>
