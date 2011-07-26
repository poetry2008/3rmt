//引用JQUERY 文件
if (typeof window.jQuery == "undefined") {
  document.write('<script language="javascript" src="includes/javascript/jquery.js"><\/script>');
}
function in_array(needle, haystack) {
  if(typeof needle == 'string' || typeof needle == 'number') {
    for(var i in haystack) {
      if(haystack[i] == needle) {
        return true;
      }   
    }   
  }
  return false;
}
