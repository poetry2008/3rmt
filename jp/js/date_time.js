/******************************************************************************/
/*                            時間セレクトボックス                             /
/******************************************************************************/
function selectDate(start_time,end_time){
	//var num    = document.order.date.selectedIndex; //'選択セレクトボックス番号
	//var myD    = new Date();                        //'日付オブジェクト
//	var myHour = myD.getHours();                    //'時間
//	var myMin  = myD.getMinutes();                  //'分
		//myMin  = Math.ceil(myMin/10) * 10;          //'切り上げ処理済「分」
	//var plus   = 20;                                //'追加分初期値


	//'整数化
	//myHour = parseInt(myHour);
	//myMin  = parseInt(myMin);


	//'セレクトボックス値クリア
	//document.order.min.options.length  = 1;
	//document.order.hour.options.length = 1;


	//'セレクトボックス表示時間範囲値取得
	//'
	//'
        /*
	if (num == 0) {
		return false;

	} else if (num == 1) {
		hour = myHour;

		if ((myMin+plus) > 59) {
			hour = hour + 1;
		}
		if (hour > 23) {
			hour = 10;
		}
	} else {
		hour = 10;
	}
	hour = (hour < 10)? 10 : hour;
        */
        
        var array_start_time = new Array();
        var array_start_time_str = new Array();
        array_start = start_time.split(','); 
        for(x in array_start){
        
          array_start_str = array_start[x].split(':');
          array_start_time[x] = array_start_str[0];
          array_start_time_str[x] = array_start[x];
        }

        var array_end_time = new Array();
        var array_end_time_str = new Array();
        array_end = end_time.split(','); 
        for(x in array_end){
        
          array_end_str = array_end[x].split(':');
          array_end_time[x] = array_end_str[0];
          array_end_time_str[x] = array_end[x];
        }

        //new
        html_str = '<table width="100%" border="0" cellspacing="2" cellpadding="2"><tr>';
        for(j=0;j<24;j++){
          for(x in array_start_time){
          
            if(array_start_time[x] == j){
            
              hour_start = parseInt(array_start_time[x]);
              hour_end = parseInt(array_end_time[x]);
            }
          } 
          if(j >= hour_start && j <= hour_end){
            html_str += '<td id="hour'+j+'" bgcolor="#ccc" style="color:#000;cursor:pointer;" align="center" onclick="this.style.background=\'#F5F9FC\';selectHour(\''+start_time+'\',\''+end_time+'\','+j+');">'+j+'</td>';
          }else{
            html_str += '<td id="hour'+j+'" bgcolor="#f1f0ef" style="color:#ccc;" align="center">'+j+'</td>';
          }
          if((j+1) % 6 == 0){
          
            html_str += '</tr><tr>';
          }
        }
        
        html_str += '</tr></table>';
        $("#shipping_list_show").html('');
        $("#shipping_list").show();
        $("#shipping_list_show").html(html_str);

        $("#shipping_list_show_min").html('');
        $("#shipping_list_min").hide();


	//'セレクトボックス値作成
	//for (i=hour; i<24; i++) {
		//document.order.hour.options[document.order.hour.options.length]=new Option(i, i);
		//if(document.layers){
			//top.resizeBy(-10,-10)
			//top.resizeBy(10,10)
		//}
	//}
}




/******************************************************************************/
/*                            分セレクトボックス                               /
/******************************************************************************/
function selectHour(start_time,end_time,hour,min_num){

        var array_start_time = new Array();
        var array_start_time_str = new Array();
        var array_start_min = new Array();
        array_start = start_time.split(','); 
        for(x in array_start){
        
          array_start_str = array_start[x].split(':');
          array_start_time[x] = array_start_str[0];
          array_start_min[x] = array_start_str[1];
          array_start_time_str[x] = array_start[x];
        }

        var array_end_time = new Array();
        var array_end_time_str = new Array();
        var array_end_min = new Array();
        array_end = end_time.split(','); 
        for(x in array_end){
        
          array_end_str = array_end[x].split(':');
          array_end_time[x] = array_end_str[0];
          array_end_min[x] = array_end_str[1];
          array_end_time_str[x] = array_end[x];
        }
         
        //整数化

        for(h = 0;h < 24;h++){
          for(x in array_start_time){
          
            if(array_start_time[x] == h){
            
              hour_start = parseInt(array_start_time[x]);
              hour_end = parseInt(array_end_time[x]);
            }
          } 
          if((h >= hour_start && h <= hour_end) && h != hour){
              $("#hour"+h).css("background-color","#ccc"); 
          }
        }
        
        html_str = '';
        html_str = '<table width="100%" border="0" cellspacing="0" cellpadding="2"><tr><input type="hidden" name="hour" value="'+hour+'">';
        
        

        var string = '';
        var min_num = 0;
        for(n in array_start_time){
          
            if(hour >= array_start_time[n] && hour <= array_end_time[n]){
            
              string =  array_start_time[n]+'時'+array_start_min[n]+'分～'+array_end_time[n]+'時'+array_end_min[n]+'分';
              min_num = n;
            }
        }
          //html_str += '<td><input type="radio" name="min" value="'+j+'" '+checked+'><font size="2">'+hour+'時'+j_str+'分～'+hour+'時'+(j_str+sleep-1)+'分</font></td>';
         
          html_str += '<td><input type="radio" name="min" value="'+array_start_min[min_num]+'" checked><font size="2">'+string+'</font></td>';
          

          html_str += '</tr><tr></table>'; 

        $("#shipping_list_show_min").html('');
        $("#shipping_list_show_min").html(html_str);
        $("#shipping_list_min").show();
        
 
	//'セレクトボックス値作成
        /*
	for (i=min; i<60; i=i+10) {
		if (i == 0) {
			document.order.min.options[document.order.min.options.length]=new Option("00", "00");
			if(document.layers){
				top.resizeBy(-10,-10)
				top.resizeBy(10,10)
			}
		} else {
			document.order.min.options[document.order.min.options.length]=new Option(i, i);
			if(document.layers){
				top.resizeBy(-10,-10)
				top.resizeBy(10,10)
			}
		}
	}
        */
}

