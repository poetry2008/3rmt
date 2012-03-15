/******************************************************************************/
/*                            時間セレクトボックス                             /
/******************************************************************************/
function selectDate(start_time,end_time,sleep){
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

        arr_start = start_time.split(':'); 
        hour_start = arr_start[0];
        arr_end = end_time.split(':');
        hour_end = arr_end[0];
       
        if(arr_end[1] == '00'){
        
          hour_end--;
        }
        //new 
        html_str = '<table width="100%" border="0" cellspacing="2" cellpadding="2"><tr>';
        for(j=1;j<=24;j++){
         
          if((j-1) >= hour_start && (j-1) <= hour_end){
            html_str += '<td id="hour'+(j-1)+'" bgcolor="#ccc" style="color:#000;cursor:pointer;" align="center" onclick="this.style.background=\'#F5F9FC\';selectHour(\''+start_time+'\',\''+end_time+'\','+(j-1)+','+sleep+');">'+(j-1)+'</td>';
          }else{
            html_str += '<td id="hour'+(j-1)+'" bgcolor="#f1f0ef" style="color:#ccc;" align="center">'+(j-1)+'</td>';
          }
          if(j % 6 == 0){
          
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
function selectHour(start_time,end_time,hour,sleep){
        arr_start = start_time.split(':'); 
        hour_start = arr_start[0];
        hour_start_min = arr_start[1];
        arr_end = end_time.split(':');
        hour_end = arr_end[0];
        hour_end_min = arr_end[1];
        
        hour_end_num = hour_end;
        if(arr_end[1] == '00'){
        
          hour_end_num--;
        } 
        //整数化
	hour_start = parseInt(hour_start);
	hour_end  = parseInt(hour_end);

        for(h = hour_start;h <= hour_end_num;h++){
           
          if(h != hour){
              $("#hour"+h).css("background-color","#ccc"); 
          }
        }
        /*
	//var num  = document.order.hour.selectedIndex;  //'選択セレクトボックス番号
	var num2 = document.order.date.selectedIndex;  //'選択セレクトボックス番号
        var num = hour_num - hour + 1; 

	var myD    = new Date();                       //'日付オブジェクト
//	var myHour = myD.getHours();                   //'時間
//	var myMin  = myD.getMinutes();                 //'分
		myMin  = Math.ceil(myMin/10) * 10;         //'切り上げ処理済「分」
	var min    = 0;                                //'分初期値
	var plus   = 20;                               //'追加分初期値


	//'整数化
	myHour = parseInt(myHour);
	myMin  = parseInt(myMin);
        num = parseInt(num);


	//'セレクトボックス値クリア
	//document.order.min.options.length = 1;


	//'セレクトボックス表示分範囲値取得
	//'
	//'
	if (num2 == 1) {
		if (num == 0) {
			return false;

		} else if (num == 1) {
			min = myMin;

			if ((myMin+plus) > 59) {
				min    = (myMin+plus) - 60;
				myHour = myHour + 1;
			} else {
				min = myMin+plus;
			}
		}
		if ((myHour < 10) || (myHour > 23)) {
			min = 00;
		}

	} else {
		if (num == 0) {
			return false;

		} else if (num == 1) {
			min = 00;
		}
	}
        */
        html_str = '';
        html_str = '<table width="100%" border="0" cellspacing="0" cellpadding="2"><tr><input type="hidden" name="hour" value="'+hour+'">';
        
        

        min = 0;
        min_end = 60;
        if(hour_start == hour){
          hour_start_min = hour_start_min == '00' ? 0 : hour_start_min;
          min = hour_start_min; 
        }else if(hour_end == hour){
          hour_end_min = hour_end_min == '00' ? 0 : hour_end_min;
          min_end = hour_end_min;   
        }

        jj = 1;
        for(j=min;j<min_end;j=j+sleep){

          j_str = j == 0 ? '00' : j;
          html_str += '<td><input type="radio" name="min" value="'+j+'"><font size="2">'+hour+'時'+j_str+'分～'+hour+'時'+(j_str+sleep-1)+'分</font></td>';
          if(jj % 2 == 0){
          
            html_str += '</tr><tr>';
          }
          jj++;
        }
        html_str += '</table>'; 

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

