/******************************************************************************/
/*                            ���֥��쥯�ȥܥå���                             /
/******************************************************************************/
function selectDate(myHour, myMin){


	var num    = document.order.date.selectedIndex; //'���򥻥쥯�ȥܥå����ֹ�
	var myD    = new Date();                        //'���ե��֥�������
//	var myHour = myD.getHours();                    //'����
//	var myMin  = myD.getMinutes();                  //'ʬ
		myMin  = Math.ceil(myMin/10) * 10;          //'�ڤ�夲�����ѡ�ʬ��
	var plus   = 20;                                //'�ɲ�ʬ�����


	//'������
	myHour = parseInt(myHour);
	myMin  = parseInt(myMin);


	//'���쥯�ȥܥå����ͥ��ꥢ
	document.order.minute.options.length  = 1;
	document.order.hour.options.length = 1;


	//'���쥯�ȥܥå���ɽ�������ϰ��ͼ���
	//'
	//'
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


	//'���쥯�ȥܥå����ͺ���
	for (i=hour; i<24; i++) {
		document.order.hour.options[document.order.hour.options.length]=new Option(i, i);
		if(document.layers){
			top.resizeBy(-10,-10)
			top.resizeBy(10,10)
		}
	}
}

/******************************************************************************/
/*                            ʬ���쥯�ȥܥå���                               /
/******************************************************************************/
function selectHour(myHour, myMin){
	var num  = document.order.hour.selectedIndex;  //'���򥻥쥯�ȥܥå����ֹ�
	var num2 = document.order.date.selectedIndex;  //'���򥻥쥯�ȥܥå����ֹ�

	var myD    = new Date();                       //'���ե��֥�������
//	var myHour = myD.getHours();                   //'����
//	var myMin  = myD.getMinutes();                 //'ʬ
		myMin  = Math.ceil(myMin/10) * 10;         //'�ڤ�夲�����ѡ�ʬ��
	var min    = 0;                                //'ʬ�����
	var plus   = 20;                               //'�ɲ�ʬ�����


	//'������
	myHour = parseInt(myHour);
	myMin  = parseInt(myMin);


	//'���쥯�ȥܥå����ͥ��ꥢ
	document.order.minute.options.length = 1;


	//'���쥯�ȥܥå���ɽ��ʬ�ϰ��ͼ���
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


	//'���쥯�ȥܥå����ͺ���
	for (i=min; i<60; i=i+10) {
		if (i == 0) {
			document.order.minute.options[document.order.minute.options.length]=new Option("00", "00");
			if(document.layers){
				top.resizeBy(-10,-10)
				top.resizeBy(10,10)
			}
		} else {
			document.order.minute.options[document.order.minute.options.length]=new Option(i, i);
			if(document.layers){
				top.resizeBy(-10,-10)
				top.resizeBy(10,10)
			}
		}
	}
}
