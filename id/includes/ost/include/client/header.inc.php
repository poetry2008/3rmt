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
  <div class="body_shadow" align="center">
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
      <tr>
        <!-- body_text //--> 
        <td valign="top" id="contents" class="left_colum_border">
          <h1 class="pageHeading">
        <span class="game_t">お問い合わせ</span>
          </h1> 
          <div class="comment_inc">
