
	/**
	* 表头排序时的动作
	*/
	function fnTableSort(tablename,colName,sortDir){
		addCookie('sortName_'+tablename,colName,30*24);
		addCookie('sortOrder_'+tablename,sortDir,30*24);
		fnChangePage(tablename,0);
	}	
	
	/**
	* 改变表格显示行数的动作
	*/
	function fnChangeSize(tablename,pageSize){
		addCookie('pageSize_'+tablename,pageSize,24*30);
		fnChangePage(tablename,0);
	}
	
	/**
	* 改变分类的动作
	*/
	function fnChangeCategory(tablename,category){
		addCookie('category_'+tablename,category,24*30);
		fnChangePage(tablename,0);
	}
	
	/**
	* 跳页动作
	*/
	function fnChangePage(tablename,pageNo){
		//alert('tableName:'+tablename+". pageNo="+pageNo+".");
		addCookie('pageNo_'+tablename,pageNo,24*30);
		setTimeout('window.location.href=gUrl;',1000);		//使用了全局变量
		return false;
	}
	
	/**
	* 全选 或 全部取消选中的动作
	*/
	function fnCheckAll(tablename,checked){
		var eles = document.getElementsByTagName("input");
 		for (var i=0; i<eles.length; i++)
 			if(eles[i].type=='checkbox')
		 	if (eles[i].name == tablename+"[]")
				eles[i].checked = checked;
	}
	
	/**
	* 点击删除的时确认
	*/
	function fnConfirm(){
		return confirm(gConfirm);		//使用了全局变量
	}
