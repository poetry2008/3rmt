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
    /* ��ǧ��������ɽ�� */ 
    var flag = confirm ( "��ǧ�Ϥ��ޤ�������\n\n�� ���� �۲��ʹ������Ǥ��ѹ��������ϡ���ˡ���ʸ���Ƴ�ǧ�ץܥ���򲡤�ɬ�פ�����ޤ���\n\n������ [����󥻥�] �ܥ���򥯥�å����Ƥ���������"); 
    /* send_flg �� TRUE�ʤ�������FALSE�ʤ��������ʤ� */ 
    return flag; 
} 

function update_price() {
	
	if (window.confirm("��ʸ���Ƥ��ǧ���ޤ�����")) {
		document.edit_order.notify.checked = false;
		document.edit_order.notify_comments.checked = false;
		document.edit_order.submit();
		window.alert("��ʸ���Ƥ򹹿����ޤ�������׶�ۤ�ɬ����ǧ���Ƥ���������\n\n�� ���� �ۥ᡼�����������Ƥ��ޤ��󡣡� ���� ��");
		document.edit_order.notify.checked = true;
		document.edit_order.notify_comments.checked = false;
	} else {
		window.alert("��ʸ���Ƴ�ǧ�򥭥�󥻥뤷�ޤ�����\n\n�� ���� �ۥ᡼�����������Ƥ��ޤ��󡣡� ���� ��");
	}

}
