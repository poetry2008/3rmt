function msg(){
	if(document.login.email_address.value == ""){
		alert("メールアドレスを入力してください");
		document.login.email_address.focus();
		return false;
	}
	if(document.login.password.value == ""){
		alert("パスワードを入力してください");
		document.login.password.focus();
		return false;
	}
}
