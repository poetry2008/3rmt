INSERT INTO `sites` (
`id` ,
`romaji` ,
`name` ,
`url` ,
`order_num`
)
VALUES (
'4', 'id', 'RMTデポ', 'http://itemdepot.jp', '0'
);

INSERT INTO `banners` (`site_id`, `banners_id`, `banners_title`, `banners_url`, `banners_image`, `banners_group`, `banners_html_text`, `expires_impressions`, `expires_date`, `date_scheduled`, `date_added`, `date_status_change`, `status`) VALUES
('4', 4, 'クレジットカード（VISA MASTER JCB AMEX Diners）、銀行振込', '', 'payment.gif', 'left1', '', NULL, NULL, NULL, '2005-06-17 00:49:10', NULL, 1),
('4', 13, 'SSL', '', '', 'right3', '<!-- GeoTrust Smart Icon tag. Do not edit. -->\r\n          <SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript" SRC="//smarticon.geotrust.com/si.js"></SCRIPT>\r\n          <!-- END of GeoTrust Smart Icon tag -->', NULL, NULL, NULL, '2005-06-25 19:28:21', '2007-01-05 16:37:59', 0),
('4', 5, '株式会社iimy-営業時間', '', 'banners/businesshours.gif', 'left2', '', NULL, NULL, NULL, '2005-06-17 00:50:22', NULL, 1),
('4', 18, '左4段目', '', '', 'left4', '', NULL, NULL, NULL, '2005-06-26 23:12:10', '2006-12-03 19:46:07', 0),
('4', 11, 'メールマガジン', '', 'banners/banner_1.gif', 'left2', '', NULL, NULL, NULL, '2005-06-17 10:10:49', '2006-12-16 07:40:59', 0),
('4', 12, '特価商品', '', 'banners/banner_2.gif', 'right2', '', NULL, NULL, NULL, '2005-06-17 10:11:40', '2006-12-16 07:40:57', 0),
('4', 14, 'リンク集', '', '', 'right4', '<img src="images/design/box/bestlinks.gif" border="0" alt="リンク集" width="171" height="25">\r\n<div class="boxText">\r\n<a href="http://www.playonline.com/home/polnews/list_mnt.shtml" target="_blank">FFXI メンテナンス情報</a>\r\n<a href="http://www5.plala.or.jp/SQR/ff11/" target="_blank">FF11 攻略 eLeMeN</a>\r\n<a href="http://ff11wiki.rdy.jp/-1450975598.html" target="_blank">FF11 Wiki</a>\r\n<a href="http://agardens.fc2web.com/" target="_blank">FF11 合成 職人の庭</a>\r\n<a href="http://www.hotgame.co.jp/" target="_blank">FF11 コミュニティ ホットゲーム</a>\r\nデータベース\r\n<hr>\r\n<a href="http://www.lineage2.jp/bbs/list.aspx?bid=3" target="_blank">リネージュ2 メンテナンス情報</a>\r\n<a href="http://hrb.finito.fc2.com/" target="_blank">リネージュII いもづる〜LINEAGE2 Links〜</a>\r\n<a href="http://l2quest.web2.jp/" target="_blank">LINEAGE2 クエスト攻略</a>\r\n<a href="http://www.geocities.jp/flttsr_q3p2u/" target="_blank">LINEAGE2 攻略通信</a>\r\n<a href="http://l2mpt.net/" target="_blank">LINEAGE2相場情報 Lineage MarketPriceTurbo</a>\r\n<a href="http://ammonite.pya.jp/totsugeki/" target="_blank">LINEAGE2血盟情報局</a>\r\n<hr>\r\n<a href="http://www.lineage.jp/news/news_list.aspx?ct=1" target="_blank">リネージュ メンテナンス情報</a>\r\n<a href="http://www.lineinfo.jp/" target="_blank">LINEAGE 攻略通信</a>\r\n<a href="http://lin1.l2mpt.net/" target="_blank">リネージュ相場情報 Lineage MarketPriceTurbo</a>\r\n<a href="http://f61.aaa.livedoor.jp/~syaka/" target="_blank">LINEAGEシュミレータ Effective Lineage </a>\r\n<a href="http://www.jias.jp/line/" target="_blank">リネージュ情報 リネしながら見るページ</a>\r\n</div>', NULL, NULL, NULL, '2005-06-25 19:32:31', NULL, 1),
('4', 15, '右5段目', '', 'banners/banner158-50.gif', 'right5', '', NULL, NULL, NULL, '2005-06-25 19:33:46', '2006-12-02 21:28:05', 0),
('4', 19, 'RMT激安情報メールマガジン', 'http://www.itemdepot.jp/mail_magazine.php', 'banners/banner_1.gif', 'left3', '', NULL, NULL, NULL, '2005-07-15 16:06:41', NULL, 1),
('4', 24, 'RMTゲームマネー', '', '', 'footer', '<br><br><br>', NULL, NULL, NULL, '2005-08-08 19:11:41', NULL, 1),
('4', 25, 'フッター１', '', '', 'footer1', '', NULL, NULL, NULL, '2006-07-02 03:00:01', '2006-12-03 19:39:28', 0),
('4', 26, 'フッター2', '', '', 'footer2', '', NULL, NULL, NULL, '2006-07-02 03:00:42', '2006-12-03 19:39:30', 0),
('4', 27, 'フッター3', '', '', 'footer3', '', NULL, NULL, NULL, '2006-07-02 03:01:03', '2006-12-03 19:39:31', 0);