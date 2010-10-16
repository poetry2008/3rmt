<?php
class Filter{
  protected $db;
  function __construct(){
    $this->db = db::getConn();
  }
  function edit($arr){
    $sql = "update site_filter set
           record_siteurl = '".trim($arr['record_siteurl'])."',
           state = '".$arr['state']."'
           where id = '".$arr['id']."'";
    $this->db->query($sql);
  }
  function insert($arr){
    $sql = "insert into site_filter
           (`id`,`record_siteurl`,`state`)
           values
           ('','".trim($arr['record_siteurl'])
            ."','".$arr['state']."')";
    $this->db->query($sql);
  }
  function delete($id){
    $sql = "delete from site_filter
           where id = '".$id."'"; 
    $this->db->query($sql);
  }
}
