<?php
/*
  $Id$
*/

require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_FAQ_INFO);
require(DIR_WS_ACTIONS.'faq_question.php');
check_uri('/faq_info\.php/');
define('FAQ_HTML_REPLACE','</td></tr><tr><td valign="top" style="float:left;"><img
    src="images/design/answer.gif" alt="" width="23" height="15"></td><td
    class="faq_answer_row">');
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
<!-- header --> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof --> 
<!--body -->
<div id="main">
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<?php include('includes/search_include.php');?>
<div id="main-content">
    <h2><?php echo TEXT_QUESTION_TITLE;?></h2>
    <div class="comment_faq">
    <?php 
    if(isset($faq_question_id)&&$faq_question_id!=''){
      $faq_question_info = tep_get_faq_question_info($faq_question_id);
    ?>
      <table class="redtext" width="100%"><tr>
        <td><img src="images/design/ask.gif" alt="question"></td>
        <td width="90%"><?php echo
        $faq_question_info['ask'];?></td>
      </tr></table>
      <table class="faq_answer" width="100%"><tr>
      <td valign="top">
       <img src="images/design/answer.gif" alt="ask"></td><td class="faq_answer_row"><span>
       <?php 
        $question_answer =
        str_replace('#STORE_NAME#',STORE_NAME,$faq_question_info['answer']);
        $question_answer = str_replace('#REPLACE#',FAQ_HTML_REPLACE,$question_answer);
        echo $question_answer;
       ?>
       </span>
      </td>
      </tr>
      </table>
    <?php }?>
    </div>
    <p class="pageBottom"></p>


    <div class="botton-continue" style="margin-bottom:40px">
      <a href="<?php echo HTTP_SERVER.'/'.implode('/',$link_url_arr).'/';?>"><img
        onmouseout="this.src='images/design/button/faq_back.gif'"  onmouseover="this.src='images/design/button/faq_back_hover.gif'" src="images/design/button/faq_back.gif" alt="<?php echo TEXT_BACK;?>">
      </a>
    </div>
    <?php //question list ?>
    <?php if($q_row = tep_db_fetch_array($last_faq_question_query)){ ?>
    <h2 class="pageHeading"><?php echo $temp_category_info['title'].TEXT_CATEGORY_TITLE;?></h2>
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

</div>
<?php include("includes/float-box.php");?>
</div>
<div id='f_menu'>
<!-- footer_eof --> 
</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

<!--body_EOF -->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
