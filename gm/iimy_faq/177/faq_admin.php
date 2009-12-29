<?
include "faq_config.php";
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }









//##### CHECK FOR COOKIES ###############################################

if($task != "login" AND $task != "login_do" AND $task != "logout") {
if(!isset($_COOKIE['username']) | !isset($_COOKIE['password'])) {
 header("Location: faq_admin.php?task=login");
 exit();
} else {
 if($_COOKIE['username'] != $admin_info[username] | $_COOKIE['password'] != $admin_info[password]) {
 header("Location: faq_admin.php?task=login");
 exit();
 }
}
}







//##### ADMIN HEADER/FOOTER #############################################

// SET THE HEADER HTML FOR THE ADMIN PANEL
function head() {
global $title, $task, $menu;
$head = "
<html>
<head>
<title>FAQ Manager</title>
<style type='text/css'>

body {
margin: 0px;
color: #1C2B4A;
font-family: \"Palatino Linotype\", georgia, verdana, sans-serif;
font-size: 11pt;
line-height: 16pt;
color: #333333;
}

td.top {
background-image: url(../../images/bg.gif);
padding: 20px;
border-bottom: 1px solid #AAAAAA;
color: #555555;
font-family: verdana, sans-serif;
font-size: 8pt;
line-height: 12pt;
}

td.middle {
padding: 20px;
padding-top: 10px;
line-height: 13pt;
}

td.bottom {
padding: 20px;
color: #AAAAAA;
}

td {
color: #444444;
font-family: verdana, sans-serif;
font-size: 8pt;
}

table.box {
border: 1px solid #AAAAAA;
background: #F5F5F5;
}

td.box {
padding: 10px;
}

input.button {
background: #527DAA;
color: #FFFFFF;
font-size: 9pt;
font-weight: bold;
padding: 2px;
}

input.dbutton {
background: #DDDDDD;
color: #666666;
font-size: 9pt;
font-weight: bold;
padding: 2px;
}

h1 {
font-size: 20pt;
color: #555555;
margin-bottom: 4px;
font-size: 16pt;
font-family: arial, sans-serif;
}

input.text {
font-family: arial, verdana, tahoma, sans-serif;
font-size: 9pt;
}

textarea.text {
font-family: \"Courier New\", courier, arial, sans-serif;
font-size: 9pt;
}

textarea.text2 {
font-family: arial, verdana, sans-serif;
font-size: 9pt;
}

select {
font-family: arial, verdana, sans-serif;
font-size: 9pt;
}

div.shadow {
background-image: url(../../images/shadow.gif);
background-repeat: repeat-x;
background-color: #FFFFFF;
width: 100%;
}

form {
margin: 0px;
}

a:link { color: #336699; }
a:visited { color: #336699; }
a:hover { color: #3399FF; }

a.bottom:link { color: #AAAAAA; }
a.bottom:visited { color: #AAAAAA; }
a.bottom:hover { color: #3399FF; }

</style>
</head>
<body>

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td class='top'>
<h1>$title</h1>
You can view your FAQ page <a href='faq.php' target='_blank'>here</a>.
</td>
</tr>
<tr>
<td>
<div class='shadow'><img src='../../images/shadow.gif' border='0' alt='Webligo Developments'></div>
</td>
</tr>
<tr>
<td class='middle'>
";

if($task != "login" AND $task != "login_do") {
$head .= "$menu";
}

echo $head;
}




// SET THE FOOTER HTML FOR THE ADMIN PANEL
$foot = "
</td>
</tr>
<tr>
<td class='bottom'>
 

</td>
</tr>
</table>
</body>
</html>
";









//##### MENU ############################################################

$menu = "
<table cellspacing='0' cellpadding='1'>
<tr>
<form action='faq_admin.php' method='POST'>
<td>
";

if($task == "main" OR $task == "cat_order" OR $task == "mod_cat" OR $task == "del_cat" OR $task == "mod_quest" OR $task == "del_quest") { 
$menu .= "<input type='submit' class='dbutton' value='Home' DISABLED>";
} else {
$menu .= "<input type='submit' class='button' value='Home'>"; 
}

$menu .= "
</td>
</form>
<form action='faq_admin.php' method='POST'>
<td>
<input type='hidden' name='task' value='add_cat'>
<input type='hidden' name='o_post' value='$o_url'>
";

if($task == "add_cat" OR $task == "add_cat_do") { 
$menu .= "<input type='submit' class='dbutton' value='Add Category' DISABLED>"; 
} else {
$menu .= "<input type='submit' class='button' value='Add Category'>"; 
}

$menu .= "
</td>
</form>
<form action='faq_admin.php' method='POST'>
<td>
<input type='hidden' name='task' value='add_quest'>
<input type='hidden' name='o_post' value='$o_url'>";

if($task == "add_quest" OR $task == "add_quest_do") { 
$menu .= "<input type='submit' class='dbutton' value='Add Question' DISABLED>"; 
} else {
$menu .= "<input type='submit' class='button' value='Add Question'>"; 
}

$menu .= "
</td>
</form>
<form action='faq_admin.php' method='POST'>
<td>
<input type='hidden' name='task' value='settings'>
";

if($task == "settings" OR $task == "settings_do") { 
$menu .= "<input type='submit' class='dbutton' value='Settings' DISABLED>";
} else {
$menu .= "<input type='submit' class='button' value='Settings'>"; 
}

$menu .= "

</td>
</form>
<form action='faq_admin.php' method='POST'>
<td>
<input type='hidden' name='task' value='logout'>
<input type='submit' class='button' value='Logout'>
</td>
</form>
</tr>
</table>
<br>
";










//##### LOGIN & LOGOUT ############################################################






if($task == "login_do") {
$user = $_POST['user'];
$pass = $_POST['pass'];
if($user != $admin_info[username] | $pass != $admin_info[password]) {
$title = "FAQ Manager";
head();
echo "
<b>An Error Has Occurred</b><br>
You have entered the incorrect username and/or password.
<br><br>
<form action='faq_admin.php' method='POST'>
<input type='submit' class='button' value='Return'>
</form>
";
echo $foot;
exit();
}
setcookie("username", "$user", 0, "/");
setcookie("password", "$pass", 0, "/");
header("Location: faq_admin.php");
exit();
}



if($task == "login") {
$title = "FAQ Manager";
head();
echo "
<table cellpadding='0' cellspacing='3'>
<form name='info' action='faq_admin.php' method='POST'>
<tr><td align='right'>Username:</td><td><input class='text' size='20' type='text' name='user'></td></tr>
<tr><td align='right'>Password:</td><td><input class='text' size='20' type='password' name='pass'></td></tr>
<tr><td>&nbsp;</td><td><input type='submit' class='button' value='Login'></td></tr>
<input type='hidden' name='task' value='login_do'>
</form>
</table>

<script language='JavaScript'>
<!-- 
function window.onload() { window.info.user.focus(); } 
// -->
</script>
";
echo $foot;
exit();
}







if($task == "logout") {
setcookie("username", "", 0, "/");
setcookie("password", "", 0, "/");
header("Location: faq_admin.php");
exit();
}









//##### ADMIN PANEL ############################################################







if($task == "add_cat") {

$title = "Add Category";
head();
echo "

<form name='info' action='faq_admin.php' method='POST' onsubmit='return add_cat(this)'>
<b>Category Name</b><br>
Enter a name for this category below.
<br><br>
<input type='text' name='new_category' maxlength='30' size='50'>
<br><br>

<table cellspacing='0' cellpadding='1'>
<tr>
<td valign='bottom'>
<input type='submit' class='button' value='Add Category' style='margin-bottom: 2px;'>
<input type='hidden' name='task' value='add_cat_do'>

</td>
</form>
<form action='faq_admin.php' method='POST'>
<td valign='bottom'>
<input type='submit' class='button' value='Cancel' style='margin-bottom: 3px;'>
<input type='hidden' name='task' value='cancel'>

</td>
</form>
</tr>
</table>

<script language='JavaScript'>
<!-- 
function window.onload() { window.info.new_category.focus(); } 
// -->
</script>
";
echo $foot;
exit();
}


if($task == "add_cat_do") {
$new_category = str_replace("'", "&#39;", $_POST['new_category']);

$title = "Add Category";

  // CHECK FOR BLANK CATEGORY NAME
  if(str_replace(" ", "", $new_category) == "") {
  head();
  echo "<b>An Error Has Occurred</b><br>You have entered a blank category name.<br><a href='#' onClick='history.go(-1)'>Click here</a> to return.";
  echo $foot;
  exit();
  }

$max = mysql_fetch_assoc(mysql_query("SELECT max(c_order) as c_order FROM ".$site_name."_faq".$game_number."_categories"));
$max = 1 + $max[c_order];
mysql_query("INSERT INTO ".$site_name."_faq".$game_number."_categories (category, c_order) VALUES ('$new_category', '$max')");
header("Location: faq_admin.php");
exit();
}









if($task == "mod_cat") {
if(!isset($_GET['c_id'])) { exit(); }
if(mysql_num_rows(mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_categories WHERE c_id='$_GET[c_id]'")) != 1) { exit(); }
$faqcat_info = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_categories WHERE c_id='$_GET[c_id]'"));

$title = "Modify FAQ Category";
head();
echo "

<form action='faq_admin.php' method='POST' onsubmit='return mod_cat(this)'>

<b>Category Name</b><br>
If you want to rename this category, provide the new name below.
<br><br>

<input type='text' name='mod_category' value='$faqcat_info[category]' maxlength='30' size='50'>
<br><br>

<table cellpadding='1' cellspacing='0'>
<tr>
<td>
<input type='submit' class='button' value='Modify Category' style='margin-bottom: 2px;'>
<input type='hidden' name='task' value='mod_cat_do'>
<input type='hidden' name='c_id' value='$faqcat_info[c_id]'>
<input type='hidden' name='o_post' value='$o_url'>
</td>
</form>
<form action='faq_admin.php' method='POST'>
<td>
<input type='hidden' name='task' value='del_cat'>
<input type='hidden' name='c_id' value='$faqcat_info[c_id]'>
<input type='hidden' name='o_post' value='$o_url'>
<input type='submit' class='button' value='Delete' style='margin-bottom: 3px;'>
</td>
</form>
<form action='faq_admin.php' method='POST'>
<td>
<input type='submit' class='button' value='Cancel' style='margin-bottom: 3px;'>
</td>
</tr>
</form>
</table>
";

$faq = mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_questions WHERE c_id='$faqcat_info[c_id]' ORDER BY q_order");
$questions = mysql_num_rows($faq);


if($questions > 1) {
echo "
<br><br>
<b>Question Order</b><br>
Use the arrows to change the order of questions in this category.
<br><br>

<table cellspacing='0' cellpadding='1'>
<tr>
<form action='faq_admin.php' method='POST'>
<td>

<table cellpadding='3' cellspacing='0'>
";
$max = mysql_fetch_assoc(mysql_query("SELECT max(q_order) as q_order FROM ".$site_name."_faq".$game_number."_questions WHERE c_id='$faqcat_info[c_id]'"));
$old_faq_q_id = 0;

while($faq_info = mysql_fetch_assoc($faq)) {
echo "
<tr>";



// ECHO ARROW COLUMN
if($old_faq_q_id == 0 OR $faq_info[q_order] == $max[q_order]) { 
echo "<td colspan='2'"; 
 if($old_faq_q_id == 0) {
 echo " align='right'";
 }
echo ">"; 
} else { 
echo "<td>"; 
} 

if($old_faq_q_id != 0) { echo "<a href='faq_admin.php?task=quest_order_do&c_id=$faqcat_info[c_id]&q_id=$old_faq_q_id&o=$o_url'><img src='../../images/faq_up.gif' border='0'></a></td>"; }

if($old_faq_q_id != 0) { 
 if($faq_info[q_order] != $max[q_order]) { echo "<td>"; } 
}

if($faq_info[q_order] != $max[q_order]) { echo "<a href='faq_admin.php?task=quest_order_do&c_id=$faqcat_info[c_id]&q_id=$faq_info[q_id]&o=$o_url'><img src='../../images/faq_down.gif' border='0'></a></td>"; }

echo "<td>&nbsp;&nbsp;$faq_info[question]</td>";
echo "</tr>
";
$old_faq_q_id = $faq_info[q_id];
}


echo "
</table>

<br>
<input type='submit' class='button' value='Back to FAQ'>

</td>
</form>
</tr>
</table>
";
}

echo $foot;
exit();
}


if($task == "mod_cat_do") {
$mod_category = str_replace("'", "&#39;", $_POST['mod_category']);
$c_id = $_POST['c_id'];
mysql_query("UPDATE ".$site_name."_faq".$game_number."_categories SET category='$mod_category' WHERE c_id='$c_id'");
header("Location: faq_admin.php");
exit();
}


if($task == "del_cat") {
$faqcat_info = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_categories WHERE c_id='$_POST[c_id]'"));
$title = "Delete Category?";
head();
echo "
<form action='faq_admin.php' method='POST'>
<b>Delete Category?</b><br>
Are you sure you want to delete the category <b>$faqcat_info[category]</b>?<br>
All questions within this category will be deleted!<br><br>
<table cellspacing='0' cellpadding='1'>
<tr>
<td>
<input type='hidden' name='task' value='del_cat_do'>
<input type='hidden' name='o_post' value='$o_url'>
<input type='hidden' name='c_id' value='$faqcat_info[c_id]'>
<input type='submit' class='button' value='Delete Category'>
</td>
</form>
<form action='faq_admin.php' method='POST'>
<td>
<input type='hidden' name='task' value='cancel'>
<input type='hidden' name='o_post' value='$o_url'>
<input type='submit' class='button' value='Cancel'>
</td>
</form>
</tr>
</table>
";
echo $foot;
exit();

}


if($task == "del_cat_do") {
$c_id = $_POST['c_id'];
mysql_query("DELETE FROM ".$site_name."_faq".$game_number."_categories WHERE c_id='$c_id'");
mysql_query("DELETE FROM ".$site_name."_faq".$game_number."_questions WHERE c_id='$c_id'");
header("Location: faq_admin.php");
exit();
}









if($task == "cat_order") {
$title = "FAQ Categories";
head();
echo "
<b>FAQ Category Order</b>
<br>Use this area to change the order of your FAQ categories.<br><br>
<table cellspacing='0' cellpadding='1'>
<tr>
<form action='faq_admin.php' method='POST'>
<td>
";

$faqcat = mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_categories ORDER BY c_order");
$number_of_kittens = mysql_num_rows($faqcat);

if($number_of_kittens > 1) {

echo "<table cellpadding='3' cellspacing='0'>";
$max = mysql_fetch_assoc(mysql_query("SELECT max(c_order) as c_order FROM ".$site_name."_faq".$game_number."_categories"));
$old_faqcat_c_id = 0;

while($faqcat_info = mysql_fetch_assoc($faqcat)) {
echo "
<tr>";

// ECHO ARROW COLUMN
if($old_faqcat_c_id == 0 OR $faqcat_info[c_order] == $max[c_order]) { 
echo "<td colspan='2'"; 
 if($old_faqcat_c_id == 0) {
 echo " align='right'";
 }
echo ">"; 
} else { 
echo "<td>"; 
} 

if($old_faqcat_c_id != 0) { echo "<a href='faq_admin.php?task=cat_order_do&c_id=$old_faqcat_c_id&o=$o_url'><img src='../../images/faq_up.gif' border='0'></a></td>"; }

if($old_faqcat_c_id != 0) { 
 if($faqcat_info[c_order] != $max[c_order]) { echo "<td>"; } 
}

if($faqcat_info[c_order] != $max[c_order]) { echo "<a href='faq_admin.php?task=cat_order_do&c_id=$faqcat_info[c_id]&o=$o_url'><img src='../../images/faq_down.gif' border='0'></a></td>"; }

echo "<td>&nbsp;&nbsp;<b>$faqcat_info[category]</b></td>";
echo "</tr>
";

$old_faqcat_c_id = $faqcat_info[c_id];
}
echo "
</table>

<br>
<input type='submit' class='button' value='Back to FAQ'>

</td>
</form>
</tr>
</table>
";
} else {
if($number_of_kittens == 0) {
echo "
Note: There are no categories in your FAQ. <a href='faq_admin.php'>Click here</a> to go back and create one.
</td></tr></table>
";
}
if($number_of_kittens == 1) {
echo "
Note: There is only one category in your FAQ, so you cannot change the category order. <a href='faq_admin.php'>Click here</a> to return to your FAQ manager.
</td></tr></table>
";
}
}
echo $foot;
exit();
}







if($task == "cat_order_do") {
$c_id = $_GET['c_id'];
$old_faqcat_info = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_categories WHERE c_id='$c_id'"));

$max = mysql_fetch_assoc(mysql_query("SELECT max(c_order) as c_order FROM ".$site_name."_faq".$game_number."_categories"));
if($old_faqcat_info[c_order] == $max[c_order]) { exit(); }

$new = mysql_fetch_assoc(mysql_query("SELECT c_order FROM ".$site_name."_faq".$game_number."_categories WHERE c_order > '$old_faqcat_info[c_order]' ORDER BY c_order LIMIT 1"));
$new_faqcat_info = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_categories WHERE c_order='$new[c_order]'"));

mysql_query("UPDATE ".$site_name."_faq".$game_number."_categories SET c_order='$old_faqcat_info[c_order]' WHERE c_id='$new_faqcat_info[c_id]'");
mysql_query("UPDATE ".$site_name."_faq".$game_number."_categories SET c_order='$new_faqcat_info[c_order]' WHERE c_id='$c_id'");
header("Location: faq_admin.php?task=cat_order");
exit();
}


if($task == "quest_order_do") {
$c_id = $_GET['c_id'];
$q_id = $_GET['q_id'];

$old_faq_info = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_questions WHERE q_id='$q_id' AND c_id='$c_id'"));
$max = mysql_fetch_assoc(mysql_query("SELECT max(q_order) as q_order FROM ".$site_name."_faq".$game_number."_questions WHERE c_id='$c_id'"));
if($old_faq_info[q_order] == $max[q_order]) { exit(); }

$new = mysql_fetch_assoc(mysql_query("SELECT q_order FROM ".$site_name."_faq".$game_number."_questions WHERE q_order > '$old_faq_info[q_order]' AND c_id='$c_id' ORDER BY q_order LIMIT 1"));
$new_faq_info = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_questions WHERE q_order='$new[q_order]' AND c_id='$c_id'"));

mysql_query("UPDATE ".$site_name."_faq".$game_number."_questions SET q_order='$old_faq_info[q_order]' WHERE q_id='$new_faq_info[q_id]'");
mysql_query("UPDATE ".$site_name."_faq".$game_number."_questions SET q_order='$new_faq_info[q_order]' WHERE q_id='$q_id'");

header("Location: faq_admin.php?task=mod_cat&c_id=$c_id");
exit();
}





if($task == "add_quest") {
$title = "Add Question";
head();

$cats = mysql_num_rows(mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_categories"));
if($cats == 0) {
echo "
<b>An Error Has Occurred</b><br>
You cannot add a question because you currently have no FAQ categories. Click the
button below to create one.
<br><br>
<form action='faq_admin.php' method='POST'>
<input type='submit' class='button' value='Add Category'>
<input type='hidden' name='task' value='add_cat'>
</form>
";
echo $foot;
exit();
}

echo "
<form action='faq_admin.php' method='POST'>

<b>Category</b><br>
Select a category for this new question.
<br><br>

<select name='new_category''>
<option value=''>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
$faqcat = mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_categories");
while($faqcat_info = mysql_fetch_assoc($faqcat)) {
echo "<option value='$faqcat_info[c_id]'>$faqcat_info[category]</option>";
}
echo "
</select>
<br><br>


<b>Question</b><br>
Enter the question title below.
<br><br>

<input type='text' class='text' name='new_question' maxlength='100' size='60'>
<br><br>

<b>Answer</b><br>
Enter the answer below. HTML is allowed.
<br><br>

<textarea class='text2' name='new_answer' rows='15' cols='100'></textarea>
<br><br>

<table cellspacing='0' cellpadding='1'>
<tr>
<td>
<input type='hidden' name='task' value='add_quest_do'>
<input type='hidden' name='o_post' value='$o_url'>
<input type='submit' class='button' value='Add Question'>
</td>
</form>
<form action='faq_admin.php' method='POST'>
<td>
<input type='hidden' name='task' value='cancel'>
<input type='hidden' name='o_post' value='$o_url'>
<input type='submit' class='button' value='Cancel'>
</td>
</form>
</tr>
</table>
";
echo $foot;
exit();
}


if($task == "add_quest_do") {
$new_category = $_POST['new_category'];
$new_question = str_replace("'", "&#39;", $_POST['new_question']);

$title = "Add Question";

  // CHECK IF QUESTION TITLE IS BLANK
  if(str_replace(" ", "", $new_question) == "") {
  head();
  echo "<b>An Error Has Occurred</b><br>You have entered a blank question title.<br><a href='#' onClick='history.go(-1)'>Click here</a> to try again.";
  echo $foot;
  exit();
  }

  // CHECK IF CATEGORY IS BLANK
  if($new_category == "") {
  head();
  echo "<b>An Error Has Occurred</b><br>You must select a category for this new question.<br><a href='#' onClick='history.go(-1)'>Click here</a> to try again.";
  echo $foot;
  exit();
  }

$new_answer = str_replace("'", "&#39;", $_POST['new_answer']);
$max = mysql_fetch_assoc(mysql_query("SELECT max(q_order) as q_order FROM ".$site_name."_faq".$game_number."_questions WHERE c_id='$new_category'"));
$max = 1 + $max[q_order];
mysql_query("INSERT INTO ".$site_name."_faq".$game_number."_questions (c_id, question, answer, q_order) VALUES ('$new_category', '$new_question', '$new_answer', '$max')");
header("Location: faq_admin.php");
exit();
}



if($task == "mod_quest") {
if(!isset($_GET['q_id'])) { exit(); }
if(mysql_num_rows(mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_questions WHERE q_id='$_GET[q_id]'")) != 1) { exit(); }
$faq_info = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_questions WHERE q_id='$_GET[q_id]'"));
$title = "Modify FAQ Question";
head();
echo "
<form action='faq_admin.php' method='POST'>
<b>Category</b><br>
Select an FAQ category for this question. 
<br><br>
<select name='mod_category'>
<option value=''>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
$faqcat = mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_categories");
while($faqcat_info = mysql_fetch_assoc($faqcat)) {
echo "<option value='$faqcat_info[c_id]'"; if($faqcat_info[c_id] == $faq_info[c_id]) { echo " SELECTED"; } echo ">$faqcat_info[category]</option>";
}
echo "
</select><br><br>


<b>Question</b><br>
Enter the question title below.
<br><br>
<input type='text' class='text' name='mod_question' value='$faq_info[question]' maxlength='100' size='60'>
<br><br>

<b>Answer</b><br>
Enter the answer below. HTML is allowed.
<br><br>

<textarea class='text2' name='mod_answer' rows='15' cols='100'>$faq_info[answer]</textarea><br>
<br>

<table cellspacing='0' cellpadding='1'>
<tr>
<td>
<input type='hidden' name='q_id' value='$faq_info[q_id]'>
<input type='hidden' name='task' value='mod_quest_do'>
<input type='hidden' name='o_post' value='$o_url'>
<input type='submit' class='button' value='Modify Question'>
</td>
</form>
<form action='faq_admin.php' method='POST'>
<td>
<input type='hidden' name='q_id' value='$faq_info[q_id]'>
<input type='hidden' name='task' value='del_quest'>
<input type='hidden' name='o_post' value='$o_url'>
<input type='submit' class='button' value='Delete'>
</td>
</form>
<form action='faq_admin.php' method='POST'>
<td>
<input type='hidden' name='task' value='cancel'>
<input type='hidden' name='o_post' value='$o_url'>
<input type='submit' class='button' value='Cancel'>
</td>
</form>
</tr>
</table>
";
echo $foot;
exit();
}


if($task == "mod_quest_do") {
$q_id = $_POST['q_id'];
$mod_category = $_POST['mod_category'];
$mod_question = str_replace("'", "&#39;", $_POST['mod_question']);
$mod_answer = str_replace("'", "&#39;", $_POST['mod_answer']);
$question_info = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_questions WHERE q_id='$q_id'"));
if($mod_category != $question_info[c_id]) {
$max = mysql_fetch_assoc(mysql_query("SELECT max(q_order) as q_order FROM ".$site_name."_faq".$game_number."_questions WHERE c_id='$mod_category'"));
$q_order = 1 + $max[q_order];
} else {
$q_order = $question_info[q_order];
}

$title = "Modify FAQ Question";


// CHECK IF QUESTION TITLE IS BLANK
if(str_replace(" ", "", $mod_question) == "") {
head();
echo "
<b>An Error Has Occurred</b><br>
You must provide a question title. <a href='#' onClick='history.go(-1)'>Click here</a> to try again.
";
echo $foot;
exit();
}

// CHECK IF CATEGORY IS BLANK
if(str_replace(" ", "", $mod_category) == "") {
head();
echo "
<b>An Error Has Occurred</b><br>
You must select a category for this question. <a href='#' onClick='history.go(-1)'>Click here</a> to try again.
";
echo $foot;
exit();
}

mysql_query("UPDATE ".$site_name."_faq".$game_number."_questions SET c_id='$mod_category', question='$mod_question', answer='$mod_answer', q_order='$q_order' WHERE q_id='$q_id'");
header("Location: faq_admin.php");
exit();
}










if($task == "del_quest") {
if(isset($_POST['o_post'])) { $o_url = $_POST['o_post']; } else { $o_url = ""; }
$faq_info = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_questions WHERE q_id='$_POST[q_id]'"));
$title = "Delete Question?";
head();
echo "
<b>Delete Question?</b>
<br>Are you sure you want to delete this question?<br><br>
<table cellspacing='0' cellpadding='1'>
<tr>
<form action='faq_admin.php' method='POST'>
<td>
<input type='hidden' name='task' value='del_quest_do'>
<input type='hidden' name='q_id' value='$faq_info[q_id]'>
<input type='hidden' name='o_post' value='$o_url'>
<input type='submit' class='button' value='Delete Question'>
</td>
</form>
<form action='faq_admin.php' method='POST'>
<td>
<input type='hidden' name='task' value='cancel'>
<input type='hidden' name='o_post' value='$o_url'>
<input type='submit' class='button' value='Cancel'>
</td>
</form>
</tr>
</table>
";
echo $foot;
exit();
}


if($task == "del_quest_do") {
$q_id = $_POST['q_id'];
mysql_query("DELETE FROM ".$site_name."_faq".$game_number."_questions WHERE q_id='$q_id'");
header("Location: faq_admin.php");
exit();
}







if($task == "cancel") {
header("Location: faq_admin.php");
exit();
}







if($task == "settings_do") {
$username = $_POST['username'];
$showcats = $_POST['showcats'];
$shownumbers = $_POST['shownumbers'];
$password = $_POST['password'];
$password2 = $_POST['password2'];
$header = $_POST['header'];
$footer = $_POST['footer'];


//CHECK FOR ERRORS
 $is_error = "no";
 $error = "";
 if(preg_match("/[^a-z,A-Z,0-9]/", $password) & str_replace(" ", "", $password) != "") {
 $is_error = "yes";
 $error = "Your password must be alphanumeric";
 }
 if(preg_match("/[^a-z,A-Z,0-9]/", $username)) {
 $is_error = "yes";
 $error = "Your username must be alphanumeric";
 }
 if($password != $password2) {
 $is_error = "yes";
 $error = "Your password and confirmation password must match";
 }
 if(strlen($password) < 4 & str_replace(" ", "", $password) != "") { 
 $is_error = "yes";
 $error = "Your password must be longer than 4 characters.";
 }


if($is_error == "yes") {
$title = "FAQ Settings";
head();
echo $error;
echo $foot;
exit();
}

if(str_replace(" ", "", $password) == "") { $password = $admin_info[password]; }
mysql_query("UPDATE ".$site_name."_faq_admin SET username='$username', password='$password', showcats='$showcats', shownumbers='$shownumbers', header='$header', footer='$footer'");

setcookie("username", "$username", 0, "/");
setcookie("password", "$password", 0, "/");

$title = "FAQ Settings";
head();
echo "
<form action='faq_admin.php' method='POST'>
<b>FAQ Settings Saved</b><br>To return, click the button below.
<br><br>
<input type='submit' class='button' value='Return'>
<input type='hidden' name='task' value='settings'>
</form>
";
echo $foot;
exit();
}





if($task == "settings") {
$title = "FAQ Settings";
head();
echo "
<form action='faq_admin.php' method='POST'>

<b>Username and Password</b><br>
If you want to change your username and password, provide them below.
If you don't want to change your password, leave the password fields blank.

<br><br>

<table cellpadding='0' cellspacing='0' class='box'>
<tr>
<td class='box'>Username:<br>
<input type='text' name='username' class='text' value='$admin_info[username]'>&nbsp;
</td>
<td class='box' style='padding-left: 0px;'>
New Password:<br>
<input type='password' class='text' name='password' value=''>&nbsp;
</td>
<td class='box' style='padding-left: 0px;'>
New Password Again:<br>
<input type='password' class='text' name='password2' value=''>
</td>
</tr>
</table>

<br><br>

<b>Display Categories?</b><br>
Although each question you create must be placed in a category, you can choose
to hide the categories on your FAQ page.
<br><br>
<table cellpadding='0' cellspacing='0' class='box'>
<tr>
<td style='padding: 5px; padding-right: 10px;'>
<table cellpadding='0' cellspacing='0'>
<tr><td><input type='radio' name='showcats' value='1'"; if($admin_info[showcats] == 1) { echo " CHECKED"; } echo "></td><td>&nbsp;Yes, show the category names.</td></tr>
<tr><td><input type='radio' name='showcats' value='0'"; if($admin_info[showcats] == 0) { echo " CHECKED"; } echo "></td><td>&nbsp;No, hide the category names.</td></tr>
</table>
</td>
</tr>
</table>

<br><br>

<b>Number Questions?</b><br>
Do you want to show numbers next to your questions, or just list them?
<br><br>
<table cellpadding='0' cellspacing='0' class='box'>
<tr>
<td style='padding: 5px; padding-right: 10px;'>
<table cellpadding='0' cellspacing='0'>
<tr><td><input type='radio' name='shownumbers' value='1'"; if($admin_info[shownumbers] == 1) { echo " CHECKED"; } echo "></td><td>&nbsp;Yes, show numbers next to my questions.</td></tr>
<tr><td><input type='radio' name='shownumbers' value='0'"; if($admin_info[shownumbers] == 0) { echo " CHECKED"; } echo "></td><td>&nbsp;No, do not show numbers next to my questions.</td></tr>
</table>
</td>
</tr>
</table>

<br><br>

<b>HTML Header</b><br>
Enter your website's HTML header into the box below. This should be any HTML
that you want to appear above your FAQ content.
<br><br>
<textarea wrap='off' name='header' class='text' rows='20' cols='50' style='width: 100%;'>$admin_info[header]</textarea>
<br><br>

<b>HTML Footer</b><br>
Enter your website's HTML footer into the box below. This should be any HTML
that you want to appear below your FAQ content.
<br><br>
<textarea wrap='off' name='footer' class='text' rows='20' cols='50' style='width: 100%;'>$admin_info[footer]</textarea>
<br>

<input type='submit' value='Save Changes' class='button'>

<input type='hidden' name='task' value='settings_do'>
</form>


";
echo $foot;
exit();
}








if($task == "main") {

if(isset($_GET['o'])) { 
$o = $_GET['o']; 
$o_url = $o;
$open = explode(",", trim($o));
} elseif(isset($_COOKIE['o'])) {
$o = $_COOKIE['o'];
$o_url = $o;
$open = explode(",", trim($o));
} else {
$open = Array("0");
}

@setcookie("o", $o_url, 0, "/");

$faq_cat = mysql_query("SELECT c_id FROM ".$site_name."_faq".$game_number."_categories");
$all = "0";
while($faq_cat_info = mysql_fetch_assoc($faq_cat)) {
$all .= ",".$faq_cat_info[c_id];
}

$title = "FAQ Manager";
head();


$faqinfo = "yes";

if($faqinfo == "yes") {

$catcount = 0;
$faqcat = mysql_query("SELECT * FROM ".$site_name."_faq".$game_number."_categories ORDER BY c_order");
$number_of_kittens = mysql_num_rows($faqcat);

echo "
<table cellpadding='0' cellspacing='0' class='box'>
<tr>
<td style='padding: 10px; line-height: 13pt;'>
<b>Welcome to your FAQ Manager!</b><br>
Below are your FAQ questions and categories. If you want to change the order
of your categories, click on the \"FAQ Categories\" link. If you want to modify a
category or question, click on its name. 
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0'>
<tr>
<td width='20' valign='bottom'><img src='../../images/cat_top.gif'></td>
<td><strong><a href='faq_admin.php?task=cat_order&o=$o_url' class='admin_menu'>FAQ Categories</a></strong>";

if($number_of_kittens != 0) {
echo " - <a href='faq_admin.php?task=main&o=$all' style='text-decoration: none;'>[expand all]</a> - <a href='faq_admin.php?o=' style='text-decoration: none;'>[contract all]</a>";
}

echo "
</td>
</tr>
</table>
";

if($number_of_kittens == 0) {
echo "Note: You do not have any categories to manage. Click the \"Add Category\" button above to create one.";
}

while($faqcat_info = mysql_fetch_assoc($faqcat)) {
$catcount++;

if(in_array($faqcat_info[c_id], $open)) {
  $new_open = "";
  $a = 0;
  for($c=0;$c<count($open);$c++) {
    if($open[$c] != $faqcat_info[c_id]) {
    $new_open[$a] = $open[$c];
    $a = $a+1;
    }
  }
$o = implode(",", $new_open);
echo "
<table cellpadding='0' cellspacing='0'>
<tr>
<td width='38' NOWRAP><a href='faq_admin.php?o=$o'><img src='../../images/cat_open.gif' border='0'></a></td>
<td valign='bottom'>
<strong><a href='faq_admin.php?task=mod_cat&c_id=$faqcat_info[c_id]&o=$o_url' class='admin_menu'>$faqcat_info[category]</a></strong>
</td>
</tr>
</table>
";

if($catcount == mysql_num_rows($faqcat)) {
$leftimage = "<img src='../../images/trans.gif'>";
} else {
$leftimage = "<img src='../../images/space_left.gif'>";
}

$questions1 = mysql_query("SELECT q_id, c_id, question FROM ".$site_name."_faq".$game_number."_questions WHERE c_id='$faqcat_info[c_id]' ORDER BY q_order");
$total_questions = mysql_num_rows($questions1);
$count = 0;
while($question = mysql_fetch_assoc($questions1)) {
$count = $count + 1;
echo "
<table cellpadding='0' cellspacing='0'>
<tr>
<td width='56'>$leftimage<img src='../../images/question"; if($count == $total_questions) { echo "_last"; } echo ".gif' border='0'></td>
<td valign='bottom'>
<a href='faq_admin.php?task=mod_quest&q_id=$question[q_id]&o=$o_url' class='admin_menu'>$question[question]</a>
</td>
</tr>
</table>
";
}
} else {
$o = implode(",", $open);
$o = $o.",".$faqcat_info[c_id];
echo "
<table cellpadding='0' cellspacing='0'>
<tr>
<td width='38' NOWRAP><a href='faq_admin.php?o=$o'><img src='../../images/cat_closed.gif' border='0'></a></td>
<td valign='bottom'>
<strong><a href='faq_admin.php?task=mod_cat&c_id=$faqcat_info[c_id]&o=$o_url' class='admin_menu'>$faqcat_info[category]</a></strong>
</td>
</tr>
</table>
";
}}
}

echo $foot;
exit();
}

?>