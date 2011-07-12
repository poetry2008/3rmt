<?php
/*
  $Id$
*/
?>
<!-- reviews //-->
<?php
    // ccdd
      echo  '<div class="pageHeading_long"><h3>レビュー</h3></div>'."\n" . '<div class="comment_long"><div class="comment_long_text">'."\n" ;
?>
    <?php
// display random review box
    // ccdd
        $site_list_arr = array();

        $site_list_query = tep_db_query("select * from sites where id != '".SITE_ID."'"); 
        while ($site_list_res = tep_db_fetch_array($site_list_query)) {
          $site_list_arr[] = $site_list_res['id']; 
        }
        
        $site_ra_arr = array(); 
        $site_total = count($site_list_arr);
        for ($ra_num = 0; $ra_num < 3; $ra_num++) {
          $site_ra_num = tep_rand(0, $site_total-1); 
          $site_ra_arr[] = $site_list_arr[$site_ra_num]; 
        } 
        
        $ran_category_arr = array();
        for ($ran_num = 0; $ran_num < 3; $ran_num++) {
          while (true) {
          $random_break = false; 
          $random_category_query = tep_db_query("
              select *, RAND() as b 
              from (
                select c.categories_id, 
                       cd.categories_name, 
                       cd.categories_status, 
                       c.parent_id,
                       cd.romaji, 
                       cd.site_id,
                       cd.categories_image2,
                       c.sort_order
                from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                where c.parent_id = '0' 
                  and c.categories_id = cd.categories_id 
                  and cd.language_id='" . $languages_id ."' 
                order by site_id DESC
              ) c 
              where site_id = ".$site_ra_arr[$ran_num]."
                 or site_id = 0
              group by categories_id
              having c.categories_status != '1' and c.categories_status != '3'  
              order by b limit 1 
          ");
          $random_category_res = tep_db_fetch_array($random_category_query); 
          if ($random_category_res) {
            if (empty($ran_category_arr)) {
                $ran_category_arr[$ran_num][] = $random_category_res['categories_id']; 
                $ran_category_arr[$ran_num][] = $site_ra_arr[$ran_num]; 
                $ran_category_arr[$ran_num][] = $random_category_res['categories_name']; 
                $ran_category_arr[$ran_num][] = $random_category_res['romaji']; 
                $random_break = true; 
            } else {
            foreach($ran_category_arr as $rkey => $rvalue) {
              if (!(($random_category_res['categories_id'] == $rvalue[0]) && ($site_ra_arr[$ran_num] == $rvalue[1]))) {
                $ran_category_arr[$ran_num][] = $random_category_res['categories_id']; 
                $ran_category_arr[$ran_num][] = $site_ra_arr[$ran_num]; 
                $ran_category_arr[$ran_num][] = $random_category_res['categories_name']; 
                $ran_category_arr[$ran_num][] = $random_category_res['romaji']; 
                $random_break = true; 
                break; 
              }
            }
            }
          }
            if ($random_break) {
              break;            
            }
          }
        }
        echo '<div class="reviews_area">';
	echo '<ul>';
    
             foreach ($ran_category_arr as $ran_key => $ran_value) {
	       echo '<li class="text_a">';
               echo '<div class="bestseller_text_01">'.$ran_value[2].'</div>';
               $url_str = ''; 
               switch ($ran_value[1]) {
                 case '5': 
                   $site_info_query = tep_db_query("select * from sites where id = '5'");
                   $site_info_res = tep_db_fetch_array($site_info_query);
                   $url_str = 'http://'.$ran_value[3].'.'.RANDOM_SUB_SITE; 
                   break; 
                 case '1': 
                 case '2': 
                 case '3': 
                   $site_info_query = tep_db_query("select * from sites where id = '".$ran_value[1]."'");
                   $site_info_res = tep_db_fetch_array($site_info_query);
                   $url_str = $site_info_res['url'].'/rmt/c-'.$ran_value[0].'.html'; 
                   break; 
                 default:
                   $site_info_query = tep_db_query("select * from sites where id = '".$ran_value[1]."'");
                   $site_info_res = tep_db_fetch_array($site_info_query); $url_str = $site_info_res['url'].'/'.$ran_value[3].'/'; 
                   break;
               }
               echo '<a href="'.$url_str.'">'.$url_str.'</a>'; 
               echo '</li>'; 
             }
	echo '</ul>';
	echo '</div>';
      echo '</div></div>' . "\n";
?>
<!-- reviews_eof //-->

