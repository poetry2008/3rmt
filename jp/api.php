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
                 p.relate_products_id, 
                 p.products_real_quantity,
                 p.products_attention_1_3,
                 p.products_attention_1_4,
                 p.products_id, 
                 p.products_price, 
                 p.products_price_offset, 
                 p.products_small_sum, 
                 p.products_tax_class_id, 
                 p.products_bflag,
                 p.sort_order,
                 p.max_inventory,
                 p.min_inventory,
                 pd.site_id,
                 pd.products_name,
                 pd.products_status
                 from " . TABLE_PRODUCTS . " p, " .  TABLE_PRODUCTS_DESCRIPTION . "
                   pd, " .TABLE_PRODUCTS_TO_CATEGORIES . " p2c
                   where  p.products_id = p2c.products_id 
                   and pd.products_id = p2c.products_id 
                   and p2c.categories_id = '" . $cpath . "' order by pd.site_id desc) p
                   where site_id = '0' or site_id = '1' group by products_id having p.products_status=1 order by sort_order,products_name,products_id";
  
  $result_query = tep_db_query($listing_sql);

header ("content-type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8" ?><?xml-stylesheet href="http://www.w3.org/2000/08/w3c-synd/style.css" type="text/css" encoding="UTF-8"?>'."\n";
echo "<result>\n";
  while ($result = tep_db_fetch_array($result_query)) {
    $p_bflag = tep_get_bflag_by_product_id($listing['products_id']);
    //根据库存来获取商品价格
    if (tep_get_special_price($result['products_price'], $result['products_price_offset'], $result['products_small_sum'])) {
      $pricedef = $result['products_price'];
      $products_price = $currencies->display_price(tep_get_special_price($result['products_price'], $result['products_price_offset'], $result['products_small_sum']), tep_get_tax_rate($result['products_tax_class_id']));
    } else {
      
      $pricedef = $result['products_price'];
      $products_price = $currencies->display_price(tep_get_price($result['products_price'], $result['products_price_offset'], tep_get_price($result['products_price'],$result['products_small_sum'], '', $result['products_bflag']), $result['products_bflag']), tep_get_tax_rate($result['products_tax_class_id']));
    }

    if(tep_not_null($result['products_small_sum'])) {
      $wari_array = array();
      $parray = explode(",", $result['products_small_sum']);
      for($i=0; $i<sizeof($parray); $i++) {
        $tt = explode(':', $parray[$i]);
        $wari_array[$tt[0]] = $tt[1];
      }                    
      @krsort($wari_array);

      $products_price = $currencies->display_price(round($pricedef + current($wari_array)),0); 
    }
    $rate = $result['products_attention_1_3'];
    $rate_other  = $result['products_attention_1_4'];
    $res=tep_db_query("select * from set_auto_calc where parent_id='".$cpath."'");
    $cacl = 0;
    if($col=tep_db_fetch_array($res)){
      $cacl = $col['bairitu'];
    }
    $pInfo = $result;
    $max_inventory_array = explode('|||',$pInfo['max_inventory']);
    $min_inventory_array = explode('|||',$pInfo['min_inventory']);
    $max_inventory_num_1 = tep_inventory_operations($max_inventory_array[0],$pInfo['products_id'],$site_id);
    $max_inventory_num_2 = tep_inventory_operations($max_inventory_array[1],$pInfo['products_id'],$site_id);
    $min_inventory_num_1 = tep_inventory_operations($min_inventory_array[0],$pInfo['products_id'],$site_id);
    $min_inventory_num_2 = tep_inventory_operations($min_inventory_array[1],$pInfo['products_id'],$site_id);
    if($max_inventory_array[2] == 'min'){

      $max_inventory_num = $max_inventory_num_1 < $max_inventory_num_2 ? $max_inventory_num_1 : $max_inventory_num_2;
    }else{
      $max_inventory_num = $max_inventory_num_1 > $max_inventory_num_2 ? $max_inventory_num_1 : $max_inventory_num_2;
    }
  
    if($min_inventory_array[2] == 'min'){

      $min_inventory_num = $min_inventory_num_1 < $min_inventory_num_2 ? $min_inventory_num_1 : $min_inventory_num_2;
    }else{
      $min_inventory_num = $min_inventory_num_1 > $min_inventory_num_2 ? $min_inventory_num_1 : $min_inventory_num_2;
    }
    $max_inventory_num = $max_inventory_num < 0 ? 0 : $max_inventory_num;
    $min_inventory_num = $min_inventory_num < 0 ? 0 : $min_inventory_num;
    $inventory['max'] = $max_inventory_num;
    $inventory['min'] = $min_inventory_num;


    $avg = 0;
    if (!$pInfo['products_bflag'] && $pInfo['relate_products_id']) {
      $avg = tep_new_get_avg_by_pid($pInfo);
    }else{
      $relate_pInfo = tep_get_pinfo_by_pid($pInfo['relate_products_id'], SITE_ID);
      $avg = $relate_pInfo['products_price'];
    }
    if ($pInfo['products_bflag'] == 1){
      $relate_pInfo = tep_get_pinfo_by_pid($pInfo['relate_products_id'], SITE_ID);
      $avg = $relate_pInfo['products_price'];
    }
?>
<product>
  <name><?php echo $result['products_name'];?></name>
  <price><?php echo $products_price;?></price>
  <quantity><?php echo tep_show_quantity(tep_get_quantity($result['products_id'],true))?></quantity>
  <rate><?php echo $rate;?></rate>
  <rate_other><?php echo $rate_other;?></rate_other>
  <cacl><?php echo $cacl;?></cacl>
  <max><?php echo $inventory['max'];?></max>
  <min><?php echo $inventory['min'];?></min>
  <avg><?php echo $avg;?></avg>
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
    where site_id = '0' 
    or site_id = '1' 
    group by categories_id
    having c.categories_status = '0' 
    order by sort_order, categories_name
");
while ($category = tep_db_fetch_array($categories_query))  {
  $categories[] = $category;
}
echo json_encode($categories);
}
