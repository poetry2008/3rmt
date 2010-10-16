<?php
class session {
  protected $db;
  function __construct(){
    $this->db = db::getConn();
  }
  function delete($id){
    $sql = "delete from session_log
           where id='".$id."'";
    $this->db->query($sql);
  }
  function deleteByMissionId($mid){
    $sql = "delete from session_log
           where mission_id='".$mid."'";
    $this->db->query($sql);
  }
  function stop($id){
    $sql = "update session_log set
           forced='1',
           end_at='".time()."'
           where id = '".$id."'";
    $this->db->query($sql);
  }
}
