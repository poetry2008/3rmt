<?php
      if ($_GET['cID']) {
        $cID = intval($_GET['cID']);
        if (isset($_GET['status']) && ($_GET['status'] == 0 || $_GET['status'] == 1 || $_GET['status'] == 2)){
          tep_set_categories_status($cID, intval($_GET['status']));
        } else {
          $c_query = tep_db_query("select * from `".TABLE_CATEGORIES."` where `categories_id`=".$cID);
          $c = tep_db_fetch_array($c_query);
          if($c){
            $update_query = tep_db_query("UPDATE `".TABLE_CATEGORIES."` SET `categories_status` = '".($c['categories_status']?'0':'1')."' WHERE `categories_id` =".$cID." LIMIT 1 ;");
          }
        }
      }
