<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  $breadcrumb->add('フリーメールでメールが受け取れない方へ', tep_href_link('email_trouble.php'));
?>
<?php page_head();?> 
</head>
<body>
<!-- header --> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof --> 
<!-- body --> 
<div id="main">
<!-- left_navigation -->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- left_navigation_eof -->
<!-- body_text -->
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading"><?php echo 'フリーメールでメールが受け取れない方へ'; ?></h1>
<div class="box">
<div class="content_email01">
  <div id="bgn_content">
    <div id="wrapper_kiyaku">
      <p>
        <span>*****@yahoo.co.jp、*****@hotmail.com、*****@msn.com、AOLなどのフリーメールを御使用になられている場合、</span><br>
        上記のドメインでご登録された方で、当社からのメールが届かないというご報告を頂いております。 </p>
      <p>
        これは、Yahoo!メール、hotmail、msnメール、AOLの受信手続きで「迷惑メール」として処理をされている可能性がございます。<br>
        この処理は各フリーメールが持つ機能で、不特定多数のサイトからのメールを「迷惑メール」として排除し、快適にメールを使用するためのものです。<br>
        上記のようなドメインをご使用の場合には、<span class="txt_blue">当社からのメールを「通常メール」として受信許可をいただく必要がございます。</span><br>
      </p>
      <p>
        以下に各フリーメールの毎に受信許可の設定手順を記します。 </p>
      <h3><span>Yahoo!メール、Yahoo!BBメール&nbsp;&nbsp;受信許可の設定手順</span></h3>
      <ul>
        <li>Yahoo!メールにログインして[メールオプション]をクリック</li>
        <li>[フィルターと受信通知設定] → [新規作成]を順にクリック</li>
        <li>設定を以下のように変更します。<br>
          「Fromが次（を含む）（<span><?php echo STORE_DOMAIN;?></span>）」&nbsp;&nbsp;移動先フォルダ「受信箱」 </li>
      </ul>
      <h3><span>hotmail、msnmail&nbsp;&nbsp;受信許可の設定手順</span></h3>
      <ul>
        <li>hotmail にサインインして[オプション] をクリック</li>
        <li>迷惑メール処理の[セーフリスト]をクリック</li>
        <li><?php echo STORE_NAME;?>および当社の利用するドメイン「<span><?php echo STORE_DOMAIN;?></span>」を追加します</li>
        <li>最後に「OK」をクリックして終了します</li>
      </ul>
      <h3><span>AOL&nbsp;&nbsp;受信許可の設定手順</span></h3>
      ご利用環境によりAOL接続ソフト「AOL Communicator」の設定が必要です。
      <ul>
        <li>クイックガイドのメールコントロールを参照します</li>
        <li>[迷惑メールフィルタ]で「<span><?php echo STORE_DOMAIN;?></span>」の受信を許可する設定にして下さい</li>
      </ul>
      <h3><span>上記以外のメールアドレスをご利用の場合</span></h3>
      <p> 上記以外のメールアドレスをご利用の方で、同様に<?php echo STORE_NAME;?>および当社からのメールが届かないという場合も、同様の原因（迷惑メール処理機能）が考えられます。<br>
        お手数ですがご利用先のマニュアル等をご覧の上、<?php echo STORE_NAME;?>および当社の利用するドメイン「<span class="txt_blue"><?php echo STORE_DOMAIN;?></span>」から送信されるメールの受信許可を設定して下さい。<br>
      </p>
    </div>
    <!-- end of wrapper_mail_trouble -->
  </div>
  <!-- end of bgn_content -->
</div>

      </div></div>
      <!-- body_text_eof --> 
<!-- right_navigation --> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
<!-- right_navigation_eof -->
  <!-- body_eof -->
  <!-- footer -->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof -->
</div>
</body>
</html>

