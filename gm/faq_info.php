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
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!--body -->
<div id="main">
<!-- left_navigation //-->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
</div>
<div class="header_Navigation">
   <?php echo $breadcrumb->trail(' &raquo; '); ?>
</div>
<div id="content">
    <div class="pageHeading"><?php echo TEXT_QUESTION_TITLE;?></div>
    <div class="comment_faq">
    <?php 
    if(isset($faq_question_id)&&$faq_question_id!=''){
      $faq_question_info = tep_get_faq_question_info($faq_question_id);
    ?>
      <h3 class="redtext">
        <img src="images/design/ask.gif" alt="question"><?php echo
        $faq_question_info['ask'];?>
      </h3>
      <table class="faq_answer"><tr><td>
      <div>
       <img src="images/design/answer.gif" alt="ask"></div><div class="faq_answer_row"><span>
       <?php 
        $question_answer =
        str_replace('#STORE_NAME#',STORE_NAME,$faq_question_info['answer']);
        echo $question_answer;
       ?>
       </span>
      </div>
      </td></tr></table>
    <?php }?>
    </div>
    <p class="pageBottom"></p>


    <div class="faq_back">
      <a href="<?php echo HTTP_SERVER.'/'.implode('/',$link_url_arr).'/';?>"><img src="images/design/button/faq_back.gif" alt="<?php echo TEXT_BACK;?>">
      </a>
    </div>


    <?php //question list ?>
    <?php if($q_row = tep_db_fetch_array($last_faq_question_query)){ ?>
    <h2 class="pageHeading"><?php echo $temp_category_info['title'].TEXT_CATEGORY_TITLE;?></h2>
    <div  style="border-bottom-style:dotted; width:94%; margin-top:10px; color:#444; margin-left:2px;"></div>
    <div class="comment_faq">
    <table class="faq_question_row"><tr><td>
    <div>
    <img src="images/design/ask.gif" alt="question"></div>
    <div class="faq_question_row_div"><span><a href="<?php echo
     HTTP_SERVER.'/'.$link_url.'/'.urlencode($q_row['romaji']).'.html';?>">
      <?php echo $q_row['ask'];?>
    </a></span></div>
    </td></tr>
    </table>
    <?php 
    while($q_row = tep_db_fetch_array($last_faq_question_query)){ 
    ?>
    <table class="faq_question_row"><tr><td>
    <div><img src="images/design/ask.gif" alt="question"></div><div class="faq_question_row_div"><span><a href="<?php echo
      HTTP_SERVER.'/'.$link_url.'/'.urlencode($q_row['romaji']).'.html';?>">
        <?php echo $q_row['ask'];?>
      </a></span>
      </div>
      </td></tr></table>
    <?php
    } 
    ?>
    </div>
    <p class="pageBottom"></p>
    <?php } ?>



</div>
<div id='r_menu'>
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
<div id='f_menu'>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
<!-- footer_eof //--> 
</div>
<!--body_EOF// -->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
