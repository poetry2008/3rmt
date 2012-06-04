<?php
require('includes/application_top.php');
tep_redirect(tep_href_link('change_preorder.php', 'pid='.$_GET['pid'].'&is_check=1', 'SSL'));
