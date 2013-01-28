<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES.$language.'/change_preorder_timeout.php');
  $breadcrumb->add(CPREORDER_TIMEOUT_NAVBAR_TITLE, '');
?>
<?php page_head();?>
</head>
<body>
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<div id="main-content">
<h2><?php echo CPREORDER_TIMEOUT_HEADING_TITLE ; ?></h2>
<table cellpadding="0" border="0" cellspacing="0" class="content_distance" >
<tr><td>
      <div id="detail-div">
        <?php 
        echo sprintf(CPREORDER_TIMEOUT_INFO, '<a href="'.tep_href_link('open.php', 'pname='.urlencode($_GET['pname'])).'">'.CPREORDER_CONTACT_US.'</a>');
        ?> 
      </div>
	  </td></tr>
	  </table>
  </div>
</div>
<?php include('includes/float-box.php');?>
</div>
  <?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
