<?php
/*
  browser.php,v1.0 2007/01/13

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

	require('includes/application_top.php');

	//「商品名」についてのお問い合わせ
	define('HEADING_TITLE', 'Internet Explorer6の設定について');
	define('NAVBAR_TITLE', 'ブラウザの設定');
	
	$breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_BROWSER_IE6X));
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
			<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
			<h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
			<table border="0" class="box_des" width="100%" cellspacing="0" cellpadding="0">
            	<tr>
					<td>
						<table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="2">
							<tr>
								<td>
									<p>当ショッピングシステムは、ブラウザの初期設定のままで動作するように作られております。<br>
									ショッピングができない場合は、以下の手順で設定内容をお確かめください。</p>
									<div class="dot">&nbsp;</div>
									<img src="images/browser/ie6x01.gif" width="420" height="160" alt="インターネットオプション">
									<p>Internet&nbsp;Explorerの上段メニューから［ツール］を選択し、次に［インターネットオプション］を選択してください。</p>
									<div class="dot">&nbsp;</div>
									<img src="images/browser/ie6x02.gif" width="420" height="379" alt="プライバシー設定">
									<p>インターネットオプション画面が開きますので［プライバシー］をクリックしプライバシー設定画面を開きます。<br>
									スライダのつまみを移動して［中］を選択し、［適用］をクリックします。<br>
									<span class="red">※</span>&nbsp;青枠内の［既定］ボタンが押せるようでしたらクリックして、［適用］をクリックしてください。</p>
									<div class="dot">&nbsp;</div>
									<p class="redtext"><b>上記の設定でも解決しない場合は、続けて下記の設定を行ってください。</b></p>
									<div class="dot">&nbsp;</div>
									<img src="images/browser/ie6x03.gif" width="420" height="379" alt="プライバシー詳細設定">
									<p>次に［詳細設定］をクリックし、プライバシー設定の詳細画面を開きます。</p>
									<div class="dot">&nbsp;</div>
									<img src="images/browser/ie6x04.gif" width="420" height="268" alt="cookieの設定">
									<p>［自動Cookie処理を上書きする］と［常にセッションCookieを許可する］にチェックしてCookieを有効にします。その後［OK］ボタンをクリックしてください。</p>
									<div class="dot">&nbsp;</div>
									<img src="images/browser/ie6x05.gif" width="420" height="380" alt="レベルのカスタマイズ">
									<p>インターネットオプション画面の［セキュリティ］をクリックし［レベルのカスタマイズ］をクリックしてください。</p>
									<div class="dot">&nbsp;</div>
									<img src="images/browser/ie6x06.gif" width="410" height="383" alt="アクティブ　スクリプト">
									<p>セキュリティの設定画面が開きます。［アクティブ&nbsp;スクリプト］を［有効にする］にチェックしてJavaScriptを有効にします。その後［OK］ボタンをクリックしてください。</p>
									<div class="dot">&nbsp;</div>
									<p>以上で設定は完了です。ショッピングをお楽しみください。</p>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<!-- body_text_eof //--> 
		<!-- right_navigation //--> 
		<div id="r_menu">
			<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
		</div>
		<!-- right_navigation_eof //--> 
		<!-- body_eof //--> 
		<!-- footer //--> 
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
		<!-- footer_eof //--> 
	</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
