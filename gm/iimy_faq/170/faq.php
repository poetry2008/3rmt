<?php
/*
  $Id: FAQ.php,v o.1 2006/12/01 

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

$game_name = '��͡�����';

// IF USER HAS CLICKED ON A QUESTION
if($q_id == "" || $q_id == "index") {
	// IF USER IS LOOKING AT THE MAIN PAGE
	$faq_meta_d = '��͡����� RMT�Τ褯�����������ڡ����Ǥ�����͡�����Υ��ǥ����䡦���ˤĤ���';
	$faq_meta_k = '��͡�����,���,Lineage,����,����,RMT,FAQ,���ǥ�,����,���';
	$faq_title = $gc_name . '�Τ褯����������/' . TITLE;
	$faq_mes = '<h2 align="right">' . $game_name . '��FAQ����</h2>' . "\n";
	
	$faq_mes .= '<p>���Υڡ����Ǥϡ������ͤ���󤻤���<strong>' . $game_name . '</strong>��';
	$faq_mes .= '�褯�������ˤ��������Ƥ��ޤ���' . $game_name . '�ʳ���FAQ������������<a href="' . tep_href_link('info-7.html') . '">������</a>�򥯥�å����Ƥ���������';
	$faq_mes .= '���ܤΤʤ�������ϡ�������ǤϤ������ޤ������ݡ��ȥ��󥿡��ؤ��䤤��碌����������</p>' . "\n" . '<br>' . "\n";
	
	$faq_questions = tep_db_query("SELECT * FROM gm_faq".(int)$g_id."_questions");
	$faqcat = tep_db_query("SELECT * FROM gm_faq".(int)$g_id."_categories ORDER BY c_order");
	$num_of_kitties = mysql_num_rows($faqcat);
	while($faqcat_info = mysql_fetch_assoc($faqcat)) {
		$questions = tep_db_query("SELECT q_id, c_id, question FROM gm_faq".(int)$g_id."_questions WHERE c_id='".$faqcat_info[c_id]."' ORDER BY q_order");
		
		// SHOW CATEGORY NAME, IF IT CONTAINS QUESTIONS AND CATEGORY NAMES ARE TURNED ON
		if(mysql_num_rows($questions) > 0) {
			$faq_mes .= '<h3>' . $faqcat_info['category'] . '��' . $gc_name . '</h3><br>' . "\n";
		}
		
		// SHOW QUESTIONS
		$count = 0;
		$faq_mes .= '<ul>' . "\n";
		
		while($question = mysql_fetch_assoc($questions)) {
			$count++;
			
			$faq_mes .= '<li><a href="' . tep_href_link('faq' . (int)$g_id . '/' . $question['q_id'] . '.html') . '">' . $question['question'] . '</a></li>' . "\n";
		}
		
		$faq_mes .= '</ul>' . "\n";
		$faq_mes .= '<p class="page_top"><a href="' . tep_href_link('faq' . (int)$g_id . '/#top') . '">�����Υڡ����Υȥåפ�</a></p>' . "\n";
	}

} else {
	// FAQ�ɤ߹���
	$question_query = tep_db_query("SELECT * FROM gm_faq".(int)$g_id."_questions WHERE q_id = '".$q_id."'");
	$question = tep_db_fetch_array($question_query);
	// ���ƥ����ɤ߹���
	$q_category_query = tep_db_query("SELECT category FROM gm_faq".(int)$g_id."_categories WHERE c_id = '".$question[c_id]."'");
	$q_category = tep_db_fetch_array($q_category_query);
	// ���֥��ƥ����ɤ߹���
	$g_sub_categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$g_id . "' and c.categories_id = cd.categories_id and cd.language_id='" . $languages_id ."' order by sort_order, cd.categories_name");
	// ����¾�μ����ɤ߹���
	$other_questions_query = tep_db_query("SELECT q_id, c_id, question FROM gm_faq".(int)$g_id."_questions WHERE c_id='".$question[c_id]."' ORDER BY q_order");
	// �����ȥ�����
	$faq_meta_d = $game_name . '�μ��䡧' . $question['question'];
	$faq_meta_k = '��͡�����,���,Lineage,����,����,RMT,FAQ,���ǥ�,����,���';
	$faq_title = $game_name . '�μ��䡧' . $question['question'] . '-' . TITLE;

	if (!tep_db_num_rows($question_query)) { // not found in database
		$faq_mes .= "<h3>���Υե������¸�ߤ��ޤ���</h3><p>������줿��URL���ְ�äƤ���ޤ���</p>";
	} else {
		$faq_mes .= '<h2 align="right">' . $gc_name . '��' . $q_category['category'] . '</h2>' . "\n";
		$faq_mes .= '<h3 class="redtext"><img src="./images/q.gif" alt="" width="23" height="15">' . $question['question'] . '</h3>' . "\n";
		$faq_mes .= '<p><img src="./images/a.gif" alt="" width="23" height="15">' . $question['answer'] . '<br><br><br><a href="' . tep_href_link('faq' . $g_id . '/') . '">' . $game_name . '�Τ褯���������������</a></p>' . "\n";

		$faq_mes .= '<p>���Υڡ�����';
		while($g_sub_categories = tep_db_fetch_array($g_sub_categories_query)) {
			$faq_mes .= '��<a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . (int)$g_id . '_' . $g_sub_categories['categories_id']) . '"><em class="bold">' . $g_sub_categories['categories_name'] . '</em></a>��';
		}
		$faq_mes .= '�˴ؤ���<strong>' . $game_name . '</strong>�Τ褯�������Ȥ��β����Ǥ���������β������ɤߤ��������Ƥ����꤬��褷�ʤ��ä����ϡ�
������ǤϤ������ޤ��������ݡ��ȥ��󥿡��ؤ��䤤��碌����������</p>' . "\n";

		// ����¾�μ���ɽ��
		$faq_mes .= '<h3>' . $game_name . '��' . $q_category['category'] . '��FAQ</h3>' . "\n";
		$faq_mes .= '<ul>' . "\n";
		
		while($other_question = tep_db_fetch_array($other_questions_query)) {
			$faq_mes .= '<li><a href="' . tep_href_link('faq' . (int)$g_id . '/' . $other_question['q_id']) . '.html">' . $other_question['question'] . '</a></li>' . "\n";
		}
		
		$faq_mes .= '</ul>' . "\n";
	}
}
$faq_mes .= '<br><p class="smalltext"><span class="redtext">��</span>&nbsp;�����ӥ����Ƥ�����󶡾��ϡ��������Τ���ͽ��ʤ��ѹ������礬����ޤ���</p><br><br>' . "\n";
?>