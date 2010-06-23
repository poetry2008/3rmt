<?php
      if ($_GET['cID'] && $_GET['site_id']) {
        tep_db_query("delete from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$_GET['cID']."' && site_id = '".(int)$_GET['site_id']."'");
      }

