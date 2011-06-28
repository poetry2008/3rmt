<?php
/*
   $Id$
 */

require('includes/application_top.php');
require(DIR_WS_ACTIONS.'faq_category.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_FAQ);
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
    <?php if (isset($parent_info)&&$parent_info!=null){ ?>
    <h1 class="pageHeading"><?php echo $parent_info['title'].TEXT_QUESTION_TITLE;?></h1>
    <?php }else {?>
    <h1 class="pageHeading"><?php echo TEXT_FAQ_TITLE;?></h1>
    <?php } ?>
    <div class="comment_faq">
    <div class="faq_question_row">
    <img src="images/design/ask.gif" alt="question"><span><a href="<?php echo HTTP_SERVER.'/'.$link_url.'/'.$c_row['romaji'];?>">
      <?php echo $c_row['title'];?>
    </a></span>
    </div>
    <?php while($c_row = tep_db_fetch_array($faq_category_query)){ ?>
    <div class="faq_question_row">
     <img src="images/design/ask.gif" alt="question"><span><a href="<?php echo HTTP_SERVER.'/'.$link_url.'/'.$c_row['romaji'];?>">
          <?php echo $c_row['title'];?>
        </a></span>
    </div>
    <?php } ?>
    </div>
    <p class="pageBottom"></p>
    <?php 
    }
    //this show faq question 
    ?>
    <?php if($q_row = tep_db_fetch_array($faq_question_query)){ ?>
    <h1 class="pageHeading"><?php echo $parent_info['title'].TEXT_QUESTION_TITLE;?></h1>
    <div class="comment_faq">
    <div class="faq_question_row">
    <img src="images/design/ask.gif" alt="question"><span><a href="<?php echo HTTP_SERVER.'/'.$link_url.'/'.$q_row['romaji'].'.html';?>">
      <?php echo $q_row['ask'];?>
    </a></span>
    </div>
    <?php 
    while($q_row = tep_db_fetch_array($faq_question_query)){ 
    ?>
    <div class="faq_question_row">
      <img src="images/design/ask.gif" alt="question"><span><a href="<?php echo HTTP_SERVER.'/'.$link_url.'/'.$q_row['romaji'].'.html';?>">
        <?php echo $q_row['ask'];?>
      </a></span>
      </div>
    <?php
    } 
    ?>
    </div>
    <p class="pageBottom"></p>
    <?php } ?>
    <?php if($link_url != 'faq') { ?>
    <div class="faq_back">
      <a href="<?php echo HTTP_SERVER.'/'.implode('/',$link_arr).'/';?>"><img src="images/design/button/faq_back.gif" alt="<?php echo TEXT_BACK;?>">
      </a>
    </div>
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
