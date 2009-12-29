+ADw-?php
/+ACo-
  +ACQ-Id: new+AF8-products.php,v 1.2 2003/05/02 12:02:47 ptosh Exp +ACQ-
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce
  Released under the GNU General Public License
  +ADw-meta http-equiv+AD0AIg-Content-Type+ACI- content+AD0AIg-text/html+ADs- charset+AD0-euc-jp+ACIAPg-
+ACo-/
?+AD4-
+ADwAIQ--- new+AF8-products //--+AD4-
        +ADw-h2+AD4- 
          +ADw-table width+AD0AIg-100+ACUAIg- border+AD0AIg-0+ACI- align+AD0AIg-center+ACI- cellpadding+AD0AIg-0+ACI- cellspacing+AD0AIg-0+ACI- summary+AD0AIgA8-?+AD0-sprintf(TABLE+AF8-HEADING+AF8-NEW+AF8-PRODUCTS, strftime('+ACU-B'))?+AD4AIgA+- 
            +ADw-tr+AD4- 
              +ADw-td width+AD0AIg-63+ACIAPgA8-img src+AD0AIg-images/design/contents/title+AF8-newproducts+AF8-left.jpg+ACI- width+AD0AIg-63+ACI- height+AD0AIg-23+ACI- title+AD0AIgA8-?+AD0-sprintf(TABLE+AF8-HEADING+AF8-NEW+AF8-PRODUCTS, strftime('+ACU-B'))?+AD4AIgA+ADw-/td+AD4- 
              +ADw-td background+AD0AIg-images/design/contents/title+AF8-bg.jpg+ACIAPgAm-nbsp+ADsAPA-/td+AD4- 
              +ADw-td width+AD0AIg-47+ACIAPgA8-img src+AD0AIg-images/design/contents/title+AF8-newproducts+AF8-right.jpg+ACI- width+AD0AIg-47+ACI- height+AD0AIg-23+ACI- title+AD0AIgA8-?+AD0-sprintf(TABLE+AF8-HEADING+AF8-NEW+AF8-PRODUCTS, strftime('+ACU-B'))?+AD4AIgA+ADw-/td+AD4- 
            +ADw-/tr+AD4- 
          +ADw-/table+AD4- 
        +ADw-/h2+AD4-
 
+ADw-?php
  if ( (+ACE-isset(+ACQ-new+AF8-products+AF8-category+AF8-id)) +AHwAfA- (+ACQ-new+AF8-products+AF8-category+AF8-id +AD0APQ- '0') ) +AHs-
    +ACQ-new+AF8-products+AF8-query +AD0- tep+AF8-db+AF8-query(+ACI-select p.products+AF8-id, p.products+AF8-image, p.products+AF8-tax+AF8-class+AF8-id, if(s.status, s.specials+AF8-new+AF8-products+AF8-price, p.products+AF8-price) as products+AF8-price from +ACI- . TABLE+AF8-PRODUCTS . +ACI- p left join +ACI- . TABLE+AF8-SPECIALS . +ACI- s on p.products+AF8-id +AD0- s.products+AF8-id where products+AF8-status +AD0- '1' order by p.products+AF8-date+AF8-added desc limit +ACI- . MAX+AF8-DISPLAY+AF8-NEW+AF8-PRODUCTS)+ADs-
  +AH0- else +AHs-
    +ACQ-new+AF8-products+AF8-query +AD0- tep+AF8-db+AF8-query(+ACI-select distinct p.products+AF8-id, p.products+AF8-image, p.products+AF8-tax+AF8-class+AF8-id, if(s.status, s.specials+AF8-new+AF8-products+AF8-price, p.products+AF8-price) as products+AF8-price from +ACI- . TABLE+AF8-PRODUCTS . +ACI- p left join +ACI- . TABLE+AF8-SPECIALS . +ACI- s on p.products+AF8-id +AD0- s.products+AF8-id, +ACI- . TABLE+AF8-PRODUCTS+AF8-TO+AF8-CATEGORIES . +ACI- p2c, +ACI- . TABLE+AF8-CATEGORIES . +ACI- c where p.products+AF8-id +AD0- p2c.products+AF8-id and p2c.categories+AF8-id +AD0- c.categories+AF8-id and c.parent+AF8-id +AD0- '+ACI- . +ACQ-new+AF8-products+AF8-category+AF8-id . +ACI-' and p.products+AF8-status +AD0- '1' order by p.products+AF8-date+AF8-added desc limit +ACI- . MAX+AF8-DISPLAY+AF8-NEW+AF8-PRODUCTS)+ADs-
  +AH0-

  +ACQ-num+AF8-products +AD0- tep+AF8-db+AF8-num+AF8-rows(+ACQ-new+AF8-products+AF8-query)+ADs-
  if (0 +ADw- +ACQ-num+AF8-products) +AHs-
    +ACQ-info+AF8-box+AF8-contents +AD0- array()+ADs-
    +ACQ-info+AF8-box+AF8-contents+AFsAXQ- +AD0- array('text' +AD0APg- sprintf(TABLE+AF8-HEADING+AF8-NEW+AF8-PRODUCTS, strftime('+ACU-B')))+ADs-
     echo '
	 +ADw-table width+AD0AIg-100+ACUAIg-  border+AD0AIg-0+ACI- cellspacing+AD0AIg-0+ACI- cellpadding+AD0AIg-0+ACI- align+AD0AIg-center+ACIAPg- 
          +ADw-tr+AD4-'+ADs-
 //   new contentBoxHeading(+ACQ-info+AF8-box+AF8-contents)+ADs-

    +ACQ-row +AD0- 0+ADs-
    +ACQ-col +AD0- 0+ADs-
	+ACQ-info+AF8-box+AF8-contents +AD0- array()+ADs-
    while (+ACQ-new+AF8-products +AD0- tep+AF8-db+AF8-fetch+AF8-array(+ACQ-new+AF8-products+AF8-query)) +AHs-
      +ACQ-row +ADs-
    +ACQ-product+AF8-query +AD0- tep+AF8-db+AF8-query(+ACI-select products+AF8-name, products+AF8-description+AF8AIg-.ABBR+AF8-SITENAME.+ACI- from +ACI- . TABLE+AF8-PRODUCTS+AF8-DESCRIPTION . +ACI- where products+AF8-id +AD0- '+ACI- . +ACQ-new+AF8-products+AFs-'products+AF8-id'+AF0- . +ACI-' and language+AF8-id +AD0- '+ACI- . +ACQ-languages+AF8-id . +ACI-'+ACI-)+ADs-
    +ACQ-product+AF8-details +AD0- tep+AF8-db+AF8-fetch+AF8-array(+ACQ-product+AF8-query)+ADs-
  
  
    +ACQ-new+AF8-products+AFs-'products+AF8-name'+AF0- +AD0- +ACQ-product+AF8-details+AFs-'products+AF8-name'+AF0AOw-
	
	  if(mb+AF8-strlen(+ACQ-new+AF8-products+AFs-'products+AF8-name'+AF0-) +AD4- 17) +AHs-
	     +ACQ-products+AF8-name +AD0- mb+AF8-substr(+ACQ-new+AF8-products+AFs-'products+AF8-name'+AF0-,0,17)+ADs-
		  +ACQ-ten +AD0- '..'+ADs-
	    +AH0-else+AHs-
          +ACQ-products+AF8-name +AD0- +ACQ-new+AF8-products+AFs-'products+AF8-name'+AF0AOw-
		  +ACQ-ten +AD0- ''+ADs-
	  +AH0-
	// edit 2009.5.14 maker
	//+ACQ-description+AF8-array +AD0- explode(+ACIAfA--+ACM--+AHwAIg-, +ACQ-product+AF8-details+AFs-'products+AF8-description+AF8-'.ABBR+AF8-SITENAME+AF0-)+ADs-
	+ACQ-description+AF8-view +AD0- strip+AF8-tags(mb+AF8-substr(+ACQ-product+AF8-details+AFs-'products+AF8-description+AF8-'.ABBR+AF8-SITENAME+AF0-,0,63))+ADs-
//	+ACQ-description +AD0- strip+AF8-tags(mb+AF8-substr (+ACQ-description+AF8-array+AFs-0+AF0-,0,50))+ADs-
?+AD4-
            +ADw-td width+AD0AIg-250+ACIAPgA8ACE--- products+AF8-id +ADw-?+AD0AJA-new+AF8-products+AFs-'products+AF8-id'+AF0-?+AD4---+AD4APA-table width+AD0AIg-250+ACI-  border+AD0AIg-0+ACI- cellspacing+AD0AIg-0+ACI- cellpadding+AD0AIg-0+ACIAPg- 
              +ADw-tr+AD4- 
                +ADw-td width+AD0AIgA8-?+AD0-SMALL+AF8-IMAGE+AF8-WIDTH?+AD4AIg- style+AD0AIg-padding-right:8px+ADs- +ACI- align+AD0AIg-center+ACIAPgA8-?php echo '+ADw-a href+AD0AIg-' . tep+AF8-href+AF8-link(FILENAME+AF8-PRODUCT+AF8-INFO, 'products+AF8-id+AD0-' . +ACQ-new+AF8-products+AFs-'products+AF8-id'+AF0-) . '+ACIAPg-' . tep+AF8-image(DIR+AF8-WS+AF8-IMAGES . +ACQ-new+AF8-products+AFs-'products+AF8-image'+AF0-, +ACQ-new+AF8-products+AFs-'products+AF8-name'+AF0-, SMALL+AF8-IMAGE+AF8-WIDTH, SMALL+AF8-IMAGE+AF8-HEIGHT) . '+ADw-/a+AD4-' +ADs- ?+AD4APA-/td+AD4- 
                +ADw-td valign+AD0AIg-top+ACI- style+AD0AIg-padding-left:5px+ADs- +ACIAPgA8-p class+AD0AIg-main+ACIAPgA8-img src+AD0AIg-images/design/box/arrow+AF8-2.gif+ACI- width+AD0AIg-5+ACI- height+AD0AIg-5+ACI- hspace+AD0AIg-5+ACI- border+AD0AIg-0+ACI- align+AD0AIg-absmiddle+ACIAPgA8-?php echo '+ADw-a href+AD0AIg-' . tep+AF8-href+AF8-link(FILENAME+AF8-PRODUCT+AF8-INFO, 'products+AF8-id+AD0-' . +ACQ-new+AF8-products+AFs-'products+AF8-id'+AF0-) . '+ACIAPg-'.+ACQ-products+AF8-name.+ACQ-ten.'+ADw-/a+AD4-'+ADs-?+AD4APA-br+AD4- 
                  +ADw-span class+AD0AIg-red+ACIAPgA8-?php echo +ACQ-currencies-+AD4-display+AF8-price(+ACQ-new+AF8-products+AFs-'products+AF8-price'+AF0-, tep+AF8-get+AF8-tax+AF8-rate(+ACQ-new+AF8-products+AFs-'products+AF8-tax+AF8-class+AF8-id'+AF0-)) +ADs- ?+AD4APA-/span+AD4APA-br+AD4- 
                  +ADw-span class+AD0AIg-smallText+ACIAPgA8-?php echo +ACQ-description+AF8-view+ADs- ?+AD4-...+ADw-/span+AD4APA-/p+AD4APA-/td+AD4- 
              +ADw-/tr+AD4- 
            +ADw-/table+AD4- 
            +ADw-br+AD4- 
            +ADw-div class+AD0AIg-dot+ACIAPgAm-nbsp+ADsAPA-/div+AD4APA-/td+AD4- 
+ADw-?php      
		 if ((+ACQ-row/2) +AD0APQ- floor(+ACQ-row/2)) +AHs-
           echo '+ADw-/tr+AD4-'.+ACIAXA-n+ACI-.'+ADw-tr+AD4-' +ADs-
         +AH0- else +AHs-
		   echo '+ADw-td+AD4-'.tep+AF8-draw+AF8-separator('pixel+AF8-trans.gif', '10', '1').'+ADw-/td+AD4-'.+ACIAXA-n+ACIAOw-
		 +AH0-  
    +AH0-

    //new contentBox(+ACQ-info+AF8-box+AF8-contents)+ADs-
  echo '+ADw-/tr+AD4APA-/table+AD4-' +ADs-
  
  +AH0-
?+AD4-
+ADwAIQ--- new+AF8-products+AF8-eof //--+AD4-
