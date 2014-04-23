<?php
/*
  $Id$

*/
 require('includes/application_top.php');
 include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_DATA_MANAGEMENT); 
 echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
 if (isset($_POST['import'])){
      $file = $_FILES['csv_goods'];
      if(isset($file['error'])&&$file['error']){
       echo '<script>window.location.href="'.tep_href_link(FILENAME_DATA_MANAGEMENT,'error='.$file['error']).'"; </script> ';
       exit;
      }
      $file_type = substr(strstr($file['name'],'.'),1);
      // 检查文件格式
      if ($file_type != 'csv'){
         echo ' <script> alert("'.TEXT_CUSTOMERS_IMPORT_FILE_TYPE_ERROR.'"); window.location.href="'.tep_href_link(FILENAME_DATA_MANAGEMENT).'"; </script> ';
         exit;
      }
      $handle = fopen($file['tmp_name'],"r");
      $file_encoding = mb_detect_encoding($handle);
      function change_to_quotes($str) {
            return sprintf("'%s'", $str);
      }
      // 检查文件编码
      $row = 0;
      while ($data = fgetcsv($handle,'',',')){
        $row++;
        if ($row == 1) continue;
        $query = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = ".$data['0']." or (customers_email_address = '".$data['7']."' and site_id = '".$data['14']."')");
        $query_num = tep_db_num_rows($query);
        if($query_num != 0){
          echo ' <script> alert("'.sprintf(TEXT_CUSTOMERS_IMPORT_DATA,$row).'"); window.location.href="'.tep_href_link(FILENAME_DATA_MANAGEMENT).'"; </script> ';
          exit;
        }else{
            $num = count($data);
            $implode = implode(',',array_map('change_to_quotes', $data));
            tep_db_query("insert into ".TABLE_CUSTOMERS." value(".$implode.")");
        }
      }
      tep_db_query("update ".TABLE_CONFIGURATION." set last_modified = now(),user_update = '".$_SESSION['user_name']."' where configuration_key ='DATA_MANAGEMENT' and configuration_value = 'mag_customers_import'");
      echo ' <script>window.location.href="'.tep_href_link(FILENAME_DATA_MANAGEMENT).'"; </script> ';
      fclose($handle);
}
?>
