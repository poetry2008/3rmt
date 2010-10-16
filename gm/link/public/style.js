function $_(eleId){
  return document.getElementById(eleId);
}
<!--
function ch_type(sel){
// onchangeでウィンドウを開く方法を選択
var form=document.getElementById('form1');
var open_type=sel.options[sel.selectedIndex].value;
if(open_type==1){form.target="_blank";}
else{form.target="";}
}
//-->
function allSite(eleid,ele2){
	//eles = document.getElementsByName('more');
	eles = getElementsByName_iefix('div','more');
	ele2s = getElementsByName_iefix('span','imag');
	for(j in ele2s){
		//alert(ele2s[j].className);
		ele2s[j].className = 'sp-show';
	}
	//alert('run');
	for(i in eles){
		//alert(eles[i].id);
		if(eles[i].style){
			if(eles[i].id != eleid){
				eles[i].style.display = 'none';
			}
		}
	}
	soh(eleid,ele2);
}
function soh(id,ele2){
	ele = document.getElementById(id);
	//alert(self.childNodes[0]);
	if(ele.style.display=='block'){
		ele.style.display = 'none';
		if(ele2){
			ele2.className = 'sp-show';
		}
	}else{
		ele.style.display = 'block';
		if(ele2){
			ele2.className = 'sp-close';
		}
	}
}
function getElementsByName_iefix(tag, name) {
	var elem = document.getElementsByTagName(tag);
	var arr = new Array();
	for(i = 0,iarr = 0; i < elem.length; i++) {
	att = elem[i].getAttribute("name");
	if(att == name) {
	arr[iarr] = elem[i];
	iarr++;
	}
	}
	return arr;
}
function checkSm(search_url,site_id)
{
  var error = false;
  var error_message = '記入ミス' + ':';
  //fname
  var fname = document.getElementById('content_fname');
  if (fname.value == '')
  {
    error = error || true;
    error_message += '\nお名前は記入必須項目です';
  }
  else if (fname.value.length>25)
  {
    error = error || true;
    error_message += '\nお名前文字数オーバー';
  }

  //femail
  var femail = document.getElementById('content_femail');
  var email_reg = /[\w\.-]+(\+[\w-]*)?@([\w-]+\.)+[\w-]+/;
  var email_patt = new RegExp(email_reg);

  if (femail.value == '')
  {
    error = error || true;
    error_message += '\nメールアドレスは記入必須項目です';
  }
  else if (femail.value.length>40)
  {
    error = error || true;
    error_message += '\nメールアドレス文字数オーバー';
  }
  else if (!email_patt.exec(femail.value))
  {
    error = error || true;
    error_message += '\nメールアドレスの入力が正しくありません';
  }
    


  //pass
  if(document.getElementById('content_fpass')&&document.getElementById('content_fpass')){
  var pass = document.getElementById('content_fpass');
  var pass2 = document.getElementById('content_fpass');
  if (pass.value == '')
  {
    error = error || true;
    error_message += '\nパスワードは記入必須項目です';
  }
  else if (pass.value != pass2.value)
  {
    error = error || true;
    error_message += '\n２回のパスワード入力が一致しませんでした';
  }
  else
  {
    var pass_reg = /[\wa-zA-Z]{1,8}/;
    var pass_patt = new RegExp(pass_reg);
    if (!pass_patt.exec(pass.value))
    {
      error = error || true;
      error_message += '\nパスワードの入力が正しくありません';
    }
  }
  }

  //name
  var name = document.getElementById('content_name');
  if (name.value == '')
  {
    error = error || true;
    error_message += '\nホームページのタイトルは記入必須項目です';
  }
  else if (name.value.length>25)
  {
    error = error || true;
    error_message += '\nホームページのタイトル文字数オーバー';
  }

  //url
  var url = document.getElementById('content_url');
  var url_reg = /http:\/\/[-\w\.\/]+\.[-\w\.\/]+/;
  var url_patt = new RegExp(url_reg);
  if (url.value == '')
  {
    error = error || true;
    error_message += '\nURLは記入必須項目です';
  }
  else if (url.value.length>130)
  {
    error = error || true;
    error_message += '\nURLの文字数オーバー';
  }
  else if (!url_patt.exec(url.value))
  {
    error = error || true;
    error_message += '\nURLの入力が正しくありません';
  }
  else 
  {
    url_duplicate = checkUrl(search_url);

   if (url_duplicate == '2')
   { 
     error = error || true;
     error_message += '\nそのURLはすでに登録されています';
   }
   if (url_duplicate == '3'){
     
     error = error || true;
     error_message += '\nコードエラー';
   }
  }

  //comment
  var comment = document.getElementById('content_comment');
  if (comment.value == '')
  {
    error = error || true;
    error_message += '\nホームページの紹介文は記入必須項目です';
  }
  else if (comment.value.length>100)
  {
    error = error || true;
    error_message += '\nホームページの紹介文文字数オーバー';
  }

  //linkpage_url
  var linkpage_url = document.getElementById('content_linkpage_url');
  if (linkpage_url.value == '')
  {
    error = error || true;
    error_message += '\nリンクURLは記入必須項目です';
  }
  else if (linkpage_url.value.length>130)
  {
    error = error || true;
    error_message += '\nリンクURLの文字数オーバー';
  }
  else
  {
    var linkpage_url_reg = /http:\/\/[-\w\.\/]+\.[-\w\.\/]+/;
    var linkpage_url_patt = new RegExp(linkpage_url_reg);
    if (!linkpage_url_patt.exec(linkpage_url.value))
    {
      error = error || true;
      error_message += '\nリンクURLの入力が正しくありません';
    }
  }

  if (error)
  {
    alert(error_message);
    return false;
  }
  else
  {
    return true;
  }
}

function checkUrl(_search_url,site_id)
{
  var urlstate = '';
  var a_url = _search_url;
  var new_url = document.getElementById('content_url');
  if(document.getElementById('image_code')){
  var imgcode = document.getElementById('image_code');
  data_val = "url="+new_url.value+"&image_code="+imgcode.value;
  }else{
  data_val = "url="+new_url.value+"&site_id="+site_id;
  }
  new_url = encodeURI(new_url.value);
  var urlCheck = $.ajax({
async:false,
type:"POST",
dataType: "text",
url: a_url,
data: data_val,
beforeSend: function(){},
error: function (XMLHttpRequest, textStatus, errorThrown){},
success: function (msg){}
});
return urlCheck.responseText;
}


  function checkPa()
{
  var error = false;
  var error_message = '記入ミス' + ':';

 // pass
  var pass = document.getElementById('new_pass');
  var pass2 = document.getElementById('new_pass2');
  if (pass.value == '')
  {
   error = error || true;
    error_message += '\nパスワードは記入必須項目です';
  }
  else if (pass.value != pass2.value)
  {
   error = error || true;
    error_message += '\n２回のパスワード入力が一致しませんでした';
  }
  else
  {
   var pass_reg = /[\wa-zA-Z]{1,8}/;
   var pass_patt = new RegExp(pass_reg);
   if (!pass_patt.exec(pass.value))
   {
    //  error = error || true;
     error_message += '\nパスワードの入力が正しくありません';
   }
  }

  if (error)
  {
    alert(error_message);
    return false;
  }
  else
  {
    return true;
  }
}

/*
function checkSm()
{
  var error = false;
  var error_message = '記入ミス' + ':';
  //fname
  var fname = document.getElementById('content_fname');
  if (fname.value == '')
  {
    error = error || true;
    error_message += '\nお名前は記入必須項目です';
  }
  else if (fname.value.length>25)
  {
    error = error || true;
    error_message += '\nお名前文字数オーバー';
  }

  //femail
  var femail = document.getElementById('content_femail');
  if (femail.value == '')
  {
    error = error || true;
    error_message += '\nメールアドレスは記入必須項目です';
  }
  else if (femail.value.length>40)
  {
    error = error || true;
    error_message += '\nメールアドレス文字数オーバー';
  }
  else
  {
    var email_reg = /[\w\.-]+(\+[\w-]*)?@([\w-]+\.)+[\w-]+/;
    var email_patt = new RegExp(email_reg);
    if (!email_patt.exec(femail.value))
    {
      error = error || true;
      error_message += '\nメールアドレスの入力が正しくありません';
    }
  }

  //pass
  //var pass = document.getElementById('content_fpass');
  //var pass2 = document.getElementById('content_fpass');
  //if (pass.value == '')
  //{
   // error = error || true;
    //error_message += '\nパスワードは記入必須項目です';
  //}
  //else if (pass.value != pass2.value)
  //{
   // error = error || true;
    //error_message += '\n２回のパスワード入力が一致しませんでした';
  //}
  //else
  //{
   // var pass_reg = /[\wa-zA-Z]{1,8}/;
   // var pass_patt = new RegExp(pass_reg);
   // if (!pass_patt.exec(pass.value))
   // {
    ////  error = error || true;
     // error_message += '\nパスワードの入力が正しくありません';
   // }
  //}

  //name
  var name = document.getElementById('content_name');
  if (name.value == '')
  {
    error = error || true;
    error_message += '\nホームページのタイトルは記入必須項目です';
  }
  else if (name.value.length>25)
  {
    error = error || true;
    error_message += '\nホームページのタイトル文字数オーバー';
  }

  //url
  var url = document.getElementById('content_url');
  if (url.value == '')
  {
    error = error || true;
    error_message += '\nURLは記入必須項目です';
  }
  else if (url.value.length>130)
  {
    error = error || true;
    error_message += '\nURLの文字数オーバー';
  }
  else
  {
    var url_reg = /http:\/\/[-\w\.\/]+\.[-\w\.\/]+/;
    var url_patt = new RegExp(url_reg);
    if (!url_patt.exec(url.value))
    {
      error = error || true;
      error_message += '\nURLの入力が正しくありません';
    }
  }

  //comment
  var comment = document.getElementById('content_comment');
  if (comment.value == '')
  {
    error = error || true;
    error_message += '\nホームページの紹介文は記入必須項目です';
  }
  else if (comment.value.length>100)
  {
    error = error || true;
    error_message += '\nホームページの紹介文文字数オーバー';
  }

  //linkpage_url
  var linkpage_url = document.getElementById('content_linkpage_url');
  if (linkpage_url.value == '')
  {
    error = error || true;
    error_message += '\nリンクURLは記入必須項目です';
  }
  else if (linkpage_url.value.length>130)
  {
    error = error || true;
    error_message += '\nリンクURLの文字数オーバー';
  }
  else
  {
    var linkpage_url_reg = /http:\/\/[-\w\.\/]+\.[-\w\.\/]+/;
    var linkpage_url_patt = new RegExp(linkpage_url_reg);
    if (!linkpage_url_patt.exec(linkpage_url.value))
    {
      error = error || true;
      error_message += '\nリンクURLの入力が正しくありません';
    }
  }

  //to_admin
  if(document.getElementById('content_to_admin')){
  var to_admin = document.getElementById('content_to_admin');
  if (to_admin.value.length>200)
  {
    error = error || true;
    error_message += '\n管理人へのメッセージ文文字数オーバー';
  }
  }

  if (error)
  {
    alert(error_message);
    return false;
  }
  else
  {
    return true;
  }
}
*/
function valsearch(){
  if(document.getElementById('search_txt')
      &&document.getElementById('search_txt').value!=''){
    return true;
  }
  alert('検索キーワードを入力してください');
  return false;
}
function check_pre_Sm()
{

  var error = false;
  var error_message = '<!--{_t key="please check:"}-->';

  //comment
  var comment = document.getElementById('content_comment');
  if (comment.value.length < 1 || comment.value.length > 100)
  {
    error = error || true;
    error_message += '\n<!--{_t key="comment_error"}-->';
  }



  if (error)
  {
    alert(error_message);
    return false;
  }
  else
  {
    return true;
  }
}
