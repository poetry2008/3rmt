<?php
/*
  $Id: FAQ.php,v o.1 2006/12/01 

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

$game_name = 'リネージュ';

// IF USER HAS CLICKED ON A QUESTION
if($q_id == "" || $q_id == "index") {
	// IF USER IS LOOKING AT THE MAIN PAGE
	$faq_meta_d = 'リネージュ RMTのよくある質問一覧ページです。リネージュのアデナ販売・買取について';
	$faq_meta_k = 'リネージュ,リネ,Lineage,質問,一覧,RMT,FAQ,アデナ,販売,買取';
	$faq_title = $gc_name . 'のよくある質問一覧/' . TITLE;
	$faq_mes = '<h2 align="right">' . $game_name . 'のFAQ一覧</h2>' . "\n";
	
	$faq_mes .= '<p>このページでは、お客様から寄せられる<strong>' . $game_name . '</strong>の';
	$faq_mes .= 'よくある質問にお答えしています。' . $game_name . '以外のFAQを閲覧する場合は<a href="' . tep_href_link('info-7.html') . '">コチラ</a>をクリックしてください。';
	$faq_mes .= '記載のないご質問は、お手数ではございますがサポートセンターへお問い合わせください。</p>' . "\n" . '<br>' . "\n";
	
	$faq_questions = tep_db_query("SELECT * FROM gm_faq".(int)$g_id."_questions");
	$faqcat = tep_db_query("SELECT * FROM gm_faq".(int)$g_id."_categories ORDER BY c_order");
	$num_of_kitties = mysql_num_rows($faqcat);
	while($faqcat_info = mysql_fetch_assoc($faqcat)) {
		$questions = tep_db_query("SELECT q_id, c_id, question FROM gm_faq".(int)$g_id."_questions WHERE c_id='".$faqcat_info[c_id]."' ORDER BY q_order");
		
		// SHOW CATEGORY NAME, IF IT CONTAINS QUESTIONS AND CATEGORY NAMES ARE TURNED ON
		if(mysql_num_rows($questions) > 0) {
			$faq_mes .= '<h3>' . $faqcat_info['category'] . '：' . $gc_name . '</h3><br>' . "\n";
		}
		
		// SHOW QUESTIONS
		$count = 0;
		$faq_mes .= '<ul>' . "\n";
		
		while($question = mysql_fetch_assoc($questions)) {
			$count++;
			
			$faq_mes .= '<li><a href="' . tep_href_link('faq' . (int)$g_id . '/' . $question['q_id'] . '.html') . '">' . $question['question'] . '</a></li>' . "\n";
		}
		
		$faq_mes .= '</ul>' . "\n";
		$faq_mes .= '<p class="page_top"><a href="' . tep_href_link('faq' . (int)$g_id . '/#top') . '">▲このページのトップへ</a></p>' . "\n";
	}

} else {
	// FAQ読み込み
	$question_query = tep_db_query("SELECT * FROM gm_faq".(int)$g_id."_questions WHERE q_id = '".$q_id."'");
	$question = tep_db_fetch_array($question_query);
	// カテゴリ読み込み
	$q_category_query = tep_db_query("SELECT category FROM gm_faq".(int)$g_id."_categories WHERE c_id = '".$question[c_id]."'");
	$q_category = tep_db_fetch_array($q_category_query);
	// サブカテゴリ読み込み
	$g_sub_categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$g_id . "' and c.categories_id = cd.categories_id and cd.language_id='" . $languages_id ."' order by sort_order, cd.categories_name");
	// その他の質問読み込み
	$other_questions_query = tep_db_query("SELECT q_id, c_id, question FROM gm_faq".(int)$g_id."_questions WHERE c_id='".$question[c_id]."' ORDER BY q_order");
	// タイトル挿入
	$faq_meta_d = $game_name . 'の質問：' . $question['question'];
	$faq_meta_k = 'リネージュ,リネ,Lineage,質問,回答,RMT,FAQ,アデナ,販売,買取';
	$faq_title = $game_name . 'の質問：' . $question['question'] . '-' . TITLE;

	if (!tep_db_num_rows($question_query)) { // not found in database
		$faq_mes .= "<h3>このファイルは存在しません</h3><p>削除されたかURLが間違っております。</p>";
	} else {
		$faq_mes .= '<h2 align="right">' . $gc_name . 'の' . $q_category['category'] . '</h2>' . "\n";
		$faq_mes .= '<h3 class="redtext"><img src="./images/q.gif" alt="" width="23" height="15">' . $question['question'] . '</h3>' . "\n";
		$faq_mes .= '<p><img src="./images/a.gif" alt="" width="23" height="15">' . $question['answer'] . '<br><br><br><a href="' . tep_href_link('faq' . $g_id . '/') . '">' . $game_name . 'のよくある質問一覧へ戻る</a></p>' . "\n";

		$faq_mes .= '<p>このページは';
		while($g_sub_categories = tep_db_fetch_array($g_sub_categories_query)) {
			$faq_mes .= '「<a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . (int)$g_id . '_' . $g_sub_categories['categories_id']) . '"><em class="bold">' . $g_sub_categories['categories_name'] . '</em></a>」';
		}
		$faq_mes .= 'に関する<strong>' . $game_name . '</strong>のよくある質問とその回答です。こちらの回答をお読みいただいても問題が解決しなかった場合は、
お手数ではございますが、サポートセンターへお問い合わせください。</p>' . "\n";

		// その他の質問表示
		$faq_mes .= '<h3>' . $game_name . '：' . $q_category['category'] . 'のFAQ</h3>' . "\n";
		$faq_mes .= '<ul>' . "\n";
		
		while($other_question = tep_db_fetch_array($other_questions_query)) {
			$faq_mes .= '<li><a href="' . tep_href_link('faq' . (int)$g_id . '/' . $other_question['q_id']) . '.html">' . $other_question['question'] . '</a></li>' . "\n";
		}
		
		$faq_mes .= '</ul>' . "\n";
	}
}
$faq_mes .= '<br><p class="smalltext"><span class="redtext">※</span>&nbsp;サービス内容および提供条件は、改善等のため予告なく変更する場合があります。</p><br><br>' . "\n";
?>