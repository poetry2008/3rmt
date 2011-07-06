<?php 
if(isset($_GET['question_romaji'])&&$_GET['question_romaji']!=''){
  $qromaji = $_GET['question_romaji'];
  $link_url = 'faq';
  $link_url_arr = array();
  $link_url_arr[] = 'faq';
  $breadcrumb->add(TEXT_FAQ,HTTP_SERVER.'/'.$link_url);
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
      $breadcrumb->add($temp_category_info['title'],HTTP_SERVER.'/'.$link_url);
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
  $breadcrumb->add($temp_question_info['ask'],HTTP_SERVER.'/'.$link_url);
}
