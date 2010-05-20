<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  define('FILENAME_FAQ', 'faq.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_FAQ);

  define('FAQ_CATEGORIES_LIST', 'よくある質問');

  $breadcrumb->add(FAQ_CATEGORIES_LIST, tep_href_link(FILENAME_PAGE, 'pID=7'));

  $g_id = $_GET['g_id'];
  $q_id = @$_GET['q_id'];
  if ($q_id && $g_id) {
    $page = 'question';
    $question = tep_get_faq_questions($q_id);
    $g_categories = tep_get_category_by_id($g_id, SITE_ID, $languages_id);
    $faq_categories = tep_get_faq_categories($question['c_id']);
    $sub_categories = tep_get_categories_by_parent_id($g_id);
    //if (!$question or $g_id != $question['g_id']) {
      // 404
    //}
    $breadcrumb->add($question['question'], tep_href_link('faq.php', 'g_id='.$g_id.'&q_id='.$q_id));
  } elseif ($g_id) { 
    $page = 'index';
    $g_categories = tep_get_category_by_id($g_id, SITE_ID, $languages_id);
    $all_faq_categories = tep_get_faq_categories_by_g_id($g_id);
    
    print_r($sub_categories);
    $breadcrumb->add($g_categories['categories_name'] . 'のよくある質問', tep_href_link('faq.php', 'g_id='.$g_id));
  } else {
    // 404
    forward404();
    exit('401');
  }
  if (!$g_categories) {
    // 404
    forward404();
    exit('402');
  }
?>
<?php page_head();?>
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<!-- left_navigation //-->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- left_navigation_eof //-->
<!-- body_text //-->
<div id="content">
  <div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<?php if($page == 'question') {?>
        <h2 class="pageHeading"><?php echo $g_categories['categories_name']; ?></h2>
        <h2 align="right"><?php echo $g_categories['categories_name']; ?>の<?php echo $faq_categories['category']; ?></h2>
        <h3 class="redtext"><img src='images/q.gif'><?php echo $question['question']; ?></h3>
        <p><img src='images/a.gif'><?php echo $question['answer'];?></p>

        <p><a href="<?php echo tep_href_link('faq.php', 'g_id='.$g_id)?>"><?php echo $g_categories['seo_name']; ?>のよくある質問一覧へ戻る</a></p>
        <p>このページは<?php
    foreach($sub_categories as $g_sub_categories) {
      echo '「<a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . (int)$g_id . '_' . $g_sub_categories['categories_id']) . '"><em class="bold">' . $g_sub_categories['categories_name'] . '</em></a>」';
    }
?>に関する<strong><?php echo $g_categories['seo_name']; ?></strong>のよくある質問とその回答です。こちらの回答をお読みいただいても問題が解決しなかった場合は、
お手数ではございますが、サポートセンターへお問い合わせください。</p> 
        <!-- other faq -->
        <h3><?php echo $g_categories['categories_name']; ?>：<?php echo $faq_categories['category'];?>のFAQ</h3>
        <?php
          $questions = tep_get_questions_by_c_id($faq_categories['c_id']);
        ?>
        <ul>
        <?php
    foreach($questions as $q){ ?>
      <li><a href='<?php echo tep_href_link('faq.php', 'g_id='.$faq_categories['g_id'].'&q_id='.$q['q_id']);?>'><?php echo $q['question'];?></a></li>
    <?php
    }
    ?>
        </ul>
        <br>

<?php } else {?>
  <!-- index //-->
        <h2 class="pageHeading"><?php echo $g_categories['categories_name']; ?>のよくある質問（FAQ)</h2>
        <h2 align="right"><?php echo $g_categories['seo_name']; ?>のFAQ一覧</h2>
        <p>このページでは、お客様から寄せられる<strong><?php echo $g_categories['seo_name']; ?></strong>のよくある質問にお答えしています。<?php echo $g_categories['seo_name']; ?>以外のFAQを閲覧する場合は<a href="/info-7.html">コチラ</a>をクリックしてください。記載のないご質問は、お手数ではございますがサポートセンターへお問い合わせください。</p> 
        <?php foreach($all_faq_categories as $faq_categories){?>
          <?php
          $questions = tep_get_questions_by_c_id($faq_categories['c_id']);
          if($questions){ ?>
            <h3><?php echo $faq_categories['category'];?>：<?php echo $g_categories['categories_name']; ?></h3>
              <ul>
              <?php
          foreach($questions as $q){ ?>
            <li><a href='<?php echo tep_href_link('faq.php', 'g_id='.$faq_categories['g_id'].'&q_id='.$q['q_id']);?>'><?php echo $q['question'];?></a></li>
          <?php
          }
          ?>
              </ul>
              <p class="page_top"><a href="#top">▲このページのトップへ</a></p>

              <?php
          }
          ?>
        <?php }?>
<?php }?>
        <br><p class="smalltext"><span class="redtext">※</span>&nbsp;サービス内容および提供条件は、改善等のため予告なく変更する場合があります。</p><br><br> 
      </div>
      <!-- body_text_eof //--> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
