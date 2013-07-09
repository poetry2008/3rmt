<?php
/*
   $Id$
*/
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
      $sql_site_where = 'site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
      $show_list_array = explode('-',$_GET['site_id']);
  } else {
      $show_list_str = tep_get_setting_site_info(FILENAME_FAQ);
      $sql_site_where = 'site_id in ('.$show_list_str.')';
      $show_list_array = explode(',',$show_list_str);
  }
    $sites_id_sql = tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
       while($userslist= tep_db_fetch_array($sites_id_sql)){
           $site_arr = $userslist['site_permission'];
    }
    $site_array = explode(',',$site_arr);
  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'toggle' 设置faq分类的状态 
   case 'setflag' 设置faq问题的状态 
   case 'delete_faq_category_confirm' 删除faq分类 
   case 'delete_faq_question_confirm' 删除faq问题 
   case 'insert_faq_question' 新建faq问题 
   case 'update_faq_question' 更新faq问题 
   case 'insert_faq_category' 新建faq分类 
   case 'update_faq_category' 更新faq分类 
------------------------------------------------------*/
        case 'toggle':
        if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
        else $site_arr="";
        $site_id = (isset($_GET['site_id']))?$_GET['site_id']:0;
        forward401Unless(editPermission($site_arr, $site_id));
        tep_insert_pwd_log($_GET['once_pwd'],$ocertify->auth_user);
        $cID = $_GET['cID'];
        $c_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
        if($_GET['cID']&&isset($_GET['status'])&&
            ($_GET['status']==1||$_GET['status']==0)
            &&$site_id != 0){
          tep_db_query("UPDATE `".TABLE_FAQ_CATEGORIES."` set `user_update` = '".$_SESSION['user_name']."',`updated_at` = now() WHERE `id` = '".$cID."'");
          tep_db_query("UPDATE `".'faq_sort'."` set `updated_at` = now() WHERE `id` = '".$cID."' and site_id='".$site_id."'");
          tep_set_faq_category_link_question_status($cID, $_GET['status'], $site_id);
        }
        tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' .  $HTTP_GET_VARS['cPath'].$c_page.'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&site_id='.$_GET['show_site']));
        break;
      case 'setflag':
        if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
        else $site_arr="";
        forward401Unless(editPermission($site_arr, $site_id));
        tep_insert_pwd_log($_GET['once_pwd'],$ocertify->auth_user);
        $c_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';

        $site_id = (isset($_GET['site_id']))?$_GET['site_id']:0;
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if ($_GET['qID']&&$site_id!=0) {
           tep_db_query("update " . TABLE_FAQ_QUESTION . " set `updated_at` = now(),`user_update` = '".$_SESSION['user_name']."' where id = '".$_GET['qID']."'");
           tep_db_query("UPDATE `".'faq_sort'."` set `updated_at` = now() WHERE `id` = '".$_GET['qID']."' and site_id='".$site_id."'");
           tep_set_faq_question_status_by_site_id($_GET['qID'], $_GET['flag'], $site_id);
          }
        }
        tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath='.$_GET['cPath'].$c_page.'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&site_id='.$_GET['show_site']));
        break;
      case 'delete_faq_confirm':
      if((isset($_GET['cID']) && $_GET['cID']) || (isset($_POST['cID']) && $_POST['cID'])){
         if($_GET['cID']){
            tep_db_query("delete from `faq_sort` where info_id = '" . $_GET['cID'] . "' and info_type = 'c'");
         }
         if(isset($_POST['cID']) && !empty($_POST['cID'])){
          foreach($_POST['cID'] as $ge_key => $ge_value){
            tep_db_query("delete from `faq_sort` where info_id = '" . $ge_value . "' and info_type = 'c'");
          }
         }
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         forward401Unless(editPermission($site_arr, $site_id));
         $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
         $dc_site = (isset($_POST['site_id']))?'&site_id='.$_POST['site_id']:'';
         if(isset($_POST['cID']) && !empty($_POST['cID'])){
            foreach($_POST['cID'] as $ge_key => $ge_value){         
          $categories = tep_get_faq_category_tree($ge_value, '', '0', '', true);
          $questions = array();
          $questions_delete = array();

          for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
            $question_ids_query = tep_db_query("select faq_category_id from " .  TABLE_FAQ_QUESTION_TO_CATEGORIES . " where faq_category_id = '" . $categories[$i]['id'] . "'");
            while ($question_ids = tep_db_fetch_array($question_ids_query)) {
              $questions[$question_ids['faq_category_id']]['categories'][] = $categories[$i]['id'];
            }
          }
          reset($questions);
          while (list($key, $value) = each($questions)) {
            $category_ids = '';
            for ($i = 0, $n = sizeof($value['categories']); $i < $n; $i++) {
              $category_ids .= '\'' . $value['categories'][$i] . '\', ';
            }
            $category_ids = substr($category_ids, 0, -2);

            $check_query = tep_db_query("select count(*) as total from " .  TABLE_FAQ_QUESTION_TO_CATEGORIES . " where faq_question_id = '" .  $key . "' and faq_category_id not in (" . $category_ids . ")");
            $check = tep_db_fetch_array($check_query);
            if ($check['total'] < '1') {
              $questions_delete[$key] = $key;
            }
          }
          for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
            tep_remove_faq_category($categories[$i]['id']);
          }

          reset($questions_delete);
          while (list($key) = each($questions_delete)) {
            tep_remove_faq_question($key);
          }
            }
         } 
         if ($_GET['cID']) {
          $faq_category_id = tep_db_prepare_input($_GET['cID']);
          tep_set_time_limit(0);
          $categories = tep_get_faq_category_tree($faq_category_id, '', '0', '', true);
          $questions = array();
          $questions_delete = array();

          for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
            $question_ids_query = tep_db_query("select faq_category_id from " .  TABLE_FAQ_QUESTION_TO_CATEGORIES . " where faq_category_id = '" . $categories[$i]['id'] . "'");
            while ($question_ids = tep_db_fetch_array($question_ids_query)) {
              $questions[$question_ids['faq_category_id']]['categories'][] = $categories[$i]['id'];
            }
          }
          reset($questions);
          while (list($key, $value) = each($questions)) {
            $category_ids = '';
            for ($i = 0, $n = sizeof($value['categories']); $i < $n; $i++) {
              $category_ids .= '\'' . $value['categories'][$i] . '\', ';
            }
            $category_ids = substr($category_ids, 0, -2);

            $check_query = tep_db_query("select count(*) as total from " .  TABLE_FAQ_QUESTION_TO_CATEGORIES . " where faq_question_id = '" .  $key . "' and faq_category_id not in (" . $category_ids . ")");
            $check = tep_db_fetch_array($check_query);
            if ($check['total'] < '1') {
              $questions_delete[$key] = $key;
            }
          }
          for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
            tep_remove_faq_category($categories[$i]['id']);
          }

          reset($questions_delete);
          while (list($key) = each($questions_delete)) {
            tep_remove_faq_question($key);
          }
         }
      }
       if((isset($_POST['faq_question_id']) && $_POST['faq_question_id']) || (isset($_POST['qID']) && $_POST['qID'])){
         if($_POST['faq_question_id']){
            tep_db_query("delete from `faq_sort` where info_id = '" .  $_POST['faq_question_id'] . "' and info_type = 'q' and site_id ='".$_POST['site_id']."'");
         }
         if(isset($_POST['qID']) && !empty($_POST['qID'])){
          foreach($_POST['qID'] as $ge_key => $ge_value){
            tep_db_query("delete from `faq_sort` where info_id = '" . $ge_value . "' and info_type = 'q'");
          }
         }
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         forward401Unless(editPermission($site_arr, $site_id));
         $d_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
         $d_site = (isset($_POST['site_id']))?'&site_id='.$_POST['site_id']:'';
         if(isset($_POST['qID']) && !empty($_POST['qID'])){
           foreach($_POST['qID'] as $ge_key => $ge_value){    
           $faq_question_id = tep_db_prepare_input($ge_value);
            tep_db_query("delete from " .  TABLE_FAQ_QUESTION_TO_CATEGORIES . " where faq_question_id = '" . tep_db_input($faq_question_id) . "'");
           $question_categories_query = tep_db_query("select count(*) as total from " .  TABLE_FAQ_QUESTION_TO_CATEGORIES .  " where faq_question_id = '" . tep_db_input($faq_question_id) . "'");
           $question_categories = tep_db_fetch_array($question_categories_query);
           if ($question_categories['total'] == '0') {
             tep_remove_faq_question($faq_question_id);
           } 
           }
         }
         if(($_POST['faq_question_id']) && (is_array($_POST['question_categories']))){
           $faq_question_id = tep_db_prepare_input($_POST['faq_question_id']);
           $question_categories = $_POST['question_categories'];
           for ($i = 0, $n = sizeof($question_categories); $i < $n; $i++) {
            tep_db_query("delete from " . 
                TABLE_FAQ_QUESTION_TO_CATEGORIES . " 
                where faq_question_id = '" . tep_db_input($faq_question_id) . "' 
                and faq_category_id = '" . tep_db_input($question_categories[$i]) . "'");
           }
           $question_categories_query = tep_db_query("select count(*) as total from " .
               TABLE_FAQ_QUESTION_TO_CATEGORIES . 
               " where faq_question_id = '" . tep_db_input($faq_question_id) . "'");
           $question_categories = tep_db_fetch_array($question_categories_query);
           if ($question_categories['total'] == '0') {
             tep_remove_faq_question($faq_question_id);
           }
         }
      } 
         tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath.$d_page));
         break;
      case 'insert_faq_question':
      case 'update_faq_question':
         $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         forward401Unless(editPermission($site_arr, $site_id));
         $faq_question_id = tep_db_prepare_input($_POST['faq_question_id']);
         $sort_order = tep_db_prepare_input($_POST['sort_order']);
         $sql_data_array = array('sort_order' => $sort_order);

         if($_GET['action'] == 'insert_faq_question') {
           $insert_sql_data = array('updated_at' => 'now()',
		                    'user_update' => $_POST['user_update'],
				    'user_added' => $_POST['user_added'],
                                    'created_at' => 'now()');
           $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
           tep_db_perform(TABLE_FAQ_QUESTION, $sql_data_array);
           $faq_question_id = tep_db_insert_id();
           $faq_c2q_arr = array('faq_category_id' => $current_category_id,
                                'faq_question_id' => $faq_question_id);
           tep_db_perform(TABLE_FAQ_QUESTION_TO_CATEGORIES, $faq_c2q_arr);
         }else{
           $update_sql_data = array('updated_at' => 'now()','user_update' => $_POST['user_update']);
           $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
           tep_db_perform(TABLE_FAQ_QUESTION, $sql_data_array, 'update',
               'id = \'' . $faq_question_id . '\'');
         }
         $sql_data_array = array(
            'romaji' => str_replace(array('/','_'),'-',tep_db_prepare_input($_POST['romaji'])),
            'ask' => tep_db_prepare_input($_POST['ask']),
            'keywords' => tep_db_prepare_input($_POST['keywords']),
            'answer' => tep_db_prepare_input($_POST['answer']),
              );
         if($_GET['action'] == 'insert_faq_question' || ($_GET['action'] ==
               'update_faq_question' &&
               !tep_faq_question_description_exist($faq_question_id,$site_id))) {
           if($_GET['action'] == 'insert_faq_question'){
            $insert_sql_data = array('faq_question_id' => $faq_question_id,
                                     'site_id' => $site_id);
           }else{
            $insert_sql_data = array('faq_question_id' => $faq_question_id,
                                     'site_id' => $site_id);
           }
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
            $faq_q_sql_id = tep_db_insert_id();
            $faq_q_id = tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_QUESTION_DESCRIPTION." where id =".$faq_q_sql_id));
            $faq_q_to_c = tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_QUESTION_TO_CATEGORIES." where faq_question_id =".$faq_q_id['faq_question_id']));
            $c_sql = "SELECT * FROM `faq_categories` c, `faq_categories_description` cd WHERE c.id =".$faq_q_to_c['faq_category_id'];
            $faq_search = tep_db_fetch_array(tep_db_query($c_sql));
            if($faq_search['romaji'] == '' && $faq_search['title'] == '' && $faq_search['keywords'] == '' && $faq_search['description']){
               $search_text = '';
            }else{
               $search_text = $faq_search['romaji'].'>>>'.$faq_search['title'].'>>>'.$faq_search['keywords'].'>>>'.$faq_search['description'];
            }
            $faq_sort_array = array(
                'site_id' => $_POST['site_id'],
                'title'   => $_POST['ask'],
                'sort_order'=>$_POST['sort_order'],
                'parent_id'=>$_GET['cPath'],
                'info_id' => $faq_question_id,
                'is_show' => '1',
                'info_type'=> 'q',
                'search_text' => $search_text
                );
            print_r($faq_sort_array);
            tep_db_perform('faq_sort',$faq_sort_array);
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
            set_time_limit(0);
            $page_sort_order = tep_get_rownum_faq_question($current_category_id,$faq_question_id, $site_id,$_GET['search']);
            $select_page = intval((intval($page_sort_order)-1)/MAX_DISPLAY_FAQ_ADMIN)+1;
         if(isset($_GET['rdirect'])){
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .  '&qID=' .  $faq_question_id.'&site_id='.$_GET['site_id'].'&page='.$_GET['page'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type']));
         }else{
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .'&qID=' .  $faq_question_id.'&site_id='.$_GET['site_id'].'&page='.$_GET['page'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type']));
         }

                break;
      case 'insert_faq_category':
      case 'update_faq_category':
         $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         forward401Unless(editPermission($site_arr, $site_id));
         $faq_category_id = tep_db_prepare_input($_POST['faq_category_id']);
         $sort_order = tep_db_prepare_input($_POST['sort_order']);
         $sql_data_array = array('sort_order' => $sort_order);

         if($_GET['action'] == 'insert_faq_category') {
           $insert_sql_data = array('parent_id' => $current_category_id,
		                    'user_added'=> $_POST['user_added'],
				    'user_update'=>$_POST['user_update'],
                                    'updated_at' => 'now()',
                                    'created_at' => 'now()');
           $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
           tep_db_perform(TABLE_FAQ_CATEGORIES, $sql_data_array);
           $faq_category_id = tep_db_insert_id();
         }else{
           $update_sql_data = array('updated_at' => 'now()','user_update' => $_POST['user_update']);
           $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
           tep_db_perform(TABLE_FAQ_CATEGORIES, $sql_data_array, 'update', 'id = \'' . $faq_category_id . '\'');
         }
         $sql_data_array = array(
            'romaji' => str_replace(array('/','_'),'-',tep_db_prepare_input($_POST['romaji'])),
            'title' => tep_db_prepare_input($_POST['title']),
            'keywords' => tep_db_prepare_input($_POST['keywords']),
            'description' => tep_db_prepare_input($_POST['description']),
              );
         if($_GET['action'] == 'insert_faq_category' || ($_GET['action'] == 'update_faq_category' && !tep_faq_categories_description_exist($faq_category_id,$site_id))) {
            if($_GET['action'] == 'insert_faq_category'){
            $insert_sql_data = array('faq_category_id' => $faq_category_id,
                                     'site_id' => $site_id);
            }else{
            $insert_sql_data = array('faq_category_id' => $faq_category_id,
                                     'site_id' => $site_id);
            }
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
            $faq_c_sql_id = tep_db_insert_id();
            $faq_category_id = tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_CATEGORIES_DESCRIPTION." where id =".$faq_c_sql_id));
            $c_sql = "SELECT * FROM `faq_categories` c, `faq_categories_description` cd WHERE c.id =".$faq_category_id['faq_category_id'];
            $faq_search = tep_db_fetch_array(tep_db_query($c_sql));
            if($faq_search['romaji'] == '' && $faq_search['title'] == '' && $faq_search['keywords'] == '' && $faq_search['description'] == ''){
               $search_text = '';
            }else{
               $search_text = $faq_search['romaji'].'>>>'.$faq_search['title'].'>>>'.$faq_search['keywords'].'>>>'.$faq_search['description'];
            }
            $faq_sort_array = array(
                'site_id' => $_POST['site_id'],
                'title'   => $_POST['title'],
                'sort_order'=>$_POST['sort_order'],
                'parent_id'=>$_GET['cPath'],
                'info_id' => $faq_category_id,
                'is_show' => '1',
                'info_type'=> 'c',
                'search_text' => $search_text
                );
            tep_db_perform('faq_sort',$faq_sort_array);
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
           tep_db_perform(TABLE_FAQ_CATEGORIES_DESCRIPTION, $sql_data_array, 'update','faq_category_id =\''.$faq_category_id.'\' and site_id = \''.$site_id.'\'');
           $faq_sort_array = array(
               'title' => $_POST['title'],
           'sort_order'=> $_POST['sort_order']
               );
         }
         if(isset($_GET['rdirect'])){
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .  '&cID=' .  $faq_category_id.'&site_id='.$_GET['site_id'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page='.$_GET['page']));
         }else{
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .  '&cID=' .  $faq_category_id.'&site_id='.$_GET['site_id'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page='.$_GET['page']));
         }
        break;
    }
  }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>
<?php 
$site_id = isset($_GET['site_id']) ? $_GET['site_id']:0;
  if((isset($_GET['cPath']) && $_GET['cPath']=="" && isset($_GET['cID']) && $_GET['cID']!="")){
  echo HEADING_TITLE;
  }else if(isset($_GET['cPath']) && $_GET['cPath']!=""){
$faq_query = tep_db_query("select title from ".TABLE_FAQ_CATEGORIES_DESCRIPTION." where faq_category_id='".$_GET['cPath']."'");
 $faq_array = tep_db_fetch_array($faq_query);
  echo HEADING_TITLE.$faq_array['title']; 
  }else{
 echo   HEADING_TITLE;
  }
?>
</title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script language="javascript">
$(document).ready(function() { 
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_faq').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           <?php //回车?>
        if ($('#show_faq').css('display') != 'none') {
            if (o_submit_single){
              cid = $("#cid").val();
              $("#button_save").trigger("click");
            }
        }
     }

     if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_faq').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_faq').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});

<?php //删除分类?>
function delete_fix_faq_category(param_str)
{
  param_str = decodeURIComponent(param_str);
  
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
    async: false,
    success: function(msg) {
      var tmp_msg_arr = msg.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      <?php
      if ($ocertify->npermission > 15) {
      ?>
      if (confirm('<?php echo TEXT_DEL_NEWS;?>')) {
        window.location.href = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_FAQ;?>'+'?action=delete_faq_confirm'+'&'+param_str;  
      }
      <?php
      } else {
      ?>
      if (confirm('<?php echo TEXT_DEL_NEWS;?>')) {
        if (tmp_msg_arr[0] == '0') {
          window.location.href = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_FAQ;?>'+'?action=delete_faq_confirm'+'&'+param_str;  
        } else {
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_FAQ;?>'+'?action=delete_faq_confirm&'+param_str),
              async: false,
              success: function(msg_info) {
                window.location.href = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_FAQ;?>'+'?action=delete_faq_confirm'+'&'+param_str;  
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
          }
        }
      } 
      <?php
      }
      ?>
    }
  });
}
function delete_select_faq(cID,qID,c_permission){
        sel_num = 0;
        if(typeof(eval(del_faq.elements[cID]))!= "undefined"){
        if (document.del_faq.elements[cID].length == null) {
         if (document.del_faq.elements[cID].checked == true){
               sel_num = 1;
            }
         } else {
         for (i = 0; i < document.del_faq.elements[cID].length; i++) {
             if(document.del_faq.elements[cID][i].checked == true) {
                   sel_num = 1;
                   break;
                  }
               }
         }
        }
        if(typeof(eval(del_faq.elements[qID]))!= "undefined"){
        if (document.del_faq.elements[qID].length == null) {
         if (document.del_faq.elements[qID].checked == true){
               sel_num = 1;
            }
         } else {
         for (i = 0; i < document.del_faq.elements[qID].length; i++) {
             if(document.del_faq.elements[qID][i].checked == true) {
                   sel_num = 1;
                   break;
                  }
               }
         }
        }
       if (sel_num == 1) {
         if (confirm('<?php echo TEXT_DEL_NEWS;?>')) {
           if (c_permission == 31) {
             document.forms.del_faq.submit(); 
           } else {
             $.ajax({
              url: 'ajax_orders.php?action=getallpwd',   
              type: 'POST',
              dataType: 'text',
              data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
              async: false,
              success: function(msg) {
                var tmp_msg_arr = msg.split('|||'); 
                var pwd_list_array = tmp_msg_arr[1].split(',');
                if (tmp_msg_arr[0] == '0') {
                  document.forms.del_faq.submit(); 
                } else {
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.del_faq.action),
                      async: false,
                      success: function(msg_info) {
                        document.forms.del_faq.submit(); 
                      }
                    }); 
                  } else {
                    document.getElementsByName('customers_action')[0].value = 0;
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                  }
                }
              }
            });
           }
          }else{
             document.getElementsByName('customers_action')[0].value = 0;
          }
         } else {
            document.getElementsByName('customers_action')[0].value = 0;
             alert('<?php echo TEXT_NEWS_MUST_SELECT;?>'); 
          }
}
function save_del(){
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
    async: false,
    success: function(msg) {
      var tmp_msg_arr = msg.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      <?php
      if ($ocertify->npermission > 15) {
      ?>
      if (confirm('<?php echo TEXT_DEL_NEWS;?>')) {
        document.forms.question.submit(); 
      }
      <?php
      } else {
      ?>
      if (confirm('<?php echo TEXT_DEL_NEWS;?>')) {
        if (tmp_msg_arr[0] == '0') {
          document.forms.question.submit(); 
        } else {
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.question.action),
              async: false,
              success: function(msg_info) {
                document.forms.question.submit(); 
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
          }
        }
      } 
      <?php
      }
      ?>
    }
  });       
  
}
function all_select_faq(cID,qID){
        var check_flag = document.del_faq.all_check.checked;
           if (document.del_faq.elements[cID]) {
                if (document.del_faq.elements[cID].length == null){
                      if (check_flag == true) {
                        document.del_faq.elements[cID].checked = true;
                       } else {
                         document.del_faq.elements[cID].checked = false;
                       }
                 } else {
                 for (i = 0; i < document.del_faq.elements[cID].length; i++){
                      if(!document.del_faq.elements[cID][i].disabled) { 
                            if (check_flag == true) {
                               document.del_faq.elements[cID][i].checked = true;
                            } else {
                               document.del_faq.elements[cID][i].checked = false;
                            }
                      }
                  }
                }
            }
           if (document.del_faq.elements[qID]) {
                if (document.del_faq.elements[qID].length == null){
                      if (check_flag == true) {
                        document.del_faq.elements[qID].checked = true;
                       } else {
                         document.del_faq.elements[qID].checked = false;
                       }
                 } else {
                 for (i = 0; i < document.del_faq.elements[qID].length; i++){
                      if(!document.del_faq.elements[qID][i].disabled) { 
                            if (check_flag == true) {
                               document.del_faq.elements[qID][i].checked = true;
                            } else {
                               document.del_faq.elements[qID][i].checked = false;
                            }
                      }
                  }
                }
            }

}
function faq_change_action(r_value,r_str,q_str) {
 if (r_value == '1') {
    delete_select_faq(r_str,q_str,'<?php echo $ocertify->npermission;?>');
  }
}
<?php //显示/关闭分类树?>
function show_faq(ele,cID,qID,page,action_sid,faq_id,info_type){
 site_id = '<?php echo (isset($_GET['site_id'])&&$_GET['site_id']!=''?($_GET['site_id']):'-1');?>';
 var cPath  = $("#cPath").val();
 var search = $('#search').val();
 var sort = $("#sort").val(); 
 var type = $("#type").val();
 $.ajax({
 url: 'ajax.php?&action=edit_faq',
 data: {cID:cID,qID:qID,page:page,site_id:site_id,search:search,action_sid:action_sid,cPath:cPath,sort:sort,type:type,info_type:info_type,faq_id:faq_id} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_faq").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(cID != -1 || qID != -1){
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_faq').height()){
offset = ele.offsetTop+$("#show_faq_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_faq').height()) > $('.box_warp').height())&&($('#show_faq').height()<ele.offsetTop+parseInt(head_top)-$("#show_faq_list").position().top-1)) {
offset = ele.offsetTop+$("#show_faq_list").position().top-1-$('#show_faq').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_faq_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_faq_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_faq').height()) > $('.box_warp').height())&&($('#show_faq').height()<ele.offsetTop+parseInt(head_top)-$("#show_faq_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_faq_list").position().top-1-$('#show_faq').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_faq_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_faq_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_faq').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_faq').height()) > $('.box_warp').height())&&($('#show_faq').height()<ele.offsetTop+parseInt(head_top)-$("#show_faq_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_faq_list").position().top-1-$('#show_faq').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_faq_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_faq_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_faq_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_faq').css('top',offset);
}
}
box_warp_height = box_warp_height + $('#show_faq').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(cID == -1 || qID == -1){
  $('#show_faq').css('top',$('#show_faq_list').offset().top);
}
$('#show_faq').css('z-index','1');
$('#show_faq').css('left',leftset);
$('#show_faq').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
function hidden_info_box(){
   $('#show_faq').css('display','none');
}


function display(){
    var categories_tree = document.getElementById('categories_tree'); 
    offset = $("#categories_block").offset();
      if(categories_tree.style.display == 'none'){
           categories_tree.style.top = offset.top + 'px';
           categories_tree.style.display = 'block';
              }else{
                    categories_tree.style.display = 'none';
                       }
         }
<?php //提交动作?>
function check_faq_form(c_permission, f_type)
{
   if (c_permission == 31) {
     if (f_type == 0) {
       document.forms.del_faq_categories.submit(); 
     } else if (f_type == 1) {
       document.forms.question.submit(); 
     }
   } else {
     $.ajax({
       url: 'ajax_orders.php?action=getallpwd',   
       type: 'POST',
       dataType: 'text',
       data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
       async: false,
       success: function(msg) {
         var tmp_msg_arr = msg.split('|||'); 
         var pwd_list_array = tmp_msg_arr[1].split(',');
         if (tmp_msg_arr[0] == '0') {
           if (f_type == 0) {
             document.forms.del_faq_categories.submit(); 
           } else if (f_type == 1) {
             document.forms.question.submit(); 
           }
         } else {
           var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
           var form_action_str = ''; 
           if (f_type == 0) {
             form_action_str = document.forms.del_faq_categories.action; 
           } else if (f_type == 1) {
             form_action_str = document.forms.question.action; 
           }
           if (in_array(input_pwd_str, pwd_list_array)) {
             $.ajax({
               url: 'ajax_orders.php?action=record_pwd_log',   
               type: 'POST',
               dataType: 'text',
               data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(form_action_str),
               async: false,
               success: function(msg_info) {
                 if (f_type == 0) {
                   document.forms.del_faq_categories.submit(); 
                 } else if (f_type == 1) {
                   document.forms.question.submit(); 
                 }
               }
             }); 
           } else {
             alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
           }
         }
       }
     });
   }
}
</script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/cPath=[^&]+/',$belong,$belong_array);
if($belong_array[0][0] != '' && $belong_array[0][0] != 'cPath=0'){

  $belong = $href_url.'?'.$belong_array[0][0];
}else{

  $belong = $href_url;
}

require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<div id="categories_tree">
                <?php
                  require(DIR_WS_CLASSES . 'faq_tree.php');
                  $osC_FaqTree = new osC_FaqTree;
                  echo $osC_FaqTree->buildTree();
                ?>
                </div>
<!-- body //-->
<input id="show_info_id" type="hidden" name="show_info_id" value="show_faq">
<div id="show_faq" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top" id="categories_right_td"><div class="box_warp"><?php echo $notes;?><div
    class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2" >
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading" height="40" width="50%">
            <?php echo HEADING_TITLE; ?>
            <?php
            echo tep_get_faq_breadcreumb_by_cpath($_GET['cPath'],$_GET['site_id']);
            ?>
            </td>
            <td class="smallText" align="right">
            <?php 
            //search form for faq
            echo tep_draw_form('search',FILENAME_FAQ,'site_id='.$_GET['site_id'].'&page='.$_GET['page'],'get')."\n";
            ?>
            <div class="faq_search">
            <?php
            echo tep_draw_hidden_field('site_id',isset($_GET['site_id'])?$_GET['site_id']:'');
            echo HEADING_TITLE_SEARCH. ' ' .
            tep_draw_input_field('search',isset($_GET['search'])?$_GET['search']:'');
            ?>
            <input type="submit" value="<?php echo IMAGE_SEARCH;?>">
            </div>
            </form>
            </td>
            <td class="smallText" align="right">
            <div class="faq_gotomenu">
            <?php //goto menu start?>              
              <div id="gotomenu">
                <a href="javascript:void(0)" onclick="display()"><?php echo CATEGORY_TREE_SELECT_TEXT;?></a>
                
              </div>
            </div>
<?php
  //end menu
?>
            </td>
          </tr>
        </table></td>
      </tr>
      <tr id="categories_block">
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
            <?php tep_show_site_filter(FILENAME_FAQ,true,array(0));?>
            <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_faq_list">
              <tr>
                <td >
<?php 
                echo '<input type="hidden" id="search" value="'.$_GET['search'].'">';
                echo '<input type="hidden" id="cPath" value="'.$_GET['cPath'].'">';
                echo '<input type="hidden" id="sort" value="'.$_GET['sort'].'">';
                echo '<input type="hidden" id="type" value="'.$_GET['type'].'">';
                 if(!isset($_GET['type']) || $_GET['type'] == ''){
                       $_GET['type'] = 'asc';
                 }
                if($faq_type == ''){
                      $faq_type = 'asc';
                }
                if(!isset($_GET['sort']) || $_GET['sort'] == ''){
                    $faq_str = 'info_type'; 
                    $faq_qid_str = 'c.sort_order,c.ask,c.faq_question_id';
                }else if($_GET['sort'] == 'site_romaji'){
                  if($_GET['type'] == 'desc'){
                    $faq_str = 'site_id desc';
                    $faq_type = 'asc';
                    }else{
                    $faq_str = 'site_id asc';
                    $faq_type = 'desc';
                    }
                }else if($_GET['sort'] == 'title'){
                  if($_GET['type'] == 'desc'){
                    $faq_str = 'title desc';
                    $faq_type = 'asc';
                    }else{
                    $faq_str = 'title asc';
                    $faq_type = 'desc';
                    }
                }else if($_GET['sort'] == 'is_show'){
                  if($_GET['type'] == 'desc'){
                    $faq_str = 'is_show desc';
                    $faq_type = 'asc';
                    }else{
                    $faq_str = 'is_show asc';
                    $faq_type = 'desc';
                    }
                }else if($_GET['sort'] == 'updated_at'){
                  if($_GET['type'] == 'desc'){
                    $faq_str = 'updated_at desc';
                    $faq_type = 'asc';
                    }else{
                    $faq_str = 'updated_at asc';
                    $faq_type = 'desc';
                    }
                }
                if($_GET['sort'] == 'site_romaji'){
                  if($_GET['type'] == 'desc'){
                     $faq_site_romaji = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                   }else{
                     $faq_site_romaji = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                   }
                } 
                if($_GET['sort'] == 'title'){
                  if($_GET['type'] == 'desc'){
                     $faq_title = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                   }else{
                     $faq_title = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                   }
                } 
                if($_GET['sort'] == 'is_show'){
                  if($_GET['type'] == 'desc'){
                     $faq_is_show = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                   }else{
                     $faq_is_show = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                   }
                } 
                if($_GET['sort'] == 'updated_at'){
                   if($_GET['type'] == 'desc'){
                     $faq_updated_at = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                   }else{
                     $faq_updated_at = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                   }
                }
                $faq_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
                $notice_box = new notice_box('','',$faq_table_params);
                $faq_table_row = array();
                $faq_title_row = array();
                $faq_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" onclick="all_select_faq(\'cID[]\',\'qID[]\');">');
                if(isset($_GET['sort']) && $_GET['sort'] == 'site_romaji'){
                $faq_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_FAQ,'sort=site_romaji&search='.$_GET['search'].'&cPath='.$_GET['cPath'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&cID='.$_GET['cID'].'&ype='.$faq_type).'">'.TABLE_FAQ_SITE.$faq_site_romaji.'</a>');
                }else{
                $faq_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_FAQ,'sort=site_romaji&search='.$_GET['search'].'&cPath='.$_GET['cPath'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&cID='.$_GET['cID'].'&type=desc').'">'.TABLE_FAQ_SITE.$faq_site_romaji.'</a>');
                }
                if(isset($_GET['sort']) && $_GET['sort'] == 'title'){
                $faq_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_FAQ,'sort=title&search='.$_GET['search'].'&cPath='.$_GET['cPath'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&cID='.$_GET['cID'].'&type='.$faq_type).'">'.TABLE_FAQ_CATEGORY_TITLE.$faq_title.'</a>');
                }else{
                $faq_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_FAQ,'sort=title&search='.$_GET['search'].'&cPath='.$_GET['cPath'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&cID='.$_GET['cID'].'&type=desc').'">'.TABLE_FAQ_CATEGORY_TITLE.$faq_title.'</a>');
                }
                if(isset($_GET['sort']) && $_GET['sort'] == 'is_show'){
                $faq_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="center"','text' => '<a href="'.tep_href_link(FILENAME_FAQ,'sort=is_show&search='.$_GET['search'].'&cPath='.$_GET['cPath'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&cID='.$_GET['cID'].'&type='.$faq_type).'">'.TABLE_FAQ_CATEGORY_IS_SHOW.$faq_is_show.'</a>');
                }else{
                $faq_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="center"','text' => '<a href="'.tep_href_link(FILENAME_FAQ,'sort=is_show&search='.$_GET['search'].'&cPath='.$_GET['cPath'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&cID='.$_GET['cID'].'&type=desc').'">'.TABLE_FAQ_CATEGORY_IS_SHOW.$faq_is_show.'</a>');
                }
                if(isset($_GET['sort']) && $_GET['sort'] == 'updated_at'){
                $faq_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_FAQ,'sort=updated_at&search='.$_GET['search'].'&cPath='.$_GET['cPath'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&cID='.$_GET['cID'].'&type='.$faq_type).'">'.TABLE_HEADING_ACTION.$faq_updated_at.'</a>');
                }else{
                $faq_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_FAQ,'sort=updated_at&search='.$_GET['search'].'&cPath='.$_GET['cPath'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&cID='.$_GET['cID'].'&type=desc').'">'.TABLE_HEADING_ACTION.$faq_updated_at.'</a>');
                }
                $faq_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $faq_title_row);
                  // faq category list
                  if($_GET['page'] == '') { 
                    $_GET['page'] = '1'; 
                  }
                  $faq_category_count = 0;
                  $rows = 0;
                  $sql_search_where = '';
                  if(isset($_GET['search'])&&$_GET['search']!=''){
                    $sql_search_where = " and search_text like '%".$_GET['search']."%' ";
                    $faq_category_query_raw = "select * from faq_sort where 1 ".  $sql_search_where." and ".$sql_site_where."  and parent_id = '".$current_category_id."' order by info_type asc,".$faq_str; 
                  }else{
                    $faq_category_query_raw = "select * from faq_sort where parent_id
                      = '".$current_category_id."' and ".$sql_site_where." order by
                      info_type asc,".$faq_str; 
                  }
                  $c_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
                  $faq_split = new splitPageResults($_GET['page'],MAX_DISPLAY_FAQ_ADMIN, $faq_category_query_raw,$faq_query_number);
                  $faq_category_query = tep_db_query($faq_category_query_raw);
                  $faq_category_num = tep_db_num_rows($faq_category_query);
                  while($faq_category = tep_db_fetch_array($faq_category_query)){
                    if(!isset($_GET['s_id'])||$_GET['s_id']==''){
                      $_GET['s_id'] = $faq_category['id'];
                    }
                    if($faq_category['info_type'] == 'c'){
                    $faq_count++;
                    $rows++;

                    $faq_info_arr = array('id','parent_id','sort_order','name','site_id');
                    if(isset($_GET['search']) && $_GET['search']){
                        $cPath= $faq_category['parent_id'];
                    }

                    // row color 
                    $even = 'dataTableSecondRow';
                    $odd = 'dataTableRow';
                    if(isset($nowColor) && $nowColor == $odd) {
                      $nowColor = $even;
                    }else{
                      $nowColor = $odd;
                    }
                    if($faq_category['id'] == $_GET['s_id']){
                      $faq_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
                    }else{
                      $faq_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
                    }
                    $faq_info = array();
                    if(in_array($faq_category['site_id'],$site_array)){
                    $faq_checkbox = '<input type="checkbox" name="cID[]" value="'.$faq_category['info_id'].'">';
                    }else{
                    $faq_checkbox = '<input type="checkbox" disabled="disabled" name="cID[]" value="'.$faq_category['info_id'].'">';
                    }
                    $faq_info[] = array(
                        'params' => 'class="dataTableContent"',
                        'text'   => $faq_checkbox 
                        );
                    if((isset($faq_info)&&is_object($faq_info))&& ($faq_category['info_id'] == $faq_info->info_id)){
                      $onclick = 'onclick="document.location.herf=\''.tep_href_link(FILENAME_FAQ, tep_get_faq_path($faq_category['id']).'&site_id='.$_GET['site_id']) . '\'" ';
                    }else{
                      $onclick = 'onclick="document.location.href=\''.tep_href_link(FILENAME_FAQ,'cPath='.$_GET['cPath'].  '&site_id='.$_GET['site_id'].'&qID='.$_GET['qID'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page='.$_GET['page'].'&s_id='.$faq_category['id']). '\'" ';
                    }
                    $faq_info[] = array(
                        'params' => 'class="dataTableContent"'.$onclick,
                        'text'   => get_romaji_by_site_id($faq_category['site_id'])
                        );
                    if(in_array($faq_category['site_id'],$site_array)){
                      $faq_file = '<a href="'.tep_href_link(FILENAME_FAQ, tep_get_faq_path($faq_category['info_id']).'&site_id='.$_GET['site_id']). '">' .  tep_image(DIR_WS_ICONS.'folder.gif',ICON_FOLDER) .'</a>';
                    }else{
                      $faq_file = tep_image(DIR_WS_ICONS.'folder.gif',ICON_FOLDER,'','','disabled="disabled"');
                    }
                    $faq_info[] = array(
                        'params' => 'class="dataTableContent"'.$onclick,
                        'text'   => $faq_file.'&nbsp; '.$faq_category['title']
                        );
                    //faq category is show 
                    if($faq_category['is_show']=='1'){
                      if(!in_array($faq_category['site_id'],$site_array)){
                        $faq_info[] = array(
                            'params' => 'align="center"',
                            'text'   =>  '&nbsp;'.tep_image(DIR_WS_IMAGES .  'icon_status_green.gif', '').'&nbsp;&nbsp;'. tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', '')
                            );
                      }else{
                        $faq_info[] = array(
                            'params' => 'align="center"',
                            'text'   => '<a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=toggle&cID='.  $faq_category['info_id'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&status=1&cPath='.  $HTTP_GET_VARS['cPath'].'&show_site='.$_GET['site_id'].'&site_id='.$faq_category['site_id'].$c_page) .'\', \''.$ocertify->npermission.'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', '') .'</a> <a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=toggle&cID='.  $faq_category['info_id'].'&show_site='.$_GET['site_id'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&status=0&cPath='.  $HTTP_GET_VARS['cPath'].'&site_id='.$faq_category['site_id'].$c_page) .'\', \''.$ocertify->npermission.'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', '') .'</a>'
                            );
                      }
                    }else{
                      if(!in_array($faq_category['site_id'],$site_array)){
                        $faq_info[] = array(
                            'params' => 'align="center"',
                            'text'   => '&nbsp;'.  tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '') .'&nbsp;&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', '') 
                            );
                      }else{
                        $faq_info[] = array(
                            'params' => 'align="center"',
                            'text'   => '<a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=toggle&cID='.  $faq_category['info_id'].'&show_site='.$_GET['site_id'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'status=1&cPath='.  $HTTP_GET_VARS['cPath'].'&site_id='.$faq_category['site_id'].$c_page) .'\', \''.$ocertify->npermission.'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', '') .'</a> <a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=toggle&cID='.  $faq_category['info_id'].'&show_site='.$_GET['site_id'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'status=0&cPath='.  $HTTP_GET_VARS['cPath'].'&site_id='.$faq_category['site_id'].$c_page) .'\', \''.$ocertify->npermission.'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', '') .'</a>'
                            );
                      }
                    }
                    $faq_categories_update = tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_CATEGORIES." where id = '".$faq_category['info_id']."'"));
                    $faq_info[] = array(
                        'params' => 'class="dataTableContent" align="right"',
                        'text'   => '<a href="javascript:void(0)" onclick="show_faq(this,'.$faq_category['info_id'].',\'\','.$_GET['page'].','.$faq_category['site_id'].','.$faq_category['id'].',\'c\')">'.tep_get_signal_pic_info($faq_categories_update['updated_at'] != null?$faq_categories_update['updated_at']:$faq_categories_update['created_at']). '</a>'
                        );
                    $faq_table_row[] = array('params' => $faq_params, 'text' => $faq_info);
                    }
  if($faq_category['info_type'] == 'q'){
  $faq_count++;
  $rows++;

  if(isset($_GET['search'])&&$_GET['search']){
     $cPath=$faq_category['info_id'];
   }
  
                    $even = 'dataTableSecondRow';
                    $odd = 'dataTableRow';
                    if(isset($nowColor) && $nowColor == $odd) {
                      $nowColor = $even;
                    }else{
                      $nowColor = $odd;
                    }
                    if($faq_category['id'] == $_GET['s_id']){
                      $faq_qid_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ';
                      $onclick_qid = 'onclick="document.location.herf=\''.tep_href_link(FILENAME_FAQ, 'cPath='.$cPath.($_GET['page'] ? ('&page=' . $_GET['page']): '' ) .  '&s_id='.$faq_category['id']) . '\'"';
                    }else{
                      $faq_qid_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
                      $onclick_qid = 'onclick="document.location.href=\''.tep_href_link(FILENAME_FAQ, 'cPath='.$cPath.($_GET['page'] ? ('&page=' . $_GET['page']): '' ) .  '&s_id='.$faq_category['id']) .  '\'" ';
                    }
                    $faq_qid_info = array();
                    if(in_array($faq_category['site_id'],$site_array)){
                        $faq_qid_checkbox =  '<input type="checkbox" name="qID[]" value="'.$faq_category['info_id'].'">';
                    }else{
                        $faq_qid_checkbox =  '<input type="checkbox" disabled="disabled" name="qID[]" value="'.$faq_category['info_id'].'">';
                    }
                    $faq_qid_info[] = array(
                        'params' => 'class="dataTableContent"',
                        'text'   => $faq_qid_checkbox 
                        );
                    if((isset($qInfo)&&is_object($qInfo))&& ($faq_category['info_id'] == $qInfo->info_id)){
                       $onclick_qid_ask = 'onclick="document.location.herf=\''.tep_href_link(FILENAME_FAQ,'cPath='.$_GET['cPath'].  '&site_id='.$_GET['site_id'].'&qID='.$_GET['qID'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page='.$_GET['page'].'&s_id='.$faq_category['id']) . '\'"';
                    }else{
                       $onclick_qid_ask = 'onclick="document.location.href=\''.tep_href_link(FILENAME_FAQ,'cPath='.$_GET['cPath'].  '&site_id='.$_GET['site_id'].'&qID='.$_GET['qID'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page='.$_GET['page'].'&s_id='.$faq_category['id']) . '\'"';
                    }
                    $faq_qid_info[] = array(
                        'params' => 'class="dataTableContent"'.$onclick_qid.$onclick_qid_ask,
                        'text'   => get_romaji_by_site_id($faq_category['site_id'])
                        );
                    $faq_qid_info[] = array(
                        'params' => 'class="dataTableContent"'.$onclick_qid_ask,
                        'text'   => $faq_category['title']
                        );
                    if($faq_category['is_show']=='1'){
                    if(!in_array($faq_category['site_id'],$site_array)){
                    $faq_qid_info[] = array(
                        'params' => 'align="center" ',
                        'text'   => '&nbsp;'.  tep_image(DIR_WS_IMAGES .  'icon_status_green.gif', '') .'&nbsp;&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', '') 
                        ); 
                    }else{
                    $faq_qid_info[] = array(
                        'params' => 'align="center" ',
                        'text'   => '<a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=setflag&qID='.  $faq_category['info_id'].'&flag=1&cPath='.  $cPath.'&site_id='.$faq_category['site_id'].$c_page.'&show_site='.$_GET['site_id'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type']) .'\', \''.$ocertify->npermission.'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', '') .'</a> <a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=setflag&qID='.  $faq_category['info_id'].'&flag=0&cPath='.  $cPath.'&site_id='.$faq_category['site_id'].$c_page.'&show_site='.$_GET['site_id'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type']) .'\', \''.$ocertify->npermission.'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', '') .'</a>'
                        );
                    }
                      }else{
                    if(!in_array($faq_category['site_id'],$site_array)){
                    $faq_info[] = array(
                        'params' => 'align="center" ',
                        'text'   => '&nbsp;'.  tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '') .'&nbsp;&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', '') 
                      );
                    }else{
                    $faq_qid_info[] = array(
                        'params' => 'align="center" ',
                        'text'   => '<a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=setflag&qID='.  $faq_category['info_id'].'&show_site='.$_GET['site_id'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&flag=1&cPath='.  $cPath.'&site_id='.$faq_category['site_id']).'\', \''.$ocertify->npermission.'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', '') .'</a>&nbsp;<a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=setflag&qID='.  $faq_category['info_id'].'&flag=0&cPath='.  $cPath.'&show_site='.$_GET['site_id'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&site_id='.$faq_category['site_id'].$c_page) .'\', \''.$ocertify->npermission.'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', '') .'</a>'
                        );
                    }
                      }
                    $faq_question_update = tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_QUESTION." where id = '".$faq_category['info_id']."'"));
                    $faq_qid_info[] = array(
                        'params' => 'class="dataTableContent" align="right"',
                        'text'   => '<a href="javascript:void(0)"
                        onclick="show_faq(this,\'\','.$faq_category['info_id'].','.$_GET['page'].','.$faq_category['site_id'].','.$faq_category['id'].',\'q\')">'.tep_get_signal_pic_info($faq_question_update['updated_at'] != null?$faq_question_update['updated_at']:$faq_question_update['created_at']). '</a>'
                        );
                    $faq_table_row[] = array('params' => $faq_qid_params, 'text' => $faq_qid_info);
                     }
                  }
                  $faq_form = tep_draw_form('del_faq',FILENAME_FAQ,'action=delete_faq_confirm');
                  $notice_box->get_form($faq_form);
                  $notice_box->get_contents($faq_table_row);
                  $notice_box->get_eof(tep_eof_hidden());
                  echo $notice_box->show_notice();
                 ?>
                </table>
                </td>
              </tr>
              </table>
              <table width="100%" cellspacing="0" cellpadding="2" border="0"> 
              <tr>
                <td class="smallText">
                  <?php 
                    if($faq_category_num > 0){
                       if($ocertify->npermission >= 15){
                          echo '<select name="customers_action" onchange="faq_change_action(this.value,\'cID[]\',\'qID[]\');">';
                          echo '<option value="0">'.TEXT_REVIEWS_SELECT_ACTION.'</option>';
                          echo '<option value="1">'.TEXT_REVIEWS_DELETE_ACTION.'</option>';
                          echo '</select>';
                         }
                     }else{
                         echo TEXT_DATA_EMPTY;
                     }
                 ?>
                </td>
              </tr>
              <tr> 
    <td class="smallText" valign="top" align='right'>
      <div class='faq_page_text'>
      <?php echo $faq_split->display_count($faq_query_number, MAX_DISPLAY_FAQ_ADMIN, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_FAQ); ?>
      </div>
      <div class='faq_page_link'>
      <?php echo $faq_split->display_links($faq_query_number, MAX_DISPLAY_FAQ_ADMIN, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'pID'))); ?>
      </div>
    </td>
  </tr>  
  <tr>
    <td align="right">
       <?php 
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
		 echo '<div class="td_box">';
                   if((($site_array[0] != '' && $site_array[0] != 0) || $site_array[1] != '') &&!(isset($_GET['search'])&&$_GET['search']!='') ){     
                   if(isset($_GET['cPath']) && $_GET['cPath'] != ''){
                    $faq_site_id =  tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_CATEGORIES_DESCRIPTION." where faq_category_id = '".$current_category_id."'"));
                    echo '<a href="javascript:void(0)" onclick="show_faq(this,-1,\'\','.$_GET['page'].','.$faq_site_id['site_id'].')">'.tep_html_element_button(IMAGE_NEW_FAQ_CATEGORY).'</a>';
                    echo '&nbsp;';
                    echo '<a href="javascript:void(0)" onclick="show_faq(this,\'\',-1,'.$_GET['page'].','.$faq_site_id['site_id'].')">'.tep_html_element_button(IMAGE_NEW_FAQ).'</a>';
                    }else{
                    echo '<a href="javascript:void(0)" onclick="show_faq(this,-1,\'\','.$_GET['page'].',-1)">'.tep_html_element_button(IMAGE_NEW_FAQ_CATEGORY).'</a>';
                    echo '&nbsp;';
                    echo '<a href="javascript:void(0)" onclick="show_faq(this,\'\',-1,'.$_GET['page'].',-1)">'.tep_html_element_button(IMAGE_NEW_FAQ).'</a>';
                    }
                   }else{
                    echo tep_html_element_button(IMAGE_NEW_FAQ_CATEGORY,'disabled="disabled"');
                    echo '&nbsp;';
                    echo tep_html_element_button(IMAGE_NEW_FAQ,'disabled="disabled"');
                   }
                  echo '</div>';
       ?>
    </td>
  </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  // faq right info
if(isset($_GET['action'])&& $_GET['action']&&(!isset($site_id)||$site_id==0)){
  $_action = null;
}else{
  $_action = $_GET['action'];
}
?>
          </tr>
        </table></td>
      </tr>
    </table></div></div></td>
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
