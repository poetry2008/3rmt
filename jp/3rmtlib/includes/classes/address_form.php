<?php
/*
  $Id$
*/
  class addressForm {
    // private
    var $address_format = array();
    // private
    var $formlines = array();
    // private
    var $formhiddens = array();
    // private
    var $boldtitle = false;

    function addressForm(){
    }
/*---------------------------------
 功能：获取顾客的个人信息 
 参数：无
 返回值：顾客的个人信息(string)
 --------------------------------*/
    // private
    function getOrderCategoryPersonal(){
        if (in_array('firstname', $this->address_format)
         && in_array('lastname',  $this->address_format)
         && (array_search('lastname',$this->address_format) < array_search('firstname',$this->address_format))){
            $name = array('lastname', 'firstname', 'lastname_f', 'firstname_f');
        } else {
            $name = array('firstname', 'lastname', 'firstname_f', 'lastname_f');
        }

        return array_merge(
            ((ACCOUNT_GENDER == 'true') ? array('gender') : array()),
            $name,
            ((ACCOUNT_DOB == 'true') ? array('dob') : array()),
            array('email_address','quited_date'));
    }

    // private
/*--------------------------------
 功能：获取顾客的公司 
 参数：无
 返回值：顾客公司的信息(string) 
 -------------------------------*/
    function getOrderCategoryCompany(){
        return (ACCOUNT_COMPANY == 'true') ? array('company') : array();
    }

    // private
/*-------------------------------
 功能：获取顾客的地址信息
 参数：无
 返回值：顾客的地址信息(string) 
 ------------------------------*/
    function getOrderCategoryAddress(){
        $orders = array();
        foreach ($this->address_format as $element) {
            switch($element){
            case 'streets':
                $orders[] = 'street_address';
                if (ACCOUNT_SUBURB == 'true') {
                    $orders[] = 'suburb';
                }
                break;
            case 'postcode':
            case 'city':
            case 'country':

            case 'telephone':
                $orders[] = $element;
                break;
            case 'state':
            case 'statecomma':
            case 'statename':   // add for Japanese Localize
                if (ACCOUNT_STATE == 'true') {
                    $orders[] = 'state';
                }
                break;
            }
        }
        return $orders;
    }

    // private
/*----------------------------------
 功能：打印表格行列
 参数：无
 返回值：无
 ---------------------------------*/
    function printTableLine($orders){
        foreach ($orders as $name) {
          if (!isset($this->formlines[$name])) $this->formlines[$name]=NULL;
          if (!isset($this->formlines[$name]['title'])) $this->formlines[$name]['title']=NULL;
            $title = $this->formlines[$name]['title'];
            if ($title) {
                if ($this->boldtitle) {
                    $title = '<b>'.$title.'</b>';
                }
                echo '<tr>'
                  .'<td class="main"'.((NEW_STYLE_WEB === true)?' valign="top" width="15%" align="left">':'width="93" valign="top">').$title.'</td>'
                  .'<td class="main" '.((NEW_STYLE_WEB === true)?' align="left"':'').'>'.$this->formlines[$name]['value'].'</td>'
                  .'</tr>'."\n";
            }
        }
    }

    // public
/*----------------------------------
 功能：实体店地址格式
 参数：$address_format_id(number) 地址格式ID
 返回值：无
 ---------------------------------*/
    function storeAddressFormat($address_format_id){
        $query = tep_db_query("
            select address_format 
            from " . TABLE_ADDRESS_FORMAT . " 
            where address_format_id = '" . $address_format_id . "'
        ");
        $row = tep_db_fetch_array($query);
        $formatstring = ereg_replace('[^a-z]+', ' ', $row['address_format']);
        $elements = explode(' ', $formatstring);

        $this->address_format = array();
        foreach($elements as $one) {
            if ($one) {
                $this->address_format[] = $one;
            }
        }
    }

    // public
/*---------------------------------
 功能：设置国家 
 参数：$countries_id(number) 国家ID
 返回值：无
 --------------------------------*/
    function setCountry($countries_id=0){
        global $account;
        if (!$countries_id) {
            $countries_id = isset($account['entry_country_id'])
                            ? $account['entry_country_id'] : STORE_COUNTRY;
        }
        $address_format_id = tep_get_address_format_id($countries_id);
        $this->storeAddressFormat($address_format_id);
    }

    // public
/*--------------------------------
 功能：查看有没有国家名
 参数：$name(string) 国家名
 返回值：搜索有没有国家名(boolean) 
 -------------------------------*/
    function inForm($name){
        if (count($this->address_format) == 0) {
            $this->setCountry();
        }
        return ($name && in_array($name, $this->address_format));
    }

    // public
/*-----------------------------
 功能：设置粗体标题
 参数：无
 返回值：无
 ---------------------------*/
    function setBoldTitle($is_bold=true){
        $this->boldtitle = $is_bold;
    }

    // public
/*---------------------------
 功能：设置表格
 参数：$name(string) 名字
 参数：$title(string) 标题
 参数：$value(string) 绘制输入字段 
 返回值：无
 --------------------------*/
    function setFormLine($name, $title, $value){
        $this->formlines[$name]['title'] = $title;
        $this->formlines[$name]['value'] = $value;
    }

    // public
/*---------------------------
 功能：设置表格隐藏 
 参数：$name(string) 名字
 参数：$value(string) 输入字段  
 返回值：无
 --------------------------*/
    function setFormHidden($name, $value){
        $this->formhiddens[$name] = $value;
    }

    // public
/*--------------------------
 功能：打印顾客信息 
 参数：无
 返回值：无
 -------------------------*/
    function printCategoryPersonal(){
        if (count($this->address_format) == 0) {
            $this->setCountry();
        }
        $this->printTableLine($this->getOrderCategoryPersonal());
    }

    // public
/*--------------------------
 功能：打印顾客公司
 参数：无
 返回值：无
 -------------------------*/
    function printCategoryCompany(){
        if (count($this->address_format) == 0) {
            $this->setCountry();
        }
        $this->printTableLine($this->getOrderCategoryCompany());
    }

    // public
/*--------------------------
 功能：打印顾客地址
 参数：无
 返回值：无
 -------------------------*/
    function printCategoryAddress(){
        if (count($this->address_format) == 0) {
            $this->setCountry();
        }
        $this->printTableLine($this->getOrderCategoryAddress());

        foreach ($this->formhiddens as $name => $value) {
            if (!$this->inForm($name) && $value) { // in case without country
                echo $value."\n";
            }
        }
    }
  }
?>
