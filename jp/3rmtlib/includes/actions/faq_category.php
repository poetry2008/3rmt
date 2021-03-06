<?php
$link_url = "faq";
$link_arr = array();
$link_arr[] = 'faq';
$breadcrumb->add(TEXT_FAQ,HTTP_SERVER.'/'.$link_url.'/');
$parent_info = null;
if(isset($_GET['faq_name'])&&$_GET['faq_name']!=''){
  //faq的名字是否为空 
  if(!preg_match('|\/$|',$_GET['faq_name'])){
      forward404();
  }
$page=0;
$character_arr = explode('/',$_GET['faq_name']);
  $temp_parent_id = 0;
  foreach($character_arr as $value){
    if($value == ''){
      continue;
    }
    if(preg_match('/page-(\d+).html/',$value,$arr)){
      $page = $arr[1];
      continue;
    }
    $last_link_url = $link_url;
    $link_url .= '/'.urlencode($value);
    $link_arr[] = $value;
    $last_faq_category_id = $temp_parent_id;
    //获取该分类的子分类id 
    $temp_parent_id = tep_get_faq_cpath_by_cname($value,$temp_parent_id);
    if(!$temp_parent_id){
      forward404();
    }
    //获取该分类的信息 
    $temp_category_info = tep_get_faq_category_info($temp_parent_id);
    $breadcrumb->add($temp_category_info['title'],HTTP_SERVER.'/'.$link_url.'/');
  }
  $parent_info = $temp_category_info;
  if(count($link_arr)>1){
    array_pop($link_arr);
  }
  $current_faq_category_id = $temp_parent_id;
}else{
  $current_faq_category_id = 0;
}
$faq_category_sql = "
                      select * from 
                      (
                        select 
                        fcd.is_show,
                        fcd.faq_category_id,
                        fc.parent_id,
                        fc.sort_order,
                        fcd.site_id,
                        fcd.url_words,
                        fcd.title,
                        fcd.keywords,
                        fcd.description 
                        from ".TABLE_FAQ_CATEGORIES." fc, 
                        ".TABLE_FAQ_CATEGORIES_DESCRIPTION. " fcd 
                        where fc.parent_id = '".$current_faq_category_id."'
                        and fc.id = fcd.faq_category_id 
                        order by site_id DESC
                      ) c 
                      where (site_id = ".SITE_ID."
                      or site_id = 0) 
                      and is_show = '1'
                      group by c.faq_category_id 
                      order by sort_order,title";
$faq_category_query = tep_db_query($faq_category_sql);
if( $last_faq_category_id != $temp_parent_id){
  $last_faq_category_sql = "
                      select * from 
                      (
                        select 
                        fcd.is_show,
                        fcd.faq_category_id,
                        fc.parent_id,
                        fc.sort_order,
                        fcd.site_id,
                        fcd.url_words,
                        fcd.title,
                        fcd.keywords,
                        fcd.description 
                        from ".TABLE_FAQ_CATEGORIES." fc, 
                        ".TABLE_FAQ_CATEGORIES_DESCRIPTION. " fcd 
                        where fc.parent_id = '".$last_faq_category_id."'
                        and fc.id = fcd.faq_category_id 
                        and fc.id != '".$current_faq_category_id."' 
                        order by site_id DESC
                      ) c 
                      where (site_id = ".SITE_ID."
                      or site_id = 0) 
                      and is_show = '1'
                      group by c.faq_category_id 
                      order by sort_order,title";
  $last_faq_category_query = tep_db_query($last_faq_category_sql);
  $last_parent_info = tep_get_faq_category_info($last_faq_category_id);
}
$faq_question_sql = "select * from (
                      select 
                      fqd.is_show,
                      fq2c.faq_category_id,
                      fqd.faq_question_id,
                      fqd.url_words,
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
                      and fq2c.faq_category_id = '". $current_faq_category_id . "' 
                      order by fqd.site_id DESC
                      ) c  
                      where (site_id = ".SITE_ID." 
                      or site_id = 0) 
                      and is_show = '1'
                      group by c.faq_question_id 
                      order by c.sort_order,c.ask,c.faq_question_id 
                      ";
$faq_question_query = tep_db_query($faq_question_sql);
