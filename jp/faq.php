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
<!--<div class="header_Navigation">
   <?php echo $breadcrumb->trail(' &raquo; '); ?>
</div>-->
    <?php //this show faq category ?>
    <?php if ($c_row = tep_db_fetch_array($faq_category_query)){?>
    <?php if (isset($parent_info)&&$parent_info!=null){ ?>
    <h2 class="pageHeading"><?php echo $parent_info['title'].TEXT_QUESTION_TITLE;?></h2>
    <?php }else {?>
    <h2 class="pageHeading"><?php echo
      TEXT_FAQ_TITLE.'</h2><br><font style="margin-left:12px;">'.TEXT_FAQ_TITLE_END.'</font>';?>
    <?php } ?>
    <div class="comment_faq">
    <table class="faq_question_row"><tr><td>
    <div><img src="images/q.gif" alt="question"></div>
    <div class="faq_question_row_div"><span><a href="<?php echo
    HTTP_SERVER.'/'.$link_url.'/'.urlencode($c_row['romaji']).'/';?>">
      <?php echo $c_row['title'];?>
    </a></span></div>
    </td></tr></table>
    
    <?php while($c_row = tep_db_fetch_array($faq_category_query)){ ?>
    <table class="faq_question_row"><tr><td>
    <div><img src="images/q.gif" alt="question"></div>
   	<div class="faq_question_row_div"><span><a href="<?php echo
		HTTP_SERVER.'/'.$link_url.'/'.urlencode($c_row['romaji']).'/';?>">
          <?php echo $c_row['title'];?>
        </a>
    </span></div>
    </td></tr></table>
    <?php } ?>
    </ul>
    </div>
    <p class="pageBottom"></p>
    <?php 
    }
    //this show faq question 
    if($parent_info){
    ?>
    <h2 class="pageHeading"><?php echo $parent_info['title'].TEXT_QUESTION_TITLE;?></h2>
    <?php } ?>
    <?php if($q_row = tep_db_fetch_array($faq_question_query)){ ?>
    <div class="comment_faq">
    <table class="faq_question_row"><tr><td>
    <div>
    <img src="images/q.gif" alt="question"></div>
    <div class="faq_question_row_div"><span><a href="<?php echo
     HTTP_SERVER.'/'.$link_url.'/'.urlencode($q_row['romaji']).'.html';?>">
      <?php echo $q_row['ask'];?>
    </a></span></div>
    </td></tr>
    </table>
    <?php 
    while($q_row = tep_db_fetch_array($faq_question_query)){ 
    ?>
    <table class="faq_question_row"><tr><td>
    <div><img src="images/q.gif" alt="question"></div><div class="faq_question_row_div"><span><a href="<?php echo
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

    <?php if($link_url != 'faq') { ?>
    <div class="faq_back">
      <a href="<?php echo HTTP_SERVER.'/'.implode('/',$link_arr).'/';?>"><img src="includes/languages/japanese/images/buttons/button_back.gif" alt="<?php echo TEXT_BACK;?>">
      </a>
    </div>
    <?php } ?>

    <?php //this last  show faq category ?>
    <?php if ($last_row = tep_db_fetch_array($last_faq_category_query)){?>
    <?php if (isset($last_parent_info)&&$last_parent_info!=null){ ?>
    <h2 class="pageHeading"><?php echo
      $last_parent_info['title'].TEXT_QUESTION_TITLE;?></h2>
    <?php }else {?>
    <h2 class="pageHeading"><?php echo TEXT_FAQ_TITLE_LAST;?></h2>
    <?php } ?>
    <!--<div  style="border-bottom-style:dotted; width:94%; margin-top:10px; color:#444; margin-left:2px;"></div>-->
    <div class="comment_faq">
    <table class="faq_question_row">
    	<tr><td><div>
    <img src="images/q.gif" alt="question"></div><div class="faq_question_row_div"><span><a href="<?php echo
    HTTP_SERVER.'/'.$last_link_url.'/'.urlencode($last_row['romaji']).'/';?>">
      <?php echo $last_row['title'];?>
    </a></span></div></td></tr>
    </table>
    <?php while($last_row = tep_db_fetch_array($last_faq_category_query)){ ?>
    <table class="faq_question_row"><tr><td>
    	<div><img src="images/q.gif" alt="question"></div>
   		<div class="faq_question_row_div"><span><a href="<?php echo
     HTTP_SERVER.'/'.$last_link_url.'/'.urlencode($last_row['romaji']).'/';?>">
          <?php echo $last_row['title'];?>
        </a></span>
        </div>
        </td></tr>
    </table>
    <?php } ?>
    </div>
    <p class="pageBottom"></p>
    <?php 
    }
    //this show faq question 
    ?>



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