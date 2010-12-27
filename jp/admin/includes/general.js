function SetFocus() {
  if (document.forms.length > 0) {
    var field = document.forms[0];
    for (i=0; i<field.length; i++) {
      if ( (field.elements[i].type != "image") && 
           (field.elements[i].type != "hidden") && 
           (field.elements[i].type != "reset") && 
           (field.elements[i].type != "submit") ) {

        document.forms[0].elements[i].focus();

        if ( (field.elements[i].type == "text") || 
             (field.elements[i].type == "password") )
          document.forms[0].elements[i].select();
        
        break;
      }
    }
  }
}


function submitChk() { 
    /* 確認ダイアログ表示 */ 
    var flag = confirm ( "確認はしましたか？\n\n【 重要 】価格構成要素を変更した場合は、先に「注文内容確認」ボタンを押す必要があります。\n\n戻る場合は [キャンセル] ボタンをクリックしてください。"); 
    /* send_flg が TRUEなら送信、FALSEなら送信しない */ 
    return flag; 
} 

function update_price() {
	
	if (window.confirm("注文内容を確認しますか？")) {
		document.edit_order.notify.checked = false;
		document.edit_order.notify_comments.checked = false;
		document.edit_order.submit();
		window.alert("注文内容を更新しました。合計金額を必ず確認してください。\n\n【 重要 】メールは送信されていません。【 重要 】");
		document.edit_order.notify.checked = true;
		document.edit_order.notify_comments.checked = false;
	} else {
		window.alert("注文内容確認をキャンセルしました。\n\n【 重要 】メールは送信されていません。【 重要 】");
	}

}
