<?php
  require('includes/application_top.php');
  require("includes/jcode.phps");
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<?php  
  $msg = "";
  if (isset($_GET['action']) && $_GET['action'] == 'upload'){
     
    
	echo '<P>インサート作業開始...</P>';
	// CSVファイルのチェック
    $chk_csv = true;
    $filename = $HTTP_POST_FILES['products_csv']['name'];
    if(substr($filename, strrpos($filename,".")+1)!="csv") $chk_csv = false;
     
    // ファイル名の参照チェック
    if($HTTP_POST_FILES['products_csv']['tmp_name']!="" && $chk_csv){
  	$file = fopen($products_csv,"r");
	
	$cnt = "0"; $chk_input = true;
	while($dat = fgetcsv($file,10000,',')){
		// 価格情報が数字でない場合は2行目から読む
		//if(!is_numeric($dat[8])) $dat = fgetcsv($file,10000,',');
		
		//修正：2006.08.28 ds-style
		if(!is_numeric($dat[8])) {
		  //定価情報が数字として扱える？
		  if(is_numeric($dat[7])) {
		    //特価情報があるか？
			if(is_numeric($dat[9])) {
			  //process OK
			} else {
			  continue;
			}
		  } else {
		    continue;
		  }
		} else {
		  //process OK
		}
		
		// EUCに変換
		for($e=0;$e<count($dat);$e++){
			$dat[$e] = addslashes(jcodeconvert($dat[$e],"0","1"));
		}
		
		####################
		# DBへのデータ挿入 #
		####################
		if($chk_input){
			//変数に挿入
			$dat0 = trim($dat[0]);//A 大カテゴリ
			$dat1 = trim($dat[1]);//B 中カテゴリ
			$dat2 = trim($dat[2]);//C メーカー名
			$dat3 = trim($dat[3]);//D 商品名
			$dat4 = trim($dat[4]);//E 商品説明
			$dat5 = trim($dat[5]);//F 型番
			$dat6 = trim($dat[6]);//G 画像パス
			$org_price = trim($dat[7]);//H ---追加項目---定価
			$dat7 = trim($dat[8]);//I 価格
			$dat8 = trim($dat[9]);//J 特売価格
			$dat9 = trim($dat[10]);//K 数量
			$dat10 = trim($dat[11]);//L 発売日
			$dat11 = trim($dat[12]);//M 在庫ステータス
			$dat12 = trim($dat[13]);//N 関連URL
			$dat13 = trim($dat[14]);//O 重量
			$add_jan = trim($dat[15]);//P ---追加項目---JANコード
			$add_size = trim($dat[16]);//Q ---追加項目---サイズ
			$add_naiyou = trim($dat[17]);//R ---追加項目---内容量
			$add_zaishitu = trim($dat[18]);//S ---追加項目---材質
			$dat14 = trim($dat[19]);//T 税種別
			$add_com = trim($dat[20]);//U ---追加項目---備考
			
			//説明文を整形
			// edit 2009.5.14 maker
			//説明文[0]｜定価[1]｜JANコード[2]｜サイズ[3]｜内容量[4]｜材質[5]｜備考[6]｜の順
			//$description = $dat4 . '|-#-|' . $org_price . '|-#-|' . $add_jan . '|-#-|' . $add_size . '|-#-|' . $add_naiyou . '|-#-|' . $add_zaishitu . '|-#-|' . $add_com;//maker
			
			//現在の時刻
			$now_date = date("Y-m-d H:i:s", time());
			
			//初期設定（読み込みエラー防止用）
			if($dat6 == "") $dat6 = 'NULL';//画像パス
			if($dat9 == "") $dat9 = '0';//数量
			if($dat10 == "") $dat10 = 'NULL';//発売日
			if($dat11 == "" && $dat9 == '0') { $dat11 = '0'; }else{ $dat11 = '1' ;}//在庫
			if($dat14 == "") $dat14 = '1';//税種別が空だったら、「一般消費税」を挿入
				
			//カテゴリ挿入
			if($dat0 != "") {
			  //大カテゴリの重複チェック
			  $dat0count_query = tep_db_query("select count(*) as cnt from categories_description where categories_name = '".$dat0."'");
			  $dat0count = tep_db_fetch_array($dat0count_query);
			  if($dat0count['cnt'] == 0) {
			    //データがないカテゴリなので挿入
				tep_db_query("insert into categories (categories_id, categories_image, parent_id, date_added) values ('', '', '0', '".$now_date."')");
				$categories_id = tep_db_insert_id();
				tep_db_query("insert into categories_description(categories_id, language_id, categories_name) values ('".$categories_id."', '4', '".$dat0."')");
			  } else {
			    //カテゴリが重複していたら該当カテゴリIDを取得
				$cquery = tep_db_query("select categories_id from categories_description where categories_name = '".$dat0."'");
				$c = tep_db_fetch_array($cquery);
				$categories_id = $c['categories_id'];
			  }
			  
			  //中カテゴリの重複チェック
			  if($dat1 != '') {
			  $dat1count_query = tep_db_query("select count(*) as cnt from categories_description where categories_name = '".$dat1."'");
			  $dat1count = tep_db_fetch_array($dat1count_query);
			  if($dat1count['cnt'] == 0) {
			    //データがないカテゴリなので挿入
				tep_db_query("insert into categories (categories_id, categories_image, parent_id, date_added) values ('', '', '".$categories_id."', '".$now_date."')");
				$categories_id_up = tep_db_insert_id();
				tep_db_query("insert into categories_description(categories_id, language_id, categories_name) values ('".$categories_id_up."', '4', '".$dat1."')");
			  } else {
			    //カテゴリカウントがあったので上位カテゴリとの整合性チェック
				$dat1chk_id = '';
				$dat1chk_query = tep_db_query("select categories_id from categories_description where categories_name = '".$dat1."'");
				while($dat1chk = tep_db_fetch_array($dat1chk_query)) {
				  $dat1chk2_query = tep_db_query("select parent_id from categories where categories_id = '".$dat1chk['categories_id']."'");
				  $dat1chk2 = tep_db_fetch_array($dat1chk2_query);
				  if($categories_id == $dat1chk2['parent_id']) {
				    $dat1chk_id = $dat1chk['categories_id'];
				  }
				}
				
				if($dat1chk_id != '') {
				  //上位との整合性が取れた場合該当カテゴリIDを定義
				  $categories_id_up = $dat1chk_id;
				} else {
				  //整合性が取れない新規のカテゴリの場合
				  tep_db_query("insert into categories (categories_id, categories_image, parent_id, date_added) values ('', '', '".$categories_id."', '".$now_date."')");
				  $categories_id_up = tep_db_insert_id();
				  tep_db_query("insert into categories_description(categories_id, language_id, categories_name) values ('".$categories_id_up."', '4', '".$dat1."')");
				}
			  }
			  
			  }//if($dat1 != '') {
			}
			
			//----------------------------------//
			
			//メーカー挿入
			if($dat2 != "") {
			  //重複チェック開始
			  $dat2count_query = tep_db_query("select count(*) as cnt from manufacturers where manufacturers_name = '".$dat2."'");
			  $dat2count = tep_db_fetch_array($dat2count_query);
			  
			  //重複していない場合は挿入(manufacturers)
			  if($dat2count['cnt'] == 0 && $dat2 != "") {
			    tep_db_query("insert into manufacturers (manufacturers_id, manufacturers_name, date_added) values ('', '".$dat2."', '".$now_date."')");
				
				$manufacturers_id = tep_db_insert_id();
				
				tep_db_query("insert into manufacturers_info (manufacturers_id, languages_id) values ('".$manufacturers_id."', '4')");
			  } else {
			    //重複していた場合の処理
				$mquery = tep_db_query("select manufacturers_id from manufacturers where manufacturers_name = '".$dat2."'");
				$m = tep_db_fetch_array($mquery);
				$manufacturers_id = $m['manufacturers_id'];
			  }
			  
			}
			
			//----------------------------------//
			
			//商品関連挿入
			if($dat3 != "" && $dat5 != "") {
			  //重複チェック開始（基準：型番）
			  $dat3count_query = tep_db_query("select count(*) as cnt from products where products_model = '".$dat5."'");
			  $dat3count = tep_db_fetch_array($dat3count_query);
			  
			  //重複していない場合はデータの挿入開始
			  if($dat3count['cnt'] == 0) {
			    
				//products
				tep_db_query("insert into products (
				products_id, 
				products_quantity, 
				products_model, 
				products_image, 
				products_price, 
				products_date_added, 
				products_date_available, 
				products_weight, 
				products_status, 
				products_tax_class_id, 
				manufacturers_id
				) values ('', '".$dat9."', '".$dat5."', '".$dat6."', '".$dat7."', '".$now_date."', '".$dat10."', '".$dat13."', '".$dat11."', '".$dat14."', '".$manufacturers_id."')");
			  
				$products_id = tep_db_insert_id();
				
				//products_description
				/* 仕様変更 - 2005.11.29 ds-style
				tep_db_query("insert into products_description (
				products_id, 
				language_id,
				products_name,
				products_description, 
				products_url
				) values ('".$products_id."', '4', '".$dat3."', '".$dat4."', '".$dat12."')");
				*/
				// edit 2009.5.14 maker
				tep_db_query("insert into products_description (
				products_id, 
				language_id,
				products_name,
				products_attention_1,
				products_attention_2,
				products_attention_3,
				products_attention_4,
				products_attention_5,
				products_description_".ABBR_SITENAME.", 
				products_url
				) values ('".$products_id."', '4', '".$dat3."', '".$add_jan."', '".$add_size."', '".$add_naiyou."', '".$add_zaishitu."', '".$add_com."', '".$dat4."', '".$dat12."')");
			
				
				//products_to_categories
				if($dat1 != "") {
				tep_db_query("insert into products_to_categories (products_id, categories_id) values ('".$products_id."', '".$categories_id_up."')");
				} else {
				tep_db_query("insert into products_to_categories (products_id, categories_id) values ('".$products_id."', '".$categories_id."')");
				}
				
			  } else {
			    //重複していた場合情報の更新
				
				//商品ID取得
			    $datinfo_query = tep_db_query("select products_id from products where products_model = '".$dat5."'");
			    $datinfo = tep_db_fetch_array($datinfo_query);
				$products_id = $datinfo['products_id'];

				$products_array = array('products_quantity' => $dat9,
										'products_model' => $dat5,
										'products_image' => $dat6,
										'products_price' => $dat7,
										'products_date_available' => $dat10,
										'products_last_modified' => $now_date,
										'products_weight' => $dat13,
										'products_status' => $dat11,
										'products_tax_class_id' => $dat14,
										'manufacturers_id' => $manufacturers_id
										);
										
			    tep_db_perform(TABLE_PRODUCTS, $products_array, 'update', 'products_id = \'' . tep_db_input($products_id) . '\'');
				
				/* 仕様変更 - 2005.11.29 ds-style
				$products_description_array = array('language_id' => '4',
													'products_name' => $dat3,
													'products_description' => $dat4,
													'products_url' => $dat12
													);
				*/
				$products_description_array = array('language_id' => '4',
													'products_name' => $dat3,
													'products_description_'.ABBR_SITENAME => $description,
													'products_url' => $dat12
													);
													
			    tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $products_description_array, 'update', 'products_id = \'' . tep_db_input($products_id) . '\'');
			
				//products_to_categories
				if($dat1 != "") {
				tep_db_query("update products_to_categories set categories_id = '".$categories_id_up."' where products_id = '".$products_id."'");
				} else {
				tep_db_query("update products_to_categories set categories_id = '".$categories_id."' where products_id = '".$products_id."'");
				}
			}
			
			//----------------------------------//
			
			//特価商品挿入
			if($dat8 != "") {
			  //重複チェック
			  $dat8count_query = tep_db_query("select count(*) as cnt from specials where products_id = '".$products_id."'");
			  $dat8count = tep_db_fetch_array($dat8count_query);
			  if($dat8count['cnt'] == 0) {
			     //インサート
				 tep_db_query("insert into specials (
				 specials_id, 
				 products_id, 
				 specials_new_products_price, 
				 specials_date_added, 
				 status
				 ) values ('', '".$products_id."', '".$dat8."', '".$now_date."', '1')");
			  } else {
				//アップデート
				$products_specialprice_array = array('specials_new_products_price' => $dat8,
													 'specials_last_modified' => $now_date,
													 'status' => $dat11
													 );
			    
				tep_db_perform(TABLE_SPECIALS, $products_specialprice_array, 'update', 'products_id = \'' . tep_db_input($products_id) . '\'');
			  }
			} else {
			  $dat8count_query = tep_db_query("select count(*) as cnt from specials where products_id = '".$products_id."'");
			  $dat8count = tep_db_fetch_array($dat8count_query);
			  if($dat8count['cnt'] != '0') {
				//アップデート
				$products_specialprice_array = array('specials_new_products_price' => $dat8,
													 'specials_last_modified' => $now_date,
													 'status' => '0'
													 );
			    
				tep_db_perform(TABLE_SPECIALS, $products_specialprice_array, 'update', 'products_id = \'' . tep_db_input($products_id) . '\'');
			  }
			}
			
		    $cnt++;
			if($cnt % 200 == 0) {
			  echo '・';
			}
			Flush();
	    }
	  }
	}
	
	fclose($file);
	
	   echo "<P><font color='#CC0000'><b>".$cnt."件の商品データをアップロードしました。</b></font></P>";
	
	}else{
	 
	   echo "<P><font color='#CC0000'><b>商品データをアップロードできませんでした。<br>所定のCSVファイルを参照してください。</b></font></P>";
	
	}
	echo '<a href="products_up.php">←戻る</a>';
} else {
?>
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>

            <td class="pageHeading">商品データアップロード</td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width="100%" border="0" cellpadding="2" cellspacing="0">
          <tr><FORM method="POST" action="products_up.php?action=upload"  enctype="multipart/form-data">
            <td><table border="0" cellpadding="0" cellspacing="2">
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right"><input type="file" name="products_csv" size="50"></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="left"><input type=submit name=download value="アップロード"></td>
              </tr>
            </table></td>
	    <input type="hidden" name="max_file_size" value="1000000">
          </form></tr>
          <tr>
            <td class="pageHeading">データ配列について<br>
            <span class="fieldRequired">*必須項目 </span></td>
          </tr>
          <tr>
            <td>            <p class="smallText">CSVでと商品登録を行う場合は<a href="backup.php" target="_blank">ここをクリック</a>して全データのバックアップをしてから必ず行うようにしてください。アップロードする前に既存商品データのダウンロードをしたファイルを編集してアップロードしてください。</p>            <table width="100%"  border="0" cellpadding="2" cellspacing="1" class="infoBoxHeading">
              <tr>
                <td width="20" align="center" class="infoBoxContent">&nbsp;</td>
                <td width="120" class="menuBoxHeading">項目</td>
                <td class="menuBoxHeading">説明</td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">A</td>
                <td class="menuBoxHeading">大カテゴリ<span class="fieldRequired">*</span></td>
                <td class="menuBoxHeading">大カテゴリ名を入力してください。大カテゴリ名が　カタログ管理　→　カタログ/商品登録　に登録されていない場合は自動的に大カテゴリを生成します。<br>
                <span class="fieldRequired">半角英数32文字、日本語16文字まで入力可能。</span></td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">B</td>
                <td class="menuBoxHeading">中カテゴリ</td>
                <td class="menuBoxHeading">大カテゴリに対する中カテゴリを入力してください。<br>
                <span class="fieldRequired">空白でも可。半角英数32文字、日本語16文字まで入力可能。</span></td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">C</td>
                <td class="menuBoxHeading">メーカー名</td>
                <td class="menuBoxHeading">メーカー名を入力してください。メーカー名が　カタログ管理　→　メーカー登録　に登録されていない場合は自動的にメーカー名を生成します。<br>
                <span class="fieldRequired">空白でも可。半角英数32文字、日本語16文字まで入力可能。</span></td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">D</td>
                <td class="menuBoxHeading">商品名<span class="fieldRequired">*</span></td>
                <td class="menuBoxHeading">商品名を入力してください。<br>
                <span class="fieldRequired">半角英数64文字、日本語32文字まで入力可能。</span></td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">E</td>
                <td class="menuBoxHeading">商品説明</td>
                <td class="menuBoxHeading">商品説明文を入力してください。HTMLを入力する場合は開業せずに一行でCSVカラムに入力してください。<br>
                例）&lt;table&gt;&lt;tr&gt;&lt;td&gt;XXXXXX&lt;/td&gt;&lt;/tr&gt;......<br>
                <span class="fieldRequired">空白でも可。HTML使用可、文字数無制限。</span></td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">F</td>
                <td class="menuBoxHeading">型番<span class="fieldRequired">*</span></td>
                <td class="menuBoxHeading">商品に対する型番（品番）を入力してください。<span class="fieldRequired"><strong>【重要！】データベース内に同一の型番が存在した場合は既存登録商品の在庫数が更新され、商品の登録は行われません。</strong></span><br>
                <span class="fieldRequired">半角英数12文字、日本語入力不可。</span></td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">G</td>
                <td class="menuBoxHeading">画像パス</td>
                <td class="menuBoxHeading">メイン画像のみ登録することができます。複数ある場合はCSVで登録後、商品毎に追加画像をアップロードしてください。<br>                
                <?php echo HTTP_SERVER .DIR_WS_CATALOG .DIR_WS_IMAGES ;?>に対する画像のパスを入力してください。<br>
                商品ページには　<span class="fieldRequired">横幅:<?php echo PRODUCT_INFO_IMAGE_WIDTH ; ?>px　縦幅:<?php echo PRODUCT_INFO_IMAGE_HEIGHT ; ?>pxで設定されていますが、これよりも画像が大きい場合は横幅と縦幅を自動的に最適化したサムネイル画像が生成されます。</span>画像サイズは特に気にする必要はありません。CSVでファイルをアップロードした後に、FTPクライアントから画像のみを指定した保存ディレクトリにアップロードを必ず行ってください。<br>
                例）sample.gif</td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">H</td>
                <td class="menuBoxHeading">定価<span class="fieldRequired">*</span></td>
                <td class="menuBoxHeading">税抜きの定価を入力してください</td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">I</td>
                <td class="menuBoxHeading">価格<span class="fieldRequired">*</span></td>
                <td class="menuBoxHeading">税抜き価格を入力してください。</td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">J</td>
                <td class="menuBoxHeading">特売価格</td>
                <td class="menuBoxHeading">特売価格がある場合は特売価格を入力してください。
                税抜き価格を入力してください。特売価格を入力した場合には商品価格が次のように表示されます。<br> 
                例）商品価格に5000と入力し、特売価格に4000と入力した場合　<s>5,000</s>円&nbsp;<span class="specialPrice">4,000円</span>               <br>
                <span class="fieldRequired">空白でも可。</span></td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">K</td>
                <td class="menuBoxHeading">数量（在庫数）</td>
                <td class="menuBoxHeading"><a href="configuration.php?gID=9&cID=113&action=edit" target="_blank">在庫水準のチェック</a>をTRUEにしている場合は、在庫管理在庫数を入力してください。<br>
                空白でも可、数字のみ可。</td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">L</td>
                <td class="menuBoxHeading">発売日</td>
                <td class="menuBoxHeading">2005/01/26　のようにYYYY/mm/dd　形式で入力してください。<br>
                空白の場合は本日の日付（<?php echo date("Y/m/d");?>）が登録されます。<span class="fieldRequired">本日の日付よりも未来の日付を入力した場合</span>はトップページWhat'sNew(新着情報)に自動的に反映されます。<br>
                空白でも可。</td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">M</td>
                <td class="menuBoxHeading">在庫ステータス<span class="fieldRequired">*</span></td>
                <td class="menuBoxHeading">商品を表示する場合は１、非表示にする場合は0を入力してください。</td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">N</td>
                <td class="menuBoxHeading">関連URL</td>
                <td class="menuBoxHeading">商品に関連するページがある場合はhttp://を除く、www.xxx...から入力してください。<br>
                <span class="fieldRequired">空白でも可。半角英数255文字、日本語122文字まで入力可能。</span></td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">O</td>
                <td class="menuBoxHeading">重量</td>
                <td class="menuBoxHeading">送料を重量を基準に計算する場合はヤマト、佐川の料金表を元にキログラムの単位で入力してください。<br>
                <span class="fieldRequired">空白でも可。数字のみ。</span></td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">P</td>
                <td class="menuBoxHeading">項目１</td>
                <td class="menuBoxHeading">JANコードを入力してください。<br>
                  日本語・英数字全て可。<span class="fieldRequired">文字数無制限。</span></td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">Q</td>
                <td class="menuBoxHeading">項目２</td>
                <td class="menuBoxHeading">サイズを入力してください<br>
                  日本語・英数字全て可。<span class="fieldRequired">文字数無制限。</span></td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">R</td>
                <td class="menuBoxHeading">項目３</td>
                <td class="menuBoxHeading">内容量を単位つきで入力してください（例：50Kg）<br>
                  日本語・英数字全て可。<span class="fieldRequired">文字数無制限。</span></td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">S</td>
                <td class="menuBoxHeading">項目４</td>
                <td class="menuBoxHeading">材質を入力してください。<br>
                  日本語・英数字全て可。<span class="fieldRequired">文字数無制限。</span></td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">T</td>
                <td class="menuBoxHeading">税種別<span class="fieldRequired">*</span></td>
                <td class="menuBoxHeading">税込表示は1、税抜き表示は0。</td>
              </tr>
              <tr>
                <td align="center" class="infoBoxContent">U</td>
                <td class="menuBoxHeading">項目５</td>
                <td class="menuBoxHeading">備考を入力してください。<br>
                  <span class="fieldRequired">空白でも可。HTML使用可、文字数無制限。</span></td>
              </tr>
            </table></td>
          </tr>


<!-- body_text_eof //-->
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- body_eof //-->
<?php
}
?>

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php 

  require(DIR_WS_INCLUDES . 'application_bottom.php');


?>
