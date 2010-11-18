<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  //「商品名」についてのお問い合わせ
  define('HEADING_TITLE', 'Internet Explorer6の設定について');
  define('NAVBAR_TITLE', 'ブラウザの設定');
  
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_BROWSER_IE6X));
?>
<?php page_head();?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
  <div class="body_shadow" align="center">
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
      <tr>
        <!-- body_text //--> 
        <td valign="top" id="contents">
          <h1 class="pageHeading">
            <span class="game_im">
              <img width="26" height="26" src="images/design/title_img08.gif" alt=""> 
            </span>
            <span class="game_t">
              <?php echo HEADING_TITLE; ?>
            </span>
          </h1> 
          <div class="comment">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top" id="contents" class="content_p">
                  <p>当ショッピングシステムは、ブラウザの初期設定のままで動作するように作られております。<br>
                  ショッピングができない場合は、以下の手順で設定内容をお確かめください。</p>
                  <div class="dot">&nbsp;</div>
                  <img src="images/browser/browser01.gif" width="471" height="190" alt="インターネットオプション">
                  <p>Internet&nbsp;Explorerの上段メニューから［ツール］を選択し、次に［インターネットオプション］を選択してください。</p>
                  <div class="dot">&nbsp;</div>
                  <img src="images/browser/browser02.gif" width="471" height="425" alt="プライバシー設定">
                  <p>インターネットオプション画面が開きますので［プライバシー］をクリックしプライバシー設定画面を開きます。<br>
                  スライダのつまみを移動して［中］を選択し、［適用］をクリックします。<br>
                  <span class="red">※</span>&nbsp;青枠内の［既定］ボタンが押せるようでしたらクリックして、［適用］をクリックしてください。</p>
                  <div class="dot">&nbsp;</div>
                  <p class="red"><b>上記の設定でも解決しない場合は、続けて下記の設定を行ってください。</b></p>
                  <div class="dot">&nbsp;</div>
                  <img src="images/browser/browser03.gif" width="471" height="425" alt="プライバシー詳細設定">
                  <p>次に［詳細設定］をクリックし、プライバシー設定の詳細画面を開きます。</p>
                  <div class="dot">&nbsp;</div>
                  <img src="images/browser/browser04.gif" width="440" height="281" alt="cookieの設定">
                  <p>［自動Cookie処理を上書きする］と［常にセッションCookieを許可する］にチェックしてCookieを有効にします。その後［OK］ボタンをクリックしてください。</p>
                  <div class="dot">&nbsp;</div>
                  <img src="images/browser/browser05.gif" width="471" height="426" alt="レベルのカスタマイズ">
                  <p>インターネットオプション画面の［セキュリティ］をクリックし［レベルのカスタマイズ］をクリックしてください。</p>
                  <div class="dot">&nbsp;</div>
                  <img src="images/browser/browser06.gif" width="410" height="383" alt="アクティブ　スクリプト">
                  <p>セキュリティの設定画面が開きます。［アクティブ&nbsp;スクリプト］を［有効にする］にチェックしてJavaScriptを有効にします。その後［OK］ボタンをクリックしてください。</p>
                  <div class="dot">&nbsp;</div>
                  <p>以上で設定は完了です。ショッピングをお楽しみください。</p>
                </td>
              </tr>
            </table>
          </div>
        </td>
        <!-- body_text_eof //-->
        <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
          <!-- right_navigation //-->
          <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
          <!-- right_navigation_eof //-->
        </td>
      </tr>
    </table>
    <!-- body_eof //-->
    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
