<?php
define("MYSQL_USER",'root');
define("MYSQL_PASSWORD",'123456');
define("MYSQL_HOST",'localhost');

echo mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD)?'We are alive.':'Something go wrong';
