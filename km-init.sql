INSERT INTO `sites` (
`id` ,
`romaji` ,
`name` ,
`url` ,
`order_num`
)
VALUES (
'5', 'km', 'kame', 'http://3km.maker.200.com', '0'
);

INSERT INTO `banners` (`site_id`, `banners_id`, `banners_title`, `banners_url`, `banners_image`, `banners_group`, `banners_html_text`, `expires_impressions`, `expires_date`, `date_scheduled`, `date_added`, `date_status_change`, `status`) VALUES
('5', 4, 'クレジットカード（VISA MASTER JCB AMEX Diners）、銀行振込', '', 'payment.gif', 'left1', '', NULL, NULL, NULL, '2005-06-17 00:49:10', NULL, 1),
('5', 13, 'SSL', '', '', 'right3', '<!-- GeoTrust Smart Icon tag. Do not edit. -->\r\n          <SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript" SRC="//smarticon.geotrust.com/si.js"></SCRIPT>\r\n          <!-- END of GeoTrust Smart Icon tag -->', NULL, NULL, NULL, '2005-06-25 19:28:21', '2007-01-05 16:37:59', 0),
('5', 5, '株式会社iimy-営業時間', '', 'banners/businesshours.gif', 'left2', '', NULL, NULL, NULL, '2005-06-17 00:50:22', NULL, 1),
('5', 18, '左4段目', '', '', 'left4', '', NULL, NULL, NULL, '2005-06-26 23:12:10', '2006-12-03 19:46:07', 0),
('5', 11, 'メールマガジン', '', 'banners/banner_1.gif', 'left2', '', NULL, NULL, NULL, '2005-06-17 10:10:49', '2006-12-16 07:40:59', 0),
('5', 12, '特価商品', '', 'banners/banner_2.gif', 'right2', '', NULL, NULL, NULL, '2005-06-17 10:11:40', '2006-12-16 07:40:57', 0),
('5', 14, 'リンク集', '', '', 'right4', '<img src="images/design/box/bestlinks.gif" border="0" alt="リンク集" width="171" height="25">\r\n<div class="boxText">\r\n<a href="http://www.playonline.com/home/polnews/list_mnt.shtml" target="_blank">FFXI メンテナンス情報</a>\r\n<a href="http://www5.plala.or.jp/SQR/ff11/" target="_blank">FF11 攻略 eLeMeN</a>\r\n<a href="http://ff11wiki.rdy.jp/-1450975598.html" target="_blank">FF11 Wiki</a>\r\n<a href="http://agardens.fc2web.com/" target="_blank">FF11 合成 職人の庭</a>\r\n<a href="http://www.hotgame.co.jp/" target="_blank">FF11 コミュニティ ホットゲーム</a>\r\nデータベース\r\n<hr>\r\n<a href="http://www.lineage2.jp/bbs/list.aspx?bid=3" target="_blank">リネージュ2 メンテナンス情報</a>\r\n<a href="http://hrb.finito.fc2.com/" target="_blank">リネージュII いもづる〜LINEAGE2 Links〜</a>\r\n<a href="http://l2quest.web2.jp/" target="_blank">LINEAGE2 クエスト攻略</a>\r\n<a href="http://www.geocities.jp/flttsr_q3p2u/" target="_blank">LINEAGE2 攻略通信</a>\r\n<a href="http://l2mpt.net/" target="_blank">LINEAGE2相場情報 Lineage MarketPriceTurbo</a>\r\n<a href="http://ammonite.pya.jp/totsugeki/" target="_blank">LINEAGE2血盟情報局</a>\r\n<hr>\r\n<a href="http://www.lineage.jp/news/news_list.aspx?ct=1" target="_blank">リネージュ メンテナンス情報</a>\r\n<a href="http://www.lineinfo.jp/" target="_blank">LINEAGE 攻略通信</a>\r\n<a href="http://lin1.l2mpt.net/" target="_blank">リネージュ相場情報 Lineage MarketPriceTurbo</a>\r\n<a href="http://f61.aaa.livedoor.jp/~syaka/" target="_blank">LINEAGEシュミレータ Effective Lineage </a>\r\n<a href="http://www.jias.jp/line/" target="_blank">リネージュ情報 リネしながら見るページ</a>\r\n</div>', NULL, NULL, NULL, '2005-06-25 19:32:31', NULL, 1),
('5', 15, '右5段目', '', 'banners/banner158-50.gif', 'right5', '', NULL, NULL, NULL, '2005-06-25 19:33:46', '2006-12-02 21:28:05', 0),
('5', 19, 'RMT激安情報メールマガジン', 'http://www.itemdepot.jp/mail_magazine.php', 'banners/banner_1.gif', 'left3', '', NULL, NULL, NULL, '2005-07-15 16:06:41', NULL, 1),
('5', 24, 'RMTゲームマネー', '', '', 'footer', '<br><br><br>', NULL, NULL, NULL, '2005-08-08 19:11:41', NULL, 1),
('5', 25, 'フッター１', '', '', 'footer1', '', NULL, NULL, NULL, '2006-07-02 03:00:01', '2006-12-03 19:39:28', 0),
('5', 26, 'フッター2', '', '', 'footer2', '', NULL, NULL, NULL, '2006-07-02 03:00:42', '2006-12-03 19:39:30', 0),
('5', 27, 'フッター3', '', '', 'footer3', '', NULL, NULL, NULL, '2006-07-02 03:01:03', '2006-12-03 19:39:31', 0);


INSERT INTO `configuration` (
`configuration_id` ,
`configuration_title` ,
`configuration_key` ,
`configuration_value` ,
`configuration_description` ,
`configuration_group_id` ,
`sort_order` ,
`last_modified` ,
`date_added` ,
`use_function` ,
`set_function` ,
`site_id`
)
VALUES (
NULL , 'フッターコピーライト', 'C_FOOTER_COPY_RIGHT', '当ウェブサイトに記載されている会社名・製品名・システム名などは、各社の登録商標、もしくは商標です。', 'ホームページのフッターに表示されるコピーライトを入力してください。', '901', '11', '2010-12-03 15:34:57', '2010-12-03 15:34:57', '', 'tep_cfg_textarea(', '5'
);


INSERT INTO `configuration` (`configuration_id`, `configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`, `site_id`) VALUES (NULL, 'トップページのヘッダー内容', 'DEFAULT_PAGE_TOP_CONTENTS', '<div class="welcome_box">

<div class="pageHeading">

<img src="images/menu_ico.gif" alt="" align="top">&nbsp;Welcome to RMTアイテムデポ</div>

<div class="comment_index01"><p><img src="images/design/index_text01.gif" alt="welcome">RMTアイテムデポは株式会社iimyが運営するRMT(リアルマネートレード)総合ショッピングサイトです。

 FF11、Lineage2、RedStone、TalesWeaver、CABAL、信長の野望Online、RAGNAROKなどの人気オン

ラインゲームをはじめ、 ATLANTICA、SUN、CRONOUS、LucentHeart、RFonlineZ、ROSE、AION、女

神転生、大航海時代、ラテールなど多数のプレイヤーに当RMTサイトをご利用いただけるよう、様々な

MMORPGのゲーム通貨・アカウント・アイテム・装備などを売買をしております。 RMTアイテムデポは、

新作オンラインゲームの動向の調査し、お客様のニーズに的確に対応した、クリーンな売買を心がけ

ております。激安価格・高価買取を実現し、充実したサポート体制のRMTアイテムデポをこの機会に是

非ご利用ください</p>

<p><strong>表示された在庫以上の大口売買も可能ですので、お気軽にお問い合わせください。</strong></p>

</div>

<div class="pageBottom"></div>

</div>', ' トップページのヘッダー内容', '901', '15', '2010-12-03 15:40:29', '2010-12-03 15:40:29', NULL, 'tep_cfg_textarea(', '5'), (NULL, 'トップページのフッター内容 ', 'DEFAULT_PAGE_BOTTOM_CONTENTS', '<div class="welcome_box">

<div class="pageHeading">

<img src="images/menu_ico.gif" alt="" align="top">&nbsp;RMTアイテムデポのゲーム通貨 販売の特徴</div>

<div class="comment_index02">

<p><img src="images/design/index_text02.gif" alt="welcome">

<b>販売の特徴</b><br>

VISAやMasterなど全てのクレジットカード決済、ジャパンネット銀行

やイーバンク銀行などの銀行振込、ローソンやセブンイレブンなどで

お支払いができるコンビニ決済(近日導入予定)などの様々な決済方

法を導入しており、土・日曜日、祝日も即時ゲームマネーを激安価格

で購入いただけます。

また、充実した品揃えのゲームマネーはもちろんのこと、取引場所や

取引方法も豊富にあり、お客様のアカウントに対し、安心で速やかな

お取引を選択することができます。</p>

<p><b>買取の特徴</b><br>

オンラインゲームの引退や解約、サーバー移動などの理由により不要となりましたゲームマネーや

アイテム、アカウントをRMT業界最高水準の価格にて高価買取しております。 </p>

</div>

<div class="pageBottom"></div>

</div> 
<div class="welcome_box">

<div class="pageHeading">

<img src="images/menu_ico.gif" alt="" align="top">&nbsp;RMT(リアルマネートレード)とは</div>

<div class="comment_index01"><img src="images/design/index_text03.gif" alt="welcome">Real Money Trade(リアルマネートレード)の略称。一部のマスメディアではアールエムティーと呼ぶ場

合もあります。ネットゲーム内で得られた架空財産を現金で売り買いする行為の総称を指します。世界

中のユーザーがネットワークを通じて同時にプレイするネットゲームにおいて、ゲーム内の通貨やアイ

テム、アカウントを現実世界の現金で取引する行為です。ゲーム達成の優劣が個人の技量よりも、単

純な累積プレイ時間とキャラクターのレベル値、そして装備の特殊能力や高級消耗アイテムなどのゲ

ーム内総資産量に左右されがちな特性をもつオンラインゲームにおいて活発です。RMTが盛んに行わ

れているジャンルは主にMMORPGやMORPGです。FF11やリネージュ2、ラグナロク、レッドストーン、AIO

Nをはじめとする大人気MMORPGでは、膨大なプレイ時間とゲーム通貨がなければ、アップデートで随

時追加されるオンラインゲーム内の豊富なコンテンツを十分に攻略できないシチュエーションが無数に

存在します。ゲーム内の様々な場面でゲーム通貨やアイテムが必要であり、ゲームマネーが乏しく、効

率の良いゲーム攻略が困難な場合は、当RMTサービスの通貨やアイテムを購入することにより、金策

などのプレイ時間の短縮やオンラインゲームをプレイする充実を実感できます。RMTの利用により効率

的に遊ぶことができるため、ゲーム通貨を求めるプレイヤーの需要に沿う形で、近年RMT取引が活発に

行われるようになりました。プレイヤーは、RMTアイテムデポでゲーム通貨を購入することにより、膨大

な時間をかけてレベリングや狩り、クエストを行わずとも、プレイヤーに最適なゲーム内の武器や防具、

 アイテムを購入できます。

RMTアイテムデポは、多数のお客様に支持していただけるショッピングサイトを目指して日々進化し続

けております。

</div>

<div class="pageBottom"></div>

</div>', 'トップページのフッター内容 ', '901', '16', '2010-12-03 15:42:40', '2010-12-03 15:42:40', NULL, 'tep_cfg_textarea(', '5');


