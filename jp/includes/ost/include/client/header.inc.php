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
        <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
          <!-- left_navigation //-->
          <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
          <!-- left_navigation_eof //-->
        </td> 
        <!-- body_text //--> 
        <td valign="top" id="contents">
          <h1 class="pageHeading"><?php echo お問い合わせ; ?></h1> 
          <div>
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top" class="contents">

