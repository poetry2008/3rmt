function change_num(ob, targ, quan,a_quan)
{
  var product_quantity = document.getElementById(ob);
  var product_quantity_num = parseInt(product_quantity.value);
  if (targ == 'up')
  { 
    if (product_quantity_num >= a_quan)
    {
      num_value = product_quantity_num;
    } else {
      num_value = product_quantity_num + quan; 
    }
  } else {
    if (product_quantity_num <= 1)
    {
      num_value = product_quantity_num;
    } else { 
      num_value = product_quantity_num - quan;
    }
  }
  product_quantity.value = num_value;
}
