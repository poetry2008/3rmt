echo "jp,gm,wm -> 3rmt -- "$(date)       > log
echo "jp,gm,wm -> 3rmt -- "$(date)       
echo "db_others.php start -- "$(date)    >> log
echo "move simple tables -- "$(date)   
php db_others.php                        >> log
echo "db_others.php end -- "$(date)      >> log
echo "jp_customers.php start -- "$(date) >> log
echo "move jp customers and orders data -- "$(date)
php jp_customers.php                     >> log
echo "jp_customers.php over -- "$(date)  >> log
echo "gm_customers.php start -- "$(date) >> log
echo "gm_customers.php start -- "$(date)
echo "move gm customers and orders data -- "$(date)
php gm_customers.php                     >> log
echo "gm_customers.php over -- "$(date)  >> log
echo "wm_customers.php start -- "$(date) >> log
echo "wm_customers.php start -- "$(date)
echo "move wm customers and orders data -- "$(date)
php wm_customers.php                     >> log
echo "wm_customers.php over -- "$(date)  >> log
echo "move_file.php start -- "$(date)    >> log
echo "move upload files -- "$(date)  
php move_file.php                        >> log
echo "move_file.php over -- "$(date)     >> log

echo "checkdata.php start -- "$(date)    >> log
echo "checkdata.php start -- "$(date) 
#php checkdata.php                        >> log
echo "checkdata.php over -- "$(date)     >> log
echo "checkdata.php over -- "$(date)   
echo "script over -- "$(date)     >> log
echo "Done! -- "$(date)   
