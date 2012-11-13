<?php
/*
  $Id$

*/

define('HEADING_TITLE', '编辑订单内容');
define('HEADING_TITLE_SEARCH', '订单ID:');
define('HEADING_TITLE_STATUS', '状态:');
define('ADDING_TITLE', '添加商品');

define('ENTRY_UPDATE_TO_CC', '(Update to <b>Credit Card</b> to view CC fields.)');
define('TABLE_HEADING_COMMENTS', '评论');
define('TABLE_HEADING_EMAIL_COMMENTS', '邮件模板');
define('TABLE_HEADING_CUSTOMERS', '客户名');
define('TABLE_HEADING_ORDER_TOTAL', '订单总额');
define('TABLE_HEADING_DATE_PURCHASED', '订购日期');
define('TABLE_HEADING_STATUS', '状态');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_QUANTITY', '数量');
define('TABLE_HEADING_PRODUCTS_MODEL', '型号');
define('TABLE_HEADING_PRODUCTS', '商品名');
define('TABLE_HEADING_TAX', '消费税');
define('TABLE_HEADING_TOTAL', '合计');
define('TABLE_HEADING_UNIT_PRICE', '单价');
define('TABLE_HEADING_TOTAL_PRICE', '合计');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', '告知处理情况');
define('TABLE_HEADING_DATE_ADDED', '更新日');

define('ENTRY_CUSTOMER', '客户名:');
define('ENTRY_CUSTOMER_NAME', '名字');
//add
define('ENTRY_CUSTOMER_NAME_F', '名字(假名)');
define('ENTRY_CUSTOMER_COMPANY', '公司名称');
define('ENTRY_CUSTOMER_ADDRESS', '地址');
define('ENTRY_CUSTOMER_SUBURB', '建筑物名称');
define('ENTRY_CUSTOMER_CITY', '市区镇村');
define('ENTRY_CUSTOMER_STATE', '都道府县');
define('ENTRY_CUSTOMER_POSTCODE', '邮政编码');
define('ENTRY_CUSTOMER_COUNTRY', '国名');

define('ENTRY_SOLD_TO', '买方:');
define('ENTRY_DELIVERY_TO', '收件人:');
define('ENTRY_SHIP_TO', '投递:');
define('ENTRY_SHIPPING_ADDRESS', '投递地点:');
define('ENTRY_BILLING_ADDRESS', '账单地址:');
define('ENTRY_PAYMENT_METHOD', '支付方式:');
define('ENTRY_CREDIT_CARD_TYPE', '信用卡类型:');
define('ENTRY_CREDIT_CARD_OWNER', '信用卡持有人:');
define('ENTRY_CREDIT_CARD_NUMBER', '信用卡号:');
define('ENTRY_CREDIT_CARD_EXPIRES', '信用卡有效期限:');
define('ENTRY_SUB_TOTAL', '小计:');
define('ENTRY_TAX', '消费税:');
define('ENTRY_SHIPPING', '投递方式:');
define('ENTRY_TOTAL', '合计:');
define('ENTRY_DATE_PURCHASED', '订购日期:');
define('ENTRY_STATUS', '状态:');
define('ENTRY_EMAIL_TITLE', '邮件标题:');
define('ENTRY_DATE_LAST_UPDATED', '更新日:');
define('ENTRY_NOTIFY_CUSTOMER', '告知处理情况:');
define('ENTRY_NOTIFY_COMMENTS', '添加评论:');
define('ENTRY_PRINTABLE', '打印交货单');

define('TEXT_INFO_HEADING_DELETE_ORDER', '删除订单');
define('TEXT_INFO_DELETE_INTRO', '确定删除这个订单吗?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', '恢复库存');
define('TEXT_DATE_ORDER_CREATED', '创建日:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', '更新日:');
define('TEXT_DATE_ORDER_ADDNEW', '添加商品');
define('TEXT_INFO_PAYMENT_METHOD', '支付方式:');

define('TEXT_ALL_ORDERS', '全部订单');
define('TEXT_NO_ORDER_HISTORY', '无订单历史记录');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', '订单受理情况的通知');
define('EMAIL_TEXT_ORDER_NUMBER', '订单受理号: ');
define('EMAIL_TEXT_INVOICE_URL', '至于订单信息可查看下面URL。' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', '订购日期: ');
define('EMAIL_TEXT_STATUS_UPDATE',
'订单受理情况如下。' . "\n"
.'现在的受理情况: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[联络事项]' . "\n\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', '错误: 订单不存在。');
define('SUCCESS_ORDER_UPDATED', '成功: 订单状态已更新。');
define('WARNING_ORDER_NOT_UPDATED', '警告: 订单状态没有任何更改。');

define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', '选择商品');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', '选择选项');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', '无选项: 略过..');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', '数量');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', '添加');

define('EMAIL_TEXT_STORE_CONFIRMATION', ' 的订购、非常感谢。' . "\n" . 
'订单的受理情况和联络事项、在下面介绍。');
define('TABLE_HEADING_COMMENTS_ADMIN', '[联络事项]');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'如有有关受理情况的问题等、' . "\n"
.'请联系本店' . "\n\n"
. EMAIL_SIGNATURE);
define('ADD_A_NEW_PRODUCT', '商品的添加');
define('CHOOSE_A_CATEGORY', ' --- 商品分类选择 --- ');
define('SELECT_THIS_CATECORY', '选择分类执行');
define('CHOOSE_A_PRODUCT', ' --- 商品的选择 --- ');
define('SELECT_THIS_PRODUCT', '选择商品执行');
define('NO_OPTION_SKIPPED', '无选项 - 略过....');
define('SELECT_THESE_OPTIONS', '选择选项执行');
define('SELECT_QUANTITY', ' 数量');
define('SELECT_ADD_NOW', '添加执行');
define('SELECT_STEP_ONE', 'STEP 1:');
define('SELECT_STEP_TWO', 'STEP 2:');
define('SELECT_STEP_THREE', 'STEP 3:');
define('SELECT_STEP_FOUR', 'STEP 4:');
define('TEXT_CODE_HANDLE_FEE', '手续费:');
define('TEXT_CODE_SHIPPING_FEE', '运费:');
define('EDIT_ORDERS_UPDATE_NOTICE', '请谨慎填写要更改的内容。<b>请检查是否输入了空格等多余的文字</b>');
define('EDIT_ORDERS_ID_TEXT', '订单号:');
define('EDIT_ORDERS_DATE_TEXT', '订购日期:');
define('EDIT_ORDERS_CUSTOMER_NAME', '客户名:');
define('EDIT_ORDERS_EMAIL', '邮件地址:');
define('EDIT_ORDERS_PAYMENT_METHOD', '支付方式:');
define('EDIT_ORDERS_FETCHTIME', '交货日期:');
define('EDIT_ORDERS_TORI_TEXT', '选项:');
define('EDIT_ORDERS_CUSTOMER_NAME_READ', '<font color="red">※</font>&nbsp;姓和名之间以<font color="red">半角空格</font>隔开');
define('EDIT_ORDERS_PAYMENT_METHOD_READ', '<table><tr class="smalltext"><td><font color="red">※</font>&nbsp;复制粘贴用:</td><td>银行转账</td><td>信用卡结算</td><td>银行转账(买入)</td><td>邮政银行（邮局）</td><td>便利店结算</td></tr></table>');
define('EDIT_ORDERS_TORI_READ', '<table><tr class="smalltext"><td><font color="red">※</font>&nbsp;复制粘贴用:</td><td>希望按指定时间交易</td><td>如果可以的话希望早于指定时间到达</td></tr></table>');
define('EDIT_ORDERS_PRO_LIST_TITLE', '2. 订单商品');
define('TABLE_HEADING_NUM_PRO_NAME', '数量 / 商品名');
define('TABLE_HEADING_CURRENICY', '税率');
define('TABLE_HEADING_PRICE_BEFORE', '价格(不含税)');
define('TABLE_HEADING_PRICE_AFTER', '价格(含税)');
define('TABLE_HEADING_TOTAL_BEFORE', '合计(不含税)');
define('TABLE_HEADING_TOTAL_AFTER', '合计(含税)');
define('EDIT_ORDERS_DUMMY_TITLE', '交易人物名：');
define('EDIT_ORDERS_ADD_PRO_READ', '商品添加和其他事项不能同时更改。<b>请单独进行「 添加商品 」。</b>');
define('EDIT_ORDERS_FEE_TITLE_TEXT', '3. 点数折扣、手续费、减价');
define('TABLE_HEADING_FEE_MUST', '注意事项');
define('EDIT_ORDERS_OTTOTAL_READ', '务必确认合计金额一致。');
define('EDIT_ORDERS_OTSUBTOTAL_READ', '<table><tr class="smalltext"><td><font color="red">※</font>&nbsp;复制粘贴用:</td><td>调整额</td><td>事务手续费</td><td>减价</td></tr></table>');
define('EDIT_ORDERS_TOTALDETAIL_READ', '这位客户是游客。不能填写点数折扣');
define('EDIT_ORDERS_TOTALDETAIL_READ_ONE', '减价时,请填写−（减号）符号。');
define('EDIT_ORDERS_PRICE_CONSTRUCT_READ', '<font color="red">提示:</font>&nbsp;删除价格组成要素时，请把金额变为"0"然后更新。');
define('EDIT_ORDERS_CONFIRMATION_READ', '<font color="red">重要:</font>&nbsp;<b>更改价格组成要素时,点击「<font color="red">订单内容确认</font>」按钮确认合计金额是否一致。&nbsp;⇒</b>');
define('EDIT_ORDERS_CONFIRM_BUTTON', '订单内容确认');
define('EDIT_ORDERS_ITEM_FOUR_TITLE', '4. 订单状态、评论通知');
define('EDIT_ORDERS_SEND_MAIL_TEXT', '发送邮件:');
define('EDIT_ORDERS_RECORD_TEXT', '评论记录:');
define('EDIT_ORDERS_RECORD_READ', '←这里请不要勾选');
define('EDIT_ORDERS_RECORD_ARTICLE', '在这里输入的文本将被插入到邮件正文里。');
define('EDIT_ORDERS_ITEM_FIVE_TITLE', '5. 更新数据');
define('EDIT_ORDERS_FINAL_CONFIRM_TEXT', '最终确认了吗？');
define('EDIT_ORDERS_PRO_DUMMY_NAME', '交易人物名:');
define('EDIT_NEW_ORDERS_CREATE_TITLE', '创建订单');
define('EDIT_NEW_ORDERS_CREATE_READ', '【重要】并非编辑订单。是创建订单的系统。');
define('EDIT_ORDERS_ORIGIN_VALUE_TEXT', '（初始值）');
define('EDIT_ORDERS_UPDATE_COMMENT', '<table width="100%" cellspacing="0" cellpadding="2"> <tr class="smalltext"><td valign="top" colspan="2"><font color="red">※</font>&nbsp;复制粘贴用的短语。可以点三下或全选。</td></tr> <tr class="smalltext" bgcolor="#999999"><td>除了注册以DB为交易人物的时候</td><td>预备</td></tr> <tr class="smalltext" bgcolor="#CCCCCC"> <td valign="top">【重要】交易人物【】进行了交易。</td> <td valign="top"> 预备 </td> </tr> </table>');
define('ERROR_INPUT_PRICE_NOTICE', '请写单价');
define('EDIT_ORDERS_PRICE_UNIT', '日元');
define('EDIT_ORDERS_NUM_UNIT', '个');

define('TEXT_CREATE_ADDRESS_BOOK','指定投递地址');
define('TEXT_USE_ADDRESS_BOOK','到达投递地址');
define('TEXT_TORIHIKIBOUBI_DEFAULT_SELECT','指定期望的投递时间');
define('CREATE_ORDER_FETCH_DATE_TEXT', '期望投递日期:');
define('CREATE_ORDER_FETCH_TIME_TEXT', '期望投递时间:');


define('MODULE_ORDER_TOTAL_SHIPPING_TITLE', '运费:');
define('TEXT_SHIPPING_FEE','运费:');
define('TEXT_SHIPPING_ADDRESS','住址信息▼');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_NULL','必须项目');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_TYPE_WRONG','请正确输入');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_NUM_MAX','超过可输入的文字数');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_NUM_MIN','至少');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_NUM_MIN_1','至少1个字符以上');
define('TABLE_HEADING_PRODUCTS_PRICE', '单价');
define('CALC_PRODUCTS_TEXT', '计算');
define('CREATE_ORDER_PRODUCTS_WEIGHT','总重量已找过规定的范围。请删除商品、或更改商品数量使重量在');
define('CREATE_ORDER_PRODUCTS_WEIGHT_ONE','）kg以内。');
define('PRODUCTS_WEIGHT_ERROR_ONE','总重量是（');
define('PRODUCTS_WEIGHT_ERROR_TWO','）超过了规定的重量');
define('PRODUCTS_WEIGHT_ERROR_THREE','请删除商品、或更改商品数量使重量在');
define('PRODUCTS_WEIGHT_ERROR_FOUR','）kg以内。');
define('TEXT_CANCEL_UPDATE','已取消更新。');
define('TEXT_DATE_ERROR','日期和时间格式有误。 ');
define('TEXT_DATE_NUM_ERROR','无效日期或超过了右边的数字。 ');
define('TEXT_INPUT_DATE_ERROR','尚未输入日期和时间。');
define('TEXT_NO_ENOUGH_POINT','点数不够。可输入的点数是 ');
define('TEXT_LS',' ');
define('TEXT_HOUR','时');
define('TEXT_MIN','分');
define('TEXT_TWENTY_FOUR_HOUR','　（24小时制）');
define('TEXT_DATE_YEAR','年');
define('TEXT_DATE_MONTH','月');
define('TEXT_DATE_DAY','日');
define('ORDERS_PRODUCTS','订购商品');
define('QTY_NUM','个数');
define('TEXT_CHARACTER_NAME_SEND_MAIL','※ 该公司交易人物名在交易进行10分钟前通过邮件通知。');
define('TEXT_CHARACTER_NAME_CONFIRM_SEND_MAIL','※ 该公司交易人物名在支付确认后通过邮件通知。');
define('TEXT_POINT','折扣　　　　　　：-');
define('TEXT_HANDLE_FEE','手续费　　　　　：');
define('TEXT_PAYMENT_AMOUNT','支付金额　　　：');
define('TEXT_TRANSACTION_FEE','结算手续费');
define('TEXT_HANDLE_FEE_ONE','手续费');
define('TEXT_ORDERS_UPDATE','订单内容已更改【');
define('TEXT_EMAIL_ORDERS_UPDATE','发送完毕：订单内容已更改【');
define('TEXT_PRODUCTS_DELETE','商品已删除。<font color="red">尚未发送邮件。</font>');
define('TEXT_ERROR_NO_SUCCESS','发生错误。可能无法正常处理。');
define('TEXT_REQUIRE','*必须');
define('TEXT_ADDRESS_INFO_HIDE','住址信息▲');
define('TEXT_ADDRESS_INFO_SHOW','住址信息▼');
define('TEXT_BANK_TRANSFER','银行转账(买入)');
define('TEXT_USUALLY','普通');
define('TEXT_CONVERIENCE_PAYMENT','便利店结算');
define('TEXT_RAKUTEN_BANK','乐天银行');
define('TEXT_ACCOUNT_TYPE','账户类型:');
define('TEXT_CUSTOMER_INPUT','客户是会员。可输入的点数剩余 ');
define('TEXT_REMAINING','');
define('TEXT_SUBTOTAL','（合计');
define('TEXT_RIGHT_BRACKETS','）');
define('TEXT_INPUT_POSITIVE_NUM',' 。需要输入−（减号）。务必输入正数！');
define('TEXT_VISIT_PAYMENT','来店支付');
define('TEXT_POINT_PAYMENT','点数支付(买入)');
define('TEXT_NOTICE_PAYMENT','支付通知*');
define('TEXT_POINT_ONE','▼折扣　　　　　　：-');
define('TEXT_HANDLE_FEE_ONE','▼手续费　　　　　：');
define('TEXT_PAYMENT_AMOUNT_ONE','▼支付金额　　　：');
define('TEXT_POINT_DISCOUNT','▼点数折扣');
define('TEXT_POINT_DISCOUNT_ONE','▼ 点数折扣');
define('TEXT_SHIPPING_FEE_ONE','▼运费　　　：');
define('ORDERS_PRODUCTS_ONE','▼订购商品');
define('TEXT_ADDRESS_INFO_LEFT','▼住址信息');
define('TEXT_ORDERS_SEND_MAIL','感谢订购【');
define('TEXT_CARD_PAYMENT','关于信用卡结算【');
define('TEXT_SEND_MAIL_CARD_PAYMENT','发送完毕：关于信用卡结算【');
define('TEXT_ORDER_NOT_CHOOSE','不能多选');
define('TEXT_NO_OPTION_ORDER','没有选择订单');
define('TEXT_COMPLETION_TRANSACTION','交易完成');
define('TEXT_PRESERVATION','保存');
define('TEXT_SAVE_FINISHED','保存完成');
define('TEXT_BROWER_REJECTED','被浏览器拒绝！\n在浏览器地址栏输入"about:config"按Enter键\n另外"signed.applets.codebase_principal_support"把数值设为"true"');
define('TEXT_COPY_TO_CLIPBOARD','已经复制到剪贴板！');
define('TEXT_PLEASE_PASSWORD','请输入一次性口令\r\n');
define('TEXT_PASSWORD_NOT','密码不一致');
define('TEXT_ORDER_NOT_CHOOSE','不能多选');
define('TEXT_NO_OPTION_ORDER','没有选择订单');
define('TEXT_COMPLETION_TRANSACTION','交易完成');
define('TEXT_PRESERVATION','保存');
define('TEXT_SAVE_FINISHED','保存完成');
define('TEXT_COPY_TO_CLIPBOARD','已经复制到剪贴板！');
define('TEXT_PLEASE_PASSWORD','请输入一次性口令\r\n');
define('TEXT_PASSWORD_NOT','密码不一致');
define('TEXT_DATE_TIME_ERROR','选择时间已过期，确认无误吗？');
define('TEXT_PRODUCTS_NUM','商品数量不足。确定创建订单吗？');
?>
