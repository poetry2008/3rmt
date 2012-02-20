<?php
/*
  $Id$
*/

require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_FAQ_INFO);
require(DIR_WS_ACTIONS.'faq_question.php');
define('FAQ_HTML_REPLACE','</td></tr><tr><td valign="top" style="float:left;"><img
    src="./images/a.gif" alt="" width="23" height="15"></td><td
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
<div align="center">
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!--body -->
<table cellpadding="0" cellspacing="0" border="0" class="side_border" width="900">
<tr>
<!-- left_navigation //-->
<td class="left_colum_border" width="171" valign="top">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
</td>
<td class="contents" valign="top">
<div id="content">
    <?php 
    if(isset($faq_question_id)&&$faq_question_id!=''){
      $faq_question_info = tep_get_faq_question_info($faq_question_id);
    ?>
      <div class="pageHeading"><?php echo $faq_question_info['ask'];?></div>
       <?php 
        $question_answer =
        str_replace('#STORE_NAME#',STORE_NAME,$faq_question_info['answer']);
        $question_answer = str_replace('#REPLACE#',FAQ_HTML_REPLACE,$question_answer);
        echo "<ul class='faq_ul'><li>";
        echo $question_answer;
        echo "</li></ul>";
       ?>
    <div class="faq_back">
      <a href="<?php echo HTTP_SERVER.'/'.implode('/',$link_url_arr).'/';?>"><img src="includes/languages/japanese/images/buttons/button_back.gif" alt="<?php echo TEXT_BACK;?>"></a>
    </div>
    <?php }?>


    <?php //question list ?>
    <?php if($q_row = tep_db_fetch_array($last_faq_question_query)){ ?>
    <ul class="faq_ul">
    <li><a href="<?php echo
     HTTP_SERVER.'/'.$link_url.'/'.urlencode($q_row['romaji']).'.html';?>">
      <?php echo $q_row['ask'];?>
    </a>
    </li>
    <?php 
    while($q_row = tep_db_fetch_array($last_faq_question_query)){ 
    ?>
    <li><a href="<?php echo
      HTTP_SERVER.'/'.$link_url.'/'.urlencode($q_row['romaji']).'.html';?>">
        <?php echo $q_row['ask'];?>
      </a>
    </li>
    <?php
    } 
    ?>
    </ul>
    <?php } ?>



</div>
</td>
<td class="right_colum_border" width="171" valign="top">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</td></tr></table>
<div id='f_menu'>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
<!-- footer_eof //--> 
</div>
<!--body_EOF// -->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
