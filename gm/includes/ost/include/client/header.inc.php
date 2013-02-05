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
    <!-- header_eof //-->
    <!-- body //-->
<div id="main">
<div class="yui3-u" id="layout">
       <!-- <div id="l_menu">
          <!-- left_navigation //-->
          <?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
          <!-- left_navigation_eof /        </div>        <!-- body_text //-->
 <div id="current">
 <?php echo $breadcrumb->trail(' <img  src="images/point.gif"> '); ?>
 </div>
  <?php include('includes/search_include.php');?>
 <div id="main-content">
   <h2><?php echo TEXT_CONTACT_US;?></h2> 
               
