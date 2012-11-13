<?php
/*
  $Id$

*/

define('HEADING_TITLE', '预约管理');
define('HEADING_TITLE_SEARCH', '订单ID:');
define('HEADING_TITLE_STATUS', '状态:');

define('TABLE_HEADING_COMMENTS', '评论');
define('TABLE_HEADING_CUSTOMERS', '客户名');
define('TABLE_HEADING_ORDER_TOTAL', '订单总额');
define('TABLE_HEADING_DATE_PURCHASED', '预约日期');
define('TABLE_HEADING_STATUS', '状态');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_QUANTITY', '数量');
define('TABLE_HEADING_PRODUCTS_MODEL', '型号');
define('TABLE_HEADING_PRODUCTS', '数量 / 商品名');
define('TABLE_HEADING_CHARACTER', '交易人物名');
define('TABLE_HEADING_TAX', '税率');
define('TABLE_HEADING_TOTAL', '合计');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', '价格(不含税)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', '价格(含税)');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', '合计(不含税)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', '合计(含税)');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', '客户通知');
define('TABLE_HEADING_DATE_ADDED', '处理日期');

define('ENTRY_CUSTOMER', '客户名:');
define('ENTRY_SOLD_TO', '订购者名:');
!defined('ENTRY_STREET_ADDRESS') && define('ENTRY_STREET_ADDRESS', '住址１:');
!defined('ENTRY_SUBURB')          && define('ENTRY_SUBURB', '住址２:');
!defined('ENTRY_CITY')            && define('ENTRY_CITY', '市区镇村:');
!defined('ENTRY_POST_CODE')       && define('ENTRY_POST_CODE', '邮政编码:');
!defined('ENTRY_STATE')           && define('ENTRY_STATE', '都道府县:');
!defined('ENTRY_COUNTRY')         && define('ENTRY_COUNTRY', '国名:');
!defined('ENTRY_TELEPHONE')       && define('ENTRY_TELEPHONE', '电话号码:');
!defined('ENTRY_EMAIL_ADDRESS')   && define('ENTRY_EMAIL_ADDRESS', 'E-Mail 地址:');
define('ENTRY_DELIVERY_TO', '投递地点:');
define('ENTRY_SHIP_TO', '投递地点:');
define('ENTRY_SHIPPING_ADDRESS', '投递地点:');
define('ENTRY_BILLING_ADDRESS', '账单地址:');
define('ENTRY_PAYMENT_METHOD', '支付方式:');
define('ENTRY_CREDIT_CARD_TYPE', '信用卡类别:');
define('ENTRY_CREDIT_CARD_OWNER', '信用卡所有者:');
define('ENTRY_CREDIT_CARD_NUMBER', '信用卡号:');
define('ENTRY_CREDIT_CARD_EXPIRES', '信用卡有效期限:');
define('ENTRY_SUB_TOTAL', '小计:');
define('ENTRY_TAX', '税款:');
define('ENTRY_SHIPPING', '发送:');
define('ENTRY_TOTAL', '合计:');
define('ENTRY_DATE_PURCHASED', '订购日期:');
define('ENTRY_STATUS', '状态:');
define('ENTRY_DATE_LAST_UPDATED', '更新日:');
define('ENTRY_NOTIFY_CUSTOMER', '告知处理情况:');
define('ENTRY_NOTIFY_COMMENTS', '添加评论:');
define('ENTRY_PRINTABLE', '打印交货单');
define('TEXT_PREORDER_AMOUNT_SEARCH','用订单金额搜索');

define('TEXT_INFO_HEADING_DELETE_ORDER', '删除订单');
define('TEXT_INFO_DELETE_INTRO', '确定删除这个订单吗?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', '恢复库存'); // 'Restock product quantity'
define('TEXT_DATE_ORDER_CREATED', '预约日期:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', '更新日:');
define('TEXT_INFO_PAYMENT_METHOD', '支付方式:');

define('TEXT_ALL_ORDERS', '全部订单');
define('TEXT_NO_ORDER_HISTORY', '无订单历史记录');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', '订单受理情况的通知');
define('EMAIL_TEXT_ORDER_NUMBER', '订单受理号:');
define('EMAIL_TEXT_INVOICE_URL', '至于订单信息可查看下面URL。' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', '订购日期:');
define('EMAIL_TEXT_STATUS_UPDATE',
'订单受理情况如下。' . "\n"
.'现在的受理情况: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[联络事项]' . "\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', '错误: 订单已存在。');
define('SUCCESS_ORDER_UPDATED', '成功: 订单状态已更新。');
define('WARNING_ORDER_NOT_UPDATED', '警告: 订单状态没有任何更改。');

define('EMAIL_TEXT_STORE_CONFIRMATION', ' 的订购、非常感谢。' . "\n\n"
.'订单的受理情况和联络事项、在下面介绍。');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'如有有关受理情况的问题等' . "\n"
.'请联系本店。' . "\n\n"
. EMAIL_SIGNATURE);

define('ENTRY_EMAIL_TITLE', '邮件标题：');
define('TEXT_CODE_HANDLE_FEE', '手续费:');

// old oa 
define('TEXT_ORDER_ANSWER','Order Answer');
define('TEXT_BUY_BANK','买入：银行支付');
define('TEXT_SELL_BANK','卖出：银行转账');
define('TEXT_SELL_CARD','卖出：信用卡');
define('TEXT_CREDIT_FIND','信用度调查');

define('TEXT_ORDER_SAVE','保存');
define('TEXT_ORDER_TEST_TEXT','试运行中<font color="red">（确认与上述的数值是否一致）</font>买入时复制粘贴用:');
define('TEXT_FEE_TEXT','这个订单未满5,000日元,如果购买商品,需扣除168日元的手续费');
define('TEXT_MAIL_CONTENT_INFO',' 自动换行显示,发送邮件也换行。');
define('TEXT_ORDER_COPY','复制粘贴用:');
define('TEXT_ORDER_LOGIN','现在开始登录。');
define('TEXT_ORDER_SEND_MAIL','发送邮件');
define('TEXT_ORDER_STATUS','状态通知');
define('TEXT_ORDER_HAS_ERROR','找到错误了吗？');
define('TEXT_ORDER_FIND','搜索 :');
define('TEXT_ORDER_FIND_SELECT','--------请选择--------');
define('TEXT_ORDER_FIND_NAME','用姓名搜索');
define('TEXT_ORDER_FIND','搜索 :');
define('TEXT_ORDER_FIND_PRODUCT_NAME','用商品名搜索');
define('TEXT_ORDER_FIND_MAIL_ADD','用邮箱地址搜索');
define('TEXT_ORDER_QUERYER_NAME','确认者姓名:');
define('TEXT_ORDER_OK_ORDER_NIMBER','完毕 受理号码:');
define('TEXT_ORDER_BANK','银行:');
define('TEXT_ORDER_JNB','JNB');
define('TEXT_ORDER_EBANK','eBank');
define('TEXT_ORDER_POST_BANK','邮政银行');
define('TEXT_EDIT_MAIL_TEXT','编辑邮件正文');
define('TEXT_SELECT_MORE','不能多选。');
define('TEXT_ORDER_SELECT','没选择送货单。');
define('TEXT_ORDER_WAIT','等待交易');
define('TEXT_ORDER_CARE','处理警告');
define('TEXT_ORDER_OROSHI','经销商');
define('TEXT_ORDER_CUSTOMER_INFO','客户信息');
define('TEXT_ORDER_HISTORY_ORDER','历史订单');
define('TEXT_ORDER_NEXT_ORDER','下个订单');
define('TEXT_ORDER_ORDER_DATE','有效期限');
define('TEXT_ORDER_CONVENIENCE','便利店结算');
define('TEXT_ORDER_CREDIT_CARD','信用卡结算');
define('TEXT_ORDER_POST','邮政银行（邮局）');
define('TEXT_ORDER_BANK_REMIT_MONEY','银行转账');
define('TEXT_ORDER_MIX','混合');
define('TEXT_ORDER_BUY','买');
define('TEXT_ORDER_SELL','卖');
define('TEXT_ORDER_NOTICE','【注意】');
define('TEXT_ORDER_AUTO_RUN_ON','现在启用自动重载功能　→ ');
define('TEXT_ORDER_AUTO_POWER_OFF','禁用');
define('TEXT_ORDER_AUTO_RUN_OFF','现在禁用自动重载功能　→ ');
define('TEXT_ORDER_AUTO_POWER_ON','启用');
define('TEXT_ORDER_SHOW_LIST','在一览里显示');
define('TEXT_ORDER_STATUS_SET','订单状态设置');
define('TEXT_ORDER_CSV_OUTPUT','CSV输出');
define('TEXT_ORDER_DAY','日');
define('TEXT_ORDER_MONTH','月');
define('TEXT_ORDER_YEAR','年');
define('TEXT_ORDER_END_DATE','结束日期:');
define('TEXT_ORDER_START_DATE','开始日期:');
define('TEXT_ORDER_SITE_TEXT','订单网站');
define('TEXT_ORDER_SERVER_BUSY','下载途中对服务器产生高负荷。请在访问时间内执行。');
define('TEXT_ORDER_DOWNLOPAD','订单数据输出');

define('DEL_CONFIRM_PAYMENT_TIME', '删除');
define('NOTICE_DEL_CONFIRM_PAYEMENT_TIME', '删除时间吗？');
define('NOTICE_DEL_CONFIRM_PAYMENT_TIME_SUCCESS', '删除成功');
define('NOTICE_ORDER_ID_TEXT', '订单ID');
define('NOTICE_ORDER_ID_LINK_TEXT', '的');
define('NOTICE_ORDER_INPUT_PASSWORD', '请输入一次性密码\r\n');
define('NOTICE_ORDER_INPUT_WRONG_PASSWORD', '密码有误');
define('TEXT_ORDER_INPUTED_FLAG', '输入完毕');
define('TEXT_ORDER_DATE_LONG', '有效期限:');
define('TEXT_ORDER_HOUHOU', '选项:');
define('TEXT_PREORDER_ID_TEXT', '预约号码:');
define('TEXT_PREORDER_DAY', '预约日期:');
define('TEXT_ORDER_CUSTOMER_TYPE', '客户类别:');
define('TEXT_ORDER_CUSTOMER_VIP', '会员');
define('TEXT_ORDER_GUEST', '非会员');
define('TEXT_ORDER_CONCAT_OID_CREATE', '新建咨询号码');
define('TEXT_ORDER_EMAIL_LINK', '邮件');
define('TEXT_ORDER_CREDIT_LINK', '信用卡');
define('TEXT_ORDER_IP_ADDRESS', 'IP地址:');
define('TEXT_ORDER_HOSTNAME', '主机名:');
define('TEXT_ORDER_USERAGENT', '用户代理:');
define('TEXT_ORDER_OS', 'OS:');
define('TEXT_ORDER_BROWSER_INFO', '浏览器的种类:');
define('TEXT_ORDER_HTTP_LAN', '浏览器的语言:');
define('TEXT_ORDER_SYS_LAN', '计算机的语言环境:');
define('TEXT_ORDER_USER_LAN', '用户的语言环境:');
define('TEXT_ORDER_SCREEN_RES', '画面的分辨率:');
define('TEXT_ORDER_COLOR_DEPTH', '画面的颜色:');
define('TEXT_ORDER_FLASH_VERS', 'Flash的版本:');
define('TEXT_ORDER_CREDITCARD_TITLE', '信用卡信息');
define('TEXT_ORDER_CREDITCARD_NAME', '信用卡持有人:');
define('TEXT_ORDER_CREDITCARD_TEL', '电话号码:');
define('TEXT_ORDER_CREDITCARD_EMAIL', '邮箱地址:');
define('TEXT_ORDER_CREDITCARD_MONEY', '金额:');
define('TEXT_ORDER_CREDITCARD_COUNTRY', '居住国家:');
define('TEXT_ORDER_CREDITCARD_STATUS', '认证:');
define('TEXT_ORDER_CREDITCARD_PAYMENTSTATUS', '支付状态:');
define('TEXT_ORDER_CREDITCARD_PAYMENTTYPE', '支付方式:');
define('ENTRY_ENSURE_DATE', '确保期限:');
define('TEXT_ORDER_EXPECTET_COMMENT', '需求:');
define('PREORDERS_STATUS_SELECT_PRE', '状态「');
define('PREORDERS_STATUS_SELECT_LAST', '」搜索');
define('TEXT_ORDER_FIND_OID', '用订单号码搜索');
define('NOTICE_INPUT_ENSURE_DEADLINE', '请设置确保期限。');
define('PREORDERS_PAYMENT_METHOD_PRE', '支付方式「');
define('PREORDERS_PAYMENT_METHOD_LAST', '」搜索');
define('TEXT_SORT_ASC','▲');
define('TEXT_SORT_DESC','▼');
define('PREORDER_PRODUCT_UNIT_TEXT', '个');
define('NOTICE_LIMIT_SHOW_PREORDER_TEXT', '邮件认证中无法操作');

define('TEXT_FUNCTION_INPUT_FINISH','输入完毕');
define('TEXT_FUNCTION_NOTICE','注意处理方式');
define('TEXT_FUNCTION_HAVE_HISTORY','有备注');
define('TEXT_FUNCTION_PAYMENT_METHOD','支付方式：');
define('TEXT_FUNCTION_DATE_STRING','Y年n月j日');
define('TEXT_FUNCTION_UN_GIVE_MONY','还没进账');
define('TEXT_FUNCTION_UN_GIVE_MONY_DAY','进账日期：');
define('TEXT_FUNCTION_OPTION','选项：');
define('TEXT_FUNCTION_CATEGORY','商品：');
define('TEXT_FUNCTION_FINISH','「进项」');
define('TEXT_FUNCTION_UNFINISH','「未进项」');
define('TEXT_FUNCTION_NUMBER','个数：');
define('TEXT_FUNCTION_NUM','个');
define('TEXT_FUNCTION_PC','PC：');
define('TEXT_FUNCTION_PREDATE','有效期限：');
define('TEXT_FUNCTION_ENSURE_DATE','确保期限：');
define('TEXT_FUNCTION_ORDER_FROM_INFO', '预约订单网站：');
define('TEXT_ORDER_HISTORY_FROM_ORDER', '订购');
define('TEXT_ORDER_HISTORY_FROM_PREORDER', '预约');

define('TEXT_ORDER_NOT_CHOOSE','不能多选');
define('TEXT_NO_OPTION_ORDER','没有选择订单');
define('TEXT_COMPLETION_TRANSACTION','交易完成');
define('TEXT_PRESERVATION','保存');
define('TEXT_SAVE_FINISHED','保存完成');
define('TEXT_BROWER_REJECTED','浏览器被拒绝！\n在浏览器地址栏输>    入"about:config"按Enter键\nそ把"signed.applets.co debase_princip    al_support"设置为"true"');
define('TEXT_COPY_TO_CLIPBOARD','已经复制到剪贴板！');
define('TEXT_PLEASE_PASSWORD','请输入一次性口令\r\n');
define('TEXT_PASSWORD_NOT','密码不一致');
?>
