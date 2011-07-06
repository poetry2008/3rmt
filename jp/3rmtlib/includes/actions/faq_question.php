<?php 
if(isset($_GET['question_romaji'])&&$_GET['question_romaji']!=''){
  $qromaji = $_GET['question_romaji'];
  $link_url = 'faq';
  $link_url_arr = array();
  $link_url_arr[] = 'faq';
  $breadcrumb->add(TEXT_FAQ,HTTP_SERVER.'/'.$link_url.'/');
  $temp_parent_id = 0;
  if(isset($_GET['qPath'])&&$_GET['qPath']!=''){
    $category_romaji_arr = explode('/',$_GET['qPath']);
    foreach($category_romaji_arr as $value){
      $temp_parent_id = tep_get_faq_cpath_by_cname($value,$temp_parent_id);
      if(!$temp_parent_id){
        forward404();
      }
      $temp_category_info = tep_get_faq_category_info($temp_parent_id);
      $link_url .= '/'.urlencode($value);
      $link_url_arr[] = urlencode($value);
      $breadcrumb->add($temp_category_info['title'],HTTP_SERVER.'/'.$link_url.'/');
    }
    $faq_question_id = tep_get_faq_qid_by_qname($qromaji,$temp_parent_id);
    if(!$faq_question_id){
      forward404();
    }
    $temp_question_info = tep_get_faq_question_info($faq_question_id);
    if(!tep_question_in_category_by_id($faq_question_id,$temp_parent_id)){
      forward404();
    }
  }else{
    $faq_question_id = tep_get_faq_qid_by_qname($qromaji,$temp_parent_id);
    if(!$faq_question_id){
      forward404();
    }
    $temp_question_info = tep_get_faq_question_info($faq_question_id);
    if(!tep_question_in_category_by_id($faq_question_id,$temp_parent_id)){
      forward404();
    }
    $link_url .= '/'.urlencode($temp_question_info['romaji']);
  }
  $breadcrumb->add($temp_question_info['ask'],HTTP_SERVER.'/'.$link_url.'/');

//last faq
$last_faq_question_sql = "select * from (
                      select 
                      fqd.is_show,
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
                      and fq2c.faq_category_id = '". $temp_parent_id . "' 
                      and fq.id != '".$temp_question_info['faq_question_id']."' 
                      order by fqd.site_id DESC
                      ) c  
                      where (site_id = ".SITE_ID." 
                      or site_id = 0) 
                      and is_show = '1'
                      group by c.faq_question_id 
                      order by c.sort_order,c.ask,c.faq_question_id 
                      ";
/*
$faq_question_split = new splitPageResults($page,10,
    $faq_question_sql,$faq_query_number);
*/
$last_faq_question_query = tep_db_query($last_faq_question_sql);
}
