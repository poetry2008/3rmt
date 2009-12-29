<?php
/*
  $Id: reviews.php,v 1.3 2004/05/26 05:05:11 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  define('HEADING_TITLE','コンテンツ共通設定');
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();"> 
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<table border="0" width="100%" cellspacing="2" cellpadding="2"> 
  <tr> 
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft"> 
        <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> 
      </table></td> 
    <!-- body_text //--> 
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
        <tr> 
          <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td> 
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr> 
          <td>
            <table width="100%"  border="0" cellspacing="0" cellpadding="2"> 
              <tr> 
                <td colspan="2" class="formAreaTitle">基本</td> 
              </tr>
              <tr>
                <td width="150" valign="top" class="main">ホームページタイトル</td>
                <td class="smallText"><?php echo tep_draw_input_field('title',$title,'style="width:250px;"');?><br>
ブラウザのタイトルとなります。</td>
              </tr> 
              <tr> 
                <td valign="top" class="main">キーワード</td> 
                <td class="smallText"><?php echo tep_draw_input_field('keywords',$title,'style="width:400px;"');?><br>
                  ホームページのキーワードをカンマ「,」区切りで入力してください。METAタグに反映されます。</td> 
              </tr>
              <tr>
                <td valign="top" class="main">ホームページ説明</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('description',$title,'','','','style="width:400px;height:50px;"');?><br>
                  ホームページの説明を入力してください。METAタグに反映されます。</td>
              </tr> 
              <tr> 
                <td valign="top" class="main">ロボット</td> 
                <td class="smallText"><input name="robots" type="radio" value="yes">
                index,follow 
                <input name="robots" type="radio" value="no">
                no<br>
                検索エンジンにインデクスさせる場合はindex.followにチェックを入れてください。</td> 
              </tr> 
              <tr> 
                <td valign="top" class="main">著作者</td> 
                <td class="smallText"><?php echo tep_draw_input_field('copyright',$title);?><br>
                  ホームページの著作者を入力してください。</td> 
              </tr> 
              <tr> 
                <td colspan="2" class="formAreaTitle">メール</td> 
              </tr> 

              <tr> 
                <td valign="top" class="main">Eメール署名</td> 
                <td class="smallText"><?php echo tep_draw_input_field('email_footer',$title);?><br>
                  送信される全てのメールのフッターに表示されます。</td> 
              </tr> 
              <tr> 
                <td valign="top" class="main">会員登録メール</td> 
                <td class="smallText"><?php echo tep_draw_textarea_field('email_creat_account',$title,'','','','style="width:400px;height:50px;"');?><br>
                  アカウント作成時に送信されるメールです。</td> 
              </tr>
              <tr>
                <td valign="top" class="main">注文メール</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('email_order',$title,'','','','style="width:400px;height:50px;"');?><br>
                  注文時に送信されるメールです。</td>
              </tr>
              <tr>
                <td valign="top" class="main">代金引換</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('email_cod_table',$title,'','','','style="width:400px;height:50px;"');?><br>
                注文時に送信される代金引換のメールです。有効にするにはモジュール設定→決済モジュール→代金引換をONにしてください。</td>
              </tr>
              <tr>
                <td valign="top" class="main">銀行振込</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('email_bank',$title,'','','','style="width:400px;height:50px;"');?><br>
                注文時に送信される代金引換のメールです。有効にするにはモジュール設定→決済モジュール→銀行振り込みをONにしてください。</td>
              </tr>
              <tr>
                <td valign="top" class="main">郵便振替</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('email_postal',$title,'','','','style="width:400px;height:50px;"');?><br>
                注文時に送信される郵便振替のメールです。有効にするにはモジュール設定→決済モジュール→郵便振替をONにしてください。</td>
              </tr>
              <tr>
                <td valign="top" class="main">クレジットカード</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('email_cc',$title,'','','','style="width:400px;height:50px;"');?><br>
                注文時に送信されるクレジットカードのメールです。有効にするにはモジュール設定→決済モジュール→クレジットカードをONにしてください。</td>
              </tr>
              <tr>
                <td valign="top" class="main">ショップロゴ</td>
                <td class="smallText"><?php echo tep_draw_textarea_field('logo',$title,'','','','style="width:400px;height:50px;"');?><br>
                  納品書、配送票、カタログページのヘッダー、管理ページのヘッダーに表示されるロゴです。</td>
              </tr>
              <tr>
                <td valign="top" class="main">&nbsp;</td>
                <td class="smallText">&nbsp;</td>
              </tr> 
          </table></td> 
        </tr> 
      </table></td> 
    <!-- body_text_eof //--> 
  </tr> 
</table> 
<!-- body_eof //--> 
<!-- footer //--> 
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
<!-- footer_eof //--> 
<br> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
