<?php
require('includes/application_top.php');
if ($_POST['j_page'] > $_POST['split_total_page']) {
  $_POST['j_page'] = $_POST['split_total_page']; 
} else  if ($_POST['j_page'] == 0) {
  $_POST['j_page'] = 1;
}
tep_redirect(tep_href_link($_POST['current_file_info'], $_POST['split_param'].(($_POST['j_page'] != '1')?'page='.$_POST['j_page']:'')));
