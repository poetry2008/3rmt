<?php 
if(isset($_GET['question_romaji'])&&$_GET['question_romaji']!=''){
  $qromaji = $_GET['question_romaji'];
  $faq_question_id = tep_get_faq_qid_by_qname($qromaji);
  if(!$faq_question_id){
    forward404();
  }
  $category_romaji_arr = explode('/',$_GET['qPath']);
  $link_url = 'faq';
  $link_url_arr = array();
  $link_url_arr[] = 'faq';
  $breadcrumb->add('faq',HTTP_SERVER.'/'.$link_url);
  $temp_parent_id = 0;
  foreach($category_romaji_arr as $value){
    $temp_parent_id = tep_get_faq_cpath_by_cname($value,$temp_parent_id);
    if(!$temp_parent_id){
      forward404();
    }
    $temp_category_info = tep_get_faq_category_info($temp_parent_id);
    $link_url .= '/'.$value;
    $link_url_arr[] = $value;
    $breadcrumb->add($temp_category_info['title'],HTTP_SERVER.'/'.$link_url);
  }
  $temp_question_info = tep_get_faq_question_info($faq_question_id);
  $breadcrumb->add($temp_question_info['ask'],HTTP_SERVER.'/'.$link_url);
}
