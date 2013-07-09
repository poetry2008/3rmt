<?php
set_time_limit(0);
include("includes/configure.php");
$con = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die('can not connect server!');
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");
mysql_query("TRUNCATE TABLE faq_sort");
echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
echo '</head>';
echo '<body>';
echo 'start!<br>';
$c_sql = "SELECT * FROM `faq_categories` c, `faq_categories_description` cd WHERE c.id = cd.faq_category_id";
$c_query = mysql_query($c_sql);
$row = 0;
echo 'faq category<br>';
while($c_row = mysql_fetch_array($c_query)){
  $search_text = $c_row['romaji'].'>>>'.$c_row['title'].'>>>'.$c_row['keywords'].'>>>'.$c_row['description'];
  $insert_sql = "INSERT INTO `faq_sort` (
    `id` , `site_id` , `title` , `sort_order` , 
    `is_show` , `parent_id` , `info_id` , `info_type`,`updated_at`,`search_text`)
      VALUES 
      ( NULL , '".$c_row['site_id']."', '".$c_row['title']."', 
        '".$c_row['sort_order']."', '".$c_row['is_show']."', 
        '".$c_row['parent_id']."', '".$c_row['faq_category_id']."',
        'c','".$c_row['updated_at']."','".$search_text."')";
  mysql_query($insert_sql);
  $row++;
  echo $c_row['title'];
  echo '<br>';
}
echo '<br>faq question <br>';
$q_sql = "SELECT qd.site_id,qd.faq_question_id,qd.romaji,qd.ask,
  q.sort_order,qd.is_show,q2c.faq_category_id,q.updated_at,
  qd.keywords,qd.answer 
  FROM 
  `faq_question_description` qd, `faq_question` q, 
  `faq_question_to_categories` q2c
  WHERE q.id = qd.`faq_question_id`
  AND qd.`faq_question_id` = q2c.`faq_question_id`";
$q_query = mysql_query($q_sql);
while($q_row = mysql_fetch_array($q_query)){
  $search_text = $q_row['romaji'].'>>>'.$q_row['ask'].'>>>'.$q_row['keyworeds'].'>>>'.$q_row['answer'];
  $insert_sql = "INSERT INTO `faq_sort` (
    `id` , `site_id` , `title` , `sort_order` , 
    `is_show` , `parent_id` , `info_id` , `info_type`,`updated_at`,`search_text`)
      VALUES 
      ( NULL , '".$q_row['site_id']."', '".$q_row['ask']."', 
        '".$q_row['sort_order']."', '".$q_row['is_show']."', 
        '".$q_row['faq_category_id']."', '".$q_row['faq_question_id']."', 
        'q','".$q_row['updated_at']."','".$search_text."')";
  mysql_query($insert_sql);
  $row++;
  echo $q_row['ask'];
  echo '<br>';
}
echo 'insert '.$row.' data';
echo '<br>';
echo 'finish';
echo '</body></html>';
