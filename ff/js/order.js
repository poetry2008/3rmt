/******************************************************************************/
/*                            Time select box                             /
/******************************************************************************/
function selectDate(myHour, myMin){


	var num    = document.order.date.selectedIndex; //'Select a number of select box
	var myD    = new Date();                        //'Date object
//	var myHour = myD.getHours();                    //'Time
//	var myMin  = myD.getMinutes();                  //'Minutes
		myMin  = Math.ceil(myMin/10) * 10;          //'Minutes carry dealt
	var plus   = 20;                                //'Add the initial value of minutes


	//'Integerized
	myHour = parseInt(myHour);
	myMin  = parseInt(myMin);


	//'Select box value is cleared
	document.order.minute.options.length  = 1;
	document.order.hour.options.length = 1;


	//'Obtain the range of values of the time select box
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


	//'Create a select box value
	for (i=hour; i<24; i++) {
		document.order.hour.options[document.order.hour.options.length]=new Option(i, i);
		if(document.layers){
			top.resizeBy(-10,-10)
			top.resizeBy(10,10)
		}
	}
}

/******************************************************************************/
/*                            Minutes select box                               /
/******************************************************************************/
function selectHour(myHour, myMin){
	var num  = document.order.hour.selectedIndex;  //'Select a number of select box
	var num2 = document.order.date.selectedIndex;  //'Select a number of select box

	var myD    = new Date();                       //'Date object
//	var myHour = myD.getHours();                   //'Time
//	var myMin  = myD.getMinutes();                 //'Minutes
		myMin  = Math.ceil(myMin/10) * 10;         //'Minutes carry dealt
	var min    = 0;                                //'Minutes select box
	var plus   = 20;                               //'Add the initial value of minutes


	//'Integerized
	myHour = parseInt(myHour);
	myMin  = parseInt(myMin);


	//'Select box value is cleared
	document.order.minute.options.length = 1;


	//'Obtain the range of values of the minutes select box
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


	//'Create a select box value
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
