<?php
/*
  $Id: categories.php,v 1.1.1.1 2003/02/20 01:03:53 ptosh Exp $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2002 osCommerce
  Released under the GNU General Public License
*/

	function tep_show_category($counter) {
		global $foo, $categories_string, $id;
		
		$categories_string .= '		';
		
		if ($foo[$counter]['parent'] == 0) {
			if ($foo[$counter]['level'] == 0) {
				if ( ($id) && (in_array($counter, $id)) ) {
					$categories_string .= '<li class="l_m_category_li2">';
				} else {
					$categories_string .= '<li class="l_m_category_li">';
				}
			}
		} elseif ($foo[$counter]['cnt'] == 'first') {
        if ($foo[$counter]['level'] == 1) {
          $categories_string .= "\n" . '			<ul class="l_m_category_ul2">' . "\n" . '		';
        } else {
          $categories_string .= "\n" . '			<ul class="l_m_category_ul3">' . "\n" . '		';
        }
		}

		if ($foo[$counter]['parent'] != 0) {
      if($foo[$counter]['level'] == 1) {
        $categories_string .= '		<li class="l_m_categories_tree">';
      } else {
        $categories_string .= '		<li class="l_m_categories_tree3">';
      }
			if ($foo[$counter]['cnt'] == 'end') {
				$categories_string .= '<img class="middle" src="images/design/tree_end.gif" width="12" height="8" alt="">';
			} else {
				$categories_string .= '<img class="middle" src="images/design/tree_icon.gif" width="12" height="9" alt="">';
			}
		}
		
		$categories_string .= '<a href="';
		
		if ($foo[$counter]['parent'] == 0) {
			$cPath_new = 'cPath=' . $counter;
		} else {
			$cPath_new = 'cPath=' . $foo[$counter]['path'];
		}
		
		$categories_string .= tep_href_link(FILENAME_DEFAULT, $cPath_new);
		$categories_string .= '">';
		
		if ( ($id) && (in_array($counter, $id)) ) {
			$categories_string .= '<strong>';
		}
		
		// display category name
		$categories_string .= $foo[$counter]['name'];
		
		if ( ($id) && (in_array($counter, $id)) ) {
			$categories_string .= '</strong>';
		}
		
		if (tep_has_category_subcategories($counter)) {
			$categories_string .= '';
		}
		
		$categories_string .= '</a>';
		
		if (SHOW_COUNTS == 'true') {
			$products_in_category = tep_count_products_in_category($counter);
			if ($products_in_category > 0) {
				$categories_string .= '&nbsp;(' . $products_in_category . ')';
			}
		}
		
		if ($foo[$counter]['parent'] == 0) {
			if ( ($id) && (in_array($counter, $id)) ) {
				$categories_string .= '';
			} else {
				$categories_string .= '</li>'."\n";
			}
		} else {
			$categories_string .= '</li>'."\n";
		}
		
		if ($foo[$counter]['parent'] != 0) {
			if ($foo[$counter]['cnt'] == 'end') {
				$categories_string .='			</ul>' . "\n" . '		</li>' . "\n";
			}
		}

		if ($foo[$counter]['next_id']) {
			tep_show_category($foo[$counter]['next_id']);
		}
	}
?>
<!-- categories //-->
<div id="categories">
	<?php //echo tep_image(DIR_WS_IMAGES.'design/box/menu.gif',BOX_HEADING_CATEGORIES,'172','51') . "\n"; ?>
	<img width="172" height="51" alt=" オンラインゲーム 価格" src="images/design/box/menu.gif">
	<ul class="l_m_category_ul">
<?php
	$categories_string = '';
	
	$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '0' and c.categories_id = cd.categories_id and cd.language_id='" . $languages_id ."' order by sort_order, cd.categories_name");
	while ($categories = tep_db_fetch_array($categories_query))  {
		$foo[$categories['categories_id']] = array(
						'name' => $categories['categories_name'],
						'parent' => $categories['parent_id'],
						'level' => 0,
						'path' => $categories['categories_id'],
						'next_id' => false,
						'cnt' => 'next');
	
		if (isset($prev_id)) {
			$foo[$prev_id]['next_id'] = $categories['categories_id'];
		}
		
		$prev_id = $categories['categories_id'];
		
		if (!isset($first_element)) {
			$first_element = $categories['categories_id'];
		}
	}
	
	//echo 'top';
	//print_r($foo);
	//echo 'over';
	
	//------------------------
	if ($cPath) {
		$c_cnt = 0;
		$new_path = '';
		$id = split('_', $cPath);
		reset($id);
		while (list($key, $value) = each($id)) {
			unset($prev_id);
			unset($first_id);
			$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . $value . "' and c.categories_id = cd.categories_id and cd.language_id='" . $languages_id ."' order by sort_order, cd.categories_name");
			$category_check = tep_db_num_rows($categories_query);
			//echo $category_check;
			if ($category_check > 0) {
				$new_path .= $value;
				$rows = array();
				while ($row = tep_db_fetch_array($categories_query)) {
					$rows[] = $row;
				}
				//print_r($rows);
				//$c_cnt = 0;
				if($rows)
				foreach($rows as $row){
					$c_cnt++;
					if ($c_cnt == '1') {
						$c_cnt_name = 'first';
					} elseif ($c_cnt == $category_check) {
						$c_cnt_name = 'end';
					} else {
						$c_cnt_name = 'next';
					}
					$foo[$row['categories_id']] = array(
							'name' => $row['categories_name'],
							'parent' => $row['parent_id'],
							'level' => $key+1,
							'path' => $new_path . '_' . $row['categories_id'],
							'next_id' => false,
							'cnt' => $c_cnt_name);
					
					if (isset($prev_id)) {
						$foo[$prev_id]['next_id'] = $row['categories_id'];
					}
					
					$prev_id = $row['categories_id'];
					
					if (!isset($first_id)) {
						$first_id = $row['categories_id'];
					}
	
					$last_id = $row['categories_id'];
				}
				
				$foo[$last_id]['next_id'] = $foo[$value]['next_id'];
				$foo[$value]['next_id'] = $first_id;
        //print_r($foo);
				$new_path .= '_';
			} else {
				break;
			}
		}
	}
	tep_show_category($first_element); 

	echo $categories_string;
?>
		<li class="l_m_category_li">
			
			<a href="<?php echo tep_href_link('manufacturers.php'); ?>"><?php echo MENU_MU; ?></a>
		</li>
		<li class="l_m_category_li">
			
			<a href="<?php echo tep_href_link(FILENAME_SPECIALS); ?>"><?php echo BOX_HEADING_SPECIALS; ?></a>
		</li>
<?php
	$present_query = tep_db_query("select count(*) as cnt from " . TABLE_PRESENT_GOODS);
	$present_result = tep_db_fetch_array($present_query);
	if($present_result['cnt'] > 0) {
		echo '		<li class="l_m_category_li">
			
			<a href="' . tep_href_link(FILENAME_PRESENT) . '">' . BOX_HEADING_PRESENT . '</a>
		</li>' . "\n";
	}
?>
	</ul>
    <img src="images/design/box/box_bottom_bg_01.gif" width="172" height="14" alt="" >
</div>
<!-- categories_eof //-->


















<?php
$categories = array();
$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '0' and c.categories_id = cd.categories_id and cd.language_id='" . $languages_id ."' order by sort_order, cd.categories_name");
while ($category = tep_db_fetch_array($categories_query))  {
  $categories[] = $category;
}
if($cPath){
  $id = split('_', $cPath);
}
?>

<div id='categories'>
  <img width="172" height="51" alt=" オンラインゲーム 価格" src="images/design/box/menu.gif">
  <ul class='l_m_category_ul'>
    <?php foreach($categories as $category) {?>
      <?php //if($cPath && ($id[0] == $category['categories_id'])) {?>
      <?php if($cPath && in_array($category['categories_id'], $id)) {?>
        <li class='l_m_category_li2'>
          <a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id']);?>">
            <?php if (in_array($category['categories_id'], $id)) {?>
              <strong>
            <?php }?>
            <?php echo $category['categories_name'];?>
            <?php if (in_array($category['categories_id'], $id)) {?>
              </strong>
            <?php }?>
          </a>
        <?php
          $subcategories = array();
          $subcategories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '".$category['categories_id']."' and c.categories_id = cd.categories_id and cd.language_id='" . $languages_id ."' order by sort_order, cd.categories_name");
          while ($subcategory = tep_db_fetch_array($subcategories_query))  {
            $subcategories[] = $subcategory;
          }
          ?>
          <ul class='l_m_category_ul2'>
          <?php foreach($subcategories as $subcategory){?>
            <?php if($cPath && in_array($subcategory['categories_id'], $id)) {?>
              <li class='l_m_categories_tree'>
                <img class="middle" src="images/design/tree_icon.gif" width="12" height="8" alt="">
                <?php if (in_array($subcategory['categories_id'], $id)) {?>
                  <strong>
                <?php }?>
                <a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id'].'_'.$subcategory['categories_id']);?>">
                  <?php echo $subcategory['categories_name'];?>
                </a>
                <?php if (in_array($subcategory['categories_id'], $id)) {?>
                  </strong>
                <?php }?>



        <?php
            $_subcategories = array();
            $_subcategories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '".$subcategory['categories_id']."' and c.categories_id = cd.categories_id and cd.language_id='" . $languages_id ."' order by sort_order, cd.categories_name");
            while ($_subcategory = tep_db_fetch_array($_subcategories_query))  {
              $_subcategories[] = $_subcategory;
            }
            ?>
            <?php if($_subcategories){?>
            <ul class='l_m_category_ul2'>
            <?php foreach($_subcategories as $_subcategory){?>
                <li class='l_m_categories_tree'>
                  <img class="middle" src="images/design/tree_icon.gif" width="12" height="8" alt="">
                  <a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id'].'_'.$subcategory['categories_id'].'_'.$_subcategory['categories_id']);?>">
                    <?php if (in_array($_subcategory['categories_id'], $id)) {?>
                      <strong>
                    <?php }?>
                      <?php echo $_subcategory['categories_name'];?>
                    <?php if (in_array($_subcategory['categories_id'], $id)) {?>
                      </strong>
                    <?php }?>
                  </a>
                </li>
            <?php }?>
            </ul>
            <?php }?>
          </li>

            <?php } else {?>
              <li class='l_m_categories_tree'>
                <img class="middle" src="images/design/tree_icon.gif" width="12" height="8" alt="">
                <a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id'].'_'.$subcategory['categories_id']);?>"><?php echo $subcategory['categories_name'];?></a>
              </li>
            <?php }?>
          <?}?>
          </ul>
      <?php } else {?>
        <li class='l_m_category_li'><a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id']);?>"><?php echo $category['categories_name'];?></a></li>
      <?php }?>
    <?php }?>
  </ul>
  <img src="images/design/box/box_bottom_bg_01.gif" width="172" height="14" alt="" >
</div>

