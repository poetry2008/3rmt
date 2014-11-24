<?php

$iimy_search_array =array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    );
$rmt_url_match = array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                        'rmtrank_url'=>'<td class="col0"><a href="(.*?)">.*?<\/a><\/td>'  
                    );
$rmtrank_match = array(
	'rank_price'=>'<td class="colb2top">([0-9,.]+)\(<font color=".*?">.*?</font>\)',
	'rank_inventory'=>'<td class="colb2top"><span class="nobr">([0-9,.]+)口</span></td>'
);

$search_array_match = array(
	'buy'=> array(
		'FF14' => array(
			'www.mugenrmt.com'=>array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>([a-zA-Z]+).*?\-rmt<\/td>',
                      '6-20'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                       '21-500'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                       'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
           'www.matubusi.com'=>array('products_name'=>'<th class="rowheader">.*?([^>]*?)<br>.*?<\/th>',
                      'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">[0-9,]+円<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'ftb-rmt.jp'=> array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<br>.*?<\/A><\/td>',
                      '1-29'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '30-59'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '60-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
           'www.rmt-wm.com'=>array('products_name'=>'<td rowspan="3"><span>([a-zA-Z]+)\(?L?E?G?A?C?Y?\)?<\/span><\/td>',
                      '1-9'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '10-29'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '30-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">(.*?)<\/td>' 
                    ),
           'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
        'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
        'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                   array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
            'RO' => array(
				  'www.mugenrmt.com'=>array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>([a-zA-Z]+?)<\/td>',
                        '10-99'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '100-9999'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                   'www.matubusi.com'=> array('products_name'=>'<th class="rowheader">.*?([^>]*?)<br>.*?<\/th>',
                      'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">[0-9,]+円<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                 'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<br>.*?<\/A><\/td>',
                      '1-19'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '20-29'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '30-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                'www.rmt-wm.com'=> array('products_name'=>'<td rowspan="3"><span>([a-zA-Z]+)\(?.*?\)?<\/span><\/td>',
                      '1-49'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '50-99'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '100-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">(.*?)<\/td>' 
                    ),
                'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
               'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+)\(?.*?\)?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ),
               'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
        'RS'=>array(
              'www.mugenrmt.com'=> array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>[0-9]*?\s*?([\sa-zA-Z]+?)\s*?<\/td>',
                      '1-29'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                      '30-10000'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                      'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                'www.matubusi.com'=> array('products_name'=>'<th class="rowheader">.*?([^>]*?)<br>.*?<\/th>',
                      'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">[0-9,]+円<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                'ftb-rmt.jp'=> array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<br>.*?<\/A><\/td>',
                      '1-9'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '10-19'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '20-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                'www.rmt-wm.com'=>array('products_name'=>'<td rowspan="3"><span>(.*?)<\/span><\/td>',
                      '5-99'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '100-199'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '200-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">(.*?)<\/td>' 
                    ),
                 'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),

               'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ),
              'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
             'pastel-rmt.jp'=>array('products_name'=>'<td class="center">.*?([\sa-zA-Z]+?).*?<\/td> ',
                        'price'=>'<td class="center" nowrap="nowrap" colspan="3">.*?<span style="font-weight: bold;">(.*?)<\/span>.*?<\/td>',
                        'inventory'=>'<td class="center" nowrap="nowrap" colspan="3">.*?<span style="font-weight: bold;">.*?<\/span>.*?<\/td><td class="center" nowrap="nowrap">(.*?)<\/td>'
                        ),
            'rmt.diamond-gil.jp'=> array('products_name'=>'<td align="left" bgcolor="#D6ECFC">([\sa-zA-Z]+?)<\/td>',
                          '10-49'=>'<td align="center">[0-9,]+&nbsp;円<\/td><td align="center">([0-9,]+)&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td>',
                          '50-99'=>'<td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">([0-9,]+)&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td>',
                          '100-199'=>'<td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">([0-9,]+)&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td>',
                          '200-'=>'<td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">([0-9,]+)&nbsp;円<\/td>',
                          'inventory'=>'<td align="left" bgcolor="#D6ECFC">[\sa-zA-Z]+?<\/td><td align="center">(.*?)<\/td>'
                        
                        ),
                    
            'www.asahi-rmt-service.com'=>  array('products_name'=>'<td class="left" style=".*?"><a href=".*?">(.*?)<\/a><\/td>',
                          'price'=>'<td class="left" style=".*?"><a href=".*?">.*?<\/a><\/td>.*?<td style=".*?">(.*?)円<\/td>',
                          'inventory'=>'<td style=".*?">.*?WM<\/td>.*?<td style=".*?">(.*?) 口<\/td>'
                       ),
              'www.rmt-king.com'=> array('products_name'=>'<tr .*?>.*?<td class="center" rowspan="4">(.*?)<\/td>',
                          '1-9'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>',
                          '10-29'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>',
                          '30-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*?<\/td>',
                          'inventory'=>'<td class="center" rowspan="4" nowrap="nowrap">(.*?)<\/td>.*?<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>'
                       ),
              'www.rmtsonic.jp'=> array('products_name'=>'<td class=.*? style="cursor:pointer;"><a onClick=".*?">(.*?)<\/a><\/td>',
                           '1-99'=>'<td class=.*?>([0-9,.]+)円<\/td><td class=.*?>[0-9,.]+円<\/td><td class=.*?>.*?円<\/td><td class=.*?>.*?WM<\/td>', 
                           '100'=>'<td class=.*?>[0-9,.]+円<\/td><td class=.*?>([0-9,.]+)円<\/td><td class=.*?>.*?円<\/td><td class=.*?>.*?WM<\/td>',
                           'inventory'=>'<td class=.*? style="cursor:pointer;"><a onClick=".*?">.*?<\/a><\/td>.*?<td class=.*? style="cursor:pointer;"><a href=".*?" onClick=".*?">(.*?)<\/a><\/td>'
                        ),
                    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                  ),
      'FF11' =>  array(  
                  'www.mugenrmt.com'=> array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>\s*?([\sa-zA-Z]+?) rmt*?<\/td>',
                        '1-99'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '100-10000'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)口<\/td>'
                      ), 
                   'www.matubusi.com'=> array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">([a-zA-Z]+).*?<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>.*?<td class="center">.*?円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">([a-zA-Z]+).*?<\/A><\/td>',
                      '1-9'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '10-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                   'www.rmt-wm.com'=>  array('products_name'=>'<td rowspan="3"><span>(.*?)<\/span><\/td>',
                      '1-49'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '50-99'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '100-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">(.*?)<\/td>' 
                    ),
                   'rmtrank.com'=> array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                   ),
                  'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ),  
                    'www.iimy.co.jp'=>array('products_name'=>'<name>(.*?)の.*?<\/name>',
                      'price'=>'<price>([0-9,.]+)円<\/price>',
                      'inventory'=>'<quantity>(.*?)<\/quantity>' 
                    ),

               'pastel-rmt.jp'=> array(),
               'rmt.diamond-gil.jp'=> array('products_name'=>'<td align="left" bgcolor="#D6ECFC">.*?<a href=".*?">([a-zA-Z]+)<\/a>.*?<\/td>',
                      '10-99'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                     '100-199'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                     '200-299'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                     '300-'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
                      'inventory'=>'<td align="center">.*?<a href=".*?">.*?<\/a>.*?<\/td><td align="center"><a href=".*?"><b style="text-decoration:none;">(.*?)&nbsp;口<\/b><\/a><\/td>'
                          ),
                                             
               'www.asahi-rmt-service.com'=>array('products_name'=>'<td class="left" .*?><a href=".*?">.*?([a-zA-Z]+).*?<\/a><\/td>',
                      'price'=>'<td class="left" .*?><a href=".*?">.*?<\/a>.*?<\/td>.*?<td .*?>([0-9.,]+).*?<\/td>',
                      'inventory'=>'<td style=".*?">.*?WM<\/td>.*?<td style=".*?">.*?([0-9,]+) 口.*?<\/td>' 
                    ),
               'www.rmt-king.com'=>array('products_name'=>'<tr .*?>.*?<td class="center" rowspan="4">(.*?)<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                      '1-2'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '3-4'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '5-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                    'inventory'=>'<td class="center" rowspan="4">.*?<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">(.*?)<\/td>' 
                    ),
               'www.rmtsonic.jp'=> array('products_name'=>'<td class=.*? rowspan="2" width=200>(.*?)<\/td>',
                           'price'=>'<td class=.*? rowspan="2" width=200>.*?<\/td>.*?<td class=.*? rowspan="2" width=100>.*?<\/td><td class=.*?>.*?([0-9.,]+)円.*?<\/td>',
                           'inventory'=>'<td class=.*? rowspan="2" width=200>.*?<\/td>.*?<td class=.*? rowspan="2" width=100>.*?([0-9.,]+)&nbsp;口.*?<\/td>'
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                  ),
       'DQ10'=>array(
                 'www.mugenrmt.com'=> array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>(.*?)<\/td>',
                        '51-100'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '101-9999'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                  'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                 'ftb-rmt.jp'=> array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<\/A><\/td>',
                      '1-49'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '50-99'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '100-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                   'www.rmt-wm.com'=> array('products_name'=>'<td rowspan="3"><span>(.*?)<\/span><\/td>',
                      '1-9'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '10-29'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '30-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">.*?([0-9,]+口).*?<br\/>.*?<a .*?>.*?<img.*?>.*?<\/td>' 
                    ),
                'rmtrank.com'=> array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ), 
                'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
                 'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                'pastel-rmt.jp'=> array('products_name'=>'<td class="center" rowspan="3">(.*?)<\/td>.*?<td class="center" rowspan="3" nowrap="nowrap">.*?銀行振込.*?<\/td>',
                      '1-19'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                      '20-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                      '50-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>',
                      'inventory'=>'<td class="center" rowspan="3">.*?<\/td>.*?<td class="center" rowspan="3" nowrap="nowrap">.*?([0-9,]+)&nbsp;口.*?<\/td>' 
                    ),
               'rmt.diamond-gil.jp'=>array('products_name'=>'<td align="left" bgcolor="#D6ECFC">.*?<a href=".*?">(.*?)<\/a>.*?<\/td>',
                      '1-49'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '50-99'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '100-'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
          	      'inventory'=>'<td align="left" bgcolor="#D6ECFC">.*?<\/td>.*?<td align="center">(.*?)<\/td>'
                    ),
               'www.asahi-rmt-service.com'=>array('products_name'=>'<td class="left" style=".*?"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="left" style=".*?"><a href=".*?">.*?<\/a><\/td>.*?<td style=".*?">([0-9,]+)円<\/td>',
                      'inventory'=>'<td style=".*?">.*?WM<\/td>.*?<td style=".*?">([0-9,]+) 口<\/td>' 
                    ),
               'www.rmt-king.com'=> array('products_name'=>'<td class="center" rowspan="4">(.*?)<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?<\/td>',
                      '1-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '50-149'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '150-299'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '300-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td .*?>.*?([0-9.,]+)円.*?<\/td>',
                      'inventory'=>'<td class="center" rowspan="4">.*?<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?([0-9,.]+).*?<\/td>' 
                    ),
               'www.rmtsonic.jp'=> array('products_name'=>'<tr><td class=buygmnr2 style="cursor:pointer;"><a .*?>(.*?)<\/a><\/td>',
                      '1-99'=>'<td class=buygmnr2>([0-9,]+)円<\/td><td class=buygmnr2>.*?円<\/td><td class=buygmnr2>.*?円<\/td>',
                      '100-'=>'<td class=buygmnr2>.*?円<\/td><td class=buygmnr2>([0-9,]+)円<\/td><td class=buygmnr2>.*?円<\/td>',
	     	      'inventory'=>'<td class=buygmnr2 style="cursor:pointer;"><a .*?>([0-9.,]+&nbsp;口)<\/a><\/td>.*?<td class=buygmnr2>.*?円<\/td>' 
                    ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                 'rmt1.jp'=> array('products_name'=>'<td class="center" rowspan="4">(.*?)<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?<\/td>',
                      '1-4'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '5-9'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '10-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>',
                      'inventory'=>'<td class="center" rowspan="4">.*?<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?([0-9,.]+).*?<\/td>' 
                    ),
                  ),
       'L2'=> array(
                 'www.mugenrmt.com'=> array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>[0-9１２３４５鯖]+(.*?)rmt<\/td>',
                          '5-30'=>'<td class=\'border03 border04\'>([0-9,.]+)円<span style=\'margin-right:5px\'>.*?<\/span>.*?WM<\/td><td class=\'border03 border04\'>[0-9,.]+円<span style=\'margin-right:5px\'>.*?<\/span>.*?WM<\/td>',
                          '31-500'=>'<td class=\'border03 border04\'>[0-9,.]+円<span style=\'margin-right:5px\'>.*?<\/span>.*?WM<\/td><td class=\'border03 border04\'>([0-9,.]+)円<span style=\'margin-right:5px\'>.*?<\/span>.*?WM<\/td>',
                          'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'                       
                       ),
                  'www.matubusi.com'=> array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                 'ftb-rmt.jp'=> array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-9'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '10-19'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '20-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                   'www.rmt-wm.com'=> array('products_name'=>'<tr class=".*?">.*?<td rowspan="3"><span>(.*?)<\/span><\/td>',
                          '1-19'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9.,]+)円<\/td><td>[0-9.,]+円<\/td><td>[0-9.,]+円<\/td>',
                          '20-49'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9.,]+円<\/td><td>([0-9.,]+)円<\/td><td>[0-9.,]+円<\/td>',
                          '50-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9.,]+円<\/td><td>[0-9.,]+円<\/td><td>([0-9.,]+)円<\/td>',
                           'inventory'=>'<td>[0-9.,]+円<\/td><td>[0-9.,]+円<\/td><td>[0-9.,]+円<\/td>.*?<td rowspan="3">([0-9,.]+)口<br\/>'
                      ),
                  'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                         ),
                  'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ),
                  'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
				
               'pastel-rmt.jp'=> array('products_name'=>'<tr .*?>.*?<td class="center" rowspan="3">(.*?)<\/td>.*?<td class="center" rowspan="3" nowrap="nowrap">.*?<span style="color: #CC3333; font-weight: bold;">.*?<\/span>.*?<\/td>',                
                          '1-9'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>',
                          '10-19'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>',
                          '20-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*?<\/td>',
                          'inventory'=>'<td class="center" rowspan="3">.*?<\/td>.*?<td class="center" rowspan="3" nowrap="nowrap">.*?<span style="color: #CC3333; font-weight: bold;">(.*?)<\/span>.*?<\/td>'
                        ),
                        
                'rmt.diamond-gil.jp'=> array('products_name'=>'<td align="left" bgcolor="#D6ECFC">.*?<a href=".*?">(.*?)<\/a>.*?<\/td>',
                      '1-19'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '20-49'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '50-99'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '100-'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
					  'inventory'=>'<a href=".*?">.*?<b .*?>(.*?)<\/b>.*?<\/a>' 
                    ),
            
                'www.asahi-rmt-service.com'=>array('products_name'=>'<td class="left" style=".*?"><a href=".*?">(.*?)<\/a><\/td>',
                        'price'=>'<td class="left" style=".*?"><a href=".*?">.*?<\/a><\/td>.*?<td .*?>([0-9]+)円<\/td>',
                        'inventory'=>'<td style=".*?">.*?WM<\/td>.*?<td style=".*?">([0-9]+) 口<\/td>'
                        ),
              'www.rmt-king.com'=> array('products_name'=>'<tr .*?>.*?<td class="center" rowspan="4">(.*?)<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                    '1-9'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '10-29'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                      '30-99'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '100-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" colspan="2" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                      'inventory'=>'<td class="center" rowspan="4">.*?<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">(.*?)<\/td>' 
                    ),
              'www.rmtsonic.jp'=>array('products_name'=>'<td class=.*? style="cursor:pointer;"><a onClick=".*?">[0-9]+(.*?)<\/a><\/td> <td class=.*? style="cursor:pointer;"><a onClick=".*?">[0-9]+&nbsp;口<\/a><\/td>',
                        '1-5'=>'<td class=.*? style="cursor:pointer;"><a onClick=".*?">[0-9]+&nbsp;口<\/a><\/td><td class=.*?>([0-9]+)円<\/td><td class=.*?>[0-9]+円<\/td>',
                        '6'=>'<td class=.*? style="cursor:pointer;"><a onClick=".*?">[0-9]+&nbsp;口<\/a><\/td><td class=.*?>[0-9]+円<\/td><td class=.*?>([0-9]+)円<\/td>',
                        'inventory'=>'<td class=.*? style="cursor:pointer;"><a onClick=".*?">[0-9]+.*?<\/a><\/td> <td class=.*? style="cursor:pointer;"><a onClick=".*?">([0-9]+)&nbsp;口<\/a><\/td>'
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'site_names'=>'<td class="position-relative">(.*?)<\/td><td class="compare"><span>.*?<\/span><\/td><td class="price sort">([0-9,.]+)円<\/td><td class="price">.*?<\/td>', 
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        '0'=>'<td class="price sort">([0-9,.]+)円<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                        'inventory'=>'<td class="price sort">[0-9,.]+円<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'site_names'=>'<td class="position-relative">(.*?)<\/td><td class="compare"><span>.*?<\/span><\/td><td class="price sort">([0-9,.]+)円<\/td><td class="price">.*?<\/td>', 
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        '0'=>'<td class="price sort">([0-9,.]+)円<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                        'inventory'=>'<td class="price sort">[0-9,.]+円<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
       'ARAD'=> array(
           'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-24'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '25-49'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '50-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
              'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ), 
             'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
               'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
			     array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                  ),
      'nobunaga'=>array(
            'www.matubusi.com'=> array('products_name'=>'<th class="rowheader">.*?([^<]*).*?<\/th>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-19'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '20-49'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '50-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ), 
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
             'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ),
            'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
			          array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                  ),
        'PSO2' => array(
            'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
             'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ), 
             'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
                'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
				        array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                  ),
        'L1' => array(
              'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">.*?<br>(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
              'rmtrank.com'=> array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ), 
              'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
              'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
               'www.rmt-king.com'=>array('products_name'=>'<tr .*?>.*?<td class="center" rowspan="4">(.*?)<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?<\/td>',
                      '1-9'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '10-29'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>',
                      '30-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                      'inventory'=>'<tr .*?>.*?<td class="center" rowspan="4">.*?<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">(.*?)<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
        'TERA'=>array(
	          'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
		        'rmtrank.com'=> array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
               'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
                'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
			          array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                  ),
        'AION'=>array(
             'www.matubusi.com'=> array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
              'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-9'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '10-29'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '30-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
             'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
               'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
              'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
			    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                  ),
            'CABAL' => array(
	           'www.matubusi.com'=> array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
              'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-49'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '50-99'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '100-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
                'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
				     array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
	  'WZ'=>array(
       		  'www.mugenrmt.com'=>array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>(.*?)<\/td>',
                        '1-50'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '51-5000'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
              'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                        'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">[0-9,]+円<\/td>',
                        'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                      ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ),
                'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
				    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
        'latale'=>array(
                 'www.matubusi.com'=> array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                  'rmtrank.com'=> array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
                'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
                'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
			   'www.asahi-rmt-service.com'=>array('products_name'=>'<td class="left" style=".*?"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="left" style=".*?"><a href=".*?">.*?<\/a><\/td>.*?<td style=".*?">([0-9,]+)円<\/td>',
                      'inventory'=>'<td style=".*?">.*?WM<\/td>.*?<td style=".*?">([0-9,]+) 口<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
        'blade'=> array(
           'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">([0-9,]+).*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">.*?([0-9,]+).*?<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
                'www.iimy.co.jp'=>array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
				'rmt.diamond-gil.jp'=> array('products_name'=>'<td align="left" bgcolor="#D6ECFC">.*?<a href=".*?">(.*?)<\/a>.*?<\/td>',
                      '1-4'=>'<td align="center">.*?円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '5-9'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '10-'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
                      'inventory'=>'<a href=".*?"><b style=".*?">([0-9,]+)&nbsp;口<\/b><\/a>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),

	  'megaten' => array(
            'www.mugenrmt.com'=>array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>[0-9.,] (.*?)-rmt<\/td>',
                        '1-100'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '101-9999'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
             'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                        'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">[0-9,]+円<\/td>',
                        'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                      ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
             'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
             'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
			      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
         'EWD'=>array(
             'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
             'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
               'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
			       array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
        'LH'=>array(
		   'www.matubusi.com'=> array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
           'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-49'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '50-99'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '100-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
           'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
             'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
				 array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
	  'HR'=> array(
  		    'www.mugenrmt.com'=> array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>(.*?)<\/td>',
                        '1-10'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '11-2000'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
           'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                        'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">[0-9,]+円<\/td>',
                        'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                      ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
           'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ),
            'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
		           array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
        'AA'=>array(
          'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">([a-zA-Z]+).*?<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">([0-9,]+).*?<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
           'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
            'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
		           array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
        'ThreeSeven'=> array(
             'www.matubusi.com'=> array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
              'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)販売<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
		        ),
        'ECO' => array(
               /*       array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                      'price'=>'<td><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,]+)円<\/td>',
                      'inventory'=>'<td>.*?円<\/span><\/td>.*?<td>([0-9,]+)<\/span>.*?<\/td>.*?<td class="price">.*?<a href=".*?">.*?<\/a>.*?<\/td>' 
                    ),
                */
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
                 'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
                  'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
			     'rmt.diamond-gil.jp'=> array('products_name'=>'<td align="left" bgcolor="#D6ECFC">(.*?)<\/td>',
                      '10-49'=>'<td align="center">.*?円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '50-99'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '100-299'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
                      '300-'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
                      'inventory'=>'<td align="left" bgcolor="#D6ECFC">.*?<\/td>.*?<td align="center">(.*?)<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
	  'FNO'=>array(
        'www.mugenrmt.com'=>array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>.*?_(.*?)<\/td>',
                        '1-10'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '11-100'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
         'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
           'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
            'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
				   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
        'SUN'=>array(
             'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=> array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
            'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
             'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
    'talesweave'=>array(
           'rmtrank.com'=> array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
            'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
            'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
        'MU'=>array(
             'www.matubusi.com'=> array('products_name'=>'<th class="rowheader">(.*?)<\/th>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
             'rmtrank.com'=> array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
              'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の宝石販売<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
        'C9'=>array(
             'www.matubusi.com'=> array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),

                'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
	  'MS'=>array(
	         'www.mugenrmt.com'=> array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>[0-9,.]+(.*?)<\/td>',
                        '5-10'=>'<td height=\'24\' class=\'border03 border04\'>[0-9,.]+.*?<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '11-500'=>'<td height=\'24\' class=\'border03 border04\'>[0-9,.]+.*?<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td height=\'24\' class=\'border03 border04\'>[0-9,.]+.*?<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
             'www.rmt-wm.com'=>array('products_name'=>'<td rowspan="3"><span>(.*?)<\/span><\/td>',
                      '1-19'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '20-49'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '50-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
               'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                 'rmt.diamond-gil.jp'=> array('products_name'=>'<td align="left" bgcolor="#D6ECFC"><a href=".*?">[0-9,.]+(.*?)<\/a>.*?<\/td>',
                      '20-49'=>'<td align="center">[0-9]+&nbsp;円<\/td><td align="center">([0-9]+)&nbsp;円<\/td><td align="center">[0-9]+&nbsp;円<\/td><td align="center">[0-9]+&nbsp;円<\/td>',
                      '50-99'=>'<td align="center">[0-9]+&nbsp;円<\/td><td align="center">[0-9]+&nbsp;円<\/td><td align="center">([0-9]+)&nbsp;円<\/td><td align="center">[0-9]+&nbsp;円<\/td>',
                      '100-'=>'<td align="center">[0-9]+&nbsp;円<\/td><td align="center">[0-9]+&nbsp;円<\/td><td align="center">[0-9]+&nbsp;円<\/td><td align="center">([0-9]+&nbsp;)円<\/td>',
                      'inventory'=>'<td align="center"><a href=".*?"><b style=".*?">([0-9,.]+)&nbsp;口<\/b><\/a><\/td>'
                      ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
        'cronous'=>array(
                'www.matubusi.com'=>array('products_name'=>'<th class="rowheader">(.*?)<\/th>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                'rmtrank.com'=> array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
                 'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  ),
      'tenjouhi'=>array(
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
               'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-49'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '50-99'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '100-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                  'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
        'rose'=>array(
            'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
              'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
         'hzr'=>array(
               'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
                  'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                      ),
        'dekaron'=>array(
             'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
               'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
                 'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
        'fez'=> array(
              'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">([a-zA-Z]+)<br>.*?<\/A><\/td>',
                      '1-19'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '20-29'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '30-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
             'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
             'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
       'lakatonia'=> array(
                'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-2'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '3-4'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '5-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                  'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
        'moe' => array(
                'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.diamond-gil.jp'=>array('products_name'=>'<td align="left" bgcolor="#D6ECFC">.*?<a href=".*?">([a-zA-Z]+)<\/a>.*?<\/td>',
                      '5-9'=>'<td align="center">.*?円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '10-49'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '50-'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
                      'inventory'=>'<td align="left" bgcolor="#D6ECFC"><a href=".*?">.*?<\/td>.*?<td align="center"><a href=".*?"><b .*?>(.*?)&nbsp;口<\/b><\/a><\/td>' 
                    ),
                  ),
       'mabinogi' => array(
             'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
            'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
        'WF'=> array(

                'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
               'www.rmt-king.com'=>array('products_name'=>'<td class="center" rowspan="4">(.*?)<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?<\/td>',
                      '1-24'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '25-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>',
                      '50-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                      'inventory'=>'<td class="center" rowspan="4">.*?<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?([0-9,.]+)&nbsp;.*?<\/td>' 
                    ),
                  ),
        'rohan' => array(
               'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
                  'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
        'genshin' => array(
                'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">([0-9,]+).*?<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
               'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ), 
                'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
              'www.rmt-king.com'=>array('products_name'=>'<td class="center" rowspan="4">(.*?)<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?<\/td>',
                      '1-19'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '20-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>',
                      '50-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                      'inventory'=>'<td class="center" rowspan="4">.*?<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?([0-9,.]+).*?<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
	  ),
                  ),
'sell' => array(
		'FF14'=>array(
			        array(),
            'www.matubusi.com'=>array('products_name'=>'<th class="rowheader">.*?([^>]*?)<br>.*?<\/th>',
                      'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<br>.*?<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">([0-9,.]+)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
            'www.rmt-wm.com'=>array('products_name'=>'<td class="th"><?a? ?h?r?e?f?=?"?.*?"?>?([a-zA-Z]+)\(?L?E?G?A?C?Y?\)?<?\/?a?>?<\/td>',
                      'price'=>'<td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td>[0-9,.]+?円<\/td>.*?<td>[0-9,.]+?Pt<\/td>.*?<td>(.*?)<\/td>' 
                    ),
          /*          array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>',
                            'price'=>'<td class="price"><a href=".*?">.*?([0-9]+)<\/a><\/td>.*?<td>.*?<\/span>口<\/td>',
                            'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>(.*?)<\/span>口<\/td>'
                       ),
		   */
		   
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
            'rmt.kakaran.jp'=>array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                          'price'=>'<td class="price"><a href=".*?">.*?([0-9]+)<\/a><\/td>',
                         'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
               'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
	   'RO'=>array(
	           'www.mugenrmt.com'=> array(),
               'www.matubusi.com'=>array('products_name'=>'<th class="rowheader">.*?([^>]*?)<br>.*?<\/th>',
                      'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
              'ftb-rmt.jp'=> array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<br>.*?<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
              'www.rmt-wm.com'=>array('products_name'=>'<td class="th">([a-zA-Z]+).*?<\/td>',
                      'price'=>'<td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td>[0-9,.]+?円<\/td>.*?<td>[0-9,.]+?Pt<\/td>.*?<td>(.*?)<\/td>' 
                    ),
              'rmtrank.com'=> array( 'url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=> array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                          'price'=>'<td class="price"><a href=".*?">.*?([0-9]+)<\/a><\/td>',
                         'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
              'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
        'RS'=>array(
			 'www.mugenrmt.com'=>  array(),
             'www.matubusi.com'=> array('products_name'=>'<th class="rowheader">.*?([^>]*?)<br>.*?<\/th>',
                      'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
               'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<br>.*?<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
               'www.rmt-wm.com'=>array('products_name'=>'<td class="th"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td>[0-9,.]+?円<\/td>.*?<td>[0-9,.]+?Pt<\/td>.*?<td>(.*?)<\/td>' 
                    ),
               'rmtrank.com'=> array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
               'rmt.kakaran.jp'=> array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>',
                            'price'=>'<td class="price"><a href=".*?">.*?([0-9]+)<\/a><\/td>.*?<td>.*?<\/span>口<\/td>',
                            'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>(.*?)<\/span>口<\/td>'
                       ),
                'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
              'pastel-rmt.jp'=>array('products_name'=>'<td class="center">.*?([\sa-zA-Z]+).*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                            'price'=>'<td class="center" colspan="3">.*?<span style="font-weight: bold;">(.*?)<\/span>.*?<\/td>',
                            'inventory'=>'<td class="center">.*?([\sa-zA-Z]+).*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>'
                       ),
              'rmt.diamond-gil.jp'=> array('products_name'=>'<span class="sell_serverName" style="width:40%">&nbsp;(.*?)<\/span>',
                          '5-'=>'<span class="sell_serverPrice" title=".*?" style="width:33%"  onmouseover=".*?" onmouseout=".*?">(.*?)&nbsp;円<\/span>',
                          'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/span>'                        
                        ),

             'www.asahi-rmt-service.com'=>array('products_name'=>'<td class="left"><a href=".*?">(.*?)<\/a><\/td>',
                           'price'=>'<td class="left"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]*?)円<\/td>',
                           'inventory'=>'<td>[0-9,.]*?円.*?<\/td><!-- <td>0 WM<\/td> --><td>([0-9,.]*?) 口<\/td>'                        
                        ),
              'www.rmt-king.com'=> array('products_name'=>'<tr .*?>.*?<td class="center">(.*?)<\/td>',
                           '1-9'=>'<td class="center" nowrap="nowrap">.*?([0-9,]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>', 
                           '10-29'=>'<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9,]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                           '30-'=>'<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>',
                           'inventory'=>'<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9,.]*?)&nbsp;口.*?<\/td>'
                        ),
                    array(),
                  ),
    'FF11'=>array(
                    array(),
          'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">([a-zA-Z]+).*?<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
           'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">([a-zA-Z]+).*?<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),

           'www.rmt-wm.com'=>array('products_name'=>'<td class="th"><?.*?>?([^>]*?)<?\/?a?>?<\/td>',
                      'price'=>'<td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td>[0-9,.]+?円<\/td>.*?<td>[0-9,.]+?Pt<\/td>.*?<td>(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
            'rmt.kakaran.jp'=>array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                          'price'=>'<td class="price"><a href=".*?">.*?([0-9]+)<\/a><\/td>',
                         'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
             'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
            'pastel-rmt.jp'=>array(),
            'rmt.diamond-gil.jp'=>   array('products_name'=>'<span class="sell_serverName" style="width:33%">&nbsp;(.*?)<\/span>.*?<span class="sell_serverBlank">&nbsp;<\/span>',
                    '1-4'=>'<span class="sell_serverPrice" title=".*?" style="width:20%"  onmouseover=".*?" onmouseout=".*?">([0-9,.]+)&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:20%"  onmouseover=".*?" onmouseout=".*?">[0-9,.]+&nbsp;円<\/span>',   
                    '5-'=>'<span class="sell_serverPrice" title=".*?" style="width:20%"  onmouseover=".*?" onmouseout=".*?">[0-9,.]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:20%"  onmouseover=".*?" onmouseout=".*?">([0-9,.]+)&nbsp;円<\/span>',
                    'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/span>' 
                    ),
                    
          'www.asahi-rmt-service.com'=> array('products_name'=>'<td class="left"><a href=".*?">([a-zA-Z]+)<br \/>.*?<\/a>.*?<\/td>',
                           'price'=>'<td class="left"><a href=".*?">.*?<\/a>.*?<\/td>.*?<td>([0-9,]+)円<\/td>',
                           'inventory'=>'<td>.*?円<\/td>.*?<td>([0-9,]+) 口<\/td>'
                     ),

           'www.rmt-king.com'=> array('products_name'=>'<tr .*?>.*?<td class="center">.*?([a-zA-Z]+).*?<\/td>.*?<td class="center" nowrap="nowrap">.*?銀行振込<br \/>',
                      '1-2'=>'<td class="center" nowrap="nowrap">.*?([0-9,]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>',
                      '3-4'=>'<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9,]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>',
                      '5-'=>'<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9,]+)円<br \/>.*?<\/td>',
	                  'inventory'=>'<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9,]+)&nbsp;口.*?<\/td>' 
                    ),
           'www.rmtsonic.jp'=>array(),
                  ),
    'DQ10' => array(
		    array(),
             'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
             'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
              'www.rmt-wm.com'=>array('products_name'=>'<td class="th"><?.*?>?([^>]*?)<?\/?a?>?<\/td>',
                      'price'=>'<td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td>[0-9,.]+?円<\/td>.*?<td>[0-9,.]+?Pt<\/td>.*?<td>([0-9,.]+ 口).*?<\/td>' 
                    ),
	       	  'rmtrank.com'=>  array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=>   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                      'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                      'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
              'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
              'pastel-rmt.jp'=>array('products_name'=>'<td class="center">(.*?)<\/td>.*?<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>',
                      '1-19'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                      '20-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>',
                      '50-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>',
                      'inventory'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9,]+)&nbsp;口.*?<\/td>' 
                    ),
             'rmt.diamond-gil.jp'=>array('products_name'=>'<span class="sell_serverName" style="width:21%">&nbsp;(.*?)<\/span>',
                      '1-9'=>'<span class="sell_serverPrice" .*?>(.*?)円<\/span><span .*?>.*?円<\/span><span.*?>.*?円<\/span><span .*?>.*?円<\/span>',
                      '10-49'=>'<span class="sell_serverPrice" .*?>.*?円<\/span><span .*?>(.*?)円<\/span><span.*?>.*?円<\/span><span .*?>.*?円<\/span>',
                      '50-99'=>'<span class="sell_serverPrice" .*?>.*?円<\/span><span .*?>.*?円<\/span><span.*?>(.*?)円<\/span><span .*?>.*?円<\/span>',
                      '100-'=>'<span class="sell_serverPrice" .*?>.*?円<\/span><span .*?>.*?円<\/span><span.*?>.*?円<\/span><span .*?>.*?円<\/span>',
                      'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/span><\/li>' 
                    ),
           'www.asahi-rmt-service.com'=>  array('products_name'=>'<td class="left"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td>([0-9,]+)円<\/td>',
                      'inventory'=>'<td>([0-9,]+) 口<\/td>' 
                    ),
            'www.rmt-king.com'=>array('products_name'=>'<tr .*?>.*?<td class="center">(.*?)<\/td>.*?<td .*?>',
                      '1-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '50-149'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '150-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                      'inventory'=>'<td class="center" nowrap="nowrap">.*?([0-9,.]+&nbsp;口).*?<\/td>' 
                    ),
            'www.rmtsonic.jp'=>array(),
            'www.rmt-king.com'=>array('products_name'=>'<tr .*?>.*?<td class="center">(.*?)<\/td>.*?<td .*?>',
                      '1-4'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '5-9'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '10-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                      'inventory'=>'<td class="center" nowrap="nowrap">.*?([0-9,.]+&nbsp;口).*?<\/td>' 
                    ),
                  ),
     'L2'=>array(
               'www.mugenrmt.com'=>array(),
              'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
               'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                'www.rmt-wm.com'=>array('products_name'=>'<tr class=".*?">.*?<td class="th"><a href=".*?">(.*?)<\/a><\/td>',
                          'price'=>'<tr class=".*?">.*?<td class="th"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)円<\/td>',
                          'inventory'=>'<td>[0-9,.]+Pt<\/td>.*?<td>([0-9,.]+) 口.*?<\/a><\/td>'
                      ),
               'rmtrank.com'=> array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                      ),
               'rmt.kakaran.jp'=> array('products_name'=>'<tr>.*?<td><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="price"><a href=".*?">.*?([0-9]+)<\/a><\/td>',
                      'inventory'=>'<td class="price"><a href=".*?">.*?[0-9]+<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>'
                      ),
                'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
               'pastel-rmt.jp'=> array('products_name'=>'<td class="center" rowspan="3">(.*?)<\/td>.*?<td class="center" rowspan="3" nowrap="nowrap">.*?<span style=".*?">.*?<\/span>.*?<\/td>',
                         '1-9'=>'<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*? <\/td><td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*? <\/td><td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*? <\/td>',
                         '10-19'=>'<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*? <\/td><td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*? <\/td><td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*? <\/td>',
                         '20-'=>'<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*? <\/td><td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*? <\/td><td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*? <\/td>',
                         'inventory'=>'<td class="center" rowspan="3">.*?<\/td>.*?<td class="center" rowspan="3" nowrap="nowrap">.*?<span style=".*?">(.*?)<\/span>.*?<\/td>'                        
                        ),
               'rmt.diamond-gil.jp'=> array('products_name'=>'<span class="sell_serverName" style="width:40%">&nbsp;(.*?)<\/span>',
                      'price'=>'<span class="sell_serverPrice" title=".*?" style=".*?"  onmouseover=".*?" onmouseout=".*?">([0-9,.]+)&nbsp;円<\/span>',
                      'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/span>' 
                    ),
              'www.asahi-rmt-service.com'=>  array('products_name'=>'<td class="left"><a href=".*?">(.*?)<\/a>.*?<\/td>',
                          'price'=>'<td>([0-9]+)円<\/td><!-- <td>.*? WM<\/td> --><td>[0-9]+ 口<\/td>',
                          'inventory'=>'<td>[0-9]+円<\/td><!-- <td>.*? WM<\/td> --><td>([0-9]+) 口<\/td>'
                        ),
                'www.rmt-king.com'=> array('products_name'=>'<tr .*?>.*?<td class="center">(.*?)<\/td>.*?<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>',
                    '1-9'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      'inventory'=>'<td class="center" colspan="2" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>' 
                    ),
                    
                    array(),
                  ),
         'ARAD' => array(
               'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
               'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
               'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
               'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ), 
        'nobunaga'=>array(
               'www.matubusi.com'=>array('products_name'=>'<th class="rowheader">.*?([^<]*).*?<\/th>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                 'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
                'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                  'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ), 
         'PSO2'=>array(
               'www.matubusi.com'=> array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ), 
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
                'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
        'L1'=>array(
             'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">.*?<br>(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ), 
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
             'rmt.kakaran.jp'=>  array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                      'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                      'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
              'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
              'www.rmt-king.com'=> array('products_name'=>'<tr .*?>.*?<td class="center">(.*?)<\/td>.*?<td .*?>.*?銀行振込.*?<\/td>',
                     'price'=>'<td class="center" nowrap="nowrap">.*?([0-9,.]+)円.*?<\/td>',
                     'inventory'=>'<td class="center" nowrap="nowrap">.*?([0-9,.]+)&nbsp;口.*?<\/td>' 
                    ),
                  ),
        'TERA'=>array(
            'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),

            'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
             'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
        'AION'=>array(
           'www.matubusi.com'=> array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
              'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
               'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
               'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),

      'CABAL'=>array(
          'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
          'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
          'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
           'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
        'WZ'=>array(
               'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                        'price'=>'<td class="center">([0-9,]+)円<\/td>',
                        'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">.*?([0-9,]+).*?<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
               'rmt.kakaran.jp'=>  array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),

     'latale'=>array(
              'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
               'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
              'www.asahi-rmt-service.com'=>array('products_name'=>'<td class="left"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td>([0-9,]+)円<\/td>',
                      'inventory'=>'<td>([0-9,]+) 口<\/td>' 
                    ),
                  ),
      'blade'=>array(
             'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?([0-9,.]+).*?<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
              'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
              'rmt.diamond-gil.jp'=> array('products_name'=>'<span class="sell_serverName" style="width:33%">&nbsp;([^"]*?)<\/span>',
                      '1-9'=>'<span class="sell_serverPrice" .*?>([0-9,.]+)&nbsp;円<\/span><span class="sell_serverPrice" .*?>.*?円<\/span>',
                      '10-'=>'<span class="sell_serverPrice" .*?>.*?円<\/span><span class="sell_serverPrice" .*?>([0-9,.]+)&nbsp;円<\/span>',
                      'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/span>' 
                    ),
                  ),
      'megaten'=>array(
             'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?([0-9,.]+).*?<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=>array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
              'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
      'EWD'=>array(
         'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?([0-9,.]+).*?M募集.*?<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
           'rmt.kakaran.jp'=>array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
             'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
      'LH'=>array(
		     'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
              'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
               'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
        'HR'=>array(
             'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                        'price'=>'<td class="center">([0-9,]+)円<\/td>',
                        'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">.*?([0-9,]+).*?<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
              'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
      'AA'=> array(
           'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">([a-zA-Z]+).*?<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
             'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
              'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
      'ThreeSeven'=>array(
            'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'www.iimy.co.jp'=>array('products_name'=>'<name>(.*?)買取<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
					 
                  ),
      'ECO'=>array(
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
               'rmt.kakaran.jp'=>array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
               'rmt.diamond-gil.jp'=> array('products_name'=>'<span class="sell_serverName" style="width:40%">&nbsp;(.*?)<\/span>',
                      'price'=>'<span class="sell_serverPrice" .*?>([0-9,.]+)&nbsp;円<\/span>',
                      'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/li>' 
                    ),
                  ),
       'FNO'=>array(
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
               'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
      'SUN'=>array(
           'www.matubusi.com'=> array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
           'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
            'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
    'talesweave'=>array(
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
            'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
      'MU'=>array(
           'www.matubusi.com'=> array('products_name'=>'<th class="rowheader">(.*?)<\/th>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
             'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
              'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の宝石買取<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
      'C9'=>array(
            'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
             'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
     'MS'=>array(
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
             'www.rmt-wm.com'=>array('products_name'=>'<td class="th"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td>([0-9,.]+)円<\/td>',
                      'inventory'=>'<td>[0-9,.]+?円<\/td>.*?<td>[0-9,.]+?Pt<\/td>.*?<td>(.*?)<\/td>' 
                    ),

               /*     array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                          'price'=>'<td class="price"><a href=".*?">.*?([0-9]+)<\/a><\/td>',
                         'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
				*/
               'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
					/*
                    array('products_name'=>'<span class="sell_serverName" style="width:21%">&nbsp;[1,2,3,4,50-9.]+(.*?)<\/span>',
                      '1-19'=>'<span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">([0-9]+)&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span>',
                      '20-49'=>'<span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">([0-9]+)&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span>',
                      '50-99'=>'<span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">([0-9]+)&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span>',
                      '100-'=>'<span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">([0-9]+)&nbsp;円<\/span>',
                      'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/span>'
                     ),
					 */
                      ),
     'cronous'=>array(
              'www.matubusi.com'=> array('products_name'=>'<th class="rowheader">(.*?)<\/span>.*?<\/th>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'rmt.kakaran.jp'=> array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
              'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
      'tenjouhi'=>array(
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
             'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
              'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
      'rose'=>array(
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
           'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
            'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
      'hzr'=>array(
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
             'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
      'dekaron'=>array(
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
             'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
             'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
      'fez'=>array(
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
           'ftb-rmt.jp'=>array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">([a-zA-Z]+)<br>.*?<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
           'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
       'lakatonia' => array(
            'ftb-rmt.jp'=>  array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
             'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
           ),
       'moe' => array(
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
             'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                 'rmt.diamond-gil.jp' => array('products_name'=>'<span class="sell_serverName" style="width:40%">&nbsp;(.*?)<\/span>',
                      'price'=>'<span class="sell_serverPrice" .*?>([0-9,.]+)&nbsp;円<\/span>',
                      'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/li>' 
                    ),
           ),
      'mabinogi'=> array(
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
              'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
             'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
            ),
      'WF'=> array(
            'www.iimy.co.jp'=>  array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
             'www.rmt-king.com'=>  array('products_name'=>'<tr .*?>.*?<td class="center">(.*?)<\/td>.*?<td .*?>',
                      '1-24'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '25-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>',
                      '50-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                     'inventory'=>'<td class="center" nowrap="nowrap">.*?([0-9,.]+)&nbsp;口.*?<\/td>' 
                    ),
                  ),
      'rohan'=> array(
            'rmtrank.com'=>array('url'=>'<td class="col0"><a href="([^"]*)">[^<]*<\/a><\/td>',
                        'products_name'=>'<td class="col0"><a href="[^"]*">([^<]*)<\/a><\/td>'
                    ),
             'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
                  ),
      'genshin'=> array(
             'www.matubusi.com'=>array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
              'rmt.kakaran.jp'=> array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                      'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                      'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
               'www.iimy.co.jp'=> array('products_name'=>'<name>(.*?)の.*?<\/name>',
                           'price'=>'<price>([0-9,.]+)円<\/price>',
                           'inventory'=>'<quantity>(.*?)<\/quantity>'
                    ),
               'www.rmt-king.com'=> array('products_name'=>'<tr .*?>.*?<td class="center">(.*?)<\/td>.*?<td .*?>',
                     'price'=>'<td class="center" nowrap="nowrap">.*?([0-9,.]+)円.*?<\/td>',
                     'inventory'=>'<td class="center" nowrap="nowrap">.*?([0-9,.]+&nbsp;口).*?<\/td>' 
                    ),
                  )
	  )
        );

//kakran
$other_array_match = array(
	'buy'=> array(
        'rmt.kakaran.jp'=>  array( 
                        'site_names'=>'<td class="position-relative">(.*?)<\/td><td class="compare"><span>.*?<\/span><\/td><td class="price sort">([0-9,.]+)円<\/td><td class="price">.*?<\/td>', 
                        'price'=>'<td class="price sort">([0-9,.]+)円<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                        'inventory'=>'<td class="price sort">[0-9,.]+円<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
        'rmtrank.com'=> array('site_names'=>'<td class="colb0\w{0,}"><a href="[^"]*"[^>]*>([^<]*)<\/a><\/td>|<td class="colb0\w{0,}"><img[^>]*\/>\s{0,}<a href="[^"]*"[^>]*>([^<]*)<\/a><\/td>',
                        'price'=> '<td class="colb2\w{0,}">(\d+)[^<]*\(<font[^>]*>[^<]*<\/font>\)[^<]*<\/td>',
                        'inventory' => '<td class="colb2\w{0,}"><span class="nobr">(\d+)[^<]*口'
                        ), 
         ),
'sell' => array(
            'rmt.kakaran.jp'=>array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                         'price'=>'<td class="price"><a href=".*?">.*?([0-9]+)<\/a><\/td>',
                         'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                       ),
            'rmtrank.com'=> array('site_names'=>'<td class="colb0\w{0,}"><a href="[^"]*"[^>]*>([^<]*)<\/a><\/td>|<td class="colb0\w{0,}"><img[^>]*\/>\s{0,}<a href="[^"]*"[^>]*>([^<]*)<\/a><\/td>',
                         'price'=> '<td class="colb2\w{0,}">(\d+)[^<]*円',
                         'inventory' => '<td class="colb2\w{0,}">(\d+)[^<]*口'
                        ), 
                     )
);
