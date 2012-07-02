
/**
 * 设置一个Cookie的值
 */
function addCookie(objName,objValue,objHours){
  var str = objName + "=" + escape(objValue);
  if(objHours > 0){
    var date = new Date();
    var ms = objHours*3600*1000;
    date.setTime(date.getTime() + ms);
    str += "; expires=" + date.toGMTString();
  }
  document.cookie = str;		
}

/**
 * 取得一个Cookie的值
 */
function getCookie(objName){
  var arrStr = document.cookie.split("; ");
  for(var i = 0;i < arrStr.length;i ++){
    var temp = arrStr[i].split("=");
    if(temp[0] == objName) return unescape(temp[1]);
  }
  return '';
}

/**
 * 删除一个Cookie
 */
function delCookie(name){
  var date = new Date();
  date.setTime(date.getTime() - 10000);
  document.cookie = name + "=a; expires=" + date.toGMTString();
}

/**
 * 字符串去前后空格
 */
function trim(str){
  return   str.replace(/(^\s*)|(\s*$)/g,"");
}
function checklink(){
  var url = $("input[name='url']").attr('value');
  var linkpage_url = $("input[name='linkpage_url']").attr('value');
  if(!linkpage_url){
    linkpage_url = url;
  }
  $.post("linkcheck.php?controller=Seoplink&action=linkcheckstate",
      { url:url,linkpage_url:linkpage_url},
      function(data){
        if(data!=0){
          $("#checklink_state").attr('src','images/img/success.png');
        }else{
          $("#checklink_state").attr('src','images/img/fail.png');
        }
      }
  );
}
