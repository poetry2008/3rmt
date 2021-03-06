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
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading">
<?php echo CPREORDER_TIMEOUT_HEADING_TITLE ; ?>
</h1>
<table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
      <div id="contents">
        <?php 
        echo sprintf(CPREORDER_TIMEOUT_INFO, '<a href="'.tep_href_link('open.php', 'pname='.urlencode($_GET['pname'])).'">'.CPREORDER_CONTACT_US.'</a>');
        ?> 
      </div>
    </td>
  </tr>
</table>
</div>
<div id="r_menu">
  <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
