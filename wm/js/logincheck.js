function msg(){
	if(document.login.email_address.value == ""){
		alert("�᡼�륢�ɥ쥹�����Ϥ��Ƥ�������");
		document.login.email_address.focus();
		return false;
	}
	if(document.login.password.value == ""){
		alert("�ѥ���ɤ����Ϥ��Ƥ�������");
		document.login.password.focus();
		return false;
	}
}
