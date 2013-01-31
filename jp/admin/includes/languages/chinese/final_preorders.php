<?php
/*
  $Id$

*/

define('HEADING_TITLE', '预约内容编辑');
define('HEADING_TITLE_SEARCH', '预约ID:');
define('HEADING_TITLE_STATUS', '状态:');
define('ADDING_TITLE', '添加商品');

define('ENTRY_UPDATE_TO_CC', '(Update to <b>Credit Card</b> to view CC fields.)');
define('TABLE_HEADING_COMMENTS', '说明');
define('TABLE_HEADING_CUSTOMERS', '顾客名');
define('TABLE_HEADING_ORDER_TOTAL', '预约总额');
define('TABLE_HEADING_DATE_PURCHASED', '预约日');
define('TABLE_HEADING_STATUS', '状态');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_QUANTITY', '数量');
define('TABLE_HEADING_PRODUCTS_MODEL', '型号');
define('TABLE_HEADING_PRODUCTS', '商品名');
define('TABLE_HEADING_TAX', '消费税');
define('TABLE_HEADING_TOTAL', '合计');
define('TABLE_HEADING_UNIT_PRICE', '单价');
define('TABLE_HEADING_TOTAL_PRICE', '合计');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', '通知处理情况');
define('TABLE_HEADING_DATE_ADDED', '更新日');

define('ENTRY_CUSTOMER', '顾客名:');
define('ENTRY_CUSTOMER_NAME', '名字');
//add
define('ENTRY_CUSTOMER_NAME_F', '名字(注音假名)');
define('ENTRY_CUSTOMER_COMPANY', '公司名');
define('ENTRY_CUSTOMER_ADDRESS', '住处');
define('ENTRY_CUSTOMER_SUBURB', '建筑物的名称');
define('ENTRY_CUSTOMER_CITY', '市区町村');
define('ENTRY_CUSTOMER_STATE', '都道府县');
define('ENTRY_CUSTOMER_POSTCODE', '邮政编码');
define('ENTRY_CUSTOMER_COUNTRY', '国名');

define('ENTRY_SOLD_TO', '买入者:');
define('ENTRY_DELIVERY_TO', '投递地址:');
define('ENTRY_SHIP_TO', '投递地址:');
define('ENTRY_SHIPPING_ADDRESS', '投递地址:');
define('ENTRY_BILLING_ADDRESS', '请求地:');
define('ENTRY_PAYMENT_METHOD', '支付方法:');
define('ENTRY_CREDIT_CARD_TYPE', '信用卡类型:');
define('ENTRY_CREDIT_CARD_OWNER', '信用卡名:');
define('ENTRY_CREDIT_CARD_NUMBER', '卡号:');
define('ENTRY_CREDIT_CARD_EXPIRES', '卡有效期:');
define('ENTRY_SUB_TOTAL', '小计:');
define('ENTRY_TAX', '消费税:');
define('ENTRY_SHIPPING', '配送方法:');
define('ENTRY_TOTAL', '合计:');
define('ENTRY_DATE_PURCHASED', '预约日:');
define('ENTRY_STATUS', '状态:');
define('ENTRY_DATE_LAST_UPDATED', '更新日:');
define('ENTRY_NOTIFY_CUSTOMER', '通知处理情况:');
define('ENTRY_NOTIFY_COMMENTS', '追加说明:');
define('ENTRY_PRINTABLE', '打印交货单');

define('TEXT_INFO_HEADING_DELETE_ORDER', '预约删除');
define('TEXT_INFO_DELETE_INTRO', '确定要删除这个预约吗?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', '还原库存数');
define('TEXT_DATE_ORDER_CREATED', '建成日:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', '更新日:');
define('TEXT_DATE_ORDER_ADDNEW', '追加商品');
define('TEXT_INFO_PAYMENT_METHOD', '支付方法:');

define('TEXT_ALL_ORDERS', '所有的预约');
define('TEXT_NO_ORDER_HISTORY', '没有预约履历');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', '预约受理情况的通知');
define('EMAIL_TEXT_ORDER_NUMBER', '预约受理号码: ');
define('EMAIL_TEXT_INVOICE_URL', '请从下面的url中看预约信息。' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', '预约日: ');
define('EMAIL_TEXT_STATUS_UPDATE',
'预约的受理情况如下。' . "\n"
.'现在的受理情况: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[联系事项]' . "\n\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', '错误: 预约不存在。');
define('SUCCESS_ORDER_UPDATED', '成功: 预约状态已更新。');
define('WARNING_ORDER_NOT_UPDATED', '警告: 预约状态完全没变。');

define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', '选择商品');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', '选择选项');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', '没有选项，跳过..');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', '数量');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', '追加');

define('EMAIL_TEXT_STORE_CONFIRMATION', '的预约，非常感谢 。' . "\n" . 
'预约的受理情况和联络事项、在下面介绍。');
define('TABLE_HEADING_COMMENTS_ADMIN', '[联络事项]');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'如有关于受理情况的问题' . "\n"
.'请联系本店。' . "\n\n"
. EMAIL_SIGNATURE);
define('ADD_A_NEW_PRODUCT', '添加商品');
define('CHOOSE_A_CATEGORY', ' --- 商品种类的选择 --- ');
define('SELECT_THIS_CATECORY', '选择执行类别');
define('CHOOSE_A_PRODUCT', ' --- 商品的选择 --- ');
define('SELECT_THIS_PRODUCT', '选择执行商品');
define('NO_OPTION_SKIPPED', '没有选项 - 跳过....');
define('SELECT_THESE_OPTIONS', '选择执行选项');
define('SELECT_QUANTITY', ' 数量');
define('SELECT_ADD_NOW', '执行追加');
define('SELECT_STEP_ONE', 'STEP 1:');
define('SELECT_STEP_TWO', 'STEP 2:');
define('SELECT_STEP_THREE', 'STEP 3:');
define('SELECT_STEP_FOUR', 'STEP 4:');
define('TEXT_CODE_HANDLE_FEE', '手续费:');
define('EDIT_ORDERS_UPDATE_NOTICE', '请谨慎填写要更改的内容。<b>请检查是否输入了空格等多余的文字！</b>');
define('EDIT_ORDERS_ID_TEXT', '预约订单号:');
define('EDIT_ORDERS_DATE_TEXT', '预约日期:');
define('EDIT_ORDERS_CUSTOMER_NAME', '顾客名:');
define('EDIT_ORDERS_EMAIL', '邮箱地址:');
define('EDIT_ORDERS_PAYMENT_METHOD', '付款方式:');
define('EDIT_ORDERS_FETCHTIME', '有效期限:');
define('EDIT_ORDERS_TORI_TEXT', '选项:');
define('EDIT_ORDERS_CUSTOMER_NAME_READ', '<font color="red">※</font>&nbsp;姓和名之间以<font color="red">半角空格</font>隔开。');
define('EDIT_ORDERS_PAYMENT_METHOD_READ', '<table><tr class="smalltext"><td><font color="red">※</font>&nbsp;复制粘贴用:</td><td>银行转账</td><td>信用卡结算</td><td>银行转账(买入)</td><td>邮政银行邮局）</td><td>便利店结算</td></tr></table>');
define('EDIT_ORDERS_TORI_READ', '<table><tr class="smalltext"><td><font color="red">※</font>&nbsp;复制粘贴用:</td><td>希望按指定时间交易</td><td>如果可以的话希望早于指定时间到达</td></tr></table>');
define('EDIT_ORDERS_PRO_LIST_TITLE', '2. 预约商品');
define('TABLE_HEADING_NUM_PRO_NAME', '数量 / 商品名');
define('TABLE_HEADING_CURRENICY', '税率');
define('TABLE_HEADING_PRICE_BEFORE', '价格(税前)');
define('TABLE_HEADING_PRICE_AFTER', '价格(税后)');
define('TABLE_HEADING_TOTAL_BEFORE', '合计(不含税)');
define('TABLE_HEADING_TOTAL_AFTER', '合计(含税)');
define('EDIT_ORDERS_DUMMY_TITLE', '交易人物名：');
define('EDIT_ORDERS_ADD_PRO_READ', '商品添加和其他事项不能同时更改。<b>请单独进行「 添加商品 」。</b>');
define('EDIT_ORDERS_FEE_TITLE_TEXT', '3. 点数折扣、手续费、减价');
define('TABLE_HEADING_FEE_MUST', '注意事项');
define('EDIT_ORDERS_OTTOTAL_READ', '务必确认合计金额一致。');
define('EDIT_ORDERS_OTSUBTOTAL_READ', '<table><tr class="smalltext"><td><font color="red">※</font>&nbsp;复制粘贴用:</td><td>调整额</td><td>事务手续费</td><td>减价</td></tr></table>');
define('EDIT_ORDERS_TOTALDETAIL_READ', '这位客户是游客。不能填写点数折扣。');
define('EDIT_ORDERS_TOTALDETAIL_READ_ONE', '如有折扣或减价金额，请输入负号和金额。');
define('EDIT_ORDERS_PRICE_CONSTRUCT_READ', '<font color="red">提示:</font>&nbsp;删除价格组成要素时，请把金额变为"0"然后更新。');
define('EDIT_ORDERS_CONFIRMATION_READ', '<font color="red">重要:</font>&nbsp;<b>更改价格组成要素时,点击「<font color="red">预约内容确认</font>」按钮确认合计金额是否一致。&nbsp;⇒</b>');
define('EDIT_ORDERS_CONFIRM_BUTTON', '预约内容确认');
define('EDIT_ORDERS_ITEM_FOUR_TITLE', '4. 预约订单状态、评论通知');
define('EDIT_ORDERS_SEND_MAIL_TEXT', '发送邮件:');
define('EDIT_ORDERS_RECORD_TEXT', '评论记录:');
define('EDIT_ORDERS_RECORD_READ', '←这里请不要勾选');
define('EDIT_ORDERS_RECORD_ARTICLE', '在这里输入的文本将被插入到邮件正文里。');
define('EDIT_ORDERS_ITEM_FIVE_TITLE', '5. 更新数据');
define('EDIT_ORDERS_FINAL_CONFIRM_TEXT', '最终确认了吗？');
define('EDIT_ORDERS_PRO_DUMMY_NAME', '交易人物名:');
define('EDIT_NEW_ORDERS_CREATE_TITLE', '创建预约订单');
define('EDIT_NEW_ORDERS_CREATE_READ', '【重要】并非编辑预约订单。是创建预约订单的系统。');
define('EDIT_ORDERS_ORIGIN_VALUE_TEXT', '（初始值）');
define('EDIT_ORDERS_UPDATE_COMMENT', '<table width="100%" cellspacing="0" cellpadding="2"> <tr class="smalltext"><td valign="top" colspan="2"><font color="red">※</font>&nbsp;复制粘贴用的短语。可以点三下或全选。</td></tr> <tr class="smalltext" bgcolor="#999999"><td>除了注册以DB为交易人物的时候</td><td>预备</td></tr> <tr class="smalltext" bgcolor="#CCCCCC"> <td valign="top">【重要】交易人物【】进行了交易。</td> <td valign="top"> 预备 </td> </tr> </table>');
define('ERROR_INPUT_PRICE_NOTICE', '请写单价');
define('EDIT_ORDERS_PRICE_UNIT', '日元');
define('EDIT_ORDERS_NUM_UNIT', '个');
define('EDIT_ORDERS_NOTICE_UPDATE_FAIL_TEXT', '取消更新。');
define('EDIT_ORDERS_NOTICE_DATE_WRONG_TEXT', '日期时间格式有误。 "2008-01-01 10:30:00"');
define('EDIT_ORDERS_NOTICE_NOUSE_DATE_TEXT', '无效日期或超过右边的数字。 "23:59:59"');
define('EDIT_ORDERS_NOTICE_MUST_INPUT_DATE_TEXT', '无法输入日期和时间。');
define('EDIT_ORDERS_NOTICE_POINT_ERROR', '点数不够。可以输入的点数为');
define('EDIT_ORDERS_NOTICE_POINT_ERROR_LINK', '');
define('EDIT_ORDERS_NOTICE_PRODUCT_DEL', '商品已删除。<font color="red">未发送邮件。</font>');
define('EDIT_ORDERS_NOTICE_ERROR_OCCUR', '发生错误。可能无法正常进行。');
define('EDIT_ORDERS_ENSUREDATE', '确保期限:');
define('NOTICE_INPUT_ENSURE_DEADLINE', '请设置确保期限。');
define('FORDERS_NOTICE_INPUT_ONCE_PWD', '请输入一次性密码');
define('FORDERS_NOTICE_ONCE_PWD_WRONG', '密码不一致');
define('PREORDER_PRODUCT_UNIT_TEXT', '个');
define('ENTRY_EMAIL_TITLE', '邮件标题：');
define('BUTTON_WRITE_PREORDER', '预约订单复制');
define('TABLE_HEADING_PRODUCTS_PRICE', '单价');
define('EDIT_ORDERS_NOTICE_EMAIL_MATCH_TEXT','输入的邮箱地址有误!');
?>
