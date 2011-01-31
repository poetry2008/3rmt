function change_sort_type(sort_type)
{
  document.cookie = "sort="+sort_type;
  var url = window.location.href;
  url = url.replace(/page\-\d+\.html/g,'');
  window.location.href=url;
}
