<?php
/*
  $Id$
*/

  require('includes/application_top.php');
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
<div class="headerNavigation"></div>
<h1 class="pageHeading"></h1>
<table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <div id="contents">
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
