
	/**
	* Operation when the table header to sort
	*/
	function fnTableSort(tablename,colName,sortDir){
		addCookie('sortName_'+tablename,colName,30*24);
		addCookie('sortOrder_'+tablename,sortDir,30*24);
		fnChangePage(tablename,0);
	}	
	
	/**
	* The change table shows the number of rows of action
	*/
	function fnChangeSize(tablename,pageSize){
		addCookie('pageSize_'+tablename,pageSize,24*30);
		fnChangePage(tablename,0);
	}
	
	/**
	* To change the classification of the action
	*/
	function fnChangeCategory(tablename,category){
		addCookie('category_'+tablename,category,24*30);
		fnChangePage(tablename,0);
	}
	
	/**
	* Jump page action
	*/
	function fnChangePage(tablename,pageNo){
		//alert('tableName:'+tablename+". pageNo="+pageNo+".");
		addCookie('pageNo_'+tablename,pageNo,24*30);
		setTimeout('window.location.href=gUrl;',1000);		//The use of global variables
		return false;
	}
	
	/**
	* Select or uncheck all the action
	*/
	function fnCheckAll(tablename,checked){
		var eles = document.getElementsByTagName("input");
 		for (var i=0; i<eles.length; i++)
 			if(eles[i].type=='checkbox')
		 	if (eles[i].name == tablename+"[]")
				eles[i].checked = checked;
	}
	
	/**
	* Confirm delete when click it
	*/
	function fnConfirm(){
		return confirm(gConfirm);		//The use of global variables
	}
