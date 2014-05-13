<?php
/*
  $Id$
*/

require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_FAQ);
require(DIR_WS_ACTIONS.'faq_category.php');
check_uri('/faq\.php/');
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
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<?php include('includes/search_include.php');?>
<div id="main-content">
    <?php //this show faq category ?>
    <?php if ($c_row = tep_db_fetch_array($faq_category_query)){?>
    <?php if (isset($parent_info)&&$parent_info!=null){ ?>
    <h2><?php echo $parent_info['title'].TEXT_QUESTION_TITLE;?></h2>
    <?php }else {?>
    <h2 class="pageHeading"><?php echo
      TEXT_FAQ_TITLE.'</h2><div style="line-height:21px; margin-top:13px; padding-left:6px;">'.TEXT_FAQ_TITLE_END.'</div>';?>
    <?php } ?>
    <div class="comment_faq">
    <table class="faq_question_row">
    	<tr><td><div>
    <img src="images/design/ask.gif" alt="question"></div><div class="faq_question_row_div"><span><a href="<?php echo
    HTTP_SERVER.'/'.$link_url.'/'.urlencode($c_row['url_words']).'/';?>">
      <?php echo $c_row['title'];?>
    </a></span></div></td></tr>
    </table>
    <?php while($c_row = tep_db_fetch_array($faq_category_query)){ ?>
    <table class="faq_question_row"><tr><td>
    	<div><img src="images/design/ask.gif" alt="question"></div>
   		<div class="faq_question_row_div"><span><a href="<?php echo
     HTTP_SERVER.'/'.$link_url.'/'.urlencode($c_row['url_words']).'/';?>">
          <?php echo $c_row['title'];?>
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
    <?php if($q_row = tep_db_fetch_array($faq_question_query)){ ?>
    <h2 class="pageHeading"><?php echo $parent_info['title'].TEXT_QUESTION_TITLE;?></h2>
    <div class="comment_faq">
    <table class="faq_question_row"><tr><td>
    <div>
    <img src="images/design/ask.gif" alt="question"></div>
    <div class="faq_question_row_div"><span><a href="<?php echo
     HTTP_SERVER.'/'.$link_url.'/'.urlencode($q_row['url_words']).'.html';?>">
      <?php echo $q_row['ask'];?>
    </a></span></div>
    </td></tr>
    </table>
    <?php 
    while($q_row = tep_db_fetch_array($faq_question_query)){ 
    ?>
    <table class="faq_question_row"><tr><td>
    <div><img src="images/design/ask.gif" alt="question"></div><div class="faq_question_row_div"><span><a href="<?php echo
      HTTP_SERVER.'/'.$link_url.'/'.urlencode($q_row['url_words']).'.html';?>">
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
    <div class="botton-continue" style="margin-bottom:40px">
      <a href="<?php echo HTTP_SERVER.'/'.implode('/',$link_arr).'/';?>"><img
      src="images/design/button/faq_back.gif"
      onmouseout="this.src='images/design/button/faq_back.gif'"    onmouseover="this.src='images/design/button/faq_back_hover.gif'" alt="<?php echo TEXT_BACK;?>">
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
    <div class="comment_faq">
    <table class="faq_question_row">
    	<tr><td><div>
    <img src="images/design/ask.gif" alt="question"></div><div class="faq_question_row_div"><span><a href="<?php echo
    HTTP_SERVER.'/'.$last_link_url.'/'.urlencode($last_row['url_words']).'/';?>">
      <?php echo $last_row['title'];?>
    </a></span></div></td></tr>
    </table>
    <?php while($last_row = tep_db_fetch_array($last_faq_category_query)){ ?>
    <table class="faq_question_row"><tr><td>
    	<div><img src="images/design/ask.gif" alt="question"></div>
   		<div class="faq_question_row_div"><span><a href="<?php echo
     HTTP_SERVER.'/'.$last_link_url.'/'.urlencode($last_row['url_wrods']).'/';?>">
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



</div>
</div>
<?php include('includes/float-box.php');?>

</div>

<div id='f_menu'>
<!-- footer_eof //--> 
</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

<!--body_EOF// -->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
