<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  define('FILENAME_FAQ', 'faq.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_FAQ);

  define('FAQ_CATEGORIES_LIST', 'FAQ_CATEGORIES_LIST');

  $breadcrumb->add(FAQ_CATEGORIES_LIST, tep_href_link(FILENAME_PAGE, 'pID=7'));

  $g_id = $_GET['g_id'];
  $q_id = @$_GET['q_id'];
  if ($q_id && $g_id) {
    $page = 'question';
    $question = tep_get_faq_questions($q_id);
    $g_categories = tep_get_category_by_id($g_id, SITE_ID, $languages_id);
    $faq_categories = tep_get_faq_categories($question['c_id']);
    //if (!$question or $g_id != $question['g_id']) {
      // 404
    //}
    $breadcrumb->add($question['question'], tep_href_link('faq.php', 'g_id='.$g_id.'&q_id='.$q_id));
  } elseif ($g_id) { 
    $page = 'index';
    $g_categories = tep_get_category_by_id($g_id, SITE_ID, $languages_id);
    $all_faq_categories = tep_get_faq_categories_by_g_id($g_id);
    $breadcrumb->add($g_categories['categories_name'], tep_href_link('faq.php', 'g_id='.$g_id));
  } else {
    // 404
    exit('401');
  }
  if (!$g_categories) {
    // 404
    exit('402');
  }
?>
<?php page_head();?>
</head>
<body>
<div class="body_shadow" align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"><!-- left_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td id="contents" valign="top">
<?php if($page == 'question') {?>
        <h1 class="pageHeading"><?php echo $g_categories['categories_name']; ?></h1>
        <h2 align="right"><?php echo $faq_categories['category']; ?></h2>
        <h3 class="redtext"><img src='images/q.gif'><?php echo $question['question']; ?></h3>
        <p><img src='images/a.gif'><?php echo $question['answer'];?></p>
        <h3><?php echo $faq_categories['category'];?></h3>
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
        <p class="smalltext"><span class="tedtext">x</span> xxxxxxxxxxxxxx</p>

<?php } else {?>
  <!-- index //-->
        <h1 class="pageHeading"><?php echo $g_categories['categories_name']; ?></h1>

        <?php foreach($all_faq_categories as $faq_categories){?>
          <?php
          $questions = tep_get_questions_by_c_id($faq_categories['c_id']);
          if($questions){ ?>
            <h3><?php echo $faq_categories['category'];?></h3>
              <ul>
              <?php
          foreach($questions as $q){ ?>
            <li><a href='<?php echo tep_href_link('faq.php', 'g_id='.$faq_categories['g_id'].'&q_id='.$q['q_id']);?>'><?php echo $q['question'];?></a></li>
          <?php
          }
          ?>
              </ul>
              <p class="page_top"><a href="#top">TOP</a></p>
              <?php
          }
          ?>
        <?php }?>

<?php }?>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"><!-- right_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
      <!-- right_navigation_eof //-->
      </td>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
