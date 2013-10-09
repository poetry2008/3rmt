//jump to page
function jump_page(j_ele, j_total_page, j_c_page)
{
  var jump_page = j_ele.parentNode.childNodes[0].value;
  if (jump_page.match(/^\d+$/)) {
    if (parseInt(jump_page, 10) >= 0) {
       j_ele.parentNode.submit();
    }
  }
}
//replace special characters then jump to page
function page_change(j_page)
{
  while (true) {
    if (j_page.indexOf("||||") >= 0) {
      j_page = j_page.replace("||||", "'");
    } else {
      break; 
    }
  }
  while (true) {
    if (j_page.indexOf(">>>>") >= 0) {
      j_page = j_page.replace(">>>>", '"');
    } else {
      break; 
    }
  }
  window.location.href = j_page;
}
