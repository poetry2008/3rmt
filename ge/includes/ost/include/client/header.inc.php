<?php page_head();?>
<script type="text/javascript">
<!--
var a_vars = Array();
var pagename='';
var visitesSite = 1;
var visitesURL = "<?php echo ($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER; ?>/visites.php";
<?php
  require(DIR_WS_ACTIONS.'visites.js');
?>
//-->
</script>
</head>
<body>
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof -->
    <!-- body -->
<div id="main">
        <div id="l_menu">
          <!-- left_navigation -->
          <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
          <!-- left_navigation_eof -->
        </div> 
        <!-- body_text --> 
        <div id="content">
        <div class="headerNavigation"><a class="headerNavigation" href="http://www.gm-exchange.jp">RMT</a> » お問い合わせ</div>
          <h2 class="pageHeading">お問い合わせ</h2> 
          <div class="comment">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top" class="contents">

