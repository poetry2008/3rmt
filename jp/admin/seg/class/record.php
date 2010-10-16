<?php
class Record{
  protected $db;
  function __construct(){
    $this->db = db::getConn();
  }
  function edit($url,$show=1){
    $sql = "update record set
            `show` = '".$show."'
            where siteurl = '".$url."'";
    $this->db->query($sql);
  }
  function deleteBySessionId($sid){
    $sql = "delete from record
           where session_id='".
           $sid."'";
    $this->db->query($sql);
  }
  function deleteByMissionId($mid){
    $sql = "delete from record
           where mission_id='".
           $mid."'";
    $this->db->query($sql);
  }
}
