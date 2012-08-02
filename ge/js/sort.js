function change_sort_type(sort_type)
{
  document.cookie = "sort="+sort_type;
  var url = window.location.href;
  url = url.replace(/_page\d+/g,'');
  window.location.href=url;
}
