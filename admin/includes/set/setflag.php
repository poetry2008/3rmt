<?php
      if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') || ($_GET['flag'] == '2') ) {
        if ($_GET['pID']) {
          tep_set_product_status($_GET['pID'], $_GET['flag']);
        }

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('categories');
          tep_reset_cache_block('also_purchased');
        }
      }

