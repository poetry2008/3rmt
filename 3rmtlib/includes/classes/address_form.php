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
            array('email_address'));
    }

    // private
    function getOrderCategoryCompany(){
        return (ACCOUNT_COMPANY == 'true') ? array('company') : array();
    }

    // private
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
// 2003-06-06 add_telephone
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
                  .'<td class="main">&nbsp;'.$title.'</td>'
                  .'<td class="main">&nbsp;'.$this->formlines[$name]['value'].'</td>'
                  .'</tr>'."\n";
            }
        }
    }

    // public
    function storeAddressFormat($address_format_id){
//ccdd
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
    function inForm($name){
        if (count($this->address_format) == 0) {
            $this->setCountry();
        }
        return ($name && in_array($name, $this->address_format));
    }

    // public
    function setBoldTitle($is_bold=true){
        $this->boldtitle = $is_bold;
    }

    // public
    function setFormLine($name, $title, $value){
        $this->formlines[$name]['title'] = $title;
        $this->formlines[$name]['value'] = $value;
    }

    // public
    function setFormHidden($name, $value){
        $this->formhiddens[$name] = $value;
    }

    // public
    function printCategoryPersonal(){
        if (count($this->address_format) == 0) {
            $this->setCountry();
        }
        $this->printTableLine($this->getOrderCategoryPersonal());
    }

    // public
    function printCategoryCompany(){
        if (count($this->address_format) == 0) {
            $this->setCountry();
        }
        $this->printTableLine($this->getOrderCategoryCompany());
    }

    // public
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
