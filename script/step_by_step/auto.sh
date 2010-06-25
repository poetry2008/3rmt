echo "jp,gm,wm -> 3rmt -- "$(date)       > log
echo "db_others.php start -- "$(date)    >> log
php db_others.php                        >> log
echo "db_others.php end -- "$(date)      >> log
echo "jp_customers.php start -- "$(date) >> log
php jp_customers.php                     >> log
echo "jp_customers.php over -- "$(date)  >> log
echo "gm_customers.php start -- "$(date) >> log
php gm_customers.php                     >> log
echo "gm_customers.php over -- "$(date)  >> log
echo "wm_customers.php start -- "$(date) >> log
php wm_customers.php                     >> log
echo "wm_customers.php over -- "$(date)  >> log
echo "move_file.php start -- "$(date)    >> log
php move_file.php                        >> log
echo "move_file.php over -- "$(date)     >> log
echo "checkdata.php start -- "$(date)    >> log
php checkdata.php                        >> log
echo "checkdata.php over -- "$(date)     >> log
