/******************************************************************************/
/*                            時間セレクトボックス                             /
/******************************************************************************/
function selectDate(){

	var num    = document.order.date.selectedIndex; //'選択セレクトボックス番号
	var myD    = new Date();                        //'日付オブジェクト
	var myHour = myD.getHours();                    //'時間
	var myMin  = myD.getMinutes();                  //'分
	var myMin  = Math.ceil(myMin/10) * 10;          //'切り上げ処理済「分」


	//'セレクトボックス値クリア
	document.order.min.options.length  = 1;
	document.order.hour.options.length = 1;


	//'セレクトボックス表示時間範囲値取得
	//'
	//'
	if (num == 0) {
		return false;

	} else if (num == 1) {
		hour = myHour;

		if ((myMin+20) > 59) {
			hour = hour + 1;
		}
		if (hour > 23) {
			hour = 10;
		}
	} else {
		hour = 10;
	}
	hour = (hour < 10)? 10 : hour;


	//'セレクトボックス値作成
	for (i=hour; i<24; i++) {
		document.order.hour.options[document.order.hour.options.length]=new Option(i, i);
		if(document.layers){
			top.resizeBy(-10,-10)
			top.resizeBy(10,10)
		}
	}
}




/******************************************************************************/
/*                            分セレクトボックス                               /
/******************************************************************************/
function selectHour(){
	var num  = document.order.hour.selectedIndex;  //'選択セレクトボックス番号
	var num2 = document.order.date.selectedIndex;  //'選択セレクトボックス番号

	var myD    = new Date();                       //'日付オブジェクト
	var myHour = myD.getHours();                   //'時間
	var myMin  = myD.getMinutes();                 //'分
	var myMin  = Math.ceil(myMin/10) * 10;         //'切り上げ処理済「分」
	var min    = 0;                                //'分初期値


	//'セレクトボックス値クリア
	document.order.min.options.length = 1;


	//'セレクトボックス表示分範囲値取得
	//'
	//'
	if (num2 == 1) {
		if (num == 0) {
			return false;

		} else if (num == 1) {
			min = myMin;

			if ((myMin+20) > 59) {
				min    = (myMin+20) - 60;
				myHour = myHour + 1;
			} else {
				min = myMin+20;
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


	//'セレクトボックス値作成
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
}
