<?php   
      //<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
//Step 1. Construct the general Query!

$metaQuery  = "SELECT `products_description`.`products_name`, `categories_description`.`categories_name`, `manufacturers`.`manufacturers_name` ";
$metaQuery .= "FROM products, products_description, products_to_categories, categories, categories_description, ".TABLE_LANGUAGES.", manufacturers, ".TABLE_CONFIGURATION." ";
$metaQuery .= "WHERE products.products_id = products_description.products_id ";
$metaQuery .= "AND products_description.language_id = ".TABLE_LANGUAGES.".languages_id ";
$metaQuery .= "AND products_description.products_id = products_to_categories.products_id ";
$metaQuery .= "AND products_to_categories.categories_id = categories.categories_id ";
$metaQuery .= "AND categories.categories_id = categories_description.categories_id ";
$metaQuery .= "AND categories_description.language_id = ".TABLE_LANGUAGES.".languages_id ";
$metaQuery .= "AND products.manufacturers_id = manufacturers.manufacturers_id ";
$metaQuery .= "AND products.products_status = 1 ";
$metaQuery .= "AND ".TABLE_CONFIGURATION.".configuration_key = 'DEFAULT_LANGUAGE' ";
$metaQuery .= "AND ".TABLE_LANGUAGES.".code = ".TABLE_CONFIGURATION.".configuration_value ";


//Step 2. Narrow the search!

//Are we looking within a category?
if (isset($cPath) && tep_not_null($cPath)) {
	$metaKeywords = $seo_category['meta_keywords'];
	$metaDescription = $seo_category['meta_description'];
	
} else {
	
	//Are we looking within a manufacturer?
	if (isset($manufacturers_id) && tep_not_null($manufacturers_id))
	{
	
		$metaManufacturersId = $manufacturers_id;
	
		$metaQuery .= "AND manufacturers.manufacturers_id = '" . $metaManufacturersId . "' ";
	}
	
	//Step 3. Extract the info from the DB
	$metaQueryResult = tep_db_query ( $metaQuery );
	
	$metaProductsNames = array();
	$metaCategoriesNames = array();
	$metaManufacturersNames = array();
	
	
	
	//Step 4. Remove duplicates by using the name as the key in an array
	while($metaQueryData = tep_db_fetch_array ($metaQueryResult))
	{
		$metaProductsNames[$metaQueryData['products_name']] = $metaQueryData['products_name'];
		$metaCategoriesNames[$metaQueryData['categories_name']] = $metaQueryData['categories_name'];
		$metaManufacturersNames[$metaQueryData['manufacturers_name']] = $metaQueryData['manufacturers_name'];
	
	}
	
	
	//Step 5. Construct the keywords
	$metaKeywords = "";
	foreach($metaProductsNames as $metaProductsName)
	{
		if($metaKeywords == "")
		{
			//First Row
			$metaKeywords = $metaProductsName;
		}
		else
		{
			//Other Rows
			$metaKeywords .= ", " . $metaProductsName;
		}
	}
	
	foreach($metaCategoriesNames as $metaCategoriesName)
	{
		if($metaKeywords == "")
		{
			//No previous entries
			$metaKeywords = $metaCategoriesName;
		}
		else
		{
			//Other Rows
			$metaKeywords .= ", " . $metaCategoriesName;
		}
	}
	
	//Limit the keywords to 1000 characters
	$metaKeywords = 'RMT,' . mb_substr($metaKeywords, 0, 90);
	
	
	//Step 6. Construct the description
	$metaDescription = "RMT総合サイト RMTワールドマネーへようこそ。";
	$i = 0;
	foreach($metaManufacturersNames as $metaManufacturersName)
	{
		//Limit the decription to 150 words
		if($i >= 149)
		{
			break;
		}
	
		if($i == 0)
		{
			//First Row
			$metaDescription .= $metaManufacturersName;
		}
		else
		{
			//Other Rows
			$metaDescription .= ", " . $metaManufacturersName;
		}
	
		$i++;
	}
}

echo '<meta name="keywords" content="' . $metaKeywords . '">' . "\n";
echo '<meta name="description" content="' . $metaDescription . '">' . "\n";
?>
