<?php
/*
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
require('includes/application_top.php');

$gc_name = '';
$faq_meta_k = '';
$faq_meta_d = '';
$h1_mes = '';
$faq_mes = '';
$breadcrumb->add('よくある質問', tep_href_link('info-7.html'));

if(isset($_GET['faq_id'])) {
	$faq_no = explode("/", $faq_id, 2);
	$g_id = $faq_no[0];
	if($g_id == "168" || $g_id == "169" || $g_id == "170" || $g_id == "171" || $g_id == "177" || $g_id == "178" || $g_id == "179" || $g_id == "190" || $g_id == "195") {
		$q_no = $faq_no[1];
		$q_id = rtrim($q_no, ".html");
		
		$categories_query = tep_db_query("select categories_name from categories_description where categories_id = '".$g_id."' and language_id = '".$languages_id."' and site_id = '".SITE_ID."'");
		$categories = tep_db_fetch_array($categories_query);
		$gc_name = $categories['categories_name'];
		
		require('iimy_faq/' . (int)$g_id . '/faq.php');
		
		$breadcrumb->add($gc_name . 'のよくある質問', tep_href_link('faq' . (int)$g_id . '/'));
		$h1_mes = $gc_name . 'のよくある質問（FAQ)';
	} else {
		$faq_title = 'ファイルが見つかりません';
		$breadcrumb->add('エラー');
		$h1_mes = 'エラー';
		$faq_mes = '<h3>ファイルが見つかりません</h3><p>削除されたかURLが間違っております。</p>';
	}
}
?>
<?php page_head();?>
</head>
<body>
	<!-- header //--> 
	<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
	<!-- header_eof //--> 
	<!-- body //--> 
	<div id="main">
		<!-- left_navigation //-->
		<div id="l_menu">
			<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
		</div>
		<!-- left_navigation_eof //-->
		<!-- body_text //-->
		<div id="content">
			<div class="box">
				<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
				<h1 class="pageHeading"><?php echo $h1_mes; ?></h1>
				<?php echo $faq_mes; ?>
			 </div>
		</div>
		<!-- body_text_eof //-->
		<div id="r_menu">
			<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
		</div> 
		<!-- body_eof //--> 
		<!-- footer //--> 
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
		<!-- footer_eof //--> 
	</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
