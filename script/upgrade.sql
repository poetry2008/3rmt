-- 先执行db.php 再执行upgrade.sql
-- 隐藏设置 営業日カレンダー & 追加画像設置 & 画像設置
UPDATE  `configuration_group` SET  `visible` =  '0' WHERE  `configuration_group`.`configuration_group_id` =4;
UPDATE  `configuration_group` SET  `visible` =  '0' WHERE  `configuration_group`.`configuration_group_id` =902;
UPDATE  `configuration_group` SET  `visible` =  '0' WHERE  `configuration_group`.`configuration_group_id` =2028;
UPDATE  `configuration_group` SET  `visible` =  '0' WHERE  `configuration_group`.`configuration_group_id` =2029;
-- 插入设置 首页顶部&底部内容
INSERT INTO `configuration` (`configuration_id`, `configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`, `site_id`) VALUES (NULL, ' トップページのヘッダー内容', 'DEFAULT_PAGE_TOP_CONTENTS', '', ' トップページのヘッダー内容', '901', '15', NULL, '0000-00-00 00:00:00', NULL, 'tep_cfg_textarea(', '0'), (NULL, ' トップページのヘッダー内容', 'DEFAULT_PAGE_TOP_CONTENTS', '<h1 class="pageHeading"><strong>RMT</strong>ジャックポットへようこそ</h1>
<p class="comment">RMTジャックポットは株式会社iimyが運営するRMT(リアルマネートレード)総合サイトです。<br>
Final Fantasy XIをはじめとした大人気MMORPGであるAION、REDSTONE、Lineage、Lineage2、Ragnarok、TalesWeaver、CABAL、信長の野望Online、CRONOUS、SUN、PerfectWorld、ATLANTICAなどのオンラインゲーム通貨・装備・アイテム・アカウントを取り扱いしております。RMTジャックポットは、お客様に安全で快適なMMORPGライフをご提供いたします。<br>
<br>
<b>表示在庫以上の大口取引も受け付けておりますので、お気軽にお問い合わせください。</b></p>', ' トップページのヘッダー内容', '901', '15', NULL, '0000-00-00 00:00:00', NULL, 'tep_cfg_textarea(', '1'), (NULL, ' トップページのヘッダー内容', 'DEFAULT_PAGE_TOP_CONTENTS', '<div class="background_news01">
  <div class="news_title_01" style="height:40px; line-height:40px;"><div style="border-bottom:medium none; color:#FFFFFF; font-size:14px; font-weight:bold;padding-left:15px;">INFORMATION</div></div>
  <div class="news_title_02" id="news"> 
  <h2 class="index_h2" style="padding-left:42px;">はじめてRMTゲームマネーをご利用いただくお客様へ</h2>
	<p class="p_info_01" style="padding:10px 43px 0;">
		数多くのオンラインショップの中から<strong>RMT</strong>ゲームマネーにご来店いただき、誠にありがとうございます。<br>
		RMTゲームマネーは株式会社iimyが運営するオンラインゲーム通貨の売買(RMT)専門サイトです。左メニューからプレイされているゲームタイトルをクリックし、購入手続きを行ってください。<br>
		初めてご利用いただきますお客様は<a href="info/starting_rmt.html">はじめてのRMT</a>をご覧ください。<br>
	</p>
  </div>
</div>
<div class="box" style="font-size:14px;">
	<h3 style="padding-bottom:13px;">RMT</h3>
	<div class="sub_menu01">
		<div class="index_buy01">ゲーム通貨販売</div>
		<p style="font-size:14px; padding:0 25px;">
			FF11、リネージュ2、アイオン、レッドストーンなどのゲーム通貨(ギル・アデナ・ゴールドなど)を購入(<em>RMT</em>)できます。<br>
			RMT業界の先駆者として培ってきた経験を生かし、どこよりもより安い、どこよりも早いをモットーにお客様に安心して購入いただけるよう努めております。<br>
			インターネット銀行振込、クレジットカード決済、コンビニ決済に対応し、金融機関の営業時間外のお取引もスムーズに行えます。
		</p>
	</div>
	<div class="sub_menu01">
		<div class="index_buy01">ゲーム通貨買取</div>
		<p style="font-size:14px; padding:0 25px;">
			FF11、リネージュ2、アイオン、レッドストーンなど引退や解約等により不要になったゲーム通貨(ギル・アデナ・ゴールドなど)を買取(<em>RMT</em>)しております。<br>
			またFF11やリネージュ2などにおきましてはアカウントの買取も行っております。<br>
			現在、各ゲーム通貨の高価買取キャンペーンを実施中!
		</p>
	</div>
	<h3 class="index_payment01">RMTゲームマネー ナビ</h3>
           <div style="font-size:14px; padding:0 20px 0 25px;">
		お支払い方法については<a href="info/cubit.html">クレジットカード決済について</a>、<a href="info/bank_furikomi.html">銀行振込について</a>、<a href="info/smartpit.html">コンビニ決済について</a>をお読みください。<br>
		携帯電話メールアドレスにて、各ゲーム通貨のRMTをご注文のお客様は<a href="info/kmail_payment.html">携帯電話の設定方法</a>をお読みください。<br>
           </div>
	<h3 class="index_payment01">RMTとは?</h3>
           <div style="font-size:14px; padding:0 20px 0 25px;">
		MMORPGをプレイする中で、長年憧れていた武器や防具などの装備やアイテムがゲーム内で販売されていた。けれども、キャラクターの手持ち通貨が不足していて泣く泣く購入を諦めた。このような経験一度は経験したことがありませんか?<br>
		高価なアイテムを購入するために、ゲーム通貨を稼ぐには、非常に膨大な時間を要します。<br>
		<strong>RMT</strong>(リアルマネートレード)は、ギルやアデナ、ゴールドなどのゲーム通貨を時間が足りず上手に稼げないため購入したい方と、ゲーム通貨が余っているため売却したい方の双方の利害が一致した、オンラインゲームならではのサービス形態です。<br>
		RMTゲームマネーは、FF11やリネージュ2、ラグナロクなどの、人気ゲームタイトルはもちろんのこと、アイオンやレッドストーン、カバル、信長の野望などの新旧のゲームタイトルのゲーム通貨の激安販売、高価買取を行っております。
           </div>
	<p class="page_top"><a href="#top">▲このページのトップへ</a></p>
</div>', ' トップページのヘッダー内容', '901', '15', NULL, '0000-00-00 00:00:00', NULL, 'tep_cfg_textarea(', '2'), (NULL, ' トップページのヘッダー内容', 'DEFAULT_PAGE_TOP_CONTENTS', '<h1 class="pageHeading">

<span class="game_t">Welcome to <strong>RMT</strong>ワールドマネー</span>
</h1>
<p class="comment">
RMTワールドマネーは株式会社iimyが運営するRMT(リアルマネートレード)総合ショッピングサイトです。
FF11、Lineage2、RedStone、TalesWeaver、CABAL、信長の野望Online、RAGNAROKなどの人気オンラインゲームをはじめ、
ATLANTICA、SUN、CRONOUS、LucentHeart、RFonlineZ、ROSE、AION、女神転生、大航海時代、ラテールなど多数のプレイヤーに当RMTサイトをご利用いただけるよう、様々なMMORPGのゲーム通貨・アカウント・アイテム・装備などを売買をしております。
RMTワールドマネーは、新作オンラインゲームの動向の調査し、お客様のニーズに的確に対応した、クリーンな売買を心がけております。
激安価格・高価買取を実現し、充実したサポート体制のRMTワールドマネーをこの機会に是非ご利用ください。

<br>
<b>表示された在庫以上の大口売買も可能ですので、お気軽にお問い合わせください。</b></p><p class="pageBottom"></p>', ' トップページのヘッダー内容', '901', '15', NULL, '0000-00-00 00:00:00', NULL, 'tep_cfg_textarea(', '3'), (NULL, 'トップページのフッター内容 ', 'DEFAULT_PAGE_BOTTOM_CONTENTS', '', 'トップページのフッター内容 ', '901', '16', NULL, '0000-00-00 00:00:00', NULL, 'tep_cfg_textarea(', '0'), (NULL, 'トップページのフッター内容 ', 'DEFAULT_PAGE_BOTTOM_CONTENTS', '<div class="pageHeading">ゲーム通貨の販売について</div>
<p class="comment">
	オンラインゲーム通貨やアイテム、アカウントを現金にて購入(RMT)できます。ゲームによって1個あたりの個数が異なりますのでご注意ください。
	クレジットカード決済、コンビニ決済(近日導入予定)、銀行振込などの多種多様な決済方法を導入しており、土・日曜日、祝日も即時取引が可能です。
	また、取引場所や取引方法が豊富にあり、お客様のキャラクターに安全で最適な対応ができます。
</p>
<div class="pageHeading">ゲーム通貨の買取について</div>
<p class="comment">
	ゲームの解約、サーバー移動などの理由により必要がなくなりましたゲーム通貨やアイテム、アカウントを高価買取しております。
</p>
<h3 class="pageHeading">RMTとは</h3>
<p class="comment">
	Real Money Trade(リアルマネートレード)の略称。世界中のユーザーがネットワークを通じて同時にプレイするオンラインゲームにおいて、
	ゲーム内の通貨やアイテムを現実世界の現金で取引すること。
	RMTが盛んに行なわれているジャンルは主にMMORPGである。FFXIやリネージュ2、ラグナロクをはじめとする大人気MMORPGでは、
	ある程度のまとまった時間とゲーム通貨がなければ、ゲーム内の豊富なコンテンツを十分に楽しめない場合があります。
	ゲーム内の様々な場面で通貨が必要であり、十分な通貨がなくゲームを進めることが困難な場合は、当RMTサービスを利用することによって
	プレイ時間を短縮することができます。RMTにより効率的に遊ぶことができるのでゲーム通貨を求めるプレイヤー達の需要に沿って、
	近年RMTがより盛んに行われるようになりました。
	プレイヤーは、RMTジャックポットのゲーム通貨を購入することにより、膨大な時間をかけて狩をしなくても必要な武器や防具、
	アイテムを購入することができます。時間の足りないプレイヤーは、ゲームをより満喫するためにRMTジャックポットを利用する機会が増えています。
</p>', 'トップページのフッター内容 ', '901', '16', NULL, '0000-00-00 00:00:00', NULL, 'tep_cfg_textarea(', '1'), (NULL, 'トップページのフッター内容 ', 'DEFAULT_PAGE_BOTTOM_CONTENTS', '', 'トップページのフッター内容 ', '901', '16', NULL, '0000-00-00 00:00:00', NULL, 'tep_cfg_textarea(', '2'), (NULL, 'トップページのフッター内容 ', 'DEFAULT_PAGE_BOTTOM_CONTENTS', '<h1 class="pageHeading">
<span class="game_t">RMTワールドマネーのゲーム通貨 販売の特徴</span></h1>
<div class="comment">
<p>
<div class="service_text">販売の特徴</div>
VISAやMasterなど全てのクレジットカード決済、ジャパンネット銀行やイーバンク銀行などの銀行振込、ローソンやセブンイレブンなどでお支払いができるコンビニ決済(近日導入予定)などの様々な決済方法を導入しており、土・日曜日、祝日も即時ゲームマネーを激安価格で購入いただけます。<br>
また、充実した品揃えのゲームマネーはもちろんのこと、取引場所や取引方法も豊富にあり、お客様のアカウントに対し、安心で速やかなお取引を選択することができます。
</p>
<p>
<div class="service_text">買取の特徴</div>
オンラインゲームの引退や解約、サーバー移動などの理由により不要となりましたゲームマネーやアイテム、アカウントをRMT業界最高水準の価格にて高価買取しております。
</p></div><p class="pageBottom"></p>
<h1 class="pageHeading">
<span class="game_t">RMT(リアルマネートレード)とは</span></h1>
<p class="comment">
Real Money Trade(リアルマネートレード)の略称。一部のマスメディアではアールエムティーと呼ぶ場合もあります。ネットゲーム内で得られた架空財産を現金で売り買いする行為の総称を指します。
世界中のユーザーがネットワークを通じて同時にプレイするネットゲームにおいて、ゲーム内の通貨やアイテム、アカウントを現実世界の現金で取引する行為です。<br>
ゲーム達成の優劣が個人の技量よりも、単純な累積プレイ時間とキャラクターのレベル値、そして装備の特殊能力や高級消耗アイテムなどのゲーム内総資産量に左右されがちな特性をもつオンラインゲームにおいて活発です。<br>
RMTが盛んに行われているジャンルは主にMMORPGやMORPGです。FF11やリネージュ2、ラグナロク、レッドストーン、AIONをはじめとする大人気MMORPGでは、膨大なプレイ時間とゲーム通貨がなければ、アップデートで随時追加されるオンラインゲーム内の豊富なコンテンツを十分に攻略できないシチュエーションが無数に存在します。ゲーム内の様々な場面でゲーム通貨やアイテムが必要であり、ゲームマネーが乏しく、効率の良いゲーム攻略が困難な場合は、当RMTサービスの通貨やアイテムを購入することにより、金策などのプレイ時間の短縮やオンラインゲームをプレイする充実を実感できます。<br>
RMTの利用により効率的に遊ぶことができるため、ゲーム通貨を求めるプレイヤーの需要に沿う形で、近年RMT取引が活発に行われるようになりました。
プレイヤーは、RMTワールドマネーでゲーム通貨を購入することにより、膨大な時間をかけてレベリングや狩り、クエストを行わずとも、プレイヤーに最適なゲーム内の武器や防具、 アイテムを購入できます。<br>
<br>
RMTワールドマネーは、多数のお客様に支持していただけるショッピングサイトを目指して日々進化し続けております。 
</p><p class="pageBottom"></p>', 'トップページのフッター内容 ', '901', '16', NULL, '0000-00-00 00:00:00', NULL, 'tep_cfg_textarea(', '3');

-- GM RSS url

INSERT INTO `configuration` (`configuration_id`, `configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`, `site_id`) VALUES
(NULL, '全てのゲームのRSSアドレス', 'ALL_GAME_RSS', 'http://www.4gamer.net/rss/all_onlinegame.xml', '全てのゲームのRSSアドレス', 1, NULL, '2010-03-09 16:39:07', '0000-00-00 00:00:00', NULL, NULL, 0);
INSERT INTO `configuration` (`configuration_id`, `configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`, `site_id`) VALUES
(NULL, 'トップページのゲームニュースの表示数　', 'GAME_NEWS_MAX_DISPLAY', '5', 'トップページのゲームニュースの表示数　', 3, NULL, '2010-03-09 16:42:29', '0000-00-00 00:00:00', NULL, NULL, 0);
INSERT INTO `configuration` (`configuration_id`, `configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`, `site_id`) VALUES
(NULL, 'カテゴリのゲームニュース表示数', 'CATEGORIES_GAME_NEWS_MAX_DISPLAY', '25', 'カテゴリのゲームニュース表示数', 3, NULL, '2010-03-09 16:42:32', '0000-00-00 00:00:00', NULL, NULL, 0);


update orders set finished = (select orders_status.finished from orders_status where orders_status.orders_status_id = orders.orders_status);
update orders set orders_status_name = (select orders_status.orders_status_name from orders_status where orders_status.orders_status_id = orders.orders_status);
update orders set orders_status_image = (select orders_status.orders_status_image from orders_status where orders_status.orders_status_id = orders.orders_status);
update orders set language_id = (select orders_status.language_id from orders_status where orders_status.orders_status_id = orders.orders_status);

update orders_products set site_id=(select site_id from orders where orders.orders_id = orders_products.orders_id);

update information_page set romaji=IF(convert(romaji,SIGNED),pID,romaji);


UPDATE  `sites` SET  `name` =  'RMTジャックポット' WHERE  `sites`.`id` =1;
UPDATE  `sites` SET  `name` =  'RMTゲームマネー' WHERE  `sites`.`id` =2;
UPDATE  `sites` SET  `name` =  'RMTワールドマネー' WHERE  `sites`.`id` =3;
UPDATE  `sites` SET  `url` =  'http://www.iimy.co.jp' WHERE  `sites`.`id` =1;
UPDATE  `sites` SET  `url` =  'http://www.gamemoney.cc' WHERE  `sites`.`id` =2;
UPDATE  `sites` SET  `url` =  'http://rmt.worldmoney.jp' WHERE  `sites`.`id` =3;


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
NULL , 'プリントメール', 'PRINT_EMAIL_ADDRESS', 'printing_order@iimy.co.jp', 'プリントメール', '1', '25' , NULL , '0000-00-00 00:00:00', NULL , NULL , '0'
);