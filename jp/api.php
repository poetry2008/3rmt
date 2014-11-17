 <?php
/*
  $Id$

*/

  require('includes/application_top.php');
  
  function api_log($msg = ''){
    if (1) {
      $fp = fopen(DIR_FS_DOCUMENT_ROOT . 'api.log', 'w+');
      fwrite($fp, date('Y-m-d H:i:s') . "\t" . $_SERVER['REMOTE_ADDR']) . ($msg?("\t".$msg):'');
      fclose($fp);
    }
  }
if (isset($_GET['key'])) {
  $keys = explode('|', API_KEYS);
  if (!in_array($_GET['key'], $keys)) {
    api_log('key not exists');
    forward404();
  }
} else {
  api_log('no key');
  forward404();
}

if (isset($_GET['action']) && $_GET['action'] == 'sp' && $_GET['keyword']) {
  api_log($_GET['keyword']);
  $keyword = $_GET['keyword'];
  $limit   = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
  
  $sql = "
    select * 
    from (
      select p.*,
             pd.products_name,
             pd.products_description,
             pd.products_status, 
             pd.site_id
      from ( " . TABLE_PRODUCTS . " p ) left join " . TABLE_MANUFACTURERS . " m using(manufacturers_id), " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_CATEGORIES . " c, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
      where p.products_id = pd.products_id 
        and pd.language_id = '" . $languages_id . "' 
        and p.products_id = p2c.products_id 
        and p2c.categories_id = c.categories_id 
  ";
  
  if (isset($_GET['keyword']) && tep_not_null($_GET['keyword'])) {
    if (tep_parse_search_string(stripslashes($_GET['keyword']), $search_keywords)) {
      $where_str .= " and (";
      for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
        switch ($search_keywords[$i]) {
          case '(':
          case ')':
          case 'and':
          case 'or':
            $where_str .= " " . $search_keywords[$i] . " ";
            break;
          default:
            $where_str .= "(pd.products_name like '%" . addslashes($search_keywords[$i]) . "%' or p.products_model like '%" . addslashes($search_keywords[$i]) . "%' or m.manufacturers_name like '%" . addslashes($search_keywords[$i]) . "%'";
            if (isset($_GET['search_in_description']) && ($_GET['search_in_description'] == '1')) $where_str .= " or pd.products_description like '%" . addslashes($search_keywords[$i]) . "%'";
              $where_str .= ')';
            break;
        }
      }
      $where_str .= " )";
    }
  }

  $sql .= $where_str;
  $sql .= " order by pd.site_id desc";
  $sql .= "
    ) p 
    where site_id = 0
       or site_id = ".SITE_ID."
    group by products_id
    having p.products_status != '0' and p.products_status != '3' 
    order by sort_order,products_name
    limit ".$limit."
    ";
  $result_query = tep_db_query($sql);

header ("content-type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8" ?><?xml-stylesheet href="http://www.w3.org/2000/08/w3c-synd/style.css" type="text/css" encoding="UTF-8"?>'."\n";
echo "<result>\n";
  while ($result = tep_db_fetch_array($result_query)) {
?>
<product>
  <id><?php echo $result['products_id'];?></id>
  <name><?php echo $result['products_name'];?></name>
  <description><![CDATA[<?php echo replace_store_name($result['products_description']);?>]]></description>
  <url><?php echo HTTP_SERVER.'/item/p-'.$result['products_id'].'.html';?></url> 
  <quantity><?php echo $result['products_quantity'];?></quantity>
  <price><?php echo
  $currencies->display_price(tep_get_price($result['products_price'],
        $result['products_price_offset'], $result['products_small_sum'],
        $result['products_bflag']), tep_get_tax_rate($result['products_tax_class_id']));?></price>
</product>
<?php
  }
echo "</result>\n";


}
if(isset($_GET['action'])&&$_GET['action']=='clt'&& $_GET['cpath']){
  api_log($_GET['keyword']);

  $cpath = $_GET['cpath'];
  $sql = "select p.* from " . TABLE_PRODUCTS . " as p left jion  " . TABLE_PRODUCTS_DESCRIPTION . " pd on pd.products_id=p.products_id where p.products_id in('". $product_id_list ."')";

        $listing_sql = "
          select * from ( select
                 p.products_id, 
                 p.products_price, 
                 p.products_price_offset, 
                 p.products_small_sum, 
                 p.products_tax_class_id, 
                 pd.site_id,
                 pd.products_name
                 from " . TABLE_PRODUCTS . " p, " .  TABLE_PRODUCTS_DESCRIPTION . "
                   pd, " .TABLE_PRODUCTS_TO_CATEGORIES . " p2c
                   where  p.products_id = p2c.products_id 
                   and pd.products_id = p2c.products_id 
                   and p2c.categories_id = '" . $cpath . "' ) p
                   where site_id = '0'";
  
  $result_query = tep_db_query($listing_sql);

header ("content-type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8" ?><?xml-stylesheet href="http://www.w3.org/2000/08/w3c-synd/style.css" type="text/css" encoding="UTF-8"?>'."\n";
echo "<result>\n";
  while ($result = tep_db_fetch_array($result_query)) {
	  $p_bflag = tep_get_bflag_by_product_id($listing['products_id']);
?>
<product>
  <name><?php echo $result['products_name'];?></name>
  <price><?php 
	 echo $currencies->display_price(tep_get_price($listing['products_price'],
		  $listing['products_price_offset'], $listing['products_small_sum'],
		  $p_bflag), tep_get_tax_rate($listing['products_tax_class_id']))

?></price>
  <quantity><?php echo tep_show_quantity(tep_get_quantity($result['products_id'],true))?></quantity>
</product>
<?php
  }
echo "</result>\n";


}else if(isset($_GET['action'])&&$_GET['action']=='get_parent_category'){
$categories = array();

$categories_query = tep_db_query("
    select * 
    from (
      select c.categories_id, 
             cd.categories_name, 
             cd.categories_status, 
             c.parent_id,
             cd.site_id,
             c.sort_order
      from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
      where c.parent_id = '0' 
        and c.categories_id = cd.categories_id 
        and cd.language_id='" . $languages_id ."' 
      order by site_id DESC
    ) c 
    where site_id = ".SITE_ID."
       or site_id = 0
    group by categories_id
    having c.categories_status != '1' and c.categories_status != '3' 
    order by sort_order, categories_name
");
while ($category = tep_db_fetch_array($categories_query))  {
  $categories[] = $category;
}
echo json_encode($categories);
}
