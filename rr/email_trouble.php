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
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents">
      <h1 class="pageHeading"><span><?php echo 'フリーメールでメールが受け取れない方へ'; ?></span></h1>
        <div class="comment"> 

<div class="product_info_box">
  <div id="bgn_content">
    <div id="wrapper_kiyaku">
      <br>
        <span class="txt_blue">*****@yahoo.co.jp、*****@hotmail.com、*****@msn.com、AOLなどのフリーメールを御使用になられている場合</span>、上記のドメインでご登録された方で、当社からのメールが届かないというご報告を頂いております。
 
       <br>
        <br>
        これは、Yahoo!メール、hotmail、msnメール、AOLの受信手続きで「迷惑メール」として処理をされている可能性がございます。<br><br>
        この処理は各フリーメールが持つ機能で、不特定多数のサイトからのメールを「迷惑メール」として排除し、快適にメールを使用するためのものです。<br><br>
        上記のようなドメインをご使用の場合には、<span class="txt_blue">当社からのメールを「通常メール」として受信許可をいただく必要がございます。</span><br>
       <br>
        <br>
        以下に各フリーメールの毎に受信許可の設定手順を記します。
      <br>
      <br>
      <h3><span class="txt_bold">Yahoo!メール、Yahoo!BBメール&nbsp;&nbsp;受信許可の設定手順</span></h3>
      <ol>
        <li>Yahoo!メールにログインして[メールオプション]をクリック</li>
        <li>[フィルターと受信通知設定] → [新規作成]を順にクリック</li>
        <li>設定を以下のように変更します。<br>
          「Fromが次（を含む）（<span class="txt_blue"><?php echo STORE_DOMAIN;?></span>）」&nbsp;&nbsp;移動先フォルダ「受信箱」 </li>
      </ol>
 <br>
      <br>
      <h3><span class="txt_bold">hotmail、msnmail&nbsp;&nbsp;受信許可の設定手順</span></h3>
      <ol>
        <li>hotmail にサインインして[オプション] をクリック</li>
        <li>迷惑メール処理の[セーフリスト]をクリック</li>
        <li><?php echo STORE_NAME;?>および当社の利用するドメイン「<span class="txt_blue"><?php echo STORE_DOMAIN;?></span>」を追加します</li>
        <li>最後に「OK」をクリックして終了します</li>
      </ol>
 <br>
      <br>
      <h3><span class="txt_bold">AOL&nbsp;&nbsp;受信許可の設定手順</span></h3>
       <br> ご利用環境によりAOL接続ソフト「AOL Communicator」の設定が必要です。
      <ol>
        <li>クイックガイドのメールコントロールを参照します</li>
        <li>[迷惑メールフィルタ]で「<span class="txt_blue"><?php echo STORE_DOMAIN;?></span>」の受信を許可する設定にして下さい</li>
      </ol>
       <br>
      <br>
      <h3><span class="txt_bold">上記以外のメールアドレスをご利用の場合</span></h3>
       <br> 上記以外のメールアドレスをご利用の方で、同様に<?php echo STORE_NAME;?>および当社からのメールが届かないという場合も、同様の原因（迷惑メール処理機能）が考えられます。<br>
        お手数ですがご利用先のマニュアル等をご覧の上、<?php echo STORE_NAME;?>および当社の利用するドメイン「<span class="txt_blue"><?php echo STORE_DOMAIN;?></span>」から送信されるメールの受信許可を設定して下さい。<br>
      <br>
      <br>
    </div>
    <!-- end of wrapper_mail_trouble -->
  </div>
  <!-- end of bgn_content -->
</div>
</div> 
    <p class="pageBottom"></p>
    </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </td> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
