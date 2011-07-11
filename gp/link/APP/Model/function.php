<?php
//エラー表示
function error($message,$backurl){
	header("HTTP/1.0 404 Not Found");
    include("APP/View/Seoplink/elfjlrror.html");
	footer(site_title);
}

//フッター表示
function footer($site_title){
	$html_powered = "PGJyIC8+UG93ZXJlZCBCeSA8YSBocmVmPSJodHRwOi8vc2VvdXAubmV0LyI+55u45LqS44Oq44Oz44KvU0VPLVAtTGluayB2ZXIzLjQ8L2E+IC0gdGhhbmtzIDogPGEgaHJlZj0iaHR0cDovL3Nlby1iZWF0LmNvbS8iPuebuOS6kuODquODs+OCr0ZpbmFsPC9hPjwvZGl2Pg==";
	$html_copyrigh_top = "PGRpdiBpZD0iY29weXJpZ2h0Ij5Db3B5cmlnaHQgQnkg";
	$html_copyrigh_top = base64_decode($html_copyrigh_top).$site_title.base64_decode($html_powered);
	$html_copyrigh = "<div id=\"copyright\">
	Copyright By ".$site_title."</div>";

    $index_page = substr($_SERVER["SCRIPT_NAME"], -9 ,5);
    
	if( $index_page == 'index'){
        echo $html_copyrigh_top;
    } else{
		if(empty($GET["cate"]) && substr($_SERVER["SCRIPT_FILENAME"], -9 ,5) =='index'){
	        echo $html_copyrigh_top;
		}else{
        	echo $html_copyrigh;
		}
    }
    echo "</body>";
    echo "</html>";
}

// 管理パスワード認証処理
function password_check() {
	if (md5(admin_password) != $_SESSION["password"]) {
		error("パスワードが違います","admin.php");
		exit;
	}
}

//GET、POSTデータコンバート
function data_convert($data){
    if(get_magic_quotes_gpc()){
		$data = mb_convert_encoding(stripslashes($data),"UTF-8","auto");
	} else{
	    $data = mb_convert_encoding($data,"UTF-8","auto");
	}
	$data = str_replace("\r\n","\n",$data);
	$data = str_replace("\r","\n",$data);
	$data = str_replace("&lt;br&gt;","\n",$data);
	$data = str_replace("\n","<br />",$data);
	$data = str_replace(",","、",$data);
    $data  = strip_tags($data);
    $data  = htmlspecialchars($data);
    return $data;
}

//リンクチェック
function linkcheck($url,$linkpage_url,$admin_mode = true){
		$bln = array();
		$bln[error_message] = "";
		$my_site_url = site_url;
		$anchor = site_title;
        $linktag = '&lt;a href=&quot;'.$my_site_url.'&quot; tagpet=&quot;_blank&quot;&gt;'.$anchor.'&lt;/a&gt;';
		$UnixSockString = "";
		
		// 相互リンク設置URLのhttp://を削除
		$ChkrelinkURL = str_replace("http://", "", $linkpage_url);
		
		// 相互リンク設置URLのHostをゲット
		$Host = substr($ChkrelinkURL, 0, strpos($ChkrelinkURL, "/"));
		
		// 相互リンク設置URLのPathをゲット
		$Path = substr($ChkrelinkURL, strpos($ChkrelinkURL, "/"));
		
		if(strpos($Path, "/") == "0"){
			$Path_check = substr($Path, 1);
		} else{
			$Path_check = $Path;
		}
			
		
		// サイトURLのhttp://を削除
		$site_url_nonhttp = str_replace("http://", "", $url);

		// サイトURLのHostをゲット
		$site_url_host = substr($site_url_nonhttp, 0, strpos($site_url_nonhttp, "/"));

		// サイトURLURLのPathをゲット
		$site_url_path = substr($site_url_nonhttp, strpos($site_url_nonhttp, "/"));
		
		//自サイトURL
		$my_site_url = str_replace("http://", "", $my_site_url);
		$my_site_url = substr($my_site_url, 0, strpos($my_site_url, "/"));

        // 登録URLとリンク設置URLが同じか確認
        if ($Host != $site_url_host) {
			$bln[state] = false;
			if(!$admin_mode){
            	$bln[error_message] .= "<li>サイトURLと相互リンク設置URLのドメインが違います。</li>";
            	$bln[err_flag] = true;
			}
			return $bln;
			exit;
        }

		//80接続
		$fp = fsockopen($Host, 80, $ErrNo, $ErrStr, 10);
		if (!$fp) {
			$bln[state] = false;
			if(!$admin_mode){
				$bln[error_message] .= "<li>相互リンク先が見つかりません。</li>";
	            $bln[err_flag] = true;
			}
		}
		else {
			// 読み込みのタイムアウト設定
			socket_set_timeout($fp, 2);
			fputs($fp, "GET ". $Path . " HTTP/1.0\r\nHost:" . $Host . "\r\n\r\n");
			while(!feof($fp))
			$UnixSockString.=fgets($fp, 128);
			// タイムアウトしたか調べる
			$stat = socket_get_status($fp);
			if ($stat["timed_out"]) {
				$bln[state] = false;
				if(!$admin_mode){
	                $bln[error_message] .= "<li>相互リンク設置先がタイムアウトしました。</li>";
	                $bln[err_flag] = true;
				}
			}
		}
		fclose($fp);
		$pos = strpos($UnixSockString, $my_site_url);

		//リンク済みの場合True
		if ($pos > 0) {
			$bln[state] = true;
		} else {
			$bln[state] = false;
			if(!$admin_mode){
				$bln[error_message] .= "<li>相互リンクが完了していません。<br />あなた様のサイトへ下記のリンクタグ<br />"."$linktag"."<br />をそのまま貼り付けてください。<br />タグを改変するとリンクされません。</li>";
				$bln[err_flag] = true;
			}
		}

		$UnixSockString2 = "";


		//リンク済みの場合True
		if($bln[state] == true && $url != $linkpage_url){

			//80接続
			$fp2 = fsockopen($site_url_host, 80, $ErrNo, $ErrStr, 10);
			if (!$fp2) {
				$bln[state] = false;
				if(!$admin_mode){
					$bln[error_message] .= "<li>登録サイトが見つかりません。</li>";
					$bln[err_flag] = true;
				}
				return $bln;
				exit;
			}
			else {
				// 読み込みのタイムアウト設定
				socket_set_timeout($fp2, 2);
				fputs($fp2, "GET ". $site_url_path . " HTTP/1.0\r\nHost:" . $site_url_host . "\r\n\r\n");
				while(!feof($fp2))
				$UnixSockString2.=fgets($fp2, 128);
				// タイムアウトしたか調べる
				$stat = socket_get_status($fp2);
				if ($stat["timed_out"]) {
					$bln[state] = false;
					if(!$admin_mode){
						$bln[error_message] .= "<li>登録サイトがタイムアウトしました。</li>";
						$bln[err_flag] = true;
					}
					exit;
				}
			}
			fclose($fp2);
			$pos2 = strpos($UnixSockString2, $Path_check);
			
			//リンク済みの場合True
			if ($pos2 > 0) {
				$bln[state] = true;
			} else {
				$bln[state] = false;
				if(!$admin_mode){
					$bln[error_message] .= $Path_check."<li>登録サイトURLに相互リンク設置URLへのリンクがありません。</li>";
					$bln[err_flag] = true;
				}
				
			}
		
		}
		return $bln;
}
?>
