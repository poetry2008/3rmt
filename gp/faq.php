<?php
/*
  $Id$
*/

require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_FAQ);
require(DIR_WS_ACTIONS.'faq_category.php');
$flag_question = true;             
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
    <h2 class="pageHeading"><?php echo $parent_info['title'].TEXT_QUESTION_TITLE;?></h2><div class="comment"><div class="comment_faq_box">
    <?php }else {?>
    <h2 class="pageHeading"><?php echo
      TEXT_FAQ_TITLE.'</h2><div class="comment"><div class="comment_faq_box"><font color="red" style=" font-size:14px; padding-left:2px;">'.TEXT_FAQ_TITLE_END.'</font>';?>
    <?php } ?>    <div class="comment_faq">
    <table class="faq_question_row">
    <tr><td>
    <img src="images/design/ask.gif" alt="question"></td><td class="faq_question_row_div"><span><a href="<?php echo
    HTTP_SERVER.'/'.$link_url.'/'.urlencode($c_row['romaji']).'/';?>">
    <?php echo $c_row['title'];?>
    </a></span></div></td></tr>
    </table>
    <?php while($c_row = tep_db_fetch_array($faq_category_query)){ ?>
    <table class="faq_question_row"><tr>
    	<td><img src="images/design/ask.gif" alt="question"></td>
   		<td class="faq_question_row_div"><span><a href="<?php echo
     HTTP_SERVER.'/'.$link_url.'/'.urlencode($c_row['romaji']).'/';?>">
          <?php echo $c_row['title'];?>
        </a></span>
        </td>
        </tr>
    </table>
    <?php } ?>
    </div>
    <p class="pageBottom"></p>
    <?php if($link_url != 'faq' && $flag_question) { ?>
    <div class="faq_back">
      <a href="<?php echo HTTP_SERVER.'/'.implode('/',$link_arr).'/';?>"><img src="images/design/button/faq_back.gif" alt="<?php echo TEXT_BACK;?>">
      </a>
    </div>
    <?php 
      $flag_question = false;
    }?>
    </div>
    </div>
    <?php 
    }
    //this show faq question 
    ?>
    <?php if($q_row = tep_db_fetch_array($faq_question_query)){ ?>
    <h2 class="pageHeading"><?php echo $parent_info['title'].TEXT_QUESTION_TITLE;?></h2>
    <div class="comment">
    <div class="comment_faq_box">
    <div class="comment_faq">
    <table class="faq_question_row"><tr><td>
    <img src="images/design/ask.gif" alt="question">
    </td>
        <td class="faq_question_row_div"><span><a href="<?php echo
     HTTP_SERVER.'/'.$link_url.'/'.urlencode($q_row['romaji']).'.html';?>">
      <?php echo $q_row['ask'];?>
    </a></span></td>

    </tr>
    </table>
    <?php 
    while($q_row = tep_db_fetch_array($faq_question_query)){ 
    ?>
    <table class="faq_question_row">
    <tr>
        <td><img src="images/design/ask.gif" alt="question"> </td>
        <td class="faq_question_row_div"><span><a href="<?php echo
      HTTP_SERVER.'/'.$link_url.'/'.urlencode($q_row['romaji']).'.html';?>">
        <?php echo $q_row['ask'];?>
      </a></span>
      </td>
    </tr></table>
    <?php
    } 
    ?>
    </div>
    <p class="pageBottom"></p>
    <?php if($link_url != 'faq' && $flag_question) { ?>
    <div class="faq_back">
      <a href="<?php echo HTTP_SERVER.'/'.implode('/',$link_arr).'/';?>"><img src="images/design/button/faq_back.gif" alt="<?php echo TEXT_BACK;?>">
      </a>
    </div>
    <?php 
      $flag_question = false;
    } 
    ?>
    </div></div>
    <?php 
    }
    ?>
    <?php //this last  show faq category ?>
    <?php if ($last_row = tep_db_fetch_array($last_faq_category_query)){?>
    <?php if (isset($last_parent_info)&&$last_parent_info!=null){ ?>
    <h2 class="pageHeading"><?php echo
      $last_parent_info['title'].TEXT_QUESTION_TITLE;?></h2>
    <?php }else {?>
    <h2 class="pageHeading"><?php echo TEXT_FAQ_TITLE_LAST;?></h2>
    <?php } ?>
    <div class="comment">
    <div class="comment_faq_box">

    <!--<div  style="border-bottom-style:dotted; width:100%; margin-top:10px;
color:#444; margin:0 2px;"></div>-->
    <div class="comment_faq">
    <table class="faq_question_row">
    <tr><td>
    <img src="images/design/ask.gif" alt="question"></td><td class="faq_question_row_div"><span><a href="<?php echo
    HTTP_SERVER.'/'.$last_link_url.'/'.urlencode($last_row['romaji']).'/';?>">
      <?php echo $last_row['title'];?>
    </a></span></td></tr>
    </table>
    <?php while($last_row = tep_db_fetch_array($last_faq_category_query)){ ?>
    <table class="faq_question_row"><tr>
        <td><img src="images/design/ask.gif" alt="question"></td>
        <td class="faq_question_row_div"><span><a href="<?php echo
     HTTP_SERVER.'/'.$last_link_url.'/'.urlencode($last_row['romaji']).'/';?>">
          <?php echo $last_row['title'];?>
        </a></span>
        </td></tr>
    </table>
    <?php } ?>
    <table width="94%" >
        <tr><td align="right">
    <?php if($link_url != 'faq' && $flag_question) { ?>
    <div class="faq_back">
      <a href="<?php echo HTTP_SERVER.'/'.implode('/',$link_arr).'/';?>"><img src="images/design/button/faq_back.gif" alt="<?php echo TEXT_BACK;?>">
      </a>
    </div>
    <?php }?>
        </td></tr>
    </table>
    </div>
    <p class="pageBottom"></p>
    </div></div>
    <?php 
    }
    //this show faq question 
    ?>



</div></div>
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
<!--body_EOF// -->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
