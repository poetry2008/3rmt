<?php
/*
   $Id$
 */

require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_FAQ_INFO);
require(DIR_WS_ACTIONS.'faq_question.php');
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
    <div class="pageHeading"><?php echo TEXT_QUESTION_TITLE;?></div>
    <div class="comment_faq">
    <?php 
    if(isset($faq_question_id)&&$faq_question_id!=''){
      $faq_question_info = tep_get_faq_question_info($faq_question_id);
    ?>
      <div class="faq_ask">
        <img src="images/design/ask.gif" alt="question"><h1><?php echo
        $faq_question_info['ask'];?></h1>
      </div>
      <div class="faq_answer">
       <img src="images/design/answer.gif" alt="ask"><span>
       <?php 
        $question_answer =
        str_replace('#STORE_NAME#',STORE_NAME,$faq_question_info['answer']);
        echo $question_answer;
       ?>
       </span>
      </div>
    <?php }?>
    </div>
    <p class="pageBottom"></p>
    <div class="faq_back">
      <a href="<?php echo HTTP_SERVER.'/'.implode('/',$link_url_arr).'/';?>"><img src="images/design/button/faq_back.gif" alt="<?php echo TEXT_BACK;?>">
      </a>
    </div>
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
