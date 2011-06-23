<?php 
if(isset($_GET['question_romaji'])&&$_GET['question_romaji']!=''){
  $qromaji = $_GET['question_romaji'];
  $faq_question_id = tep_get_faq_qid_by_qname($qromaji);
}
