function change_sort_type(sort_type)
{
  document.cookie = "sort="+sort_type;
  window.location.reload();
}