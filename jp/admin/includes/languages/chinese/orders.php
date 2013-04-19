<?php
/*
  $Id$
*/
define('HEADING_TITLE', '订单管理');
define('HEADING_TITLE_SEARCH', '订单ID:');
define('HEADING_TITLE_STATUS', '状态:');

define('TABLE_HEADING_COMMENTS', '说明');
define('TABLE_HEADING_CUSTOMERS', '顾客名');
define('TABLE_HEADING_ORDER_TOTAL', '订单总额');
define('TABLE_HEADING_DATE_PURCHASED', '订购日期');
define('TABLE_HEADING_STATUS', '状态');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_QUANTITY', '数量');
define('TABLE_HEADING_PRODUCTS_MODEL', '型号');
define('TABLE_HEADING_PRODUCTS', '数量/ 商品名');
define('TABLE_HEADING_TAX', '税率');
define('TABLE_HEADING_TOTAL', '合计');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', '价格(不含税)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', '价格(含税)');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', '合计(不含税)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', '合计(含税)');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', '通知顾客');
define('TABLE_HEADING_DATE_ADDED', '处理日期');

define('ENTRY_CUSTOMER', '顾客名:');
define('ENTRY_SOLD_TO', '订购人物名:');
!defined('ENTRY_STREET_ADDRESS') && define('ENTRY_STREET_ADDRESS', '住处１:');
!defined('ENTRY_SUBURB')          && define('ENTRY_SUBURB', '住处２:');
!defined('ENTRY_CITY')            && define('ENTRY_CITY', '市区町村:');
!defined('ENTRY_POST_CODE')       && define('ENTRY_POST_CODE', '邮政编码:');
!defined('ENTRY_STATE')           && define('ENTRY_STATE', '都道府县:');
!defined('ENTRY_COUNTRY')         && define('ENTRY_COUNTRY', '国名:');
!defined('ENTRY_TELEPHONE')       && define('ENTRY_TELEPHONE', '电话号码:');
!defined('ENTRY_EMAIL_ADDRESS')   && define('ENTRY_EMAIL_ADDRESS', 'E-Mail 地址:');
define('ENTRY_DELIVERY_TO', '发送地:');
define('ENTRY_SHIP_TO', '发送地:');
define('ENTRY_SHIPPING_ADDRESS', '发送地:');
define('ENTRY_BILLING_ADDRESS', '请求:');
define('ENTRY_PAYMENT_METHOD', '支付方法:');
define('ENTRY_CREDIT_CARD_TYPE', '信用卡种类:');
define('ENTRY_CREDIT_CARD_OWNER', '信用卡持有者:');
define('ENTRY_CREDIT_CARD_NUMBER', '信用卡号码:');
define('ENTRY_CREDIT_CARD_EXPIRES', '信用卡有效期:');
define('ENTRY_SUB_TOTAL', '小计:');
define('ENTRY_TAX', '税金:');
define('ENTRY_SHIPPING', '发送:');
define('ENTRY_TOTAL', '合计:');
define('ENTRY_DATE_PURCHASED', '订购日期:');
define('ENTRY_STATUS', '状态:');
define('ENTRY_DATE_LAST_UPDATED', '更新日:');
define('ENTRY_NOTIFY_CUSTOMER', '通知处理情况:');
define('ENTRY_NOTIFY_COMMENTS', '追加说明:');
define('ENTRY_PRINTABLE', '打印交货单');

define('TEXT_INFO_HEADING_DELETE_ORDER', '删除订单');
define('TEXT_INFO_DELETE_INTRO', '确定要删除这个订单吗?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', '还原库存数'); // 'Restock product quantity'
define('TEXT_DATE_ORDER_CREATED', '订购日期:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', '更新日:');
define('TEXT_INFO_PAYMENT_METHOD', '支付方法:');

define('TEXT_ALL_ORDERS', '全部订单');
define('TEXT_NO_ORDER_HISTORY', '没有订单历史记录');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', '订单受理情况通知');
define('EMAIL_TEXT_ORDER_NUMBER', '订单受理号码:');
define('EMAIL_TEXT_INVOICE_URL', '请从下面的url中看订单信息。' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', '订购日期:');
define('EMAIL_TEXT_STATUS_UPDATE',
'订单的受理情况如下。' . "\n"
.'现在的受理情况: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[联系事项]' . "\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', '错误: 订单不存在。');
define('SUCCESS_ORDER_UPDATED', '成功: 订单状态已更新。');
define('WARNING_ORDER_NOT_UPDATED', '警告: 订单状态未改变。');

define('EMAIL_TEXT_STORE_CONFIRMATION', ' 的订购、非常感谢。' . "\n\n"
.'订单的受理情况和联络事项、在下面介绍。');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'如有关于受理情况的问题等、' . "\n"
.'请联系本店' . "\n\n"
. EMAIL_SIGNATURE);

define('ENTRY_EMAIL_TITLE', '邮件标题：');
define('TEXT_CODE_HANDLE_FEE', '手续费:');
define('TEXT_SHIPPING_FEE','运费:');

// old oa 
define('TEXT_ORDER_ANSWER','Order Answer');
define('TEXT_BUY_BANK','买入：银行汇款');
define('TEXT_SELL_BANK','出售：银行汇款');
define('TEXT_SELL_CARD','出售：信用卡');
define('TEXT_CREDIT_FIND','信用调查');

define('TEXT_ORDER_SAVE','保存');
define('TEXT_ORDER_TEST_TEXT','试运行中<font color="red">（请确认是否与上述数值一致）</font>复制粘贴用:');
define('TEXT_FEE_TEXT','订单不满5000日元。如果购买商品，需扣除168日元的手续费');
define('TEXT_MAIL_CONTENT_INFO',' 自动换行显示，发送的邮件也另起一行。');
define('TEXT_ORDER_COPY','复制粘贴用:');
define('TEXT_ORDER_LOGIN','现在开始登录。');
define('TEXT_ORDER_SEND_MAIL','发邮件');
define('TEXT_ORDER_STATUS','状态通知');
define('TEXT_ORDER_HAS_ERROR','找到错误了吗？');
define('TEXT_ORDER_FIND','搜索 :');
define('TEXT_ORDER_AMOUNT_SEARCH','用订单金额搜索');
define('TEXT_ORDER_FIND_SELECT','--------请选择--------');
define('TEXT_ORDER_FIND_NAME','用名字搜索');
define('TEXT_ORDER_FIND','搜索 :');
define('TEXT_ORDER_FIND_PRODUCT_NAME','用商品名搜索');
define('TEXT_ORDER_FIND_MAIL_ADD','用邮箱地址搜索');
define('TEXT_ORDER_QUERYER_NAME','确认者名:');
define('TEXT_EDIT_MAIL_TEXT','邮件正文编辑');
define('TEXT_SELECT_MORE','不能选择多个。');
define('TEXT_ORDER_SELECT','还没选择订单。');
define('TEXT_ORDER_WAIT','交易等待');
define('TEXT_ORDER_CARE','注意处理方式');
define('TEXT_ORDER_OROSHI','批发业者');
define('TEXT_ORDER_CUSTOMER_INFO','顾客信息');
define('TEXT_ORDER_HISTORY_ORDER','历史订单');
define('TEXT_ORDER_NEXT_ORDER','下一个订单');
define('TEXT_ORDER_ORDER_DATE','交易日期');
define('TEXT_ORDER_MIX','买卖混合');
define('TEXT_ORDER_BUY','买');
define('TEXT_ORDER_SELL','卖');
define('TEXT_ORDER_NOTICE','【注意】');
define('TEXT_ORDER_AUTO_RUN_ON','现在自动充值功能有效。　→ ');
define('TEXT_ORDER_AUTO_POWER_OFF','使无效');
define('TEXT_ORDER_AUTO_RUN_OFF','现在自动充值功能无效。　→ ');
define('TEXT_ORDER_AUTO_POWER_ON','使有效');
define('TEXT_ORDER_SHOW_LIST','目录显示');
define('TEXT_ORDER_STATUS_SET','订单状态设置');
define('TEXT_ORDER_CSV_OUTPUT','CSV导出');
define('TEXT_ORDER_DAY','日');
define('TEXT_ORDER_MONTH','月');
define('TEXT_ORDER_YEAR','年');
define('TEXT_ORDER_END_DATE','结束日期:');
define('TEXT_ORDER_START_DATE','开始日:');
define('TEXT_ORDER_SITE_TEXT','订单网站');
define('TEXT_ORDER_SERVER_BUSY','下载过程中服务器高负荷。请执行存取较少的时间。');
define('TEXT_ORDER_DOWNLOPAD','订单数据导出');

define('DEL_CONFIRM_PAYMENT_TIME', '删除');
define('NOTICE_DEL_CONFIRM_PAYEMENT_TIME', '删除时间吗？');
define('NOTICE_DEL_CONFIRM_PAYMENT_TIME_SUCCESS', '删除成功');

//for function
define('TEXT_FUNCTION_INPUT_FINISH','输入完成');
define('TEXT_FUNCTION_NOTICE','注意处理方式');
define('TEXT_FUNCTION_HAVE_HISTORY','有记录');
define('TEXT_FUNCTION_PAYMENT_METHOD','支付方法：');
define('TEXT_FUNCTION_DATE_STRING','Y年n月j日');
define('TEXT_FUNCTION_UN_GIVE_MONY','未入账');
define('TEXT_FUNCTION_UN_GIVE_MONY_DAY','进款日期 ');
define('TEXT_FUNCTION_OPTION','选择：');
define('TEXT_FUNCTION_CATEGORY','商品：');
define('TEXT_FUNCTION_FINISH','「完成」');
define('TEXT_FUNCTION_UNFINISH','「未完成」');
define('TEXT_FUNCTION_NUMBER','个数：');
define('TEXT_FUNCTION_NUM','个');
define('TEXT_FUNCTION_PC','PC：');
define('ORDERS_STATUS_SELECT_PRE', '状态「');
define('ORDERS_STATUS_SELECT_LAST', '」搜索');
define('TEXT_ORDER_FIND_OID', '用订单号搜索');
define('TEXT_SORT_ASC','▲');
define('TEXT_SORT_DESC','▼');
define('ORDERS_PAYMENT_METHOD_PRE', '支付方法「');
define('ORDERS_PAYMENT_METHOD_LAST', '」搜索');

define('TEXT_ORDER_TYPE_PRE', '订单种类「');
define('TEXT_ORDER_TYPE_LAST', '」搜索');
define('TEXT_ORDER_TYPE_SELL', '出售');
define('TEXT_ORDER_TYPE_BUY', '买入');
define('TEXT_ORDER_TYPE_MIX', '混合');
define('TEXT_ORDER_HISTORY_FROM_ORDER', '订单');
define('TEXT_ORDER_HISTORY_FROM_PREORDER', '预约');
define('TEXT_SHIPPING_METHOD','送货方法');
define('TEXT_SHIPPING_ADDRESS','送货地');
define('SHOW_MANUAL','手册');
define('SHOW_MANUAL_TITLE','的手册');
define('SHOW_MANUAL_SEARCH','搜索');
define('SHOW_MANUAL_NONE','暂无相关数据！！！');
define('SHOW_MANUAL_RETURN','返回');
define('SEARCH_MANUAL_PRODUCTS_FAIL','没有被搜索的手册！！！');
define('SEARCH_CAT_PRO_TITLE','种类 / 商品');
define('SEARCH_MANUAL_CONTENT','手册');
define('SEARCH_MANUAL_LOOK','操作');
define('MANUAL_SEARCH_HEAD', '的搜索结果');
define('MANUAL_SEARCH_EDIT', '编辑');
define('MANUAL_SEARCH_NORES','现在手册未注册... ');
define('TEXT_NO_RECEIVABLES','没到账');
define('TEXT_PAYMENT_BUY_POINT','点数(买入)');
define('TEXT_YEN','日元');
define('TEXT_HOUR','时');
define('TEXT_MIN','分');
define('TEXT_TWENTY_FOUR_HOUR','　（24小时制）');
define('TEXT_SEND_MAIL','发送完成：');
define('TEXT_ORDERS_ID','订单ID');
define('TEXT_OF','的');
define('TEXT_PAYMENT_VISIT','来店支付');
define('TEXT_PAYMENT_NOTICE','支付通知*');
define('TEXT_INPUT_ONE_TIME_PASSWORD','请输入一次性口令');
define('TEXT_INPUT_PASSWORD_ERROR','密码错误');
define('TEXT_STATUS_HANDLING_WARNING','注意处理');
define('TEXT_STATUS_WAIT_TRADE','等待交易');
define('TEXT_STATUS_READY_ENTER','输入完成');
define('TEXT_SITE_ORDER_FORM','订单网站:');
define('TEXT_TRADE_DATE','交易日期:');
define('TEXT_ORDERS_OID','订单号:');
define('TEXT_ORDERS_DATE','订购日期:');
define('TEXT_CUSTOMER_CLASS','顾客种类:');
define('TEXT_GUEST','非会员');
define('TEXT_MEMBER','会员');
define('TEXT_CREATE_NEW_NUMBER_SEARCH','新建咨询号码');
define('TEXT_EMAIL_ADDRESS','邮件');
define('TEXT_TEL_UNKNOW','信用卡');
define('TEXT_ADDRESS_INFO','住处信息');
define('TEXT_IP_ADDRESS','IP地址:');
define('TEXT_HOST_NAME','主机名:');
define('TEXT_USER_AGENT','用户代理:');
define('TEXT_BROWSER_TYPE','浏览器种类:');
define('TEXT_BROWSER_LANGUAGE','浏览器语言:');
define('TEXT_PC_LANGUAGE','电脑语言环境:');
define('TEXT_USERS_LANGUAGE','用户语言环境:');
define('TEXT_SCREEN_RESOLUTION','画面分辨率:');
define('TEXT_SCREEN_COLOR','画面颜色:');
define('TEXT_FLASH_VERSION','Flash版本:');
define('TEXT_CART_INFO','信用卡信息');
define('TEXT_CART_HOLDER','信用卡名:');
define('TEXT_TEL_NUMBER','电话号码:');
define('TEXT_EMAIL_ADDRESS_INFO','邮箱地址:');
define('TEXT_PRICE','金额:');
define('TEXT_PAYMENT_PAYPAL','PayPal结算');
define('TEXT_COUNTRY_CODE','居住国家:');
define('TEXT_PAYER_STATUS','认证:');
define('TEXT_PAYMENT_STATUS','支付状态:');
define('TEXT_PAYMENT_TYPE','支付类型:');
define('TEXT_SAVE','保存');
define('TEXT_TIME_LINK','到');
define('ORDER_TOP_MANUAL_TEXT', '首页');
define('ORDER__MANUAL_ALL_SHOW', '阅读全文');
?>
