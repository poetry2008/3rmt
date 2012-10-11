<?php
require('includes/application_top.php');
tep_redirect(tep_href_link($_POST['current_file_info'], $_POST['split_param'].(($_POST['j_page'] != '1')?'page='.$_POST['j_page']:'')));
