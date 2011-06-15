<?php
/*
   $Id$
   
   GM的FAQ管理
*/
  require('includes/application_top.php');

  //define('FILENAME_FAQ', 'faq.php');
  define('TABLE_FAQ_CATEGORIES', 'faq_categories');
  define('TABLE_FAQ_QUESTION',  'faq_question');
  define('TABLE_FAQ_CATEGORIES_DESCRIPTION', 'faq_categories_description');
  define('TABLE_FAQ_QUESTION_DESCRIPTION',  'faq_question_description');
  define('TABLE_FAQ_QUESTION_TO_CATEGORIES','faq_question_to_categories');

  //require(DIR_WS_LANGUAGES . $language . '/' .  FILENAME_FAQ);
  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
      case 'delete_faq_category_confirm':
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         //   $edit_per=editPermission($site_arr, $site_id);//判断是否拥有相应网站的管理权限
         forward401Unless(editPermission($site_arr, $site_id));
         $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
         if ($_POST['faq_category_id']) {
          $faq_category_id = tep_db_prepare_input($_POST['faq_category_id']);
          tep_set_time_limit(0);
          $categories = tep_get_faq_category_tree($faq_category_id, '', '0', '', true);
          $products = array();
          $products_delete = array();

          for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
            $product_ids_query = tep_db_query("select faq_category_id from " .
                TABLE_FAQ_QUESTION_TO_CATEGORIES . "
                where faq_category_id = '" . $categories[$i]['id'] . "'");
            while ($product_ids = tep_db_fetch_array($product_ids_query)) {
              $products[$product_ids['faq_category_id']]['categories'][] =
                $categories[$i]['faq_category_id'];
            }
          }
          reset($products);
          while (list($key, $value) = each($products)) {
            $category_ids = '';
            for ($i = 0, $n = sizeof($value['categories']); $i < $n; $i++) {
              $category_ids .= '\'' . $value['categories'][$i] . '\', ';
            }
            $category_ids = substr($category_ids, 0, -2);

            $check_query = tep_db_query("select count(*) as total from " .
                TABLE_FAQ_QUESTION_TO_CATEGORIES . " where faq_question_id = '" .
                $key . "' and faq_category_id not in (" . $category_ids . ")");
            $check = tep_db_fetch_array($check_query);
            if ($check['total'] < '1') {
              $products_delete[$key] = $key;
            }
          }
          for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
            tep_remove_faq_category($categories[$i]['faq_category_id']);
          }

          reset($products_delete);
          while (list($key) = each($products_delete)) {
            tep_remove_faq_question($key);
          }
         }
         tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' .
               $cPath.$dc_page));
         break;
      case 'delete_faq_category_description_confirm':
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         //   $edit_per=editPermission($site_arr, $site_id);//判断是否拥有相应网站的管理权限
         forward401Unless(editPermission($site_arr, $site_id));
         $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
         if ($_GET['cID'] && $_GET['site_id']) {
           tep_db_query("delete from ".TABLE_FAQ_CATEGORIES_DESCRIPTION." where
               faq_category_id = '".$_GET['cID']."' && site_id =
               '".(int)$_GET['site_id']."'");
         }
         if (isset($_GET['rdirect'])) {
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .
                 '&cID='.  (int)$_GET['cID'].'&site_id=0'.$dc_page));
         }else{
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .
                 '&cID='.  (int)$_GET['cID'].'&site_id='.(int)$_GET['site_id'].$dc_page));
         }
         break;
      case 'delete_faq_question_confirm':
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         //   $edit_per=editPermission($site_arr, $site_id);//判断是否拥有相应网站的管理权限
         forward401Unless(editPermission($site_arr, $site_id));
         if ($_GET['qID'] && $_GET['site_id']) {
           tep_db_query("delete from ".TABLE_FAQ_QUESTION_DESCRIPTION." where
               faq_question_id = '".$_GET['qID']."'");
         }
         if (isset($_GET['rdirect'])) {
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .
                 '&qID='.  (int)$_GET['qID'].'&site_id=0'.$dc_page));
         }else{
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .
                 '&qID='.  (int)$_GET['qID'].'&site_id='.(int)$_GET['site_id'].$dc_page));
         }
         break;
         break;
      case 'delete_faq_question_description_confirm':
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         //   $edit_per=editPermission($site_arr, $site_id);//判断是否拥有相应网站的管理权限
         forward401Unless(editPermission($site_arr, $site_id));
         $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
         if ($_GET['qID'] && $_GET['site_id']) {
           tep_db_query("delete from ".TABLE_FAQ_QUESTION_DESCRIPTION." where
               faq_question_id = '".$_GET['qID']."' && site_id =
               '".(int)$_GET['site_id']."'");
         }
         if (isset($_GET['rdirect'])) {
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .
                 '&qID='.  (int)$_GET['qID'].'&site_id=0'.$dc_page));
         }else{
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .
                 '&qID='.  (int)$_GET['qID'].'&site_id='.(int)$_GET['site_id'].$dc_page));
         }
         break;
      case 'insert_faq_question':
      case 'update_faq_question':
         $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         //   $edit_per=editPermission($site_arr, $site_id);//判断是否拥有相应网站的管理权限
         forward401Unless(editPermission($site_arr, $site_id));
         $faq_question_id = tep_db_prepare_input($_POST['faq_question_id']);
         $sort_order = tep_db_prepare_input($_POST['sort_order']);
         $sql_data_array = array('sort_order' => $sort_order);

         if($_GET['action'] == 'insert_faq_question') {
           $insert_sql_data = array('updated_at' => 'now()',
                                    'created_at' => 'now()');
           $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
           tep_db_perform(TABLE_FAQ_QUESTION, $sql_data_array);
           $faq_question_id = tep_db_insert_id();
           $faq_c2q_arr = array('faq_category_id' => $current_category_id,
                                'faq_question_id' => $faq_question_id);
           tep_db_perform(TABLE_FAQ_QUESTION_TO_CATEGORIES, $faq_c2q_arr);
         }else{
           $update_sql_data = array('updated_at' => 'now()');
           $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
           tep_db_perform(TABLE_FAQ_QUESTION, $sql_data_array, 'update',
               'id = \'' . $faq_question_id . '\'');
         }
         $sql_data_array = array(
            'romaji' => str_replace(array('/','_'),'-',tep_db_prepare_input($_POST['romaji'])),
            'ask' => tep_db_prepare_input($_POST['ask']),
            'keywords' => tep_db_prepare_input($_POST['keywords']),
            'answer' => tep_db_prepare_input($_POST['anwser']),
              );
         if($_GET['action'] == 'insert_faq_question' || ($_GET['action'] ==
               'update_faq_question' &&
               !tep_faq_question_description_exist($faq_question_id,$site_id))) {
            $insert_sql_data = array('faq_question_id' => $faq_question_id,
                                     'site_id' => $site_id);
            if(!tep_check_romaji($sql_data_array['romaji'])){
              $messageStack->add_session(TEXT_ROMAJI_ERROR, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
            $row_faq_sql = "select * from  
              ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd,
              ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
              where fqd.faq_question_id=fq2c.faq_question_id and
              fq2c.faq_category_id='".$current_category_id."'      and
              fqd.romaji='".$sql_data_array['romaji']."' and
              fqd.site_id='".$site_id."'";
            if(tep_db_num_rows(tep_db_query($row_faq_sql))){
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
            $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
            tep_db_perform(TABLE_FAQ_QUESTION_DESCRIPTION, $sql_data_array);
         }else{
            if(!tep_check_romaji($sql_data_array['romaji'])){
              $messageStack->add_session(TEXT_ROMAJI_ERROR, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
            $row_faq_sql = "select * from  
              ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd,
              ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
              where fqd.faq_question_id=fq2c.faq_question_id and
              fq2c.faq_category_id='".$current_category_id."'      and
              fqd.romaji='".$sql_data_array['romaji']."' and
              fqd.site_id='".$site_id."' and 
              fqd.faq_question_id != '".$faq_question_id."'";
            if(tep_db_num_rows(tep_db_query($row_faq_sql))){
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
           tep_db_perform(TABLE_FAQ_QUESTION_DESCRIPTION, $sql_data_array,
               'update','faq_question_id =\''.$faq_question_id.'\' and site_id =
               \''.$site_id.'\'');

         }
         if(isset($_GET['rdirect'])){
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .
                 '&cID=' . $faq_category_id.'&site_id=0'));
         }else{
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .
                 '&cID=' . $faq_category_id.'&site_id='.$site_id));
         }

                break;
      case 'insert_faq_category':
      case 'update_faq_category':
        $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         //   $edit_per=editPermission($site_arr, $site_id);//判断是否拥有相应网站的管理权限
         forward401Unless(editPermission($site_arr, $site_id));
         $faq_category_id = tep_db_prepare_input($_POST['faq_category_id']);
         $sort_order = tep_db_prepare_input($_POST['sort_order']);
         $sql_data_array = array('sort_order' => $sort_order);

         if($_GET['action'] == 'insert_faq_category') {
           $insert_sql_data = array('parent_id' => $current_category_id,
                                    'updated_at' => 'now()',
                                    'created_at' => 'now()');
           $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
           tep_db_perform(TABLE_FAQ_CATEGORIES, $sql_data_array);
           $faq_category_id = tep_db_insert_id();
         }else{
           $update_sql_data = array('updated_at' => 'now()');
           $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
           tep_db_perform(TABLE_FAQ_CATEGORIES, $sql_data_array, 'update',
               'id = \'' . $faq_category_id . '\'');
         }
         $sql_data_array = array(
            'romaji' => str_replace(array('/','_'),'-',tep_db_prepare_input($_POST['romaji'])),
            'title' => tep_db_prepare_input($_POST['title']),
            'keywords' => tep_db_prepare_input($_POST['keywords']),
            'description' => tep_db_prepare_input($_POST['description']),
              );
         if($_GET['action'] == 'insert_faq_category' || ($_GET['action'] ==
               'update_faq_category' &&
               !tep_faq_categories_description_exist($faq_category_id,$site_id))) {
            $insert_sql_data = array('faq_category_id' => $faq_category_id,
                                     'site_id' => $site_id);
            if(!tep_check_romaji($sql_data_array['romaji'])){
              $messageStack->add_session(TEXT_ROMAJI_ERROR, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
            $row_faq_sql = "select * from ".TABLE_FAQ_CATEGORIES." fc,
              ".TABLE_FAQ_CATEGORIES_DESCRIPTION." 
              fcd where fc.id=fcd.faq_category_id and
              fc.parent_id='".$current_category_id."'      and
              fcd.romaji='".$sql_data_array['romaji']."' and
              fcd.site_id='".$site_id."'";
            if(tep_db_num_rows(tep_db_query($row_faq_sql))){
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
            $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
            tep_db_perform(TABLE_FAQ_CATEGORIES_DESCRIPTION, $sql_data_array);
         }else{
            if(!tep_check_romaji($sql_data_array['romaji'])){
              $messageStack->add_session(TEXT_ROMAJI_ERROR, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
            $row_faq_sql = "select * from ".TABLE_FAQ_CATEGORIES." fc,
              ".TABLE_FAQ_CATEGORIES_DESCRIPTION." 
              fcd where fc.id=fcd.faq_category_id and
              fc.parent_id='".$current_category_id."'      and
              fcd.romaji='".$sql_data_array['romaji']."' and
              fcd.site_id='".$site_id."' and 
              fc.id != '".$faq_category_id."'";
            if(tep_db_num_rows(tep_db_query($row_faq_sql))){
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
           tep_db_perform(TABLE_FAQ_CATEGORIES_DESCRIPTION, $sql_data_array,
               'update','faq_category_id =\''.$faq_category_id.'\' and site_id =
               \''.$site_id.'\'');

         }
         if(isset($_GET['rdirect'])){
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .
                 '&cID=' . $faq_category_id.'&site_id=0'));
         }else{
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .
                 '&cID=' . $faq_category_id.'&site_id='.$site_id));
         }
        break;
    }
  }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
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
            <td class="pageHeading" align="right">
            <?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?>
            </td>
            <td class="smallText" align="right">
            <?php 
            //search form for faq
            echo tep_draw_form('search',FILENAME_FAQ,'','get')."\n";
            ?>
            <div class="faq_search">
            <?php
            echo tep_draw_hidden_field('site_id',isset($_GET['site_id'])?$_GET['site_id']:'0');
            echo HEADING_TITLE_SEARCH. ' ' .
            tep_draw_input_field('search',isset($_GET['search'])?$_GET['search']:'');
            ?>
            </div>
            </form>
            </td>
            <td class="smallText" align="right">
            <div class="faq_gotomenu">
              goto menu
            </div>
            </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td ><div ><?php tep_site_filter(FILENAME_FAQ,true);?></div></td>
              </tr>
              <tr>
                <td >
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <?php /* faq infor table */?>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent">
                      <?php echo TABLE_FAQ_CATEGORY_NAME; ?>
                    </td>
                    <td class="dataTableHeadingContent" align="right">
                      <?php echo TABLE_HEADING_ACTION;?>&nbsp;
                    </td>
                  </tr>
                  <?php 
                  // faq category list
                  $faq_category_count = 0;
                  $rows = 0;
                  if(isset($_GET['search']) && $_GET['search']) {
                    $faq_category_query_raw = "
                      select 
                      fcd.faq_category_id,
                      fc.parent_id,
                      fc.created_at,
                      fc.updated_at,
                      fc.sort_order,
                      fcd.site_id,
                      fcd.romaji,
                      fcd.title,
                      fcd.keywords,
                      fcd.description 
                      from ".TABLE_FAQ_CATEGORIES." fc, 
                      ".TABLE_FAQ_CATEGORIES_DESCRIPTION. " fcd 
                      where fc.id = fcd.faq_category_id 
                      and fcd.title like '%".$_GET['search']."%' 
                      and fcd.site_id = '0'
                      order by fc.sort_order,fcd.title 
                       ";
                  }else{
                    $faq_category_query_raw = "
                      select * from 
                      (
                        select 
                        fcd.faq_category_id,
                        fc.parent_id,
                        fc.created_at,
                        fc.updated_at,
                        fc.sort_order,
                        fcd.site_id,
                        fcd.romaji,
                        fcd.title,
                        fcd.keywords,
                        fcd.description 
                        from ".TABLE_FAQ_CATEGORIES." fc, 
                        ".TABLE_FAQ_CATEGORIES_DESCRIPTION. " fcd 
                        where fc.parent_id = '".$current_category_id."'
                        and fc.id = fcd.faq_category_id 
                        order by site_id DESC
                      ) c 
                      where site_id = ".((isset($_GET['site_id']) &&
                      $_GET['site_id'])?$_GET['site_id']:0)."
                      or site_id = 0 
                      group by c.faq_category_id 
                      order by sort_order,title
                      ";
                  }
                  $faq_category_query = tep_db_query($faq_category_query_raw);
                  while($faq_category = tep_db_fetch_array($faq_category_query)){
                    $faq_count++;
                    $rows++;

                    $faq_info_arr =
                      array('id','parent_id','sort_order','name','site_id');
                    if(isset($_GET['search']) && $_GET['search']){
                        $cPath= $faq_category['parent_id'];
                    }
                    if(
                        ((!isset($_GET['cID']) || !$_GET['cID']) &&
                         (!isset($_GET['qID']) || !$_GET['qID']) ||
                         (isset($_GET['cID']) && $_GET['cID'] ==
                          $faq_category['faq_category_id'])) 
                         && (!isset($faq_info) || !$faq_info) 
                         && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_')
                      ){
                    $faq_category_childs = array('childs_count' => 
                        tep_childs_in_faq_category_count($faq_category['id']));
                    $faq_category_question = array('question_count' => 
                        tep_question_in_faq_category_count($faq_category['id']));
                    $faq_array = tep_array_merge($faq_category,$faq_category_childs,
                        $faq_category_question);
                    $faq_info = new objectInfo($faq_array);
                    }

                    // row color 
                    $even = 'dataTableSecondRow';
                    $odd = 'dataTableRow';
                    if(isset($newColor) && $newColor == $odd) {
                      $nowColor = $even;
                    }else{
                      $nowColor = $odd;
                    }

                    if((isset($faq_info)&&is_object($faq_info))&&
                        ($faq_category['faq_category_id'] ==
                         $faq_info->faq_category_id)){
                      echo '<tr class="dataTableRowSelected" 
                        onmouseover="this.style.cursor=\'hand\'" 
                        onclick="document.location.herf=\''.tep_href_link(FILENAME_FAQ,
                        tep_get_faq_path($faq_category['faq_category_id']).
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)))
                        . '\'">' . "\n";

                    }else{
                      echo '<tr class="'.$nowColor.'" 
                        onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" 
                        onmouseout="this.className=\''.$nowColor.'\'" 
                        onclick="document.location.href=\''.tep_href_link(FILENAME_FAQ,
                        'cPath='.
                          $cPath.(isset($_GET['page'])&&$_GET['page']?('&page='.$_GET['page']):'').
                          '&cID='.$faq_category['faq_category_id'].'&site_id='.
                          ((isset($_GET['site_id'])?$_GET['site_id']:0))).
                          '\'">'."\n";
                    }

                    echo '<td class="dataTableContent">';
                    echo '<a
                      href="'.tep_href_link(FILENAME_FAQ,
                        tep_get_faq_path($faq_category['faq_category_id']).'&site_id='.
                      ((isset($_GET['site_id'])?$_GET['site_id']:0))). '">' .
                      tep_image(DIR_WS_ICONS.'folder.gif',ICON_FOLDER) .'</a>&nbsp;
                    <b>'.$faq_category['title'].'</b>';
                    echo '</td>';
                    echo '<td class="dataTableContent" align="right">';
                    if((isset($faq_info)&&is_object($faq_info))&&
                        ($faq_category['faq_category_id'] ==
                         $faq_info->faq_category_id)){
                      echo tep_image(DIR_WS_IMAGES.'icon_arrow_right.gif','');
                    }else{
                      echo '<a
                        href="'.tep_href_link(FILENAME_FAQ,'cPath='.$cPath.
                        '&cID='.$faq_category['faq_category_id'].
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0))).
                        '">'.tep_image(DIR_WS_IMAGES . 'icon_info.gif',
                        IMAGE_ICON_INFO) . '</a>';
                    }
                    echo '&nbsp;</td>';
                    echo '</tr>';
                  }
                  $faq_count=0;
                  // sql for question 
                  if(isset($_GET['search']) && $_GET['search']) {
                    $faq_query_raw = "select 
                      fq2c.faq_category_id,
                      fqd.faq_question_id,
                      fqd.romaji,
                      fqd.ask,
                      fqd.keywords,
                      fqd.answer,
                      fq.sort_order,
                      fq.created_at,
                      fq.updated_at,
                      fqd.site_id 
                      from ".TABLE_FAQ_QUESTION." fq, 
                           ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd ,
                           ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
                      where fq.id = fqd.faq_question_id 
                      and fq.id = fq2c.faq_question_id 
                      and fqd.ask like '%".$_GET['search']."%' 
                      and fqd.site_id ='0' 
                      order by fq.sort_order,fqd.ask,fq.id  
                      ";
                  }else{
                    $faq_query_raw = "select * from (
                      select 
                      fq2c.faq_category_id,
                      fqd.faq_question_id,
                      fqd.romaji,
                      fqd.ask,
                      fqd.keywords,
                      fqd.answer,
                      fq.sort_order,
                      fq.created_at,
                      fq.updated_at,
                      fqd.site_id 
                      from ".TABLE_FAQ_QUESTION." fq, 
                           ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd ,
                           ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
                      where fq.id = fqd.faq_question_id 
                      and fq.id = fq2c.faq_question_id 
                      and fq2c.faq_category_id = '". $current_category_id . "' 
                      order by fqd.site_id DESC
                      ) c  
                      where site_id = ".((isset($_GET['site_id']) &&
                      $_GET['site_id'])?$_GET['site_id']:0)." 
                      or site_id = 0 
                      group by c.faq_question_id 
                      order by c.sort_order,c.ask,c.faq_question_id 
                      ";
                  }
                  define(MAX_DISPLAY_FAQ_ADMIN,5);
                  $faq_split = new splitPageResults($_GET['page'],MAX_DISPLAY_FAQ_ADMIN,
                      $faq_query_raw,$faq_query_number);
                  $_faq_query = tep_db_query($faq_query_raw);
                  while($_faq_info = tep_db_fetch_array($_faq_query)){
                    $faq_count++;
                    $rows++;

                    if(isset($_GET['search'])&&$_GET['search']){
                      $cPath=$_faq_info['faq_category_id'];
                    }
                    if(
                        ((!isset($_GET['qID']) || !$_GET['qID']) &&
                         (!isset($_GET['cID']) || !$_GET['cID']) ||
                         (isset($_GET['qID']) && $_GET['qID'] ==
                          $_faq_info['faq_question_id']))
                        && (!isset($qInfo) || !$qInfo)
                        && (!isset($faq_info) || !$faq_info)
                        && (!isset($_GET['action']) || substr($_GET['action'], 0, 4)
                          != 'new_')
                      ){
                      $qInfo = new objectInfo($_faq_info);
                    }

                    $even = 'dataTableSecondRow';
                    $odd = 'dataTableRow';
                    if(isset($newColor) && $newColor == $odd) {
                      $nowColor = $even;
                    }else{
                      $nowColor = $odd;
                    }

                    if((isset($qInfo)&&is_object($qInfo))&&
                        ($_faq_info['faq_question_id'] == $qInfo->faq_question_id)){
                      echo '<tr class="dataTableRowSelected" 
                        onmouseover="this.style.cursor=\'hand\'" 
                        onclick="document.location.herf=\''.tep_href_link(FILENAME_FAQ,
                        'cPath='.$cPath.($_GET['page'] ? ('&page=' . $_GET['page']): '' ) .
                        '&qID='.$_faq_info['faq_question_id'].
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)))
                        . '\'">' . "\n";

                    }else{
                      echo '<tr class="'.$nowColor.'" 
                        onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" 
                        onmouseout="this.className=\''.$nowColor.'\'" 
                        onclick="document.location.href=\''.tep_href_link(FILENAME_FAQ,
                        'cPath='.$cPath.($_GET['page'] ? ('&page=' . $_GET['page']): '' ) .
                        '&qID='.$_faq_info['faq_question_id'].
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)))
                        .  '\'">'."\n";
                    }
                      echo "<td class='dataTableContent'>";
                      echo "<b>".$_faq_info['ask']."</b>";
                      echo "</td>";
                      echo '<td class="dataTableContent" align="right">';
                      if(isset($qInfo)&&(is_object($qInfo))&&
                            $_faq_info['faq_question_id'] ==  $qInfo->faq_question_id){
                        echo tep_image(DIR_WS_IMAGES.'icon_arrow_right.gif', '');
                      }else{
                        echo '<a href="'.tep_href_link(FILENAME_FAQ,
                             'cPath='.$cPath.'&qID='.$_faq_info['faq_question_id'].'&site_id='.
                               ((isset($_GET['site_id'])?$_GET['site_id']:0))).'">'.
                               tep_image(DIR_WS_IMAGES . 'icon_info.gif',
                                   IMAGE_ICON_INFO) . '</a>';
                      }
                      echo "&nbsp;</td>";
                      echo "</tr>";
                  }

                  ?>
                </table>
                </td>
              </tr>
              <tr>
                <td align="right" class="smallText">
                  <?php 
    //path array to  path_back
    if ($cPath_array) {
      $cPath_back = '';
      for($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
        if ($cPath_back == '') {
          $cPath_back .= $cPath_array[$i];
        } else {
          $cPath_back .= '_' . $cPath_array[$i];
        }    
      }    
    }    

    $cPath_back = isset($cPath_back) && $cPath_back ? 'cPath=' . $cPath_back : '';
                  // new faq category and new faq button
                  if($cPath){
                    echo tep_html_button(IMAGE_BACK,
                      tep_href_link(FILENAME_FAQ,$cPath_back.'&cID='.$current_category_id.
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0))));
                    echo '&nbsp;';
                  }
                  if((!isset($_GET['search'])||!$_GET['search'])&&
                      $ocertify->npermission >= 10){
                    echo tep_html_button(IMAGE_NEW_FAQ_CATEGORY,tep_href_link(FILENAME_FAQ,
                            'cPath='.$cPath.'&action=new_faq_category'.'&site_id='.$site_id));
                    echo '&nbsp;';
                    echo tep_html_button(IMAGE_NEW_FAQ,tep_href_link(FILENAME_FAQ,
                          'cPath='.$cPath.'&action=new_faq_question'.'&site_id='.$site_id));
                  }


                  ?>
                </td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  // faq right info
switch (isset($_GET['action'])? $_GET['action']:'') {
  case 'new_faq_category':
    $heading[] = array('text' => '<b>' .TEXT_INFO_HEADING_NEW_FAQ_CATEGORY.'</b>');
    $contents = array('form' =>
        tep_draw_form('newfaqcategory',FILENAME_FAQ,'action=insert_faq_category&cPath='.$cPath,
          'post'));
    $contents[] = array('text' => TEXT_NEW_FAQ_CATEGORY_INFO);
    $faq_category_inputs_string = ''; 
    $faq_category_inputs_string .= '<br>Romaji:<br>'.
      tep_draw_input_field('romaji','','id="cromaji"').'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_TITLE.':<br>'.
      tep_draw_input_field('title','','id="title"').'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_KEYWORDS.':<br>'.
      tep_draw_textarea_field('keywords','soft',30,3).'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_DESCRIPTION.':<br>'.
      tep_draw_textarea_field('description','soft',30,3).'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_SORT_ORDER.':<br>'.
      tep_draw_input_field('sort_order','','size="2"').'<br>';
    $contents[] = array('text' => $faq_category_inputs_string.
        tep_draw_hidden_field('site_id',$site_id));
    $contents[] = array('align' => 'center' ,
        'text' => tep_html_submit(TEXT_SAVE).tep_html_button(IMAGE_CANCEL,
          tep_href_link(FILENAME_FAQ,'cPath='.$cPath)));
    break;
  case 'new_faq_question':
    $heading[] = array('text' => '<b>' .TEXT_INFO_HEADING_NEW_FAQ_QUESTION.'</b>');
    $contents = array('form' =>
        tep_draw_form('newfaqcategory',FILENAME_FAQ,'action=insert_faq_question&cPath='.$cPath.
          '&site_id='.$site_id.'&qID='.$_GET['qID'],'post'));
    $contents[] = array('text' => TEXT_NEW_FAQ_QUESTION_INFO);
    $faq_question_inputs_string = ''; 
    $faq_question_inputs_string .= '<br>Romaji:<br>'.
      tep_draw_input_field('romaji','','id="cromaji"').'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_ASK.':<br>'.
      tep_draw_input_field('ask','','id="title"').'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_KEYWORDS.':<br>'.
      tep_draw_textarea_field('keywords','soft',30,3).'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_ANSWER.':<br>'.
      tep_draw_textarea_field('answer','soft',30,3).'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_SORT_ORDER.':<br>'.
      tep_draw_input_field('sort_order','','size="2"').'<br>';
    $contents[] = array('text' => $faq_question_inputs_string.
        tep_draw_hidden_field('site_id',$site_id));
    $contents[] = array('align' => 'center' ,
        'text' => tep_html_submit(TEXT_SAVE).tep_html_button(IMAGE_CANCEL,
          tep_href_link(FILENAME_FAQ,'cPath='.$cPath.'&site_id='.$site_id.'&qID='.$_GET['qID'])));
    break;
  case 'edit_faq_category':
    $heading[] = array('text' => '<b>' .TEXT_INFO_HEADING_EDIT_FAQ_CATEGORY.'</b>');
    $contents = array('form' =>
        tep_draw_form('newfaqcategory',FILENAME_FAQ,'action=update_faq_category&cPath='.$cPath,
          'post'));
    $contents[] = array('text' => TEXT_NEW_FAQ_CATEGORY_INFO);
    $faq_category_inputs_string = ''; 
    $faq_category_inputs_string .= '<br>Romaji:<br>'.
      tep_draw_input_field('romaji',$faq_info->romaji,'id="cromaji"').'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_TITLE.':<br>'.
      tep_draw_input_field('title',$faq_info->title,'id="title"').'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_KEYWORDS.':<br>'.
      tep_draw_textarea_field('keywords','soft',30,3,$faq_info->keywords).'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_DESCRIPTION.':<br>'.
      tep_draw_textarea_field('description','soft',30,3,$faq_info->description).'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_SORT_ORDER.':<br>'.
      tep_draw_input_field('sort_order',$faq_info->sort_order,'size="2"').'<br>';
    $contents[] = array('text' => $faq_category_inputs_string.
      tep_draw_hidden_field('faq_category_id',$faq_info->faq_category_id).
      tep_draw_hidden_field('site_id',$site_id));
    $contents[] = array('align' => 'center' ,
        'text' => tep_html_submit(TEXT_SAVE).tep_html_button(IMAGE_CANCEL,
          tep_href_link(FILENAME_FAQ,'cPath='.$cPath)));
    break;
  case 'edit_faq_question':
    $heading[] = array('text' => '<b>' .TEXT_INFO_HEADING_EDIT_FAQ_QUESTION.'</b>');
    $contents = array('form' =>
        tep_draw_form('newfaqcategory',FILENAME_FAQ,'action=update_faq_question&cPath='.$cPath.
          '&site_id='.$site_id.'&qID='.$_GET['qID'],'post'));
    $contents[] = array('text' => TEXT_NEW_FAQ_QUESTION_INFO);
    $faq_question_inputs_string = ''; 
    $faq_question_inputs_string .= '<br>Romaji:<br>'.
      tep_draw_input_field('romaji',$qInfo->romaji,'id="cromaji"').'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_ASK.':<br>'.
      tep_draw_input_field('ask',$qInfo->ask,'id="title"').'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_KEYWORDS.':<br>'.
      tep_draw_textarea_field('keywords','soft',30,3,$qInfo->keywords).'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_ANSWER.':<br>'.
      tep_draw_textarea_field('answer','soft',30,3,$qInfo->answer).'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_SORT_ORDER.':<br>'.
      tep_draw_input_field('sort_order',$qInfo->sort_order,'size="2"').'<br>';
    $contents[] = array('text' => $faq_question_inputs_string.
      tep_draw_hidden_field('faq_question_id',$qInfo->faq_question_id).
        tep_draw_hidden_field('site_id',$site_id));
    $contents[] = array('align' => 'center' ,
        'text' => tep_html_submit(TEXT_SAVE).tep_html_button(IMAGE_CANCEL,
          tep_href_link(FILENAME_FAQ,'cPath='.$cPath.'&site_id='.$site_id.'&qID='.$_GET['qID'])));
    break;
  case 'delete_faq_category':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CATEGORY .
          '</b>');
      $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
      $contents = array('form' => tep_draw_form('categories', FILENAME_FAQ,
            'action=delete_faq_category_confirm&cPath=' . $cPath.$dc_page) .
          tep_draw_hidden_field('faq_category_id', $faq_info->faq_category_id));
      $contents[] = array('text' => TEXT_DELETE_FAQ_CATEGORY_INTRO);
      $contents[] = array('text' => '<br><b>' . $faq_info->title . '</b>');
      if($faq_info->childs_count > 0) {
         $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_CHILDS,
               $faq_info->childs_count));
      }
      if($faq_info->question_count >0){
         $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_QUESTION,
               $faq_info->question_count));
      }
      $contents[] = array('align' => 'center',
          'text' => '<br>'.tep_html_submit(IMAGE_DELETE).
          tep_html_button(IMAGE_CANCEL,tep_href_link(FILENAME_FAQ, 'cPath=' .
              $cPath . '&cID=' . $faq_info->faq_category_id.$dc_page))
          );
    break;
  case 'delete_faq_category_description':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_FAQ_CATEGORY .
          '</b>');
      $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
      if (isset($_GET['rdirect'])) {
        $contents = array('form' => tep_draw_form('categories', FILENAME_FAQ,
              'action=delete_faq_category_description_confirm&cID=' . $_GET['cID']
              .  '&cPath=' . $cPath . '&site_id=' .  $_GET['site_id'].'&rdirect=all'
              .$dc_page, 'post'));
      }else{
        $contents = array('form' => tep_draw_form('categories', FILENAME_FAQ,
              'action=delete_faq_category_description_confirm&cID=' . $_GET['cID']
              . '&cPath=' . $cPath . '&site_id=' . $_GET['site_id'].$dc_page,
              'post'));
      }
      $contents[] = array('text' => TEXT_DELETE_FAQ_CATEGORY_INTRO);
      $contents[] = array('text' => '<br><b>' . $faq_info->title . '</b>');
      if (isset($_GET['rdirect'])) {
        $contents[] = array('align' => 'center',
            'text' => tep_html_submit(IMAGE_DELETE).
            tep_html_button(IMAGE_CANCEL,tep_href_link(FILENAME_FAQ,
                'cPath=' . $cPath . '&cID=' .
                $faq_info->faq_category_id.'&site_id=0'.$dc_page))
            );
      }else{
        $contents[] = array('align' => 'center',
            'text' => tep_html_submit(IMAGE_DELETE).
            tep_html_button(IMAGE_CANCEL,tep_href_link(FILENAME_FAQ,
                'cPath=' . $cPath . '&cID=' .
                $faq_info->faq_category_id.'&site_id='.$_GET['site_id'].$dc_page))
            );
      }
    break;
  case 'delete_faq_question':
    $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_FAQ_QUESTION . '</b>');
    $d_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
    if (isset($_GET['rdirect'])) {
      $contents = array('form' => tep_draw_form('question', FILENAME_FAQ,
            'action=delete_faq_question_confirm&site_id=' .  $_GET['site_id']
            . '&qID=' . $_GET['qID'] . '&cPath=' .  $cPath.'&rdirect=all'.$d_page,
            'post'));
    }else{
       $contents = array('form' => tep_draw_form('question', FILENAME_FAQ,
             'action=delete_faq_question_confirm&site_id=' .
             $_GET['site_id'] . '&qID=' . $_GET['qID'] . '&cPath=' . $cPath.$d_page,
             'post'));
    }
    $contents[] = array('text' => TEXT_DELETE_PRODUCT_INTRO);
    $contents[] = array('text' => '<br><b>' . $qInfo->ask . '</b>');
    if (isset($_GET['rdirect'])) {
        $contents[] = array('align' => 'center',
            'text' => tep_html_submit(IMAGE_DELETE).
            tep_html_button(IMAGE_CANCEL,tep_href_link(FILENAME_CATEGORIES, 'cPath='
                . $cPath . '&qID=' . $qInfo->faq_question_id.
                '&site_id='.$_GET['site_id'].'&rdirect=all'.$d_page))
            );
    }else{
        $contents[] = array('align' => 'center',
            'text' => tep_html_submit(IMAGE_DELETE).
            tep_html_button(IMAGE_CANCEL,tep_href_link(FILENAME_CATEGORIES, 'cPath='
                . $cPath . '&qID=' . $qInfo->faq_question_id.
                '&site_id='.$_GET['site_id'].$d_page))
            );
    }
    break;
  case 'delete_faq_question_description':
    $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_FAQ_QUESTION . '</b>');
    $d_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
    if (isset($_GET['rdirect'])) {
      $contents = array('form' => tep_draw_form('question', FILENAME_FAQ,
            'action=delete_faq_question_description_confirm&site_id=' .  $_GET['site_id']
            . '&qID=' . $_GET['qID'] . '&cPath=' .  $cPath.'&rdirect=all'.$d_page,
            'post'));
    }else{
       $contents = array('form' => tep_draw_form('question', FILENAME_FAQ,
             'action=delete_faq_question_description_confirm&site_id=' .
             $_GET['site_id'] . '&qID=' . $_GET['qID'] . '&cPath=' . $cPath.$d_page,
             'post'));
    }
    $contents[] = array('text' => TEXT_DELETE_PRODUCT_INTRO);
    $contents[] = array('text' => '<br><b>' . $qInfo->ask . '</b>');
    if (isset($_GET['rdirect'])) {
        $contents[] = array('align' => 'center',
            'text' => tep_html_submit(IMAGE_DELETE).
            tep_html_button(IMAGE_CANCEL,tep_href_link(FILENAME_CATEGORIES, 'cPath='
                . $cPath . '&qID=' . $qInfo->faq_question_id.
                '&site_id='.$_GET['site_id'].'&rdirect=all'.$d_page))
            );
    }else{
        $contents[] = array('align' => 'center',
            'text' => tep_html_submit(IMAGE_DELETE).
            tep_html_button(IMAGE_CANCEL,tep_href_link(FILENAME_CATEGORIES, 'cPath='
                . $cPath . '&qID=' . $qInfo->faq_question_id.
                '&site_id='.$_GET['site_id'].$d_page))
            );
    }
    break;
  default:
    if ($rows > 0){
      if(isset($faq_info)&&is_object($faq_info)){
        $heading[] = array('text' => '<b>'.$faq_info->title.'</b>');
        if($ocertify->npermission >= 10 ){
          if(empty($_GET['site_id'])){
            $contents[] = array(
                'align' => 'left',
                'text' =>  tep_html_button(IMAGE_IDET,tep_href_link(FILENAME_FAQ,'cPath='. 
                    $cPath.'&cID='.$faq_info->faq_category_id.'&action=edit_faq_category')).
                ($ocertify->npermission == 15 ? tep_html_button(IMAGE_DELETE,
                tep_href_link(FILENAME_FAQ,      'cPath=' . $cPath . '&cID='
                  . $faq_info->faq_category_id . '&action=delete_faq_category')):''
                )
                /*
                .tep_html_button(IMAGE_MOVE,tep_href_link(FILENAME_CATEGORIES,
                    'cPath=' . $cPath . '&cID=' . $faq_info->faq_category_id .
                    '&action=move_faq_category'))
                */
                );
          }
          if (isset($_GET['site_id'])){
            foreach(tep_get_sites() as $site){
              if($site['id'] != $_GET['site_id']){
                continue;
              }
              $contents[] = array('text' => '<b>'.$site['romaji'] .'</b>');
              $contents[] = array(
                  'align' => 'left',
                  'text' => tep_html_button(IMAGE_EDIT,
                    tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath . '&cID=' .
                      $faq_info->faq_category_id .
                      '&action=edit_faq_category&site_id='.$site['id'])).
                  (tep_faq_categories_description_exist($faq_info->faq_category_id,$site['id'])?
                   tep_html_button(IMAGE_DELETE,tep_href_link(FILENAME_FAQ,'cPath='
                       . $cPath . '&cID=' .      $faq_info->faq_category_id .
                       '&action=delete_faq_category_description&site_id='.$site['id']))
                   :'')
                  );
            }
          }
          

        }
              $contents[] = array('text' => '<br>'. TEXT_CREATED_AT. ' '
                  .tep_date_short($faq_info->created_at));
              $contents[] = array('text' => '<br>'. TEXT_UPDATED_AT. ' '
                  .tep_date_short($faq_info->updated_at));

      }else if(isset($qInfo)&&is_object($qInfo)){
        $heading[] = array('text' => '<b>'.$qInfo->ask.'</b>');
        if($ocertify->npermission >= 10) {  //limit for permission
          if(empty($_GET['site_id'])){
          $contents[] = array('align' => 'left', 
              'text' => tep_html_button(IMAGE_EDIT, tep_href_link(FILENAME_FAQ, 
                  'cPath=' . $cPath . '&qID=' . $qInfo->faq_question_id.
                  '&action=edit_faq_question'.'&page='.$_GET['page'])).
               ($ocertify->npermission==15 ?
                tep_html_button(IMAGE_DELETE,tep_href_link(FILENAME_FAQ,'cPath=' .
                    $cPath . '&qID=' . $qInfo->faq_question_id .
                    '&action=delete_faq_question'.'&page='.$_GET['page'])):'')/*.
               tep_html_button(IMAGE_MOVE,tep_href_link(FILENAME_FAQ,
                   'cPath=' . $cPath . '&qID=' . $qInfo->faq_question_id .
                   '&action=move_faq_question'.'&page='.$_GET['page'])).
               tep_html_button(IMAGE_COPY_TO,tep_href_link(FILENAME_FAQ,
                   'cPath=' . $cPath . '&qID=' . $qInfo->faq_question_id .
                   '&action=copy_to_faq_question'.'&page='.$_GET['page']))*/
              );
          }
          if(isset($_GET['site_id'])){
            foreach(tep_get_sites() as $site){
              if($site['id'] != $_GET['site_id']){
                continue;
              }
              $contents[] = array('text' => '<b>' .$site['romaji'] .'</b>');
              $contents[] = array('align' => 'left' ,
                  'text' => tep_html_button(IMAGE_EDIT,tep_href_link(FILENAME_FAQ,
                      'cPath=' . $cPath . '&qID=' . $qInfo->faq_question_id .
                      '&action=edit_faq_question'. '&site_id='.
                      $site['id'].'&page='.$_GET['page'])).(
                   tep_faq_question_description_exist($qInfo->faq_question_id,$site['id'])?
                   tep_html_button(IMAGE_DELETE,tep_href_link(FILENAME_FAQ,
                       'cPath=' . $cPath . '&qID=' . $qInfo->faq_question_id .
                       '&action=delete_faq_question_description&site_id='.
                       $site['id'].'&page='.$_GET['page']))
                   :'')
                  );
            }
          }
        }
      }

    }else{
      $heading[] = array('text' => '<b>' .EMPTY_FAQ_CATEGORY .'</b>');
    }
    break;
  
  }

  if ((tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";
    $box = new box;
    echo $box->infoBox($heading, $contents);
    echo '            </td>' . "\n";
  }
?>
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
