<?php
/*
  $Id$
*/
define('TABLE_HEADING_CONFIGURATION_TITLE', '标题');
define('TABLE_HEADING_CONFIGURATION_VALUE', '设定值');
define('TABLE_HEADING_ACTION', '操作');
define('TEXT_INFO_EDIT_INTRO', '请添加必要的更改');
define('TEXT_INFO_DATE_ADDED', '创建日期:');
define('TEXT_INFO_LAST_MODIFIED', '更新日期:');
define('HEADING_TITLE_901','店铺初期设置');
define('HEADING_TITLE_16','时间设置');
define('HEADING_TITLE_2','最小值');
define('HEADING_TITLE_3','最大值');
define('HEADING_TITLE_1','店铺信息');
define('HEADING_TITLE_18','指定交易方法');
define('HEADING_TITLE_19','新评论设置');
define('HEADING_TITLE_5','账号显示');
define('HEADING_TITLE_7','发货/包装');
define('HEADING_TITLE_9','库存管理');
define('HEADING_TITLE_10','登录显示/记录');
define('HEADING_TITLE_11','页面缓存');
define('HEADING_TITLE_12','发送E-Mail');
define('HEADING_TITLE_13','下载销售');
define('HEADING_TITLE_14','GZip 压缩');
define('HEADING_TITLE_15','Session');
define('HEADING_TITLE_100','设置评论安全');
define('HEADING_TITLE_30','警告字符串设置');
define('HEADING_TITLE_2030','简易订单信息');
define('HEADING_TITLE_3000','混合图表设置');
define('TEXT_USER_ADDED','创建者:');
define('TEXT_USER_UPDATE','更新者:');
//=======================================================
define('DB_TITLE_STORE_OWNER','前台・后台：店名・店主姓名');
define('DB_DESCRIPTION_STORE_OWNER','设置店名・店主姓名（或者经营负责人姓名）.');
define('DB_TITLE_STORE_OWNER_EMAIL_ADDRESS','前台・后台：E-Mail 地址');
define('DB_DESCRIPTION_STORE_OWNER_EMAIL_ADDRESS','设置店铺用E-Mail 地址.');
define('DB_TITLE_EMAIL_FROM','前台・后台：发信人');
define('DB_DESCRIPTION_EMAIL_FROM','设置给顾客发送邮件的寄件人和地址.');
define('DB_TITLE_STORE_COUNTRY','前台・后台：国名');
define('DB_DESCRIPTION_STORE_COUNTRY','设置店铺所在地国名.');
define('DB_TITLE_STORE_ZONE','前台・后台：地区');
define('DB_DESCRIPTION_STORE_ZONE','设置店铺所在地区 (市名) .');
define('DB_TITLE_EXPECTED_PRODUCTS_SORT','前台：到货预定商品的分类顺序');
define('DB_DESCRIPTION_EXPECTED_PRODUCTS_SORT','设置到货预定商品的排列顺序. asc (升序) 或者 desc (降序).');
define('DB_TITLE_EXPECTED_PRODUCTS_FIELD','前台：到货预定商品的分类・范围');
define('DB_DESCRIPTION_EXPECTED_PRODUCTS_FIELD','设置到货预定商品的排列顺序使用专栏. products_name (名称顺序) 或者 date_expected (日期顺序).');
define('DB_TITLE_USE_DEFAULT_LANGUAGE_CURRENCY','前台：默认语言/变更货币');
define('DB_DESCRIPTION_USE_DEFAULT_LANGUAGE_CURRENCY','设置语言和变更货币是否联动.true (联动) false (不联动).');
define('DB_TITLE_SEND_EXTRA_ORDER_EMAILS_TO','前台：订单通知邮件的发送地址');
define('DB_DESCRIPTION_SEND_EXTRA_ORDER_EMAILS_TO','设置订单通知邮件的发送地址. 指定格式: 名称1 &lt;email@address1&gt;, 名称2 &lt;email@address2&gt;');
define('DB_TITLE_SEARCH_ENGINE_FRIENDLY_URLS','前台：使用搜索引擎用的URL(开发中)');
define('DB_DESCRIPTION_SEARCH_ENGINE_FRIENDLY_URLS','设置网站的所有链接是否使用搜索引擎用的URL. true (使用) false (不使用).');
define('DB_TITLE_DISPLAY_CART','前台：追加商品后显示在购物车里');
define('DB_DESCRIPTION_DISPLAY_CART','设置买入商品后购物车内容是否立即显示. true (显示) false (不显示).');
define('DB_TITLE_ALLOW_GUEST_TO_TELL_A_FRIEND','：非会员也可以使用「通知朋友」功能');
define('DB_DESCRIPTION_ALLOW_GUEST_TO_TELL_A_FRIEND','设置是否允许非会员把商品推荐给好友. true (允许) false(不允许).');
define('DB_TITLE_ADVANCED_SEARCH_DEFAULT_OPERATOR','前台・后台：默认的搜索运算符');
define('DB_DESCRIPTION_ADVANCED_SEARCH_DEFAULT_OPERATOR','设置默认的搜索运算符.');
define('DB_TITLE_STORE_NAME_ADDRESS','后台：店铺地址及电话');
define('DB_DESCRIPTION_STORE_NAME_ADDRESS','设置印刷和显示所使用的店铺名，地址，电话号码.');
define('DB_TITLE_SHOW_COUNTS','前台・后台：显示商品数量');
define('DB_DESCRIPTION_SHOW_COUNTS','设置分类中的商品数量是否也包含下一级分类的商品数.true (包含) false (不包含).');
define('DB_TITLE_TAX_DECIMAL_PLACES','前台・后台：税额的小数点位置');
define('DB_DESCRIPTION_TAX_DECIMAL_PLACES','设置税额的小数点以下的位数.');
define('DB_TITLE_DISPLAY_PRICE_WITH_TAX','前台・后台：显示含税金额');
define('DB_DESCRIPTION_DISPLAY_PRICE_WITH_TAX','true = 显示含税金额. false = 显示最后的税额.');
define('DB_TITLE_ENTRY_FIRST_NAME_MIN_LENGTH','前台・后台：姓');
define('DB_DESCRIPTION_ENTRY_FIRST_NAME_MIN_LENGTH','设置姓字数的最小值.');
define('DB_TITLE_ENTRY_LAST_NAME_MIN_LENGTH','前台・后台：名');
define('DB_DESCRIPTION_ENTRY_LAST_NAME_MIN_LENGTH','设置名字数的最小值.');
define('DB_TITLE_ENTRY_DOB_MIN_LENGTH','前台・后台：出生年月日');
define('DB_DESCRIPTION_ENTRY_DOB_MIN_LENGTH','设置出生年月日字数的最小值.');
define('DB_TITLE_ENTRY_EMAIL_ADDRESS_MIN_LENGTH','前台・后台：E-Mail 地址');
define('DB_DESCRIPTION_ENTRY_EMAIL_ADDRESS_MIN_LENGTH','E-Mail 设置地址字数的最小值.');
define('DB_TITLE_ENTRY_STREET_ADDRESS_MIN_LENGTH','前台・后台：地址1');
define('DB_DESCRIPTION_ENTRY_STREET_ADDRESS_MIN_LENGTH','设置地址1字数的最小值.');
define('DB_TITLE_ENTRY_COMPANY_LENGTH','前台・后台：公司');
define('DB_DESCRIPTION_ENTRY_COMPANY_LENGTH','设置公司名称字数的最小值.');
define('DB_TITLE_ENTRY_POSTCODE_MIN_LENGTH','前台・后台：邮政编码');
define('DB_DESCRIPTION_ENTRY_POSTCODE_MIN_LENGTH','设置邮政编码字数的最小值.');
define('DB_TITLE_ENTRY_CITY_MIN_LENGTH','前台・后台：省市区县');
define('DB_DESCRIPTION_ENTRY_CITY_MIN_LENGTH','设置省市区县字数的最小值.');
define('DB_TITLE_ENTRY_STATE_MIN_LENGTH','前台：省市区县');
define('DB_DESCRIPTION_ENTRY_STATE_MIN_LENGTH','设置省市区县字数的最小值.');
define('DB_TITLE_ENTRY_TELEPHONE_MIN_LENGTH','后台：电话号码');
define('DB_DESCRIPTION_ENTRY_TELEPHONE_MIN_LENGTH','设置电话号码字数的最小值.');
define('DB_TITLE_ENTRY_PASSWORD_MIN_LENGTH','前台・后台：密码');
define('DB_DESCRIPTION_ENTRY_PASSWORD_MIN_LENGTH','设置密码字数的最小值.');
define('DB_TITLE_CC_OWNER_MIN_LENGTH','前台・后台：信用卡持有者姓名');
define('DB_DESCRIPTION_CC_OWNER_MIN_LENGTH','设置信用卡持有者姓名字数的最小值.');
define('DB_TITLE_CC_NUMBER_MIN_LENGTH','前台・后台：信用卡号');
define('DB_DESCRIPTION_CC_NUMBER_MIN_LENGTH','设置信用卡号字数的最小值.');
define('DB_TITLE_REVIEW_TEXT_MIN_LENGTH','前台：评论');
define('DB_DESCRIPTION_REVIEW_TEXT_MIN_LENGTH','设置评论字数的最小值.');
define('DB_TITLE_MIN_DISPLAY_BESTSELLERS','前台：畅销');
define('DB_DESCRIPTION_MIN_DISPLAY_BESTSELLERS','设置畅销商品数量的最小值.');
define('DB_TITLE_MIN_DISPLAY_ALSO_PURCHASED','前台：同时购买');
define('DB_DESCRIPTION_MIN_DISPLAY_ALSO_PURCHASED','设置商品购买者...中商品数量的最小值.');
define('DB_TITLE_MAX_ADDRESS_BOOK_ENTRIES','前台：地址簿的登记数');
define('DB_DESCRIPTION_MAX_ADDRESS_BOOK_ENTRIES','设置顾客登记地址簿数量的最大值.');
define('DB_TITLE_MAX_DISPLAY_SEARCH_RESULTS','前台・后台：搜索结果的数量');
define('DB_DESCRIPTION_MAX_DISPLAY_SEARCH_RESULTS','设置商品一览数量的最大值.');
define('DB_TITLE_MAX_DISPLAY_PAGE_LINKS','前台・后台：页・链接数');
define('DB_DESCRIPTION_MAX_DISPLAY_PAGE_LINKS','设置商品目录和购买历史记录一览页面的页码最大值.');
define('DB_TITLE_MAX_DISPLAY_SPECIAL_PRODUCTS','后台：特价商品的显示数量');
define('DB_DESCRIPTION_MAX_DISPLAY_SPECIAL_PRODUCTS','设置特价商品数量的最大值.');
define('DB_TITLE_MAX_DISPLAY_NEW_PRODUCTS','前台：最新商品的数量');
define('DB_DESCRIPTION_MAX_DISPLAY_NEW_PRODUCTS','设置最新商品数量的最大值.');
define('DB_TITLE_MAX_DISPLAY_UPCOMING_PRODUCTS','前台：到货预定商品数量');
define('DB_DESCRIPTION_MAX_DISPLAY_UPCOMING_PRODUCTS','设置到货预定商品数量的最大值.');
define('DB_TITLE_MAX_DISPLAY_MANUFACTURERS_IN_A_LIST','前台:制造商・列表数量');
define('DB_DESCRIPTION_MAX_DISPLAY_MANUFACTURERS_IN_A_LIST','设置制造商・箱的参考值. 制造商数量超过这个值时,按照下拉清单显示.');
define('DB_TITLE_MAX_MANUFACTURERS_LIST','前台：制造商选择规格');
define('DB_DESCRIPTION_MAX_MANUFACTURERS_LIST','设置制造商・箱的参考值. 这个值是\'1\'时,按照下拉清单显示.除了1以外,指定的行数在文本框中表示.');
define('DB_TITLE_MAX_DISPLAY_MANUFACTURER_NAME_LEN','前台：制造商名称的长度');
define('DB_DESCRIPTION_MAX_DISPLAY_MANUFACTURER_NAME_LEN','设置制造商・箱参考的制造商名称字数的最大值.');
define('DB_TITLE_MAX_DISPLAY_NEW_REVIEWS','前台：新评论');
define('DB_DESCRIPTION_MAX_DISPLAY_NEW_REVIEWS','设置新评论数量的最大值.');
define('DB_TITLE_MAX_DISPLAY_CATEGORIES_PER_ROW','前台：一行显示的分类数');
define('DB_DESCRIPTION_MAX_DISPLAY_CATEGORIES_PER_ROW','设置一行显示的分类・种类数量.');
define('DB_TITLE_MAX_DISPLAY_PRODUCTS_NEW','前台：最新商品一览');
define('DB_DESCRIPTION_MAX_DISPLAY_PRODUCTS_NEW','设置最新商品页商品数量的最大值.');
define('DB_TITLE_MAX_DISPLAY_BESTSELLERS','前台：畅销');
define('DB_DESCRIPTION_MAX_DISPLAY_BESTSELLERS','设置畅销商品数量的最大值.');
define('DB_TITLE_MAX_DISPLAY_ALSO_PURCHASED','前台：同时购买');
define('DB_DESCRIPTION_MAX_DISPLAY_ALSO_PURCHASED','\'设置也购买这种商品\'中商品数量的最大值.');
define('DB_TITLE_MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX','前台：顾客的订单历史记录区');
define('DB_DESCRIPTION_MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX','设置顾客的订单历史记录区中商品数量的最大值.');
define('DB_TITLE_MAX_DISPLAY_ORDER_HISTORY','前台：订单历史记录');
define('DB_DESCRIPTION_MAX_DISPLAY_ORDER_HISTORY','设置顾客订单历史记录每页显示的最多订单数.');
define('DB_TITLE_SMALL_IMAGE_WIDTH','商品图片的宽度');
define('DB_DESCRIPTION_SMALL_IMAGE_WIDTH','设置小图片的宽（像素）.');
define('DB_TITLE_SMALL_IMAGE_HEIGHT','商品图片的高');
define('DB_DESCRIPTION_SMALL_IMAGE_HEIGHT','设置小图片的高（像素）.');
define('DB_TITLE_HEADING_IMAGE_WIDTH','页眉图片的宽');
define('DB_DESCRIPTION_HEADING_IMAGE_WIDTH','设置页眉图片的宽（像素）.');
define('DB_TITLE_HEADING_IMAGE_HEIGHT','页眉图片的高');
define('DB_DESCRIPTION_HEADING_IMAGE_HEIGHT','设置页眉图片的高（像素）.');
define('DB_TITLE_SUBCATEGORY_IMAGE_WIDTH','子分类图片的宽');
define('DB_DESCRIPTION_SUBCATEGORY_IMAGE_WIDTH','设置子分类图片的宽（像素）.');
define('DB_TITLE_SUBCATEGORY_IMAGE_HEIGHT','子分类图片的高');
define('DB_DESCRIPTION_SUBCATEGORY_IMAGE_HEIGHT','设置子分类图片的高（像素）.');
define('DB_TITLE_CONFIG_CALCULATE_IMAGE_SIZE','计算图片规格');
define('DB_DESCRIPTION_CONFIG_CALCULATE_IMAGE_SIZE','自动计算图片规格');
define('DB_TITLE_IMAGE_REQUIRED','必须有图片');
define('DB_DESCRIPTION_IMAGE_REQUIRED','没有图片（目录创建有效）');
define('DB_TITLE_API_KEYS','前台：API Keys');
define('DB_DESCRIPTION_API_KEYS','API Keys');
define('DB_TITLE_ACCOUNT_DOB','前台：出生年月日');
define('DB_DESCRIPTION_ACCOUNT_DOB','设置在顾客的帐号里是否显示出生年月日.');
define('DB_TITLE_ACCOUNT_COMPANY','前台：公司');
define('DB_DESCRIPTION_ACCOUNT_COMPANY','设置在顾客的帐号里是否显示公司.');
define('DB_TITLE_ACCOUNT_SUBURB','前台：地址2');
define('DB_DESCRIPTION_ACCOUNT_SUBURB','设置在顾客的帐号里是否显示地址2.');
define('DB_TITLE_MODULE_PAYMENT_INSTALLED','Installed Modules');
define('DB_DESCRIPTION_MODULE_PAYMENT_INSTALLED','List of payment module filenames separated by a semi-colon. This is automatically updated. No need to edit. 
(Example: cc.php;cod.php;paypal.php)');
define('DB_TITLE_MODULE_ORDER_TOTAL_INSTALLED','Installed Modules');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_INSTALLED','List of order_total module filenames separated by a semi-colon. This is automatically updated. No need to 
edit. (Example: ot_subtotal.php;ot_tax.php;ot_shipping.php;ot_total.php)');
define('DB_TITLE_MODULE_SHIPPING_INSTALLED','Installed Modules');
define('DB_DESCRIPTION_MODULE_SHIPPING_INSTALLED','List of shipping module filenames separated by a semi-colon. This is automatically updated. No need to edit. 
(Example: ups.php;flat.php;item.php)');
define('DB_TITLE_DEFAULT_CURRENCY','Default Currency');
define('DB_DESCRIPTION_DEFAULT_CURRENCY','Default Currency');
define('DB_TITLE_DEFAULT_LANGUAGE','Default Language');
define('DB_DESCRIPTION_DEFAULT_LANGUAGE','Default Language');
define('DB_TITLE_DEFAULT_ORDERS_STATUS_ID','Default Order Status For New Orders');
define('DB_DESCRIPTION_DEFAULT_ORDERS_STATUS_ID','When a new order is created, this order status will be assigned to it.');
define('DB_TITLE_MODULE_ORDER_TOTAL_CODT_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_CODT_SORT_ORDER','可设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MODULE_ORDER_TOTAL_CODT_STATUS','货到付款手续费的显示');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_CODT_STATUS','显示货到付款手续费吗？');
define('DB_TITLE_STORE_ORIGIN_COUNTRY','前台・后台：国家名称代码');
define('DB_DESCRIPTION_STORE_ORIGIN_COUNTRY','设置估算运费时被使用店铺的 &quot; ISO 3166&quot; 国家名称代码. 国家名称代码 <A HREF=
\"http://www.din.de/gremien/nas/nabd/iso3166ma/codlstp1/index.html\" TARGET=\"_blank\">请参考ISO 3166 Maintenance Agency</A>.');
define('DB_TITLE_STORE_ORIGIN_ZIP','前台・后台：邮政编码');
define('DB_DESCRIPTION_STORE_ORIGIN_ZIP','设置店铺的邮政编码. 用这个来估算运费.');
define('DB_TITLE_SHIPPING_MAX_WEIGHT','前台・后台：可以送货的最大包装重量');
define('DB_DESCRIPTION_SHIPPING_MAX_WEIGHT','设置可以送货的最大包装重量. 送货包装的重量有限制. 这是通用数值.');
define('DB_TITLE_SHIPPING_BOX_WEIGHT','前台・后台：包装重量');
define('DB_DESCRIPTION_SHIPPING_BOX_WEIGHT','设置小・中包装的标准重量.');
define('DB_TITLE_SHIPPING_BOX_PADDING','前台・后台：大包装- 增加率(%)');
define('DB_DESCRIPTION_SHIPPING_BOX_PADDING','增加10% 时请输入10 .');
define('DB_TITLE_PRODUCT_LIST_IMAGE','显示商品图像');
define('DB_DESCRIPTION_PRODUCT_LIST_IMAGE','设置商品一览中是否显示图像及排列顺序.');
define('DB_TITLE_PRODUCT_LIST_MANUFACTURER','显示制造商名称');
define('DB_DESCRIPTION_PRODUCT_LIST_MANUFACTURER','设置商品一览中是否显示制造商名称及排列顺序.');
define('DB_TITLE_PRODUCT_LIST_MODEL','显示商品型号');
define('DB_DESCRIPTION_PRODUCT_LIST_MODEL','设置商品一览中是否显示商品型号及排列顺序.');
define('DB_TITLE_PRODUCT_LIST_NAME','显示商品名称');
define('DB_DESCRIPTION_PRODUCT_LIST_NAME','设置商品一览中是否显示商品名称及排列顺序.');
define('DB_TITLE_PRODUCT_LIST_PRICE','显示商品价格');
define('DB_DESCRIPTION_PRODUCT_LIST_PRICE','设置商品一览中是否显示商品价格及排列顺序.');
define('DB_TITLE_PRODUCT_LIST_QUANTITY','显示商品数量');
define('DB_DESCRIPTION_PRODUCT_LIST_QUANTITY','设置商品一览中是否显示商品数量及排列顺序.');
define('DB_TITLE_PRODUCT_LIST_WEIGHT','显示商品重量');
define('DB_DESCRIPTION_PRODUCT_LIST_WEIGHT','设置商品一览中是否显示商品重量及排列顺序.');
define('DB_TITLE_PRODUCT_LIST_BUY_NOW','显示『立即购买』栏');
define('DB_DESCRIPTION_PRODUCT_LIST_BUY_NOW','设置商品一览中是否显示『立即购买』栏及排列顺序.');
define('DB_TITLE_PRODUCT_LIST_FILTER','分类/制造商的锁定');
define('DB_DESCRIPTION_PRODUCT_LIST_FILTER','设置在种类一览页是否显示『锁定』.？ (0=不显示 / 1=显示)');
define('DB_TITLE_PREV_NEXT_BAR_LOCATION','[上一页]/[下一页]的显示位置');
define('DB_DESCRIPTION_PREV_NEXT_BAR_LOCATION','设置『上一页』/『下一页』的显示位置. (1上 / 2=下 / 3=两者)');
define('DB_TITLE_STOCK_CHECK','前台：检查库存情况');
define('DB_DESCRIPTION_STOCK_CHECK','设置检查是否有足够的库存.');
define('DB_TITLE_STOCK_LIMITED','前台：从库存数中减去');
define('DB_DESCRIPTION_STOCK_LIMITED','设置接受订货时各库存数从订单数量中是否减去.');
define('DB_TITLE_STOCK_ALLOW_CHECKOUT','前台：允许细算(不可更改)');
define('DB_DESCRIPTION_STOCK_ALLOW_CHECKOUT','设置库存不足时是否允许细算.');
define('DB_TITLE_STOCK_MARK_PRODUCT_OUT_OF_STOCK','前台：无现货商品提示');
define('DB_DESCRIPTION_STOCK_MARK_PRODUCT_OUT_OF_STOCK','设置接受订货时无现货商品的提示.');
define('DB_TITLE_STOCK_REORDER_LEVEL','前台：库存的再次订购情况');
define('DB_DESCRIPTION_STOCK_REORDER_LEVEL','设置库存再次订购所需要的商品数量.');
define('DB_TITLE_STORE_PAGE_PARSE_TIME','前台・后台：记录页・解析时间');
define('DB_DESCRIPTION_STORE_PAGE_PARSE_TIME','在日志中记录页面解析时间的设置.');
define('DB_TITLE_STORE_PAGE_PARSE_TIME_LOG','前台・后台：日志的保存地址');
define('DB_DESCRIPTION_STORE_PAGE_PARSE_TIME_LOG','设置保存页面解析日志的目录和文件名.');
define('DB_TITLE_STORE_PARSE_DATE_TIME_FORMAT','前台・后台：日志的日期格式');
define('DB_DESCRIPTION_STORE_PARSE_DATE_TIME_FORMAT','设置日志中记录日期的格式.');
define('DB_TITLE_DISPLAY_PAGE_PARSE_TIME','前台・后台：显示页面・解析时间');
define('DB_DESCRIPTION_DISPLAY_PAGE_PARSE_TIME','页面下方解析时间显示的设置. (&quot;记录页面・解析时间&quot; 设置为 true )');
define('DB_TITLE_STORE_DB_TRANSACTIONS','前台・后台：记录数据库查询');
define('DB_DESCRIPTION_STORE_DB_TRANSACTIONS','设置日志中数据库查询记录（只有PHP4 ）.');
define('DB_TITLE_USE_CACHE','前台・后台：使用缓存');
define('DB_DESCRIPTION_USE_CACHE','设置是否使用缓存功能.');
define('DB_TITLE_DIR_FS_CACHE','前台・后台：缓存・目录');
define('DB_DESCRIPTION_DIR_FS_CACHE','设置缓存・文件的存储目录.');
define('DB_TITLE_EMAIL_TRANSPORT','前台・后台：发送邮件设置');
define('DB_DESCRIPTION_EMAIL_TRANSPORT','设置使用本地连接或通过TCP / IP的SMTP连接来发送E-mail.');
define('DB_TITLE_EMAIL_LINEFEED','前台・后台：E-Mail换行');
define('DB_DESCRIPTION_EMAIL_LINEFEED','指定用于分隔电子邮件・标头的换行代码.');
define('DB_TITLE_EMAIL_USE_HTML','前台・后台：用MIME HTML发送E-mail');
define('DB_DESCRIPTION_EMAIL_USE_HTML','设置用HTML的形式来发送E-mail.');
define('DB_TITLE_ENTRY_EMAIL_ADDRESS_CHECK','前台・后台：用DNS确认E-Mail地址');
define('DB_DESCRIPTION_ENTRY_EMAIL_ADDRESS_CHECK','设置用DNS服务器确认E-Mail.');
define('DB_TITLE_SEND_EMAILS','前台・后台：发送E-Mail');
define('DB_DESCRIPTION_SEND_EMAILS','E-mail发送到外部');
define('DB_TITLE_DOWNLOAD_ENABLED','前台・后台：支持下载');
define('DB_DESCRIPTION_DOWNLOAD_ENABLED','设置商品的下载销售.');
define('DB_TITLE_DOWNLOAD_BY_REDIRECT','前台・后台：重定向下载');
define('DB_DESCRIPTION_DOWNLOAD_BY_REDIRECT','使用浏览器重定向下载. 只用于Unix系统.');
define('DB_TITLE_DOWNLOAD_MAX_DAYS','后台：有效期(天)');
define('DB_DESCRIPTION_DOWNLOAD_MAX_DAYS','设置下载・链接有效期的天数. 0 时没有期限.');
define('DB_TITLE_DOWNLOAD_MAX_COUNT','后台：下载的最大次数');
define('DB_DESCRIPTION_DOWNLOAD_MAX_COUNT','设置可以下载的最大次数. 0 时下载无法认证.');
define('DB_TITLE_GZIP_COMPRESSION','前台：使用GZip压缩');
define('DB_DESCRIPTION_GZIP_COMPRESSION','设置使用HTTP GZip 压缩发送页.');
define('DB_TITLE_GZIP_LEVEL','前台：压缩级别');
define('DB_DESCRIPTION_GZIP_LEVEL','使用压缩级别 (0 = 最小, 9 = 最大).');
define('DB_TITLE_TAX_ROUND_OPTION','前台・后台：处理税额的零数');
define('DB_DESCRIPTION_TAX_ROUND_OPTION','指定税额零数的处理方法. drop=舍去, round=四舍五入, raise=进位');
define('DB_TITLE_STORE_ORIGIN_ZONE','前台・后台：地区代码');
define('DB_DESCRIPTION_STORE_ORIGIN_ZONE','设置估算运费所使用的店铺的JIS省市区县代码(2位).');
define('DB_TITLE_SESSION_RECREATE','前台：Session的再生成');
define('DB_DESCRIPTION_SESSION_RECREATE','顾客登录或者创建账号时,为了赋予新的SessionID进行Session的再生成. (PHP >=4.1 needed).');
define('DB_TITLE_AFFILIATE_EMAIL_ADDRESS','前台・后台：E-Mail地址');
define('DB_DESCRIPTION_AFFILIATE_EMAIL_ADDRESS','设置网络联盟计划用的E-Mail地址');
define('DB_TITLE_DEFAULT_PAGE_BOTTOM_CONTENTS','前台：首页的页脚内容 ');
define('DB_DESCRIPTION_DEFAULT_PAGE_BOTTOM_CONTENTS','首页的页脚内容  ');
define('DB_TITLE_AFFILATE_INDIVIDUAL_PERCENTAGE','前台・后台：会员还原率设置');
define('DB_DESCRIPTION_AFFILATE_INDIVIDUAL_PERCENTAGE','是否启用网络联盟会员的报酬还原率？');
define('DB_TITLE_AFFILATE_USE_TIER','前台・后台：下一阶层的有效化');
define('DB_DESCRIPTION_AFFILATE_USE_TIER','允许加入会员自身的下一阶层的联盟会员吗？');
define('DB_TITLE_MODULE_PAYMENT_MEMBER_CD','会员号');
define('DB_DESCRIPTION_MODULE_PAYMENT_MEMBER_CD','认证码的设置。');
define('DB_TITLE_MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER','可以设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MODULE_ORDER_TOTAL_TOTAL_STATUS','显示合计金额');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_TOTAL_STATUS','显示合计金额吗?');
define('DB_TITLE_MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER','可以设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MAX_DISPLAY_LATEST_NEWS','前台：最新信息');
define('DB_DESCRIPTION_MAX_DISPLAY_LATEST_NEWS','请输入显示最新信息的数量。');
define('DB_TITLE_MODULE_PAYMENT_REMISE_EXTURL','Remise ExtUrl');
define('DB_DESCRIPTION_MODULE_PAYMENT_REMISE_EXTURL','扩展包发送网址');
define('DB_TITLE_MODULE_PAYMENT_REMISE_CARD','保存信用卡信息');
define('DB_DESCRIPTION_MODULE_PAYMENT_REMISE_CARD','请选择保存信用卡信息');
define('DB_TITLE_MODULE_PAYMENT_REMISE_MODEL','波动价格商品的型号');
define('DB_DESCRIPTION_MODULE_PAYMENT_REMISE_MODEL','商品型号里只有指定的文字列存在时才进行购物车有效性检查。之后在管理页面以任意价格进行销售额管理。');
define('DB_TITLE_MODULE_PAYMENT_REMISE_ORDER_STATUS_ID_CAPTURE','销售订单状态的延长设置');
define('DB_DESCRIPTION_MODULE_PAYMENT_REMISE_ORDER_STATUS_ID_CAPTURE','请设置信用卡结算时的订单状态');
define('DB_TITLE_MODULE_PAYMENT_REMISE_ORDER_STATUS_ID_RETURN','扩展包退货订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_REMISE_ORDER_STATUS_ID_RETURN','请设置信用卡结算退货时的订单状态');
define('DB_TITLE_MODULE_PAYMENT_REMISE_ORDER_STATUS_ID_VOID','扩展包取消订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_REMISE_ORDER_STATUS_ID_VOID','请设置取消信用卡结算时的订单状态');
define('DB_TITLE_MODULE_PAYMENT_REMISE_ORDER_STATUS_ID_FUTEIKAN','波动价格商品的订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_REMISE_ORDER_STATUS_ID_FUTEIKAN','请设置购买波动价格商品时的订单状态');
define('DB_TITLE_C_TITLE','前台：主页标题');
define('DB_DESCRIPTION_C_TITLE','变成浏览器标题。<title>.....</title>');
define('DB_TITLE_C_KEYWORDS','前台：主页关键字');
define('DB_DESCRIPTION_C_KEYWORDS','请输入主页关键字用逗号「,」隔开。将反映在META标签里。');
define('DB_TITLE_C_DESCRIPTION','前台：主页说明');
define('DB_DESCRIPTION_C_DESCRIPTION','请输入主页的说明。将反映在META标签里。');
define('DB_TITLE_C_ROBOTS','前台：自动装置');
define('DB_DESCRIPTION_C_ROBOTS','用搜索引擎索引时请在index.follow里检查。');
define('DB_TITLE_C_AUTHER','前台：主页作者');
define('DB_DESCRIPTION_C_AUTHER','请输入主页作者。将反映在META标签里。');
define('DB_TITLE_C_EMAIL_FOOTER','前台：邮件署名');
define('DB_DESCRIPTION_C_EMAIL_FOOTER','显示在发送所有电子邮件的页脚。');
define('DB_TITLE_C_CREAT_ACCOUNT','前台：会员注册邮件');
define('DB_DESCRIPTION_C_CREAT_ACCOUNT','创建账号时发送的邮件。<br>用户ID：　${MAIL}<br>密码：　${PASS}');
define('DB_TITLE_MODULE_PAYMENT_BUYING_STATUS','启用购买功能');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYING_STATUS','支持银行汇款的支付方式吗?');
define('DB_TITLE_C_ORDER','前台・后台：订单邮件');
define('DB_DESCRIPTION_C_ORDER','订购时发送的邮件标头。');
define('DB_TITLE_MODULE_PAYMENT_GUIDANCE_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_PAYMENT_GUIDANCE_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_C_FOOTER_COPY_RIGHT','前台：页脚版权');
define('DB_DESCRIPTION_C_FOOTER_COPY_RIGHT','请输入出现在主页页脚的版权。');
define('DB_TITLE_CL_COLOR_01','店铺休息日的背景颜色');
define('DB_DESCRIPTION_CL_COLOR_01','设置店铺休息日日历上的背景颜色');
define('DB_TITLE_CL_COLOR_02','休息日回复邮件的背景颜色');
define('DB_DESCRIPTION_CL_COLOR_02','设置休息日只回复邮件日历上的背景颜色');
define('DB_TITLE_MODULE_PAYMENT_CARDP_CARD_DISCOVER','Discover');
define('DB_DESCRIPTION_MODULE_PAYMENT_CARDP_CARD_DISCOVER','可使用Discover时，请选择ON。');
define('DB_TITLE_MODULE_PAYMENT_CARDP_CARD_ABANKCARD','Australian BankCard');
define('DB_DESCRIPTION_MODULE_PAYMENT_CARDP_CARD_ABANKCARD','如果可以使用Australian BankCard，请选择ON。');
define('DB_TITLE_MODULE_PAYMENT_CARDP_CARD','保存信用卡信息');
define('DB_DESCRIPTION_MODULE_PAYMENT_CARDP_CARD','请选择是否保存信用卡信息');
define('DB_TITLE_PRODUCT_INFO_IMAGE_WIDTH','商品图片宽度');
define('DB_DESCRIPTION_PRODUCT_INFO_IMAGE_WIDTH','设置商品图片的宽度(像素）.');
define('DB_TITLE_PRODUCT_INFO_IMAGE_HEIGHT','商品图片的高度');
define('DB_DESCRIPTION_PRODUCT_INFO_IMAGE_HEIGHT','设置商品图片的高度（像素）.');
define('DB_TITLE_ADMINPAGE_LOGO_IMAGE','后台：管理页面logo');
define('DB_DESCRIPTION_ADMINPAGE_LOGO_IMAGE','设置在管理页面显示标志');
define('DB_TITLE_PDF_SHOW_LOGO','显示标志');
define('DB_DESCRIPTION_PDF_SHOW_LOGO','显示标志吗？ 0 =不显示, 1 = 显示');
define('DB_TITLE_PDF_IMAGE_KEEP_PROPORTIONS','Keep imageproportions');
define('DB_DESCRIPTION_PDF_IMAGE_KEEP_PROPORTIONS','Keep proportions of the productsimage. 0 = No, 1=Yes.');
define('DB_TITLE_PDF_MAX_IMAGE_WIDTH','图像宽度（最大）');
define('DB_DESCRIPTION_PDF_MAX_IMAGE_WIDTH','请输入最大宽度以毫米为单位的图像。');
define('DB_TITLE_PDF_SHOW_WATERMARK','背景色');
define('DB_DESCRIPTION_PDF_SHOW_WATERMARK','背景颜色用蓝线显示吗？ 0 = 不显示 1 = 显示');
define('DB_TITLE_PDF_MAX_IMAGE_HEIGHT','图像的高度（最大）');
define('DB_DESCRIPTION_PDF_MAX_IMAGE_HEIGHT','请输入最大高度以毫米为单位的图像。');
define('DB_TITLE_PDF_DOC_PATH','pdf文件的路径');
define('DB_DESCRIPTION_PDF_DOC_PATH','The Path where your pdf-documents shall be stored. Example: \"pdfdocs/\". Without leading but with a trailing slash. Take 
care that this directory exists and remember to change the directory rights, so that the shop can write to that directory!');
define('DB_TITLE_PDF_FILE_REDIRECT','重定向・下载');
define('DB_DESCRIPTION_PDF_FILE_REDIRECT','Shall the User be redirected to the file or shall start a download of this document? 0 = download, 1 = redirect');
define('DB_TITLE_PDF_SHOW_BACKGROUND','背景');
define('DB_DESCRIPTION_PDF_SHOW_BACKGROUND','Shall the page have a background? 1 = Yes, 0 = No');
define('DB_TITLE_PDF_PAGE_BG_COLOR','背景颜色');
define('DB_DESCRIPTION_PDF_PAGE_BG_COLOR','The backgroundcolor of the page. Insert a commaseparated RGB-Value (Red,Yellow,Blue).Values per color from 0-255.');
define('DB_TITLE_PDF_STORE_LOGO','店铺标志的路径');
define('DB_DESCRIPTION_PDF_STORE_LOGO','Set this Value to the path of your storelogo');
define('DB_TITLE_PDF_HEADER_COLOR_TEXT','标题文字颜色');
define('DB_DESCRIPTION_PDF_HEADER_COLOR_TEXT','The color of the text in the header. Insert a commaseparated RGB-Value (Red,Yellow,Blue).Values per color from 0-255.');
define('DB_TITLE_PDF_BODY_COLOR_TEXT','文本颜色');
define('DB_DESCRIPTION_PDF_BODY_COLOR_TEXT','The textcolor of the body. Insert a commaseparated RGB-Value (Red,Yellow,Blue).Values per color from 0-255.');
define('DB_TITLE_PDF_HEADER_COLOR_TABLE','表头的颜色');
define('DB_DESCRIPTION_PDF_HEADER_COLOR_TABLE','The backgroundcolor of the Header Table. Insert a commaseparated RGB-Value (Red,Yellow,Blue). Values per color from 0-255.');
define('DB_TITLE_PDF_SAVE_DOCUMENT','服务器保存');
define('DB_DESCRIPTION_PDF_SAVE_DOCUMENT','Shall the generated document be saved on the server? Values: 0 = No, 1 = Yes');
define('DB_TITLE_MODULE_ORDER_TOTAL_SUBTOTAL_STATUS','显示小计');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_SUBTOTAL_STATUS','显示小计吗?');
define('DB_TITLE_SEO_ENABLED','Enable SEO URLs?');
define('DB_DESCRIPTION_SEO_ENABLED','Enable the SEO URLs?  This is a global setting and will turn them off completely.');
define('DB_TITLE_SEO_ADD_CPATH_TO_PRODUCT_URLS','Add cPath to product URLs?');
define('DB_DESCRIPTION_SEO_ADD_CPATH_TO_PRODUCT_URLS','This setting will append the cPath to the end of product URLs (i.e. -some-product-p-1.html?cPath=xx).');
define('DB_TITLE_SEO_ADD_CAT_PARENT','Add category parent to begining of URLs?');
define('DB_DESCRIPTION_SEO_ADD_CAT_PARENT','This setting will add the category parent name to the beginning of the category URLs (i.e. - parent-category-c-1.html).');
define('DB_TITLE_SEO_URLS_FILTER_SHORT_WORDS','Filter Short Words');
define('DB_DESCRIPTION_SEO_URLS_FILTER_SHORT_WORDS','This setting will filter words less than or equal to the value from the URL.');
define('DB_TITLE_USE_SEO_CACHE_GLOBAL','Enable SEO cache to save queries?');
define('DB_DESCRIPTION_USE_SEO_CACHE_GLOBAL','This is a global setting and will turn off caching completely.');
define('DB_TITLE_USE_SEO_CACHE_PRODUCTS','Enable product cache?');
define('DB_DESCRIPTION_USE_SEO_CACHE_PRODUCTS','This will turn off caching for the products.');
define('DB_TITLE_USE_SEO_CACHE_CATEGORIES','Enable categories cache?');
define('DB_DESCRIPTION_USE_SEO_CACHE_CATEGORIES','This will turn off caching for the categories.');
define('DB_TITLE_USE_SEO_CACHE_MANUFACTURERS','Enable manufacturers cache?');
define('DB_DESCRIPTION_USE_SEO_CACHE_MANUFACTURERS','This will turn off caching for the manufacturers.');
define('DB_TITLE_USE_SEO_CACHE_ARTICLES','Enable articles cache?');
define('DB_DESCRIPTION_USE_SEO_CACHE_ARTICLES','This will turn off caching for the articles.');
define('DB_TITLE_USE_SEO_CACHE_TOPICS','Enable topics cache?');
define('DB_DESCRIPTION_USE_SEO_CACHE_TOPICS','This will turn off caching for the article topics.');
define('DB_TITLE_USE_SEO_CACHE_INFO_PAGES','Enable information cache?');
define('DB_DESCRIPTION_USE_SEO_CACHE_INFO_PAGES','This will turn off caching for the information pages.');
define('DB_TITLE_USE_SEO_REDIRECT','Enable automatic redirects?');
define('DB_DESCRIPTION_USE_SEO_REDIRECT','This will activate the automatic redirect code and send 301 headers for old to new URLs.');
define('DB_TITLE_SEO_REWRITE_TYPE','Choose URL Rewrite Type');
define('DB_DESCRIPTION_SEO_REWRITE_TYPE','Choose which SEO URL format to use.');
define('DB_TITLE_SEO_CHAR_CONVERT_SET','Enter special character conversions');
define('DB_DESCRIPTION_SEO_CHAR_CONVERT_SET','This setting will convert characters.<br><br>The format <b>MUST</b> be in the form: 
<b>char=>conv,char2=>conv2</b>');
define('DB_TITLE_SEO_REMOVE_ALL_SPEC_CHARS','Remove all non-alphanumeric characters?');
define('DB_DESCRIPTION_SEO_REMOVE_ALL_SPEC_CHARS','This will remove all non-letters and non-numbers.  This should be handy to remove all special characters with 1 setting.');
define('DB_TITLE_SEO_URLS_CACHE_RESET','Reset SEO URLs Cache');
define('DB_DESCRIPTION_SEO_URLS_CACHE_RESET','This will reset the cache data for SEO');
define('DB_TITLE_COLOR_SEARCH_BOX_TF','前台・后台：颜色搜索功能');
define('DB_DESCRIPTION_COLOR_SEARCH_BOX_TF','启用颜色搜索功能');
define('DB_TITLE_MODULE_PAYMENT_MONEYORDER_STATUS','启用银行汇款功能');
define('DB_DESCRIPTION_MODULE_PAYMENT_MONEYORDER_STATUS','启用银行汇款支付方式吗?');
define('DB_TITLE_MODULE_PAYMENT_MONEYORDER_PAYTO','汇款人:');
define('DB_DESCRIPTION_MODULE_PAYMENT_MONEYORDER_PAYTO','汇款人设置.');
define('DB_TITLE_MODULE_PAYMENT_MONEYORDER_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_PAYMENT_MONEYORDER_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_PAYMENT_MONEYORDER_ZONE','适用地区');
define('DB_DESCRIPTION_MODULE_PAYMENT_MONEYORDER_ZONE','选择适用地区后，只有被选择的地区可以使用.');
define('DB_TITLE_MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID','初期订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID','设置的状态在接受订单时适用.');
define('DB_TITLE_MODULE_PAYMENT_MONEYORDER_COST','结算手续费');
define('DB_DESCRIPTION_MODULE_PAYMENT_MONEYORDER_COST','结算手续费 例: 价格在300日元一下，需支付30日元的手续费　300:*0+30, 价格在301〜1000日元以内，需支付价格的2％的手续费999:*0.02, 价格在1000日元以上，免手续费　99999999:*0, 为了避免使用无限大符号，请使用本网站不可能存在的数值。 300:*0+30中如果是*0，手续费变为300+30，请谨慎处理。');
define('DB_TITLE_MODULE_ORDER_TOTAL_CONV_STATUS','显示货到付款手续费');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_CONV_STATUS','显示便利店付款手续费吗?');
define('DB_TITLE_MODULE_ORDER_TOTAL_CONV_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_CONV_SORT_ORDER','可设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_NG_URL','返回网址');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_NG_URL','完成网址的设置。<br>因为暂时改变设定值，<br>所以一般变为空值。');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_OK_URL','结束网址');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_OK_URL','完成网址的设置。<br>因为暂时改变设定值，<br>所以一般变为空值。');
define('DB_TITLE_MODULE_PAYMENT_BUYING_COST','结算手续费');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYING_COST','结算手续费 例: 价格在300日元以下，需支付30日元的手续费　300:*0+30, 价格在301〜1000日元以内，需支付价格的2％
的手续费　999:*0.02, 价格在1000日元以上，免手续费　99999999:*0, 为了避免使用无限大符号，请使用本网站不可能存在的数值。 300:*0+30中如果是*0，手续费变为300+30，请
谨慎处理。');
define('DB_TITLE_MODULE_PAYMENT_BUYING_MONEY_LIMIT','可结算金额');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYING_MONEY_LIMIT','设置可结算金额的最大和最小值
例：0,3000
输入0,3000日元时，从0到3000日元的金额可以结算。设置范围之外的不可以结算。');
define('DB_TITLE_MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN','设置总销售额的合计期间');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN','设置判定顾客等级总金额的合计期间。<br>以「日」为单位，合计期间是365日时请输入「365」。');
define('DB_TITLE_MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL','顾客级别的使用');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL','点数的计算方法适用于顾客级别?');
define('DB_TITLE_MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK','设置与顾客级别对应的还原率');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK','设置顾客级别的点数还原率。<br>用逗号隔开可创建多个。<br>例）
排名：青铜，给点数5％，销售总额20000日元时→「青铜,0.05,20000」<br>※创建多个是请用「||」隔开');
define('DB_TITLE_MODULE_ORDER_TOTAL_POINT_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_POINT_SORT_ORDER','可设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MODULE_ORDER_TOTAL_POINT_FEE','点数还原率');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_POINT_FEE','设置还原率。<br>还原率是5%时请输入「0.05」10%时请输入「0.1」。');
define('DB_TITLE_MODULE_ORDER_TOTAL_POINT_ADD_STATUS','点数合计的设置');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_POINT_ADD_STATUS','进行点数合计状态的设置<br>结账时合计选择默认，更新状态时合计选择合计状态');
define('DB_TITLE_MODULE_ORDER_TOTAL_POINT_LIMIT','设置点数的有效期限');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_POINT_LIMIT','设置点数的有效期限（天数）。<br>不设置时请输入「0」。');
define('DB_TITLE_MODULE_ORDER_TOTAL_POINT_STATUS','点数系统的使用');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_POINT_STATUS','使用点数系统吗?');
define('DB_TITLE_MODULE_PAYMENT_POSTALMONEYORDER_COST','结算手续费');
define('DB_DESCRIPTION_MODULE_PAYMENT_POSTALMONEYORDER_COST','结算手续费 例: 价格在300日元一下，需支付30日元的手续费　300:*0+30, 价格在301〜1000日元以内，需支付价格的2％的手续费　999:*0.02, 价格在1000日元以上，免手续费　99999999:*0, 为了避免使用无限大符号，请使用本网站不可能存在的数值。 300:*0+30中如果是*0，手续费变为300+30，请谨慎处理。');
define('DB_TITLE_SUPPORT_EMAIL_ADDRESS','前台・后台：咨询用邮箱地址');
define('DB_DESCRIPTION_SUPPORT_EMAIL_ADDRESS','设置咨询用邮箱地址.');
define('DB_TITLE_SENTMAIL_ADDRESS','前台・后台：发送完毕邮件');
define('DB_DESCRIPTION_SENTMAIL_ADDRESS','设置发送完毕邮件地址.');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_ZONE','适用地区');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_ZONE','选择适用地区后，只有被选择的地区可以使用.');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_ORDER_STATUS_ID','初期订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_ORDER_STATUS_ID','设置的状态在接受订单时适用.');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_MONEY_LIMIT','可结算金额');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_MONEY_LIMIT','设置可结算金额的最大和最小值
例：0,3000
输入0,3000日元时，从0到3000日元的金额可以结算。设置范围之外的不可以结算。');
define('DB_TITLE_CATEGORY_IMAGE_WIDTH','主要分类图像的宽度');
define('DB_DESCRIPTION_CATEGORY_IMAGE_WIDTH','设置主要分类图像的宽度(像素).');
define('DB_TITLE_CATEGORY_IMAGE_HEIGHT','主要分类图像的高度');
define('DB_DESCRIPTION_CATEGORY_IMAGE_HEIGHT','设置主要分类图像的高度(像素).');
define('DB_TITLE_MAX_DISPLAY_PRODUCTS_RESULTS','前台：最新商品显示数');
define('DB_DESCRIPTION_MAX_DISPLAY_PRODUCTS_RESULTS','最新商品页面每页显示的商品数量.');
define('DB_TITLE_MAX_DISPLAY_PRODUCTS_ADMIN','前台・后台：商品列表的显示数量');
define('DB_DESCRIPTION_MAX_DISPLAY_PRODUCTS_ADMIN','管理页面商品列表每页的显示数量');
define('DB_TITLE_MAX_DISPLAY_ORDERS_RESULTS','后台：订单显示数');
define('DB_DESCRIPTION_MAX_DISPLAY_ORDERS_RESULTS','设置订单一览显示数量的最大值.');
define('DB_TITLE_DOCUMENTS_SHOW_CATEGORIES','Documents Show Categories');
define('DB_DESCRIPTION_DOCUMENTS_SHOW_CATEGORIES','Show the documents on the Documents page grouped by category');
define('DB_TITLE_DOCUMENTS_SHOW_BOX','Show Documents Box');
define('DB_DESCRIPTION_DOCUMENTS_SHOW_BOX','Show links to a page of document types in a separate box');
define('DB_TITLE_DOCUMENTS_SHOW_INFO_BOX','Show Documents Info Box');
define('DB_DESCRIPTION_DOCUMENTS_SHOW_INFO_BOX','Show links to a page of document types in the Information box');
define('DB_TITLE_DOCUMENTS_SHOW_PRODUCT_INFO','Show Documents Product Info');
define('DB_DESCRIPTION_DOCUMENTS_SHOW_PRODUCT_INFO','Show links to relevant documents on the Product Info page');
define('DB_TITLE_STORE_DOMAIN','后台：域名');
define('DB_DESCRIPTION_STORE_DOMAIN','网站域名');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_STATUS','启用便利店结算');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_STATUS','启用便利店结算的支付方法吗?');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_IP','加盟店代码');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_IP','设置加盟店代码。');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_URL','连接网址');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_URL','设置连接网址。');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_COST','结算手续费');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_COST','结算手续费 例: 价格在300日元以下，需支付30日元的手续费　300:*0+30, 价格在301〜1000日元以内，需支付价格的2％的手续费　999:*0.02, 价格在1000日元以上，免手续费　99999999:*0, 为了避免使用无限大符号，请使用本网站不可能300:*0+30中如果是*0，手续费变为300+30，请谨慎处理。');
define('DB_TITLE_MODULE_PAYMENT_MONEYORDER_MONEY_LIMIT','可结算金额');
define('DB_DESCRIPTION_MODULE_PAYMENT_MONEYORDER_MONEY_LIMIT','设置可结算金额的最大和最小值
例：0,3000
输入0,3000日元时，从0到3000日元的金额可以结算。设置范围之外的不可以结算。');
define('DB_TITLE_MODULE_PAYMENT_BUYING_ZONE','适用地区');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYING_ZONE','选择适用地区后，只有被选择的地区可以使用.');
define('DB_TITLE_MODULE_PAYMENT_BUYING_ORDER_STATUS_ID','初期订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYING_ORDER_STATUS_ID','设置的状态在接受订单时适用.');
define('DB_TITLE_MODULE_PAYMENT_BUYING_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYING_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_PAYMENT_POSTALMONEYORDER_MONEY_LIMIT','可结算金额');
define('DB_DESCRIPTION_MODULE_PAYMENT_POSTALMONEYORDER_MONEY_LIMIT','设置可结算金额的最大和最小值
例：0,3000
输入0,3000日元时，从0到3000日元的金额可以结算。设置范围之外的不可以结算。');
define('DB_TITLE_MODULE_PAYMENT_POSTALMONEYORDER_ZONE','适用地区');
define('DB_DESCRIPTION_MODULE_PAYMENT_POSTALMONEYORDER_ZONE','选择适用地区后，只有被选择的地区可以使用.');
define('DB_TITLE_MODULE_PAYMENT_POSTALMONEYORDER_ORDER_STATUS_ID','初期订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_POSTALMONEYORDER_ORDER_STATUS_ID','设置的状态在接受订单时适用.');
define('DB_TITLE_MODULE_PAYMENT_TELECOM_STATUS','TELECOM 启用支付功能');
define('DB_DESCRIPTION_MODULE_PAYMENT_TELECOM_STATUS','启用TELECOM支付吗?');
define('DB_TITLE_MODULE_PAYMENT_TELECOM_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_PAYMENT_TELECOM_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_PAYMENT_TELECOM_ORDER_STATUS_ID','初期订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_TELECOM_ORDER_STATUS_ID','设置的状态在接受订单时适用.');
define('DB_TITLE_MODULE_PAYMENT_TELECOM_CONNECTION_URL','连接网址');
define('DB_DESCRIPTION_MODULE_PAYMENT_TELECOM_CONNECTION_URL','设置telecom信用卡申请页面url。');
define('DB_TITLE_MODULE_PAYMENT_TELECOM_KID','节目编排');
define('DB_DESCRIPTION_MODULE_PAYMENT_TELECOM_KID','设置节目编排。');
define('DB_TITLE_MODULE_PAYMENT_OK_URL','戻り先URL(正常時)');
define('DB_DESCRIPTION_MODULE_PAYMENT_OK_URL','设置返回网址（正常时）。');
define('DB_TITLE_MODULE_PAYMENT_NO_URL','返回网址（取消时）');
define('DB_DESCRIPTION_MODULE_PAYMENT_NO_URL','设置返回网址（取消时）。');
define('DB_TITLE_MODULE_PAYMENT_TELECOM_COST','结算手续费');
define('DB_DESCRIPTION_MODULE_PAYMENT_TELECOM_COST','结算手续费 例:价格在300日元以下，需支付30日元的手续费　300:*0+30,价格在301〜1000日元以内，需支付价格的2％的手续费　999:*0.02,价格在1000日元以上，免手续费　99999999:*0,为了避免使用无限大符号，请使用本网站不可能存在的数值。300:*0+30中如果是*0，手续费变为300+30，请谨慎处理。');
define('DB_TITLE_MODULE_PAYMENT_TELECOM_MONEY_LIMIT','可结算金额');
define('DB_DESCRIPTION_MODULE_PAYMENT_TELECOM_MONEY_LIMIT','设置可结算金额的最大和最小值
例：0,3000
输入0,3000日元时，从0到3000日元的金额可以结算。设置范围之外的不可以结算。');
define('DB_TITLE_MODULE_PAYMENT_POSTALMONEYORDER_STATUS','启用邮政银行（邮局）');
define('DB_DESCRIPTION_MODULE_PAYMENT_POSTALMONEYORDER_STATUS','启用邮政银行（邮局）支付方法吗?');
define('DB_TITLE_MODULE_PAYMENT_POSTALMONEYORDER_PAYTO','汇款人:');
define('DB_DESCRIPTION_MODULE_PAYMENT_POSTALMONEYORDER_PAYTO','汇款人设置.');
define('DB_TITLE_MODULE_PAYMENT_POSTALMONEYORDER_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_PAYMENT_POSTALMONEYORDER_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_DS_ADMIN_ORDER_RELOAD','后台：订单管理页面重新载入时间的设置');
define('DB_DESCRIPTION_DS_ADMIN_ORDER_RELOAD','管理页面的订单管理页指定每秒重新载入。<BR>请指定以秒为单位。');
define('DB_TITLE_DS_LIMIT_PRICE','前台：设置订单上限金额');
define('DB_DESCRIPTION_DS_LIMIT_PRICE','设置用户可以下订单的上限金额。请用半角输入');
define('DB_TITLE_DS_TORIHIKI_HOUHOU','前台・后台：设置交易方法');
define('DB_DESCRIPTION_DS_TORIHIKI_HOUHOU','设置交易方法。换行隔开可设置多个。在选择器中从上面依次显示。');
define('DB_TITLE_DS_LATEST_NEWS_NEW_LIMIT','前台：NEW的显示期限');
define('DB_DESCRIPTION_DS_LATEST_NEWS_NEW_LIMIT','从投稿日到设置日期以NEW图标显示。单位为”天”。');
define('DB_TITLE_ALL_GAME_RSS','前台：所有游戏的RSS地址');
define('DB_DESCRIPTION_ALL_GAME_RSS','所有游戏的RSS地址');
define('DB_TITLE_GAME_NEWS_MAX_DISPLAY','前台：主页游戏新闻的显示数量　');
define('DB_DESCRIPTION_GAME_NEWS_MAX_DISPLAY','主页游戏新闻的显示数量　');
define('DB_TITLE_CATEGORIES_GAME_NEWS_MAX_DISPLAY','前台：分类游戏新闻的显示数量');
define('DB_DESCRIPTION_CATEGORIES_GAME_NEWS_MAX_DISPLAY','分类游戏新闻的显示数量');
define('DB_TITLE_MODULE_METASEO_A_LATEST_NEWS_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_A_LATEST_NEWS_TITLE','通知内容的标题
<br>#TITLE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_A_LATEST_NEWS_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_A_LATEST_NEWS_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_INSTALLED','Installed Modules');
define('DB_DESCRIPTION_MODULE_METASEO_INSTALLED','This is automatically updated. No need to edit.');
define('DB_TITLE_PRINT_EMAIL_ADDRESS','前台・后台：打印邮件');
define('DB_DESCRIPTION_PRINT_EMAIL_ADDRESS','打印邮件');
define('DB_TITLE_REVIEWS_TIME_LIMIT','前台：评论的最短间隔时间');
define('DB_DESCRIPTION_REVIEWS_TIME_LIMIT','比评论的最短间隔时间还短，单位：秒');
define('DB_TITLE_IP_LIGHT_KEYWORDS','后台：IP地址警告字符串');
define('DB_DESCRIPTION_IP_LIGHT_KEYWORDS','IP地址警告字符串，ex：192.|222.');
define('DB_TITLE_USER_AGENT_LIGHT_KEYWORDS','后台：用户代理警告字符串');
define('DB_DESCRIPTION_USER_AGENT_LIGHT_KEYWORDS','用户代理警告字符串，ex：tt|qq|msie');
define('DB_TITLE_REVIEWS_DAY_LIMIT','前台：每天评论的次数');
define('DB_DESCRIPTION_REVIEWS_DAY_LIMIT','超过每24小时和每IP的限制');
define('DB_TITLE_MODULE_METASEO_A_TAG_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_A_TAG_KEYWORDS','标签的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_A_TAG_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_A_TAG_DESCRIPTION','标签的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_A_TAG_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_A_TAG_ROBOTS','标签的自动装置');
define('DB_TITLE_MODULE_METASEO_A_TAG_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_A_TAG_COPYRIGHT','标签的作者');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_TITLE','顾客信息的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_KEYWORDS','顾客信息的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_DESCRIPTION','顾客信息的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_ROBOTS','顾客信息的自动装置');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_COPYRIGHT','顾客信息的作者');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_EDIT_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_EDIT_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_EDIT_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_EDIT_TITLE','账号编辑的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_EDIT_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_EDIT_KEYWORDS','账号编辑的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_EDIT_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_EDIT_DESCRIPTION','账号编辑的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_EDIT_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_EDIT_ROBOTS','账号编辑的自动装置');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_EDIT_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_EDIT_COPYRIGHT','账号编辑的作者');
define('DB_TITLE_MODULE_METASEO_BROWSER_IE6X_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_BROWSER_IE6X_ROBOTS','浏览器设置的自动装置');
define('DB_TITLE_MODULE_METASEO_BROWSER_IE6X_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_BROWSER_IE6X_COPYRIGHT','浏览器设置的著作者');
define('DB_TITLE_MODULE_METASEO_BROWSER_IE6X_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_BROWSER_IE6X_DESCRIPTION','浏览器设的説明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_BROWSER_IE6X_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_BROWSER_IE6X_KEYWORDS','浏览器设置的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_BROWSER_IE6X_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_BROWSER_IE6X_TITLE','浏览器设的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_HISTORY_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_HISTORY_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_HISTORY_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_HISTORY_TITLE','订单历史记录的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_HISTORY_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_HISTORY_KEYWORDS','订单历史记录的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_HISTORY_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_HISTORY_DESCRIPTION','订单历史记录的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_HISTORY_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_HISTORY_ROBOTS','订单历史记录的自动装置');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_HISTORY_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_HISTORY_COPYRIGHT','订单历史记录的著作者');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_HISTORY_INFO_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_HISTORY_INFO_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_HISTORY_INFO_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_HISTORY_INFO_TITLE','订单信息的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_HISTORY_INFO_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_HISTORY_INFO_KEYWORDS','订单信息的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_HISTORY_INFO_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_HISTORY_INFO_DESCRIPTION','订单信息的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_HISTORY_INFO_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_HISTORY_INFO_ROBOTS','订单信息的自动装置');
define('DB_TITLE_MODULE_METASEO_ACCOUNT_HISTORY_INFO_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_ACCOUNT_HISTORY_INFO_COPYRIGHT','订单信息的著作者');
define('DB_TITLE_MODULE_METASEO_ADVANCED_SEARCH_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_ADVANCED_SEARCH_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_ADVANCED_SEARCH_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_ADVANCED_SEARCH_TITLE','商品搜索的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ADVANCED_SEARCH_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_ADVANCED_SEARCH_KEYWORDS','商品搜索的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ADVANCED_SEARCH_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_ADVANCED_SEARCH_DESCRIPTION','商品搜索的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ADVANCED_SEARCH_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_ADVANCED_SEARCH_ROBOTS','商品搜索的自动装置');
define('DB_TITLE_MODULE_METASEO_ADVANCED_SEARCH_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_ADVANCED_SEARCH_COPYRIGHT','商品搜索的著作者');
define('DB_TITLE_MODULE_METASEO_ADVANCED_SEARCH_RESULT_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_ADVANCED_SEARCH_RESULT_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_ADVANCED_SEARCH_RESULT_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_ADVANCED_SEARCH_RESULT_TITLE','搜索结果的标题
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ADVANCED_SEARCH_RESULT_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_ADVANCED_SEARCH_RESULT_KEYWORDS','搜索结果的关键字
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ADVANCED_SEARCH_RESULT_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_ADVANCED_SEARCH_RESULT_DESCRIPTION','搜索结果的说明
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_ADVANCED_SEARCH_RESULT_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_ADVANCED_SEARCH_RESULT_ROBOTS','搜索结果的自动装置');
define('DB_TITLE_MODULE_METASEO_ADVANCED_SEARCH_RESULT_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_ADVANCED_SEARCH_RESULT_COPYRIGHT','搜索结果的作者');
define('DB_TITLE_MODULE_METASEO_BROWSER_IE6X_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_BROWSER_IE6X_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_CATEGORY_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_CATEGORY_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_CATEGORY_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_CATEGORY_TITLE','分类信息的标题
<br>#CATEGORIES_NAME#<br>#SEO_NAME#<br>#SEO_DESCRIPTION#<br>#CATEGORIES_META_TEXT#<br>#CATEGORIES_HEADER_TEXT#<br>#CATEGORIES_FOOTER_TEXT#<br>#TEXT_INFORMATION#
<br>#META_KEYWORDS#<br>#META_DESCRIPTION#<br>#CATEGORIES_ID#<br>#STORE_NAME#<br>#BRE');
define('DB_TITLE_MODULE_METASEO_CATEGORY_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_CATEGORY_KEYWORDS','种类信息的关键字
<br>#CATEGORIES_NAME#<br>#SEO_NAME#<br>#SEO_DESCRIPTION#<br>#CATEGORIES_META_TEXT#<br>#CATEGORIES_HEADER_TEXT#<br>#CATEGORIES_FOOTER_TEXT#<br>#TEXT_INFORMATION#
<br>#META_KEYWORDS#<br>#META_DESCRIPTION#<br>#CATEGORIES_ID#<br>#STORE_NAME#<br>#BR');
define('DB_TITLE_MODULE_METASEO_CATEGORY_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_CATEGORY_DESCRIPTION','分类信息的说明
<br>#CATEGORIES_NAME#<br>#SEO_NAME#<br>#SEO_DESCRIPTION#<br>#CATEGORIES_META_TEXT#<br>#CATEGORIES_HEADER_TEXT#<br>#CATEGORIES_FOOTER_TEXT#<br>#TEXT_INFORMATION#
<br>#META_KEYWORDS#<br>#META_DESCRIPTION#<br>#CATEGORIES_ID#<br>#STORE_NAME#<br>#BREAD');
define('DB_TITLE_MODULE_METASEO_CATEGORY_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_CATEGORY_ROBOTS','分类信息的自动装置');
define('DB_TITLE_MODULE_METASEO_CATEGORY_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_CATEGORY_COPYRIGHT','分类信息的作者');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_CONFIRMATION_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_CONFIRMATION_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_CONFIRMATION_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_CONFIRMATION_TITLE','最终确认的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_CONFIRMATION_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_CONFIRMATION_KEYWORDS','最终确认的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_CONFIRMATION_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_CONFIRMATION_DESCRIPTION','最终确认的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_CONFIRMATION_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_CONFIRMATION_ROBOTS','最终确认的自动装置');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_CONFIRMATION_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_CONFIRMATION_COPYRIGHT','最终确认的著作者');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_PAYMENT_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_PAYMENT_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_PAYMENT_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_PAYMENT_TITLE','支付方式的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_PAYMENT_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_PAYMENT_KEYWORDS','支付方法的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_PAYMENT_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_PAYMENT_DESCRIPTION','支付方法的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_PAYMENT_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_PAYMENT_ROBOTS','支付方法的自动装置');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_PAYMENT_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_PAYMENT_COPYRIGHT','支付方法的著作者');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_PRODUCTS_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_PRODUCTS_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_PRODUCTS_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_PRODUCTS_TITLE','输入的交易人物名的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_PRODUCTS_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_PRODUCTS_KEYWORDS','输入的交易人物名的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_PRODUCTS_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_PRODUCTS_DESCRIPTION','输入的交易人物名的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_PRODUCTS_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_PRODUCTS_ROBOTS','输入的交易人物名的自动装置');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_PRODUCTS_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_PRODUCTS_COPYRIGHT','输入的交易人物名的作者');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_SHIPPING_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_SHIPPING_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_SHIPPING_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_SHIPPING_TITLE','指定的交易时间的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_SHIPPING_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_SHIPPING_KEYWORDS','指定的交易时间的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_SHIPPING_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_SHIPPING_DESCRIPTION','指定的交易时间的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_SHIPPING_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_SHIPPING_ROBOTS','指定的交易时间的自动装置');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_SHIPPING_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_SHIPPING_COPYRIGHT','指定的交易时间的作者');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_SUCCESS_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_SUCCESS_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_SUCCESS_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_SUCCESS_TITLE','订单手续完成的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_SUCCESS_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_SUCCESS_KEYWORDS','订单手续完成的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_SUCCESS_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_SUCCESS_DESCRIPTION','订单手续完成的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_SUCCESS_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_SUCCESS_ROBOTS','订单手续完成的自动装置');
define('DB_TITLE_MODULE_METASEO_CHECKOUT_SUCCESS_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_CHECKOUT_SUCCESS_COPYRIGHT','订单手续完成的作者');
define('DB_TITLE_MODULE_METASEO_CONTACT_US_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_CONTACT_US_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_CONTACT_US_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_CONTACT_US_TITLE','咨询的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CONTACT_US_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_CONTACT_US_KEYWORDS','咨询的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CONTACT_US_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_CONTACT_US_DESCRIPTION','咨询的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CONTACT_US_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_CONTACT_US_ROBOTS','咨询的自动装置');
define('DB_TITLE_MODULE_METASEO_CONTACT_US_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_CONTACT_US_COPYRIGHT','咨询的作者');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_TITLE','创建账号的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_KEYWORDS','创建账号的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_DESCRIPTION','创建账号的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_ROBOTS','创建账号的自动装置');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_COPYRIGHT','创建账号的作者');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_PROCESS_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_PROCESS_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_PROCESS_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_PROCESS_TITLE','创建账号手续的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_PROCESS_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_PROCESS_KEYWORDS','账号创建手续的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_PROCESS_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_PROCESS_DESCRIPTION','账号创建手续的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_PROCESS_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_PROCESS_ROBOTS','账号创建手续的自动装置');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_PROCESS_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_PROCESS_COPYRIGHT','账号创建手续的作者');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_SUCCESS_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_SUCCESS_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_SUCCESS_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_SUCCESS_TITLE','账号创建完成的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_SUCCESS_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_SUCCESS_KEYWORDS','账号创建完成的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_SUCCESS_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_SUCCESS_DESCRIPTION','账号创建完成的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_SUCCESS_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_SUCCESS_ROBOTS','账号创建完成的自动装置');
define('DB_TITLE_MODULE_METASEO_CREATE_ACCOUNT_SUCCESS_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_CREATE_ACCOUNT_SUCCESS_COPYRIGHT','账号创建完成的作者');
define('DB_TITLE_MODULE_METASEO_EMAIL_TROUBLE_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_EMAIL_TROUBLE_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_EMAIL_TROUBLE_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_EMAIL_TROUBLE_TITLE','收不到免费邮件的顾客的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_EMAIL_TROUBLE_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_EMAIL_TROUBLE_KEYWORDS','收不到免费邮件的顾客的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_EMAIL_TROUBLE_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_EMAIL_TROUBLE_DESCRIPTION','收不到免费邮件的顾客的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_EMAIL_TROUBLE_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_EMAIL_TROUBLE_ROBOTS','收不到免费邮件的顾客的自动装置');
define('DB_TITLE_MODULE_METASEO_EMAIL_TROUBLE_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_EMAIL_TROUBLE_COPYRIGHT','收不到免费邮件的顾客的作者');
define('DB_TITLE_MODULE_METASEO_LATEST_NEWS_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_LATEST_NEWS_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_LATEST_NEWS_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_LATEST_NEWS_TITLE','通知的标题
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_LATEST_NEWS_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_LATEST_NEWS_KEYWORDS','通知的关键字
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_LATEST_NEWS_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_LATEST_NEWS_DESCRIPTION','通知的说明
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_LATEST_NEWS_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_LATEST_NEWS_ROBOTS','通知的自动装置');
define('DB_TITLE_MODULE_METASEO_LATEST_NEWS_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_LATEST_NEWS_COPYRIGHT','通知的作者');
define('DB_TITLE_MODULE_METASEO_LOGIN_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_LOGIN_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_LOGIN_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_LOGIN_TITLE','登录的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_LOGIN_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_LOGIN_KEYWORDS','登录的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_LOGIN_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_LOGIN_DESCRIPTION','登录的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_LOGIN_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_LOGIN_ROBOTS','登录的自动装置');
define('DB_TITLE_MODULE_METASEO_LOGIN_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_LOGIN_COPYRIGHT','登录的作者');
define('DB_TITLE_MODULE_METASEO_LOGOFF_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_LOGOFF_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_LOGOFF_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_LOGOFF_TITLE','退出登录的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_LOGOFF_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_LOGOFF_KEYWORDS','退出登录的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_LOGOFF_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_LOGOFF_DESCRIPTION','退出登录的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_LOGOFF_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_LOGOFF_ROBOTS','退出登录的自动装置');
define('DB_TITLE_MODULE_METASEO_LOGOFF_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_LOGOFF_COPYRIGHT','退出登录的作者');
define('DB_TITLE_MODULE_METASEO_MAIL_MAGAZINE_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_MAIL_MAGAZINE_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_MAIL_MAGAZINE_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_MAIL_MAGAZINE_TITLE','邮件杂志的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_MAIL_MAGAZINE_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_MAIL_MAGAZINE_KEYWORDS','邮件杂志的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_MAIL_MAGAZINE_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_MAIL_MAGAZINE_DESCRIPTION','邮件杂志的説明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_MAIL_MAGAZINE_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_MAIL_MAGAZINE_ROBOTS','邮件杂志的自动装置');
define('DB_TITLE_MODULE_METASEO_MAIL_MAGAZINE_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_MAIL_MAGAZINE_COPYRIGHT','邮件杂志的著作者');
define('DB_TITLE_MODULE_METASEO_TELL_A_FRIEND_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_TELL_A_FRIEND_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_TELL_A_FRIEND_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_TELL_A_FRIEND_TITLE','通知朋友的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_TELL_A_FRIEND_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_TELL_A_FRIEND_KEYWORDS','通知朋友的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_TELL_A_FRIEND_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_TELL_A_FRIEND_DESCRIPTION','通知朋友的説明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_TELL_A_FRIEND_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_TELL_A_FRIEND_ROBOTS','通知朋友的自动装置');
define('DB_TITLE_MODULE_METASEO_TELL_A_FRIEND_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_TELL_A_FRIEND_COPYRIGHT','通知朋友的著作者');
define('DB_TITLE_MODULE_METASEO_TAGS_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_TAGS_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_TAGS_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_TAGS_TITLE','标签一览的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_TAGS_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_TAGS_KEYWORDS','标签一览的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_TAGS_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_TAGS_DESCRIPTION','标签一览的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_TAGS_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_TAGS_ROBOTS','标签一览的自动装置');
define('DB_TITLE_MODULE_METASEO_TAGS_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_TAGS_COPYRIGHT','标签一览的作者');
define('DB_TITLE_MODULE_METASEO_PAGE_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_PAGE_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_PAGE_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_PAGE_TITLE','内容的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PAGE_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_PAGE_KEYWORDS','内容的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PAGE_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_PAGE_DESCRIPTION','内容的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PAGE_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_PAGE_ROBOTS','内容的自动装置');
define('DB_TITLE_MODULE_METASEO_PAGE_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_PAGE_COPYRIGHT','内容的作者');
define('DB_TITLE_MODULE_METASEO_MANUFACTURERS_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_MANUFACTURERS_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_MANUFACTURERS_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_MANUFACTURERS_TITLE','制造商一览的标题
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_MANUFACTURERS_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_MANUFACTURERS_KEYWORDS','制造商一览的关键字
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_MANUFACTURERS_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_MANUFACTURERS_DESCRIPTION','制造商一览的说明
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_MANUFACTURERS_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_MANUFACTURERS_ROBOTS','制造商一览的自动装置');
define('DB_TITLE_MODULE_METASEO_MANUFACTURERS_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_MANUFACTURERS_COPYRIGHT','制造商一览的作者');
define('DB_TITLE_MODULE_METASEO_SPECIALS_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_SPECIALS_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_SPECIALS_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_SPECIALS_TITLE','特价商品的标题
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_SPECIALS_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_SPECIALS_KEYWORDS','特价商品的关键字
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_SPECIALS_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_SPECIALS_DESCRIPTION','特价商品的说明
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_SPECIALS_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_SPECIALS_ROBOTS','特价商品的自动装置');
define('DB_TITLE_MODULE_METASEO_SPECIALS_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_SPECIALS_COPYRIGHT','特价商品的作者');
define('DB_TITLE_MODULE_METASEO_PRODUCT_INFO_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_INFO_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_PRODUCT_INFO_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_INFO_TITLE','商品信息的标题
<br>#PRODUCT_NAME#<br>#PRODUCT_MODEL#<br>#PRODUCT_DESCRITION#<br>#MANUFACTURERS_NAME#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br
>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_INFO_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_INFO_KEYWORDS','商品信息的关键字
<br>#PRODUCT_NAME#<br>#PRODUCT_MODEL#<br>#PRODUCT_DESCRITION#<br>#MANUFACTURERS_NAME#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br
>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_INFO_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_INFO_DESCRIPTION','商品信息的说明
<br>#PRODUCT_NAME#<br>#PRODUCT_MODEL#<br>#PRODUCT_DESCRITION#<br>#MANUFACTURERS_NAME#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br
>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_INFO_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_INFO_ROBOTS','商品信息的自动装置');
define('DB_TITLE_MODULE_METASEO_PRODUCT_INFO_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_INFO_COPYRIGHT','商品信息的作者');
define('DB_TITLE_MODULE_METASEO_MANUFACTURER_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_MANUFACTURER_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_MANUFACTURER_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_MANUFACTURER_TITLE','制造商的标题
<br>#KEYWORDS#<br>#DESCRIPTION#<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWO
RD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_MANUFACTURER_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_MANUFACTURER_KEYWORDS','制造商的关键字
<br>#KEYWORDS#<br>#DESCRIPTION#<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWO
RD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_MANUFACTURER_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_MANUFACTURER_DESCRIPTION','制造商的说明
<br>#KEYWORDS#<br>#DESCRIPTION#<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWO
RD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_MANUFACTURER_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_MANUFACTURER_ROBOTS','制造商的自动装置');
define('DB_TITLE_MODULE_METASEO_MANUFACTURER_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_MANUFACTURER_COPYRIGHT','制造商的作者');
define('DB_TITLE_MODULE_METASEO_PASSWORD_FORGOTTEN_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_PASSWORD_FORGOTTEN_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_PASSWORD_FORGOTTEN_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_PASSWORD_FORGOTTEN_TITLE','更改密码标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PASSWORD_FORGOTTEN_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_PASSWORD_FORGOTTEN_KEYWORDS','更改密码关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PASSWORD_FORGOTTEN_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_PASSWORD_FORGOTTEN_DESCRIPTION','更改密码说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PASSWORD_FORGOTTEN_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_PASSWORD_FORGOTTEN_ROBOTS','更改密码自动装置');
define('DB_TITLE_MODULE_METASEO_PASSWORD_FORGOTTEN_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_PASSWORD_FORGOTTEN_COPYRIGHT','更改密码作者');
define('DB_TITLE_MODULE_METASEO_POPUP_SEARCH_HELP_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_POPUP_SEARCH_HELP_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_POPUP_SEARCH_HELP_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_POPUP_SEARCH_HELP_TITLE','详细搜索的使用方法的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_POPUP_SEARCH_HELP_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_POPUP_SEARCH_HELP_KEYWORDS','详细搜索的使用方法的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_POPUP_SEARCH_HELP_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_POPUP_SEARCH_HELP_DESCRIPTION','详细搜索的使用方法的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_POPUP_SEARCH_HELP_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_POPUP_SEARCH_HELP_ROBOTS','详细搜索的使用方法的自动装置');
define('DB_TITLE_MODULE_METASEO_POPUP_SEARCH_HELP_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_POPUP_SEARCH_HELP_COPYRIGHT','详细搜索的使用方法的作者');
define('DB_TITLE_MODULE_METASEO_PREORDER_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_PREORDER_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_PREORDER_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_PREORDER_TITLE','预约的标题
<br>#CATEGORIES_NAME#<br>#PRODUCTS_NAME#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<b
r>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PREORDER_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_PREORDER_KEYWORDS','预约的关键字
<br>#CATEGORIES_NAME#<br>#PRODUCTS_NAME#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<b
r>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PREORDER_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_PREORDER_DESCRIPTION','预约的说明
<br>#CATEGORIES_NAME#<br>#PRODUCTS_NAME#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<b
r>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PREORDER_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_PREORDER_ROBOTS','预约的自动装置');
define('DB_TITLE_MODULE_METASEO_PREORDER_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_PREORDER_COPYRIGHT','预约的作者');
define('DB_TITLE_MODULE_METASEO_PRESENT_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_PRESENT_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_TITLE','馈赠礼品的标题
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRESENT_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_KEYWORDS','馈赠礼品的关键字
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRESENT_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_DESCRIPTION','馈赠礼品的说明
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRESENT_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_ROBOTS','馈赠礼品的自动装置');
define('DB_TITLE_MODULE_METASEO_PRESENT_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_COPYRIGHT','馈赠礼品的作者');
define('DB_TITLE_MODULE_METASEO_PRESENT_CONFIRMATION_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_CONFIRMATION_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_PRESENT_CONFIRMATION_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_CONFIRMATION_TITLE','馈赠礼品募集的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRESENT_CONFIRMATION_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_CONFIRMATION_KEYWORDS','馈赠礼品募集的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRESENT_CONFIRMATION_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_CONFIRMATION_DESCRIPTION','馈赠礼品募集的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRESENT_CONFIRMATION_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_CONFIRMATION_ROBOTS','馈赠礼品募集的自动装置');
define('DB_TITLE_MODULE_METASEO_PRESENT_CONFIRMATION_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_CONFIRMATION_COPYRIGHT','馈赠礼品募集的作者');
define('DB_TITLE_MODULE_METASEO_PRODUCT_NOTIFICATIONS_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_NOTIFICATIONS_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_PRODUCT_NOTIFICATIONS_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_NOTIFICATIONS_TITLE','商品通知的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_NOTIFICATIONS_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_NOTIFICATIONS_KEYWORDS','商品通知的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_NOTIFICATIONS_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_NOTIFICATIONS_DESCRIPTION','商品通知的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_NOTIFICATIONS_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_NOTIFICATIONS_ROBOTS','商品通知的自动装置');
define('DB_TITLE_MODULE_METASEO_PRODUCT_NOTIFICATIONS_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_NOTIFICATIONS_COPYRIGHT','商品通知的作者');
define('DB_TITLE_MODULE_METASEO_SITEMAP_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_SITEMAP_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_SITEMAP_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_SITEMAP_TITLE','网站地图的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_SITEMAP_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_SITEMAP_KEYWORDS','网站地图的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_SITEMAP_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_SITEMAP_DESCRIPTION','网站地图的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_SITEMAP_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_SITEMAP_ROBOTS','网站地图的自动装置');
define('DB_TITLE_MODULE_METASEO_SITEMAP_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_SITEMAP_COPYRIGHT','网站地图的作者');
define('DB_TITLE_MODULE_METASEO_SHOPPING_CART_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_SHOPPING_CART_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_SHOPPING_CART_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_SHOPPING_CART_TITLE','购物车的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_SHOPPING_CART_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_SHOPPING_CART_KEYWORDS','购物车的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_SHOPPING_CART_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_SHOPPING_CART_DESCRIPTION','购物车的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_SHOPPING_CART_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_SHOPPING_CART_ROBOTS','购物车的自动装置');
define('DB_TITLE_MODULE_METASEO_SHOPPING_CART_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_SHOPPING_CART_COPYRIGHT','购物车的作者');
define('DB_TITLE_MODULE_METASEO_SEND_MAIL_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_SEND_MAIL_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_SEND_MAIL_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_SEND_MAIL_TITLE','接受邮件测试的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_SEND_MAIL_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_SEND_MAIL_KEYWORDS','接受邮件测试的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_SEND_MAIL_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_SEND_MAIL_DESCRIPTION','接受邮件测试的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_SEND_MAIL_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_SEND_MAIL_ROBOTS','接受邮件测试的自动装置');
define('DB_TITLE_MODULE_METASEO_SEND_MAIL_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_SEND_MAIL_COPYRIGHT','接受邮件测试的作者');
define('DB_TITLE_MODULE_METASEO_REVIEWS_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_REVIEWS_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_REVIEWS_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_REVIEWS_TITLE','评论一览的标题
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_REVIEWS_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_REVIEWS_KEYWORDS','评论一览的关键字
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_REVIEWS_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_REVIEWS_DESCRIPTION','评论一览的说明
<br>#SEO_PAGE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_REVIEWS_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_REVIEWS_ROBOTS','评论一览的自动装置<br>#SEO_PAGE#');
define('DB_TITLE_MODULE_METASEO_REVIEWS_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_REVIEWS_COPYRIGHT','评论一览的作者<br>#SEO_PAGE#');
define('DB_TITLE_MODULE_METASEO_REORDER2_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_REORDER2_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_REORDER2_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_REORDER2_TITLE','再送货形式2的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_REORDER2_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_REORDER2_KEYWORDS','再送货形式2的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_REORDER2_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_REORDER2_DESCRIPTION','再送货形式2的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_REORDER2_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_REORDER2_ROBOTS','再送货形式2的自动装置');
define('DB_TITLE_MODULE_METASEO_REORDER2_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_REORDER2_COPYRIGHT','再送货形式2的著作者');
define('DB_TITLE_MODULE_METASEO_REORDER_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_REORDER_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_REORDER_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_REORDER_TITLE','再送货形式的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_REORDER_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_REORDER_KEYWORDS','再送货形式的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_REORDER_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_REORDER_DESCRIPTION','再送货形式的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_REORDER_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_REORDER_ROBOTS','再送货形式的自动装置');
define('DB_TITLE_MODULE_METASEO_REORDER_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_REORDER_COPYRIGHT','再送货形式的作者');
define('DB_TITLE_MODULE_METASEO_PRODUCTS_NEW_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCTS_NEW_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_PRODUCTS_NEW_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCTS_NEW_TITLE','最新商品的标题
<br>#SEO_PAGE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCTS_NEW_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCTS_NEW_KEYWORDS','最新商品的关键字
<br>#SEO_PAGE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCTS_NEW_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCTS_NEW_DESCRIPTION','最新商品的说明
<br>#SEO_PAGE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCTS_NEW_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCTS_NEW_ROBOTS','最新商品的自动装置');
define('DB_TITLE_MODULE_METASEO_PRODUCTS_NEW_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCTS_NEW_COPYRIGHT','最新商品的作者');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_INFO_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_INFO_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_INFO_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_INFO_TITLE','评论的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_INFO_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_INFO_KEYWORDS','评论的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_INFO_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_INFO_DESCRIPTION','评论的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_INFO_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_INFO_ROBOTS','评论的自动装置');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_INFO_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_INFO_COPYRIGHT','评论的著作者');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_WRITE_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_WRITE_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_WRITE_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_WRITE_TITLE','写商品评论的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_WRITE_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_WRITE_KEYWORDS','写商品评论的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_WRITE_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_WRITE_DESCRIPTION','写商品评论的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_WRITE_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_WRITE_ROBOTS','写商品评论的自动装置<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_WRITE_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_WRITE_COPYRIGHT','写商品评论的作者<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_TITLE','商品评论的标题
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_KEYWORDS','商品评论的关键字
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_DESCRIPTION','商品评论的说明
<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_ROBOTS','商品评论的自动装置');
define('DB_TITLE_MODULE_METASEO_PRODUCT_REVIEWS_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_PRODUCT_REVIEWS_COPYRIGHT','商品评论的著作者');
define('DB_TITLE_MODULE_METASEO_A_LATEST_NEWS_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_A_LATEST_NEWS_KEYWORDS','通知内容的关键字
<br>#TITLE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_A_LATEST_NEWS_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_A_LATEST_NEWS_DESCRIPTION','通知内容的说明
<br>#TITLE#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_DEFAULT_PAGE_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_DEFAULT_PAGE_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_DEFAULT_PAGE_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_DEFAULT_PAGE_TITLE','分类信息的标题
<br>#CATEGORIES_NAME#<br>#SEO_NAME#<br>#SEO_DESCRIPTION#<br>#CATEGORIES_META_TEXT#<br>#CATEGORIES_HEADER_TEXT#<br>#CATEGORIES_FOOTER_TEXT#<br>#TEXT_INFORMATION#
<br>#META_KEYWORDS#<br>#META_DESCRIPTION#<br>#CATEGORIES_ID#<br>#STORE_NAME#<br>#BRE');
define('DB_TITLE_MODULE_METASEO_DEFAULT_PAGE_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_DEFAULT_PAGE_KEYWORDS','种类信息的关键字
<br>#CATEGORIES_NAME#<br>#SEO_NAME#<br>#SEO_DESCRIPTION#<br>#CATEGORIES_META_TEXT#<br>#CATEGORIES_HEADER_TEXT#<br>#CATEGORIES_FOOTER_TEXT#<br>#TEXT_INFORMATION#
<br>#META_KEYWORDS#<br>#META_DESCRIPTION#<br>#CATEGORIES_ID#<br>#STORE_NAME#<br>#BR');
define('DB_TITLE_MODULE_METASEO_DEFAULT_PAGE_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_DEFAULT_PAGE_DESCRIPTION','分类信息的说明
<br>#CATEGORIES_NAME#<br>#SEO_NAME#<br>#SEO_DESCRIPTION#<br>#CATEGORIES_META_TEXT#<br>#CATEGORIES_HEADER_TEXT#<br>#CATEGORIES_FOOTER_TEXT#<br>#TEXT_INFORMATION#
<br>#META_KEYWORDS#<br>#META_DESCRIPTION#<br>#CATEGORIES_ID#<br>#STORE_NAME#<br>#BREAD');
define('DB_TITLE_MODULE_METASEO_DEFAULT_PAGE_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_DEFAULT_PAGE_ROBOTS','分类信息的自动装置');
define('DB_TITLE_MODULE_METASEO_DEFAULT_PAGE_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_DEFAULT_PAGE_COPYRIGHT','分类信息的著作者');
define('DB_TITLE_MODULE_METASEO_A_LATEST_NEWS_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_A_LATEST_NEWS_ROBOTS','通知内容的自动装置');
define('DB_TITLE_MODULE_METASEO_A_LATEST_NEWS_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_A_LATEST_NEWS_COPYRIGHT','通知内容的作者');
define('DB_TITLE_BOX_NEW_PRODUCTS_DAY_LIMIT','前台：最新商品天数条件');
define('DB_DESCRIPTION_BOX_NEW_PRODUCTS_DAY_LIMIT','例：若设置为120天，则显示120天内追加的商品，设为0时可忽视条件。');
define('DB_TITLE_MODULE_METASEO_PRESENT_SUCCESS_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_SUCCESS_KEYWORDS','馈赠礼品募集的关键字<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRESENT_SUCCESS_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_SUCCESS_TITLE','馈赠礼品募集的标题<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRESENT_SUCCESS_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_SUCCESS_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_MODULE_METASEO_PRESENT_ORDER_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_ORDER_COPYRIGHT','馈赠礼品募集的著作者');
define('DB_TITLE_MODULE_METASEO_PRESENT_ORDER_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_ORDER_ROBOTS','馈赠礼品募集的自动装置');
define('DB_TITLE_MODULE_METASEO_PRESENT_ORDER_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_ORDER_DESCRIPTION','馈赠礼品募集的説明<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRESENT_ORDER_KEYWORDS','关键字');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_ORDER_KEYWORDS','馈赠礼品募集的关键字<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRESENT_ORDER_TITLE','标题');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_ORDER_TITLE','馈赠礼品募集的标题<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRESENT_ORDER_SORT_ORDER','显示排列顺序');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_ORDER_SORT_ORDER','可设置显示的排列顺序。小数字显示在上面.');
define('DB_TITLE_LIMIT_MIN_PRICE','前台：设置最低销售金额');
define('DB_DESCRIPTION_LIMIT_MIN_PRICE','总额在1日元到规定金额之间禁止销售，不限制商品单价，0也可以。');
define('DB_TITLE_LAST_CUSTOMER_ACTION','LAST CUSTOMER ACTION');
define('DB_DESCRIPTION_LAST_CUSTOMER_ACTION','LAST_CUSTOMER_ACTION');
define('DB_TITLE_HOST_NAME_LIGHT_KEYWORDS','后台：主机名的警告字符串');
define('DB_DESCRIPTION_HOST_NAME_LIGHT_KEYWORDS','主机名的警告字符串，ex：localhost|abc');
define('DB_TITLE_OS_LIGHT_KEYWORDS','后台：OS警告字符串');
define('DB_DESCRIPTION_OS_LIGHT_KEYWORDS','OS警告字符串，ex：mac|windows');
define('DB_TITLE_BROWSER_LIGHT_KEYWORDS','后台：服务器的种类警告字符串');
define('DB_DESCRIPTION_BROWSER_LIGHT_KEYWORDS','服务器种类警告字符串，ex：msie|moz');
define('DB_TITLE_HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS','后台：服务器的语言警告字符串');
define('DB_DESCRIPTION_HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS','服务器的语言警告字符串，ex：zh|cn');
define('DB_TITLE_SYSTEM_LANGUAGE_LIGHT_KEYWORDS','后台：电脑语言环境警告字符串');
define('DB_DESCRIPTION_SYSTEM_LANGUAGE_LIGHT_KEYWORDS','电脑语言环境警告字符串，ex：zh|cn');
define('DB_TITLE_USER_LANGUAGE_LIGHT_KEYWORDS','后台：用户语言环境警告字符串');
define('DB_DESCRIPTION_USER_LANGUAGE_LIGHT_KEYWORDS','用户语言环境警告字符串，ex：zh|cn');
define('DB_TITLE_SCREEN_RESOLUTION_LIGHT_KEYWORDS','后台：页面像素警告字符串');
define('DB_DESCRIPTION_SCREEN_RESOLUTION_LIGHT_KEYWORDS','页面像素警告字符串，ex：1280|800');
define('DB_TITLE_COLOR_DEPTH_LIGHT_KEYWORDS','后台：页面颜色警告字符串');
define('DB_DESCRIPTION_COLOR_DEPTH_LIGHT_KEYWORDS','页面颜色警告字符串，ex：2|3');
define('DB_TITLE_FLASH_LIGHT_KEYWORDS','后台：Flash警告字符串');
define('DB_DESCRIPTION_FLASH_LIGHT_KEYWORDS','Flash警告字符串，ex：Y|N');
define('DB_TITLE_FLASH_VERSION_LIGHT_KEYWORDS','后台：Flash的版本警告字符串');
define('DB_DESCRIPTION_FLASH_VERSION_LIGHT_KEYWORDS','Flash的版本警告字符串，ex：1|2');
define('DB_TITLE_DIRECTOR_LIGHT_KEYWORDS','后台：Director警告字符串');
define('DB_DESCRIPTION_DIRECTOR_LIGHT_KEYWORDS','Director警告字符串，ex：Y|N');
define('DB_TITLE_QUICK_TIME_LIGHT_KEYWORDS','后台：Quick time警告字符串');
define('DB_DESCRIPTION_QUICK_TIME_LIGHT_KEYWORDS','Quick time警告字符串，ex：Y|N');
define('DB_TITLE_REAL_PLAYER_LIGHT_KEYWORDS','后台：Real player警告字符串');
define('DB_DESCRIPTION_REAL_PLAYER_LIGHT_KEYWORDS','Real player警告字符串，ex：Y|N');
define('DB_TITLE_WINDOWS_MEDIA_LIGHT_KEYWORDS','后台：Windows media警告字符串');
define('DB_DESCRIPTION_WINDOWS_MEDIA_LIGHT_KEYWORDS','Windows media警告字符串，ex：Y|N');
define('DB_TITLE_PDF_LIGHT_KEYWORDS','后台：PDF警告字符串');
define('DB_DESCRIPTION_PDF_LIGHT_KEYWORDS','PDF警告字符串，ex：Y|N');
define('DB_TITLE_JAVA_LIGHT_KEYWORDS','后台：JAVA警告字符串');
define('DB_DESCRIPTION_JAVA_LIGHT_KEYWORDS','JAVA警告字符串，ex：Y|N');
define('DB_TITLE_DEFAULT_PAGE_TOP_CONTENTS','前台：首页的页脚内容');
define('DB_DESCRIPTION_DEFAULT_PAGE_TOP_CONTENTS',' 首页的页脚内容');
define('DB_TITLE_STORE_NAME','前台・后台：店名');
define('DB_DESCRIPTION_STORE_NAME','设置店名.');
define('DB_TITLE_SESSION_FORCE_COOKIE_USE','前台：Force Cookie Use');
define('DB_DESCRIPTION_SESSION_FORCE_COOKIE_USE','Force the use of sessions when cookies are only enabled.');
define('DB_TITLE_CART_TAG_PRODUCTS_MAX','前台：忘记购买提示的最大显示数');
define('DB_DESCRIPTION_CART_TAG_PRODUCTS_MAX','忘记购买提示的最大显示数');
define('DB_TITLE_TELNO_KEYWORDS','后台：黑名单');
define('DB_DESCRIPTION_TELNO_KEYWORDS','黑名单');
define('DB_TITLE_MODULE_METASEO_PRESENT_SUCCESS_DESCRIPTION','说明');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_SUCCESS_DESCRIPTION','馈赠礼品募集的说明<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#');
define('DB_TITLE_MODULE_METASEO_PRESENT_SUCCESS_ROBOTS','自动装置');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_SUCCESS_ROBOTS','馈赠礼品募集的自动装置');
define('DB_TITLE_MODULE_METASEO_PRESENT_SUCCESS_COPYRIGHT','作者');
define('DB_DESCRIPTION_MODULE_METASEO_PRESENT_SUCCESS_COPYRIGHT','馈赠礼品募集的著作者');
define('DB_TITLE_WE_ARE_ALIVE','WE_ARE_ALIVE');
define('DB_DESCRIPTION_WE_ARE_ALIVE','We are alive');
define('DB_TITLE_MODULE_PAYMENT_GUIDANCE_PREORDER_SHOW','预约订单');
define('DB_DESCRIPTION_MODULE_PAYMENT_GUIDANCE_PREORDER_SHOW','显示预约订单中的webmoney及game之间的移动');
define('DB_TITLE_MODULE_PAYMENT_GUIDANCE_MAILSTRING','订单完成的邮件模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_GUIDANCE_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}');
define('DB_TITLE_ACTIVE_ACCOUNT_EMAIL_TITLE','前台：邮件认证的标题（会员）');
define('DB_DESCRIPTION_ACTIVE_ACCOUNT_EMAIL_TITLE','邮件认证的标题（会员）<br> 网站名：${SITE_NAME}');
define('DB_TITLE_ACCOUNT_GENDER','前台：性别');
define('DB_DESCRIPTION_ACCOUNT_GENDER','设置顾客账号里是否显示性别.');
define('DB_TITLE_IDPW_PASSWORD_LENGTH','IDPW_PASSWORD_LENGTH');
define('DB_DESCRIPTION_IDPW_PASSWORD_LENGTH','');
define('DB_TITLE_IDPW_PASSWORD_ITEM','IDPW_PASSWORD_ITEM');
define('DB_DESCRIPTION_IDPW_PASSWORD_ITEM','');
define('DB_DESCRIPTION_MODULE_PAYMENT_PAYPAL_STATUS','是否受理通过PAYPAL 进行的支付?');
define('DB_TITLE_MODULE_PAYMENT_PAYPAL_SORT_ORDER','显示的排列顺序');
define('DB_DESCRIPTION_MODULE_PAYMENT_PAYPAL_SORT_ORDER','可设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID','初始订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID','设置的状态在接受订货时应用.');
define('DB_TITLE_MODULE_PAYMENT_PAYPAL_CONNECTION_URL','连接URL');
define('DB_DESCRIPTION_MODULE_PAYMENT_PAYPAL_CONNECTION_URL','接受telecom credit申请的页面链接的设置。');
define('DB_TITLE_MODULE_PAYMENT_PAYPAL_KID','节目编排');
define('DB_DESCRIPTION_MODULE_PAYMENT_PAYPAL_KID','节目编排的设置。');
define('DB_TITLE_MODULE_PAYMENT_PAYPAL_COST','结算手续费');
define('DB_DESCRIPTION_MODULE_PAYMENT_PAYPAL_COST','结算手续费
例:
代金300円以下、需支付30日元的手续费　300:*0+30,
价格在301〜1000日元以内、需支付价格的2％的手续费　999:*0.02,
价格在1000日元以上、免手续费　99999999:*0,
为了避免使用无限大符号、请使用本网站不可能存在的数值。
300:*0+30中如果是*0，手续费变为300+30，请谨慎处理。');
define('DB_TITLE_MODULE_PAYMENT_PAYPAL_MONEY_LIMIT','可结算金额');
define('DB_DESCRIPTION_MODULE_PAYMENT_PAYPAL_MONEY_LIMIT','可结算金额的最大值和最小值的设置
例：0,3000
如果输入0,3000日元、那可结算金额为0到3000日元之间。设置范围以外的不可以进行结算。');
define('DB_TITLE_MODULE_PAYMENT_PAYPAL_STATUS','启用PAYPAL 支付');
define('DB_TITLE_IDPW_START_URL','后台：重新定向域名');
define('DB_DESCRIPTION_IDPW_START_URL','');
define('DB_TITLE_MODULE_PAYMENT_FETCH_GOOD_STATUS','启用购买商品时所用支付方式');
define('DB_DESCRIPTION_MODULE_PAYMENT_FETCH_GOOD_STATUS','是否受理通过银行转账进行的支付?');
define('DB_TITLE_MODULE_PAYMENT_FETCH_GOOD_SORT_ORDER','显示的排列顺序');
define('DB_DESCRIPTION_MODULE_PAYMENT_FETCH_GOOD_SORT_ORDER','可设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MODULE_PAYMENT_FETCH_GOOD_ORDER_STATUS_ID','初始订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_FETCH_GOOD_ORDER_STATUS_ID','设置的状态在接受订货时应用.');
define('DB_TITLE_BEST_SELLERS_LIMIT_TIME','BEST_SELLERS_LIMIT_TIME');
define('DB_DESCRIPTION_BEST_SELLERS_LIMIT_TIME','');
define('DB_TITLE_MODULE_PAYMENT_FREE_PAYMENT_SORT_ORDER','显示的排列顺序');
define('DB_DESCRIPTION_MODULE_PAYMENT_FREE_PAYMENT_SORT_ORDER','可设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MODULE_PAYMENT_FREE_PAYMENT_STATUS','启用购买商品时所用支付方式');
define('DB_DESCRIPTION_MODULE_PAYMENT_FREE_PAYMENT_STATUS','是否受理通过银行转账进行的支付?');
define('DB_TITLE_MAX_DISPLAY_CUSTOMER_MAIL_RESULTS','后台：给顾客发送 E-Mail');
define('DB_DESCRIPTION_MAX_DISPLAY_CUSTOMER_MAIL_RESULTS','在搜索结果页、设置每页显示的顾客数的最大值。');
define('DB_TITLE_MODULE_PAYMENT_FETCH_GOOD_MONEY_LIMIT',' 可结算金额');
define('DB_DESCRIPTION_MODULE_PAYMENT_FETCH_GOOD_MONEY_LIMIT','可结算金额的最大值和最小值的设置 例：0,3000 如果输入0,3000日元、那可结算金额为0到3000日元之间。设置范围以外的不可以进行结算。');
define('DB_TITLE_MODULE_PAYMENT_FREE_PAYMENT_ORDER_STATUS_ID',' 初始订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_FREE_PAYMENT_ORDER_STATUS_ID','设置的状态在接受订货时应用.');
define('DB_TITLE_MODULE_PAYMENT_FREE_PAYMENT_MONEY_LIMIT',' 可结算金额');
define('DB_DESCRIPTION_MODULE_PAYMENT_FREE_PAYMENT_MONEY_LIMIT','可结算金额的最大值和最小值的设置 例：0,3000 如果输入0,3000日元、那可结算金额为0到3000日元之间。设置范围以外的不可以进行结算。');
define('DB_TITLE_MODULE_PAYMENT_RAKUTEN_BANK_MAILSTRING','订单完成的邮件模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_RAKUTEN_BANK_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}');
define('DB_TITLE_MODULE_PAYMENT_BUYING_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYING_LIMIT_SHOW','显示设置');
define('DB_TITLE_MODULE_PAYMENT_RAKUTEN_BANK_PREORDER_SHOW','预约订单');
define('DB_DESCRIPTION_MODULE_PAYMENT_RAKUTEN_BANK_PREORDER_SHOW','显示预约订单中的乐天银行');
define('DB_TITLE_MODULE_PAYMENT_RAKUTEN_BANK_MONEY_LIMIT','可结算金额');
define('DB_DESCRIPTION_MODULE_PAYMENT_RAKUTEN_BANK_MONEY_LIMIT','可结算金额的最大值和最小值的设置
      例：0,3000
      如果输入0,3000日元、那可结算金额为0到3000日元之间。设置范围以外的不可以进行结算。');
define('DB_TITLE_MODULE_PAYMENT_RAKUTEN_BANK_ORDER_STATUS_ID','初始订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_RAKUTEN_BANK_ORDER_STATUS_ID','设置的状态在接受订货时应用.');
define('DB_TITLE_MODULE_PAYMENT_RAKUTEN_BANK_ZONE','应用地区');
define('DB_DESCRIPTION_MODULE_PAYMENT_RAKUTEN_BANK_ZONE','选择应用地区后、就只可使用所选择的地区.');
define('DB_TITLE_MODULE_PAYMENT_RAKUTEN_BANK_SORT_ORDER','显示的排列顺序');
define('DB_DESCRIPTION_MODULE_PAYMENT_RAKUTEN_BANK_SORT_ORDER','可设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MODULE_PAYMENT_RAKUTEN_BANK_COST','结算手续费');
define('DB_DESCRIPTION_MODULE_PAYMENT_RAKUTEN_BANK_COST','结算手续费 例: 价格在300日元以下、需支付30日元的手续费　300:*0+30, 价格在301～1000日元以内、需支付价格的2％的手续费　999:*0.02, 价格在1000日元以上、免手续费　99999999:*0, 为了避免使用无限大符号、请使用本网站不可能存在的数值。 300:*0+30中如果是*0，手续费变为300+30，请谨慎处理。');
define('DB_TITLE_MODULE_PAYMENT_RAKUTEN_BANK_URL','连接URL');
define('DB_DESCRIPTION_MODULE_PAYMENT_RAKUTEN_BANK_URL','连接URL的设置。');
define('DB_TITLE_MODULE_PAYMENT_RAKUTEN_BANK_STATUS','启用乐天银行');
define('DB_DESCRIPTION_MODULE_PAYMENT_RAKUTEN_BANK_STATUS','是否受理通过乐天银行进行的支付?');
define('DB_TITLE_MODULE_PAYMENT_RAKUTEN_BANK_IP','加盟店代码');
define('DB_DESCRIPTION_MODULE_PAYMENT_RAKUTEN_BANK_IP','加盟店代码的设置。');
define('DB_TITLE_MODULE_PAYMENT_RAKUTEN_BANK_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_PAYMENT_RAKUTEN_BANK_LIMIT_SHOW','显示设置');
define('DB_TITLE_MAX_RANDOM_SELECT_NEW','前台：新到商品的随机选择数');
define('DB_DESCRIPTION_MAX_RANDOM_SELECT_NEW','为了随机选取新到商品设置被选择的最高纪录.');
define('DB_TITLE_MODULE_PAYMENT_MONEYORDER_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_PAYMENT_MONEYORDER_LIMIT_SHOW','显示设置');
define('DB_TITLE_MODULE_PAYMENT_POSTALMONEYORDER_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_PAYMENT_POSTALMONEYORDER_LIMIT_SHOW','显示设置');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_LIMIT_SHOW','显示设置');
define('DB_TITLE_MODULE_PAYMENT_TELECOM_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_PAYMENT_TELECOM_LIMIT_SHOW','显示设置');
define('DB_TITLE_MODULE_PAYMENT_PAYPAL_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_PAYMENT_PAYPAL_LIMIT_SHOW','显示设置');
define('DB_TITLE_MODULE_PAYMENT_FETCH_GOOD_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_PAYMENT_FETCH_GOOD_LIMIT_SHOW','显示设置');
define('DB_TITLE_MODULE_PAYMENT_FREE_PAYMENT_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_PAYMENT_FREE_PAYMENT_LIMIT_SHOW','显示设置');
define('DB_TITLE_MAX_DISPLAY_PW_MANAGER_RESULTS','后台：ID管理显示数');
define('DB_DESCRIPTION_MAX_DISPLAY_PW_MANAGER_RESULTS','设置ID管理每页的最大显示数。');
define('DB_TITLE_MODULE_PAYMENT_FETCH_GOOD_PREORDER_SHOW','预约订单');
define('DB_DESCRIPTION_MODULE_PAYMENT_FETCH_GOOD_PREORDER_SHOW','显示预约订单中的来店');
define('DB_TITLE_MODULE_PAYMENT_BUYINGPOINT_PREORDER_SHOW','预约订单');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYINGPOINT_PREORDER_SHOW','显示预约订单中的点数(购买)');
define('DB_TITLE_MODULE_PAYMENT_BUYING_PREORDER_SHOW','预约订单');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYING_PREORDER_SHOW','显示预约订单中的银行转账(购买)');
define('DB_TITLE_MODULE_SHIPPING_FLAT_STATUS','启用乐天银行');
define('DB_DESCRIPTION_MODULE_SHIPPING_FLAT_STATUS','是否受理通过乐天银行进行的支付?');
define('DB_TITLE_MODULE_SHIPPING_FLAT_COST','标准费用');
define('DB_DESCRIPTION_MODULE_SHIPPING_FLAT_COST','所订购商品的标准运费多少合适呢?');
define('DB_TITLE_MODULE_SHIPPING_FLAT_SORT_ORDER','显示的排列顺序');
define('DB_DESCRIPTION_MODULE_SHIPPING_FLAT_SORT_ORDER','可设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MODULE_SHIPPING_FLAT__TAX_CLASS','税种');
define('DB_DESCRIPTION_MODULE_SHIPPING_FLAT__TAX_CLASS','请选择适用于标准费用的税种.');
define('DB_TITLE_MODULE_SHIPPING_FLAT_ZONE','应用地区');
define('DB_DESCRIPTION_MODULE_SHIPPING_FLAT_ZONE','选择应用地区后、就只可使用所选择的地区.');
define('DB_TITLE_MODULE_SHIPPING_FLAT_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_SHIPPING_FLAT_LIMIT_SHOW','显示设置');
define('DB_TITLE_MODULE_SHIPPING_FLAT_PREORDER_SHOW','预约订单');
define('DB_DESCRIPTION_MODULE_SHIPPING_FLAT_PREORDER_SHOW','显示预约订单中的乐天银行');
define('DB_TITLE_MODULE_PAYMENT_RAKUTEN_BANK_PRINT_MAILSTRING','打印邮件的模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_RAKUTEN_BANK_PRINT_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}</br>用户信息:${CUSTOMER_INFO}</br>信用调查:${CREDIT_RESEARCH}</br>订单历史记录:${ORDER_HISTORY}');
define('DB_TITLE_ACTIVE_ACCOUNT_EMAIL_CONTENT','前台：邮件认证的内容（会员）');
define('DB_DESCRIPTION_ACTIVE_ACCOUNT_EMAIL_CONTENT','邮件确认用的链接:${URL}<br> 姓名：${NAME}<br> 网站名：${SITE_NAME}<br> 网址：${SITE_URL}');
define('DB_TITLE_GUEST_LOGIN_EMAIL_TITLE','前台：邮件认证的标题（非会员）');
define('DB_DESCRIPTION_GUEST_LOGIN_EMAIL_TITLE','邮件认证的标题（非会员）<br> 网站名：${SITE_NAME}');
define('DB_TITLE_GUEST_LOGIN_EMAIL_CONTENT','前台：邮件认证的内容（非会员）');
define('DB_DESCRIPTION_GUEST_LOGIN_EMAIL_CONTENT','邮件认证的内容（非会员）<br> 姓名：${NAME}<br> 网站名：${SITE_NAME}<br> 网址：${SITE_URL}');
define('DB_TITLE_ACTIVE_EDIT_ACCOUNT_EMAIL_TITLE','前台：邮件认证的标题（会员编辑）');
define('DB_DESCRIPTION_ACTIVE_EDIT_ACCOUNT_EMAIL_TITLE','邮件认证的标题（会员编辑）<br> 网站名：${SITE_NAME}');
define('DB_TITLE_ACTIVE_EDIT_ACCOUNT_EMAIL_CONTENT','前台：邮件认证的内容（会员编辑）');
define('DB_DESCRIPTION_ACTIVE_EDIT_ACCOUNT_EMAIL_CONTENT','邮件确认用的链接:${URL}<br> 姓名：${NAME}<br> 网站名：${SITE_NAME}<br> 网址：${SITE_URL}<br>');
define('DB_TITLE_MAX_DISPLAY_FAQ_ADMIN','后台：FAQ显示的最大值');
define('DB_DESCRIPTION_MAX_DISPLAY_FAQ_ADMIN','');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_PREORDER_SHOW','预约订单');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_PREORDER_SHOW','显示预约订单中的便利店结算');
define('DB_TITLE_MODULE_PAYMENT_MONEYORDER_PREORDER_SHOW','预约订单');
define('DB_DESCRIPTION_MODULE_PAYMENT_MONEYORDER_PREORDER_SHOW','显示预约订单中的银行转账');
define('DB_TITLE_ORDER_INFO_BASIC_TEXT','后台：顾客名');
define('DB_DESCRIPTION_ORDER_INFO_BASIC_TEXT','顾客名<br><br><font color=\"#ff0000\"><b>由于显示器的大小不同、所有的项目不能完全显示。要显示所有项目请设置适合的显示器大小。</b></font>');
define('DB_TITLE_PREORDER_MAIL_ACTIVE_SUBJECT','前台：邮件认证的标题（预约订单)');
define('DB_DESCRIPTION_PREORDER_MAIL_ACTIVE_SUBJECT','邮件认证的标题（预约订单)<br> 网站名：${SITE_NAME}');
define('DB_TITLE_PREORDER_MAIL_ACTIVE_CONTENT','前台：邮件认证的内容（预约订单）');
define('DB_DESCRIPTION_PREORDER_MAIL_ACTIVE_CONTENT','邮件认证的内容（预约订单）<br> 姓名：${NAME}<br> 网站名：${SITE_NAME}<br> 网址：${SITE_URL}<br>');
define('DB_TITLE_PREORDER_MAIL_CONTENT','前台：预约完成的邮件内容');
define('DB_DESCRIPTION_PREORDER_MAIL_CONTENT','预约完成的邮件内容<br> 商品名:${PRODUCTS_NAME}<br> 期望数:${PRODUCTS_QUANTITY}<br> 期限:${EFFECTIVE_TIME}<br> 支付方式：${PAY}<br>姓名：${NAME}<br> 网站名：${SITE_NAME}<br> 网址：${SITE_URL}<br> 预约号码：${PREORDER_N}<br>备注：${ORDER_COMMENT}');
define('DB_TITLE_DEFAULT_PREORDERS_STATUS_ID','Default PreOrder Status For New Orders');
define('DB_DESCRIPTION_DEFAULT_PREORDERS_STATUS_ID','When a new order is created, this order status will be assigned to it.');
define('DB_TITLE_PREORDER_MAIL_SUBJECT','前台：预约完成的邮件标题');
define('DB_DESCRIPTION_PREORDER_MAIL_SUBJECT','预约完成的邮件标题<br> 网站名：${SITE_NAME}');
define('DB_TITLE_ORDER_INFO_ORDER_COMMENT','后台：Order Comment');
define('DB_DESCRIPTION_ORDER_INFO_ORDER_COMMENT','Order Comment<br><br><font color=\"#ff0000\"><b>由于显示器的大小不同、所有的项目不能完全显示。要显示所有项目请设置适合的显示器大小。</b></font>');
define('DB_TITLE_ORDER_INFO_PRODUCT_LIST','后台：数量/商品名');
define('DB_DESCRIPTION_ORDER_INFO_PRODUCT_LIST','数量/商品名<br><br><font color=\"#ff0000\"><b>由于显示器的大小不同、所有的项目不能完全显示。要显示所有项目请设置适合的显示器大小。</b></font>');
define('DB_TITLE_ORDER_INFO_REPUTAION_SEARCH','后台：信用调查');
define('DB_DESCRIPTION_ORDER_INFO_REPUTAION_SEARCH','信用调查<br><br><font color=\"#ff0000\"><b>由于显示器的大小不同、所有的项目不能完全显示。要显示所有项目请设置适合的显示器大小。</b></font>');
define('DB_TITLE_ORDER_INFO_ORDER_HISTORY','后台：Order History');
define('DB_DESCRIPTION_ORDER_INFO_ORDER_HISTORY','Order History<br><br><font color=\"#ff0000\"><b>由于显示器的大小不同、所有的项目不能完全显示。要显示所有项目请设置适合的显示器大小。</b></font>');
define('DB_TITLE_ORDER_INFO_REFERER_INFO','后台：Referer Info');
define('DB_DESCRIPTION_ORDER_INFO_REFERER_INFO','Referer Info<br><br><font color=\"#ff0000\"><b>由于显示器的大小不同、所有的项目不能完全显示。要显示所有项目请设置适合的显示器大小。</b></font>');
define('DB_TITLE_ORDER_INFO_CUSTOMER_INFO','后台：Customer Info');
define('DB_DESCRIPTION_ORDER_INFO_CUSTOMER_INFO','Customer Info<br><br><font color=\"#ff0000\"><b>由于显示器的大小不同、所有的项目不能完全显示。要显示所有项目请设置适合的显示器大小。</b></font>');
define('DB_TITLE_ORDER_INFO_ORDER_INFO','后台：Order Info');
define('DB_DESCRIPTION_ORDER_INFO_ORDER_INFO','Order Info<br><br><font color=\"#ff0000\"><b>由于显示器的大小不同、所有的项目不能完全显示。要显示所有项目请设置适合的显示器大小。</b></font>');
define('DB_TITLE_ORDER_INFO_INPUT_FINISH','后台：输入完毕');
define('DB_DESCRIPTION_ORDER_INFO_INPUT_FINISH','输入完毕<br><br><font color=\"#ff0000\"><b>由于显示器的大小不同、所有的项目不能完全显示。要显示所有项目请设置适合的显示器大小。</b></font>');
define('DB_TITLE_ORDER_INFO_TRANS_WAIT','后台：交易等待');
define('DB_DESCRIPTION_ORDER_INFO_TRANS_WAIT','交易等待<br><br><font color=\"#ff0000\"><b>由于显示器的大小不同、所有的项目不能完全显示。要显示所有项目请设置适合的显示器大小。</b></font>');
define('DB_TITLE_ORDER_INFO_TRANS_NOTICE','后台：注意处理方式');
define('DB_DESCRIPTION_ORDER_INFO_TRANS_NOTICE','注意处理方式<br><br><font color=\"#ff0000\"><b>由于显示器的大小不同、所有的项目不能完全显示。要显示所有项目请设置适合的显示器大小。</b></font>');
define('DB_TITLE_MODULE_PAYMENT_TELECOM_PREORDER_SHOW','预约订单');
define('DB_DESCRIPTION_MODULE_PAYMENT_TELECOM_PREORDER_SHOW','显示预约订单中的信用卡结算');
define('DB_TITLE_MODULE_PAYMENT_PAYPAL_PREORDER_SHOW','预约订单');
define('DB_DESCRIPTION_MODULE_PAYMENT_PAYPAL_PREORDER_SHOW','显示预约订单中的PayPal结算');
define('DB_TITLE_MODULE_PAYMENT_BUYING_PRINT_MAILSTRING','打印邮件的模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYING_PRINT_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}</br>用户信息:${CUSTOMER_INFO}</br>信用调查:${CREDIT_RESEARCH}</br>订单历史记录:${ORDER_HISTORY}');
define('DB_TITLE_MODULE_PAYMENT_B2_COST','结算手续费');
define('DB_DESCRIPTION_MODULE_PAYMENT_B2_COST','▼订单号码：${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品：${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>');
define('DB_TITLE_MODULE_PAYMENT_B2_STATUS','启用购买商品时所用支付方式');
define('DB_DESCRIPTION_MODULE_PAYMENT_B2_STATUS','▼订单号码：${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品：${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>');
define('DB_TITLE_MODULE_PAYMENT_B2_MONEY_LIMIT','可结算金额');
define('DB_DESCRIPTION_MODULE_PAYMENT_B2_MONEY_LIMIT','▼订单号码：${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品：${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>');
define('DB_TITLE_MODULE_PAYMENT_B2_ZONE','应用地区');
define('DB_DESCRIPTION_MODULE_PAYMENT_B2_ZONE','▼订单号码：${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品：${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>');
define('DB_TITLE_MODULE_PAYMENT_B2_ORDER_STATUS_ID','初始订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_B2_ORDER_STATUS_ID','▼订单号码：${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品：${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>');
define('DB_TITLE_MODULE_PAYMENT_B2_SORT_ORDER','显示的排列顺序');
define('DB_DESCRIPTION_MODULE_PAYMENT_B2_SORT_ORDER','▼订单号码：${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品：${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>');
define('DB_TITLE_MODULE_PAYMENT_B2_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_PAYMENT_B2_LIMIT_SHOW','▼订单号码：${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品：${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>');
define('DB_TITLE_LAST_ORDER_ID_END_NUM','last_orders_num');
define('DB_DESCRIPTION_LAST_ORDER_ID_END_NUM','');
define('DB_TITLE_PREORDER_LAST_CUSTOMER_ACTION','PREORDER_LAST_CUSTOMER_ACTION');
define('DB_DESCRIPTION_PREORDER_LAST_CUSTOMER_ACTION','PREORDER_LAST_CUSTOMER_ACTION');
define('DB_TITLE_MODULE_PAYMENT_BUYINGPOINT_MONEY_LIMIT','可结算金额');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYINGPOINT_MONEY_LIMIT','可结算金额的最大值和最小值的设置 例：0,3000 如果输入0,3000日元、那可结算金额为0到3000日元之间。设置范围以外的不可以进行结算。');
define('DB_TITLE_MODULE_PAYMENT_GUIDANCE_STATUS','启用webmoney及game之间的移动');
define('DB_DESCRIPTION_MODULE_PAYMENT_GUIDANCE_STATUS','');
define('DB_TITLE_MODULE_PAYMENT_BUYINGPOINT_MAILSTRING','订单完成的邮件模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYINGPOINT_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}');
define('DB_TITLE_MODULE_PAYMENT_BUYINGPOINT_ORDER_STATUS_ID','初始订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYINGPOINT_ORDER_STATUS_ID','设置的状态在接受订货时应用.');
define('DB_TITLE_MODULE_PAYMENT_BUYINGPOINT_STATUS','启用购买商品时所用支付方式');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYINGPOINT_STATUS','是否受理通过银行转账进行的支付?');
define('DB_TITLE_MODULE_PAYMENT_BUYINGPOINT_SORT_ORDER','显示的排列顺序');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYINGPOINT_SORT_ORDER','可设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MODULE_PAYMENT_MONEYORDER_PRINT_MAILSTRING','打印邮件的模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_MONEYORDER_PRINT_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}</br>用户信息:${CUSTOMER_INFO}</br>信用调查:${CREDIT_RESEARCH}</br>订单历史记录:${ORDER_HISTORY}');
define('DB_TITLE_MODULE_PAYMENT_MONEYORDER_MAILSTRING','订单完成的邮件模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_MONEYORDER_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}');
define('DB_TITLE_MODULE_PAYMENT_GUIDANCE_COST','结算手续费');
define('DB_DESCRIPTION_MODULE_PAYMENT_GUIDANCE_COST','结算手续费 例: 价格在300日元以下、需支付30日元的手续费　300:*0+30, 价格在301～1000日元以内、需支付价格的2％的手续费　999:*0.02, 价格在1000日元以上、免手续费　99999999:*0, 为了避免使用无限大符号、请使用本网站不可能存在的数值。 300:*0+30中如果是*0，手续费变为300+30，请谨慎处理。');
define('DB_TITLE_MODULE_PAYMENT_TELECOM_MAILSTRING','订单完成的邮件模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_TELECOM_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}');
define('DB_TITLE_MODULE_PAYMENT_BUYING_MAILSTRING','订单完成的邮件模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYING_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}');
define('DB_TITLE_MODULE_PAYMENT_BUYINGPOINT_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYINGPOINT_LIMIT_SHOW','显示设置');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_MAILSTRING','订单完成的邮件模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}');
define('DB_TITLE_MODULE_PAYMENT_FETCH_GOOD_MAILSTRING','订单完成的邮件模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_FETCH_GOOD_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}');
define('DB_TITLE_MODULE_PAYMENT_FREE_PAYMENT_MAILSTRING','订单完成的邮件模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_FREE_PAYMENT_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}');
define('DB_TITLE_MODULE_PAYMENT_PAYPAL_MAILSTRING','订单完成的邮件模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_PAYPAL_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}');
define('DB_TITLE_MODULE_PAYMENT_POSTALMONEYORDER_MAILSTRING','订单完成的邮件模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_POSTALMONEYORDER_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}');
define('DB_TITLE_MODULE_PAYMENT_FETCH_GOOD_PRINT_MAILSTRING','打印邮件的模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_FETCH_GOOD_PRINT_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}</br>用户信息:${CUSTOMER_INFO}</br>信用调查:${CREDIT_RESEARCH}</br>订单历史记录:${ORDER_HISTORY}');
define('DB_TITLE_MODULE_PAYMENT_GUIDANCE_ZONE','应用地区');
define('DB_DESCRIPTION_MODULE_PAYMENT_GUIDANCE_ZONE','选择应用地区后、就只可使用所选择的地区.');
define('DB_TITLE_MODULE_PAYMENT_POSTALMONEYORDER_PREORDER_SHOW','预约订单');
define('DB_DESCRIPTION_MODULE_PAYMENT_POSTALMONEYORDER_PREORDER_SHOW','显示预约订单中的邮政银行（邮局）');
define('DB_TITLE_MODULE_PAYMENT_GUIDANCE_MONEY_LIMIT','可结算金额');
define('DB_DESCRIPTION_MODULE_PAYMENT_GUIDANCE_MONEY_LIMIT','可结算金额的最大值和最小值的设置 例：0,3000 如果输入0,3000日元、那可结算金额为0到3000日元之间。设置范围以外的不可以进行结算。');
define('DB_TITLE_MODULE_PAYMENT_GUIDANCE_ORDER_STATUS_ID','初始订单状态');
define('DB_DESCRIPTION_MODULE_PAYMENT_GUIDANCE_ORDER_STATUS_ID','设置的状态在接受订货时应用.');
define('DB_TITLE_MODULE_PAYMENT_PAYPAL_PRINT_MAILSTRING','打印邮件的模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_PAYPAL_PRINT_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}</br>用户信息:${CUSTOMER_INFO}</br>信用调查:${CREDIT_RESEARCH}</br>订单历史记录:${ORDER_HISTORY}');
define('DB_TITLE_MODULE_PAYMENT_GUIDANCE_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_PAYMENT_GUIDANCE_LIMIT_SHOW','显示设置');
define('DB_TITLE_MODULE_PAYMENT_FREE_PAYMENT_PRINT_MAILSTRING','打印邮件的模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_FREE_PAYMENT_PRINT_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}</br>用户信息:${CUSTOMER_INFO}</br>信用调查:${CREDIT_RESEARCH}</br>订单历史记录:${ORDER_HISTORY}');
define('DB_TITLE_MODULE_PAYMENT_BUYINGPOINT_PRINT_MAILSTRING','打印邮件的模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_BUYINGPOINT_PRINT_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}</br>用户信息:${CUSTOMER_INFO}</br>信用调查:${CREDIT_RESEARCH}</br>订单历史记录:${ORDER_HISTORY}');
define('DB_TITLE_MODULE_PAYMENT_POSTALMONEYORDER_PRINT_MAILSTRING','打印邮件的模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_POSTALMONEYORDER_PRINT_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}</br>用户信息:${CUSTOMER_INFO}</br>信用调查:${CREDIT_RESEARCH}</br>订单历史记录:${ORDER_HISTORY}');
define('DB_TITLE_PICTURE_BAR_IS_SHOW','是否启用混合图表');
define('DB_DESCRIPTION_PICTURE_BAR_IS_SHOW','是否启用混合图表');
define('DB_TITLE_','Installed Modules');
define('DB_DESCRIPTION_','This is automatically updated. No need to edit.');
define('DB_TITLE_MODULE_ORDERTOTAL_INSTALLED','Installed Modules');
define('DB_DESCRIPTION_MODULE_ORDERTOTAL_INSTALLED','This is automatically updated. No need to edit.');
define('DB_TITLE_PICTURE_BAR_COLOR_LARGE','混合图表的设置(>=7)');
define('DB_DESCRIPTION_PICTURE_BAR_COLOR_LARGE','混合图表的设置(>=7)');
define('DB_TITLE_PICTURE_BAR_COLOR_SMALL','混合图表的设置(<=3)');
define('DB_DESCRIPTION_PICTURE_BAR_COLOR_SMALL','混合图表的设置(<=3)');
define('DB_TITLE_PICTURE_BAR_COLOR_MIDDLE','混合图表的设置(4-6)');
define('DB_DESCRIPTION_PICTURE_BAR_COLOR_MIDDLE','混合图表的设置(4-6)');
define('DB_TITLE_MODULE_ORDER_TOTAL_SHIPPING_STATUS','显示运费');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_SHIPPING_STATUS','显示运费吗?');
define('DB_TITLE_MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER','显示的排列顺序');
define('DB_DESCRIPTION_MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER','可设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MODULE_PAYMENT_TELECOM_PRINT_MAILSTRING','打印邮件的模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_TELECOM_PRINT_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}</br>用户信息:${CUSTOMER_INFO}</br>信用调查:${CREDIT_RESEARCH}</br>订单历史记录:${ORDER_HISTORY}');
define('DB_TITLE_MODULE_PAYMENT_CONVENIENCE_STORE_PRINT_MAILSTRING','打印邮件的模板');
define('DB_DESCRIPTION_MODULE_PAYMENT_CONVENIENCE_STORE_PRINT_MAILSTRING','▼订单号码-${ORDER_ID}</br>▼订购日期：${ORDER_DATE}</br>▼姓名：${USER_NAME}</br>▼邮箱地址：${USER_MAILACCOUNT}</br>▼支付金额：${ORDER_TOTAL}</br>▼支付方式：${ORDER_PAYMENT}</br>▼交易时间：${ORDER_TTIME}</br>▼备注：${ORDER_COMMENT}</br>订购商品${ORDER_PRODUCTS}</br>交易方法：${ORDER_TMETHOD}</br>网站名：${SITE_NAME}</br>店铺邮箱地址：${SITE_MAIL}</br>店铺URL：${SITE_URL}</br>点数：${POINT}</br>金额机构名：${BANK_NAME}</br>分店名：${BANK_SHITEN}</br>账户类型：${BANK_KAMOKU}</br>账号：${BANK_KOUZA_NUM}</br>账户持有人：${BANK_KOUZA_NAME}</br>追加信息：${ADD_INFO}</br>用户信息:${CUSTOMER_INFO}</br>信用调查:${CREDIT_RESEARCH}</br>订单历史记录:${ORDER_HISTORY}');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING2_STATUS','启用SHIPPING2222222222222');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING2_STATUS','是否受理通过SHIPPING进行的支付?');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING_WORK_TIME','work');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING_WORK_TIME','09:00-20:30');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING2_SLEEP_TIME','SLEEP');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING2_SLEEP_TIME','SLEEP');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING2_WORK_TIME','work');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING2_WORK_TIME','09:00-20:30');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING2_DB_SET_DAY','DB SET DAY222');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING2_DB_SET_DAY','DB_SET_DAY');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING2_PREORDER_SHOW','预约订单');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING2_PREORDER_SHOW','显示预约订单中的乐天银行');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING2_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING2_LIMIT_SHOW','显示设置');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING2_ZONE','应用地区');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING2_ZONE','选择应用地区后、就只可使用所选择的地区.');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING2__TAX_CLASS','税种');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING2__TAX_CLASS','请选择适用于标准费用的税种
.');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING2_SORT_ORDER','显示的排列顺序');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING2_SORT_ORDER','可设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING2_COST','标准费用');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING2_COST','所订购商品的标准运费多少合适呢?');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING_SLEEP_TIME','SLEEP');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING_SLEEP_TIME','SLEEP');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING_DB_SET_DAY','DB SET DAY');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING_DB_SET_DAY','DB_SET_DAY');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING_SORT_ORDER','显示的排列顺序');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING_SORT_ORDER','可设置显示的排列顺序. 小数字显示在上面.');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING_STATUS','启用SHIPPING');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING_STATUS','是否受理通过SHIPPING进行的支付?');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING_PREORDER_SHOW','预约订单');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING_PREORDER_SHOW','显示预约订单中的乐天银行');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING_ZONE','应用地区');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING_ZONE','选择应用地区后、就只可使用所选择的地区.');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING__TAX_CLASS','税种');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING__TAX_CLASS','请选择适用于标准费用的税种
.');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING_COST','标准费用');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING_COST','所订购商品的标准运费多少合适呢?');
define('DB_TITLE_MODULE_SHIPPING_TESTSHIPPING_LIMIT_SHOW','显示设置');
define('DB_DESCRIPTION_MODULE_SHIPPING_TESTSHIPPING_LIMIT_SHOW','显示设置');
define('DB_TITLE_DB_CALC_PRICE_HISTORY_DATE','订单业绩的计算天数');
define('DB_DESCRIPTION_DB_CALC_PRICE_HISTORY_DATE','请设置订单业绩的计算天数。单位为天。');
define('DB_TITLE_SHIPPING_BOX_TIME','到货时间计算');
define('DB_DESCRIPTION_SHIPPING_BOX_TIME','计算期望的到货时间');
define('DB_TITLE_SHIPPING_BOX_WEIGHT_LIST','设置包裹重量');
define('DB_DESCRIPTION_SHIPPING_BOX_WEIGHT_LIST','设置包裹重量');
define('DB_TITLE_SEND_PASSWORLD_POPUP_EMAIL_TITLE','重设密码的邮件标题');
define('DB_DESCRIPTION_SEND_PASSWORLD_POPUP_EMAIL_TITLE','重设密码的邮件标题');
define('DB_TITLE_SEND_PASSWORLD_POPUP_EMAIL_CONTENT','重设密码的邮件正文');
define('DB_DESCRIPTION_SEND_PASSWORLD_POPUP_EMAIL_CONTENT','修改密码的邮件URL:${URL}<br> 网站名：${SITE_NAME}<br> 网站的URL：${SITE_URL}<br> IP地址：${IP}');
define('DB_TITLE_SEND_PASSWORLD_EMAIL_TITLE','修改密码的邮件标题');
define('DB_DESCRIPTION_SEND_PASSWORLD_EMAIL_TITLE','修改密码的邮件标题');
define('DB_TITLE_SEND_PASSWORLD_EMAIL_CONTENT','修改密码的邮件内容');
define('DB_DESCRIPTION_SEND_PASSWORLD_EMAIL_CONTENT','修改密码的URL:${URL}<br> 网站名：${SITE_NAME}<br> 网站的URL：${SITE_URL}<br> IP地址：${IP}');
define('DB_TITLE_MODULE__INSTALLED','Installed Modules');
define('DB_DESCRIPTION_MODULE__INSTALLED','This is automatically updated. No need to edit.');
define('DB_TITLE_IP_SEAL_EMAIL_ADDRESS','前台・后台：管理员邮箱地址');
define('DB_DESCRIPTION_IP_SEAL_EMAIL_ADDRESS','IP EMAIL');
define('DB_TITLE_IP_SEAL_EMAIL_TITLE','登录锁定的邮件标题');
define('DB_DESCRIPTION_IP_SEAL_EMAIL_TITLE','');
define('DB_TITLE_IP_SEAL_EMAIL_TEXT','登录锁定的邮件正文');
define('DB_DESCRIPTION_IP_SEAL_EMAIL_TEXT','');
