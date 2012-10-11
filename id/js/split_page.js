function jump_page(j_ele, j_total_page, j_c_page)
{
  var jump_page = j_ele.parentNode.childNodes[1].value;
  if (jump_page.match(/\d+/)) {
    if ((parseInt(jump_page, 10) <= parseInt(j_total_page, 10)) && (parseInt(jump_page, 10) != parseInt(j_c_page, 10)) && (parseInt(jump_page, 10) > 0)) {
       j_ele.parentNode.submit();
    }
  }
}

function page_change(j_page)
{
  j_page = j_page.replace("||||", "'"); 
  j_page = j_page.replace('>>>>', '"'); 
  window.location.href = j_page;
}
