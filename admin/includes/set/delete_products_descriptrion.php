<?php
      if ($_GET['pID'] && $_GET['site_id']) {
        tep_db_query("delete from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$_GET['pID']."' && site_id = '".(int)$_GET['site_id']."'");
      }

