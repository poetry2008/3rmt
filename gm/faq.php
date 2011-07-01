<?php
/*
  $Id$
*/

require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_FAQ);
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
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!--body -->
<div id="main">
<!-- left_navigation //-->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
</div>
<div id="content">
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
