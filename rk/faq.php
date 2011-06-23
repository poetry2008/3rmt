<?php
/*
   $Id$
 */

require('includes/application_top.php');
require(DIR_WS_ACTIONS.'faq_category.php');
?>
    <?php page_head();?>
    </head>
    <?php
    if (isset($body_option)) {
      ?>
        <body <?php echo $body_option;?>>
        <?php
    } else {
      ?>
        <body>
        <?php
    }
  ?>
    <div align="center">
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <table width="900" summary="container" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
    <td valign="top" class="left_colum_border">
    <!-- left_navigation //-->
    <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
    <!-- left_navigation_eof //-->
    </td>
    <td valign="top"  id="contents">
    <?php //this show faq category ?>
    <?php if ($c_row = tep_db_fetch_array($faq_category_query)){?>
    <h1 class="pageHeading">faq</h1>
    <div class="comment">
    <div class="faq_category_row">
    <a href="/<?php echo $link_url.'/'.$c_row['romaji'];?>">
      <?php echo $c_row['title'];?>
    </a>
    <?php while($c_row = tep_db_fetch_array($faq_category_query)){ ?>
        <a href="/<?php echo $link_url.'/'.$c_row['romaji'];?>">
          <?php echo $c_row['title'];?>
        </a>
    <?php } ?>
    </div>
    </div>
    <p class="pageBottom"></p>
    <?php 
    }
    //this show faq question 
    ?>
    <?php if($q_row = tep_db_fetch_array($faq_question_query)){ ?>
    <h1 class="pageHeading">question</h1>
    <div class="comment">
    <a href="/<?php echo $link_url.'/'.$q_row['romaji'].'.html';?>">
      <?php echo $q_row['ask'];?>
    </a>
    <?php 
    while($q_row = tep_db_fetch_array($faq_question_query)){ 
    ?>
      <a href="/<?php echo $link_url.'/'.$q_row['romaji'].'.html';?>">
        <?php echo $q_row['ask'];?>
      </a>
    <?php
    } 
    ?>
    </div>
    <p class="pageBottom"></p>
    <?php } ?>
    </td>
    <td valign="top" class="right_colum_border">
    <!-- right_navigation //--> 
    <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
    <!-- right_navigation_eof //--></td> 
    </tr> 
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
