<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Global extends FLEA_Db_TableDataGateway
{
    function __construct(){
    parent::FLEA_Db_TableDataGateway();
    $this->disableLinks();
  }
  var $tableName = 'rr_global';
  var $primaryKey = 'name';

    /**
    *
    */
    function _create($name,$value = null,$comment = null)
    {
        $data = array(
            'name'    => $name,
            'value'   => $value,
            'comment' => $comment,
        );
        return $this->create($data);
    }

    /**
    *
    */
    function _get($name) //get value by name
    {
        $cond = array(
            'name' => $name,
        );
        return $this->find($cond);
    }


    /**
    *
    */
    function _setValue($name,$value = null)
    {
        $data = array(
            'name'    => $name,
            'value'   => $value,
        );
        return $this->save($data);
    }

    /**
    *
    */
    function _setComment($name,$comment = null)
    {
        $data = array(
            'name'    => $name,
            'comment' => $comment,
        );
        return $this->save($data);
    }


    /**
    *
    */
    function _set($name,$value = null,$comment = null)
    {
        $data = array(
            'name'    => $name,
            'value'   => $value,
            'comment' => $comment,
        );
        return $this->save($data);
    }

    /**
    *
    */
    function _removeByName($name)
    {

    }

    /**
    *
    */
    function _removeById($id)
    {

    }




}
