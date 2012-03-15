<?php
/*
  $Id$

*/

  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/mix_graph.php');  
?>
<?php page_head();?>
</head>
<body>
<div align="center">
  <!-- header //-->
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td valign="top" id="contents">
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              <?php
              if (PICTURE_BAR_IS_SHOW == 'true') {
              ?>
              <div class="message">
              <?php echo HEADING_TITLE;?> 
              </div>
              <!--<div style="width: 528px; overflow-x: scroll;">--> 
              <img src="<?php echo tep_href_link('show_mix_graph.php?width=872&height=400');?>" alt="pic"> 
              <!--</div>--> 
              <div class="message">
              <?php echo GRAPH_BOTTOM_READ;?> 
              </div>
              <?php
              }
              ?>
            </td>
          </tr>
        </table>
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
