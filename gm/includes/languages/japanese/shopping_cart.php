<?php
/*
  $Id: shopping_cart.php,v 1.9 2003/07/02 12:42:19 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE', '����åԥ󥰥�����');
define('HEADING_TITLE', '����åԥ󥰥�����');
define('TABLE_HEADING_REMOVE', '���');
define('TABLE_HEADING_QUANTITY', '����');
define('TABLE_HEADING_MODEL', '����');
define('TABLE_HEADING_PRODUCTS', '����̾');
define('TABLE_HEADING_TOTAL', '���');
define('TEXT_CART_EMPTY', '<p class="redtext"><b>����åԥ󥰥����Ȥˤϲ������äƤ��ޤ���</b></p>
<p>������åԥ󥰥����ƥ�ϡ�<b>JavaScript</b>��<b>Cookie</b>�����Ѥ��Ƥ��ޤ�������åԥ󥰤����Ѥ�����������ˤϥ֥饦����JavaScript��Cookie�����꤬ͭ���ˤʤäƤ���ɬ�פ�����ޤ����̾�ä�������Ѥ���ɬ�פϤ������ޤ��󤬡�����ʸ�����ޤ������ʤ�����<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">������</a>��ꤪ�䤤��碌����������</p>
<h3>�֥饦����������ˡ</h3>
<p>����ʸ���Ǥ��ʤ������ͤ�<br>������򤪤����������ޤ�����������������ˡ�򻲾Ȥ�������ǧ���Ƥ���������</p>
<img src="images/design/question.gif" alt="" width="16" height="15">&nbsp;<a href="' . tep_href_link(FILENAME_BROWSER_IE6X) . '">Internet&nbsp;Explorer6��������ˡ�Ϥ�����</a>');
define('SUB_TITLE_SUB_TOTAL', '����:');
define('SUB_TITLE_TOTAL', '���:');

define('OUT_OF_STOCK_CANT_CHECKOUT', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' ���ξ��ʤϤ���˾�ο��̤���ݤǤ��ޤ���<br><b>ͽ����ʸ�򾵤äƤ���ޤ��Τǡ����䤤��碌�򤪴ꤤ�������ޤ���</b>');
//define('OUT_OF_STOCK_CAN_CHECKOUT', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' ���ξ��ʤϤ���˾�ο��̤��߸ˤˤ������ޤ���<br>���Τޤ޹�����³����³�Ԥ��Ƥ��������ȡ�����ʸ�γ�ǧ���̤�ȯ����ǽ�ʿ��̤��ǧ���뤳�Ȥ��Ǥ��ޤ���');
define('OUT_OF_STOCK_CAN_CHECKOUT', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' ���ξ��ʤϤ���˾�ο��̤��߸ˤˤ������ޤ���<br>���Τޤ޹�����³����³�Ԥ��Ƥ��������ȡ�����Ǽ����Ϣ�����Ƥ��������ޤ���');

// '... Make any changes above? Click.' (tamura 2002/03/28 �ɲ�)
define('TEXT_UPDATE_CART_INFO', '<b><font color="#ff0000">��</font> ���̤��ѹ���������������Ϲ������Ƥ�������!</b>');   //Add Japanese osCommerce
define('TABLE_HEADING_IMAGE', '����');
define('TABLE_HEADING_OPERATE', '���');
define('TEXT_DEL_LINK', '���');
?>
