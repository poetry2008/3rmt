<?php
/*
 * 
 */
require('includes/application_top.php');

$action = (isset($_GET['action']) ? $_GET['action'] : '');

switch($_GET['action']){
/* -----------------------------------------------------
   case 'edit_top_manual' 编辑top手册
   case 'save_top_manual' 保存top手册
   case 'show_categories_manual_link' 显示分类手册
   case 'save_categories_manual_link' 保存分类手册
   case 'edit_top_categories_manual' 编辑分类手册
   case 'save_top_categories_manual' 保存分类手册
   case 'show_products_manual' 商品手册信息    
   case 'show_categories_manual' 分类手册信息     
   case 'p_categories_manual' 来自订单页的商品手册信息     
   case 'save_products_manual' 保存商品手册信息     
   case 'save_categories_manual' 保存分类手册信息     
   case 'save_p_categories_manual' 保存来自订单页的手册信息     
------------------------------------------------------*/
case 'edit_top_manual':
  $title_char = TOP_MANUAL_TEXT.MANUAL_TITLE; 
  $form_info = '<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array('action', 'info', 'x', 'y'))."action=save_top_manual").'" method="post">';
  $manual_content = get_configuration_by_site_id('TOP_MANUAL_CONTENT');
  $return_button = '<a href="'.tep_href_link(FILENAME_CATEGORIES, tep_get_all_get_params(array('action', 'info', 'x', 'y','parent'))).'">'.tep_html_element_button(MANUAL_RETURN).'</a>';
  break;
case 'save_top_manual':
  tep_db_query("update `".TABLE_CONFIGURATION."` set `configuration_value` = '".addslashes($_POST['manual'])."' where `configuration_key` = 'TOP_MANUAL_CONTENT'");
   
  tep_redirect(tep_href_link(FILENAME_CATEGORIES, tep_get_all_get_params(array('action', 'info', 'x', 'y')))); 
  break;
case 'show_categories_manual_link':
  $tmp_path_info = explode('_', $_GET['cPath']);
  $tmp_c_path_info = $tmp_path_info[count($tmp_path_info) - 1];
  $manual_category_query = tep_db_query("select categories_name, c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".(int)$tmp_c_path_info."' and site_id = '0'"); 
  $manual_category_res = tep_db_fetch_array($manual_category_query); 
  $title_char = $manual_category_res['categories_name'].MANUAL_TITLE;
  $form_info = '<form  name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array('action', 'info', 'x', 'y'))."action=save_categories_manual_link").'" method="post">';
  $manual_content = $manual_category_res['c_manual'];
  $return_button = '<a href="'.tep_href_link(FILENAME_CATEGORIES, tep_get_all_get_params(array('action', 'info', 'x', 'y','parent'))).'">'.tep_html_element_button(MANUAL_RETURN).'</a>';
  break;
case 'save_categories_manual_link':
  $tmp_path_info = explode('_', $_GET['cPath']);
  $tmp_c_path_info = $tmp_path_info[count($tmp_path_info) - 1];
  $manual_category_query = tep_db_query("select categories_name, c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".(int)$tmp_c_path_info."' and site_id = '0'"); 
  $manual_category_res = tep_db_fetch_array($manual_category_query); 
  if ($manual_category_res) {
    tep_db_query("update ".TABLE_CATEGORIES_DESCRIPTION." set c_manual='".addslashes($_POST['manual'])."' where categories_id='".(int)$tmp_c_path_info."' and site_id = '0'");
  }
  tep_redirect(tep_href_link(FILENAME_CATEGORIES, tep_get_all_get_params(array('action', 'info', 'x', 'y')))); 
  break;
case 'edit_top_categories_manual':
  $title_char = TOP_MANUAL_TEXT.MANUAL_TITLE; 
  $form_info = '<form  name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array('action', 'info', 'x', 'y'))."action=save_top_categories_manual").'" method="post">';
  $manual_content = get_configuration_by_site_id('TOP_MANUAL_CONTENT');
  $return_button='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action",'parent'))."action=show_manual_info").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
  break;
case 'save_top_categories_manual':
  tep_db_query("update `".TABLE_CONFIGURATION."` set `configuration_value` = '".addslashes($_POST['manual'])."' where `configuration_key` = 'TOP_MANUAL_CONTENT'");
  tep_redirect(tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action"))."action=show_manual_info")); 
  break;
case 'show_products_manual':
//来自订单
if(isset($_GET['oID']) && $_GET['oID']){
$oID=$_GET['oID'];
$pID=$_GET['pID'];
$page=$_GET['page'];
$site_id=0;
$categories_info_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pID."'");
$categories_info_array=tep_db_fetch_array($categories_info_query);
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_info_array['categories_id']."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$cp_manual_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid_array['parent_id']."' and site_id='".$site_id."'");
$cp_manual_array=tep_db_fetch_array($cp_manual_query);

$c_manual_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_info_array['categories_id']."' and site_id='".$site_id."'");
$c_manual_array=tep_db_fetch_array($c_manual_query);

$pro_manual_query=tep_db_query("select products_name,p_manual from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$pID."' and site_id='".$site_id."'");
$pro_manual_array=tep_db_fetch_array($pro_manual_query);
$title_char=$cp_manual_array['categories_name'].'/'.$c_manual_array['categories_name'].'/'.$pro_manual_array['products_name'].MANUAL_TITLE;
$manual_content=$pro_manual_array['p_manual'];
$form_info='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_products_manual").'" method="post">';
$return_button='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action",'parent'))."action=show_manual_info").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';

break;
}
//来自categories
if(isset($_GET['cPath']) && !isset($_GET['keyword'])){
if(isset($_GET['pID']) && $_GET['pID']){
$title_char = "";
$cPath      = $_GET['cPath'];
$page       = $_GET['page'];
$pid        = $_GET['pID'];
$search = $_GET['search'];
$site_id = 0;

$categories_id=explode('_',$_GET['cPath']);
$p_categories_id=$categories_id[0];
$s_categories_id=$categories_id[1];
$categories_p_info_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$p_categories_id."' and site_id='".$site_id."'");
$categories_p_info=tep_db_fetch_array($categories_p_info_query);
$categories_s_info_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$s_categories_id."' and site_id='".$site_id."'");
$categories_s_info=tep_db_fetch_array($categories_s_info_query);
if($categories_id[2] != ''){

  $categories_parent_info_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_id[2]."' and site_id='".$site_id."'");
  $categories_parent_info=tep_db_fetch_array($categories_parent_info_query);
  tep_db_free_result($categories_parent_info_query);
}
$products_info_query=tep_db_query("select products_name,p_manual from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$pid."' and site_id='0'");
$products_info_arr=tep_db_fetch_array($products_info_query);
$title_char=($categories_p_info['categories_name'] !='' ? $categories_p_info['categories_name'].'/' : '').($categories_s_info['categories_name'] != '' ? $categories_s_info['categories_name'].'/' : '').($categories_parent_info['categories_name'] != '' ? $categories_parent_info['categories_name'].'/' : '').($products_info_arr['products_name'] != '' ? $products_info_arr['products_name'] : '').MANUAL_TITLE;
$manual_content=$products_info_arr['p_manual'];
$param_str='cPath='.$cPath.'&pID='.$pid.'&site_id='.$site_id.'&page='.$page.($search != '' ? '&search='.$search : '');
$return_button='<a onclick="location=this.href" href="categories.php?'.$param_str.'"><input type="button" value="'.MANUAL_RETURN.'"></a>';

$form_info='<form name="manual_form" action="products_manual.php?cPath='.$cPath.'&action=save_products_manual&pID='.$pid.'&site_id='.$site_id.'&page='.$page.($search != '' ? '&search='.$search : '').'" method="post">';
}
break;
}
//来自搜索
if(isset($_GET['keyword']) ){
$title_char = "";
$pid        = $_GET['pID'];
$site_id    = 0;

$products_query=tep_db_query("select products_name,p_manual from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$pid."' and site_id='".$site_id."'");
$pro_to_cate_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
$categories_array=tep_db_fetch_array($pro_to_cate_query);

$categories_id=$categories_array['categories_id'];
$categories_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_id."'");
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_id."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$categories_pid=$categories_pid_array['parent_id'];
$categories_p_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid."'");
$categories_p_array=tep_db_fetch_array($categories_p_query);
$categories_array=tep_db_fetch_array($categories_query);
$products_array=tep_db_fetch_array($products_query);
if(isset($_GET['cid2']) && $_GET['cid2']!=""){
$categories_2_query   = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cid2."' and site_id='".$site_id."'");
$categories_2_array   = tep_db_fetch_array($categories_2_query);
$title_cid2 = $categories_2_array['categories_name'].'/';
}
$title_char=$categories_p_array['categories_name'].'/'.$title_cid2.$categories_array['categories_name'].'/'.$products_array['products_name'].MANUAL_TITLE;
$manual_content=$products_array['p_manual'];
$form_info='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_products_manual").'" method="post">';
$return_button='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action",'parent'))."action=show_search_manual").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
break;
}



case 'show_categories_manual':
$title_char = "";
$site_id    = 0;

//来自categories
if(isset($_GET['cPath']) && $_GET['cPath']==''){
$cPath      = $_GET['cPath'];
$page       = $_GET['page'];
$cid        = $_GET['cID'];
$search = $_GET['search'];
$categories_s_info_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cID']."' and site_id='".$site_id."'");
$categories_s_info=tep_db_fetch_array($categories_s_info_query);
$title_char=$categories_s_info['categories_name'].MANUAL_TITLE;
$manual_content=$categories_s_info['c_manual'];
$form_info='<form name="manual_form" action="products_manual.php?cPath='.$cPath.'&action=save_categories_manual&cID='.$cid.'&site_id='.$site_id.'&page='.$page.($search != '' ? '&search='.$search : '').'" method="post">';
$param_str='cPath='.$cPath.'&cID='.$cid.'&site_id='.$site_id.'&page='.$page.($search != '' ? '&search='.$search : '');
$return_button='<a onclick="location=this.href" href="categories.php?'.$param_str.'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
break;
} 
else if(isset($_GET['cPath']) && $_GET['cPath']!='' && !isset($_GET['pID'])){
if(!isset($_GET['cID']) && isset($_GET['keyword']) && $_GET['keyword']){
$check_categories = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$_GET['cPath']."'");
$check_categories_array = tep_db_fetch_array($check_categories);
if($check_categories_array['parent_id']!=0){
$get_parent_categories = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$check_categories_array['parent_id']."'");
$get_parent_categories_array = tep_db_fetch_array($get_parent_categories);
$title_add = $get_parent_categories_array['categories_name'].'/';
}
$categories_info_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cPath']."' and site_id='".$site_id."'");
$categories_info=tep_db_fetch_array($categories_info_query);
$title_char=$title_add.$categories_info['categories_name'].MANUAL_TITLE;
$manual_content=$categories_info['c_manual'];
$return_button        ='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action",'parent'))."action=show_search_manual").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
$form_info            ='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_categories_manual").'" method="post">';
break;
}
if(isset($_GET['cID']) && $_GET['cID'] && !isset($_GET['pID'])){
$check_categories = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$_GET['cPath']."'");
$check_categories_array = tep_db_fetch_array($check_categories);
if($check_categories_array['parent_id']!=0){
$get_parent_categories = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$check_categories_array['parent_id']."'");
$get_parent_categories_array = tep_db_fetch_array($get_parent_categories);
$title_add = $get_parent_categories_array['categories_name'].'/';
}
$categories_p_info_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cPath']."' and site_id='".$site_id."'");
$categories_p_info=tep_db_fetch_array($categories_p_info_query);
$categories_s_info_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cID']."' and site_id='".$site_id."'");
$categories_s_info=tep_db_fetch_array($categories_s_info_query);
if(isset($_GET['cid2']) && $_GET['cid2']!=""){
$categories_2_query   = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cid2."' and site_id='".$site_id."'");
$categories_2_array   = tep_db_fetch_array($categories_2_query);
$title_cid2 = $categories_2_array['categories_name'].'/';
}
$title_char=$categories_p_info['categories_name'].'/'.$title_cid2.$categories_s_info['categories_name'].MANUAL_TITLE;
$manual_content=$categories_s_info['c_manual'];
//search
if(isset($_GET['keyword']) && $_GET['keyword']){
$return_button        ='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action",'parent'))."action=show_search_manual").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
$form_info            ='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_categories_manual").'" method="post">';
}

//categories
else{
$return_button        ='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_CATEGORIES,tep_get_all_get_params(array("action",'parent'))).'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
$form_info            ='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_categories_manual").'" method="post">';

}
break;
}
}
//orders
if(isset($_GET['oID']) && $_GET['oID']){
$pid     = $_GET['pID'];
$site_id = 0;
$pro_to_cate_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
$categories_array=tep_db_fetch_array($pro_to_cate_query);
$categories_id=$categories_array['categories_id'];
$categories_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_id."'");
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_id."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$categories_pid=$categories_pid_array['parent_id'];
$categories_p_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid."'");
$categories_p_array=tep_db_fetch_array($categories_p_query);
$categories_array=tep_db_fetch_array($categories_query);
$title_char=$categories_p_array['categories_name'].'/'.$categories_array['categories_name'].MANUAL_TITLE;
$manual_content=$categories_array['c_manual'];
$form_info='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_categories_manual").'" method="post">';
$return_button='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action",'parent'))."action=show_manual_info").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
break;
}
//products search
if(isset($_GET['pID']) && $_GET['pID']){
$pid     = $_GET['pID'];
$site_id = 0;
if(isset($_GET['cPath']) && $_GET['cPath']!="" && isset($_GET['cid']) && $_GET['cid']!=""){
$categories_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cid']."'");
$categories_p_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cPath']."'");
$categories_p_array=tep_db_fetch_array($categories_p_query);
$categories_array=tep_db_fetch_array($categories_query);
if(isset($_GET['cid2']) && $_GET['cid2']!=""){
$categories_2_query   = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cid2."' and site_id='".$site_id."'");
$categories_2_array   = tep_db_fetch_array($categories_2_query);
$title_cid2 = $categories_2_array['categories_name'].'/';
}
$title_char=$categories_p_array['categories_name'].'/'.$title_cid2.$categories_array['categories_name'].MANUAL_TITLE;
$manual_content=$categories_array['c_manual'];
$form_info='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_categories_manual").'" method="post">';
$return_button='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action",'parent'))."action=show_search_manual").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
break;

}
$pro_to_cate_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
$categories_array=tep_db_fetch_array($pro_to_cate_query);
$categories_id=$categories_array['categories_id'];
$categories_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_id."'");
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_id."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$categories_pid=$categories_pid_array['parent_id'];
$categories_p_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid."'");
$categories_p_array=tep_db_fetch_array($categories_p_query);
$categories_array=tep_db_fetch_array($categories_query);
$title_char=$categories_p_array['categories_name'].'/'.$categories_array['categories_name'].MANUAL_TITLE;
$manual_content=$categories_array['c_manual'];
$form_info='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_categories_manual").'" method="post">';
$return_button='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action",'parent'))."action=show_search_manual").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
break;
}


case 'p_categories_manual':
//orders
if(isset($_GET['oID']) && $_GET['oID']){
$pid     = $_GET['pID'];
$site_id = 0;
$pro_to_cate_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
$categories_array=tep_db_fetch_array($pro_to_cate_query);
$categories_id=$categories_array['categories_id'];
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_id."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$categories_pid=$categories_pid_array['parent_id'];
//判断是否还有上级分类
if(isset($_GET['parent']) && $_GET['parent'] == 1){ 
  $parent_categories_pid_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_pid."'");
  $parent_categories_pid_array = tep_db_fetch_array($parent_categories_pid_query);
  $parent_categories_pid_num = tep_db_num_rows($parent_categories_pid_query);
  tep_db_free_result($parent_categories_pid_query);
  if($parent_categories_pid_num > 0){

    $categories_pid = $parent_categories_pid_array['parent_id'];
  }
}
$categories_p_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid."' and site_id='".$site_id."'");
$categories_p_array=tep_db_fetch_array($categories_p_query);
$title_char=$categories_p_array['categories_name'].MANUAL_TITLE;
$manual_content=$categories_p_array['c_manual'];
$form_info='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_p_categories_manual").'" method="post">';
$return_button='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action",'parent'))."action=show_manual_info").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
break;
}
//products search
if(isset($_GET['pID']) && $_GET['pID']){
	if(isset($_GET['cID']) && $_GET['cID']){
$categories_query = tep_db_query("select categories_id,categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cID']."' and site_id='".$site_id."'");
$categories_array = tep_db_fetch_array($categories_query);
$title_char = $categories_array['categories_name'].MANUAL_TITLE;
$manual_content=$categories_array['c_manual'];
$form_info='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_p_categories_manual").'" method="post">';
$return_button='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action","cID",'parent'))."action=show_search_manual").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
break;
	}
$pid     = $_GET['pID'];
$pro_to_cate_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
$categories_array=tep_db_fetch_array($pro_to_cate_query);
$categories_id=$categories_array['categories_id'];
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_id."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$check_pid = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_pid_array['parent_id']."'");
$check_pid_array = tep_db_fetch_array($check_pid);
if($check_pid_array['parent_id']!=0){
$get_pid = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$check_pid_array['parent_id']."' and site_id='".$site_id."'");
$get_pid_array = tep_db_fetch_array($get_pid);
$title_add = $get_pid_array['categories_name'].'/';
}
$categories_pid=$categories_pid_array['parent_id'];
$categories_p_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid."' and site_id='".$site_id."'");
$categories_p_array=tep_db_fetch_array($categories_p_query);
$title_char=$title_add.$categories_p_array['categories_name'].MANUAL_TITLE;
$manual_content=$categories_p_array['c_manual'];
$form_info='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_p_categories_manual").'" method="post">';
$return_button='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action",'parent'))."action=show_search_manual").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
break;
}
//categories search
if(isset($_GET['cID']) && $_GET['cID']){
if(isset($_GET['cID1']) && $_GET['cID1']){
$categories_query = tep_db_query("select categories_id,categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cID1']."' and site_id='".$site_id."'");
$categories_array = tep_db_fetch_array($categories_query);
$title_char = $categories_array['categories_name'].MANUAL_TITLE;
$manual_content=$categories_array['c_manual'];
$form_info='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_p_categories_manual").'" method="post">';
$return_button='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action","cID1",'parent'))."action=show_search_manual").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
break;
	}
if(isset($_GET['cid2']) && $_GET['cid2']!=""){
$categories_2_query   = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cid2."' and site_id='".$site_id."'");
$categories_2_array   = tep_db_fetch_array($categories_2_query);
$title_cid2 = $categories_2_array['categories_name'];
}
$cid     = $_GET['cID'];
$categories_pid_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$cid."'");
$categories_pid_array = tep_db_fetch_array($categories_pid_query);
$categories_pid       = $categories_pid_array['parent_id'];
$categories_p_query   = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid."' and site_id='".$site_id."'");
$categories_p_array   = tep_db_fetch_array($categories_p_query);
$title_char=$categories_p_array['categories_name'].'/'.$title_cid2.MANUAL_TITLE;
$manual_content       = $categories_p_array['c_manual'];
$form_info            ='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_p_categories_manual").'" method="post">';
$return_button        ='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action",'parent'))."action=show_search_manual").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
break;
}
// p_categories search
if(isset($_GET['cPath']) && $_GET['cPath'] && !isset($_GET['p_cpath']) ){
$cpath=$_GET['cPath'];
$site_id = 0;
$check_categories = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$cpath."'");
$check_categories_array = tep_db_fetch_array($check_categories);

if($check_categories_array['parent_id']){

}
$categories_p_query   = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cpath."' and site_id='".$site_id."'");
$categories_p_array   = tep_db_fetch_array($categories_p_query);
$title_char=$categories_p_array['categories_name'].MANUAL_TITLE;
$manual_content       = $categories_p_array['c_manual'];
$form_info            ='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_p_categories_manual").'" method="post">';
$return_button        ='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action",'parent'))."action=show_search_manual").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
break;

}
if(isset($_GET['p_cpath']) && $_GET['p_cpath']!=""){
$p_cpath = $_GET['p_cpath'];
$site = 0;
$categories_pid_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$p_cpath."'");
$categories_pid_array = tep_db_fetch_array($categories_pid_query);
$categories_pid       = $categories_pid_array['parent_id'];
$categories_p_query   = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid."' and site_id='".$site_id."'");
$categories_p_array   = tep_db_fetch_array($categories_p_query);
$title_char=$categories_p_array['categories_name'].MANUAL_TITLE;
$manual_content       = $categories_p_array['c_manual'];
$form_info            ='<form name="manual_form" action="'.tep_href_link(FILENAME_PRODUCTS_MANUAL,tep_get_all_get_params(array("action"))."action=save_p_categories_manual").'" method="post">';
$return_button        ='<a onclick="location=this.href" href="'.tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("action",'parent'))."action=show_search_manual").'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
break;

}
case 'save_products_manual':
	if(isset($_GET['oID']) && $_GET['oID']){
	$oID=$_GET['oID'];
$pID=$_GET['pID'];
$site_id=0;
$products_manual_sql="update ".TABLE_PRODUCTS_DESCRIPTION." set p_manual='".addslashes($_POST['manual'])."' where products_id='".(int)$pID."' and site_id='0'";

tep_db_query($products_manual_sql);
$param_str=tep_get_all_get_params(array("action"))."action=show_manual_info";
tep_redirect(tep_href_link(FILENAME_ORDERS, $param_str)); 

	}else{
$cPath=$_GET['cPath'];
$page=$_GET['page'];
$pid=$_GET['pID'];
$site_id = (!empty($_GET['site_id']))?$_GET['site_id']:0;


$products_manual_sql="update ".TABLE_PRODUCTS_DESCRIPTION." set p_manual='".addslashes($_POST['manual'])."' where products_id='".(int)$pid."' and site_id='0'";

tep_db_query($products_manual_sql);
if(isset($_GET['cPath']) && !isset($_GET['keyword'])){
$param_str='cPath='.$cPath.'&pID='.$pid.'&site_id='.$site_id.'&page='.$page.($search != '' ? '&search='.$search : '');
tep_redirect(tep_href_link(FILENAME_CATEGORIES, $param_str)); 

}else{
$param_str=tep_get_all_get_params(array("action"))."action=show_search_manual";
tep_redirect(tep_href_link(FILENAME_ORDERS, $param_str)); 

}
	}
break;




case 'save_categories_manual':
if(!isset($_GET['cID']) && isset($_GET['keyword']) && $_GET['keyword'] && !isset($_GET['pID'])){
$cPath=$_GET['cPath'];
$site_id=0;
$categories_manual_sql="update ".TABLE_CATEGORIES_DESCRIPTION." set c_manual='".addslashes($_POST['manual'])."' where categories_id='".(int)$cPath."' and site_id='".$site_id."'";
tep_db_query($categories_manual_sql);
$param_str=tep_get_all_get_params(array("action"))."action=show_search_manual";
tep_redirect(tep_href_link(FILENAME_ORDERS, $param_str)); 
break;
	}

if(isset($_GET['cID']) && $_GET['cID']){
$cPath=$_GET['cPath'];
$page=$_GET['page'];
$cid=$_GET['cID'];
$site_id=0;
$categories_manual_sql="update ".TABLE_CATEGORIES_DESCRIPTION." set c_manual='".addslashes($_POST['manual'])."' where categories_id='".(int)$cid."' and site_id='".$site_id."'";

tep_db_query($categories_manual_sql);
//search
if(isset($_GET['keyword']) && $_GET['keyword']){
$param_str=tep_get_all_get_params(array("action"))."action=show_search_manual";
tep_redirect(tep_href_link(FILENAME_ORDERS, $param_str)); 

break;
}

//categories
else{
$param_str='cPath='.$cPath.'&cID='.$cid.'&site_id='.$site_id.'&page='.$page.($search != '' ? '&search='.$search : '');
tep_redirect(tep_href_link(FILENAME_CATEGORIES, $param_str)); 

break;
}
}
//orders
if(isset($_GET['oID']) && $_GET['oID'] ){
$pid     = $_GET['pID'];
$site_id = 0;
$pro_to_cate_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
$categories_array=tep_db_fetch_array($pro_to_cate_query);
$categories_id=$categories_array['categories_id'];
$categories_manual_sql="update ".TABLE_CATEGORIES_DESCRIPTION." set c_manual='".addslashes($_POST['manual'])."' where categories_id='".(int)$categories_id."' and site_id='".$site_id."'";
tep_db_query($categories_manual_sql);
$param_str=tep_get_all_get_params(array("action"))."action=show_manual_info";
tep_redirect(tep_href_link(FILENAME_ORDERS, $param_str)); 

break;
}
//products orders
if(isset($_GET['pID']) && $_GET['pID']){
$pid     = $_GET['pID'];
$site_id = 0;
$pro_to_cate_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
$categories_array=tep_db_fetch_array($pro_to_cate_query);
$categories_id=$categories_array['categories_id'];
$categories_manual_sql="update ".TABLE_CATEGORIES_DESCRIPTION." set c_manual='".addslashes($_POST['manual'])."' where categories_id='".(int)$categories_id."' and site_id='".$site_id."'";
tep_db_query($categories_manual_sql);
$param_str=tep_get_all_get_params(array("action"))."action=show_search_manual";
tep_redirect(tep_href_link(FILENAME_ORDERS, $param_str)); 

break;
}

case 'save_p_categories_manual':
//orders
if(isset($_GET['oID']) && $_GET['oID']){
$pid     = $_GET['pID'];
$site_id = (!empty($_GET['site_id']))?$_GET['site_id']:0;
$pro_to_cate_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
$categories_array=tep_db_fetch_array($pro_to_cate_query);
$categories_id=$categories_array['categories_id'];
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_id."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$categories_pid=$categories_pid_array['parent_id'];

//判断是否还有上级分类
if(isset($_GET['parent']) && $_GET['parent'] == 1){ 
  $parent_categories_pid_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_pid."'");
  $parent_categories_pid_array = tep_db_fetch_array($parent_categories_pid_query);
  $parent_categories_pid_num = tep_db_num_rows($parent_categories_pid_query);
  tep_db_free_result($parent_categories_pid_query);
  if($parent_categories_pid_num > 0){

    $categories_pid = $parent_categories_pid_array['parent_id'];
  }
}
$categories_manual_sql="update ".TABLE_CATEGORIES_DESCRIPTION." set c_manual='".addslashes($_POST['manual'])."' where categories_id='".(int)$categories_pid."' and site_id='".$site_id."'";
tep_db_query($categories_manual_sql);
$param_str=tep_get_all_get_params(array("action","parent"))."action=show_manual_info";
tep_redirect(tep_href_link(FILENAME_ORDERS, $param_str)); 
break;
}
//products search
if(isset($_GET['pID']) && $_GET['pID']){
if(isset($_GET['cID']) && $_GET['cID']){
$categories_query = tep_db_query("select categories_id,categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cID']."' and site_id='".$site_id."'");
$categories_array = tep_db_fetch_array($categories_query);
$categories_manual_sql = "update ".TABLE_CATEGORIES_DESCRIPTION." set c_manual='".addslashes($_POST['manual'])."' where categories_id='".(int)$categories_array['categories_id']."' and site_id='".$site_id."'";
tep_db_query($categories_manual_sql);
$param_str=tep_get_all_get_params(array("action","cID"))."action=show_search_manual";
tep_redirect(tep_href_link(FILENAME_ORDERS, $param_str)); 
break;
}
$pid     = $_GET['pID'];
$site_id = (!empty($_GET['site_id']))?$_GET['site_id']:0;
$pro_to_cate_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
$categories_array=tep_db_fetch_array($pro_to_cate_query);
$categories_id=$categories_array['categories_id'];
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_id."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$categories_pid=$categories_pid_array['parent_id'];

$categories_manual_sql="update ".TABLE_CATEGORIES_DESCRIPTION." set c_manual='".addslashes($_POST['manual'])."' where categories_id='".(int)$categories_pid."' and site_id='".$site_id."'";
tep_db_query($categories_manual_sql);
$param_str=tep_get_all_get_params(array("action"))."action=show_search_manual";
tep_redirect(tep_href_link(FILENAME_ORDERS, $param_str)); 
break;
}
//categories search
if(isset($_GET['cID']) && $_GET['cID']){
if(isset($_GET['cID1']) && $_GET['cID1']){
$categories_query = tep_db_query("select categories_id,categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cID1']."' and site_id='".$site_id."'");
$categories_array = tep_db_fetch_array($categories_query);
$categories_manual_sql = "update ".TABLE_CATEGORIES_DESCRIPTION." set c_manual='".addslashes($_POST['manual'])."' where categories_id='".(int)$categories_array['categories_id']."' and site_id='".$site_id."'";
tep_db_query($categories_manual_sql);
$param_str=tep_get_all_get_params(array("action","cID1"))."action=show_search_manual";
tep_redirect(tep_href_link(FILENAME_ORDERS, $param_str)); 
break;
}
$cid     = $_GET['cID'];
$site_id = (!empty($_GET['site_id']))?$_GET['site_id']:0;
$categories_pid_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$cid."'");
$categories_pid_array = tep_db_fetch_array($categories_pid_query);
$categories_pid       = $categories_pid_array['parent_id'];
$categories_manual_sql="update ".TABLE_CATEGORIES_DESCRIPTION." set c_manual='".addslashes($_POST['manual'])."' where categories_id='".(int)$categories_pid."' and site_id='".$site_id."'";
tep_db_query($categories_manual_sql);
$param_str=tep_get_all_get_params(array("action"))."action=show_search_manual";
tep_redirect(tep_href_link(FILENAME_ORDERS, $param_str)); 
break;
}
//p_categories search
if(isset($_GET['cPath']) && $_GET['cPath'] && !isset($_GET['p_cpath'])){
$cpath=$_GET['cPath'];
$site_id = (!empty($_GET['site_id']))?$_GET['site_id']:0;
$categories_manual_sql="update ".TABLE_CATEGORIES_DESCRIPTION." set c_manual='".addslashes($_POST['manual'])."' where categories_id='".(int)$cpath."' and site_id='".$site_id."'";
tep_db_query($categories_manual_sql);
$param_str=tep_get_all_get_params(array("action"))."action=show_search_manual";
tep_redirect(tep_href_link(FILENAME_ORDERS, $param_str)); 

break;
}
if(isset($_GET['p_cpath']) && $_GET['p_cpath'] !=""){
$p_cpath     = $_GET['p_cpath'];
$site_id = 0;
$categories_pid_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$p_cpath."'");
$categories_pid_array = tep_db_fetch_array($categories_pid_query);
$categories_pid       = $categories_pid_array['parent_id'];
$categories_manual_sql="update ".TABLE_CATEGORIES_DESCRIPTION." set c_manual='".addslashes($_POST['manual'])."' where categories_id='".(int)$categories_pid."' and site_id='".$site_id."'";
tep_db_query($categories_manual_sql);
$param_str=tep_get_all_get_params(array("action","p_cpath"))."action=show_search_manual";
tep_redirect(tep_href_link(FILENAME_ORDERS, $param_str)); 
break;

}
}
$rand_num=time();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>
<?php 
if(isset($_GET['site_id']) &&  $_GET['site_id']){
$site_id = $_GET['site_id'];
}else{
$site_id = 0;
}
if((isset($_GET['cPath']) && $_GET['cPath']=="") && (isset($_GET['cID']) && $_GET['cID']!="") && (isset($_GET['action']) && $_GET['action']=="show_categories_manual")){
$categories_query = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cID']."' and site_id='0'");
$categories_array = tep_db_fetch_array($categories_query);
echo $categories_array['categories_name'].MANUAL_TITLE;
}else if((isset($_GET['cPath']) && $_GET['cPath']!="") && (isset($_GET['cID']) && $_GET['cID']!="") && (isset($_GET['action']) && $_GET['action']=="show_categories_manual")){
$parent_categories_query = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cPath']."' and site_id='0'");
$parent_categories_array = tep_db_fetch_array($parent_categories_query);
$categories_query = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cID']."' and site_id='0'");
$categories_array = tep_db_fetch_array($categories_query);
echo $parent_categories_array['categories_name']."/".$categories_array['categories_name'].MANUAL_TITLE;
}else if((isset($_GET['cPath']) && $_GET['cPath']!="")  && (isset($_GET['action']) && $_GET['action']=="show_products_manual")){
$cpath_array = explode("_",$_GET['cPath']) ;
$tmp_categories_array = array();
foreach($cpath_array as $key =>$val){
$categories_query = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$val."' and site_id='0'");
$tmp_categories_array[] = tep_db_fetch_array($categories_query);
}
$products_query = tep_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$_GET['pID']."' and site_id='0'");
$products_array = tep_db_fetch_array($products_query);
foreach($tmp_categories_array as $key1=>$val1){
$title_str .= $val1['categories_name']."/";
}
$title_str .= $products_array['products_name'].MANUAL_TITLE;
echo $title_str;
} else if ($_GET['action'] == 'show_categories_manual_link' || $_GET['action'] == 'edit_top_manual') {
  echo $title_char;
}
?>
</title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script type="text/javascript" src="lib/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
<?php //提交动作?>
function check_manual_form(c_permission)
{
  if (c_permission == 31) {
    document.forms.manual_form.submit(); 
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
          document.forms.manual_form.submit(); 
        } else {
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.manual_form.action),
              async: false,
              success: function(msg_info) {
                document.forms.manual_form.submit(); 
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
function manual_open(){

  $("#manual_info_id").hide();
}
tinyMCE.init({
        mode : "exact",
        elements : "elm1",
        theme : "advanced",
        height: "800", 
        plugins : "imageupload,pagebreak,style,layer,table,advhr,advlink,emotions,iespell,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,inlinepopups",
         
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontsizeselect,forecolor,backcolor,imageupload,|,cut,copy,paste,|,search,replace,|,bullist,numlist,|,undo,redo,|,link,unlink,anchor,|,code",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : false,
        
        skin : "o2k7",
        skin_variant : "silver",
});
</script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=[^&]+/',$belong,$belong_array);
$categories_url = $_GET['cPath'];
$categories_cid = $_GET['cID'];;
$categories_url = $_GET['action'] != 'show_categories_manual_link' && $categories_cid != '' ? $categories_cid : $categories_url;
if($belong_array[0][0] != ''){
    $pid     = $_GET['pID'];
    $pro_to_cate_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
    $categories_array=tep_db_fetch_array($pro_to_cate_query);
    $categories_id=$categories_array['categories_id'];
    $categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_id."'");
    $categories_pid_array=tep_db_fetch_array($categories_pid_query);
    tep_db_free_result($categories_pid_query);
    $categories_pid=$categories_pid_array['parent_id'];
    //判断是否还有上级分类
    if(isset($_GET['parent']) && $_GET['parent'] == 1){ 
      $parent_categories_pid_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_pid."'");
      $parent_categories_pid_array = tep_db_fetch_array($parent_categories_pid_query);
      $parent_categories_pid_num = tep_db_num_rows($parent_categories_pid_query);
      tep_db_free_result($parent_categories_pid_query);
      if($parent_categories_pid_num > 0){

        $categories_pid = $parent_categories_pid_array['parent_id'];
        $categories_parent_id = $parent_categories_pid_array['parent_id'];
      }
    }
    $categories_url_array = explode('_',$categories_url);
    $categories_pid = $categories_url != '' ? end($categories_url_array) : $categories_pid;
  if($belong_array[0][0] == 'action=edit_top_categories_manual' || $belong_array[0][0] == 'action=edit_top_manual'){

    $belong = $href_url.'?action=edit_top_categories_manual';
  }else if($belong_array[0][0] == 'action=p_categories_manual' || $belong_array[0][0] == 'action=show_categories_manual_link' || $belong_array[0][0] == 'action=show_categories_manual'){    

    if($belong_array[0][0] == 'action=p_categories_manual'){ 
      if(isset($_GET['parent']) && $_GET['parent'] == 1){
        $belong = $href_url.'?action=show_categories_manual&cPath='.$categories_parent_id;  
      }else{
        $belong = $href_url.'?action=show_categories_manual&cPath='.$categories_pid;
      }
    }else{
      $categories_pid_query = tep_db_query("select * from ".TABLE_CATEGORIES." where parent_id='".$categories_id."'");
      $categories_parent_num = tep_db_num_rows($categories_pid_query);
      tep_db_free_result($categories_pid_query);
      if($categories_parent_num > 0){
        if($categories_cid != ''){
          $belong = $href_url.'?action=show_categories_manual&cPath='.$categories_pid; 
        }else{
          if($belong_array[0][0] == 'action=show_categories_manual_link'){
            $belong = $href_url.'?action=show_categories_manual&cPath='.$categories_pid; 
          }else{
            $belong = $href_url.'?action=show_categories_manual&cPath='.$categories_id; 
          }
        }
      }else{
        $belong = $href_url.'?action=show_categories_manual&cPath='.$categories_id; 
      }
    }
  }else if($belong_array[0][0] == 'action=show_products_manual'){
    $belong = $href_url.'?action=show_products_manual&pID='.$pid; 
  }
}else{

  $belong = $href_url;
}
$belong = str_replace('&','|||',$belong);
require("includes/note_js.php");
?>
<script type="text/javascript">
$(document).ready(function(){
  var box_warp_height = $(".box_warp").height();
  var box_warp_height = parseInt(box_warp_height);
  if(box_warp_height < 826){
    $(".box_warp").height('');
  }
});
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft"><tr><td>
<!-- left_navigation --> <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> <!-- left_navigation_eof -->
    </td></tr></table>
<!-- body_text -->
<td width="100%" valign = "top" id='categories_right_td'><div id="manual_info_id" style="position:absolute;width:100%;height:100%;margin-left:0;z-index:1;opacity:0;display:none;" onclick="manual_open();"></div><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>

<td class="pageHeading"><?php echo $title_char;  ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
</tr>
<tr>
<td>
<?php echo $form_info;?>
<table width="100%">

<tr><td id="emd" >
<textarea id="elm1" class="" cols="207" rows="20" name="manual" style="width:100%;height:100%;"><?php echo stripcslashes($manual_content);?></textarea>


<td></tr>
<tr><td align="right" id="button_width">
<a href="javascript:void(0);"><?php echo tep_html_element_button(MANUAL_SAVE, 'onclick="check_manual_form(\''.$ocertify->npermission.'\');"');?></a>
<?php echo $return_button;?>
</td></tr>

</table>


</td>
</tr>
</form>
</table>
<!-- body_eof -->
</div></div></td></tr>
   </table> 

<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
