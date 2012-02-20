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
<?php 
if($current_faq_category_id){
?>
    <?php //this show faq category ?>
    <?php if ($c_row = tep_db_fetch_array($faq_category_query)){?>
    <?php if (isset($parent_info)&&$parent_info!=null){ ?>
    <h2 class="pageHeading"><?php echo $parent_info['title'].TEXT_QUESTION_TITLE;?></h2>
    <?php }else {?>
    <h2 class="pageHeading"><?php echo
      TEXT_FAQ_TITLE.'</h2><br><font style="margin-left:12px;">'.TEXT_FAQ_TITLE_END.'</font>';?>
    <?php } ?>
    <ul class="faq_ul">
    <li><a href="<?php echo
    HTTP_SERVER.'/'.$link_url.'/'.urlencode($c_row['romaji']).'/';?>">
      <?php echo $c_row['title'];?>
    </a></li>
    
    <?php while($c_row = tep_db_fetch_array($faq_category_query)){ ?>
    <li><a href="<?php echo
    HTTP_SERVER.'/'.$link_url.'/'.urlencode($c_row['romaji']).'/';?>">
          <?php echo $c_row['title'];?>
        </a>
    </li>
    <?php } ?>
    </ul>
    <?php 
    }
    //this show faq question 
    ?>
    <?php if($q_row = tep_db_fetch_array($faq_question_query)){ ?>
    <h2 class="pageHeading"><?php echo $parent_info['title'].TEXT_QUESTION_TITLE;?></h2>
    <ul class="faq_ul">
    <li><a href="<?php echo
     HTTP_SERVER.'/'.$link_url.'/'.urlencode($q_row['romaji']).'.html';?>">
      <?php echo $q_row['ask'];?>
    </a>
    </li>
    <?php 
    while($q_row = tep_db_fetch_array($faq_question_query)){ 
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
    <div class="faq_question_row_div"><span><a href="<?php echo
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
}else{
  ?>
    <h2 class="pageHeading"><?php echo
      TEXT_FAQ_TITLE.'</h2><br><font style="margin-left:12px;">'.TEXT_FAQ_TITLE_END.'</font>';?>
  <?php 
  $all_category_sql = "
                      select * from 
                      (
                        select 
                        fcd.is_show,
                        fcd.faq_category_id,
                        fc.parent_id,
                        fc.sort_order,
                        fcd.site_id,
                        fcd.romaji,
                        fcd.title,
                        fcd.keywords,
                        fcd.description 
                        from ".TABLE_FAQ_CATEGORIES." fc, 
                        ".TABLE_FAQ_CATEGORIES_DESCRIPTION. " fcd 
                        where fc.parent_id = '0'
                        and fc.id = fcd.faq_category_id 
                        order by site_id DESC
                      ) c 
                      where (site_id = ".SITE_ID."
                      or site_id = 0) 
                      and is_show = '1'
                      group by c.faq_category_id 
                      order by sort_order,title";
  $all_category_query = tep_db_query($all_category_sql);
  while($one_faq_category = tep_db_fetch_array($all_category_query)){
    ?>
    <div class="all_category_row_div"><a href="<?php echo
    HTTP_SERVER.'/'.$link_url.'/'.urlencode($one_faq_category['romaji']).'/';?>">
      <?php echo $one_faq_category['title'];?>
    </a></div>
    <?php
    echo "<ul>";
   $one_category_question_sql = "select * from (
                      select 
                      fqd.is_show,
                      fq2c.faq_category_id,
                      fqd.faq_question_id,
                      fqd.romaji,
                      fqd.ask,
                      fqd.keywords,
                      fqd.answer,
                      fq.sort_order,
                      fq.created_at,
                      fq.updated_at,
                      fqd.site_id 
                      from ".TABLE_FAQ_QUESTION." fq, 
                           ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd ,
                           ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
                      where fq.id = fqd.faq_question_id 
                      and fq.id = fq2c.faq_question_id 
                      and fq2c.faq_category_id = '".
                      $one_faq_category['faq_category_id'] . "' 
                      order by fqd.site_id DESC
                      ) c  
                      where (site_id = ".SITE_ID." 
                      or site_id = 0) 
                      and is_show = '1'
                      group by c.faq_question_id 
                      order by c.sort_order,c.ask,c.faq_question_id 
                      ";
    $one_category_question_query = tep_db_query($one_category_question_sql);
    while($one_category_question_row = tep_db_fetch_array($one_category_question_query)){
      echo "<li>";
      ?>
      <a href="<?php echo
    HTTP_SERVER.'/'.$link_url.'/'.urlencode($one_faq_category['romaji']).'/'.
    urlencode($one_category_question_row['romaji']).'.html';?>">
      <?php echo $one_category_question_row['ask'];?>
      </a>
      <?php
      echo "</li>";
    }
    echo "</ul>";
  }
}
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
