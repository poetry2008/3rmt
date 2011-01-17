<?php
/*
 $Id$
*/

require('includes/application_top.php');
forward404();

$connection = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD)    or die("Couldn't make connection.");
// select database
$db = mysql_select_db(DB_DATABASE, $connection) or die(mysql_error());

if (!isset($_GET['language']) || !$_GET['language']) {
  // ccdd
  $lang_query = tep_db_query("
      select languages_id, 
             code 
      from " . TABLE_LANGUAGES . " 
      where directory = '" . $language . "'
  ");
} else {
  $cur_language = tep_db_output($_GET['language']);
  // ccdd
  $lang_query = tep_db_query("
      select languages_id, 
             code 
      from " . TABLE_LANGUAGES . "
      where code = '" . $cur_language . "'
  ");
}


if (tep_db_num_rows($lang_query)) {
  $lang_a = tep_db_fetch_array($lang_query);
    $lang_code = $lang_a['code'];
    $lang_id = $lang_a['languages_id'];
}

// If the default of your catalog is not what you want in your RSS feed, then
// please change this three constants:
// Enter an appropriate title for your website
define('RSS_TITLE', STORE_NAME);
// Enter your main shopping cart link
define('WEBLINK', HTTP_SERVER.DIR_WS_CATALOG);
// Enter a description of your shopping cart
define('DESCRIPTION', TITLE);
/////////////////////////////////////////////////////////////
//That's it.  No More Editing (Unless you renamed DB tables or need to switch
//to SEO links (Apache Rewrite URL)
/////////////////////////////////////////////////////////////

Header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8" ?>';
echo '<?xml-stylesheet href="http://www.w3.org/2000/08/w3c-synd/style.css" type="text/css" encoding="UTF-8"?>' . "\n";
echo "<!-- RSS for " . STORE_NAME . ", generated on " . date('r') . " -->\n";
?>
<rss version="0.92">
<channel>
<title><?php echo RSS_TITLE; ?></title>
<link><?php echo WEBLINK;?></link>
<description><?php echo DESCRIPTION; ?></description>
<webMaster><?php echo STORE_OWNER_EMAIL_ADDRESS; ?></webMaster>
<language><?php echo $lang_code; ?></language>
<lastBuildDate><?php echo date(r); ?></lastBuildDate>

<?php
// Create SQL statement
if ($_GET['cPath'] != "") {
  $sql = "SELECT p.products_id, products_model, products_image, products_price, products_tax_class_id FROM products p, products_to_categories pc WHERE p.products_id = pc.products_id AND pc.categories_id = '" . $_GET['cPath'] . "' AND products_status != '0' ORDER BY products_id DESC LIMIT " . MAX_DISPLAY_SEARCH_RESULTS;
} else {
  $sql = "SELECT products_id, products_model, products_image, products_price,  products_tax_class_id FROM products WHERE products_status != '0' ORDER BY products_id DESC LIMIT " . MAX_DISPLAY_SEARCH_RESULTS;
}
// Execute SQL query and get result
//ccdd
$sql_result = mysql_query($sql,$connection) or die("Couldn't execute query.");

// Format results by row
while ($row = mysql_fetch_array($sql_result)) {
  $id = $row["products_id"];

  // RSS Links for Ultimate SEO (Gareth Houston 10 May 2005)
  $link = tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $id) ;

  $model = $row["products_model"];
  $image = $row["products_image"];
  $price = $row["products_price"];
  $tax = $row["products_tax_class_id"];
//  Add VAt if product subject to VAT (might not be perfect if you have different VAT zones)
  $sql3 = "SELECT tax_rate FROM ".TABLE_TAX_RATES." WHERE  tax_class_id = " . $tax . " LIMIT 1";
  $sql3_result = mysql_query($sql3,$connection) or die("Couldn't execute query.");
  $row3 = mysql_fetch_array($sql3_result);
  $tax = ($row3["tax_rate"] / 100)+1;
  $price = $price * $tax;
  if ($price=='$0.00') {$price= 'Many price options availably for this product';}  else {
  $price = $currencies->format($price);}

  $sql2 = "SELECT products_name, 
                  products_description 
           FROM ".TABLE_PRODUCTS_DESCRIPTION." 
           WHERE products_id = '$id' 
             AND language_id = '$lang_id' 
             AND (site_id = '".SITE_ID."' or site_id ='0')
           order by site_id DESC
           LIMIT 1
   ";
  //ccdd
  $sql2_result = mysql_query($sql2,$connection) or die("Couldn't execute query.");
  $row2 = mysql_fetch_array($sql2_result);
  
  $name = $row2["products_name"];
  $desc = $row2['products_description'];

// add extra data here
  $name = htmlentities($name, ENT_QUOTES, 'UTF-8');
  $desc = htmlentities(strip_tags($desc), ENT_QUOTES, 'UTF-8');
  $image_url = HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . $image;
  
// Replace HTML entities &something; by real characters
// This should be working but is not on my server
//    $trans_tbl = get_html_translation_table (HTML_ENTITIES);
//    $trans_tbl = array_flip ($trans_tbl);
//    $name = strtr ($name, $trans_tbl);
//    $desc = strtr ($desc, $trans_tbl);

// dumb method , but it works
  $name = str_replace ('&amp;','&',$name);
  $desc = str_replace ('&amp;','&',$desc);
  $name = str_replace ('&eacute;','・',$name);
  $desc = str_replace ('&eacute;','・',$desc);
  $name = str_replace ('&agrave;','・',$name);
  $desc = str_replace ('&agrave;','・',$desc);
  $name = str_replace ('&egrave;','・',$name);
  $desc = str_replace ('&egrave;','・',$desc);
  $name = str_replace ('&acirc;','・',$name);
  $desc = str_replace ('&acirc;','・',$desc);
  $name = str_replace ('&ccedil;','・',$name);
  $desc = str_replace ('&ccedil;','・',$desc);
  $name = str_replace ('&ecirc;','・',$name);
  $desc = str_replace ('&ecirc;','・',$desc);
  $name = str_replace ('&icirc;','・',$name);
  $desc = str_replace ('&icirc;','・',$desc);
  $name = str_replace ('&ocirc;','・',$name);
  $desc = str_replace ('&ocirc;','・',$desc);
  $name = str_replace ('&nbsp;',' ',$name);
  $desc = str_replace ('&nbsp;',' ',$desc);
  
  echo "<item>
  <title>" . htmlspecialchars($name) . "</title>
  <link>" . $link . "</link>
  <description>
" . htmlspecialchars($desc) . "
  </description>
  <price>" . $price . "</price>\n";

  if ($image != "") {
    echo "  <image>
    <url>" . $image_url . "</url>
    <title>" . htmlspecialchars($name) . "</title>
    <link>" . $link . "</link>
  </image>\n";
  }
    echo "</item>\n";
}
// free resources and close connection
mysql_free_result($sql_result);
mysql_close($connection);
?>
</channel>
</rss>
