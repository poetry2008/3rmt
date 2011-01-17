<?php
define("MYSQL_USER",'jp_gamelife_jp'); 
define("MYSQL_PASSWORD",'kWSoiSiE');
define("MYSQL_HOST",'localhost');

echo mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD)?'We are alive.':'Something go wrong';
