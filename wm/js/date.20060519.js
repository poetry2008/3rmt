/******************************************************************************/
/*                            ���ԃZ���N�g�{�b�N�X                             /
/******************************************************************************/
function selectDate(){

	var num    = document.order.date.selectedIndex; //'�I���Z���N�g�{�b�N�X�ԍ�
	var myD    = new Date();                        //'���t�I�u�W�F�N�g
	var myHour = myD.getHours();                    //'����
	var myMin  = myD.getMinutes();                  //'��
	var myMin  = Math.ceil(myMin/10) * 10;          //'�؂�グ�����ρu���v


	//'�Z���N�g�{�b�N�X�l�N���A
	document.order.min.options.length  = 1;
	document.order.hour.options.length = 1;


	//'�Z���N�g�{�b�N�X�\�����Ԕ͈͒l�擾
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


	//'�Z���N�g�{�b�N�X�l�쐬
	for (i=hour; i<24; i++) {
		document.order.hour.options[document.order.hour.options.length]=new Option(i, i);
		if(document.layers){
			top.resizeBy(-10,-10)
			top.resizeBy(10,10)
		}
	}
}




/******************************************************************************/
/*                            ���Z���N�g�{�b�N�X                               /
/******************************************************************************/
function selectHour(){
	var num  = document.order.hour.selectedIndex;  //'�I���Z���N�g�{�b�N�X�ԍ�
	var num2 = document.order.date.selectedIndex;  //'�I���Z���N�g�{�b�N�X�ԍ�

	var myD    = new Date();                       //'���t�I�u�W�F�N�g
	var myHour = myD.getHours();                   //'����
	var myMin  = myD.getMinutes();                 //'��
	var myMin  = Math.ceil(myMin/10) * 10;         //'�؂�グ�����ρu���v
	var min    = 0;                                //'�������l


	//'�Z���N�g�{�b�N�X�l�N���A
	document.order.min.options.length = 1;


	//'�Z���N�g�{�b�N�X�\�����͈͒l�擾
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


	//'�Z���N�g�{�b�N�X�l�쐬
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
